# Qlever
Umgebung für [qlever-control](https://github.com/ad-freiburg/qlever-control)

Um den Index aufzubauen muss der RDF-Datensatz in das Verzeichnis kopiert werden.
```bash
cp ../data-factory/data/output/agents_expanded.ttl ./
```

Initialisierung Index und Server starten
```bash
qlever index && qlever start
```