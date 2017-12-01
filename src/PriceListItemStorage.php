<?php

namespace Drupal\commerce_pricelist;

use Drupal\commerce\CommerceContentEntityStorage;

/**
 * Defines the product attribute value storage.
 */
class PriceListItemStorage extends CommerceContentEntityStorage implements PriceListItemStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function loadMultipleByPriceList($price_list_id) {
    $entity_query = $this->getQuery();
    $entity_query->condition('price_list_id', $price_list_id);
    $entity_query->sort('weight');
    $result = $entity_query->execute();
    return $result ? $this->loadMultiple($result) : [];
  }

}
