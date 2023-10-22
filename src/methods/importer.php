<?php


/**
 * Returns an array of header mappings from a CSV file.
 *
 * This function reads a CSV file located at CONFIG_PATH/remap.csv and returns an associative array where the keys are the original headers and the values are the updated headers.
 *
 * @return array An associative array where the keys are the original headers and the values are the updated headers.
 */
function getHeaderMappings(): array{
  $filename = CONFIG_PATH.'/remap.csv';
  $data = array();
  if (($handle = fopen($filename, "r")) !== FALSE) {
    $headers = fgetcsv($handle, 10000, ",");
    while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
      $row_data = array();
      foreach ($headers as $i => $header) {
        $row_data[$header] = $row[$i];
      }
      $data[] = $row_data;
    }
    fclose($handle);
  }
  $result = array();
  foreach ($data as $row) {
    $result[$row['original']] = $row['updated'];
  }
  return $result;
}


/**
 * Retrieves the final export keys from the export-keys.csv file.
 *
 * @return array An array containing the final export keys.
 */
function getFinalExportKeys(): array {
  $filename = CONFIG_PATH.'/exportKeys.csv';
  $data = array();
  if (($handle = fopen($filename, "r")) !== FALSE) {
    while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
      $data[] = $row;
    }
    fclose($handle);
  }
  return $data[0];
}
