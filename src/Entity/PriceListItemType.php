<?php

namespace Drupal\commerce_pricelist\Entity;

use Drupal\commerce\Entity\CommerceBundleEntityBase;

/**
 * Defines the price list item type entity class.
 *
 * @ConfigEntityType(
 *   id = "price_list_item_type",
 *   label = @Translation("Price list item type"),
 *   label_collection = @Translation("Price list item types"),
 *   label_singular = @Translation("price list item type"),
 *   label_plural = @Translation("price list item types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count price list item type",
 *     plural = "@count price list item types",
 *   ),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\commerce_pricelist\Form\PriceListItemTypeForm",
 *       "edit" = "Drupal\commerce_pricelist\Form\PriceListItemTypeForm",
 *       "delete" = "Drupal\commerce\Form\CommerceBundleEntityDeleteFormBase"
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\commerce_pricelist\PriceListItemTypeListBuilder",
 *   },
 *   admin_permission = "administer price_list_type",
 *   config_prefix = "price_list_item_type",
 *   bundle_of = "price_list_item",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "label",
 *     "id",
 *     "purchasableEntityType",
 *     "priceListType",
 *     "traits",
 *   },
 *   links = {
 *     "add-form" = "/admin/commerce/config/price-list-item-types/add",
 *     "edit-form" = "/admin/commerce/config/price-list-item-types/{price_list_item_type}/edit",
 *     "delete-form" = "/admin/commerce/config/price-list-item-types/{price_list_item_type}/delete",
 *     "collection" = "/admin/commerce/config/price-list-item-types"
 *   }
 * )
 */
class PriceListItemType extends CommerceBundleEntityBase implements PriceListItemTypeInterface {

  /**
   * The purchasable entity type ID.
   *
   * @var string
   */
  protected $purchasableEntityType;

  /**
   * The order type ID.
   *
   * @var string
   */
  protected $priceListType;

  /**
   * {@inheritdoc}
   */
  public function getPurchasableEntityTypeId() {
    return $this->purchasableEntityType;
  }

  /**
   * {@inheritdoc}
   */
  public function setPurchasableEntityTypeId($purchasable_entity_type_id) {
    $this->purchasableEntityType = $purchasable_entity_type_id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPriceListTypeId() {
    return $this->priceListType;
  }

  /**
   * {@inheritdoc}
   */
  public function setPriceListTypeId($price_list_type_id) {
    $this->priceListType = $price_list_type_id;
    return $this;
  }

}
