<?php

namespace Drupal\design_thinking\Methods;

use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

class Methods {
    public function getAllMethods($phase='', $raum='') {
        $query = \Drupal::entityQuery('node');
        $query->condition('type', 'Methode');
        $enity_ids = $query->execute();
        $methods = [];
        $nodes = Node::loadMultiple($enity_ids);

        foreach ($nodes as $node) {            
            $term_raum = Term::load($node->get('field_raum')->target_id);
            $term_phase = Term::load($node->get('field_phase')->target_id);

            $method_raum = '';
            $method_phase = '';

            if(!is_null($term_raum)) {
                $method_raum = $term_raum->getName();
            }

            if (!is_null($term_phase)) {
                $method_phase = $term_phase->getName();
            }

            if (!empty($raum) && empty($phase)) {
                if (strcmp($method_raum, $raum) == 0) {
                    $node_title = $node->getTitle();
                    // Entferne letzten Teil des Titels
                    $split_node_title = explode("(", $node_title);
                    //Output
                    array_push($methods,trim($split_node_title[0]));
                }
    
            } elseif (empty($raum) && !empty($phase)) {
                if (strcmp($method_phase, $phase) == 0) {
                    $node_title = $node->getTitle();
                    // Entferne letzten Teil des Titels
                    $split_node_title = explode("(", $node_title);
                    //Output
                    array_push($methods,trim($split_node_title[0]));
                }
    
            } elseif (!empty($raum) && !empty($phase)) {
                if (strcmp($method_raum, $raum) == 0 && strcmp($method_phase, $phase) == 0) {
                    $node_title = $node->getTitle();
                    // Entferne letzten Teil des Titels
                    $split_node_title = explode("(", $node_title);
                    //Output
                    array_push($methods,trim($split_node_title[0]));
                }
                
            } else {
                $node_title = $node->getTitle();
                // Entferne letzten Teil des Titels
                $split_node_title = explode("(", $node_title);
                //Output
                array_push($methods,trim($split_node_title[0]));
            } 
        }

        return $methods;
    }

    public function methodByTime($phase='', $raum='', $zeit='', $relation='') {
        $query = \Drupal::entityQuery('node');
        $query->condition('type', 'Methode');
        $entity_ids = $query->execute();
        $array = array();
        $nodes = Node::loadMultiple($entity_ids);

        foreach ($nodes as $node) {
            $node_time = '';
            $condition_sat = FALSE;
            #$method_intervall = array();

            // Hole benoetigte Zeit der Methode und filtere die Zahlen heraus
            $node_time = $node->get('field_benoetigte_zeit')->value;
            preg_match_all('!\d+!', $node_time, $method_intervall);            
                       
            
            $term_raum = Term::load($node->get('field_raum')->target_id);
            $term_phase = Term::load($node->get('field_phase')->target_id);

            $method_raum = '';
            $method_phase = '';

            if(!is_null($term_raum)) {
                $method_raum = $term_raum->getName();
            }

            if (!is_null($term_phase)) {
                $method_phase = $term_phase->getName();
            }

            $condition_sat = $this->testRelation($relation, $zeit, $method_intervall);

            // Pruefe, ob Methode zu Array hinzugefuegt werden kann
            if (!empty($raum) && empty($phase)) {
                if (strcmp($method_raum, $raum) == 0 && $condition_sat) {
                    $node_title = $node->getTitle();
                    $split_node_title = explode("(", $node_title);
                    array_push($array,trim($split_node_title[0]));
                }
    
            } elseif (empty($raum) && !empty($phase)) {
                if (strcmp($method_phase, $phase) == 0 && $condition_sat) {
                    $node_title = $node->getTitle();
                    $split_node_title = explode("(", $node_title);
                    array_push($array,trim($split_node_title[0]));
                }
    
            } elseif (!empty($raum) && !empty($phase)) {
                if (strcmp($method_raum, $raum) == 0 && strcmp($method_phase, $phase) == 0 && $condition_sat) {
                    $node_title = $node->getTitle();
                    $split_node_title = explode("(", $node_title);
                    array_push($array,trim($split_node_title[0]));
                }
                
            } else {
                if ($condition_sat) {
                    $node_title = $node->getTitle();
                    $split_node_title = explode("(", $node_title);
                    array_push($array,trim($split_node_title[0]));
                }
                
            } 
        } 

        return $array;
    }

    public function testRelation($relation, $zeit, $method_intervall) {
        $condition_sat = FALSE;
        if ((strcmp($relation, 'weniger') == 0) && !empty($method_intervall[0][1]) && $zeit >= $method_intervall[0][1]) { //...fuege hinzu, wenn Methode weniger als x dauert
            $condition_sat = TRUE;
        } elseif ((strcmp($relation, 'länger') == 0) && !empty($method_intervall[0][0]) && $zeit <= $method_intervall[0][0]) { //...fuege hinzu, wenn Methode laenger als x dauert
            $condition_sat = TRUE;
        }

        return $condition_sat;
    }

