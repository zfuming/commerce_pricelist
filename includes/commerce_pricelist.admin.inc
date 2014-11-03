<?php
/**
* @file
* Summary
*
* Description
*/


/**
 * Basic information for the page.
 */
function commerce_pricelist_list_info_page() {
  $content['preface'] = array(
    '#type' => 'item',
    '#markup' => t('The entity example provides a simple example entity.'),
  );
  if (user_access('administer commerce_pricelist_list entities')) {
    $content['preface']['#markup'] = t('You can administer these and add fields and change the view !link.',
      array('!link' => l(t('here'), 'admin/commerce/pricelist/commerce_pricelist_list/manage'))
    );
  }
  $content['table'] = commerce_pricelist_list_list_entities();

  return $content;
}


/**
 * Returns a render array with all commerce_pricelist_list entities.
 *
 * In this basic example we know that there won't be many entities,
 * so we'll just load them all for display. See pager_example.module
 * to implement a pager. Most implementations would probably do this
 * with the contrib Entity API module, or a view using views module,
 * but we avoid using non-core features in the Examples project.
 *
 * @see pager_example.module
 */
function commerce_pricelist_list_list_entities() {

}


/**
 * Menu callback to display an entity.
 *
 * As we load the entity for display, we're responsible for invoking a number
 * of hooks in their proper order.
 *
 * @see hook_entity_prepare_view()
 * @see hook_entity_view()
 * @see hook_entity_view_alter()
 */
function commerce_pricelist_list_view($entity, $view_mode = 'full') {
  // Our entity type, for convenience.
  $entity_type = 'commerce_pricelist_list';
  // Start setting up the content.
  $entity->content = array(
    '#view_mode' => $view_mode,
  );

  // We call entity_prepare_view() so it can invoke hook_entity_prepare_view()
  // for us.
  entity_prepare_view($entity_type, array($entity->list_id => $entity));

  $pricelist_items = commerce_pricelist_item_list_entities($entity->uuid);

  $entity->content['pricelist_items'] = $pricelist_items;


  // Now to invoke some hooks. We need the language code for
  // hook_entity_view(), so let's get that.
  global $language;
  $langcode = $language->language;
  // And now invoke hook_entity_view().
  module_invoke_all('entity_view', $entity, $entity_type, $view_mode,
    $langcode);
  // Now invoke hook_entity_view_alter().
  drupal_alter(array('commerce_pricelist_list_view', 'entity_view'),
    $entity->content, $entity_type);

  // And finally return the content.
  return $entity->content;
}

/**
 * Form function to create an commerce_pricelist_list entity.
 *
 * The pattern is:
 * - Set up the form for the data that is specific to your
 *   entity: the columns of your base table.
 * - Call on the Field API to pull in the form elements
 *   for fields attached to the entity.
 */
function commerce_pricelist_list_form($form, &$form_state, $entity) {
  $form['title'] = array(
    '#type' => 'textfield',
    '#title' => t('Title'),
    '#required' => TRUE,
    '#default_value' => $entity->title,
  );

  $form['weight'] = array(
    '#type' => 'weight',
    '#title' => t('Weight'),
    '#required' => TRUE,
    '#default_value' => $entity->weight,
  );

  $form['active'] = array(
    '#type' => 'checkbox',
    '#title' => t('Active'),
    '#default_value' => $entity->status,
  );

  $form['entity'] = array(
    '#type' => 'value',
    '#value' => $entity,
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
    '#weight' => 100,
  );

  // Check if this is an existing item, add delete button
  if ($entity->list_id) {
    $form['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Delete'),
      '#submit' => array('commerce_pricelist_list_edit_delete'),
      '#weight' => 200,
    );
  }

  return $form;
}


/**
 * Validation handler for commerce_pricelist_list_add_form form.
 *
 * We pass things straight through to the Field API to handle validation
 * of the attached fields.
 */
function commerce_pricelist_list_form_validate($form, &$form_state) {
}


/**
 * Form submit handler: Submits basic_add_form information.
 */
