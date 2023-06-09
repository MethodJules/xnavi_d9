<?php

namespace Drupal\key_management\Plugin\ResponseKey;

use Drupal\Core\Url;
use Drupal\key_management\Annotation\ResponseKey;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Init.
 *
 * @ResponseKey(
 *   id = "init",
 *   description = @Translation("Combines all key management information into a
 *   single response."), requestMethods = {
 *     "GET",
 *   },
 *   isCacheable = true
 * )
 *
 * @package Drupal\key_management\Plugin\ResponseKey
 */
class Init extends ResponseKeyBase {
    public function getPluginResponse() {
        $data = [
            'attribute1' => 'didduhdudh',
            'attribute2' => 'soidhoisdds',
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