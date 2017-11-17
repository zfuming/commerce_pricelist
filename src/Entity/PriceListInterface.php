<?php

namespace Drupal\commerce_pricelist\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Price list entities.
 *
 * @ingroup commerce_pricelist
 */
interface PriceListInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {
  // Add get/set methods for your configuration properties here.
  /**
   * Gets the Price list type.
   *
   * @return string
   *   The Price list type.
   */
  public function getType();

  /**
   * Gets the Price list name.
   *
   * @return string
   *   Name of the Price list.
   */
  public function getName();

  /**
   * Sets the Price list name.
   *
   * @param string $name
   *   The Price list name.
   *
   * @return \Drupal\commerce_pricelist\Entity\PriceListInterface
   *   The called Price list entity.
   */
  public function setName($name);

  /**
   * Gets the Price list creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Price list.
   */
  public function getCreatedTime();

  /**
   * Sets the Price list creation timestamp.
   *
   * @param int $timestamp
   *   The Price list creation timestamp.
   *
   * @return \Drupal\commerce_pricelist\Entity\PriceListInterface
   *   The called Price list entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Price list published status indicator.
   *
   * Unpublished Price list are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Price list is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Price list.
   *
   * @param bool $published
   *   TRUE to set this Price list to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\commerce_pricelist\Entity\PriceListInterface
   *   The called Price list entity.
   */
  public function setPublished($published);

}
