<?php

namespace Drupal\commerce_pricelist\Resolver;

use Drupal\commerce\PurchasableEntityInterface;
use Drupal\commerce_price\Plugin\Field\FieldType\Price;


/**
 * Class PriceListDefaultBasePriceResolver.
 *
 * @package Drupal\commerce_pricelist
 */
class PriceListDefaultBasePriceResolver implements PriceListBasePriceResolverInterface {

  /**
   * {@inheritdoc}
   */
  public function resolve(PurchasableEntityInterface $entity, $quantity = 1) {
    return $this->applies($entity) ? $entity->price->first() : NULL;
  }

  /**
   * Determines whether the resolver applies to the given purchasable entity.
   *
   * @param \Drupal\commerce\PurchasableEntityInterface $entity
   *   The purchasable entity.
   *
   * @return bool
   *   TRUE if the resolver applies to the given purchasable entity, FALSE
   *   otherwise.
   */
  public function applies(PurchasableEntityInterface $entity) {
    return $entity->hasField('price') && !$entity->get('price')->isEmpty();
  }

}
