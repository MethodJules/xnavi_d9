<?php

namespace Drupal\design_thinking\EventSubscriber;

use Alexa\Request\IntentRequest;
use Drupal\alexa\AlexaEvent;
use Alexa\Response\Card;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\design_thinking\Methods\Methods;

class RequestSubscriber implements EventSubscriberInterface {
    /**
     * Get the event
     */
    public static function getSubscribedEvents()
    {
        $events['alexaevent.request'][] = ['onRequest', 0];
        return $events;
    }

    /**
     * Called upon a request event.
     * 
     * @param \Drupal\alexa\AlexaEvent $event
     *
     */
    public function onRequest(AlexaEvent $event) {
        $request = $event->getRequest();
        $response = $event->getResponse();

        //Get the default value 
        $alexa_default = \Drupal::config('design_thinking.settings')->get('alexa_default');


        $phase = isset($request->slots['Phase']) ? $request->slots['Phase'] : NULL;
        $raum = isset($request->slots['Raum']) ? $request->slots['Raum'] : NULL;

        switch($request->intentName) {
            case 'HelloWorldIntent':
                $response->respond('Hallo, willkommen Drupal Dev Days');
                break;
            case 'NameAllMethods':
                $output = $this->NameAllMethodsIntent($request);
                $response->respond($output);
                break;
            case 'SuggetMethodIntent':
                $output = $this->SuggestMethodIntent($request);
                $response->respond($output);
            default: 
                $response->respond($alexa_default);
                break;
        }
    }

    public function NameAllMethodsIntent($request) {
        $phase = isset($request->slots['Phase']) ? $request->slots['Phase'] : NULL;
        $raum = isset($request->slots['Raum']) ? ucfirst($request->slots['Raum']) : NULL;
        $anzahl = isset($request->slots['Anzahl']) ? $request->slots['Anzahl'] : NULL;

        $methods = new Methods();
        $method_array = $methods->getAllMethods($phase, $raum);
        $methodList = '';


        foreach ($method_array as $element) {
            if (strcmp($element, end($method_array)) == 0) {
                $methodList .= ' ' . $element;
            } else {
                $methodList .= ' ' . $element . ',';
            }  
        }

        $result = '';
        $result .= !is_null($phase) ? 'T' : 'F';
        $result .= !is_null($raum) ? 'T' : 'F';
        $result .= !is_null($anzahl) ? 'T' : 'F';

        $output = '';
        $num = count($method_array);

        switch ($result) {
            case 'TTT':
                $output = 'Für den ' . $raum . ' und die Phase ' . $phase . ' kenne ich ' . $num . ' Methoden.';
                break;

            case 'TTF':
                $output = 'Für den ' . $raum . ' und die Phase ' . $phase . ' kenne ich folgende Methoden:' . $methodList;
                break;
            
            case 'TFT':
                $output = 'Für die Phase ' . $phase . ' kenne ich ' . $num . ' Methoden.';
                break;

            case 'TFF':
                $output = 'Für die Phase ' . $phase . ' kenne ich folgende Methoden: ' . $methodList;
                break;

            case 'FTT':
                $output = 'Für den ' . $raum . ' kenne ich ' . $num . ' Methoden.';
                break;

            case 'FTF':
                $output = 'Für den ' . $raum . ' kenne ich folgende Methoden:' . $methodList;
                break;

            case 'FFT':
                $output = 'Ich kenne ' . $num . 'Methoden.';
                break;

            case 'FFF':
                $output = 'Ich kenne folgende Methoden:' . $methodList; 
                break;
            
            default:
                break;
        }

        return $output;
    }

