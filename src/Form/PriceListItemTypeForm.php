<?php

namespace Drupal\commerce_pricelist\Form;

use Drupal\commerce\EntityHelper;
use Drupal\commerce\Form\CommerceBundleEntityFormBase;
use Drupal\commerce\PurchasableEntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeInterface;

class PriceListItemTypeForm extends CommerceBundleEntityFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $order_item_type = $this->entity;
    // Prepare the list of purchasable entity types.
    $entity_types = $this->entityTypeManager->getDefinitions();
    $purchasable_entity_types = array_filter($entity_types, function (EntityTypeInterface $entity_type) {
      return $entity_type->entityClassImplements(PurchasableEntityInterface::class);
    });
    $purchasable_entity_types = array_map(function (EntityTypeInterface $entity_type) {
      return $entity_type->getLabel();
    }, $purchasable_entity_types);
    $order_types = $this->entityTypeManager->getStorage('price_list_type')->loadMultiple();

    $form['#tree'] = TRUE;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $order_item_type->label(),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $order_item_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\commerce_pricelist\Entity\PriceListItemType::load',
        'source' => ['label'],
      ],
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
    ];
    $form['purchasableEntityType'] = [
      '#type' => 'select',
      '#title' => $this->t('Purchasable entity type'),
      '#default_value' => $order_item_type->getPurchasableEntityTypeId(),
      '#options' => $purchasable_entity_types,
      '#empty_value' => '',
      '#disabled' => !$order_item_type->isNew(),
    ];
    $form['priceListType'] = [
      '#type' => 'select',
      '#title' => $this->t('Price list type'),
      '#default_value' => $order_item_type->getPriceListTypeId(),
      '#options' => EntityHelper::extractLabels($order_types),
      '#required' => TRUE,
    ];
    $form = $this->buildTraitForm($form, $form_state);

    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $this->validateTraitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $this->entity->save();
    $this->submitTraitForm($form, $form_state);

    drupal_set_message($this->t('Saved the %label price list item type.', [
      '%label' => $this->entity->label(),
    ]));
    $form_state->setRedirect('entity.price_list_item_type.collection');
  }

}
