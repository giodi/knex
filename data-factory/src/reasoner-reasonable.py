from pathlib import Path

import rdflib
import reasonable

DATA_PATH = Path("../data")

input_graph = rdflib.Graph()
input_graph.parse(DATA_PATH / "output" / "agents.ttl")
input_graph.parse(DATA_PATH / "input" / "onto_RiC-O_1-0-2.rdf", format="application/rdf+xml")
input_graph.parse(DATA_PATH / "input" / "onto_bio_0.1.rdf", format="application/rdf+xml")

r = reasonable.PyReasoner()
r.from_graph(input_graph)
triples = r.reason()
print("triples count: ", len(triples))

output_graph = rdflib.Graph()
for t in triples:
    output_graph.add(t)
output_graph.serialize(DATA_PATH / "output" / "agents_expanded.ttl", format='ttl')