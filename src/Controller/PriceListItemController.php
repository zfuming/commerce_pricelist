<?php

namespace Drupal\commerce_pricelist\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class PriceListAddController.
 *
 * @package Drupal\commerce_pricelist\Controller
 */
class PriceListItemController extends ControllerBase {


    public function collection(Request $request) {
      $price_list_id = $request->get('price_list_id');
      return array('#theme' => 'price_list_content_add_list', '#content' => '1234');
    }

}
