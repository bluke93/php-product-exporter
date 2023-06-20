<?php 

/**
 * Function which builds and returns the API url.
 * @return string - config hydrated full url.
 **/
function getAPIURL() {
  // Building baseUrl
  $baseUrl = "https://".BASE_URL."/".API_PATH."/".API_VERSION."/".DB_CONNECTION;

  // Get Today Date in yyyy-MM-dd
  $startDate = date('Y-m-d');

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
 * @param array $products - original array of products.
 * @return Array<Array> - The remapped product list.
 **/
function downloadImages($product){
  if(count($product["images"])){
    foreach($product["images"] as $key => $img){
      $extension = pathinfo(parse_url($img["url"], PHP_URL_PATH), PATHINFO_EXTENSION);
      $newName = $product["id"]."_".$key.".".$extension; 
      $savedImage = saveImage($img["url"], $newName, IMAGES_PATH);
      $product["images"][$key]['url'] = $savedImage;
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