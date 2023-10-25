<?php

require_once 'config/constants.php';
require_once 'methods/request.php';
require_once 'methods/process.php';
require_once 'methods/exporter.php';
require_once 'methods/importer.php';

// Check if the 'startDate' parameter exists in the query string
if (isset($_GET['startDate'])) {
    $startDate = $_GET['startDate'];
} else {
    $startDate = date('Y-m-d');
}

// REQUEST AND RETRIEVING DATA
$apiUrl = buildAPIUrl($startDate);
$maxRetries = MAX_RETRIES;
$result = sendAPIRequest($apiUrl, $maxRetries);
$productList = extractData($result);
$mappedHeaders = getHeaderMappings();

// PROCESSING DATA
$replacedKeys = replaceKeys($productList, $mappedHeaders);
$finalExportKeys = getFinalExportKeys();
$remappedProducts = filterByKeys($replacedKeys, $finalExportKeys);
$exportProductsList = addMissingKeys($remappedProducts, $finalExportKeys);
$sortedExportProductList = orderKeys($exportProductsList, $finalExportKeys);

// EXPORTING DATA
$filename = date('d-m-Y_His').'.csv';
exportToCsv($filename, $sortedExportProductList, 'dist');

echo 'API CALL RESULT: '.($result['success'] ? 'SUCCESS' : 'FAILED').'<br>';
echo 'Products from API: '.count($productList).'<br>';
echo 'Products after processing: '.count($sortedExportProductList).'<br>';
echo '<pre>'. print_r($sortedExportProductList, true) .'</pre>';