<?php 

use League\Csv\Reader;
use League\Csv\Writer;
require 'csv-master/autoload.php';



function getExportedHistory(){
  $files = glob(EXPORTED_PATH."/*");
  array_multisort(array_map('filemtime', $files), SORT_NUMERIC, SORT_DESC, $files);
  return $files;
}

function getLastExportedFile(){
  $files = getExportedHistory();
  return count($files) ? $files[0] : false;
}


/**
 * Function that reads the remapping config from the csv in /config/remap.csv.
 * @return array a key=>value associative array with current=>new keys.
 **/
function getRemapConfig(){
  $reader = Reader::createFromPath('config/remap.csv', 'r');
  $reader->setHeaderOffset(0);
  $records = $reader->getRecords();
  $remapKeys = [];

  foreach ($records as $offset => $record) {
    if($record["original"] != $record["updated"] && $record["updated"] != ""){
      $remapKeys[$record["original"]] = $record["updated"];
    } else {
      $remapKeys[$record["original"]] = $record["original"];
    }
  }

  return $remapKeys;
}


function exportProductList($productList, $headers = []){
  $headers = array_values(getRemapConfig());
  $date = date('Y-m-d');
  $destination = EXPORTED_PATH;
  $fileName = "exported_".$date.".csv";
  $writer = Writer::createFromPath($destination."/".$fileName, 'w+');
  $writer->insertOne($headers);
  $writer->insertAll($productList); //using an array
  return true;
}

function retrievePreviousProductList($date = null){
  $date = date("Y-m-d", strtotime("-1 day"));
  $fileName = "exported_".$date.".csv";
  $srcPath = EXPORTED_PATH;
  try {
    $reader = Reader::createFromPath($srcPath."/".$fileName, 'r');
    $reader->setHeaderOffset(0);
    $headers = $reader->getHeader(0);
    $records = $reader->getRecords();
    $arrayRecords = convertIteratorToArray($headers, $records);
    return $arrayRecords;
  }catch(Exception $e) {
    return false;
  }
  
}

function convertIteratorToArray($headers, $records){
  $result = [];
  foreach ($records as $offset => $record) {
    $result[] = $record;
  }
  return $result;
}

/**
 * Function which builds and returns the API url.
 * @return string - config hydrated full url.
 **/
function getAPIURL() {
  // Building baseUrl
  $baseUrl = BASE_URL."/".API_PATH."/".API_VERSION."/".DB_CONNECTION;

  // Get Today Date in yyyy-MM-dd
  $startDate = date('Y-m-d');
  $startDate = "2020-01-01";

  // Replacing wildcard with config
  $endpoint = str_replace("{codice_negozio}", STORE_ID, API_ENDPOINT);
  $endpoint = str_replace("{startDateString}", $startDate, $endpoint);

  // Complete and return fullUrl
  $fullUrl = $baseUrl.$endpoint;
  return $fullUrl;
}

/**
 * Function that makes a request and returns the product list
 * @param string $url - URL to call for the request.
 * @param int $maxRetries - Number of retries if the request fails.
 * @return Array<Array> - The response of the CURL or error handling.
 **/
function getProductList($url, $maxRetries = 5){
  // Setup CURL
  $curl = curl_init();
  $retries = 0;

  $headers = [
    HEADER_APIKEY_KEY . " : " . HEADER_APIKEY_VALUE
  ];

  // CURL options
  curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => $headers
  ));

  do {
    // Execute CURL
    $response = curl_exec($curl);  
    $curlInfo = curl_getinfo($curl);

    // Handle response
    if($response !== false && $curlInfo["http_code"] != 404 && $curlInfo["http_code"] != 500){
      // CURL succeed, handle response
      curl_close($curl);
      $products = json_decode($response,true);

      // Build response
      return [
          "success" => true,
          "status_code" => $curlInfo["http_code"],
          "error" => false,
          "error_msg" => null,
          "data" => $products
        ];

    } else {
      // CURL failed, handle retries and errors
      curl_close($curl);
      if($retries == $maxRetries){
        // Max retries performed, exit
        return [
          "success" => false,
          "status_code" => $curlInfo["http_code"],
          "error" => true,
          "error_msg" => "ERROR ". $curlInfo["http_code"] .": API Request is failing.",
          "data" => json_decode($response, true)
        ];
      } else {
        // Keep trying
        $retries++;
      }
    }
  }while($retries <= $maxRetries);
}

/**
 * Function that remaps the array or products with the new keys provided.
 * @param array $products - original array of products.
 * @param array $newKeys - associative array with original keys to remap new keys.
 * @return Array<Array> - The remapped product list.
 **/
function remapProducts($products, $remapKeys){
  $resultArray = [];

  // cycling the products
  foreach ($products as $index => $product){
     // cycling the product keys to update them
    foreach ($product as $key => $value){
       // $resultArray = output array
       // $index = index of the parent loop product
       // $remapKeys = associative array with [oldKey => newKey] elements
       // $key = current loop product array  key
       // $value = current loop product array value
      $resultArray[$index][$remapKeys[$key]] = $value;
    }
    $resultArray[$index] = downloadImages($resultArray[$index]);
  }

  return $resultArray;
}

/**
 * Function that saves the images locally and updates the new url.
 * @param array $product - original product.
 * @return Array<Array> - The remapped product.
 **/
function downloadImages($product){
  if(count($product["immagini"])){
    foreach($product["immagini"] as $key => $img){
      $extension = pathinfo(parse_url($img["url"], PHP_URL_PATH), PATHINFO_EXTENSION);
      $newName = $product["idlistino"]."_".$key.".".$extension; 
      $savedImage = saveImage($img["url"], $newName, IMAGES_PATH);
      $product["immagini"][$key]['url'] = $savedImage;
    }
  }
  return $product;
}

/**
 * Function that saves the image locally and returns the new url.
 * @param string $url - original image url.
 * @param string $newName - new image name.
 * @param string $destination - path to save the image.
 * @return string the full image path.
 **/
function saveImage($url, $newName, $destination){
  
  $img = $destination."/".$newName;
  $result = file_put_contents($img, file_get_contents($url));

  if($result !== false){
    return $img;
  } else {
    return false;
  }
}

/**
 * Function that merges the old and updated product lists.
 * @param array $old - list of products.
 * @param array $updated - new list of products.
 * @return array the merged list.
 **/
function mergeProductLists($old, $updated){
  foreach($updated as $updatedItem){
    foreach ($old as $key => $val) {
      if ($val['id'] === $updatedItem["id"]) {
        $old[$key] = array_merge($old[$key], $updatedItem);
      }
    }
  }
  return $old;
}


?>