function commerce_pricelist_list_form_submit($form, &$form_state) {
  $entity = $form_state['values']['entity'];
  $entity->title = check_plain($form_state['values']['title']);
  $entity->weight = $form_state['values']['weight'];
  $entity->status = $form_state['values']['active'];
  field_attach_submit('commerce_pricelist_list', $entity, $form, $form_state);
  $entity = commerce_pricelist_list_save($entity);
  if ($entity) {
    drupal_set_message(t('Price list saved'));
  }
  $form_state['redirect'] = 'admin/commerce/pricelist/commerce_pricelist_list/' . $entity->list_id . '/edit';
}

/**
 * Form deletion handler.
 *
 * @todo: 'Are you sure?' message.
 */
function commerce_pricelist_list_edit_delete($form, &$form_state) {
  $entity = $form_state['values']['entity'];
  commerce_pricelist_list_delete($entity);
  drupal_set_message(t('The entity %item_title (ID %id) has been deleted',
      array('%item_title' => $entity->title, '%id' => $entity->list_id))
  );
  $form_state['redirect'] = 'admin/commerce/pricelist/commerce_pricelist_list';
}


/**
 * Build the tabledrag_draggable_example_form form.
 *
 * @return array
 *   A form array set for theming by theme_commerce_pricelist_draggable_form()
 *
 * @ingroup commerce_pricelist
 */
function commerce_pricelist_draggable_form($form_state) {

  $content = array();

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
        ->propertyCondition('pricelist_uuid', $item->uuid)
        ->count()
        ->execute();

      // Let modules implementing commerce_pricelists_list_info_alter()
      // modify list item.

      $info = array();
      drupal_alter('commerce_pricelists_list_info', $info, $item);

      // Create a form entry for this item.
      //
      // Each entry will be an array using the the unique id for that item as
      // the array key, and an array of table row data as the value.
      $form['pricelists'][$item->list_id] = array(

        // We'll use a form element of type '#markup' to display the item name.
        'title' => array(
          '#markup' => check_plain($item->title),
        ),

        'status' => array(
          '#type' => 'checkbox',
          '#title' => t('Active'),
          '#default_value' => $item->status,
        ),

        // The 'weight' field will be manipulated as we move the items around in
        // the table using the tabledrag activity.  We use the 'weight' element
        // defined in Drupal's Form API.
        'weight' => array(
          '#type' => 'weight',
          '#title' => t('Weight'),
          '#default_value' => $item->weight,
          '#delta' => 10,
          '#title_display' => 'invisible',
        ),

        'data' => array(
          '#markup' => implode(' ', $info),
        ),

        'rows' => array(
          '#markup' => $count,
        ),

        'view' => array(
          '#markup' => l(t('View'), 'admin/commerce/pricelist/commerce_pricelist_list/' . $item->list_id),
        ),

        'add' => array(
          '#markup' => l(t('Add price'), 'admin/commerce/pricelist/commerce_pricelist_list/' . $item->list_id . '/add',
            array('query' => array('destination' => current_path()))),
        ),

        'edit' => array(
          '#markup' => l(t('Edit'), 'admin/commerce/pricelist/commerce_pricelist_list/' . $item->list_id . '/edit'),
        ),
      );
    }

    // Now we add our submit button, for submitting the form results.
    //
    // The 'actions' wrapper used here isn't strictly necessary for tabledrag,
    // but is included as a Form API recommended practice.
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array('#type' => 'submit', '#value' => t('Save Changes'));

    return $form;
  }
  else {
    // There were no entities. Tell the user.
    $content[] = array(
      '#type' => 'item',
      '#markup' => t('No price lists currently exist.'),
    );
  }
  return $content;
}

/**
 * Theme callback for the commerce_pricelist_draggable_form form.
 *
 * The theme callback will format the $form data structure into a table and
 * add our tabledrag functionality.  (Note that drupal_add_tabledrag should be
 * called from the theme layer, and not from a form declaration.  This helps
 * keep template files clean and readable, and prevents tabledrag.js from
 * being added twice accidently.
 *
 * @return array
 *   The rendered tabledrag form
 *
 * @ingroup commerce_pricelist
 */
