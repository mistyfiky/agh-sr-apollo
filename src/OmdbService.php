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

    public function searchByList(array $moviesIds)
    {
        $moviesIdsList = implode(', ', $moviesIds);
        $result = $this->client->run("MATCH (m:Movie) WHERE m.id IN [{$moviesIdsList}] RETURN m");
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
        } else if (sizeof($genres) === 1) {
            $genresList = $genres[0]['genreObject']['id'];
        } else if (sizeof($genres) > 1) {
            $genresList = $genres[0]['genreObject']['id'] . "," . $genres[rand(1, sizeof($genres) - 1)]['genreObject']['id'];
        }
        return $this->recommendByGenres($watchedMoviesList, $genresList);
    }

    private function recommendByGenres($watchedMoviesList, $genresList)
    {
        $result = $this->client->run("MATCH (m:Movie)-[r:HAS_GENRE]->(g:Genre) WITH m, rand() as random WHERE NOT m.id IN [{$watchedMoviesList}] AND g.id IN [{$genresList}] RETURN DISTINCT m, random ORDER BY random LIMIT 8");
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

    public function generateCards($movieNodes, $addable = true)
    {
        $cards = "";
        $mod4 = 0;
        foreach ($movieNodes as $movie) {
            if (($mod4 % 4) == 0) {
                $cards .= '<div class="row flex">';
            }
            $cards .= $this->generateCard($movie, $addable);
            if (($mod4 % 4) == 3) {
                $cards .= '</div>';
            }
            $mod4++;
        }
        return $cards;
    }

    protected function generateCard($movieNode, $addable = true)
    {
        $card = "";
        $card .= '<div class="col s12 m3 movie-card-col">';
        $card .= '<div class="card">';
        $card .= '<div class="card-image">';
        $card .= '<img src="' . $movieNode['poster_image'] . '">';
        $card .= '<span class="card-title">' . $movieNode['title'] . '</span>';
        if ($addable) {
            $card .= '<a class="btn-floating halfway-fab waves-effect waves-light red add-movie" data-movieid="' . $movieNode['id'] . '"><i class="material-icons">add</i></a>';
        }
        $card .= '</div>';
        $card .= '<div class="card-content">';
        $card .= '<p>ID: ' . $movieNode['id'] . '</p>';
        $card .= '</div></div></div>';
        return $card;
    }

}
