<?php

namespace Drupal\tcm_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Client;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
  public function getMovies($find = false) {
    $client = new Client();
    if (!$find) {
      $response = $client->request('GET', $this->endpoint());
    }
    else {
      $response = $client->request('GET', 'http://18.191.140.78/tcm-db/csv');
    }
    $data = json_decode($response->getBody(), TRUE);
    if (!$find) {
      $movieIds = $this->getMovieIds($data);
    } else {
      $movieIds = $this->findMovieIds($data, $find);
    }

    $movie_entities = [];
    $search = ($find == false ? false : true);
    foreach ($movieIds as $id) {
      $movie_entities[] = $this->getMovieEntity($id, $search);
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
  public function getMovieEntity($titleId,$search = FALSE) {
    $entity = NULL;

    if ($titleId) {
      // Get a storage object.
      if (!$search) {
        $movie_storage = \Drupal::entityTypeManager()->getStorage('movie');
      } else {
        $movie_storage = \Drupal::entityTypeManager()->getStorage('movie_search');
      }
      $entity = $movie_storage->load($titleId);

      // If Movie could not be loaded, throw 404.
      if (!$entity) {
        throw new NotFoundHttpException();
      }
    }
    else {
      // If no Title ID was found, throw 404.
      throw new NotFoundHttpException();
    }

    return $entity;

  }

  public function renderResults($search) {

    $results = $this->getMovies($search);

    $build = [
      '#theme' => 'search_results',
      '#results' => $results,
    ];
    // This is the important part, because will render only the TWIG template.
    return $build;
  }

  /**
   * Retrieves Movie title ID's.
   */
  public function findMovieIds($data, $needle) {
    $ids = [];
    // Find our movie based on titleId.
    $results = $data['tcm']['titles'];
    foreach ($results as $result) {

      // Things that content can be searched by
      $haystack = $result['name'];

      if (strpos(strtolower($haystack), strtolower($needle)) !== false){
        $ids[] = $result['titleId'];
      }
    }

    return $ids;
  }

  public function handleAutocomplete(Request $request) {
    $results = [];

    // Get input string
    if ($input = $request->query->get('q')) {
      $movies = $this->getMovies($input);
      foreach ($movies as $movie) {
        $results[] = [
          'value' => $movie['title'],
          'label' => $movie['title']
        ];
      }
    }
    return new JsonResponse($results);
  }

  public function csvFile() {
    $file = 'https://s3.us-east-2.amazonaws.com/tcm-demo/CodeChallengeData.csv';
    $delimiter = ',';
    $handle = fopen($file, 'r');
    if (!$handle) {
      die('Error opening file');
    }

    $headers = fgetcsv($handle, 4000, $delimiter);
    $csv2json = array();

    while ($row = fgetcsv($handle, 4000, $delimiter)) {
      $csv2json[] = array_combine($headers, $row);
    }

    fclose($handle);

    $return = [
      'tcm' => [
        'titles' => $csv2json
      ]
      ];

    // $csv = file_get_contents($file);
    // $array = array_map("str_getcsv", explode("\n", $csv));
    $data = json_encode($return);

    return new Response($data);
  }

}
