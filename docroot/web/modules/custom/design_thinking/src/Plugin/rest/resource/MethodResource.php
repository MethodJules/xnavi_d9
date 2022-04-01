<?php

namespace Drupal\design_thinking\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

/**
 * Adds a method resource to the core REST API
 * 
 * @RestResource(
 *   id = "method_resource",
 *   label = @Translation("Method resource"),
 *   uri_paths = {
 *     "canonical" = "/dt/rest/method/{id}"
 *   }
 * )
 */
class MethodResource extends ResourceBase {
    /**
     * Responds to GET request
     * 
     * @return \Drupal\rest\ResourceResponse
     */
    public function get($id= NULL) {
        $response = ['message' => 'Hello world'];
        return new ResourceResponse($response);
    }
}