<?php

searchByTitle('daredevil', ['type' => 'movie']);

function findByTitle(string $title)
{
    prettyPrintResult(sendRequest('t', urlencode($title)));
}

function searchByTitle(string $title, $parameters = [])
{
    prettyPrintResult(sendRequest('s', urlencode($title), $parameters));
}

function sendRequest(string $queryField, string $queryValue, array $parameters = [])
{
    $ch = curl_init();
    $request = "http://www.omdbapi.com/?apikey=70bc1bb9&";
    $request .= $queryField . "=" . $queryValue;
    if (!empty($parameters)) {
        $request .= "&" . http_build_query($parameters);
    }
    curl_setopt($ch, CURLOPT_URL, $request);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function prettyPrintResult($requestResult)
{
    echo "<pre>";
    var_dump(json_decode($requestResult));
    echo "</pre>";
}