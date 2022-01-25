<?php

namespace Drupal\seminar_work\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the seminar work entity edit forms.
 */
class SeminarWorkForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $entity = $this->getEntity();
    $result = $entity->save();
    $link = $entity->toLink($this->t('View'))->toRenderable();

    $message_arguments = ['%label' => $this->entity->label()];
    $logger_arguments = $message_arguments + ['link' => render($link)];

    if ($result == SAVED_NEW) {
      $this->messenger()->addStatus($this->t('New seminar work %label has been created.', $message_arguments));
      $this->logger('seminar_work')->notice('Created new seminar work %label', $logger_arguments);
    }
    else {
      $this->messenger()->addStatus($this->t('The seminar work %label has been updated.', $message_arguments));
      $this->logger('seminar_work')->notice('Updated new seminar work %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.seminar_work.canonical', ['seminar_work' => $entity->id()]);
  }

}
