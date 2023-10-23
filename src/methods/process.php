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


/**
 * Filters an array of elements by only keeping the elements with keys present in a provided array of keys.
 *
 * @param array $elements The array of elements to filter.
 * @param array $keys The array of keys to keep.
 * @return array The filtered array of elements.
 */
function filterByKeys(array $elements, array $keys): array
{
  $result = [];
  foreach ($elements as $element) {
    $newElement = [];
    foreach ($element as $key => $value) {
      if (in_array($key, array_values($keys[0]))) {
        $newElement[$key] = $value;
      }
    }
    $result[] = $newElement;
  }
  return $result;
}


/**
 * Adds missing keys to each product in the array by setting their value to a default value.
 *
 * @param array $products An array of products.
 * @param array $values An array of keys to add to each product.
 * @return array An array of products with the missing keys added.
 */
function addMissingKeys(array $products, array $missingKeys): array {
  $result = [];
  foreach ($products as $product) {
    $newProduct = $product;
    foreach ($missingKeys[0] as $key => $value) {
      if (!isset($newProduct[$value])) {
        $newProduct[$value] = $missingKeys[1][$key];
      }
    }
    $result[] = $newProduct;
  }
  return $result;
}