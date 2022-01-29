<?php

namespace Drupal\news\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class NewsletterAdministrationForm extends ConfigFormBase {

    protected function getEditableConfigNames() {
        return ['news.settings'];
    }

    public function getFormId()
    {
        return 'newsletter_administration_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config('news.settings');

        $form['newsletter_administration_form']['email_subject_text'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Betreff der Newsletter E-Mail'),
            '#default_value'=> $config->get('email_subject_text'),
        ];

        $form['newsletter_administration_form']['greeting_text'] = [
            '#type' => 'text_format',
            '#title' => $this->t('Einleitung am Anfang der E-Mail'),
            '#format' => 'filtered_html',
            '#default_value'=> $config->get('greeting_text'),
        ];

        $form['newsletter_administration_form']['subscription_text'] = [
            '#type' => 'text_format',
            '#title' => $this->t('Abschiedformel am Ende der E-Mail'),
            '#format' => 'filtered_html',
            '#default_value' => $config->get('subscription_text'),
        ];

        $form['newsletter_administration_form']['types'] = [
          '#type' => 'checkboxes',
          '#title' => 'Kategorien',
          '#description' => $this->t('Hier können Sie die Kategorien auswählen, die von den Abonnenten gewählt werden können.'),
          '#options' => $this->getAllContentTypes(),
          '#default_value' => $config->get('type_settings', []),
        ];

        $form['newsletter_administration_form']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Speichern'),
        ];

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->config('news.settings')->set('type_settings', $form_state->getValue('types'))->save();
        $this->config('news.settings')->set('greeting_text', $form_state->getValue('greeting_text')['value'])->save();
        $this->config('news.settings')->set('subscription_text', $form_state->getValue('subscription_text')['value'])->save();
        $this->config('news.settings')->set('email_subject_text', $form_state->getValue('email_subject_text'))->save();
    }

    public function getAllContentTypes() {
        $types = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
        foreach($types as $type) {
            $content_types[$type->get('type')] = $type->get('name');
        }

        asort($content_types);
        return $content_types;
    }
}
