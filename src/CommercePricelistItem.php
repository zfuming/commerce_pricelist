<?php
namespace Drupal\commerce_pricelist;

use Drupal\Core\Entity\Entity;

/**
 * @EntityType(
 *  id = "commerce_pricelist_item",
 *  label = @Translation("Commerce Pricelist item"),
 *  controllers = {
 *    "storage" = "\Drupal\commerce_pricelist\CommercePricelistItemController
 *  },
 *  base_table = "commerce_pricelist_item",
 *  entity_keys = {
 *    "id" = "item_id",
 *  }
 * )
 */
class CommercePricelistItem extends Entity {

}
