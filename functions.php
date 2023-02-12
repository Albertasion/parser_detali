<?php
//функция форматування
function format ($expre) {
    echo "<pre>";
    print_r($expre);
    echo "</pre>";
  }
  //функція запроса
function requests ($url, $cookiefile = '/Applications/MAMP/bin/mamp/cookie.txt') {
$ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $url);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
 curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36');
$output = curl_exec($ch);
curl_close($ch);
return $output;
}


function parse_product($all_products_links_array)
{

  foreach ($all_products_links_array as $key => $value) {
    $request_all_product = requests($value);
    $output_all_product = phpQuery::newDocument($request_all_product);
    $product_name = $output_all_product->find('.product-title h1');
    $product_name = $product_name->html();

    $product_sku = $output_all_product->find('.editable');
    $product_sku = $product_sku->html();
    echo $product_sku . '<br>';
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
}
























function pull_data_sheet ($product, $spreadsheet, $category_name, $writer, $category_name_mod) {
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
$sheet->setCellValue('E'. $key, $category_name);
}
$writer->save($category_name_mod.'.'.'xlsx');
}