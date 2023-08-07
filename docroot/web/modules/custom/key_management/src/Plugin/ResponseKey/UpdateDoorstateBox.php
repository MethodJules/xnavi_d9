<?php

namespace Drupal\key_management\Plugin\ResponseKey;

use Drupal\Core\Url;
use Drupal\key_management\Annotation\ResponseKey;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\Entity\Node;
/**
 * Class Key Availability.
 *
 * @ResponseKey(
 *   id = "update_door_state_box",
 *   description = @Translation("Inserts the box access."), 
 *   requestMethods = {
 *     "POST",
 *   },
 *   isCacheable = true
 * )
 *
 * @package Drupal\key_management\Plugin\ResponseKey
 */

 class UpdateDoorstateBox extends ResponseKeyBase {
    public function getPluginResponse() {
        $content = $this->currentRequest->getContent();
        $data = json_decode($content, TRUE);

        if (isset($data["kastenID"]) && isset($data["tuerZustand"]) && isset($data["istBelegt"])) {
            $kastenID = $data["kastenID"];
            $zustand = $data["tuerZustand"];
            $istBelegt = $data["istBelegt"];


            // Load the node using the provided node ID (schluesselID)
            $node = Node::load($kastenID);
            if ($node) {
                // Update the fields of the node
                $node->set('field_tuer_zustand', $zustand);
                $node->set('field_ist_kasten_belegt', $zustand);

                // Save the updated node
                $node->save();
                return new JsonResponse(['message' => 'Record updated successfully.']);
            }
            else {
                return new JsonResponse(['error' => 'Error loading node.']);
            }
        } 
        else {
            return new JsonResponse(['error' => 'Missing parameters.']);
        }
    }
    // Use this if needed
    /*public function getNodeByKeyID($Schluessel_id) {
        $query = \Drupal::entityQuery('node')
            ->condition('type', 'schluessel_verwaltung');
            ->condition('field_schluessel_id', $Schluessel_id);
        $result = $query->execute();

        
        if (!empty($result)) {
            return $result;
        }

        // Return NULL if no user found with that RFID value.
        return NULL;
    }*/
    
    // public static function postProcessResponse(array $responsedata) {
    //     $responsedata['timestamp'] = time();
    //     return $responsedata;
    // }        //I don't need this

    public function getCacheTags() {
        return [];
    }
}