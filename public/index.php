<?php

namespace App;

use Sentry;

require_once __DIR__ . '/../vendor/autoload.php';

Sentry\init(['dsn' => getenv('SENTRY_DSN') ]);

if (!isset($_GET['function'])) {
    die(json_encode([
        'meta' => [
            'message' => 'turbo źródler 3000!'
        ]
    ]));
} elseif ($_GET['function']) {
    $omdbService = new OmdbService();
    $functionName = $_GET['function'];
    switch ($functionName) {
        case 'searchByTitle':
            $params = [];
            if ($_POST['type']) {
                $params['type'] = $_POST['type'];
            }
            $movies = $omdbService->searchByTitle($_POST['title'], $params);
            die($omdbService->generateCards($movies));
        case 'searchByGenres':
            $movies = $omdbService->searchByGenres($_POST['genres']);
            die($omdbService->generateCards($movies));
        case 'getAvailableGenres':
            $genres = $omdbService->availableGenres();
            $options = "";
            foreach ($genres as $genre) {
                $options .= '<option value="' . $genre['id'] . '">' . $genre['name'] . '</option>';
            }
            die($options);
        case 'searchByList':
            $movies = $omdbService->searchByList($_POST['moviesIds']);
            die($omdbService->generateCards($movies, false));
        case 'recommend':
            $movies = $omdbService->recommend($_POST['moviesIds']);
            die($omdbService->generateCards($movies));
        case 'triggerError':
            throw new \Exception("Nie ma to jak pisać specjalnie funkcje które służą tylko temu, żeby się wywalić.");
    }

}
