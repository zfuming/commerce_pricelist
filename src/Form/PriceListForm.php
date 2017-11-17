<?php

namespace Drupal\commerce_pricelist\Form;

use Drupal\Core\Link;
use Drupal\Core\Render\Element;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Price list edit forms.
 *
 * @ingroup commerce_pricelist
 */
class PriceListForm extends ContentEntityForm {
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\commerce_pricelist\Entity\PriceList */
    $store_query = $this->entityManager->getStorage('commerce_store')->getQuery();
    if ($store_query->count()->execute() == 0) {
      $link = Link::createFromRoute('Add a new store.', 'entity.commerce_store.add_page');
      $form['warning'] = [
        '#markup' => t("Products can't be created until a store has been added. @link", ['@link' => $link->toString()]),
      ];
      return $form;
    }

    return parent::buildForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\commerce_pricelist\Entity\PriceList $priceList */
    $priceList = $this->entity;
    $form = parent::form($form, $form_state);

    $form['#tree'] = TRUE;
    $form['#theme'] = ['commerce_product_form'];
    $form['#attached']['library'][] = 'commerce_product/form';

    $form['changed'] = [
      '#type' => 'hidden',
      '#default_value' => $priceList->getChangedTime(),
    ];

    $form['footer'] = [
      '#type' => 'container',
      '#weight' => 99,
      '#attributes' => [
        'class' => ['product-form-footer'],
      ],
    ];

    $form['status']['#group'] = 'footer';

    $last_saved = t('Not saved yet');

    $form['meta'] = [
      '#attributes' => ['class' => ['entity-meta__header']],
      '#type' => 'container',
      '#group' => 'advanced',
      '#weight' => -100,
      'published' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#value' => $priceList->isPublished() ? $this->t('Published') : $this->t('Not published'),
        '#access' => !$priceList->isNew(),
        '#attributes' => [
          'class' => ['entity-meta__title'],
        ],
      ],
      'changed' => [
        '#type' => 'item',
        '#wrapper_attributes' => [
          'class' => ['entity-meta__last-saved', 'container-inline'],
        ],
        '#markup' => '<h4 class="label inline">' . $this->t('Last saved') . '</h4> ' . $last_saved,
      ],
      'author' => [
        '#type' => 'item',
        '#wrapper_attributes' => [
          'class' => ['author', 'container-inline'],
        ],
        '#markup' => '<h4 class="label inline">' . $this->t('Author') . '</h4> ' . $priceList->getOwner()->getDisplayName(),
      ],
    ];

    $form['advanced'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['entity-meta']],
      '#weight' => 99,
    ];

    $form['price_list_store'] = [
      '#type' => 'details',
      '#title' => t('Price List Store'),
      '#open' => TRUE,
      '#group' => 'advanced',
      '#access' => !empty($form['stores']['#access']),
      '#attributes' => [
        'class' => ['product-visibility-settings'],
      ],
      '#weight' => 30,
    ];

    if (isset($form['stores'])) {
      $form['stores']['#group'] = 'price_list_store';
      $form['#after_build'][] = [get_class($this), 'hideEmptyVisibilitySettings'];
    }

    return $form;
  }

  /**
   * Hides the visibility settings if the stores widget is a hidden element.
   *
   * @param array $form
   *   The form.
   *
   * @return array
   *   The modified visibility_settings element.
   */
  public static function hideEmptyVisibilitySettings(array $form) {
    if (isset($form['stores']['widget']['target_id'])) {
      $stores_element = $form['stores']['widget']['target_id'];
      if (!Element::getVisibleChildren($stores_element)) {
        $form['price_list_store']['#printed'] = TRUE;
        // Move the stores widget out of the visibility_settings group to
        // ensure that its hidden element is still present in the HTML.
        unset($form['stores']['#group']);
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Price list.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Price list.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.price_list.collection');
  }

}
