<?php

namespace App\Api;

use App\Entity\ComicSeries;
use App\Entity\Comic;
use App\Entity\ComicHistory;
use App\Entity\User;
use Symfony\Component\HttpClient\HttpClient;

class BooksApiConnector
{

private $key = 'b307961f6b028295988492d216b5213070f1b213';
private $client;
private $baseUrl = "https://comicvine.gamespot.com/api";

public function __construct()
{
    $client = new Google\Client();
    $client->setApplicationName("Client_Library_Examples");
    $client->setDeveloperKey("YOUR_APP_KEY");

    $service = new Google_Service_Books($client);
    $optParams = array(
      'filter' => 'free-ebooks',
      'q' => 'Henry David Thoreau'
    );
    $results = $service->volumes->listVolumes($optParams);

    foreach ($results->getItems() as $item) {
      echo $item['volumeInfo']['title'], "<br /> \n";
    }
}


}




 ?>
