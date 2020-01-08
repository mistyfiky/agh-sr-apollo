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
            ->addConnection('bolt', getenv('NEO4J_URI'))
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

    public function searchByGenres(array $genres)
    {
        $genresList = implode(', ', $genres);
        $result = $this->client->run("MATCH (m:Movie)-[:HAS_GENRE]->(g:Genre) WHERE g.id IN [{$genresList}] RETURN m");
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

    public function availableGenres()
    {
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

    public function recommend(array $watchedMoviesIds)
    {
        $watchedMoviesList = implode(', ', $watchedMoviesIds);
        $genres = $this->countGenreOccurances($watchedMoviesList);
        if (sizeof($genres) === 0) {

        } else if (sizeof($genres) > 1) {
            $genresList = $genres[0]['genreObject']['id'] . "," . $genres[1]['genreObject']['id'];
        }

        return $this->recommendByGenres($watchedMoviesList, $genresList);
    }

    private function recommendByGenres($watchedMoviesList, $genresList) {
        $result = $this->client->run("MATCH (m:Movie)-[r:HAS_GENRE]->(g:Genre) WITH m, rand() as random WHERE NOT m.id IN [{$watchedMoviesList}] AND g.id IN [{$genresList}] RETURN m ORDER BY random LIMIT 5");
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

    private function countGenreOccurances($watchedMoviesList)
    {
        $genresArray = [];
        $result = $this->client->run("MATCH (m:Movie)-[r:HAS_GENRE]->(g:Genre) WHERE m.id IN [{$watchedMoviesList}] RETURN count(r),g ORDER BY count(r) DESC");
        foreach ($result->records() as $record) {
            array_push($genresArray, [
                'occurances' => $record->values()[0],
                'genreObject' => $record->values()[1]->values()
            ]);
        }
        return $genresArray;
    }

}
