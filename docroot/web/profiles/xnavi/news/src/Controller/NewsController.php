<?php

namespace Drupal\news\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\news\Helper\NewsHelper;
use Drupal\news\Services\NewsMail;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

class NewsController extends ControllerBase {
    /**
     * @var $mail_service
     */
    protected $mail_service;

    /**
     * Constructor
     */
    public function __construct(NewsMail $mail_service) {
        $this->mail_service = $mail_service;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static($container->get('xnavi_news_mail.mail'));
    }

    public function sendTestMail() {
        // Build mail params.
        $params['subject'] = 'Neuer Inhalt wurde geposted';
        $params['cta_url'] = '/node/1';
        $params['body'] = $this->t('Someone just posted new content:');
        $params['cta_text'] = 'View new post';
        $params['bold_text'] = 'Example title / subject';
        $params['lower_body'] = 'This is a lower body example text.';
        $params['users'] = $this->getAllUsers();
        //kint($params['users']);
        // Send mail via service.
        $mail_service = \Drupal::service('xnavi_news_mail.mail');
        $key = 'xnavi_news_mail';
        //$mail_service->sendMail($key, $params); //TODO: Wieder aktivieren, wenn ich es an einen User schicke
        return array();
    }



    public function content() {
        return ['#markup' => 'News Content'];
    }

    public function activities() {
        $database = \Drupal::database();
        $query = $database->select('activities', 'activities');
        $query->fields('activities', ['nid']);
        $result = $query->execute()->fetchAll();

        foreach ($result as $record) {
            $nid = $record->nid;
            $node_storage = \Drupal::entityManager()->getStorage('node');
            $node = $node_storage->load($nid);

            $title = $node->get('title')->value;
            $creation_date = $node->get('created')->value;

            $items[] = [
                '#wrapper_attributes' => [
                    'class' => ['news_item', 'list-group-item']
                ],
                '#children' => $title . ' wurde erstellt.',
            ];

        }

        $activities = [
            '#theme' => 'item_list',
            '#list_type' => 'ul',
            '#items' => $items,
            '#attributes' => [
                'class' => ['list_group', 'list-group-flush']
            ],
            '#wrapper_attributes' => [
                'class' => ['class_for_div']
            ],
        ];

        return $activities;
    }

    /**
   * Helper function to get all users.
   * @return mixed
   */

   //TODO: Sollte geloescht werden können
    private function getAllUsers(){
        $query = \Drupal::database()->select('users_field_data', 'ufd');
        $query->addField('ufd', 'name');
        $query->addField('ufd', 'mail');
        $query->condition('ufd.status', 1);
        //$data = $query->execute()->fetchAll();

        return $query->execute()->fetchAll();
    }


    public function sendConfirmationMail($email, $salutation, $firstname, $surname) {
        //global $base_url;

        $base_path = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();
        //dsm($base_url);
        $config = $this->config('news.settings');
        $subscription_text = $config->get('subscription_text');
        //Get token from database
        $result = \Drupal::database()->select('newsletter_order', 'no')
                    ->fields('no', ['token'])
                    ->condition('no.email', $email)
                    ->execute();

        foreach($result as $record) {
            $token = $record->token;
        }
                // Build mail params.
        $salutation = ($salutation === 'Herr' || $salutation === 'Frau') ? $salutation : '';
        $params['subject'] = 'Bitte bestätigen Sie Ihre Newsletter-Anmeldung';
        $params['cta_url'] = 'news/newsletter/order/confirmation/' . $token; //TODO: Url aufbauen und im Routing einbauen.
        $params['body'] = $this->t('Um Ihr Abonnement für die E-Mail-Adresse ' . $email . ' zu aktivieren, klicken Sie bitte auf den Abonnieren-Button.');
        $params['cta_text'] = 'Abonnieren';
        $params['bold_text'] = '';
        $params['greeting'] = '';
        $params['token'] = $token;
        $params['news_items'] = '';
        $params['lower_body'] = 'Sollten Sie keine Anmeldung für diesen Newsletter vorgenommen haben, so ignorieren Sie bitte diese E-Mail. Sie werden keine weiteren E-Mails von uns erhalten.' . $subscription_text;
        $params['name_recipient'] = 'Sehr geehrte(r) ' . $salutation . ' ' . $firstname . ' ' . $surname ; //TODO: Anpassen
        $params['email'] = $email;
        $params['base_path'] = $base_path;
        $params['order_flag'] = FALSE;
        // Send mail via service.
        $mail_service = \Drupal::service('xnavi_news_mail.mail');
        $key = 'xnavi_news_mail';
        $mail_service->sendMail($key, $params);
        return ['#markup' => 'Eine Bestätigungsmail wurde an "' . $email . '" gesendet.' ];
    }

