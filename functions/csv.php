<?php

use League\Csv\Reader;
use League\Csv\Writer;
require 'csv-master/autoload.php';


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





?>