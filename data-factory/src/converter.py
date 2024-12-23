import re
from pathlib import Path

import duckdb
import polars as pl
from rdflib import RDF, BNode, Graph, Literal, URIRef
from rdflib.namespace import RDFS, XSD, Namespace

BIO = Namespace("http://purl.org/vocab/bio/0.1/")
RICO = Namespace("https://www.ica.org/standards/RiC/ontology#")
WD = Namespace("http://www.wikidata.org/wiki/")
WDT = Namespace("https://www.wikidata.org/wiki/Property:")

DATA_PATH = Path("../data")
DB = duckdb.connect(DATA_PATH / "input" / "deskriptoren.db")

# Create a date literal from a string
def date_lit(date):

    # If format is YYYY => gYear
    if re.match(r"\d{4}$", date):
        return Literal(date, datatype=XSD.gYear)

    # If format is YYYY-mm or mm.YYYY => gYearMonth
    elif re.match(r"\d{2}\.\d{4}", date):
        date = date.split(".")
        date = "-".join(date[::-1])
        return Literal(date, datatype=XSD.gYearMonth)

    # If format is YYYY-mm-dd or dd.mm.YYYY => date
    elif re.match(r"\d{4}\-\d{2}-\d{2}$|\d{2}\.\d{2}.\d{4}$", date):
        date = date.split(".")
        date = "-".join(date[::-1])
        return Literal(date, datatype=XSD.date)

    # If format is YYYY v. Chr. => -YYYY => gYear
    elif re.match(r"\d+(?=\sv\.\sChr\.)", date):
        jh = int(re.match(r"\d+(?=\sv\.\sChr\.)", date).group())
        jh = str(f"-{jh:04d}")
        return Literal(jh, datatype=XSD.gYear)

    # Return as string if no ISO date found / possible
    else:
        return Literal(date, datatype=XSD.string)

# init graph
g = Graph()

# bind rico prefix
g.bind("bio", BIO)
g.bind("rico", RICO)
g.bind("wd", WD)
g.bind("wdt", WDT)

# get agents
df = DB.sql("""
    SELECT
        id_nr,
        id_name,
        ark,
        beschreibung
    FROM 
        input.personendeskriptoren
    WHERE
        ark NOT NULL
""").pl()

# Get sex of agents
df_sex = DB.sql("""
    SELECT
        ark,
        geschlecht
    FROM 
        input.personendeskriptoren
    WHERE geschlecht NOT NULL
""").pl()

# Get birthdates of agents
df_birth_dates = DB.sql("""
    SELECT
        ark,
        geburtsdatum
    FROM 
        input.personendeskriptoren
    WHERE geburtsdatum NOT NULL
""").pl()

# Get baptism dates of agents
df_baptism_dates = DB.sql("""
    SELECT
        ark,
        taufdatum
    FROM 
        input.personendeskriptoren
    WHERE taufdatum NOT NULL
""").pl()

# Get death dates of agents
df_death_dates = DB.sql("""
    SELECT
        ark,
        todesdatum
    FROM 
        input.personendeskriptoren
    WHERE todesdatum NOT NULL
""").pl()

# Get burial dates of agents
df_burial_dates = DB.sql("""
    SELECT
        ark,
        begrabnisdatum
    FROM 
        input.personendeskriptoren
    WHERE begrabnisdatum NOT NULL
""").pl()

# Get all children relations
df_children = DB.sql("""
    SELECT DISTINCT
        p1.ark AS sub_ark,
        p2.ark AS obj_ark
    FROM
        input.personendeskriptoren_zusatz pz
    JOIN
        input.personendeskriptoren p1 ON p1.id_nr = pz.col_001
    JOIN
        input.personendeskriptoren p2 ON p2.id_nr = pz.col_004
    WHERE
        pz.col_002 = 'Kind:'
""").pl()

# Get all spouses
df_spouses = DB.sql("""
    SELECT DISTINCT
        p1.ark AS sub_ark,
        p2.ark AS obj_ark
    FROM
        input.personendeskriptoren_zusatz pz
    JOIN
        input.personendeskriptoren p1 ON p1.id_nr = pz.col_001
    JOIN
        input.personendeskriptoren p2 ON p2.id_nr = pz.col_004
    WHERE
        pz.col_002 = 'Ehepartner / Ehepartnerin:'
""").pl()

