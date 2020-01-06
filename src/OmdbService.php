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
        $result = $this->client->run("MATCH (n:Movie) WHERE n.title =~ '(?i).*{$title}.*' RETURN n");
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
}
