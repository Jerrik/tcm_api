<?php

namespace Drupal\tcm_api\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\tcm_api\Entity\Movie;

/**
 * Plugin implementation of the 'movie_reference' formatter.
 *
 * @FieldFormatter(
 *   id = "movie_reference",
 *   label = @Translation("Movie reference"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class MovieReference extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      // Implement settings form.
    ] + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $id = $this->viewValue($item);
      $entity = Movie::load($id);
      $elements[$delta] = [
        '#theme' => 'movie',
        '#id' => $entity['id'],
        '#title' => $entity['title'],
        '#image' => $entity['image'],
        '#description' => $entity['description'],
        '#year' => $entity['year'],
      ];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return nl2br(Html::escape($item->value));
  }

}
