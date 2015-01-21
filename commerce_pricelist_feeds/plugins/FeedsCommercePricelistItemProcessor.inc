<?php

/**
 * @file
 * FeedsUserProcessor class.
 */

/**
 * Feeds processor plugin. Create users from feed items.
 */
class FeedsCommercePricelistItemProcessor extends FeedsProcessor {

  /**
   * Define entity type.
   */
  public function entityType() {
    return 'commerce_pricelist_item';
  }

  /**
   * Implements parent::entityInfo().
   */
  protected function entityInfo() {
    $info = parent::entityInfo();
    $info['label plural'] = t('Commerce pricelist_items');
    return $info;
  }

  /**
   * Creates a new pricelist_item and returns it.
   */
  protected function newEntity(FeedsSource $source) {
    $list_uuid = $source->config['FeedsCommercePricelistItemProcessor']['list_uuid'];
    $pricelist_item = entity_get_controller('commerce_pricelist_item')->create();
    $pricelist_item->pricelist_uuid = $list_uuid;
    $pricelist_item->log = 'Created by FeedsCommercePricelistItemProcessor';
    return $pricelist_item;
  }

  /**
   * Loads an existing pricelist_item.
   */
  protected function entityLoad(FeedsSource $source, $pricelist_item_id) {
    if ($this->config['update_existing'] == FEEDS_UPDATE_EXISTING) {
      $pricelist_item = commerce_pricelist_item_load($pricelist_item_id);
      $pricelist_item->log = 'Updated by FeedsCommercePricelistItemProcessor';
    }
    else {
      $pricelist_item->log = 'Replaced by FeedsCommercePricelistItemProcessor';
    }
    return $pricelist_item;
  }

  /**
   * Save a user account.
   */
  protected function entitySave($entity) {
    commerce_pricelist_item_save($entity);
  }

  /**
   * Delete multiple user accounts.
   */
  protected function entityDeleteMultiple($ids) {
    commerce_pricelist_item_delete_multiple($ids);
  }

  /**
   * Override parent::configDefaults().
   */
  public function configDefaults() {
    return array(
      'is_active' => TRUE,
      'is_attached' => TRUE,
      'list_uuid' => NULL,
      'convert_price' => FALSE,
      'convert_dates' => FALSE,
    ) + parent::configDefaults();
  }

  public function sourceForm($source_config) {
    return array('list_uuid' => NULL);
  }

  public function sourceDefaults() {
    return array(
      'list_uuid' => NULL,
    );
  }

  /**
   * Override parent::configForm().
   */
  public function configForm(&$form_state) {
    $form = parent::configForm($form_state);

    $form['is_active'] = array(
      '#type' => 'radios',
      '#title' => t('Status'),
      '#description' => t('Select whether pricelist_items should be imported active or not.'),
      '#options' => array(0 => t('Inactive'), 1 => t('Active')),
      '#default_value' => $this->config['is_active'],
    );

    $form['is_attached'] = array(
      '#type' => 'radios',
      '#title' => t('Attach to pricelist form'),
      '#description' => t('Should this importer be attached to th pricelist form.'),
      '#options' => array(0 => t('No'), 1 => t('Yes')),
      '#default_value' => $this->config['is_attached'],
    );

    $form['convert_price'] = array(
      '#type' => 'radios',
      '#title' => t('Convert price to minor currency on import'),
      '#description' => t('Should the price be multiplied by 100 or rounded to nearest integer?'),
      '#options' => array(0 => t('No'), 1 => t('Yes')),
      '#default_value' => $this->config['convert_price'],
    );

    $form['convert_dates'] = array(
      '#type' => 'radios',
      '#title' => t('Convert dates to Unix timestamp'),
      '#description' => t('Should we attempt to convert the dates?'),
      '#options' => array(0 => t('No'), 1 => t('Yes')),
      '#default_value' => $this->config['convert_dates'],
    );

    return $form;
  }

  /**
   * Return available mapping targets.
   */
  public function getMappingTargets() {
    $targets = parent::getMappingTargets();
    $targets += array(
      'sku' => array(
        'name' => t('Product SKU'),
        'description' => t('The product identifier.'),
      ),
      'valid_from' => array(
        'name' => t('Valid from'),
        'description' => t('Unix timestamp'),
      ),
      'valid_to' => array(
        'name' => t('Valid to'),
        'description' => t('Unix timestamp'),
      ),
      'price_amount' => array(
        'name' => t('Price amount'),
        'description' => t('In minor currency (€1,00 = 100)'),
      ),
      'currency_code' => array(
        'name' => t('Currency code'),
        'description' => t('USD'),
      ),
      'quantity' => array(
        'name' => t('Quantity'),
        'description' => t('Lower quantity limit for this unit price'),
      ),
      'pricelist_uuid' => array(
        'name' => t('Price list uuid'),
        'description' => t('Generated Unique ID'),
      ),
    );

    // Let other modules expose mapping targets.
    self::loadMappers();
    $entity_type = $this->entityType();
    $bundle = $this->bundle();
    drupal_alter('feeds_processor_targets', $targets, $entity_type, $bundle);

    return $targets;
  }

  /**
   * Get id of an existing feed item term if available.
   */
  protected function existingEntityId(FeedsSource $source, FeedsParserResult $result) {
    if ($pricelist_item_id = parent::existingEntityId($source, $result)) {
      return $pricelist_item_id;
    }

    $item_id = commerce_pricelist_item_get_existing_id($result->current_item['sku'], $result->current_item['valid_from'], $result->current_item['quantity'], $result->current_item['pricelist_uuid']);

    if ($item_id) {
      // Return with the first sku found.
      return $item_id;
    }

    return 0;
  }

  /**
   * @param FeedsSource $source
   * @param FeedsParserResult $result
   * @param null $target_item
   * @return array|null
   */
  protected function map(FeedsSource $source, FeedsParserResult $result, $target_item = NULL) {
    $target_item = parent::map($source, $result, $target_item);
    if ($source->importer->processor->config['convert_price'] == 1) {
      // Convert price to minor currency
      $target_item->price_amount = $target_item->price_amount * 100;
    }
    if ($source->importer->processor->config['convert_dates'] == 1) {
      // convert date strings to unix timestamp
      $target_item->valid_from = strtotime($target_item->valid_from);
      $target_item->valid_to = strtotime($target_item->valid_to);
    }
    return $target_item;
  }

}
