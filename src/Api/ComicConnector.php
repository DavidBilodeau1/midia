<?php

namespace App\Api;

use App\Entity\ComicSeries;
use App\Entity\Comic;
use App\Entity\ComicHistory;
use App\Entity\User;
use Symfony\Component\HttpClient\HttpClient;

class ComicConnector
{

private $key = 'b307961f6b028295988492d216b5213070f1b213';
private $client;
private $baseUrl = "https://comicvine.gamespot.com/api";

public function __construct()
{
    $this->client = HttpClient::create();
}


public function search($query, $limit = "10", $offset="", $format = "json", $fields = "")
{
    $params = [];
    $params['query'] = [];
    $params['query']['api_key'] = $this->key;
    $params['query']['query'] = $query;
    $params['query']['limit'] = $limit;
    $params['query']['format'] = $format;
    $fields != "" ?? ($params['query']['fields'] = $fields);
    $offset != "" ?? ($params['query']['offset'] = $offset);
    $response = $this->client->request('GET', $this->baseUrl . '/search/', $params);
    return $response->getContent();
}

public function details($url, $format = "json", $fields = "")
{
    $params = [];
    $params['query'] = [];
    $params['query']['api_key'] = $this->key;
    $params['query']['format'] = $format;
    $fields != "" ?? ($params['query']['fields'] = $fields);
    $response = $this->client->request('GET', $url, $params);
    return $response->getContent();
}



}




 ?>
