<?php

namespace Drupal\commerce_pricelist\Entity;

use Drupal\commerce\Entity\CommerceBundleEntityInterface;

/**
 * Defines the interface for price list item types.
 */
interface PriceListItemTypeInterface extends CommerceBundleEntityInterface {

  /**
   * Gets the price list item type's purchasable entity type ID.
   *
   * E.g, if price list items of this type are used to purchase product variations,
   * the purchasable entity type ID will be 'commerce_product_variation'.
   *
   * @return string
   *   The purchasable entity type ID.
   */
  public function getPurchasableEntityTypeId();

  /**
   * Sets the price list item type's purchasable entity type ID.
   *
   * @param string $purchasable_entity_type_id
   *   The purchasable entity type.
   *
   * @return $this
   */
  public function setPurchasableEntityTypeId($purchasable_entity_type_id);

  /**
   * Gets the price list item type's price list type ID.
   *
   * @return string
   *   The price list type.
   */
  public function getPriceListTypeId();

  /**
   * Sets the price list item type's price list type ID.
   *
   * @param string $price_list_type_id
   *   The price list type ID.
   *
   * @return $this
   */
  public function setPriceListTypeId($price_list_type_id);

}
