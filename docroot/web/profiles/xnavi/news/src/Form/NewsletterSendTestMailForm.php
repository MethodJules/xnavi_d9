<?php

namespace Drupal\news\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;


class NewsletterSendTestMailForm extends FormBase {


    public function getFormId()
    {
        return 'newsletter_administration_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {

        $config = $this->config('news.settings');
        $form['newsletter_administration']['categories'] = [
            '#type' => 'checkboxes',
            '#title' => $this->t('Kategorien'),
            '#description' => $this->t('Bitte kreuzen Sie die Kategorien an über welche Sie künftig per Newsletter informiert werden möchten.'),
            '#options' => $this->getAllContentTypes(),
            '#default' => $config->get('type_settings', []),
            '#required' => TRUE,
            '#weight' => 1,
        ];

        $form['newsletter_administration']['testemail'] = [
            '#type' => 'email',
            '#title' => $this->t('Test E-Mail-Adresse'),
            '#description' => $this->t('Bitte tragen Sie hier Ihre Test-E-Mail-Adresse ein'),
            '#required' => TRUE,
            '#weight' => 2,
        ];

        $form['newsletter_administration']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Absenden'),
            '#weight' => 3,
        ];

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        //TODO: E-Mail senden mit den Kategorien
        $email = $form_state->getValue('testemail');
        $types = implode('|', $form_state->getValue('categories'));
        $url = Url::fromRoute('news.newsletter_send_newsletter_test_mail')->setRouteParameters(['email' => $email, 'types' => $types ]);
        $form_state->setRedirectUrl($url);
    }

  public function getAllContentTypes() {
    $config = $this->config('news.settings');
    $types = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
    $types_settings = $config->get('type_settings', []);
    $content_types = [];

    foreach($types_settings as $type) {
      if ($type !== 0) {
        $content_types[$type] = $types[$type]->get('name');
      }
    }

    asort($content_types);
    return $content_types;
  }
}
