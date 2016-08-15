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
    $header['id'] = $this->t('Price list item ID');
    $header['name'] = $this->t('Name');
    $header['price'] = $this->t('Price');
    $header['product'] = $this->t('Product');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\commerce_pricelist\Entity\PriceListItem */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.price_list_item.edit_form', array(
          'price_list_item' => $entity->id(),
        )
      )
    );
    $row['price'] = $entity->getPrice();
    $row['product'] = $entity->getProductVariation()->label();
    return $row + parent::buildRow($entity);
  }

}
