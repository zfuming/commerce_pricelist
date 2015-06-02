<?php
/**
* @file
* Contains functions for fetching and manipulating pricelist items
*/


/**
 * Returns an initialized pricelist item object.
 *
 * @return
 *   A pricelist item object with all default fields initialized.
 */
function commerce_pricelist_item_new($pricelist_id = NULL, $sku = NULL) {
  return entity_create('commerce_pricelist_item', array('pricelist_id' => $pricelist_id, 'sku' => $sku));
}


/**
 * We save the entity by calling the controller.
 */
function commerce_pricelist_item_save(&$entity) {
  $result = entity_save('commerce_pricelist_item', $entity);
  cache_clear_all('commerce_pricelist', 'cache', TRUE);
  return $result;
}

/**
 * Use the controller to delete the entity.
 */
function commerce_pricelist_item_delete($id) {
  return commerce_pricelist_item_delete_multiple(array($id));
}

/**
 * Use the controller to delete the entity.
 */
function commerce_pricelist_item_delete_multiple($ids) {
  $result = entity_delete_multiple('commerce_pricelist_item', $ids);
  cache_clear_all('commerce_pricelist', 'cache', TRUE);
  return $result;
}

/**
 * Get the id of an existing pricelist item with these values.
 * We do not want more than one item with these values.
 *
 * @param $entity
 * @return int
 */
function commerce_pricelist_item_get_existing_id($pricelist_item) {
  $item_id = db_query("SELECT item_id FROM {commerce_pricelist_item} " .
    "WHERE sku = :sku AND valid_to = :valid_to AND valid_from = :valid_from " .
    "AND quantity = :quantity AND pricelist_id = :pricelist_id",
    array(':sku' => $pricelist_item->sku,
      ':valid_from' => $pricelist_item->valid_from,
      ':valid_to' => $pricelist_item->valid_to,
      ':quantity' => $pricelist_item->quantity,
      ':pricelist_id' => $pricelist_item->pricelist_id))
    ->fetchField();
  return $item_id;
}


/**
 * Callback for a page title when this entity is displayed.
 */
function commerce_pricelist_item_title($entity) {
  return t('Pricelist item');
}


/**
 * Fetch a pricelist item.
 *
 * This function ends up being a shim between the menu system and
 * commerce_pricelist_item_load_multiple().
 *
 * This function gets its name from the menu system's wildcard
 * naming conventions. For example, /path/%wildcard would end
 * up calling wildcard_load(%wildcard value). In our case defining
 * the path: admin/commerce/pricelist/commerce_pricelist/basic/%commerce_pricelist_item in
 * hook_menu() tells Drupal to call commerce_pricelist_item_load().
 *
 * @param int $entity_id
 *   Integer specifying the basic entity id.
 * @param bool $reset
 *   A boolean indicating that the internal cache should be reset.
 *
 * @return object
 *   A fully-loaded $entity object or FALSE if it cannot be loaded.
 *
 * @see commerce_pricelist_item_load_multiple()
 * @see commerce_pricelist_menu()
 */
function commerce_pricelist_item_load($entity_id = NULL, $reset = FALSE) {
  $entity_ids = (isset($entity_id) ? array($entity_id) : array());
  $entity = commerce_pricelist_item_load_multiple($entity_ids, array(), $reset);
  return $entity ? reset($entity) : FALSE;
}

/**
 * Loads multiple basic entities.
 *
 * We only need to pass this request along to entity_load(), which
 * will in turn call the load() method of our entity controller class.
 */
function commerce_pricelist_item_load_multiple($entity_ids = FALSE, $conditions = array(), $reset = FALSE) {
  return entity_load('commerce_pricelist_item', $entity_ids, $conditions, $reset);
}

/**
 * Implements the uri callback.
 */
function commerce_pricelist_item_uri($entity) {
  return array(
    'path' => 'admin/commerce/pricelist/commerce_pricelist/item/' . $entity->item_id,
  );
}
