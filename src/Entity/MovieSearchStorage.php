<?php

namespace Drupal\tcm_api\Entity;

use GuzzleHttp\Client;
use Drupal\Core\Entity\ContentEntityNullStorage;

/**
 *
 */
class MovieSearchStorage extends ContentEntityNullStorage {

  /**
   * The default info for building our API request
   * @var string
   */
  private $endpoint = "http://18.191.140.78/tcm-db/csv";

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
      'description' => $movie['synopsis'],
      'image' => $movie['profileImageUrl'],
      'year' => $movie['releaseYear'],
      'runtime' => $this->convertToHoursMins($movie['runtimeMinutes']),
      'rating' => $movie['mpaaRating'],
    ];
  }

  public function convertToHoursMins($minutes) {
    $format = '%02d:%02d';
    if ($minutes < 1) {
      return;
    }
    $hours = floor($minutes / 60);
    $minutes = ($minutes % 60);
    return sprintf($format, $hours, $minutes);
  }

}
