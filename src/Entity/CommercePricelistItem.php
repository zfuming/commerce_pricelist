<?php /**
 * @file
 * Contains \Drupal\commerce_pricelist\Entity\CommercePricelistItem.
 */

namespace Drupal\commerce_pricelist\Entity;

use Drupal\Core\Entity\ContentEntityBase;

/**
 * @EntityType(
 *  id = "commerce_pricelist_item",
 *  label = @Translation("Commerce Price List item"),
 *  controllers = {
 *    "storage" = "\Drupal\commerce_pricelist\CommercePricelistItemController
 *  },
 *  base_table = "commerce_pricelist_item",
 *  entity_keys = {
 *    "id" = "item_id",
 *  }
 * )
 */
class CommercePricelistItem extends ContentEntityBase {

  /**
   * @FIXME
   * Move all logic relating to the commerce_pricelist_item entity type into this
   * class. For more information, see https://www.drupal.org/node/1827470.
   */

}
