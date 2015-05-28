<?php
/**
* @file
* Summary
*
* Description
*/


/**
 * CommercePricelistListController extends DrupalDefaultEntityController.
 *
 * Our subclass of DrupalDefaultEntityController lets us add a few
 * important create, update, and delete methods.
 */
class CommercePricelistListController extends EntityAPIController {

  /**
   * Create and return a new commerce_pricelist_list entity.
   */
  public function create(array $values = array()) {
    $values += array(
      'list_id' => 0,
      'weight' => 0,
      'title' => '',
      'status' => 1
    );
    return parent::create($values);
  }

  /**
   * Unserializes the data property of loaded lists.
   */
  public function attachLoad(&$queried_lists, $revision_id = FALSE) {
    foreach ($queried_lists as $list_id => &$list) {
      $list->data = unserialize($list->data);
    }

    // Call the default attachLoad() method. This will add fields and call
    // hook_commerce_list_load().
    parent::attachLoad($queried_lists, $revision_id);
  }

  /**
   * Saves the entity.
   */
  public function save($entity, DatabaseTransaction $transaction = NULL) {
    // If our entity has no id, then we need to give it a
    // time of creation.
    if (empty($entity->list_id)) {
      $entity->created = time();
    }

    return parent::save($entity, $transaction);
  }


  /**
   * Delete one or more commerce_pricelist_list entities.
   *
   * Deletion is unfortunately not supported in the base
   * DrupalDefaultEntityController class.
   *
   * @param array $ids
   *   An array of entity IDs.
   */
  public function delete($ids, DatabaseTransaction $transaction = NULL) {
    parent::delete($ids, $transaction);

    // Delete orphan pricelist items.
    $query = db_select('commerce_pricelist_list', 'l')
      ->fields('l', array('item_id'))
      ->condition('list_id', $ids, 'IN')
      ->execute();
    $pricelist_items = $query->fetchAllKeyed();

    entity_delete_multiple('commerce_pricelist_item', $pricelist_items);
  }
}
