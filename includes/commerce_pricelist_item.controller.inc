<?php
/**
 * @file
 * Contains CommercePricelistItemController
 */


/**
 * CommercePricelistListController extends EntityAPIController.
 *
 * Our subclass of EntityAPIController lets us add a few
 * important create, update, and delete methods.
 */
class CommercePricelistItemController extends EntityAPIController {

  /**
   * Create and return a new commerce_pricelist_item entity.
   */
  public function create(array $values = array()) {
    $values += array(
      'pricelist_id' => NULL,
      'sku' => NULL,
      'item_id' => 0,
      'valid_to' => COMMERCE_PRICELIST_UNIX_TIME_APOCALYPSE,
      'valid_from' => 0,
      'quantity' => 0
    );
    return parent::create($values);
  }

  /**
   * Saves the custom fields using drupal_write_record().
   */
  public function save($entity, DatabaseTransaction $transaction = NULL) {

    // Make sure we are saving into an existing pricelist.
    $pricelist = entity_load('commerce_pricelist_list', array($entity->pricelist_id));
    if (empty($pricelist)) {
      watchdog('commerce pricelist', t('Attepted to save a pricelist item into a non-existent pricelist.'));
      return FALSE;
    }

    // Set default values.
    if (empty($entity->created)) {
      $entity->created = time();
    }

    if (empty($entity->valid_to)) {
      $entity->valid_to = COMMERCE_PRICELIST_UNIX_TIME_APOCALYPSE;
    }

    // Check if an item already exists with these values.
    $existing_id = commerce_pricelist_item_get_existing_id(
      $entity->sku,
      $entity->valid_from,
      $entity->quantity,
      $entity->pricelist_id);

    if ($existing_id) {
      $entity->item_id = $existing_id;
      drupal_set_message(t('Found and replaced a duplicate pricelist item in this list.'));
    }

    return parent::save($entity, $transaction);
  }
}
