<?php

namespace Drupal\commerce_pricelist\Form;

use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\inline_entity_form\Form\EntityInlineForm;
use Drupal\views\Plugin\views\row\EntityReference;

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
    $fields['name']['name'] = t('Name');
    $fields['price'] = [
      'type' => 'field',
      'label' => t('Price'),
      'weight' => 10,
    ];
    $fields['quantity'] = [
      'type' => 'field',
      'label' => t('Quantity'),
      'weight' => 100,
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
    $entity->save();
    $entity_id = $entity->id();
    $productVariation = $entity->getProductVariation();
    $priceList = $entity->getPriceList();

    if ($productVariation) {
      $productVariation->field_price_list_item[] = ['target_id' => $entity_id];
      $productVariation->save();
    }

    if ($priceList) {
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
