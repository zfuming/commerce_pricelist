<?php

namespace Drupal\commerce_pricelist;

use Drupal\Core\Entity\ContentEntityStorageInterface;

/**
 * Defines the interface for product attribute value storage.
 */
interface PriceListItemStorageInterface extends ContentEntityStorageInterface {

  /**
   * Loads product attribute values for the given product attribute.
   *
   * @param string $price_list_id
   *   The item price list ID.
   *
   * @return \Drupal\commerce_pricelist\Entity\PriceListItemInterface[]
   *   The price list item values, indexed by id, ordered by weight.
   */
  public function loadMultipleByPriceList($price_list_id);

}