    public function recieveConfirmation($token) {

        try {
            $query = \Drupal::database()->update('newsletter_order')
            ->fields([
                'confirmation_flag' => 1,
            ])
            ->condition('token', $token, '=')
            ->execute();
            $html =  '<p>Vielen Dank für die Registrierung zum Newsletter.</p>'; //TODO In Admin bringen
            \Drupal::logger('news')->notice('Newsletter wurde abonniert. Token: ' . $token);

        } catch(Exception $e) {
            \Drupal::logger('news')->error($e);
            $html = '<p>Bei der Registrierung scheint etwas schiefgelaufen zu sein. Bitte kontaktieren Sie den Systemadministrator</p>';
        }
        return ['#markup' => $html];
    }

    public function sendNewsletterAdministration() {
        $link = \Drupal::service('link_generator')->generateFromLink(Link::createFromRoute($this->t('Newsletter absenden'),'news.newsletter_send_newsletter_mail' ));

        return ['#markup' => $link];
    }

    public function sendNewsletter($sendInterval = "0,1,4,12") {
        //TODO Build Mail params
        //$email = 'hoferj@uni-hildesheim.de'; //TODO spaeter loeschen

        //Load configuration
        $config = $this->config('news.settings');
        $greeting_text = $config->get('greeting_text');
        $farewell_text = $config->get('subscription_text');
        $mailSubject = ($config->get('email_subject_text') !== '') ? $config->get('email_subject_text') : 'Newsletter';

        //Base Path
        $base_path = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();


        //Fetch database
        $database = \Drupal::database();
        $query = $database->select('newsletter_order', 'no');
        $query->fields('no',['salutation', 'firstname', 'surname', 'types', 'confirmation_flag', 'token', 'email', 'last_activity', 'interval']);
        $result = $query->execute();

        $highestActivityId = $this->getHighestActivityId();
        $sendWeeks = explode(',', $sendInterval);

        foreach($result as $record) {
            if((int) $record->confirmation_flag === 1) {

                // only send newsletter, if new activities were recorded
                $lastActivity = $record->last_activity;
                $subscriberInterval = $record->interval;

                if (in_array($subscriberInterval, $sendWeeks, false)) {
                    if ($highestActivityId > $lastActivity) {
                        $salutation = ($record->salutation === 'Herr' || $record->salutation === 'Frau') ? $record->salutation : '';
                        $firstname = $record->firstname;
                        $surname = $record->surname;
                        $email = $record->email;
                        $types = explode('|',$record->types);
                        $token = $record->token;

                        //Subject
                        $params['subject'] = $mailSubject;
                        //Base Path
                        $params['base_path'] = $base_path;
                        //Name
                        $params['name_recipient'] = $this->t('Sehr geehrte(r) ' . $salutation . ' ' . $firstname . ' ' . $surname);
                        //Token
                        $params['token'] = $token;
                        //Body
                        $params['body'] = '';
                        $params['greeting'] = $greeting_text;
                        $params['order_flag'] = TRUE;
                        //Context based news

                        $params['news_items'] = $this->_buildNewsletterNewsItems($types, $token);
                        $params['cta_url'] = '';
                        $params['cta_text'] = '';
                        $params['bold_text'] = '';
                        $params['lower_body'] = $farewell_text;
                        //Send E-Mail
                        $params['email'] = $email;
                        $mail_service = \Drupal::service('xnavi_news_mail.mail');
                        $key = 'xnavi_news_mail';

                        if ($params['news_items']['newActivities'] === true) {
                            if (in_array(0, $sendWeeks, false)) {
                                // immediately send E-Mail from admin form
                                $mail_service->sendMail($key, $params);
                                $this->saveLatestActivitySubscriber($this->getHighestActivityId(), $token);
                            } elseif ((int)$subscriberInterval === 1) {
                                // weekly e-mail
                                $isSunday = date('d.m.Y', (strtotime("this sunday"))) === date('d.m.Y');
                                if ($isSunday) {
                                  $mail_service->sendMail($key, $params);
                                  $this->saveLatestActivitySubscriber($this->getHighestActivityId(), $token);
                                }
                            } elseif ((int)$subscriberInterval === 4) {
                                // monthly e-mail
                                $isFirstSundayOfMonth = date('d.m.Y', (strtotime("first sunday of this month"))) === date('d.m.Y');
                                if ($isFirstSundayOfMonth) {
                                  $mail_service->sendMail($key, $params);
                                  $this->saveLatestActivitySubscriber($this->getHighestActivityId(), $token);
                                }
                            } elseif ((int)$subscriberInterval === 12) {
                                // quarterly e-mail
                                $quarterMonths = ["01", "04", "07", "10"];
                                if (in_array(date('m'), $quarterMonths, false)) {
                                    $isFirstSundayOfMonth = date('d.m.Y', (strtotime("first sunday of this month"))) === date('d.m.Y');
                                    if ($isFirstSundayOfMonth) {
                                      $mail_service->sendMail($key, $params);
                                      $this->saveLatestActivitySubscriber($this->getHighestActivityId(), $token);
                                    }
                                }
                            }

                            $this->saveLatestActivity($highestActivityId);
                        }
                    }
                }
            }
        }

        return ['#markup' => 'Eine News-Mail wurde verschickt'];
    }

