<?php

namespace Drupal\commerce_pricelist\Form;

use Drupal\commerce_price\Price;
use Drupal\Component\Utility\NestedArray;
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
    $entity_form['purchased_entity']['widget'][0]['target_id']['#ajax'] = [
      'callback' => [get_class($this), 'purchasedRefresh'],
      'event' => 'autocompleteclose',
      'wrapper' => 'purchased_entity_refresh',
    ];
    $entity_form['price']['#disabled'] = false;
    $target_type = $entity_form['purchased_entity']['widget'][0]['target_id']['#target_type'];
    $values = $form_state->getValues();
    $target_id = null;
    $inline_entity_form = $values['field_price_list_item']['form']['inline_entity_form'];
    if ($inline_entity_form) $target_id = $inline_entity_form['purchased_entity'][0]['target_id'];
    if ($target_id) {
      $entity_storage = \Drupal::entityManager()->getStorage($target_type);
      $purchased_entity = $entity_storage->load($target_id);
      if ($target_type == 'commerce_product_bundle') {
        $price = $purchased_entity->get('bundle_price')->getValue()[0]['number'];
      } else {
        $price = $purchased_entity->get('price')->getValue()[0]['number'];
      }
      if (!$price) {
        $entity_form['price']['#disabled'] = true;
      }
    }
    $entity_form['price']['#attributes']['id'] = 'purchased_entity_refresh';
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

  public function purchasedRefresh(array $form, FormStateInterface $form_state)
  {
    $element = [];
    $triggering_element = $form_state->getTriggeringElement();

    // Remove the action and the actions container.
    $array_parents = array_slice($triggering_element['#array_parents'], 0, -2);
    while (!(isset($element['#type']) && ($element['#type'] == 'inline_entity_form'))) {
      $element = NestedArray::getValue($form, $array_parents);
      array_pop($array_parents);
    }
    return $element['price'];
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
      if ($product->getPrice()) {
        $entity->setPrice($product->getPrice());
      }
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
