<?php

namespace Drupal\key_management\Plugin\ResponseKey;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\key_management\PluginSystem\ResponseKeyPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ResponseKeyBase.
 * 
 * @package Drupal\key_management\Plugin\ReponseKey
 */
abstract class ResponseKeyBase implements ResponseKeyPluginInterface {
    use StringTranslationTrait;

    /**
     * This plugins definition
     */
    protected $pluginDefinition;

    /**
     * Custom logger channel
     * 
     * @var \Drupal\Core\Logger\LoggerChannelInterface
     */
    protected $logger;

    /**
     * The currently processed request
     * 
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $currentRequest;

    /**
     * The response object in construction
     * 
     * @var \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected $response;

    /**
     * Drupals Entity Type Manager
     * 
     * @var \Drupal\Core\Entity\EntityTypeManagerInterface
     */
    protected $entityTypeManager;

    /**
     * Symfonys serializer service.
     *
     * @var \Symfony\Component\Serializer\Serializer
     */
    protected $serializer;

    /**
     * @var \Drupal\Core\Database\Connection
     */
    protected $database;

    /**
   * ResponseKeyBase constructor.
   *
   * @param array $pluginDefinition
   *   The plugin definition.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The currently processed request.
   * @param \Symfony\Component\HttpFoundation\JsonResponse $response
   *   The response object in construction.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  public function __construct(array $pluginDefinition, Request $request, JsonResponse &$response) {
    $this->pluginDefinition = $pluginDefinition;
    $this->currentRequest = $request;
    $this->response = $response;

    // No nice dependency injection here.
    /* @var \Drupal\Core\DependencyInjection\Container $container */
    $container = \Drupal::getContainer();
    $this->logger = $container->get('logger.channel.key_management');
    $this->entityTypeManager = $container->get('entity_type.manager');
    $this->serializer = $container->get('serializer');
    $this->database = $container->get('database');

    $this->setAdditionalDependencies($container);
  }

  /**
   * Allows plugin implementations to set their own dependencies.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   */
  protected function setAdditionalDependencies(ContainerInterface $container) {}

  /**
   * {@inheritdoc}
   */
  protected function getDatabase() {
    return $this->database;
  }

  /**
   * {@inheritdoc}
   */
  public function getCookies() {
    return [];
  }

  final public function getResponseData() {
    return $this->getPluginResponse();
  }

  /**
   * Returns the plugin response data array.
   *
   * @return array
   *   The array containing the plugin response data.
   */
  abstract protected function getPluginResponse();


}