<?php

namespace Drupal\commerce_pricelist\Feeds\Processor;

use Drupal\feeds\Feeds\Processor\EntityProcessorBase;

/**
 * Defines a PriceListItem processor.
 *
 * Creates PriceListItems from feed items.
 *
 * @FeedsProcessor(
 *   id = "entity:PriceListItem",
 *   title = @Translation("Pricelist item"),
 *   description = @Translation("Creates pricelist items from feed items."),
 *   entity_type = "price_list_item",
 *   arguments = {"@entity.manager", "@entity.query"},
 *   form = {
 *     "configuration" = "Drupal\feeds\Feeds\Processor\Form\DefaultEntityProcessorForm",
 *     "option" = "Drupal\feeds\Feeds\Processor\Form\EntityProcessorOptionForm",
 *   },
 * )
 */
class PriceListItemProcessor extends EntityProcessorBase {

  /**
   * {@inheritdoc}
   */
  public function entityLabel() {
    return $this->t('Pricelist item');
  }

  /**
   * {@inheritdoc}
   */
  public function entityLabelPlural() {
    return $this->t('Pricelist items');
  }

}
