<?php

namespace Drupal\key_management\Plugin\ResponseKey;

use Drupal\Core\Url;
use Drupal\key_management\Annotation\ResponseKey;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Key Availability.
 *
 * @ResponseKey(
 *   id = "key_availability",
 *   description = @Translation("Prodives information about all keys information"), 
 *   requestMethods = {
 *     "GET",
 *   },
 *   isCacheable = true
 * )
 *
 * @package Drupal\key_management\Plugin\ResponseKey
 */
class KeyAvailability extends ResponseKeyBase {
    public function getPluginResponse() {
        $data = [
            'S10201' => [
                'room' => 'C 135',
                'place' => 3,
                'available' => TRUE,
            ],
            'S10202' => [
                'room' => 'A 321',
                'place' => 6,
                'available' => FALSE,
            ],
            'S10203' => [
                'room' => 'H 011',
                'place' => 7,
                'available' => FALSE,
            ],
        ];

        return $data;
    }

    public static function postProcessResponse(array $responsedata) {
        $responsedata['timestamp'] = time();
        return $responsedata;
    }

    public function getCacheTags() {
        return [];
    }
}