function theme_commerce_pricelist_draggable_form($variables) {
  $form = $variables['form'];

  // Initialize the variable which will store our table rows.
  $rows = array();

  if (isset($form['pricelists'])) {

    // Iterate over each element in our $form['pricelists'] array.
    foreach (element_children($form['pricelists']) as $id) {

      // Before we add our 'weight' column to the row, we need to give the
      // element a custom class so that it can be identified in the
      // drupal_add_tabledrag call.
      //
      // This could also have been done during the form declaration by adding
      // '#attributes' => array('class' => 'pricelist-weight'),
      // directy to the 'weight' element in commerce_pricelist_draggable_form().
      $form['pricelists'][$id]['weight']['#attributes']['class'] = array('pricelist-weight');

      // We are now ready to add each element of our $form data to the $rows
      // array, so that they end up as individual table cells when rendered
      // in the final table.  We run each element through the drupal_render()
      // function to generate the final html markup for that element.
      $rows[] = array(
        'data' => array(
          // Add our 'name' column.
          drupal_render($form['pricelists'][$id]['title']),
          // Add our 'description' column.
          drupal_render($form['pricelists'][$id]['status']),
          // Add our 'weight' column.
          drupal_render($form['pricelists'][$id]['weight']),
          drupal_render($form['pricelists'][$id]['data']),
          drupal_render($form['pricelists'][$id]['rows']),
          drupal_render($form['pricelists'][$id]['view']),
          drupal_render($form['pricelists'][$id]['add']),
          drupal_render($form['pricelists'][$id]['edit']),
        ),
        // To support the tabledrag behaviour, we need to assign each row of the
        // table a class attribute of 'draggable'. This will add the 'draggable'
        // class to the <tr> element for that row when the final table is
        // rendered.
        'class' => array('draggable'),
      );
    }
  }


  $header = array(
    t('Title'),
    t('Status'),
    t('Weight'),
    t('Filters'),
    t('Rows'),
    array('data' => t('Operations'), 'colspan' => '3'),
  );
  $table_id = 'pricelists-table';

  // We can render our tabledrag table for output.
  $output = theme('table', array(
    'header' => $header,
    'rows' => $rows,
    'attributes' => array('id' => $table_id),
  ));

  // And then render any remaining form elements (such as our submit button).
  $output .= drupal_render_children($form);

  // We now call the drupal_add_tabledrag() function in order to add the
  // tabledrag.js goodness onto our page.
  //
  // For a basic sortable table, we need to pass it:
  // - the $table_id of our <table> element,
  // - the $action to be performed on our form items ('order'),
  // - a string describing where $action should be applied ('siblings'),
  // - and the class of the element containing our 'weight' element.
  drupal_add_tabledrag($table_id, 'order', 'sibling', 'pricelist-weight');

  return $output;
}

/**
 * Submit callback for the commerce_pricelist_draggable_form form.
 *
 * Updates the 'weight' column for each element in our table, taking into
 * account that item's new order after the drag and drop actions have been
 * performed.
 *
 * @ingroup commerce_pricelist
 */
function commerce_pricelist_draggable_form_submit($form, &$form_state) {
  // Because the form elements were keyed with the item ids from the database,
  // we can simply iterate through the submitted values.
  foreach ($form_state['values']['pricelists'] as $id => $item) {
    db_query(
      "UPDATE {commerce_pricelist_list} SET weight = :weight, status = :status WHERE list_id = :id",
      array(':weight' => $item['weight'], ':status' => $item['status'], ':id' => $id)
    );
  }
}


/**
 * Returns a render array with all commerce_pricelist_list entities.
 *
 * In this basic example we know that there won't be many entities,
 * so we'll just load them all for display. See pager_example.module
 * to implement a pager. Most implementations would probably do this
 * with the contrib Entity API module, or a view using views module,
 * but we avoid using non-core features in the Examples project.
 *
 * @see pager_example.module
 */
