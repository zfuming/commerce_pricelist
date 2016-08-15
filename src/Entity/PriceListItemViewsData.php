<?php

namespace Drupal\commerce_pricelist\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Price list item entities.
 */
class PriceListItemViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['price_list_item']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Price list item'),
      'help' => $this->t('The Price list item ID.'),
    );

    return $data;
  }

}
