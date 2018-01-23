<?php

namespace Drupal\commerce_pricelist\Entity;

use Drupal\commerce_price\Price;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

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

  /**
   * Gets the Price list item weight.
   *
   * @return string
   *   Weight of the Price list item.
   */
  public function getWeight();

  /**
   * Sets the Price list item weight.
   *
   * @param string $weight
   *   The Price list item weight.
   *
   * @return \Drupal\commerce_pricelist\Entity\PriceListItemInterface
   *   The called Price list item entity.
   */
  public function setWeight($weight);

  /**
   * Sets the Price list item price_list_id.
   *
   * @param string $priceListId
   *   The Price list item price_list_id.
   *
   * @return \Drupal\commerce_pricelist\Entity\PriceListItemInterface
   *   The called Price list item entity.
   */
  public function setPriceListId($priceListId);

  /**
   * Gets whether the price list item has a purchased entity.
   *
   * @return bool
   *   TRUE if the price list item has a purchased entity, FALSE otherwise.
   */
  public function hasPurchasedEntity();

  /**
   * Gets the purchased entity.
   *
   * @return \Drupal\commerce\PurchasableEntityInterface|null
   *   The purchased entity, or NULL.
   */
  public function getPurchasedEntity();

  /**
   * Gets the purchased entity ID.
   *
   * @return int
   *   The purchased entity ID.
   */
  public function getPurchasedEntityId();

  /**
   * Sets the Price list item purchased entity.
   *
   * @param string $target_id
   *   The Price list item purchased_entity_id.
   *
   * @return \Drupal\commerce_pricelist\Entity\PriceListItemInterface
   *   The called Price list item entity.
   */
  public function setPurchasedEntityId($target_id);

  /**
   * Gets the Price list item price.
   *
   * @return string
   *   Price of the Price list item.
   */
  public function getPrice();

  /**
   * Sets the Price list item price.
   *
   * @param \Drupal\commerce_price\Price $price
   *   The Price list item Price.
   *
   * @return \Drupal\commerce_pricelist\Entity\PriceListItemInterface
   *   The called Price list item entity.
   */
  public function setPrice(Price $price);

}
