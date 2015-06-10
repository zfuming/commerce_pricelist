<?php

/**
 * @file
 * Contains \Drupal\commerce_pricelist\Form\CommercePricelistItemForm.
 */

namespace Drupal\commerce_pricelist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class CommercePricelistItemForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_pricelist_item_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface &$form_state, $pricelist_item = NULL, $pricelist = NULL) {

    if (!$pricelist_item) {
      // This is a new list item being created.
      $pricelist_item = commerce_pricelist_item_new($pricelist->list_id);
    }

    if ($pricelist) {
      // This item is being created from the pricelist > add price tab.
      $pricelist_item->pricelist_id = $pricelist->list_id;
    }

    if ($pricelist_item->pricelist_id) {
      $form['pricelist_id'] = [
        '#type' => 'value',
        '#value' => $pricelist_item->pricelist_id,
      ];
    }
    else {
      $pricelists = commerce_pricelist_list_load_multiple();
      $currencies = [];
      foreach ($pricelists as $pricelist) {
        $currencies[$pricelist->list_id] = $pricelist->title;
      }
      $form['pricelist_id'] = [
        '#type' => 'select',
        '#options' => $currencies,
        '#required' => TRUE,
        '#title' => t('Price list'),
        '#element_validate' => [
          '_commerce_pricelist_validate_pricelist'
          ],
      ];
    }

    $form_fields = ['sku', 'valid_from', 'valid_to', 'quantity'];

    foreach ($form_fields as $field) {
      $form[$field] = [
        '#type' => 'textfield',
        '#title' => ucwords(str_replace('_', ' ', $field)),
        '#required' => TRUE,
        '#default_value' => isset($pricelist_item->$field) ? $pricelist_item->$field : '',
      ];
    }

    $form['price'] = [
      '#title' => t('Price'),
      '#tree' => TRUE,
      '#type' => 'item',
      '#element_validate' => [
        'commerce_price_field_widget_validate'
        ],
    ];

    // If a price has already been set for this item prepare default values.
    if (isset($pricelist_item->price_amount)) {
      $currency = commerce_currency_load($pricelist_item->currency_code);

      // Convert the price amount to a user friendly decimal value.
      $default_amount = commerce_currency_amount_to_decimal($pricelist_item->price_amount, $currency['code']);

      // Run it through number_format() to ensure it has the proper number of
      // decimal places.
      $default_amount = number_format($default_amount, $currency['decimals'], '.', '');
    }
    else {
      $default_amount = NULL;
    }

    $form['price']['amount'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#size' => 10,
      '#default_value' => $default_amount,
      '#prefix' => '<div class="container-inline">',
    ];

    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $form['price']['currency_code'] = array(
    //     '#type' => 'select',
    //     '#title' => '',
    //     '#options' => array_filter(variable_get('commerce_enabled_currencies', array('USD' => 'USD'))),
    //     '#default_value' => isset($pricelist_item->$field) ? $pricelist_item->$field : commerce_default_currency(),
    //     '#suffix' => '</div>'
    //   );


    foreach (['valid_from', 'valid_to'] as $date) {
      $default_date = isset($pricelist_item->{$date}) ? $pricelist_item->{$date} : 0;
      $form[$date]['#required'] = FALSE;
      $form[$date]['#element_validate'] = ['_commerce_pricelist_date_validate'];
      $form[$date]['#description'] = t('Format: %time. The date format is YYYY-MM-DD and %timezone is the time zone offset from UTC.', [
        '%time' => date('Y-m-d H:i:s O', $default_date),
        '%timezone' => date('O', $default_date),
      ]);
      $form[$date]['#default_value'] = _commerce_pricelist_display_date($default_date, 'Y-m-d H:i:s O');
    }

    $form['quantity']['#element_validate'] = ['element_validate_number'];

    $form['entity'] = [
      '#type' => 'value',
      '#value' => $pricelist_item,
    ];

    $form['actions'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'form-actions',
          'container-inline',
        ]
        ],
      '#weight' => 100,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
    ];

    // Check if this is an existing item, add delete button
    if ($pricelist_item->item_id) {
      $form['actions']['delete'] = [
        '#type' => 'submit',
        '#value' => t('Delete'),
        '#submit' => [
          'commerce_pricelist_item_edit_delete'
          ],
        '#weight' => 200,
      ];
    }

    return $form;
  }

  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface &$form_state) {
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface &$form_state) {
    $entity = $form_state->getValue(['entity']);

    // Convert to timestamp.
    foreach (['valid_from', 'valid_to'] as $date_field) {
      $timestamp = strtotime($form_state->getValue([$date_field]));
      $form_state->setValue([$date_field], $timestamp !== FALSE ? $timestamp : '');
    }

    // Need to shuffle values around because we wrap them and use
    // commerce_price_field_widget_validate().
    $price = $form_state->getValue(['price']);
    unset($form_state->getValue(['price']));
    $form_state->setValue(['price_amount'], $price['amount']);
    $form_state->setValue(['currency_code'], $price['currency_code']);

    foreach ($form_state->getValues() as $key => $value) {
      $entity->$key = $value;
    }
    field_attach_submit('commerce_pricelist_item', $entity, $form, $form_state);
    $entity = commerce_pricelist_item_save($entity);
    if ($entity) {
      drupal_set_message(t('Price list item saved'));
    }
  }

}
