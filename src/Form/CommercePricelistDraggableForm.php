<?php

/**
 * @file
 * Contains \Drupal\commerce_pricelist\Form\CommercePricelistDraggableForm.
 */

namespace Drupal\commerce_pricelist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class CommercePricelistDraggableForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_pricelist_draggable_form';
  }

  public function buildForm(array $form_state, \Drupal\Core\Form\FormStateInterface $form_state) {

    $content = [];

    // Load all of our entities ordered by weight.
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'commerce_pricelist_list')
      ->propertyOrderBy('weight');

    $result = $query->execute();

    if (!empty($result['commerce_pricelist_list'])) {
      $entities = commerce_pricelist_list_load_multiple(array_keys($result['commerce_pricelist_list']));
    }

    if (!empty($entities)) {

      // Identify that the elements in 'pricelists' are a collection, to
    // prevent Form API from flattening the array when submitted.
      $form['pricelists']['#tree'] = TRUE;

      // Iterate through each database result.
      foreach ($entities as $item) {

        $query = new EntityFieldQuery();

        $count = $query->entityCondition('entity_type', 'commerce_pricelist_item')
          ->propertyCondition('pricelist_id', $item->list_id)
          ->count()
          ->execute();

        // Let modules implementing commerce_pricelists_list_info_alter()
        // modify list item.

        $info = [];
        \Drupal::moduleHandler()->alter('commerce_pricelists_list_info', $info, $item);

        // Create a form entry for this item.
        //
        // Each entry will be an array using the the unique id for that item as
        // the array key, and an array of table row data as the value.
        // @FIXME
        // l() expects a Url object, created from a route name or external URI.
        // $form['pricelists'][$item->list_id] = array(
        // 
        //         // We'll use a form element of type '#markup' to display the item name.
        //         'title' => array(
        //           '#markup' => check_plain($item->title),
        //         ),
        // 
        //         'status' => array(
        //           '#type' => 'checkbox',
        //           '#title' => t('Active'),
        //           '#default_value' => $item->status,
        //         ),
        // 
        //         // The 'weight' field will be manipulated as we move the items around in
        //         // the table using the tabledrag activity.  We use the 'weight' element
        //         // defined in Drupal's Form API.
        //         'weight' => array(
        //           '#type' => 'weight',
        //           '#title' => t('Weight'),
        //           '#default_value' => $item->weight,
        //           '#delta' => 10,
        //           '#title_display' => 'invisible',
        //         ),
        // 
        //         'data' => array(
        //           '#markup' => implode(' ', $info),
        //         ),
        // 
        //         'rows' => array(
        //           '#markup' => $count,
        //         ),
        // 
        //         'view' => array(
        //           '#markup' => l(t('View'), 'admin/commerce/pricelist/commerce_pricelist_list/' . $item->list_id),
        //         ),
        // 
        //         'add' => array(
        //           '#markup' => l(t('Add price'), 'admin/commerce/pricelist/commerce_pricelist_list/' . $item->list_id . '/add',
        //             array('query' => array('destination' => current_path()))),
        //         ),
        // 
        //         'edit' => array(
        //           '#markup' => l(t('Edit'), 'admin/commerce/pricelist/commerce_pricelist_list/' . $item->list_id . '/edit'),
        //         ),
        //       );

      }

      // Now we add our submit button, for submitting the form results.
      //
      // The 'actions' wrapper used here isn't strictly necessary for tabledrag,
      // but is included as a Form API recommended practice.
      $form['actions'] = [
        '#type' => 'actions'
        ];
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => t('Save Changes'),
      ];

      return $form;
    }
    else {
      // There were no entities. Tell the user.
      $content[] = [
        '#type' => 'item',
        '#markup' => t('No price lists currently exist.'),
      ];
    }
    return $content;
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface &$form_state) {
    // Because the form elements were keyed with the item ids from the database,
  // we can simply iterate through the submitted values.
    foreach ($form_state->getValue(['pricelists']) as $id => $item) {
      db_query("UPDATE {commerce_pricelist_list} SET weight = :weight, status = :status WHERE list_id = :id", [
        ':weight' => $item['weight'],
        ':status' => $item['status'],
        ':id' => $id,
      ]);
    }
  }

}
