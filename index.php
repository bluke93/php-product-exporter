<html>
<head>
<title>Product List Exporter 1.0.0</title>
</head>
<body>
<?php 

require 'config/constants.php';
require 'functions/products.php';
require 'functions/csv.php';


$url = getAPIURL();
$maxRetries = MAX_RETRIES;
$result = getProductList($url, $maxRetries);

if($result["success"] == true && $result["status_code"] == 200){
  $productList = $result["data"];

  $remapConfig = getRemapConfig();
  $remappedProductList = remapProducts($productList, $remapConfig);

  $previousProductList = retrievePreviousProductList();
  if($previousProductList){
    $resultProductList = mergeProductLists($previousProductList, $remappedProductList);
  } else {
    $resultProductList = $remappedProductList;
  }
  exportProductList($resultProductList);
} else {
  // ERRORE
  print("<pre>".print_r($result,true)."</pre>");
}


?>
</body>
</html>