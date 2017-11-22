<?php

namespace Drupal\commerce_pricelist\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Form controller for Price list item edit forms.
 *
 * @ingroup commerce_pricelist
 */
class PriceListItemForm extends ContentEntityForm {
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\commerce_pricelist\Entity\PriceListItem */
    $form = parent::buildForm($form, $form_state);
//    $entity = $this->entity;
    $lastRoute = $this->getLastRouteName();
    if ($lastRoute == 'entity.price_list.canonical') {unset($form['price_list_id']);}
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
        drupal_set_message($this->t('Created the %label Price list item.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Price list item.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.price_list_item.collection');
  }

  /**
   * get last route name by HTTP_REFERER
   * @return string
   */
  public function getLastRouteName()
  {
    $previousUrl = \Drupal::request()->server->get('HTTP_REFERER');
    $fake_request = Request::create($previousUrl);
    $url_object = \Drupal::service('path.validator')->getUrlIfValid($fake_request->getRequestUri());
    $route_name = '';
    if ($url_object) {$route_name = $url_object->getRouteName();}
    return $route_name;
  }

}
