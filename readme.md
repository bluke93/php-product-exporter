# PHP Product Exporter (beta)

- [Overview](#overview)
- [How to use](#how-to-use)
- [Structure](#structure)
  - [Requesting](#requesting)
  - [Importing](#importing)
  - [Processing](#processing)
  - [Exporting](#exporting)
- [Configuration](#configuration)
- [Output](#output)

## Overview

This PHP script is designed to export data from a CRM and upload it to a WordPress site that functions as an ecommerce platform. The script is divided into four parts: requesting, importing, processing, and exporting. Each part has its own methods that manage the different aspects of the overall process. By dividing the script into these parts, it becomes easier to manage and maintain the codebase. This script is written in PHP.

## How to use

To execute the script is simply necessary to make a `GET` request to the `/src/main.php` file. It can accept a parameter in querystring to determine the `startDate` which the API will fetch the products from.

Using it without parameter will cause the script to use the current date.
> http://localhost:3000/src/main.php

Using it with the `startDate` parameter will cause the script to fetch the product from that date onwards.
> http://localhost:3000/src/main.php?startDate=2023-05-01

Make sure that the files `/src/config/exportKeys.csv` and `/src/config/remap.csv` exists, they contains the required data to remap the product list from the CRM to the Wordpress.

Once the process is finished, the latest created CSV file will be located in `src/dist` ready to be downloaded and imported into the wordpress platform.

## Structure

### Requesting

The requesting part is responsible for making requests to external APIs or services. This is where the script retrieves the necessary data from the CRM.

### Importing

The importing part is responsible for importing data from external sources. This is where the script takes the data retrieved from some configuration CSVs and prepares it for processing.

### Processing

The processing part is responsible for processing the data in various ways. This is where the script manipulates the data to fit the format required by the destination platform.

### Exporting

The exporting part is responsible for exporting the processed data to external sources or services. This is where the script releases a CSV file ready to be imported into the wordpress platform.


## Configuration

All the configuration is stored in the `/src/config` folder.

- `constants.php` contains some setup data to configure api calls, paths and parameters.
- `exportKeys.csv` its a core file which can be edited accordingly to provide the required final keys for the exported file.
- `remap.csv` its a core file which can be edited to map from the CRM keys to the wordpress keys.


## Output

All the exported CSV are located in the `/src/dist` folder. The exported files are named with current datetime (`d-m-Y_his`) of when has been generated.

> 22-10-2023_174818.csv

## Extending the script

Coming soon...

