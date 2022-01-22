<?php

namespace Drupal\concept_map_tool\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Concept Map Tool routes.
 */
class ConceptMapToolController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
