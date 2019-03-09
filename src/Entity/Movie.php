<?php

namespace Drupal\tcm_api\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;

/**
 * Defines the Movie entity.
 *
 * @ingroup tcm_api
 *
 * @ContentEntityType(
 *   id = "movie",
 *   label = @Translation("Movie"),
 *   handlers = {
 *    "storage" = "Drupal\tcm_api\Entity\MovieStorage",
 *   },
 *   base_table = NULL,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name"
 *   }
 * )
 */
class Movie extends ContentEntityBase {

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
    ];
  }

}
