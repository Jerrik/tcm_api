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
    var_dump($data);
    $result = array_search($id, $data);
    var_dump($result);
    die;
    $results = $data['results'];
    return [
      'id' => $results['id'],
      'title' => $results['tcm']['titles']['name'],
      'description' => $results['tcm']['titles']['description'],
      'image' => $results['tcm']['titles']['imageProfiles'][0]['url'],
      'year' => $results['tcm']['titles']['releaseYear'],
    ];
  }

}
