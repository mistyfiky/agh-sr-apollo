<?php
include 'OmdbService.php';

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
            $result = json_decode($omdbService->searchByTitle($_POST['title'], $params), true);
            if ($result["Response"] !== "True") {
                die("Error");
            }
            $cards = "";
            $mod4 = 0;
            foreach ($result["Search"] as $movie) {
                if ($movie["Poster"] == 'N/A') {
                    continue;
                }
                if (($mod4 % 4) == 0) {
                    $cards .= '<div class="row flex">';
                }
                $cards .= '<div class="col s12 m3" ><div class="card"><div class="card-image">';
                $cards .= '<img src="' . $movie["Poster"] . '">';
                $cards .= '<span class="card-title">' . $movie["Title"] . '</span></div><div class="card-content"><p>IMDB ID: ';
                $cards .= $movie["imdbID"] . '</p></div></div></div>';
                if (($mod4 % 4) == 3) {
                    $cards .= '</div>';
                }
                $mod4++;
            }
            die($cards);
    }

}
