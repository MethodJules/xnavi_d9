<?php

namespace Drupal\seminar_work;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a seminar work entity type.
 */
interface SeminarWorkInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

  /**
   * Gets the seminar work title.
   *
   * @return string
   *   Title of the seminar work.
   */
  public function getTitle();

  /**
   * Sets the seminar work title.
   *
   * @param string $title
   *   The seminar work title.
   *
   * @return \Drupal\seminar_work\SeminarWorkInterface
   *   The called seminar work entity.
   */
  public function setTitle($title);

  /**
   * Gets the seminar work creation timestamp.
   *
   * @return int
   *   Creation timestamp of the seminar work.
   */
  public function getCreatedTime();

  /**
   * Sets the seminar work creation timestamp.
   *
   * @param int $timestamp
   *   The seminar work creation timestamp.
   *
   * @return \Drupal\seminar_work\SeminarWorkInterface
   *   The called seminar work entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the seminar work status.
   *
   * @return bool
   *   TRUE if the seminar work is enabled, FALSE otherwise.
   */
  public function isEnabled();

  /**
   * Sets the seminar work status.
   *
   * @param bool $status
   *   TRUE to enable this seminar work, FALSE to disable.
   *
   * @return \Drupal\seminar_work\SeminarWorkInterface
   *   The called seminar work entity.
   */
  public function setStatus($status);

}
