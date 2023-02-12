<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 0);
define('URL', 'https://detali.org.ua');
include_once 'functions.php';
require 'vendor/autoload.php';
require 'phpquery.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$spreadsheet = new Spreadsheet();
$writer = new Xlsx($spreadsheet);

$request = requests($_POST['name']);
$output = phpQuery::newDocument($request);
//назва категорії для формування назви файла
$category_name = $output->find('.cat-name')->text();
$category_name_mod = str_replace(" ", "_", $category_name);
echo $category_name;
echo $category_name_mod;


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
parse_product($all_products_links_array);



phpQuery::unloadDocuments();
//функція додання даних в таблицю
pull_data_sheet($product, $spreadsheet, $category_name, $writer, $category_name_mod);
  

































// $all_product_links = $output->find('.product-name-container a');
// foreach ($all_product_links as $key => $value){
//   $pq2 = pq($value);
// $all_products_links_array[] = $pq2->attr("href");
// }
// format($all_products_links_array);



// foreach ($all_products_links_array as $key => $value) {
//     $request_all_product = requests($value);
//     $output_all_product = phpQuery::newDocument($request_all_product);
//     $product_name = $output_all_product->find('.product-title h1');
//     $product_name = $product_name->html();

//     $product_sku = $output_all_product->find('.editable');
//     $product_sku = $product_sku->html();
//   echo $product_sku.'<br>';
//     $product_price = $output_all_product->find('#our_price_display');
//     $product_price = $product_price->text();
//   $product_price = str_replace(' ₴', "", $product_price);
//   $product_price = str_replace(' ', "", $product_price);
//   $product_price = round($product_price);


//   $product_picture = $output_all_product->find('.MagicToolboxContainer img');
//   foreach ($product_picture as $link) {
//     $pqlink = pq($link);
//     $product_picture_arr[] = $pqlink->attr("src");
//     $product_picture_arr = str_replace('small_default', 'large_default', $product_picture_arr);
//   }
//   $product_picture = implode($product_picture_arr, ';');

//     $product[$key]['sku'] = $product_sku;
//     $product[$key]['name'] = $product_name;
//     $product[$key]['price'] = $product_price;
//     $product[$key]['picture'] = $product_picture;
// }
// phpQuery::unloadDocuments();
// //функція додання даних в таблицю
// pull_data_sheet($product, $spreadsheet, $category_name, $writer, $category_name_mod);
































