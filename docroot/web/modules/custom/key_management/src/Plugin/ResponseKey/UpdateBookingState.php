<?php

namespace Drupal\key_management\Plugin\ResponseKey;

//TODO: I wrote this detailed but maybe some stuff are already done somewhere else
use Drupal\Core\Url;
use Drupal\key_management\Annotation\ResponseKey;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\node\Entity\Node;
/**
 * Class Key Availability.
 *
 * @ResponseKey(
 *   id = "update_booking_state",
 *   description = @Translation("Inserts the box access."), 
 *   requestMethods = {
 *     "POST",
 *   },
 *   isCacheable = true
 * )
 *
 * @package Drupal\key_management\Plugin\ResponseKey
 */

 class UpdateBookingState extends ResponseKeyBase {
    public function getPluginResponse() {
        $content = $this->currentRequest->getContent();
        $data = json_decode($content, TRUE);

        if (isset($data["buchungID"]) && isset($data["zustand"])) {
            $buchungID = $data["buchungID"];
            $zustand = $data["zustand"];

            // Load the node using the provided node ID (buchungID)
            $node = Node::load($buchungID);
            if ($node) {
                // Update the fields of the node
                $node->set('field_zustand', $zustand);

                if (isset($data["abholungszeit"])) {
                    $abholungszeit = $data["abholungszeit"];
                    $node->set('field_abholungszeit', $abholungszeit);
                }
                if (isset($data["abgabezeit"])) {
                    $abgabezeit = $data["abgabezeit"];
                    $node->set('field_abgabezeit', $abgabezeit);
                }

                // Save the updated node
                $node->save();
                return new JsonResponse(['message' => 'Record updated successfully.']);
            }
            else {
                return new JsonResponse(['error' => 'Error updating record.']);
            }
        } 
        else {
            return new JsonResponse(['error' => 'Missing parameters.']);
        }
    }

    
    // public static function postProcessResponse(array $responsedata) {
    //     $responsedata['timestamp'] = time();
    //     return $responsedata;
    // }        //I don't need this

    public function getCacheTags() {
        return [];
    }
}