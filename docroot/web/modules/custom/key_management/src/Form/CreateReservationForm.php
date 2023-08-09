<?php

namespace Drupal\key_management\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

class CreateReservationForm extends FormBase {
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'key_management_create_reservation_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        if ($form_state->has('page') && $form_state->get('page') == 2) {
            return self::formPageTwo($form, $form_state);
        }

        $form_state->set('page', 1);

        $form['description'] = [
            '#type' => 'item',
            '#title' => $this->t('Page @page', ['@page' => $form_state->get('page')]),
        ];

        $form['reservationdate'] = [
            '#type' => 'datetime',
            '#title' => $this->t('Reservierungsdatum'),
            '#date_timezone' => 'UTC',
        ];

        $form['returndate'] = [
            '#type' => 'datetime',
            '#title' => $this->t('Rückgabedatum'),
            '#date_timezone' => 'UTC',
        ];

        $form['actions'] = [
            '#type' => 'actions'
        ];

        $form['actions']['next'] = [
            '#type' => 'submit',
            '#button_type' => 'primary',
            '#value' => $this->t('Next'),
            '#submit' => ['::submitPageOne'],
            '#validate' => ['::validatePageOne'],
        ];

        return $form;
         
    }

    /**
     * Validate the date values
     */
    public function validatePageOne(array &$form, FormStateInterface $form_state) {
        $reservationdate = $form_state->getValue('reservationdate');
        $returndate = $form_state->getValue('returndate');

        // Query existing 'buchung' nodes.
        $query = \Drupal::entityQuery('node')
        ->condition('type', 'buchung')
        ->condition('field_reservierungsdatum', $reservationdate->format('Y-m-d\TH:i:s'), '>=')
        ->condition('field_rueckgabe_datum', $returndate->format('Y-m-d\TH:i:s'), '<=');
        // ->range(0, 1); // Only need to know if at least one exists.

        $nids = $query->execute();

        // get all keys 

        // iterate over all nids and get from them the keys
        foreach ($nids as $nid) {
            $node_storage = \Drupal::entityTypeManager()->getStorage('node');
            $node = $node_storage->load($nid);
            $key_nids[] = $node->field_schluessel_referenz->target_id;
        }

        // filter the keys out that are reserved
        $query = \Drupal::entityQuery('node')
        ->condition('type', 'schluessel_verwaltung');
        $all_keys = $query->execute();

        // filter the keys
        $filtered_keys = array_diff($all_keys, $key_nids);
        $form_state->set('keys', $filtered_keys);

        // list all keys that are not reserved (do in antoher page)
    }

    /**
     * Submit Page One
     */
    public function submitPageOne(array &$form, FormStateInterface $form_state) {
        $form_state
        ->set('reservationdate', $form_state->getValue('reservationdate'))
        ->set('returndate', $form_state->getValue('returndate'))
        ->set('page', 2)->setRebuild(TRUE);
    }

    public function formPageTwo(array &$form, FormStateInterface $form_state) {
        $keys_nids = $form_state->get('keys');
        foreach ($keys_nids as $key_nid) {
            $node_storage = \Drupal::entityTypeManager()->getStorage('node');
            $node = $node_storage->load($key_nid);
            $options[$key_nid] = $node->title->value;
        } 
        $form['description'] = [
            '#type' => 'item',
            '#title' => $this->t('Page @page', ['@page' => $form_state->get('page')]),
        ];

        $form['keys'] = [
            '#type' => 'select',
            '#title' => $this->t('Keys'),
            '#options' => $options,
        ];

        $form['back'] = [
            '#type' => 'submit',
            '#value' => $this->t('Back'),
            '#submit' => ['::pageTwoBack'],
            '#limit_validation_errors' => [],
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#button_type' => 'primary',
            '#value' => $this->t('Buchung erstellen'),
          ];

        return $form;
    }

    /**
    * @param array $form
    *   An associative array containing the structure of the form.
    * @param \Drupal\Core\Form\FormStateInterface $form_state
    *   The current state of the form.
    */
    public function pageTwoBack(array &$form, FormStateInterface $form_state) {
        $form_state
        ->set('page', 1)
        ->setRebuild(TRUE);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $key = $form_state->getValue('keys');
        $reservationdate = $form_state->get('reservationdate');
        $returndate = $form_state->get('returndate');

        $current_user = \Drupal::currentUser();
        $uid = $current_user->id();

        $node = Node::create(['type' => 'buchung']);
        $node->title = 'Buchung_' . $uid;
        $node->field_reservierungsdatum = $reservationdate->format('Y-m-d\TH:i:s');
        $node->field_rueckgabe_datum = $returndate->format('Y-m-d\TH:i:s');
        $node->field_schluessel_referenz = $key;

        $node->save();
        $nid = $node->id();

        \Drupal::messenger()->addMessage('Buchung wurde erstellt');
        // $form_state->setRedirect('entity.node.edit_form', ['node' => $nid]);
    }


}