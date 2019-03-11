<?php

namespace Drupal\tcm_api\Entity;

use GuzzleHttp\Client;
use Drupal\Core\Entity\ContentEntityNullStorage;

/**
 *
 */
class MovieStorage extends ContentEntityNullStorage {

  /**
   * The default info for building our API request
   * @var string
   */
  private $endpoint = "http://api.tcm.com/tcmws/v1/vod/latest/6.json";

  /**
   * {@inheritdoc}
   */
  public function load($id, $default = NULL) {
    $movie = $this->getMovie($id);
    return isset($movie) ? $movie : $default;
  }

  /**
   *
   */
  public function getMovie($id) {
    $client = new Client();
    $response = $client->request('GET', $this->endpoint());
    $data = json_decode($response->getBody(), TRUE);
    return $this->mapValues($data, $id);
  }

  /**
   *
   */
  public function endpoint() {
    return $this->endpoint;
  }

  /**
   *
   */
  public function mapValues($data, $id) {

    // Find our movie based on titleId.
    $results = $data['tcm']['titles'];
    foreach ($results as $result) {
      if ($result['titleId'] == $id) {
        $movie = $result;
        break;
      }
    }

    // Return mapped data.
    return [
      'id' => $movie['titleId'],
      'title' => $movie['name'],
      'description' => $movie['description'],
      'image' => $movie['imageProfiles'][0]['url'],
      'year' => $movie['releaseYear'],
      'runtime' => $movie['runtimeMinutes'],
      'rating' => $movie['tvRating'],
    ];
  }

}
