<?php

namespace Drupal\key_management\Service;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\key_management\ResponseContent;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class RestApi implements RestApiInterface {
    /**
     * Options to pass to the json_encode function.
     */
    const JSON_OUTPUT_OPTIONS = JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES;

    /**
     * Custom logger channel
     * 
     * @var \Drupal\Core\Logger\LoggerChannelInterface
     */
    protected $logger;

    /**
     * Custom response plugin manager
     * 
     * @var \Drupal\Component\Plugin\PluginManagerInterface 
     */
    protected $responsePluginManager;

    /**
     * The currently processed request
     * 
     * @var \Symfony\Component\HttpFoundation\Request;
     */
    protected $request;

     /**
      * RestApi constructor
      *
      *@param \Drupal\Core\Logger\LoggerChannelInterface $logger
      *  Custom logger channel
      *@param \Drupal\Component\Plugin\PluginManagerInterface $response_key_plugin_manager
      *  Custom plugin manager for response plugins.
      *@param \Symfony\Component\HttpFoundation\RequestStack $request_stack
      *  The request stack object
      */
      public function __construct(
        LoggerChannelInterface $logger,
        PluginManagerInterface $response_key_plugin_manager,
        RequestStack $request_stack
      ) {
        $this->logger = $logger;
        $this->responsePluginManager = $response_key_plugin_manager;
        $this->request = $request_stack->getCurrentRequest();
      }

      public function requestEndpoint($key) {
        $response = new JsonResponse();
        try {
            $pluginDefinition = $this->responsePluginManager->getDefinition(strtolower($key));
            if (!in_array($this->request->getMethod(), $pluginDefinition['requestMethods'])) {
                $content = new ResponseContent(
                    ResponseContent::RESPONSE_STATUS_ERROR,
                    'The requested resource cannot be found on this server.',
                    404
                );
            } else {
                /* @var \Drupal\key_management\PluginSystem\ResponseKeyPluginInterface $plugin */
                $plugin = new $pluginDefinition['class']($pluginDefinition, $this->request, $response);
                try {
                    $content = new ResponseContent(
                        ResponseContent::RESPONSE_STATUS_SUCCESS,
                        $plugin->getResponseData()
                    );
                    if (!empty($cookies = $plugin->getCookies())) {
                        foreach ($cookies as $cookie) {
                            $response->headers->setCookie($cookie);
                        }
                    }
                }
                catch (NotFoundHttpException $e) {
                    $content = new ResponseContent(
                      ResponseContent::RESPONSE_STATUS_ERROR,
                      'The requested resource cannot be found on this server.',
                      404
                    );
                }
                catch (\Exception $e) {
                    $this->logger->error("Unexpected error: {$e->getCode()} - {$e->getMessage()}");
                    $content = new ResponseContent(
                      ResponseContent::RESPONSE_STATUS_ERROR,
                      $e->getMessage(),
                      ($e->getCode() !== 0 ? $e->getCode() : 500)
                    );
                }
            }
        }
        catch (PluginNotFoundException $e) {
            $this->logger->notice("Undefined endpoint: {$key}");
            $content = new ResponseContent(
              ResponseContent::RESPONSE_STATUS_ERROR,
              "Unknown key: {$key}",
              500
            );
        }

        if (!$content->isError()) {
            $content->updateContent($pluginDefinition['class']::postProcessResponse($content->getRawContent()));
        }

        $response->setData($content->getResponseContent());
        if ($content->isError()) {
            $response->setStatusCode($content->getResponseStatusCode());
        }
        $response->setEncodingOptions(static::JSON_OUTPUT_OPTIONS);
        return $response;
      }
}