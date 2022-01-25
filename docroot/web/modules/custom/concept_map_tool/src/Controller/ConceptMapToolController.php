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
      '#theme' => 'concept_map_tool_template',
      '#foo' => $this->t('It workssiduhisudiu'),
      '#attached' => [
        'library' => [
          'concept_map_tool/concept_map_tool'
        ]
      ]
    ];

    return $build;
  }

}