def bbb_uri_ref(name):
    return URIRef("https://burgerbib.ch/" + str(name))

def agent(ark):
    return bbb_uri_ref("indexterms/" + str(ark))

# Define Demographic Groups
demographicgroup_sex_m = bbb_uri_ref("demographicgroups/sex-m")
g.add((demographicgroup_sex_m, RDFS.subClassOf, RICO.DemographicGroup))
g.add((demographicgroup_sex_m, RICO.name, Literal("Male (biological sex)")))

demographicgroup_sex_f = bbb_uri_ref("demographicgroups/sex-f")
g.add((demographicgroup_sex_f, RDFS.subClassOf, RICO.DemographicGroup))
g.add((demographicgroup_sex_f, RICO.name, Literal("Female (biological sex)")))

# Define Identifiertypes
idtype_ark = bbb_uri_ref("identifiertypes/ARK")
g.add((idtype_ark, RDF.type, RICO.IdentifierType))
g.add((idtype_ark, RICO.closeTo, WD.Q2860403))

idtype_scope = bbb_uri_ref("identifiertypes/Scope-ID")
g.add((idtype_scope, RDF.type, RICO.IdentifierType))
g.add((idtype_scope, RICO.name, Literal("ID from the ScopeArchiv AIS.")))

# Iterate dataset and generate triples
for row in df.rows(named=True):
    
    ark_id = bbb_uri_ref("identifiers/"+row["ark"])
    g.add((ark_id, RICO.isOrWasIdentifierOf, (agent(row["ark"]))))
    g.add((ark_id, RICO.hasIdentifierType, idtype_ark))
    g.add((ark_id, RICO.normalizedValue, Literal(row["ark"], datatype=XSD.string)))

    scope_id = bbb_uri_ref("identifiers/scope:"+str(row["id_nr"]))
    g.add((scope_id, RICO.isOrWasIdentifierOf, (agent(row["ark"]))))
    g.add((scope_id, RICO.hasIdentifierType, idtype_scope))
    g.add((scope_id, RICO.normalizedValue, Literal(row["id_nr"], datatype=XSD.nonNegativeInteger)))
    
    label = re.findall(r".+(?=\(Personen\\Natürliche Personen\\.+\))", row["id_name"])[0].strip()
    name = label.split("(")[0].strip()

    g.add((agent(row["ark"]), RDF.type, RICO.Person))
    g.add((agent(row["ark"]), RICO.hasOrHadName, Literal(name, datatype=XSD.string)))
    g.add((agent(row["ark"]), RDFS.label, Literal(label, datatype=XSD.string)))
    g.add((agent(row["ark"]), RICO.generalDescription, Literal(row["beschreibung"], datatype=XSD.string)))

for row in df_sex.rows():
    if row[1] == "männlich":
        g.add((agent(row[0]), RICO.hasOrHadDemographicGroup, demographicgroup_sex_m))
    else:
        g.add((agent(row[0]), RICO.hasOrHadDemographicGroup, demographicgroup_sex_f))

# Add birth dates.
for row in df_birth_dates.rows():
    g.add((agent(row[0]), RICO.hasBirthDate, date_lit(row[1])))

# Add baptism dates.
for row in df_baptism_dates.rows():
    baptism = BNode()
    g.add((baptism, RDF.type, BIO.Baptism))
    g.add((baptism, RICO.date, date_lit(row[1])))
    g.add((agent(row[0]), BIO.event, baptism))

# Add dates of death.
for row in df_death_dates.rows():
    g.add((agent(row[0]), RICO.hasDeathDate, date_lit(row[1])))

# Add dates of burial.
for row in df_burial_dates.rows():
    burial = BNode()
    g.add((burial, RDF.type, BIO.Burial))
    g.add((burial, RICO.date, date_lit(row[1])))
    g.add((agent(row[0]), BIO.event, burial))

for row in df_children.rows():
    g.add((agent(row[0]), RICO.hasChild, agent(row[1])))

for row in df_spouses.rows():
    g.add((agent(row[0]), RICO.hasOrHadSpouse, agent(row[1])))

# Save graph to file.
g.serialize(destination=DATA_PATH / "output" / "agents.ttl")
