<?php
/**
 * @file
 * commerce_pricelist_feeds_example.feeds_tamper_default.inc
 */

/**
 * Implements hook_feeds_tamper_default().
 */
function commerce_pricelist_feeds_example_feeds_tamper_default() {
  $export = array();

  $feeds_tamper = new stdClass();
  $feeds_tamper->disabled = FALSE; /* Edit this to true to make a default feeds_tamper disabled initially */
  $feeds_tamper->api_version = 2;
  $feeds_tamper->id = 'pricelist-fr__n_datum-math';
  $feeds_tamper->importer = 'pricelist';
  $feeds_tamper->source = 'From date';
  $feeds_tamper->plugin_id = 'math';
  $feeds_tamper->settings = array(
    'operation' => 'subtraction',
    'value' => '25569',
  );
  $feeds_tamper->weight = 0;
  $feeds_tamper->description = 'Convert excel days to unix days';
  $export['pricelist-fr__n_datum-math'] = $feeds_tamper;

  $feeds_tamper = new stdClass();
  $feeds_tamper->disabled = FALSE; /* Edit this to true to make a default feeds_tamper disabled initially */
  $feeds_tamper->api_version = 2;
  $feeds_tamper->id = 'pricelist-fr__n_datum-math_mult_86400';
  $feeds_tamper->importer = 'pricelist';
  $feeds_tamper->source = 'From date';
  $feeds_tamper->plugin_id = 'math';
  $feeds_tamper->settings = array(
    'operation' => 'multiplication',
    'value' => '86400',
  );
  $feeds_tamper->weight = 1;
  $feeds_tamper->description = 'Multiply with sek/day';
  $export['pricelist-fr__n_datum-math_mult_86400'] = $feeds_tamper;

  $feeds_tamper = new stdClass();
  $feeds_tamper->disabled = FALSE; /* Edit this to true to make a default feeds_tamper disabled initially */
  $feeds_tamper->api_version = 2;
  $feeds_tamper->id = 'pricelist-till_datum-math';
  $feeds_tamper->importer = 'pricelist';
  $feeds_tamper->source = 'To date';
  $feeds_tamper->plugin_id = 'math';
  $feeds_tamper->settings = array(
    'operation' => 'subtraction',
    'value' => '25569',
  );
  $feeds_tamper->weight = 0;
  $feeds_tamper->description = 'Convert excel days to unix days';
  $export['pricelist-till_datum-math'] = $feeds_tamper;

  $feeds_tamper = new stdClass();
  $feeds_tamper->disabled = FALSE; /* Edit this to true to make a default feeds_tamper disabled initially */
  $feeds_tamper->api_version = 2;
  $feeds_tamper->id = 'pricelist-till_datum-math_mult_86400';
  $feeds_tamper->importer = 'pricelist';
  $feeds_tamper->source = 'To date';
  $feeds_tamper->plugin_id = 'math';
  $feeds_tamper->settings = array(
    'operation' => 'multiplication',
    'value' => '86400',
  );
  $feeds_tamper->weight = 1;
  $feeds_tamper->description = 'Multiply with sek/day';
  $export['pricelist-till_datum-math_mult_86400'] = $feeds_tamper;

  return $export;
}
