<?php

namespace Drupal\commerce_pricelist;

use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Defines a class to build a listing of Price list entities.
 *
 * @ingroup commerce_pricelist
 */
class PriceListListBuilder extends EntityListBuilder {
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Price list ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\commerce_pricelist\Entity\PriceList */
    $name_url = Url::fromRoute('entity.price_list.canonical', array('price_list' => $entity->id()));
    $name = Link::fromTextAndUrl($entity->label(), $name_url);
    $row['id'] = $entity->id();
    $row['name'] = $name;
    return $row + parent::buildRow($entity);
  }

}
