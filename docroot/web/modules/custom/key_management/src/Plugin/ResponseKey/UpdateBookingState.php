<?php

namespace Drupal\key_management\Plugin\ResponseKey;

//TODO: I wrote this detailed but maybe some stuff are already done somewhere else

use DateTimeZone;
use Drupal\Core\Url;
use Drupal\key_management\Annotation\ResponseKey;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\node\Entity\Node;
use Drupal\Core\Datetime\DrupalDateTime;

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
        $request = json_decode($this->currentRequest->getContent());//Reads the request body JSON
        $data["buchungID"] = $request->buchungId;
        $data["zustand"] = $request->zustand;
        $abholungszeit = $request->abholungszeit; 
        $abholungszeit = new DrupalDateTime($abholungszeit, new DateTimeZone('UTC'));
        $data["abholungszeit"] = $abholungszeit;

        $abgabezeit = $request->abgabezeit;
        $abgabezeit = new DrupalDateTime($abgabezeit, new DateTimeZone('UTC'));
        $data["abgabezeit"] = $abgabezeit;
        // $data = json_decode($content, TRUE);

        if (isset($data["buchungID"]) && isset($data["zustand"])) {
            $buchungID = $data["buchungID"];
            $zustand = $data["zustand"];

            // Load the node using the provided node ID (buchungID)
            $node = Node::load($buchungID);
            if ($node) {
                // Update the fields of the node
                $node->set('field_buchungszustand', $zustand);

                if (isset($data["abholungszeit"])) {
                    $abholungszeit = $data["abholungszeit"];
                    $node->set('field_abholungszeit', $abholungszeit->format('Y-m-d\TH:i:s'));
                }
                if (isset($data["abgabezeit"])) {
                    $abgabezeit = $data["abgabezeit"];
                    $node->set('field_abgabezeit', $abgabezeit->format('Y-m-d\TH:i:s'));
                }

                // Save the updated node
                $node->save();
                return ['message' => 'Record updated successfully.'];
            }
        } 
        else {
            return ['error' => 'Missing parameters.'];
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