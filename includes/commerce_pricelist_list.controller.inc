<?php
/**
* @file
* Summary
*
* Description
*/


/**
 * CommercePricelistListControllerInterface definition.
 *
 * We create an interface here because anyone could come along and
 * use hook_entity_info_alter() to change our controller class.
 * We want to let them know what methods our class needs in order
 * to function with the rest of the module, so here's a handy list.
 *
 * @see hook_entity_info_alter()
 */
interface CommercePricelistListControllerInterface
  extends DrupalEntityControllerInterface {

  /**
   * Create an entity.
   */
  public function create();

  /**
   * Save an entity.
   *
   * @param object $entity
   *   The entity to save.
   */
  public function save($entity);

  /**
   * Delete an entity.
   *
   * @param int $entity
   *   The entity to delete.
   */
  public function delete($entity);

}

/**
 * CommercePricelistListController extends DrupalDefaultEntityController.
 *
 * Our subclass of DrupalDefaultEntityController lets us add a few
 * important create, update, and delete methods.
 */
class CommercePricelistListController
  extends DrupalDefaultEntityController
  implements CommercePricelistListControllerInterface,
EntityAPIControllerInterface {

  /**
   * Create and return a new commerce_pricelist_list entity.
   */
  public function create() {
    $entity = new stdClass();
    $entity->type = 'commerce_pricelist_list';
    $entity->list_id = 0;
    $entity->weight = 0;
    $entity->title = '';
    $entity->status = 1;
    return $entity;
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
   * Saves the custom fields using drupal_write_record().
   */
  public function save($entity) {
    // If our entity has no uuid, then we need to give it a
    // time of creation.
    if (empty($entity->list_id)) {
      $entity->created = time();
    }

    // Invoke hook_entity_presave().
    module_invoke_all('entity_presave', $entity, 'commerce_pricelist_list');
    // The 'primary_keys' argument determines whether this will be an insert
    // or an update. So if the entity already has an ID, we'll specify
    // uuid as the key.
    $primary_keys = $entity->list_id ? 'list_id' : array();
    // Write out the entity record.
    drupal_write_record('commerce_pricelist_list', $entity, $primary_keys);
    // We're going to invoke either hook_entity_update() or
    // hook_entity_insert(), depending on whether or not this is a
    // new entity. We'll just store the name of hook_entity_insert()
    // and change it if we need to.
    $invocation = 'entity_insert';
    // Now we need to either insert or update the fields which are
    // attached to this entity. We use the same primary_keys logic
    // to determine whether to update or insert, and which hook we
    // need to invoke.
    if (empty($primary_keys)) {
      field_attach_insert('commerce_pricelist_list', $entity);
    }
    else {
      field_attach_update('commerce_pricelist_list', $entity);
      $invocation = 'entity_update';
    }
    // Invoke either hook_entity_update() or hook_entity_insert().
    module_invoke_all($invocation, $entity, 'commerce_pricelist_list');
    return $entity;
  }

  /**
   * Delete a single entity.
   *
   * Really a convenience function for deleteMultiple().
   */
  public function delete($id) {
    $this->deleteMultiple(array($id));
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
  public function deleteMultiple($ids) {
    $entities = $ids ? $this->load($ids) : FALSE;

    $transaction = db_transaction();
    try {
      foreach ($entities as $entity) {
        // Invoke hook_entity_delete().
        module_invoke_all('entity_delete', $entity, 'commerce_pricelist_list');
      }
      db_delete('commerce_pricelist_list')
        ->condition('list_id', $ids, 'IN')
        ->execute();
    }
    catch (Exception $e) {
      $transaction->rollback();
      watchdog_exception('commerce pricelist', $e);
      throw $e;
    }
  }
}
