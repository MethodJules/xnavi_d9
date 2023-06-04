<?php

namespace Drupal\key_management\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

/**
 * Provides a Door Resource
 * 
 * @RestResource(
 *   id = "door_resource",
 *   label = @Translation("Door Resource")
 *   uri_paths = {
 *      "canonical" = "key_api/v1/door_resource"
 *   }
 * )
 */
class DoorResource extends ResourceBase {

    /**
     * Responds to entity GET request.
     * @return \Drupal\rest\ResourceResponse
     */
    public function get() {
        $response = [
            'message' => 'Hello, this is a rest service'
        ];
        return new ResourceResponse($response);
    }
}