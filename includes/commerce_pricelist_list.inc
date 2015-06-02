<?php
/**
 * @file
 * Contains functions for fetching and manipulating pricelist entities.
 */


/**
 * Returns an initialized pricelist list object.
 *
 * @return
 *   A pricelist list object with all default fields initialized.
 */
function commerce_pricelist_list_new() {
  return entity_create('commerce_pricelist_list', array());
}


/**
 * We save the entity by calling the controller.
 */
function commerce_pricelist_list_save(&$entity) {
  $result = entity_save('commerce_pricelist_list', $entity);
  cache_clear_all('commerce_pricelist', 'cache', TRUE);
  return $result;
}

/**
 * Use the controller to delete the entity.
 */
function commerce_pricelist_list_delete($id) {
  $result = entity_delete('commerce_pricelist_list', $id);
  cache_clear_all('commerce_pricelist', 'cache', TRUE);
  return $result;
}

/**
 * Fetch a basic object.
 *
 * This function ends up being a shim between the menu system and
 * commerce_pricelist_list_load_multiple().
 *
 * This function gets its name from the menu system's wildcard
 * naming conventions. For example, /path/%wildcard would end
 * up calling wildcard_load(%wildcard value). In our case defining
 * the path: admin/commerce/pricelist/commerce_pricelist/basic/%commerce_pricelist_list in
 * hook_menu() tells Drupal to call commerce_pricelist_list_load().
 *
 * @param int $entity_id
 *   Integer specifying the basic entity id.
 * @param bool $reset
 *   A boolean indicating that the internal cache should be reset.
 *
 * @return object
 *   A fully-loaded $entity object or FALSE if it cannot be loaded.
 *
 * @see commerce_pricelist_list_load_multiple()
 * @see commerce_pricelist_menu()
 */
function commerce_pricelist_list_load($list_id = NULL, $reset = FALSE) {
  $list_id = (isset($list_id) ? array($list_id) : array());
  $entity = commerce_pricelist_list_load_multiple($list_id, array(), $reset);
  return $entity ? reset($entity) : FALSE;
}

/**
 * Loads multiple basic entities.
 *
 * We only need to pass this request along to entity_load(), which
 * will in turn call the load() method of our entity controller class.
 */
function commerce_pricelist_list_load_multiple($list_id = FALSE, $conditions = array(), $reset = FALSE) {
  return entity_load('commerce_pricelist_list', $list_id, $conditions, $reset);
}

/**
 * Implements the uri callback.
 */
function commerce_pricelist_list_uri($entity) {
  return array(
    'path' => 'admin/commerce/pricelist/commerce_pricelist_list/' . $entity->list_id,
  );
}

/**
 * Callback for a page title when this entity is displayed.
 */
function commerce_pricelist_list_title($entity) {
  return t('@item_title', array('@item_title' => $entity->title));
}
