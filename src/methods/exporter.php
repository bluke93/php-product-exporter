<?php 

/**
 * Exports an array of data to a CSV file.
 *
 * @param string $filename The name of the file to export to.
 * @param array $data The data to export.
 * @param string $path The path to the directory where the file will be saved.
 * @return void.
 */
function exportToCsv($filename, $data, $path) {
  // Create the full file path
  $fullPath = $path . '/' . $filename;

  // Open file for writing
  $file = fopen($fullPath, 'w');

  if(count($data) > 0) {
    // Write header row
    fputcsv($file, array_keys($data[0]));

    // Write data rows
    foreach ($data as $row) {
      fputcsv($file, $row);
    }
  }

  // Close file
  fclose($file);
}


