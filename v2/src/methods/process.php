<?php


/**
 * Replaces keys in a multidimensional array with new keys based on a key map.
 *
 * @param array $array The input array to replace keys in.
 * @param array $keyMap An associative array where the keys represent the old keys and the values represent the new keys.
 * @return array The resulting array with replaced keys.
 */
function replaceKeys(array $array, array $keyMap): array
{
  $result = [];
  foreach ($array as $item) {
    $newItem = [];
    foreach ($item as $key => $value) {
      if (isset($keyMap[$key])) {
        $newItem[$keyMap[$key]] = $value;
      } else {
        $newItem[$key] = $value;
      }
    }
    $result[] = $newItem;
  }
  return $result;
}


