<?php

require_once 'config/constants.php';
require_once 'methods/request.php';
require_once 'methods/process.php';
require_once 'methods/exporter.php';

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



// PROCESSING DATA



// EXPORTING DATA


echo '<pre>'. print_r($productList, true) .'</pre>';
