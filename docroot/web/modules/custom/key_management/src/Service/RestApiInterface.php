<?php

namespace Drupal\key_management\Service;

interface RestApiInterface {
   /**
   * Generates a JSONd output of the requested key.
   *
   * @param string $key
   *   The key of the desired configuration.
   *
   * @return string
   *   The JSONd contents of the key requested.
   */
  public function requestEndpoint($key);
}