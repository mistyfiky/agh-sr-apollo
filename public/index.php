<?php

namespace App;

require_once __DIR__ . '/../vendor/autoload.php';

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
            $cards = "";
            $mod4 = 0;
            foreach ($movies as $movie) {
                if (($mod4 % 4) == 0) {
                    $cards .= '<div class="row flex">';
                }
                $cards .= '<div class="col s12 m3" ><div class="card"><div class="card-image">';
                $cards .= '<img src="' . $movie['poster_image'] . '">';
                $cards .= '<span class="card-title">' . $movie['title'] . '</span></div><div class="card-content"><p>ID: ';
                $cards .= $movie['id'] . '</p></div></div></div>';
                if (($mod4 % 4) == 3) {
                    $cards .= '</div>';
                }
                $mod4++;
            }
            die($cards);
    }

}
