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
            $messenger->addWarning($this->t('Es existiert bereits ein Protokoll.'));
        }

        $form['general_information'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Generelle Informationen')
        ];

        
        $form['general_information']['participants'] = [
            '#type' => 'checkboxes',
            '#options' => ['Dummy A','Dummy B', 'Dummy C'],
            '#title' => $this->t('Teilnehmer'),
        ];

        $form['general_information']['start_time'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Starttime'),
        ];

        $form['general_information']['location'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Location'),
        ];

        $form['preparation'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Preparation'),
        ];

        $form['preparation']['flashback'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Rückblick'),
        ];

        $form['preparation']['plans'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Plans'),
        ];

        $form['open_tasks'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Open Tasks'),
        ];

        $form['open_tasks']['overview_tasks_last_protocol'] = [
            '#type' => 'table',
            '#caption' => t('Übersicht über Aufgaben aus dem letzten Protokoll'),
            '#header' => [t('Kurzbezeichnung'), t('Wer?'),t('Was?'), t('Wann?'), t('Status')],
            '#rows' => [
              [t('Amber'), t('teal')],
              [t('Addi'), t('green')],
            ],
            '#description' => t('Example of using #type.'),
        ];

        $form['open_tasks']['overview_open_tasks'] = [
            '#type' => 'table',
            '#caption' => t('Übersicht über alle noch offenen Aufgaben'),
            '#header' => [t('Kurzbezeichnung'), t('Wer?'),t('Was?'), t('Wann?'), t('Status')],
            '#rows' => [
                [t('Amber'), t('teal')],
                [t('Addi'), t('green')],
            ],
            '#description' => t('Example of using #type.'),
        ];

        $form['method_overview'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Method Overview'),
        ];

        $form['overview_of_methods_for_the_current_meeting'] = [
            '#type' => 'table',
            '#caption' => t('In diesem Schritt werden alle im Rahmen dieses Treffens bearbeitete Methoden aufgelistet.'),
            '#header' => [t('Methodenname'), t('Bewertung?')],
            '#rows' => [
                [t('Amber'), t('teal')],
                [t('Addi'), t('green')],
            ],
            '#description' => t('Example of using #type.'),
        ];

        $form['execute_method'] = [
            '#type' => 'link',
            '#title' => t('Neue Methode durchführen'),
            '#attributes' => [
                'id' => 'execute_method_button',
                'class' => [
                    'btn',
                    'btn-primary'
                ]
            ],
            '#url' => \Drupal\Core\Url::fromRoute('dt_procedure.front.page'),
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
        ];

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        
    }
}