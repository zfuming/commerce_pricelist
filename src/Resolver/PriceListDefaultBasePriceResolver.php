<?php

namespace Drupal\commerce_pricelist\Resolver;

use Drupal\commerce\Context;
use Drupal\commerce\PurchasableEntityInterface;
use Drupal\commerce_price\Price;


/**
 * Class PriceListDefaultBasePriceResolver.
 *
 * @package Drupal\commerce_pricelist
 */
class PriceListDefaultBasePriceResolver implements PriceListPriceResolverInterface {

  /**
   * {@inheritdoc}
   */
  public function resolve(PurchasableEntityInterface $entity, $quantity, Context $context) {
    // Make sure that product variation has a field called Saleprice.
    if (!$entity->hasField('bundle_price')) {
      return;
    }

    if ($entity->get('bundle_price')->isEmpty()) {
      return;
    }

    /** @var \Drupal\commerce_price\Price $sale_price */
    $sale_price = $entity->get('bundle_price')->first()->toPrice();
    $sale_price_number = $sale_price->getNumber();
    $sale_price_currency_code = $sale_price->getCurrencyCode();

    if (!$sale_price_number || $sale_price_number == 0) {
      return;
    }

    return new Price($sale_price_number, $sale_price_currency_code);
  }

  /**
   * @param PurchasableEntityInterface $entity
   * @return bool
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getPrice(PurchasableEntityInterface $entity) {
    $type_id = $entity->getEntityType()->id();
    $currency_code = \Drupal::service('commerce_store.current_store')->getStore()->getDefaultCurrencyCode();
    $price = new Price('0.00', $currency_code);
    if ($type_id == 'commerce_product_bundle') {
      if (!$entity->get('bundle_price')->isEmpty() && !$entity->get('bundle_price')->first()->toPrice()->isZero()) {
        $price = $entity->get('bundle_price')->first()->toPrice();
      }
    } else {
      if (!$entity->getPrice()->isZero()) {
        $price = $entity->getPrice();
      }
    }
    return $price;
  }

}