    public function _buildNewsletterNewsItems($types, $subscriberToken = "") {
                $newsHelper = new NewsHelper();
                $newActivities = false;
                $params = $this->getAllConfiguredContentTypes();

                if ($subscriberToken === "") {
                  $newsletter_log_activity_id = max($newsHelper->getLatestActivityIdFromLog() - 30,0);
                } else {
                  $newsletter_log_activity_id = $newsHelper->getLatestActivityIdFromSubscriber($subscriberToken);
                }
                $highest_activitiy_id = $this->getHighestActivityId();
                $database = \Drupal::database();
                $query = $database->select('activities', 'a');
                $query->fields('a',['nid', 'content_type']);
                $query->condition('activities_id', [$newsletter_log_activity_id + 1, $highest_activitiy_id], 'BETWEEN');
                $result = $query->execute();

                $node_storage = \Drupal::entityTypeManager()->getStorage('node');
                $node_types = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
                foreach($result as $row) {
                    $nid = $row->nid;
                    $content_type = $row->content_type;
                    if(in_array($content_type, $types)) {
                        $node = $node_storage->load($nid);
                        if ($node !== null) {
                          $newActivities = true;
                          $params[$content_type]['news_items'][] = [
                            'title'        => $node->title->value,
                            'type'         => $node_types[$content_type]->get('name'),
                            'type_machine' => $content_type,
                            'nid'          => $nid,
                          ];
                        }
/*                      // Differentiate between content types. Not used anymore.
                        if ($content_type !== 'event' && $node !== null) {
                            $params['news_items'][] = [
                                'title' => $node->title->value,
                                'type' => $node_types[$content_type]->get('name'),
                                'type_machine' => $content_type,
                                'nid' => $nid,
                            ];
                        } elseif ($content_type === 'event' && $node !== null) {
                            $zeit = explode('T', $node->field_zeit->value);
                            $params['news_items'][] = [
                                'title' => $node->title->value,
                                'date' => $zeit[0] ?? '',
                                'time' => $zeit[1] ?? '',
                                'nid' => $nid,
                                'type' => $node_types[$content_type]->get('name'),
                                'type_machine' => $content_type,
                            ];
                        }*/
                    }
                }

                if ($newActivities === false) {
                  $params['newActivities'] = false;
                } else {
                  $params['newActivities'] = true;
                }

                return $params;
    }

