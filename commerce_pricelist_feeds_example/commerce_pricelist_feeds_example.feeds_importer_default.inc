<?php
/**
 * @file
 * commerce_pricelist_feeds_example.feeds_importer_default.inc
 */

/**
 * Implements hook_feeds_importer_default().
 */
function commerce_pricelist_feeds_example_feeds_importer_default() {
  $export = array();

  $feeds_importer = new stdClass();
  $feeds_importer->disabled = FALSE; /* Edit this to true to make a default feeds_importer disabled initially */
  $feeds_importer->api_version = 1;
  $feeds_importer->id = 'pricelist';
  $feeds_importer->config = array(
    'name' => 'Pricelist',
    'description' => '',
    'fetcher' => array(
      'plugin_key' => 'FeedsFileFetcher',
      'config' => array(
        'allowed_extensions' => 'txt csv tsv xml opml xls',
        'direct' => 0,
        'directory' => 'public://feeds',
        'allowed_schemes' => array(
          'public' => 'public',
        ),
      ),
    ),
    'parser' => array(
      'plugin_key' => 'FeedsExcelParser',
      'config' => array(
        'no_headers' => 0,
        'all_worksheets' => 0,
      ),
    ),
    'processor' => array(
      'plugin_key' => 'FeedsCommercePricelistItemProcessor',
      'config' => array(
        'is_active' => '1',
        'mappings' => array(
          0 => array(
            'source' => 'Currency',
            'target' => 'currency_code',
            'unique' => FALSE,
          ),
          1 => array(
            'source' => 'SKU',
            'target' => 'sku',
            'unique' => FALSE,
          ),
          2 => array(
            'source' => 'From date',
            'target' => 'valid_from',
            'unique' => FALSE,
          ),
          3 => array(
            'source' => 'To date',
            'target' => 'valid_to',
            'unique' => FALSE,
          ),
          4 => array(
            'source' => 'Quantity',
            'target' => 'quantity',
            'unique' => FALSE,
          ),
          5 => array(
            'source' => 'Price',
            'target' => 'price_amount',
            'unique' => FALSE,
          ),
        ),
        'update_existing' => '1',
        'input_format' => 'plain_text',
        'skip_hash_check' => 0,
        'bundle' => 'commerce_pricelist_item',
        'is_attached' => '1',
        'list_uuid' => NULL,
        'convert_price' => '1',
        'convert_dates' => '0',
      ),
    ),
    'content_type' => '',
    'update' => 0,
    'import_period' => '-1',
    'expire_period' => 3600,
    'import_on_create' => 1,
    'process_in_background' => 0,
  );
  $export['pricelist'] = $feeds_importer;

  return $export;
}
