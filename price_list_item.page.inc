<?php

/**
 * @file
 * Contains price_list_item.page.inc.
 *
 * Page callback for Price list item entities.
 */

use Drupal\Core\Render\Element;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Prepares variables for Price list item templates.
 *
 * Default template: price_list_item.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_price_list_item(array &$variables) {
  // Fetch PriceListItem Entity Object.
  $price_list_item = $variables['elements']['#price_list_item'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}


