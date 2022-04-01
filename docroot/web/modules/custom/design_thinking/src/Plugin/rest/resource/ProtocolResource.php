<?php

namespace Drupal\design_thinking\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\ResourceResponse;

/**
 * Adds a method resource to the core REST API
 * 
 * @RestResource(
 *   id = "protocol_resource",
 *   label = @Translation("Protocol"),
 *   uri_paths = {
 *     "canonical" = "/dt/rest/protocol/{id}",
 *     "create" = "/dt/rest/protocol"
 *   }
 * )
 */
class ProtocolResource extends ResourceBase {

    /**
     * {@inheritdoc}
     */
    /*
    public function permissions()
    {
        // permissions are required
        return [];
    }*/

    /**
     * Responds to GET request
     * 
     * @return \Drupal\rest\ResourceResponse
     */
    public function get($id= NULL) {
        $response = ['message' => 'Protocol GET'];
        return new ResourceResponse($response);
    }

    public function patch($id) {
        $response = ['message' => 'Protocol PATCH'];
        return new ModifiedResourceResponse($response);
    }

    public function delete($id) {
        $response = ['message' => 'Protocol GET'];
        return new ResourceResponse($response);
    }

    /**
     * Respondes to POST request
     * 
     * @return \Drupal\rest\ModifiedResourceResponse
     */
    public function post($data) {
        // \Drupal::messenger()->addMessage(print_r($data));
        $response = ['message' => 'Protocol angelegt.'];
        return new ModifiedResourceResponse($response, 201);
    }
}