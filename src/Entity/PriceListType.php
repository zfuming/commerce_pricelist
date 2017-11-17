<?php

namespace Drupal\commerce_pricelist\Entity;

use Drupal\commerce\Entity\CommerceBundleEntityBase;

/**
 * Defines the Price list type entity.
 *
 * @ConfigEntityType(
 *   id = "price_list_type",
 *   label = @Translation("Price list type"),
 *   handlers = {
 *     "list_builder" = "Drupal\commerce_pricelist\PriceListTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\commerce_pricelist\Form\PriceListTypeForm",
 *       "edit" = "Drupal\commerce_pricelist\Form\PriceListTypeForm",
 *       "delete" = "Drupal\commerce_pricelist\Form\PriceListTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\commerce_pricelist\PriceListTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "price_list_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "price_list",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/commerce/config/price_list_type/{price_list_type}",
 *     "add-form" = "/admin/commerce/config/price_list_type/add",
 *     "edit-form" = "/admin/commerce/config/price_list_type/{price_list_type}/edit",
 *     "delete-form" = "/admin/commerce/config/price_list_type/{price_list_type}/delete",
 *     "collection" = "/admin/commerce/config/price_list_types"
 *   }
 * )
 */
class PriceListType extends CommerceBundleEntityBase implements PriceListTypeInterface {
  /**
   * A brief description of this store type.
   *
   * @var string
   */
  protected $description;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

}
