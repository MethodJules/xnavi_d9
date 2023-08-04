<?php

namespace Drupal\key_management\Plugin\ResponseKey;

use Drupal\Core\Url;
use Drupal\key_management\Annotation\ResponseKey;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Key Availability.
 *
 * @ResponseKey(
 *   id = "key_availability",
 *   description = @Translation("Prodives information about all keys information"), 
 *   requestMethods = {
 *     "GET",
 *   },
 *   isCacheable = true
 * )
 *
 * @package Drupal\key_management\Plugin\ResponseKey
 */
class KeyAvailability extends ResponseKeyBase {
    public function getKeyAvaiablityData() {
        $nids = \Drupal::entityQuery('node')
            ->condition('status', 1)
            ->condition('type', 'key')
            ->execute();
        $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
        $data = [];
        foreach($nodes as $node) {
            $key_id = $node->id();
            $room = $node->field_room_lock->value;
            $state = $node->field_state->value;
            $data[$key_id] = [
                'room' => $room,
                'state' => $state 
            ];
        }
        return $data;
    }
    public function getPluginResponse() {
        // $data = $this->getKeyAvaiablityData();
        $request = json_decode($this->currentRequest->getContent());
        $rfid_uid = $request->rfid_uid;
        $user_id = $this->getUserIdByRFIDId($rfid_uid);
        $reservations = $this->get_buchung_by_user($user_id);

        $data = [];
        if (!empty($reservations)) {
            foreach($reservations as $node_id) {
                $node = \Drupal\node\Entity\Node::load($node_id);
                $reservierungsdatum = $node->field_reservierungsdatum->value;
                $buchung_id = $node->getTitle();
                $data[] = [
                    'UserID' => $user_id,
                    'Buchung_ID' => $buchung_id,
                    'Reservierungsdatum' => $reservierungsdatum,
                    'Rueckgabedatum' => '2023-07-26 25:59:49',
                    'Schluessel_Zustand' => 'abgeholt',
                    'Kasten_ID' => '1',
                ];

            }
        }
        return $data;
        /*
        $data = [
            'S10201' => [
                'room' => 'C 135',
                'place' => 3,
                'available' => TRUE,
            ],
            'S10202' => [
                'room' => 'A 321',
                'place' => 6,
                'available' => FALSE,
            ],
            'S10203' => [
                'room' => 'H 011',
                'place' => 7,
                'available' => FALSE,
            ],
        ];
        */
        return $data;
    }

    public function get_buchung_by_user($user_id) {
        $query = \Drupal::entityQuery('node')
          ->condition('type', 'buchung')
          ->condition('uid', $user_id);
        $result = $query->execute();
      
        // Return the node IDs, or an empty array if no nodes found.
        return !empty($result) ? $result : [];
    }

    public function getData() {
        $data = [
            'UserID' => '1',
            'Buchung_ID' => 'B0001',
            'Reservierungsdatum' => '2023-07-13 12:25:58',
            'Rueckgabedatum' => '2023-07-26 25:59:49',
            'Schluessel_Zustand' => 'abgeholt',
            'Kasten_ID' => '1',
        ];

        return $data;
    }
    // get user id by rfid id
    public function getUserIdByRFIDId($rfid_uid) {
        $query = \Drupal::entityQuery('user')
            ->condition('field_rfid_uid', $rfid_uid);
        $result = $query->execute();

        // Assuming the RFID is unique, there should be one result.
        if (!empty($result)) {
            return reset($result);
        }

        // Return NULL if no user found with that RFID value.
        return NULL;
    }
    /*
    [
     {
         "UserID": "1",
         "Buchung_ID": "B0001",
         "Reservierungsdatum": "2023-07-13 13:25:58",
         "Rueckgabedatum": "2023-07-26 23:59:59",
         "Buchung_Zustand": "spaet",
         "SchluesselID": "S0001",
         "Schluessel_Zustand": "abgeholt",
         "Kasten_ID": "1"
     },
     {
         "UserID": "1",
         "Buchung_ID": "B0003",
         "Reservierungsdatum": "2023-08-01 00:00:00",
         "Rueckgabedatum": "2023-08-03 23:59:59",
         "Buchung_Zustand": "gebucht",
         "SchluesselID": "S0002",
         "Schluessel_Zustand": "reserviert",
         "Kasten_ID": "2"
     }
]
*/

    public static function postProcessResponse(array $responsedata) {
        $responsedata['timestamp'] = time();
        return $responsedata;
    }

    public function getCacheTags() {
        return [];
    }
}