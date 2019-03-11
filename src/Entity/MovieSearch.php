<?php

namespace Drupal\tcm_api\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;

/**
 * Defines the MovieSearch entity.
 *
 * @ingroup tcm_api
 *
 * @ContentEntityType(
 *   id = "movie_search",
 *   label = @Translation("Movie Search"),
 *   handlers = {
 *    "storage" = "Drupal\tcm_api\Entity\MovieSearchStorage",
 *   },
 *   base_table = NULL,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name"
 *   }
 * )
 */
class MovieSearch extends ContentEntityBase {

  use EntityChangedTrait;

  public function __construct(array $values = []) {
    // Set initial values.
    foreach ($values as $key => $value) {
      $this->$key = $value;
    }
  }

  public function content($id) {
    $entity = $this->load($id);
    return [
      '#theme' => 'movie',
      '#id' => $entity['id'],
      '#title' => $entity['title'],
      '#image' => $entity['image'],
      '#description' => $entity['description'],
      '#year' => $entity['year'],
      '#runtime' => $entity['runtime'],
      '#rating' => $entity['rating'],
    ];
  }

}