    public function sendTestNewsletter($email, $types) {

        //Load configuration
        $config = $this->config('news.settings');
        $base_path = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();


        $salutation = 'Herr';
        $firstname = 'Max';
        $surname = 'Mustermann';
        $types = explode('|',$types);

        $params['subject'] = 'Test Newsletter';
        $params['name_recipient'] = 'Sehr geehrte(r) ' . $salutation . ' ' . $firstname . ' ' . $surname;
        $params['body'] = '';
        $params['greeting'] = $config->get('greeting_text');
        $params['token'] = 'TestTokenABCD';
        $params['cta_text'] = '';
        $params['base_path'] = $base_path;
        $params['bold_text'] = '';
        $params['lower_body'] = $config->get('subscription_text');
        $params['order_flag'] = '';
        $params['cta_url'] = '';

        /*
        $database = \Drupal::database();
        $query = $database->select('activities', 'a');
        $query->fields('a',['nid', 'content_type']);
        $result = $query->execute();

        $node_storage = \Drupal::entityTypeManager()->getStorage('node');
        foreach($result as $row) {
            $nid = $row->nid;
            $content_type = $row->content_type;

            $node = $node_storage->load($nid);

            if ($content_type !== 'event') {
                $params['news_items'][] = ['title' => $node->title->value, 'type' => $content_type];
            } elseif ($content_type === 'event') {
                $zeit = explode('T', $node->field_zeit->value);
                $params['news_items'][] = [
                    'title' => $node->title->value,
                    'date' => $zeit[0],
                    'time' => $zeit[1],
                    'nid' => $nid,
                    'type' => $content_type,
                    ];
            }
        }*/

        $params['news_items'] = $this->_buildNewsletterNewsItems($types);


        //Send E-Mail
        $params['email'] = $email;
        $mail_service = \Drupal::service('xnavi_news_mail.mail');
        $key = 'xnavi_news_mail';
        $mail_service->sendMail($key, $params);

        return ['#markup' => 'Eine Test-News-Mail wurde verschickt'];

    }

    public function getAllContentTypes() {
        $types = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
        //dsm($types);

        return $types;
    }

    /**
     * Returns an array of content types configured to be used with the newsletter.
     * Content types are sorted by their alphabetically by their name.
     *
     * @return array
     * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
     * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
     */
    public function getAllConfiguredContentTypes() {
      $types = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
      foreach($types as $type) {
        $allContentTypes[$type->get('type')] = $type->get('name');
      }

      $config = $this->config('news.settings');
      $configuredContentTypes = $config->get('type_settings');

      foreach($configuredContentTypes as $key => $type) {
        if ($type === 0) {
          unset($configuredContentTypes[$key]);
        } else {
          $configuredContentTypes[$type] = [
            'name' => $allContentTypes[$key],
          ];
        }
      }

      asort($configuredContentTypes);
      return $configuredContentTypes;
    }

