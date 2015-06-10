<?php /**
 * @file
 * Contains \Drupal\commerce_pricelist\Controller\DefaultController.
 */

namespace Drupal\commerce_pricelist\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Default controller for the commerce_pricelist module.
 */
class DefaultController extends ControllerBase {

  public function commerce_pricelist_list_add() {
    $entity = commerce_pricelist_list_new();
    return \Drupal::formBuilder()->getForm('commerce_pricelist_list_form', $entity);
  }

  public function commerce_pricelist_item_add($product = NULL) {
    // Create a basic entity structure to be used and passed to the validation
  // and submission functions.
    $sku = NULL;
    if ($product != NULL) {
      $sku = $product->sku;
    }
    $entity = commerce_pricelist_item_new(NULL, $sku);
    return \Drupal::formBuilder()->getForm('commerce_pricelist_item_form', $entity);
  }

  public function commerce_pricelist_list_title($entity) {
    return t('@item_title', ['@item_title' => $entity->title]);
  }

  public function commerce_pricelist_list_view($entity, $view_mode = 'full') {
    // Our entity type, for convenience.
    $entity_type = 'commerce_pricelist_list';
    // Start setting up the content.
    $entity->content = [
      '#view_mode' => $view_mode
      ];

    // We call entity_prepare_view() so it can invoke hook_entity_prepare_view()
    // for us.
    entity_prepare_view($entity_type, [
      $entity->list_id => $entity
      ]);

    $pricelist_items = commerce_pricelist_item_list_entities($entity->list_id);

    $entity->content['pricelist_items'] = $pricelist_items;


    // Now to invoke some hooks. We need the language code for
    // hook_entity_view(), so let's get that.
    $language = \Drupal::languageManager()->getCurrentLanguage();
    $langcode = $language->language;
    // And now invoke hook_entity_view().
    \Drupal::moduleHandler()->invokeAll('entity_view', [
      $entity,
      $entity_type,
      $view_mode,
      $langcode,
    ]);
    // Now invoke hook_entity_view_alter().
    \Drupal::moduleHandler()->alter([
      'commerce_pricelist_list_view',
      'entity_view',
    ], $entity->content, $entity_type);

    // And finally return the content.
    return $entity->content;
  }

}
