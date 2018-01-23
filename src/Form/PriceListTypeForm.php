<?php

namespace Drupal\commerce_pricelist\Form;

use Drupal\commerce\Form\CommerceBundleEntityFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class PriceListTypeForm.
 *
 * @package Drupal\commerce_pricelist\Form
 */
class PriceListTypeForm extends CommerceBundleEntityFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\commerce_pricelist\Entity\PriceListTypeInterface $price_list_type */
    $price_list_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $price_list_type->label(),
      '#description' => $this->t("Label for the Price list type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $price_list_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\commerce_pricelist\Entity\PriceListType::load',
      ],
      '#disabled' => !$price_list_type->isNew(),
    ];

    $form['description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Description'),
      '#default_value' => $price_list_type->getDescription(),
    ];

    $form = $this->buildTraitForm($form, $form_state);

    /* You will need additional form elements for your custom properties. */

    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $price_list_type = $this->entity;
    $status = $price_list_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Price list type.', [
          '%label' => $price_list_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Price list type.', [
          '%label' => $price_list_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($price_list_type->urlInfo('collection'));
  }

}
