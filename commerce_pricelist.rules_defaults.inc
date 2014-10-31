<?php
/**
 * @file
 * temp.rules_defaults.inc
 */

/**
 * Implements hook_default_rules_configuration().
 */
function commerce_pricelist_default_rules_configuration() {
  $items = array();
  $items['rules_commerce_price_list_fetch_price'] = entity_import('rules_config', '{ "rules_commerce_price_list_fetch_price" : {
    "LABEL" : "Fetch the price list price for a line item",
    "PLUGIN" : "rule",
    "OWNER" : "rules",
    "TAGS" : [ "Commerce Price list" ],
    "REQUIRES" : [ "commerce_pricelist", "rules" ],
    "USES VARIABLES" : {
      "commerce_line_item" : { "label" : "Line item", "type" : "commerce_line_item" },
      "commerce_pricelist_price" : {
        "label" : "Price list price",
        "type" : "commerce_price",
        "parameter" : false
      },
      "commerce_pricelist_price_amount" : { "label" : "Amount", "type" : "decimal", "parameter" : false },
      "commerce_pricelist_price_currency_code" : { "label" : "Currency", "type" : "text", "parameter" : false }
    },
    "DO" : [
      { "commerce_pricelist_rate_apply" : {
          "USING" : {
            "commerce_line_item" : [ "commerce_line_item" ],
            "user" : [ "commerce-line-item:order:owner" ]
          },
          "PROVIDE" : {
            "commerce_pricelist_price" : { "commerce_pricelist_price_fetched" : "Applied pricelist price array" },
            "commerce_pricelist_price_amount" : { "commerce_pricelist_price_amount_fetched" : "Amount" },
            "commerce_pricelist_price_currency_code" : { "commerce_pricelist_price_currency_code_fetched" : "Currency" }
          }
        }
      },
      { "data_set" : {
          "data" : [ "commerce-pricelist-price-amount" ],
          "value" : [ "commerce-pricelist-price-amount-fetched" ]
        }
      },
      { "data_set" : {
          "data" : [ "commerce-pricelist-price-currency-code" ],
          "value" : [ "commerce-pricelist-price-currency-code-fetched" ]
        }
      }
    ],
    "PROVIDES VARIABLES" : [
      "commerce_pricelist_price_amount",
      "commerce_pricelist_price_currency_code"
    ]
  }
}');
  $items['rules_commerce_price_list_set_price'] = entity_import('rules_config', '{ "rules_commerce_price_list_set_price" : {
    "LABEL" : "Set the price for a product from price list",
    "PLUGIN" : "reaction rule",
    "WEIGHT" : "-10",
    "OWNER" : "rules",
    "REQUIRES" : [ "rules", "commerce_line_item", "commerce_product_reference" ],
    "ON" : { "commerce_product_calculate_sell_price" : [] },
    "DO" : [
      { "component_rules_commerce_price_list_fetch_price" : {
          "USING" : { "commerce_line_item" : [ "commerce_line_item" ] },
          "PROVIDE" : {
            "commerce_pricelist_price_amount" : { "commerce_pricelist_price_amount" : "Amount" },
            "commerce_pricelist_price_currency_code" : { "commerce_pricelist_price_currency_code" : "Currency" }
          }
        }
      },
      { "commerce_line_item_unit_price_currency_code" : {
          "commerce_line_item" : [ "commerce_line_item" ],
          "currency_code" : [ "commerce-pricelist-price-currency-code" ]
        }
      },
      { "commerce_line_item_unit_price_amount" : {
          "commerce_line_item" : [ "commerce_line_item" ],
          "amount" : [ "commerce-pricelist-price-amount" ],
          "component_name" : "base_price",
          "round_mode" : "0"
        }
      }
    ]
  }
}');
  $items['rules_disable_products_with_no_price'] = entity_import('rules_config', '{ "rules_disable_products_with_no_price" : {
      "LABEL" : "Disable products with no price",
      "PLUGIN" : "reaction rule",
      "WEIGHT" : "10",
      "OWNER" : "rules",
      "TAGS" : [ "Commerce Price list" ],
      "REQUIRES" : [ "rules", "commerce_product_reference" ],
      "ON" : { "commerce_product_calculate_sell_price" : [] },
      "IF" : [
        { "data_is" : {
            "data" : [ "commerce-line-item:commerce-unit-price:amount-decimal" ],
            "value" : "0"
          }
        },
        { "entity_has_field" : { "entity" : [ "commerce-line-item" ], "field" : "commerce_product" } }
      ],
      "DO" : [
        { "data_set" : { "data" : [ "commerce-line-item:quantity" ], "value" : "0" } },
        { "data_set" : { "data" : [ "commerce-line-item:commerce-unit-price:amount" ] } }
      ]
    }
  }');
  return $items;
}
