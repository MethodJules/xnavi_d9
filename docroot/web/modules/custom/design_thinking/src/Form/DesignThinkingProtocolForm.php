<?php

namespace Drupal\design_thinking\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DesignThinkingProtocolForm extends FormBase {

    /**
     * @var AccountInterface $account
     */
    protected $account;

    /**
     * Class constructor
     */
    public function __construct(AccountInterface $account)
    {
        $this->account = $account;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        // Instantiates this form class.
        return new static(
            // Load the service required to construct this class.
            $container->get('current_user')
        );
    }

    public function getFormId()
    {
        return 'design_thinking_protocol_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        //Get the current user
        $uid = $this->account->id();
        //TODO Check if an open protocol exists
        $openProtocolFlag = FALSE;
        
        if ($openProtocolFlag) {
            $messenger = \Drupal::messenger();
            $messenger->addWarning($this->t('Es existiert bereits ein Protokoll.'))
        }
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        
    }
}