<?php

namespace App;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Symfony\Component\Console\Helper\Helper;

class Sparql
{

    public static function get(string $query): ?array
    {

        $endpoint = env('QLEVER_URL', 'http://localhost:7019/?query=');
        $request = $endpoint.urlencode($query);
        $get = Http::get($request);

        if($get->successful()){
            $json = $get->json();
            return count($json['results']['bindings']) > 0 ? $json['results']['bindings'] : null;
        }

        return null;
    }

    public static function person(string $id): ?array
    {

        $query = '
            PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
            PREFIX rico: <https://www.ica.org/standards/RiC/ontology#>
            SELECT DISTINCT ?name ?sex ?desc ?birth_date ?baptism_date ?death_date ?burial_date ?id_type ?scope_id {
              ?person a rico:Person ;
                      rico:hasOrHadIdentifier ?id_ark ;
                      rico:hasOrHadIdentifier ?id_scope ;
                      rico:hasOrHadName ?name ;
                      rico:hasOrHadDemographicGroup ?demographic_groups .
              ?id_ark rico:hasIdentifierType <https://burgerbib.ch/IdentifierTypes/ARK> ;
                      rico:normalizedValue "'.$id.'" .
              ?id_scope rico:hasIdentifierType <https://burgerbib.ch/IdentifierTypes/Scope-ID> ;
                        rico:normalizedValue ?scope_id .
              ?demographic_groups rdfs:label ?sex .
              OPTIONAL {
                ?person rico:hasBirthDate ?birth_date
              } .
              OPTIONAL {
                ?person rico:hasDeathDate ?death_date
              } .
              OPTIONAL {
                ?person rico:generalDescription ?desc
              } .
              OPTIONAL {
                ?person rico:isAssociatedWithEvent ?event .
                ?event rico:hasEventType <https://burgerbib.ch/EventTypes/Baptism> ;
                       rico:occurredAtDate ?baptism_date .
              } .
              OPTIONAL {
                ?person rico:isAssociatedWithEvent ?event .
                ?event rico:hasEventType <https://burgerbib.ch/EventTypes/Burial> ;
                       rico:occurredAtDate ?burial_date .
              } .
            }
            LIMIT 1
        ';

        return self::get($query);

    }

    public static function spouses(string $id): ?array
    {

        $query = '
            PREFIX rico: <https://www.ica.org/standards/RiC/ontology#>
            SELECT ?ark ?name {
              <https://burgerbib.ch/indexterms/ark:36599/'.$id.'> rico:hasOrHadSpouse ?spouse .
              ?spouse rico:hasOrHadName ?name ;
                      rico:hasOrHadIdentifier ?id_spouse .
              ?id_spouse rico:hasIdentifierType <https://burgerbib.ch/IdentifierTypes/ARK> ;
                         rico:normalizedValue ?ark .
            }
        ';

        return self::get($query);

    }

    public static function lifePartner(string $id): ?array
    {

        $query = '
            PREFIX rico: <https://www.ica.org/standards/RiC/ontology#>
            SELECT ?ark ?name {
              <https://burgerbib.ch/indexterms/ark:36599/'.$id.'> rico:hasOrHadLifePartner ?partner .
              ?partner rico:hasOrHadName ?name ;
                      rico:hasOrHadIdentifier ?id_partner .
              ?id_partner rico:hasIdentifierType <https://burgerbib.ch/IdentifierTypes/ARK> ;
                         rico:normalizedValue ?ark .
            }
        ';

        return self::get($query);

    }

    public static function children(string $id): ?array
    {

        $query = '
            PREFIX rico: <https://www.ica.org/standards/RiC/ontology#>
            SELECT ?ark ?name {
              <https://burgerbib.ch/indexterms/ark:36599/'.$id.'> rico:hasChild ?child .
              ?child rico:hasOrHadName ?name ;
                     rico:hasOrHadIdentifier ?id_child .
              ?id_child rico:hasIdentifierType <https://burgerbib.ch/IdentifierTypes/ARK> ;
                        rico:normalizedValue ?ark .
            }
        ';

        return self::get($query);

    }

    public static function children_extended(string $id): ?array
    {

        $query = '
            PREFIX rico: <https://www.ica.org/standards/RiC/ontology#>
            SELECT DISTINCT ?ark_parent ?ark_child ?name_child {
              <https://burgerbib.ch/indexterms/ark:36599/'.$id.'> rico:hasChild+ ?child .
              ?child rico:hasOrHadName ?name_child ;
                     rico:hasOrHadIdentifier ?id_child ;
                     rico:isChildOf ?parent .
              ?parent rico:hasOrHadIdentifier ?id_parent .

              ?id_child rico:hasIdentifierType <https://burgerbib.ch/IdentifierTypes/ARK> ;
                        rico:normalizedValue ?ark_child .
              ?id_parent rico:hasIdentifierType <https://burgerbib.ch/IdentifierTypes/ARK> ;
                        rico:normalizedValue ?ark_parent .
            }
        ';

        return self::get($query);

    }

    public static function parents(string $id): ?array
    {

        $query = '
            PREFIX rico: <https://www.ica.org/standards/RiC/ontology#>
            SELECT ?ark ?name {
              <https://burgerbib.ch/indexterms/ark:36599/'.$id.'> rico:isChildOf ?parent .
              ?parent rico:hasOrHadName ?name ;
                      rico:hasOrHadIdentifier ?id_parent .
              ?id_parent rico:hasIdentifierType <https://burgerbib.ch/IdentifierTypes/ARK> ;
                         rico:normalizedValue ?ark .
            }
        ';

        return self::get($query);

    }

