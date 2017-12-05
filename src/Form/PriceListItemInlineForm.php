<?php

namespace Drupal\commerce_pricelist\Form;

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
      'singular' => t('price_list'),
      'plural' => t('price_lists'),
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
      case 'entity.commerce_product.add_form':unset($entity_form['product_variation_id']);break;
      case 'entity.commerce_product.edit_form':unset($entity_form['product_variation_id']);break;
      case 'entity.commerce_product_variation.add_form':unset($entity_form['product_variation_id']);break;
      case 'entity.commerce_product_variation.edit_form':unset($entity_form['product_variation_id']);break;
    }

    return $entity_form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(EntityInterface $entity)
  {
    $productVariation = $entity->getProductVariation();
    $priceList = $entity->getPriceList();

    // set name if name is null
    if ($productVariation && !$entity->getName()) {
      $entity->setName($productVariation->getTitle());
    }

    // set quantity if quantity is null
    if (!$entity->getQuantity()) {
      $entity->setQuantity(1);
    }

    // set price if price is null
    if ($productVariation && !$entity->getPrice()) {
      $entity->setPrice($productVariation->getPrice());
    }

    $isNew = $entity->isNew();
    $entity->save();
    $entity_id = $entity->id();

    if ($productVariation && $isNew) {
      $productVariation->field_price_list_item[] = ['target_id' => $entity_id];
      $productVariation->save();
    }

    if ($priceList && $isNew) {
      $priceList->field_price_list_item[] = ['target_id' => $entity_id];
      $priceList->save();
    }

  }

  /**
   * {@inheritdoc}
   */
  public function getEntityLabel(EntityInterface $entity) {
    return $entity->label();
  }

}
