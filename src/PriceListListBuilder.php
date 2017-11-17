<?php

namespace Drupal\commerce_pricelist;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Price list entities.
 *
 * @ingroup commerce_pricelist
 */
class PriceListListBuilder extends EntityListBuilder {
  use LinkGeneratorTrait;
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Price list ID');
    $header['name'] = $this->t('Name');
    $header['item'] = $this->t('Item');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\commerce_pricelist\Entity\PriceList */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.price_list.edit_form', array(
          'price_list' => $entity->id(),
        )
      )
    );
    $row['item'] = $this->l(
      'Item',
      new Url(
        'commerce_pricelist.price_list_item.collection', array(
          'price_list_id' => $entity->id(),
        )
      )
    );
    return $row + parent::buildRow($entity);
  }

}
