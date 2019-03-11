<?php

namespace Drupal\tcm_api\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

/**
 * Implements an example form.
 */
class MovieSearch extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'movie_search';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['search_box'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'movie',
      '#title' => 'Search for a movie.',
      '#size' => 60,
      '#maxlength' => 128,
      '#required' => TRUE,
      '#selection_settings' => [
        'target_bundles' => ['movie'],
    ]
      // '#ajax' = [
      //   'callback' =>'get_movie_titles',
      //   'event' => 'autocompleteclose',
      // ];
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue('search_box')) < 3) {
      $form_state->setErrorByName('search_box', $this->t('Please try to be more specific.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

		//$results = $this->findMovies($form_state->getValue('search_box'));
		$needle = $form_state->getValue('search_box');
		$form_state->setRedirect('tcm_api.search_results', ['search' => $needle]);

  }

}