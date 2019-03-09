<?php

namespace Drupal\tcm_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Client;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller class.
 */
class MoviesController extends ControllerBase {

  private $endpoint = "http://api.tcm.com/tcmws/v1/vod/latest/6.json";

  /**
   * All movies page.
   */
  public function moviesPage() {

    $movies = $this->getMovies();
    // Render Array.
    $build = [
      '#theme' => 'movies',
      '#movies' => $movies,
      '#attached' => [
        'library' => [
          'tcm_api/movies',
        ],
      ],
    ];

    return $build;
  }

  /**
   * API enpoint.
   */
  public function endpoint() {
    return $this->endpoint;
  }

  /**
   * Retrieves Movie ID's and then retrieves each movie as an entity.
   */
  public function getMovies() {
    $client = new Client();
    $response = $client->request('GET', $this->endpoint());
    $data = json_decode($response->getBody(), TRUE);
    $movieIds = $this->getMovieIds($data);

    $movie_entities = [];

    foreach ($movieIds as $id) {
      $movie_entities[] = $this->getMovieEntity($id);
    }

    return $movie_entities;
  }

  /**
   * Retrieves Movie title ID's.
   */
  public function getMovieIds($data) {
    $ids = [];
    // Find our movie based on titleId.
    $results = $data['tcm']['titles'];
    foreach ($results as $result) {
      $ids[] = $result['titleId'];
    }

    return $ids;
  }

  /**
   * Loads each movie as an entity.
   */
  public function getMovieEntity($titleId) {
    $movie_class = 'Drupal\tcm_api\Entity\Movie';
    $entity = NULL;

    if ($titleId) {
      // Get a storage object.
      $movie_storage = \Drupal::entityTypeManager()->getStorage('movie');
      $entity = $movie_storage->load($titleId);

      // If Movie could not be loaded, throw 404.
      if (!($entity instanceof $movie_class)) {
        throw new NotFoundHttpException();
      }
    }
    else {
      // If no Title ID was found, throw 404.
      throw new NotFoundHttpException();
    }

    return $entity;

  }

}
