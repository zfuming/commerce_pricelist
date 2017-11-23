<?php

namespace Drupal\commerce_pricelist;

use Drupal\Core\Url;
use Drupal\Core\Link;
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
    $query->sort('created', 'DESC');
    $query->pager(50);
    $entity_ids = $query->execute();
    $items = PriceListItem::loadMultiple($entity_ids);
    $header = array(
      t('Id'),
      t('Name'),
      t('Product'),
      t('SKU'),
      t('Price'),
      t('Quantity'),
      t('Created'),
      t('Operations')
    );
    $build['table'] = array(
      '#type' => 'table',
      '#header' => $header,
      '#title' => 'item list',
      '#rows' => $this->buildItemRows($items),
      '#empty' => $this->t('There is no item yet.'),
    );
    $build['pager'] = array(
      '#type' => 'pager',
    );
  }

  /**
   * @param $items
   * @return array
   */
  public function buildItemRows($items) {
    $rows = [];
    foreach ($items as $key => $item) {
      $product_url = Url::fromRoute('entity.commerce_product.canonical', array('commerce_product' => $item->getProductVariation()->id()));
      $product = Link::fromTextAndUrl($item->getProductVariation()->label(), $product_url);
      $rows[$key]['id']       = $item->id();
      $rows[$key]['name']     = $item->getName();
      $rows[$key]['product']  = $product;
      $rows[$key]['sku']      = $item->getProductVariation()->getSku();
      $rows[$key]['price']    = $item->getPrice();
      $rows[$key]['quantity'] = $item->getQuantity();
      $rows[$key]['created']  = \Drupal::service('date.formatter')->format($item->getCreatedTime(), 'date_text');
      $editLink = $item->toUrl('edit-form');
      $deleteLink = $item->toUrl('delete-form');
      $operations = array(
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
      $rows[$key]['operations'] = render($operations);
    }

    return $rows;
  }

}