function commerce_pricelist_item_list_entities($pricelist_uuid = NULL, $sku = FALSE) {
  $content = array();
  $entities = array();

  if ($pricelist_uuid) {

    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'commerce_pricelist_item')
      ->propertyCondition('pricelist_uuid', $pricelist_uuid, '=');

    if ($sku) {
      // Filter by SKU.
      $query->propertyCondition('sku', $sku, '=');
    }

    $result = $query->execute();

    if (!empty($result)) {
      // Load all of our entities.
      $entities = commerce_pricelist_item_load_multiple(array_keys($result['commerce_pricelist_item']));
    }

  } else {
    // Load all of our entities.
    $entities = commerce_pricelist_item_load_multiple();
  }

  if (!empty($entities)) {
    $rows = array();

    foreach ($entities as $entity) {
      // Don't print out the date of the apocalypse
      if ($entity->valid_to == COMMERCE_PRICELIST_UNIX_TIME_APOCALYPSE) {
        $entity->valid_to = '';
      }
      // Create tabular rows for our entities.
      $rows[] = array(
        'data' => array(
          'sku' => check_plain($entity->sku),
          'valid from' => format_date($entity->valid_from, 'short'),
          'valid to' => format_date($entity->valid_to, 'short'),
          'quantity' => $entity->quantity,
          // @todo Format currency through commerce
          'amount' => $entity->price_amount / 100,
          'currency' => check_plain($entity->currency_code),
          'edit' => l(t('Edit'),
            'admin/commerce/pricelist/commerce_pricelist_item/'. $entity->item_id .'/edit',
            array('query' => array('destination' => current_path()))),
        ),
      );
    }

    // Put our entities into a themed table. See theme_table() for details.
    $content['entity_table'] = array(
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => array(t('sku'), t('Valid from'), t('Valid to'), t('Quantity'), t('Amount'), t('Currency'), t('Operations')),
    );
  }
  else {
    // There were no entities. Tell the user.
    $content[] = array(
      '#type' => 'item',
      '#markup' => t('No price list items currently exist.'),
    );
  }
  return $content;
}


/**
 * Menu callback to display an entity.
 *
 * As we load the entity for display, we're responsible for invoking a number
 * of hooks in their proper order.
 *
 * @see hook_entity_prepare_view()
 * @see hook_entity_view()
 * @see hook_entity_view_alter()
 */
function commerce_pricelist_item_view($entity, $view_mode = 'full') {
  // Our entity type, for convenience.
  $entity_type = 'commerce_pricelist_item';
  // Start setting up the content.
  $entity->content = array(
    '#view_mode' => $view_mode,
  );

  // We call entity_prepare_view() so it can invoke hook_entity_prepare_view()
  // for us.
  entity_prepare_view($entity_type, array($entity->item_id => $entity));

  // OK, Field API done, now we can set up some of our own data.
  $entity->content['pricelist_uuid'] = array(
    '#type' => 'item',
    '#title' => t('Price list UUID'),
    '#markup' => $entity->pricelist_uuid,
  );

  // Now to invoke some hooks. We need the language code for
  // hook_entity_view(), so let's get that.
  global $language;
  $langcode = $language->language;
  // And now invoke hook_entity_view().
  module_invoke_all('entity_view', $entity, $entity_type, $view_mode,
    $langcode);
  // Now invoke hook_entity_view_alter().
  drupal_alter(array('commerce_pricelist_item_view', 'entity_view'),
    $entity->content, $entity_type);

  // And finally return the content.
  return $entity->content;
}

/**
 * Provides a wrapper on the edit form to add a new entity.
 */
function commerce_pricelist_item_add($product = NULL) {
  // Create a basic entity structure to be used and passed to the validation
  // and submission functions.
  $sku = NULL;
  if ($product != NULL) {
    $sku = $product->sku;
  }
  $entity = entity_get_controller('commerce_pricelist_item')->create(NULL, $sku);
  return drupal_get_form('commerce_pricelist_item_form', $entity);
}

/**
 * Basic information for the page.
 */
function commerce_pricelist_item_info_page() {
  $content['preface'] = array(
    '#type' => 'item',
    '#markup' => t('The entity example provides a simple example entity.'),
  );
  if (user_access('administer commerce_pricelist_list entities')) {
    $content['preface']['#markup'] = t('You can administer these and add fields and change the view !link.',
      array('!link' => l(t('here'), 'admin/commerce/pricelist/commerce_pricelist_item/manage'))
    );
  }
  $content['table'] = commerce_pricelist_item_list_entities();

  return $content;
}


/**
 * Form function to create an commerce_pricelist_list entity.
 *
 * The pattern is:
 * - Set up the form for the data that is specific to your
 *   entity: the columns of your base table.
 * - Call on the Field API to pull in the form elements
 *   for fields attached to the entity.
 */
