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
interface CommercePricelistItemControllerInterface
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
   * @param object $entity
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
class CommercePricelistItemController
  extends DrupalDefaultEntityController
  implements CommercePricelistListControllerInterface {

  /**
   * Create and return a new commerce_pricelist_list entity.
   */
  public function create() {
    $entity = new stdClass();
    $entity->type = 'commerce_pricelist_item';
    $entity->item_id = 0;
    $entity->valid_to = COMMERCE_PRICELIST_UNIX_TIME_APOCALYPSE; // The apocalypse
    return $entity;
  }

  /**
   * Saves the custom fields using drupal_write_record().
   */
  public function save($entity) {
    // If our entity has no uuid, then we need to give it a
    // time of creation.
    if (empty($entity->uuid)) {
      $entity->created = time();
    }

    if (empty($entity->valid_to)) {
      $entity->valid_to = COMMERCE_PRICELIST_UNIX_TIME_APOCALYPSE; // The apocalypse
    }

    // Invoke hook_entity_presave().
    module_invoke_all('entity_presave', $entity, 'commerce_pricelist_item');

    // Check if an item already exists with these values
    $existing_id = commerce_pricelist_item_get_existing_id($entity->sku, $entity->valid_from, $entity->quantity, $entity->pricelist_uuid);

    if ($existing_id) $entity->item_id = $existing_id;

    $primary_keys = $entity->item_id ? 'item_id' : array();
    // Write out the entity record.
    drupal_write_record('commerce_pricelist_item', $entity, $primary_keys);
    // We're going to invoke either hook_entity_update() or
    // hook_entity_insert(), depending on whether or not this is a
    // new entity. We'll just store the name of hook_entity_insert()
    // and change it if we need to.
    $invocation = 'entity_insert';

    if (!empty($primary_keys)) {
      $invocation = 'entity_update';
    }
    // Invoke either hook_entity_update() or hook_entity_insert().
    module_invoke_all($invocation, $entity, 'commerce_pricelist_item');
    return $entity;
  }

  /**
   * Delete a single entity.
   *
   * Really a convenience function for deleteMultiple().
   */
  public function delete($entity) {
    $this->deleteMultiple(array($entity));
  }

  /**
   * Delete one or more commerce_pricelist_list entities.
   *
   * Deletion is unfortunately not supported in the base
   * DrupalDefaultEntityController class.
   *
   * @param array $entities
   *   An array of entity IDs or a single numeric ID.
   */
  public function deleteMultiple($entities) {
    $entity_ids = array();
    if (!empty($entities)) {
      $transaction = db_transaction();
      try {
        foreach ($entities as $entity) {
          // Invoke hook_entity_delete().
          module_invoke_all('entity_delete', $entity, 'commerce_pricelist_item');
          field_attach_delete('commerce_pricelist_item', $entity);
          $entity_ids[] = $entity->item_id;
        }
        db_delete('commerce_pricelist_item')
          ->condition('item_id', $entity_ids, 'IN')
          ->execute();
      }
      catch (Exception $e) {
        $transaction->rollback();
        watchdog_exception('commerce_pricelist', $e);
        throw $e;
      }
    }
  }
}