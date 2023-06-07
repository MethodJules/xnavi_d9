<?php

namespace Drupal\key_management\Annotation;

use Drupal\Component\Annotation\Plugin;
/**
 * Defines a ResponseKey annotation object.
 *
 * Plugin Namespace: Plugin\ResponseKey.
 *
 * @see \Drupal\dipas\PluginSystem\ResponseKeyPluginManager
 * @see \Drupal\dipas\PluginSystem\ResponseKeyPluginInterface
 * @see plugin_api
 *
 * @Annotation
 */
class ResponseKey extends Plugin {

    /**
     * The response key.
     *
     * @var string
     */
    public $id;
  
    /**
     * The description of the plugin.
     *
     * @var \Drupal\Core\Annotation\Translation
     *
     * @ingroup plugin_translatable
     */
    public $description;
  
    /**
     * Allowed request methods for this plugin.
     *
     * @var array
     */
    public $requestMethods;  
  }