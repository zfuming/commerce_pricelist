<?php

namespace Drupal\commerce_pricelist\Feeds\Processor;

use Drupal\feeds\Feeds\Processor\EntityProcessorBase;

/**
 * Defines a PriceList processor.
 *
 * Creates PriceLists from feed items.
 *
 * @FeedsProcessor(
 *   id = "entity:PriceList",
 *   title = @Translation("Pricelist"),
 *   description = @Translation("Creates pricelists from feed items."),
 *   entity_type = "price_list",
 *   arguments = {"@entity.manager", "@entity.query"},
 *   form = {
 *     "configuration" = "Drupal\feeds\Feeds\Processor\Form\DefaultEntityProcessorForm",
 *     "option" = "Drupal\feeds\Feeds\Processor\Form\EntityProcessorOptionForm",
 *   },
 * )
 */
class PriceListProcessor extends EntityProcessorBase {

  /**
   * {@inheritdoc}
   */
  public function entityLabel() {
    return $this->t('Pricelist');
  }

  /**
   * {@inheritdoc}
   */
  public function entityLabelPlural() {
    return $this->t('Pricelists');
  }

}
