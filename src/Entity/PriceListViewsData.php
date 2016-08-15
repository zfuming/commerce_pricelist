<?php

namespace Drupal\commerce_pricelist\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Price list entities.
 */
class PriceListViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['price_list']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Price list'),
      'help' => $this->t('The Price list ID.'),
    );

    return $data;
  }

}
