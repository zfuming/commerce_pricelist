<?php

namespace Drupal\commerce_pricelist;

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\commerce_pricelist\Entity\PriceListItem;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the entity view builder for products.
 */
class PriceListViewBuilder extends EntityViewBuilder {

  /**
   * Drupal\Core\Entity\Query\QueryFactory definition.
   *
   */
  protected $entityQuery;

  /**
   * Constructs a new PriceListViewBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityManagerInterface $entity_manager, LanguageManagerInterface $language_manager, QueryFactory $entityQuery) {
    parent::__construct($entity_type, $entity_manager, $language_manager);
    $this->entityQuery = $entityQuery;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager'),
      $container->get('language_manager'),
      $container->get('entity.query')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function alterBuild(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display, $view_mode) {
    $query = $this->entityQuery->get('price_list_item');
    $query->condition('price_list_id', $entity->id());
    $entity_ids = $query->execute();
    $items = PriceListItem::loadMultiple($entity_ids);
    $build['#theme'] = 'table';
    $build['#header'] = array(t('ID'), t('Price'), t('Product'), t('Name'), t('Quantity'), t('opt'));
    $build['#rows'] = array();
    foreach ($items as $key => $item) {
      $editLink = $item->toUrl('edit-form');
      $deleteLink = $item->toUrl('delete-form');
      $build['#rows'][$key]['id'] = $item->id();
      $build['#rows'][$key]['price'] = $item->getPrice();
      $build['#rows'][$key]['product'] = $item->getProductVariation()->label();
      $build['#rows'][$key]['name'] = $item->getName();
      $build['#rows'][$key]['quantity'] = $item->getQuantity();
      $opt = array(
        '#type'  => 'dropbutton',
        '#links' => array(
          'edit' => array(
            'title' => $this->t('Edit'),
            'url' => new Url(
              $editLink->getRouteName(), $editLink->getRouteParameters()
            ),
          ),
          'delete' => array(
            'title' => $this->t('Delete'),
            'url' =>  new Url(
              $deleteLink->getRouteName(), $deleteLink->getRouteParameters()
            )
          ),
        ),
      );
      $build['#rows'][$key]['opt'] = render($opt);
    }
  }

}
