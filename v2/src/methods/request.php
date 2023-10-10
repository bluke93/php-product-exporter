<?php

function buildAPIUrl($startDate){
  $baseUrl = "https://".BASE_URL."/".API_PATH."/".API_VERSION."/".DB_CONNECTION;

  $endpoint = str_replace("{codice_negozio}", STORE_ID, API_ENDPOINT);
  $endpoint = str_replace("{startDateString}", $startDate, $endpoint);

  $fullUrl = $baseUrl.$endpoint;
  return $fullUrl;
}

function sendAPIRequest($url, $maxRetries = 5){
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

function extractData($result){
  return $result["data"];
}