    public function getEvents() {
        $events = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'event', 'status' => 1]);
        //dsm($events);
        return $events;
    }

    public function getHighestActivityId() {
        $database = \Drupal::database();

        $query = $database->select('activities', 'a');
        $query->fields('a', ['activities_id']);
        $query->orderBy('activities_id', 'DESC');
        $result = $query->execute();

        foreach($result as $record) {
            $highest_activitiy_id = $record->activities_id;
            break;
        }

        return $highest_activitiy_id;

    }

    public function getLatestNewsletterDateFromLog() {
      $database = \Drupal::database();
      $query = $database->select('newsletter_log', 'nl');
      $query->fields('nl', ['date']);
      $query->orderBy('newsletter_log_id', 'DESC');
      $result = $query->execute();
      $latestNewsletterDate = '';

      foreach($result as $record) {
        $latestNewsletterDate = $record->date;
        break;
      }

      return $latestNewsletterDate;
    }

    public function saveLatestActivity($id) {
        $database = \Drupal::database();
        $query = $database->upsert('newsletter_log')->fields([
            'date' => date('Y-m-d H:i:s', str_replace('-', '/', \Drupal::time()->getCurrentTime())),
            'last_activity' => $id]
            )->key('last_activity')->execute();
        //dsm('Saved');
    }

  /**
   * Updates the ID of the last newsletter item the subscriber received.
   *
   * @param $id int
   *   ID of the last activity.
   * @param $token string
   *   Token of the subscriber.
   */
    public function saveLatestActivitySubscriber($id, $token) {
      $database = \Drupal::database();
      $queryUser = $database->update('newsletter_order')
          ->fields([
            'last_activity' => $id,
          ])
          ->condition('token', $token, '=')
          ->execute();
    }

    public function previewNewsletter() {
        global $base_url;
        //$params = $this->getActivities();

        //dsm($params);
        //Load configuration
        $config = $this->config('news.settings');
        $typeSettings = $config->get('type_settings');


        $typeSettings = array_values(array_diff($typeSettings,[0]));
        $params['news_items'] = $this->_buildNewsletterNewsItems($typeSettings);

        //$params['lower_body'] = $this->t('Sollten Sie keine Anmeldung für diesen Newsletter vorgenommen haben, so ignorieren Sie bitte diese E-Mail. Sie werden keine weiteren E-Mail von uns erhalten.');
        $params['greeting'] = $config->get('greeting_text');
        $params['lower_body'] = $config->get('subscription_text');

        return [
            '#theme' => 'newsletter_preview',
            '#body' => '',
            '#message' =>
                [
                    'name_recipient' => $this->t('Sehr geehrte(r) Herr Max Mustermann'),
                    //'cta_text' => $this->t('Abonnieren'),
                    //'bold_text' => 'Bold Tesxt',
                    'news_items' => $params['news_items'],
                    'greeting' => $params['greeting'],
                    'lower_body' => $params['lower_body'],
                    'base_url' => $base_url,
                ],
        ];
    }

    public function getActivities() {
        $database = \Drupal::database();
        $query = $database->select('activities', 'a');
        $query->fields('a',['nid', 'content_type']);
        $result = $query->execute();

        $node_storage = \Drupal::entityTypeManager()->getStorage('node');
        $node_types = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();

      foreach($result as $row) {
            $nid = $row->nid;
            $content_type = $row->content_type;

            $node = $node_storage->load($nid);

            if ($content_type !== 'event' && $node !== null) {
                $params['news_items'][] = ['title' => $node->title->value, 'type' => $node_types[$content_type]->get('name'), 'nid' => $nid];
            } elseif ($content_type === 'event' && $node !== null) {
                $zeit = explode('T', $node->field_zeit->value);
                $params['news_items'][] = [
                    'title' => $node->title->value,
                    'date' => $zeit[0] ?? '',
                    'time' => $zeit[1] ?? '',
                    'nid' => $nid,
                    'type' => $node_types[$content_type]->get('name'),
                ];
            }
        }

        return $params;
    }

    public function unsubsribeNewsletter($token) {
        $database = \Drupal::database();
        $query = $database->delete('newsletter_order')
                            ->condition('token', $token, '=')
                            ->execute();

        return ['#markup' => '<p>' . $this->t('Sie sind nun von unserem Newsletter abgemeldet. Alle personenbezogenen Daten wurden gelöscht.') . '</p>'];
    }

    public function dashboard() {
        $database = \Drupal::database();
        $query = $database->select('newsletter_order', 'no');
        $query->condition('confirmation_flag', '1');
        $query->addField('no', 'types');
        $result = $query->execute();

        foreach($result as $record) {
            $data[] = explode('|', $record->types);
        }

        $subscribers_count = $this->_getSubcribersCount();
        $email_rows[] = [];
        $rows[] = [];

        if ($subscribers_count > 0) {
          foreach($data as $d) {
            foreach($d as $d2) {
              $data2[] = $d2;
            }
          }
        }

        $header = [$this->t('Kategorie'), $this->t('Anzahl')];
        if ($subscribers_count > 0) {
          $data3 = array_count_values($data2);
          foreach($data3 as $key => $value) {
            $rows[] = [
              $this->_getName($key),
              $value];
          }
        }

        $database = \Drupal::database();
        $query = $database->select('newsletter_order', 'no');
        $query->addField('no', 'salutation');
        $query->addField('no', 'firstname');
        $query->addField('no', 'surname');
        $query->addField('no', 'company');
        $query->addField('no', 'branch');
        $query->addField('no', 'email');
        $query->addField('no', 'interval');
        $query->condition('confirmation_flag', '1');
        $result = $query->execute();

        foreach($result as $record) {
            $email_rows[] = [
                $record->salutation,
                $record->firstname,
                $record->surname,
                $record->company,
                $record->branch,
                $record->email,
                $record->interval,
            ];
        }

        $email_header = ['Anrede', 'Vorname', 'Nachname', 'Unternehmen', 'Branche', 'E-Mail', 'Intervall'];
        $date = $this->getLatestNewsletterDateFromLog();

        if ($date !== '') {
          $sendNewsletterText = $this->t('Newsletter an alle Abonnenten versenden (Newsletter zuletzt am @date versendet)', array('@date' => $date));
        } else {
          $sendNewsletterText = $this->t('Newsletter an alle Abonnenten versenden');
        }

        $link = \Drupal::service('link_generator')->generateFromLink(Link::createFromRoute($sendNewsletterText ,'news.newsletter_send_newsletter_mail' ));

        $newsletter_config_link = \Drupal::service('link_generator')->generateFromLink(Link::createFromRoute($this->t('Newsletter konfigurieren'),'news.newsletter_send_newsletter_administration_form'));

        $newsletter_preview_link = \Drupal::service('link_generator')->generateFromLink(Link::createFromRoute($this->t('Vorschau-Newsletter (exemplarischer Newsletter mit den letzten 30 Portaleinträgen)'),'news.newsletter_preview'), ['#attributes' => ['class' => "btn btn-primary"]], ['#attributes' => ['class' => "btn btn-primary"]]);
        $newsletter_testmail_link = \Drupal::service('link_generator')->generateFromLink(Link::createFromRoute($this->t('Vorschau-Newsletter versenden (als Test an eine E-Mail-Adresse)'),'news.newsletter_send_newsletter_test_mail_form'));
        $config = $this->config('news.settings');


/*      $greeting_text = $config->get('greeting_text_settings');
        $build['greeting_text'] = [
            '#markup' => '<h4>' . $this->t('Begrüßungstext') . '</h4><br/><p>' . $greeting_text . '</p>',
        ];*/

        $build['newsletter_send'] = [
            '#theme' => 'item_list',
            '#list_type' => 'ul',
            '#items' => [$newsletter_config_link, $newsletter_preview_link, $newsletter_testmail_link, $link],
            '#attributes' => ['id' => 'newsletter-actions']
        ];

        $build['subscribers'] = [
          '#markup' => '<p><strong>' . $this->t('Anzahl Abonennten: '). '</strong> ' . $subscribers_count . '</p>',
        ];

        $build['categories'] = [
          '#type' => 'table',
          '#header' => $header,
          '#rows' => $rows,
        ];

        $build['emails'] = [
          '#type' => 'table',
          '#header' => $email_header,
          '#rows' => $email_rows,
        ];



        return $build;

    }

    //TODO: Auslagern in Service oder aehnliches wird auch in NewsletterOrderForm verwendet
    public function _getName($type_name) {
        $entities = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => $type_name]);
        //dsm($entities);
        foreach($entities as $entity) {
            //$label = $entity->label();
            //$bundle = $entity->bundle();
            //$bundle_type_id = $entity->getEntityType()->getBundleEntityType();
            $bundle_label = \Drupal::entityTypeManager()->getStorage('node_type')->load($entity->bundle())->label();
            break;
        }

        //dsm($label);
        //dsm($bundle);
        //dsm($bundle_type_id);
        return $bundle_label;
    }

    public function _getSubcribersCount() {
        $database = \Drupal::database();

        $query = $database->select('newsletter_order', 'no');
        $query->condition('confirmation_flag', '1');

        $num_rows = $query->countQuery()->execute()->fetchField(0);

        return $num_rows;
    }

    public function activityStream() {
        $database = \Drupal::database();

        $query = $database->select('activities', 'a');
        $query->fields('a', ['nid']);

        $result = $query->execute();

        foreach($result as $record) {

            $node_storage = \Drupal::entityTypeManager()->getStorage('node');
            $node = $node_storage->load($record->nid);

            $activities[] = [
                'nid' => $node->id(),
                'title' => $node->title->value,
                'bundle' => $node->bundle(),
            ];
        }

        dsm($activities);
        return [
            '#theme' => 'activity_stream',
            '#activities' => $activities,
        ];
    }

}
