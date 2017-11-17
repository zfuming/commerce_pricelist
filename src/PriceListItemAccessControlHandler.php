<?php

namespace Drupal\commerce_pricelist;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Price list item entity.
 *
 * @see \Drupal\commerce_pricelist\Entity\PriceListItem.
 */
class PriceListItemAccessControlHandler extends EntityAccessControlHandler {
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\commerce_pricelist\Entity\PriceListItemInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished price list item entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published price list item entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit price list item entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete price list item entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add price list item entities');
  }

}
