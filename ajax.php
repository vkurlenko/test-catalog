<?php
session_start();

require_once './controllers/TestController.php';
require_once './models/Model.php';

$test = new \Controllers\TestController();
$db = new \Models\Model();

if($_POST['id']) {
    echo $test->addItem($_POST['id']);
} elseif($_POST['action']) {

    switch ($_POST['action']) {

        case 'set-order':
            echo $test->setOrder();
            break;

        default: break;
    }
} else {
    echo 'No ID';
}

