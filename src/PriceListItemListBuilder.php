<?php

namespace Drupal\commerce_pricelist;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Price list item entities.
 *
 * @ingroup commerce_pricelist
 */
class PriceListItemListBuilder extends EntityListBuilder {
  use LinkGeneratorTrait;
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['product'] = $this->t('Product');
    $header['sku'] = $this->t('SKU');
    $header['name'] = $this->t('Name');
    $header['price'] = $this->t('Price');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\commerce_pricelist\Entity\PriceListItem */
    $row['product'] = $entity->getProductVariation()->label();
    $row['sku'] = $entity->getProductVariation()->getSku();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.price_list_item.edit_form', array(
          'price_list_item' => $entity->id(),
        )
      )
    );
    $row['price'] = $entity->getPrice();
    return $row + parent::buildRow($entity);
  }

}
