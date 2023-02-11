<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 0);
include_once 'functions.php';
require 'vendor/autoload.php';
require 'phpquery.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

define('URL', 'https://detali.org.ua');
$request = requests('https://detali.org.ua/uk/642-zapchasti-dlya-svarki');
$output = phpQuery::newDocument($request);
$menu = $output->find('.top-pagination-content a');
foreach ($menu as $key => $value){
  $pq = pq($value);
  $src_menu[$key] = $pq->attr("href");
}
//назва категорії для формування назви файла
$category_name = $output->find('.cat-name')->text();
$category_name = str_replace(" ", "_", $category_name);
echo $category_name;

//удаляємо останній елемент пагинаці. Він веде на 2 сторінку
$trash_page = array_pop($src_menu);
//беремо в масиві останю сторінку
$last_page_url =  end($src_menu);
//розбиваємо останнню сторінку на =
$last_page_number_array = explode("=", $last_page_url);
//дістаємо номер останьої сторінки
$last_page_number= $last_page_number_array[1];
//дістаємо всі можливі сторінки пагінації в змінну $full_url
for ($n = $last_page_number; $n > 0; $n--) {
$full_url = URL.$last_page_number_array[0] . "=". $n;
// echo $full_url. '<br>';
$request_all_pages_paginagination = requests($full_url);
$output_all_pages_paginagination = phpQuery::newDocument($request_all_pages_paginagination);
$all_product_links = $output_all_pages_paginagination->find('.product-name-container a');
foreach ($all_product_links as $key => $value){
  $pq2 = pq($value);
$all_products_links_array[] = $pq2->attr("href");
}
}
foreach ($all_products_links_array as $key => $value) {
    $request_all_product = requests($value);
    $output_all_product = phpQuery::newDocument($request_all_product);
    $product_name = $output_all_product->find('.product-title h1');
    $product_name = $product_name->html();

    $product_sku = $output_all_product->find('.editable');
    $product_sku = $product_sku->html();

    $product_price = $output_all_product->find('#our_price_display');
    $product_price = $product_price->text();
  $product_price = str_replace(' ₴', "", $product_price);
  $product_price = str_replace(' ', "", $product_price);
  $product_price = round($product_price);


  $product_picture = $output_all_product->find('.MagicToolboxContainer img');
  foreach ($product_picture as $link) {
    $pqlink = pq($link);
    $product_picture_arr[] = $pqlink->attr("src");
    $product_picture_arr = str_replace('small_default', 'large_default', $product_picture_arr);
  }

  $product_picture = implode($product_picture_arr, ';');




    $product[$key]['sku'] = $product_sku;
    $product[$key]['name'] = $product_name;
    $product[$key]['price'] = $product_price;
    $product[$key]['picture'] = $product_picture;


}
phpQuery::unloadDocuments();
  
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
foreach ($product as $key => $value) {
  $product_sku_item = $product[$key]["sku"];
  $product_name_item = $product[$key]["name"];
  $product_price_item = $product[$key]["price"];
  $product_picture_item = $product[$key]["picture"];

$sheet->setCellValue('A'. $key, $product_sku_item); 
$sheet->setCellValue('B'. $key, $product_name_item); 
$sheet->setCellValue('C'. $key, $product_price_item);
$sheet->setCellValue('D'. $key, $product_picture_item);




}
$writer = new Xlsx($spreadsheet);
$writer->save($category_name.'.'.'xlsx');