    public function SuggestMethodIntent($request) {
        $phase = isset($request->slots['Phase']) ? $request->slots['Phase'] : NULL;
        $raum = isset($request->slots['Raum']) ? ucfirst($request->slots['Raum']) : NULL;
        $anzahl = isset($request->slots['Anzahl']) ? $request->slots['Anzahl'] : NULL;
        $menge = isset($request->slots['Quantity']) ? $request->slots['Quantity'] : NULL;
        $dauer = isset($request->slots['Dauer']) ? $request->slots['Dauer'] : NULL;
        $relation = isset($request->slots['Relation']) ? $request->slots['Relation'] : NULL;
        $hilfsmittel = isset($request->slots['Hilfsmittel']) ? ucfirst($request->slots['Hilfsmittel']) : NULL;
        $beteiligte = isset($request->slots['Beteiligte']) ? $request->slots['Beteiligte'] : NULL;

        $methods = new Methods();

        $array = array();
        #$methodList = '';
        $output = 'Es ist mind. ein Fehler aufgetreten.';

        # Suche nach Methoden
        # ...wenn Hilfsmittel die Bedingung ist
        if (!empty($hilfsmittel)) {

            $array = $methods->methodByHilfsmittel($phase, $raum, $hilfsmittel);
        
        # ...wenn das Team die Bedingung ist
        } elseif (!empty($beteiligte)) {

            // 'design thinking team' -> 'Design-Thinking-Team'
            $temp_beteiligte = explode(" ", $beteiligte);
            $beteiligte = '';

            foreach ($temp_beteiligte as $temp) {
                if (strcmp($temp, end($temp_beteiligte)) == 0) {
                    $beteiligte .= ucfirst($temp);
                } else {
                    $beteiligte .= ucfirst($temp) . ' ';
                } 
            }

            $array = $methods->methodByTeam($phase, $raum, $beteiligte);


        # ...wenn die Zeit die Bedinung ist
        } elseif (!empty($menge) and !empty($dauer)) {

            #$array = $this->methodByTime($phase, $raum, $menge, $relation);

            switch ($dauer) {
                case 'minute':
                case 'minuten':
                    # keine Umrechnung erforderlich
                    
                    $array = $methods->methodByTime($phase, $raum, $menge, $relation);
                    break;
                
                case 'stunde':
                case 'stunden':
                    # $menge * 60
                    $zeit = '';
                    $zeit = $menge * 60;
                    $array = $methods->methodByTime($phase, $raum, $zeit, $relation);
                    break;

                case 'tag':
                case 'tage':
                    # $menge * 60 * 8 (Arbeitstag)
                    $zeit = '';
                    $zeit = $menge * 60 * 8;
                    $array = $methods->methodByTime($phase, $raum, $zeit, $relation);
                    break;

                case 'woche':
                case 'wochen':
                    # $menge * 60 * 8 * 5 (Arbeitswoche)
                    $zeit = '';
                    $zeit = $menge * 60 * 8 * 5;
                    $array = $methods->methodByTime($phase, $raum, $zeit, $relation);
                    break;
                
                case 'monat':
                case 'monate':
                    # 1 Monat besteht aus 4 Wochen
                    # $menge * 60 * 8 * 5 * 4
                    $zeit = '';
                    $zeit = $menge * 60 * 8 * 5 * 4;
                    $array = $methods->methodByTime($phase, $raum, $zeit, $relation);
                    break;
                
                default:
                    break;
            }
        }
        
        $methodList = '';

        foreach ($array as $element) {
            if (strcmp($element, end($array)) == 0) {
                $methodList .= ' ' . $element;
            } else {
                $methodList .= ' ' . $element . ',';
            }  
        }
        
        $result = '';
        $result .= !empty($phase) ? 'T' : 'F';
        $result .= !empty($raum) ? 'T' : 'F';
        $result .= !empty($anzahl) ? 'T' : 'F';

        #$output = '';
        $num = count($array);

        switch ($result) {
            case 'TTT':
                $output = 'Für den ' . $raum . ' und die Phase ' . $phase . ' kenne ich ' . $num . ' Methoden.';
                break;

            case 'TTF':
                $output = 'Für den ' . $raum . ' und die Phase ' . $phase . ' kenne ich folgende Methoden:' . $methodList;
                break;
            
            case 'TFT':
                $output = 'Für die Phase ' . $phase . ' kenne ich ' . $num . ' Methoden.';
                break;

            case 'TFF':
                $output = 'Für die Phase ' . $phase . ' kenne ich folgende Methoden:' . $methodList;
                break;

            case 'FTT':
                $output = 'Für den ' . $raum . ' kenne ich ' . $num . ' Methoden.';
                break;

            case 'FTF':
                $output = 'Für den ' . $raum . ' kenne ich folgende Methoden:' . $methodList;
                break;

            case 'FFT':
                $output = 'Ich kenne ' . $num . ' Methoden.';
                break;

            case 'FFF':
                $output = 'Ich kenne folgende Methoden:' . $methodList; 
                break;
            
            default:
                break;
        }

        if (empty($methodList)) {
            $output = 'Ich habe keine Methoden gefunden, die zu deiner Anforderung passen.';
        }

        // Output
        return $output;
    }
}