    public function methodByHilfsmittel($phase='', $raum='', $hilfsmittel='') {
        $query = \Drupal::entityQuery('node');
        $query->condition('type', 'Methode');
        $entity_ids = $query->execute();
        $array = array();
        $nodes = Node::loadMultiple($entity_ids);

        foreach ($nodes as $node) {
            $node_hilfsmittel = $node->get('field_hilfsmittel')->value;

            $term_raum = Term::load($node->get('field_raum')->target_id);
            $term_phase = Term::load($node->get('field_phase')->target_id);

            $method_raum = '';
            $method_phase = '';

            if(!is_null($term_raum)) {
                $method_raum = $term_raum->getName();
            }

            if (!is_null($term_phase)) {
                $method_phase = $term_phase->getName();
            }

            // Fuege Methode hinzu, wenn gefragtes Hilfsmittel in Aufzaehlung auftaucht
            # TODO: Methode hinzufuegen, wenn NUR gefragtes Hilfsmittel erforderlich 
            if (!empty($raum) && empty($phase)) {
                if (strcmp($method_raum, $raum) == 0 and strpos($node_hilfsmittel, 'lediglich ' . $hilfsmittel) !== False) {
                    $node_title = $node->getTitle();
                    $split_node_title = explode("(", $node_title);
                    array_push($array,trim($split_node_title[0]));
                }
    
            } elseif (empty($raum) && !empty($phase)) {
                if (strcmp($method_phase, $phase) == 0 and strpos($node_hilfsmittel, 'lediglich ' . $hilfsmittel) !== False) {
                    $node_title = $node->getTitle();
                    $split_node_title = explode("(", $node_title);
                    array_push($array,trim($split_node_title[0]));
                }
    
            } elseif (!empty($raum) && !empty($phase) ) {
                if (strcmp($method_raum, $raum) == 0 && strcmp($method_phase, $phase) == 0 && strpos($node_hilfsmittel, 'lediglich ' .  $hilfsmittel) !== False) {
                    $node_title = $node->getTitle();
                    $split_node_title = explode("(", $node_title);
                    array_push($array,trim($split_node_title[0]));
                }
                
            } elseif(strpos($node_hilfsmittel, 'lediglich ' . $hilfsmittel) !== False) {
                $node_title = $node->getTitle();
                $split_node_title = explode("(", $node_title);
                array_push($array,trim($split_node_title[0]));
            }

        }

        return $array;
    }

    public function methodByTeam($phase='', $raum='', $beteiligte='') {
        $query = \Drupal::entityQuery('node');
        $query->condition('type', 'Methode');
        $entity_ids = $query->execute();
        $array = array();
        $nodes = Node::loadMultiple($entity_ids);

        foreach ($nodes as $node) {
            $node_beteiligte = $node->get('field_beteiligte')->value;

            $term_raum = Term::load($node->get('field_raum')->target_id);
            $term_phase = Term::load($node->get('field_phase')->target_id);

            $method_raum = '';
            $method_phase = '';

            if(!is_null($term_raum)) {
                $method_raum = $term_raum->getName();
            }

            if (!is_null($term_phase)) {
                $method_phase = $term_phase->getName();
            }

            // Fuege Methode hinzu, wenn gefragtes Team in Aufzaehlung auftaucht
            # TODO: Methode hinzufuegen, wenn NUR gefragtes Team erforderlich ist
            if (!empty($raum) && empty($phase)) {
                if (strcmp($method_raum, $raum) == 0 and strpos($node_beteiligte, 'lediglich das ' . $beteiligte) !== False) {
                    $node_title = $node->getTitle();
                    $split_node_title = explode("(", $node_title);
                    array_push($array,trim($split_node_title[0]));
                }
    
            } elseif (empty($raum) && !empty($phase)) {
                if (strcmp($method_phase, $phase) == 0 and strpos($node_beteiligte, 'lediglich das ' . $beteiligte) !== False) {
                    $node_title = $node->getTitle();
                    $split_node_title = explode("(", $node_title);
                    array_push($array,trim($split_node_title[0]));
                }
    
            } elseif (!empty($raum) && !empty($phase)) {
                if (strcmp($method_raum, $raum) == 0 && strcmp($method_phase, $phase) == 0 && strpos($node_beteiligte, 'lediglich das ' . $beteiligte) !== False) {
                    $node_title = $node->getTitle();
                    $split_node_title = explode("(", $node_title);
                    array_push($array,trim($split_node_title[0]));
                }
                
            } elseif(strpos($node_beteiligte, 'lediglich das ' . $beteiligte) !== False) {
                $node_title = $node->getTitle();
                $split_node_title = explode("(", $node_title);
                array_push($array,trim($split_node_title[0]));
            } 
            
        }

        return $array;
    }

}