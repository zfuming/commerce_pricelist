<?php
/**
 * Created by IntelliJ IDEA.
 * User: zfm
 * Date: 2017/11/17
 * Time: 下午5:15
 */

namespace Drupal\commerce_pricelist\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;

class PriceListItemAddForm extends FormBase implements ContainerInjectionInterface
{

  /**
   * The price list item storage.
   *
   * @var \Drupal\Core\Entity\Sql\SqlContentEntityStorage
   */
  protected $priceListItemStorage;

  /**
   * The current order.
   *
   * @var \Drupal\commerce_pricelist\Entity\PriceListInterface
   */
  protected $priceList;

  /**
   * Constructs a new PriceListItemAddForm instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, RouteMatchInterface $route_match) {
    $this->priceListItemStorage = $entity_type_manager->getStorage('price_list_item');
    $this->priceList = $route_match->getParameter('price_list_id');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_price_list_item_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => '',
      '#size' => 60,
      '#maxlength' => 128,
      '#required' => TRUE,
    );

    $form['quantity'] = array(
      '#type' => 'number',
      '#title' => $this->t('Quantity'),
    );

    $form['product'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Product'),
      '#target_type' => 'commerce_product',
      '#required' => TRUE,
    ];

    $form['price'] = [
      '#type' => 'commerce_price',
      '#title' => $this->t('price'),
      '#required' => TRUE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    $item_data = [
      'price_list_id' => $this->priceList,
      'name' => $values['name'],
      'quantity' => $values['quantity'],
      'product_variation_id' => $values['product'],
      'price' => $values['price'],
    ];
    $priceListItem = $this->priceListItemStorage->create($item_data);
    $priceListItem->save();
    // Redirect to the price list item collection.
    $form_state->setRedirect('view.price_list_item.collection', ['price_list_id' => $this->priceList]);
  }

}
