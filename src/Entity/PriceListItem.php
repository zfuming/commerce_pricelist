<?php

namespace Drupal\commerce_pricelist\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Price list item entity.
 *
 * @ingroup commerce_pricelist
 *
 * @ContentEntityType(
 *   id = "price_list_item",
 *   label = @Translation("Price list item"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\commerce_pricelist\PriceListItemListBuilder",
 *     "views_data" = "Drupal\commerce_pricelist\Entity\PriceListItemViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\commerce_pricelist\Form\PriceListItemForm",
 *       "add" = "Drupal\commerce_pricelist\Form\PriceListItemForm",
 *       "edit" = "Drupal\commerce_pricelist\Form\PriceListItemForm",
 *       "delete" = "Drupal\commerce_pricelist\Form\PriceListItemDeleteForm",
 *     },
 *     "access" = "Drupal\commerce_pricelist\PriceListItemAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\commerce_pricelist\PriceListItemHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "price_list_item",
 *   admin_permission = "administer price list item entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/admin/commerce/config/price_list_item/{price_list_item}",
 *     "add-form" = "/admin/commerce/config/price_list_item/add",
 *     "edit-form" = "/admin/commerce/config/price_list_item/{price_list_item}/edit",
 *     "delete-form" = "/admin/commerce/config/price_list_item/{price_list_item}/delete",
 *     "collection" = "/admin/commerce/config/price_list_item",
 *   },
 *   field_ui_base_route = "price_list_item.settings"
 * )
 */
class PriceListItem extends ContentEntityBase implements PriceListItemInterface {
  use EntityChangedTrait;
  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getQuantity() {
    return $this->get('quantity')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setQuantity($name) {
    $this->set('quantity', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public function getPrice() {
    return $this->get('price')->first()->toPrice();
  }


  /**
   * {@inheritdoc}
   */
  public function getProductVariation() {
    return $this->get('product_variation_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getProductVariationId() {
    return $this->get('product_variation_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? NODE_PUBLISHED : NODE_NOT_PUBLISHED);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Price list item entity.'))
      ->setReadOnly(TRUE);
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Price list item entity.'))
      ->setReadOnly(TRUE);

    $fields['price_list_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Price list'))
      ->setDescription(t('The parent price list.'))
      ->setSetting('target_type', 'price_list')
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -1,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['product_variation_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Product'))
      ->setDescription(t('The parent product.'))
      ->setSetting('target_type', 'commerce_product_variation')
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -1,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('Optional label for this price list item.'))
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setRequired(TRUE)
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['quantity'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Quantity'))
      ->setDescription(t('Optional label for this price list item.'))
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setRequired(TRUE)
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'integer',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'integer',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['price'] = BaseFieldDefinition::create('commerce_price')
      ->setLabel(t('Price'))
      ->setDescription(t('The list price'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'commerce_price_default',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'commerce_price_default',
        'weight' => 0,
      ])
      ->setRequired(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
