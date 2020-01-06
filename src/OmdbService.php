<?php

namespace App;

use GraphAware\Common\Type\Node;
use GraphAware\Neo4j\Client\ClientBuilder;

class OmdbService
{
    private $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->addConnection('bolt', 'bolt://delphi')
            ->build();
    }

    public function searchByTitle(string $title, $parameters = [])
    {
        $result = $this->client->run("MATCH (m:Movie) WHERE m.title =~ '(?i).*{$title}.*' RETURN m");
        $movies = [];
        foreach ($result->records() as $record) {
            foreach ($record->values() as $value) {
                if ($value instanceof Node) {
                    $movies[] = $value->values();
                }
            }
        }
        return $movies;
    }

    public function searchByGenre(string $genre)
    {
        $result = $this->client->run("MATCH (m:Movie)-[:HAS_GENRE]->(g:Genre) WHERE g.id = {$genre} RETURN m");
        $movies = [];
        foreach ($result->records() as $record) {
            foreach ($record->values() as $value) {
                if ($value instanceof Node) {
                    $movies[] = $value->values();
                }
            }
        }
        return $movies;
    }

    public function availableGenres() {
        $result = $this->client->run("MATCH (g:Genre) RETURN g");
        $genres = [];
        foreach ($result->records() as $record) {
            foreach ($record->values() as $value) {
                if ($value instanceof Node) {
                    $genres[] = $value->values();
                }
            }
        }
        return $genres;
    }
}
