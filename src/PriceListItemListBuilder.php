<?php

namespace Drupal\commerce_pricelist;

use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Defines a class to build a listing of Price list item entities.
 *
 * @ingroup commerce_pricelist
 */
class PriceListItemListBuilder extends EntityListBuilder {
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['product'] = $this->t('Product');
    $header['sku'] = $this->t('SKU');
    $header['name'] = $this->t('Name');
    $header['price'] = $this->t('Price');
    $header['quantity'] = $this->t('Quantity');
    $header['price_list'] = $this->t('Price List');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\commerce_pricelist\Entity\PriceListItem */
    $name_url = Url::fromRoute('entity.price_list_item.edit_form', array('price_list_item' => $entity->id()));
    $price_list_url = Url::fromRoute('entity.price_list.edit_form', array('price_list' => $entity->getPriceListId()));
    $name = Link::fromTextAndUrl($entity->label(), $name_url);
    $price_list = Link::fromTextAndUrl($entity->getPriceList()->label(), $price_list_url);
    $row['product'] = $entity->getPurchasedEntity()->label();
    $row['sku'] = $entity->getPurchasedEntity()->getSku();
    $row['name'] = $name;
    $row['price'] = $entity->getPrice();
    $row['quantity'] = $entity->getQuantity();
    $row['price_list'] = $price_list;
    return $row + parent::buildRow($entity);
  }

}
