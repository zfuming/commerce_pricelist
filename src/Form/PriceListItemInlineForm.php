<?php

namespace Drupal\commerce_pricelist\Form;

use Drupal\commerce_pricelist\Entity\PriceListItemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\inline_entity_form\Form\EntityInlineForm;

/**
 * Defines the inline form for product variations.
 */
class PriceListItemInlineForm extends EntityInlineForm {

  /**
   * The loaded variation types.
   *
   * @var \Drupal\commerce_pricelist\Entity\PriceListItemInterface[]
   */
  protected $variationTypes;

  /**
   * {@inheritdoc}
   */
  public function getEntityTypeLabels() {
    $labels = [
      'singular' => t('price list item'),
      'plural' => t('price list items'),
    ];
    return $labels;
  }

  /**
   * {@inheritdoc}
   */
  public function getTableFields($bundles) {
    $fields = parent::getTableFields($bundles);
    $fields['price_list_id'] = [
      'type' => 'field',
      'label' => t('Price List'),
      'weight' => 2,
    ];
    $fields['price'] = [
      'type' => 'field',
      'label' => t('Price'),
      'weight' => 3,
    ];
    $fields['quantity'] = [
      'type' => 'field',
      'label' => t('Quantity'),
      'weight' => 4,
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function entityForm(array $entity_form, FormStateInterface $form_state) {
    $entity_form = parent::entityForm($entity_form, $form_state);
    $routeName = \Drupal::routeMatch()->getRouteName();
    switch ($routeName) {
      case 'entity.price_list.add_page':unset($entity_form['price_list_id']);break;
      case 'entity.price_list.edit_form':unset($entity_form['price_list_id']);break;
      case 'entity.commerce_product.add_form':unset($entity_form['purchased_entity']);break;
      case 'entity.commerce_product.edit_form':unset($entity_form['purchased_entity']);break;
      case 'entity.commerce_product_bundle.add_form':unset($entity_form['purchased_entity']);break;
      case 'entity.commerce_product_bundle.edit_form':unset($entity_form['purchased_entity']);break;
      case 'entity.commerce_product_variation.add_form':unset($entity_form['purchased_entity']);break;
      case 'entity.commerce_product_variation.edit_form':unset($entity_form['purchased_entity']);break;
    }

    return $entity_form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(EntityInterface $entity)
  {
    $product   = $entity->getPurchasedEntity();
    $priceList = $entity->getPriceList();

    // set name if name is null
    if ($product && !$entity->getName()) {
      $entity->setName($product->getTitle());
    }

    // set quantity if quantity is null
    if (!$entity->getQuantity()) {
      $entity->setQuantity(1);
    }

    // set price if price is null
    if ($product && !$entity->getPrice()) {
      $entity->setPrice($product->getPrice());
    }

    $entity->save();
    $entity_id = $entity->id();

    if ($product) {
      $target_id = [];
      $field_price_list_item = $product->field_price_list_item->getValue();
      foreach ($field_price_list_item as $item) {
        $target_id[] = $item['target_id'];
      }
      if (!in_array($entity_id, $target_id)) {
        $product->field_price_list_item[] = ['target_id' => $entity_id];
        $product->save();
      }
    }

    if ($priceList) {
      $target_id = [];
      $field_price_list_item = $priceList->field_price_list_item->getValue();
      foreach ($field_price_list_item as $item) {
        $target_id[] = $item['target_id'];
      }
      if (!in_array($entity_id, $target_id)) {
        $priceList->field_price_list_item[] = ['target_id' => $entity_id];
        $priceList->save();
      }
    }

  }

  /**
   * {@inheritdoc}
   */
  public function getEntityLabel(EntityInterface $entity) {
    return $entity->label();
  }

}
