<?php

namespace Drupal\tcm_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
use Drupal\Core\Entity\ContentEntityNullStorage;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MoviesController extends ControllerBase {

	private $endpoint = "http://api.tcm.com/tcmws/v1/vod/latest/6.json";

	public function moviesPage() {

		$movies = $this->getMovies();
		// Render Array
		$build = array(
			'#theme' => 'movies',
			'#type' => 'markup',
			'#attached' => array(
				'library' => array(
					'tcm_api/movies',
				),
			),
			'#movies' => $movies,
		);

		return $build;
	}

	public function endpoint() {
    return $this->endpoint;
	}

  public function getMovies() {
    $client = new Client();
    $response = $client->request('GET', $this->endpoint());
		$data = json_decode($response->getBody(), TRUE);
		$movieIds = $this->getMovieIds($data);

		$movie_entities = array();

		foreach ($movieIds as $id) {
			$movie_entities = $this->getMovieEntity($id);
		}

		return $movie_entities;
	}

	public function getMovieIds($data) {
		$ids = array();
    // Find our movie based on titleId.
    $results = $data['tcm']['titles'];
    foreach ($results as $result) {
			$ids[] = $result['titleId'];
		}

    return $ids;
  }

	public function getMovieEntity($titleId){
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
		} else {
			// If no Title ID was found, throw 404.
			throw new NotFoundHttpException();
		}

		return $entity;
	}


}