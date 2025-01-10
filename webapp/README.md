# webapp
In diesem Verzeichnis befindet sich der Programmcode für die Webapplikation zur Exploration des RDF-Datensatzes.

## Inbetriebnahme
Folgend Voraussetzungen müssen erfüllt sein um die Applikation auszuführen:
- Ein Qlever Server ist vorhanden
- PHP, Composer, Node und NPM müssen installiert sein: https://laravel.com/docs/11.x/installation#installing-php

### Installation
Installiere Abhängigkeiten des Projekts:
```bash
composer install && npm i
```
Erstelle eine Kopie der Enviroment-Konfigurationsdatei und generiere den application key:
```bash
cp .env.example .env && ./artisan key:generate
```

### Lokalen Entwicklungsserver starten
```bash
composer dev run
```

## Lizenzen
- Die Webapplikation ist mit dem Laravel Framework umgsetzt welches der [MIT Lizenz](https://github.com/laravel/laravel) untersteht.
- Der Programmcode der Webapplikation ist unter der [MIT Lizenz](LICENSE.md) verfügbar.
