<?php

namespace Drupal\design_thinking\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Design Thinking settings for this site.
 */
class DesignThinkingAlexaSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'design_thinking_design_thinking_alexa_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['design_thinking.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['alexa_default'] = [
      '#type' => 'textarea',
      '#resizable' => 'vertical',
      '#title' => $this->t('Default DT Zwei Alexa Value'),
      '#default_value' => $this->config('design_thinking.settings')->get('alexa_default'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  /*
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('alexa_default') != 'alexa_default') {
      $form_state->setErrorByName('alexa_default', $this->t('The value is not correct.'));
    }
    parent::validateForm($form, $form_state);
  }*/
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('design_thinking.settings')
      ->set('alexa_default', $form_state->getValue('alexa_default'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
