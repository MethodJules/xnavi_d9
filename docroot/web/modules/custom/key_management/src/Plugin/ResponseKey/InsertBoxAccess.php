<?php

namespace Drupal\key_management\Plugin\ResponseKey;

use Drupal\Core\Url;
use Drupal\key_management\Annotation\ResponseKey;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\Entity\Node;
/**
 * Class Insert Box Access.
 *
 * @ResponseKey(
 *   id = "insert_box_access",
 *   description = @Translation("Inserts the box access."), 
 *   requestMethods = {
 *     "POST",
 *   },
 *   isCacheable = true
 * )
 *
 * @package Drupal\key_management\Plugin\ResponseKey
 */

class InsertBoxAccess extends ResponseKeyBase {
    
    public function getPluginResponse() {
        $request = json_decode($this->currentRequest->getContent());
        if (!empty($request)){
            $user_id = $request->user_id;
            $ist_zu = $request->ist_zu; //Takes both user_id and ist_zu field from the body.
            if(!empty($user_id)&&!empty($ist_zu)){
                $current_date_time = date('Y-m-d H:i:s');
        
                // Create a new node of the "zugangsverwaltung" content type.
                $node = Node::create([
                    'type' => 'zugang_verwaltung',
                    //'type' => 'zugangs_verwaltung', //TODO: Check this system name for the entity type
                    'title' => 'Zugangsverwaltung_log_' . $user_id ,//Maybe not needed
                    'langcode' => 'de', //Maybe not needed
                    'field_datum_zugang_zur_fach' => $current_date_time, // Set the current date and time
                    'field_tuer_zustand' => $ist_zu, // Set the boolean field value
                    'field_userid' => [
                        'target_id' => $user_id,
                    ],
                ]);
                $node->save(); // Save the node.
                return ['node_uid' => $node->id()];
            }
            else if(!empty($user_id)){
                $current_date_time = date('Y-m-d H:i:s');
        
                // Create a new node of the "zugangsverwaltung" content type.
                $node = Node::create([
                    'type' => 'zugang_verwaltung',
                    //'type' => 'zugangs_verwaltung', //TODO: Check this system name for the entity type
                    'title' => 'New Zugangsverwaltung Node',//Maybe not needed
                    'langcode' => 'de', //Maybe not needed
                    'field_datum_zugang_zur_fach' => $current_date_time, // Set the current date and time
                    'field_tuer_zustand' => true, // Set the boolean field value
                    'field_userid' => [
                        'target_id' => $user_id,
                    ],
                ]);
                $node->save(); // Save the node.
                return ['node_uid' => $node->id()];
            }
            else{
                return ['response' => 'No user id is submitted!'];
            }
        }
        return ['response' => 'Body is broken!'];
    }

    public static function postProcessResponse(array $responsedata) {
        $responsedata['timestamp'] = time();
         return $responsedata;
    }

    public function getCacheTags() {
        return [];
    }
}