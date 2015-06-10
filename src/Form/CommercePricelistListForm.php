<?php

/**
 * @file
 * Contains \Drupal\commerce_pricelist\Form\CommercePricelistListForm.
 */

namespace Drupal\commerce_pricelist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class CommercePricelistListForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_pricelist_list_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface &$form_state, $entity = NULL) {
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => t('Title'),
      '#required' => TRUE,
      '#default_value' => $entity->title,
    ];

    $form['weight'] = [
      '#type' => 'weight',
      '#title' => t('Weight'),
      '#required' => TRUE,
      '#default_value' => $entity->weight,
    ];

    $form['active'] = [
      '#type' => 'checkbox',
      '#title' => t('Active'),
      '#default_value' => $entity->status,
    ];

    $form['entity'] = [
      '#type' => 'value',
      '#value' => $entity,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
      '#weight' => 100,
    ];

    // Check if this is an existing item, add delete button
    if ($entity->list_id) {
      $form['delete'] = [
        '#type' => 'submit',
        '#value' => t('Delete'),
        '#submit' => [
          'commerce_pricelist_list_edit_delete'
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
    $entity->title = check_plain($form_state->getValue(['title']));
    $entity->weight = $form_state->getValue(['weight']);
    $entity->status = $form_state->getValue(['active']);
    field_attach_submit('commerce_pricelist_list', $entity, $form, $form_state);
    $result = commerce_pricelist_list_save($entity);
    if ($result != FALSE) {
      drupal_set_message(t('Price list saved'));
    }
    $form_state->set(['redirect'], 'admin/commerce/pricelist/commerce_pricelist_list/' . $entity->list_id . '/edit');
  }

}
