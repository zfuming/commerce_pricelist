<?php

namespace Drupal\commerce_pricelist\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Price list item entities.
 *
 * @ingroup commerce_pricelist
 */
interface PriceListItemInterface extends ContentEntityInterface, EntityChangedInterface {
  // Add get/set methods for your configuration properties here.
  /**
   * Gets the Price list item name.
   *
   * @return string
   *   Name of the Price list item.
   */
  public function getName();

  /**
   * Sets the Price list item name.
   *
   * @param string $name
   *   The Price list item name.
   *
   * @return \Drupal\commerce_pricelist\Entity\PriceListItemInterface
   *   The called Price list item entity.
   */
  public function setName($name);

  /**
   * Gets the Price list item quantity.
   *
   * @return string
   *   Quantity of the Price list item.
   */
  public function getQuantity();

  /**
   * Sets the Price list item quantity.
   *
   * @param string $quantity
   *   The Price list item quantity.
   *
   * @return \Drupal\commerce_pricelist\Entity\PriceListItemInterface
   *   The called Price list item entity.
   */
  public function setQuantity($quantity);

  /**
   * Gets the Price list item creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Price list item.
   */
  public function getCreatedTime();

  /**
   * Sets the Price list item creation timestamp.
   *
   * @param int $timestamp
   *   The Price list item creation timestamp.
   *
   * @return \Drupal\commerce_pricelist\Entity\PriceListItemInterface
   *   The called Price list item entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Price list item published status indicator.
   *
   * Unpublished Price list item are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Price list item is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Price list item.
   *
   * @param bool $published
   *   TRUE to set this Price list item to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\commerce_pricelist\Entity\PriceListItemInterface
   *   The called Price list item entity.
   */
  public function setPublished($published);

}
