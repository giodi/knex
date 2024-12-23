from pathlib import Path

from owlrl import DeductiveClosure, OWLRL_Extension
from rdflib import Graph as _Graph
from rdflib import Literal

DATA_PATH = Path("../data")

# patch hack to catch triples w/ Literal as subject: https://github.com/RDFLib/OWL-RL/issues/50#issuecomment-1476897684
class Graph(_Graph):
    
    def __init__(self, *p, **k):
        self._offensive = set()
        super().__init__(*p, **k)
    
    def add(self, t):
        if isinstance(t[0], Literal):
            self._offensive.add(t)
        # so that it keeps working as usual
        return super().add(t)

# Load dataset and ontologies.
g = Graph()
g.parse(DATA_PATH / "output" / "agents.ttl")
g.parse(DATA_PATH / "input" / "onto_RiC-O_1-0-2.rdf", format="application/rdf+xml")
g.parse(DATA_PATH / "input" / "onto_bio_0.1.rdf", format="application/rdf+xml")

# Apply reasoning using 
DeductiveClosure(OWLRL_Extension, rdfs_closure = True, axiomatic_triples = True, datatype_axioms = True).expand(g)

# Remove offensive triples
for offensive_triple in g._offensive:
    g.remove(offensive_triple)

# Save the expanded RDF graph
g.serialize(destination= DATA_PATH / "output" / "agents_expanded.ttl")