    public static function siblings(string $id): ?array
    {

        $query = "
            PREFIX rico: <https://www.ica.org/standards/RiC/ontology#>
            SELECT DISTINCT ?ark  ?name {
              <https://burgerbib.ch/indexterms/ark:36599/{$id}> rico:isChildOf ?parent .
              ?parent rico:hasChild ?child .
              ?child rico:hasOrHadIdentifier ?id ;
                     rico:hasOrHadName ?name .
              ?id rico:hasIdentifierType <https://burgerbib.ch/IdentifierTypes/ARK> ;
                  rico:normalizedValue ?ark .
              FILTER (?ark != 'ark:36599/{$id}')
            }
        ";

        return self::get($query);

    }

    public static function list(int $offset = 0): ?array
    {

        $query = "
            PREFIX rico: <https://www.ica.org/standards/RiC/ontology#>
            PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
            SELECT DISTINCT ?name ?birth_date ?death_date ?ark {
              ?person a rico:Person ;
                      rico:hasOrHadIdentifier ?id ;
                      rico:hasOrHadName ?name ;
                      rico:hasBirthDate ?birth_date ;
                      rico:hasDeathDate ?death_date .
              ?id rico:hasIdentifierType ?idtype ;
                  rico:normalizedValue ?ark .
              FILTER (?idtype = <https://burgerbib.ch/IdentifierTypes/ARK>)
            }
            ORDER BY ASC(?name)
            LIMIT 100 OFFSET ".($offset * 100);

        return self::get($query);


    }

    public static function search(string $term, array $options, int $offset = 0): ?array
    {
        $hasDescendant = $options['hasDescendant'] ? 'rico:hasDescendant ?descendant ;' : '';
        $birthYear = $options['birthYear'] ? "BIND(YEAR(?birth_date) AS ?birth_year) FILTER (?birth_year = '{$options['birthYear']}'^^xsd:int)" : '';
        $birthMonth = $options['birthMonth'] ? "BIND(MONTH(?birth_date) AS ?birth_month) FILTER (?birth_month = '{$options['birthMonth']}'^^xsd:int)" : '';
        $birthDay = $options['birthDay'] ? "BIND(DAY(?birth_date) AS ?birth_day) FILTER (?birth_day = '{$options['birthDay']}'^^xsd:int)" : '';
        $deathYear = $options['deathYear'] ? "BIND(YEAR(?death_date) AS ?death_year) FILTER (?death_year = '{$options['deathYear']}'^^xsd:int)" : '';
        $deathMonth = $options['deathMonth'] ? "BIND(MONTH(?death_date) AS ?death_month) FILTER (?death_month = '{$options['deathMonth']}'^^xsd:int)" : '';
        $deathDay = $options['deathDay'] ? "BIND(DAY(?death_date) AS ?death_day) FILTER (?death_day = '{$options['deathDay']}'^^xsd:int)" : '';

        if($term){
            $term_split = explode(' ', $term);
            $term_query = '';
            foreach ($term_split as $t) {
                $term_query .= "CONTAINS(LCASE(?name),LCASE('{$t}')) && ";
            }
            $term = 'FILTER ('.rtrim($term_query, '&& ').')';
        }else{
            $term = '';
        }

        $query = "
            PREFIX rico: <https://www.ica.org/standards/RiC/ontology#>
            PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
            SELECT DISTINCT ?name ?birth_date ?death_date ?ark {
              ?person a rico:Person ;
                      rico:hasOrHadIdentifier ?id ;
                      rico:hasOrHadName ?name ;
                      rico:hasBirthDate ?birth_date ;
                      {$hasDescendant}
                      rico:hasDeathDate ?death_date .
              ?id rico:hasIdentifierType ?idtype ;
                  rico:normalizedValue ?ark .
              FILTER (?idtype = <https://burgerbib.ch/IdentifierTypes/ARK>)
              {$term}
              {$birthYear}
              {$birthMonth}
              {$birthDay}
              {$deathYear}
              {$deathMonth}
              {$deathDay}
            }
            ORDER BY ASC(?name)
            LIMIT 100 OFFSET ".($offset * 100);
        return self::get($query);
    }

    public static function filterByBirthdate(string $from, string $to, int $offset = 0): ?array
    {

        $query = "
        PREFIX rico: <https://www.ica.org/standards/RiC/ontology#>
        PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
        SELECT DISTINCT ?name ?sex ?birth_date ?baptism_date ?death_date ?burial_date ?id_type ?ark {
          ?person a rico:Person ;
                  rico:hasOrHadIdentifier ?id_primary ;
                  rico:hasOrHadIdentifier ?id_secondary ;
                  rico:hasOrHadName ?name ;
                  rico:hasBirthDate ?birth_date ;
                  rico:hasDeathDate ?death_date .
          OPTIONAL {
            ?person rico:isAssociatedWithEvent ?event .
            ?event rico:hasEventType <https://burgerbib.ch/EventTypes/Baptism> ;
                   rico:occurredAtDate ?baptism_date .
          } .
          OPTIONAL {
            ?person rico:isAssociatedWithEvent ?event .
            ?event rico:hasEventType <https://burgerbib.ch/EventTypes/Burial> ;
                   rico:occurredAtDate ?burial_date .
          } .
          ?id_primary rico:hasIdentifierType <https://burgerbib.ch/IdentifierTypes/ARK> ;
                      rico:normalizedValue ?ark .
          FILTER (?birth_date > '{$from}'^^xsd:date && ?birth_date < '{$to}'^^xsd:date && DATATYPE(?death_date) = xsd:date)
        }
        ORDER BY DESC (?birth_date)
        LIMIT 50 OFFSET ".($offset * 100);

        return self::get($query);


    }


}
