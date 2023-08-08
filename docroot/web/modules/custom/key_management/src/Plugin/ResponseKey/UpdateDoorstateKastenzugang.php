<?php

namespace Drupal\key_management\Plugin\ResponseKey;

use Drupal\Core\Url;
use Drupal\key_management\Annotation\ResponseKey;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Key Availability.
 *
 * @ResponseKey(
 *   id = "update_doorstate_kastenzugang",
 *   description = @Translation("Inserts the box access."), 
 *   requestMethods = {
 *     "POST",
 *   },
 *   isCacheable = true
 * )
 *
 * @package Drupal\key_management\Plugin\ResponseKey
 */

class UpdateDoorstateKastenzugang extends ResponseKeyBase {

    public function updateNodeData($node_id, $new_tuer_zustand) {
        // Load the node using the provided node ID
        $node = Node::load($node_id);
        
        // Update the "field_tuer_zustand" field with the new value
        $node->set('field_tuer_zustand', $new_tuer_zustand);
        
        // Save the updated node
        $node->save();
    }


    public function getPluginResponse() {
        // Decode the JSON content from the request body

        $request = json_decode($this->currentRequest->getContent());
        if (!empty($request)){
            // Get the node ID and tuer_zustand from the decoded JSON
            $node_id = $request->ID;
            $tuer_zustand = $request->IstZu;
            if (!empty($node_id) && $tuer_zustand === false){
                // Call the updateNodeData method to update the node
                $this->updateNodeData($node_id, $tuer_zustand);
                return ['response' => 'Updated successfully'];
            }
            else if(!empty($node_id)){
                $tuer_zustand = true;
                $this->updateNodeData($node_id,$tuer_zustand);
                return ['response' => 'Updated with constant tuer_zustand successfully'];
            }
            else{
                return ['response' => 'No access id is submitted!'];
            }
        }
    }

    
    public static function postProcessResponse(array $responsedata) {
         $responsedata['timestamp'] = time();
         return $responsedata;
    }

    public function getCacheTags() {
        return [];
    }
}