function commerce_pricelist_item_form($form, &$form_state, $entity) {

  if (isset($entity->list_id)) {
    // This is a new list item being created from the list/add tab.
    // Replace pricelist entity with a pricelist item entity.
    $entity = entity_get_controller('commerce_pricelist_item')->create($entity->uuid);
  }

  if ($entity->pricelist_uuid) {
    $form['pricelist_uuid'] = array(
      '#type' => 'value',
      '#value' => $entity->pricelist_uuid,
    );
  } else {
    $pricelists = commerce_pricelist_list_load_multiple();
    $options = array();
    foreach ($pricelists as $pricelist) {
      $options[$pricelist->uuid] = $pricelist->title;
    }
    $form['pricelist_uuid'] = array(
      '#type' => 'select',
      '#options' => $options,
      '#required' => TRUE,
      '#title' => t('Price list'),
      '#element_validate' => array('_commerce_pricelist_validate_pricelist'),
    );
  }

  $form_fields = array('sku','valid_from','valid_to','quantity','price_amount','currency_code');

  foreach ($form_fields as $field) {
    $form[$field] = array(
      '#type' => 'textfield',
      '#title' => ucwords(str_replace('_', ' ', $field)),
      '#required' => TRUE,
      '#default_value' => isset($entity->$field) ? $entity->$field : '',
    );
  }

  foreach (array('valid_from', 'valid_to') as $date_field) {
    $form[$date_field]['#element_validate'] = array('_commerce_pricelist_date_validate');
    $form[$date_field]['#default_value'] = date('c', (int) $form[$date_field]['#default_value']);
  }

  $form['price_amount']['#element_validate'] = array('element_validate_integer_positive');
  $form['price_amount']['#description'] = t('In Drupal Commerce minor currency so $1.50 = 150 and $150 = 15000');
  $form['quantity']['#element_validate'] = array('element_validate_number');
  $form['currency_code']['#default_value'] = variable_get('commerce_default_currency', 'USD');

  $form['entity'] = array(
    '#type' => 'value',
    '#value' => $entity,
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
    '#weight' => 100,
  );

  // Check if this is an existing item, add delete button
  if ($entity->item_id) {
    $form['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Delete'),
      '#submit' => array('commerce_pricelist_item_edit_delete'),
      '#weight' => 200,
    );
  }

  return $form;
}

/**
 * Validation handler, parses string as date
 */
function _commerce_pricelist_date_validate($element) {
  if (!is_int(strtotime($element['#value']))) {
    form_set_error($element['#name'], t('Unable to convert date string to timestamp'));
  }
}

/**
 * Validate that pricelist exists.
 *
 * @param $element
 */
function _commerce_pricelist_validate_pricelist($element) {
  $value = $element['#value'];
  if ($value == '' || !entity_uuid_load('commerce_pricelist_list', array($value))) {
    form_set_error($element['#name'], t('Invalid price list'));
  }
}

/**
 * Validation handler for commerce_pricelist_item_add_form form.
 *
 * We pass things straight through to the Field API to handle validation
 * of the attached fields.
 */
function commerce_pricelist_item_form_validate($form, &$form_state) {
}

/**
 * Form submit handler: Submits basic_add_form information.
 */
function commerce_pricelist_item_form_submit($form, &$form_state) {
  $entity = $form_state['values']['entity'];
  foreach ($form_state['values'] as $key => $value) {
    $entity->$key = $value;
  }
  field_attach_submit('commerce_pricelist_item', $entity, $form, $form_state);
  $entity = commerce_pricelist_item_save($entity);
  if ($entity) {
    drupal_set_message(t('Price list item saved'));
  }
}

/**
 * Form deletion handler.
 *
 * @todo: 'Are you sure?' message.
 */
function commerce_pricelist_item_edit_delete($form, &$form_state) {
  $entity = $form_state['values']['entity'];
  commerce_pricelist_item_delete($entity);
  drupal_set_message(t('The entity %item_description (ID %id) has been deleted',
      array('%item_description' => $entity->uuid, '%id' => $entity->item_id))
  );
}
