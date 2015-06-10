<?php /**
 * @file
 * Contains \Drupal\commerce_pricelist\Entity\CommercePricelistList.
 */

namespace Drupal\commerce_pricelist\Entity;

use Drupal\Core\Entity\Entity;

/**
 * @EntityType(
 *  id = "commerce_pricelist_list",
 *  label = @Translation("Commerce Price List list"),
 *  controllers = {
 *    "storage" = "\Drupal\commerce_pricelist\CommercePricelistListController
 *  },
 *  base_table = "commerce_pricelist_list",
 *  entity_keys = {
 *    "id" = "list_id",
 *    "label" = "title"
 *  }
 * )
 */
class CommercePricelistList extends Entity {

  /**
   * @FIXME
   * Move all logic relating to the commerce_pricelist_list entity type into this
   * class. For more information, see https://www.drupal.org/node/1827470.
   */

}
