<?php

namespace Drupal\key_management\Plugin\ResponseKey;


/**
 * Class SetKeyStatus.
 *
 * @ResponseKey(
 *   id = "setkeystatus",
 *   description = @Translation("Sets Key Status"),
 *   requestMethods = {
 *     "PATCH",
 *   },
 *   isCacheable = false,
 *   shieldRequest = true
 * )
 *
 * @package Drupal\dipas\Plugin\ResponseKey
 */
class SetKeyStatus extends ResponseKeyBase {

    public function getPluginResponse() {
        $request = json_decode($this->currentRequest->getContent());
        $key_id = $request->id;
        $state = $request->state;
        if ($state === "available" || $state === "not available") {
            $this->updateKey($key_id, $state);
            // Update the key content type
            return [
                'text' => 'The key with the id ' . $key_id . ' was updated.',
                'update_status' => 'success'
            ];
        } else {
            return [
                'text' => 'The key with the id ' . $key_id . ' was not updated. There is a failure in your request.',
                'update_status' => 'failure'
            ];
        }
        

        
    }

    public static function postProcessResponse(array $responsedata) {
        $responsedata['timestamp'] = time();
        return $responsedata;
    }

    public function getCacheTags() {
        return [];
    }

    public function updateKey($key_id, $state) {
        try {
            $node_storage = \Drupal::entityTypeManager()->getStorage('node');
            $node = $node_storage->load(intval($key_id));
            $node->field_state = $state;
            $node->save();
        } catch (\Exception $e) {
            \Drupal::logger('key_management')->error("<pre>$e</pre>");
        }
        
        // TODO: Exception und try catch block implementieren
    }
}