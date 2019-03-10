<?php

namespace Drupal\tcm_api\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Url;
/**
 * Returns video search results
 */
class MovieSearchForm extends FormBase {
/**

 * {@inheritdoc}

 */

  private $endpoint = "http://api.tcm.com/tcmws/v1/vod/latest/6.json";

  public function getFormId() {
    return 'movie_search_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['search_box'] = [
      '#type' => 'textfield',
      '#title' => 'Search for a movie.',
      '#size' => 60,
      '#maxlength' => 128,
      '#required' => TRUE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Find good stuff',
      '#button_type' => 'primary',
      '#ajax' => [
        'callback' => 'Drupal\tcm_api\Form\MovieSearchForm::searchResults',
        'effect' => 'fade',
      ],
    ];

    return $form;

  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Do nothing
  }

  public function searchResults(array $form, FormStateInterface $form_state) {

    $ajax_response = new AjaxResponse();

    $input = $form_state->getValue();
    $results = $this->findMovies($input);

    $ajax_response->addCommand(new HtmlCommand('#search_results', $results));

    return $ajax_response;
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
  public function findMovies($needle) {
    $client = new Client();
    $response = $client->request('GET', $this->endpoint());
    $data = json_decode($response->getBody(), TRUE);
    $movieIds = $this->getMovieIds($data, $needle);

    $movie_entities = [];

    foreach ($movieIds as $id) {
      $movie_entities[] = $this->getMovieEntity($id);
    }

    return $movie_entities;
  }

  /**
   * Retrieves Movie title ID's.
   */
  public function getMovieIds($data, $needle) {
    $ids = [];
    // Find our movie based on titleId.
    $results = $data['tcm']['titles'];
    foreach ($results as $result) {
      if (in_array($needle, $result)){
        $ids[] = $result['titleId'];
      }
    }

    return $ids;
  }

    /**
   * Loads each movie as an entity.
   */
  public function getMovieEntity($titleId) {
    $entity = NULL;
    if ($titleId) {
      $movie_storage = \Drupal::entityTypeManager()->getStorage('movie');
      $entity = $movie_storage->load($titleId);
    }

    return $entity;

  }

}
