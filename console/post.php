<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 12.08.2020
 * Time: 20:01
 */

require_once './controllers/TestController.php';
require_once './models/Model.php';

$transactionID = time();

$request = new \Controllers\TestController();
$db = new \Models\Model();

// Загрузим категории
$response = $request->post('GetCategories', 'GetCategories', $transactionID);
$arr = $request->parseCategories($response);
$result = $db->setCategories($arr);
echo sprintf('Сохранено записей категорий: %s', $result);
//die;

// Загрузим товары
$response = $request->post('GetProduct', 'GetProduct', $transactionID);
$arr = $request->parseProducts($response);
$result = $db->setProducts($arr);
echo sprintf('Сохранено записей товаров: %s', $result);
//var_dump($arr); //die;
