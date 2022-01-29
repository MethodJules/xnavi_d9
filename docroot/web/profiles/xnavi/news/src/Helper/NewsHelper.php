<?php

namespace Drupal\news\Helper;

class NewsHelper {

  /**
   * Returns the ID of the last logged activity.
   *
   * @return int activity ID
   */
  public function getLatestActivityIdFromLog() {
    $database = \Drupal::database();
    $query = $database->select('newsletter_log', 'nl');
    $query->fields('nl', ['last_activity']);
    $query->orderBy('last_activity', 'DESC');
    $result = $query->execute();
    $last_activity = 0;

    foreach($result as $record) {
      $last_activity = $record->last_activity;
      break;
    }

    return $last_activity;
  }

  /**
   * Returns the ID of the subscriber's last logged activity.
   *
   * @return int activity ID
   */
  public function getLatestActivityIdFromSubscriber($subscriberToken) {
    $database = \Drupal::database();
    $query = $database->select('newsletter_order', 'no');
    $query->condition('token', $subscriberToken, '=');
    $query->fields('no', ['last_activity']);
    $query->orderBy('last_activity', 'DESC');
    $result = $query->execute();
    $last_activity = 0;

    foreach($result as $record) {
      $last_activity = $record->last_activity;
      break;
    }

    return $last_activity;
  }

}
