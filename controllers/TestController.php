<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 12.08.2020
 * Time: 18:58
 */

namespace Controllers;

class TestController
{
    public $login = 'vitrina_test';
    public $password = 'Bhsue3Vf5';
    public $url = 'https://test.mgc-loyalty.ru/v1/';

    public function __construct()
    {
        if(!isset($_SESSION['order'])) {
            $_SESSION['order'] = [];
        }
    }

    /**
     * Генерация Hash
     * MD5 строка из TransactionID . MethodName . Login . Password
     *
     * @param $methodName
     * @param $transactionID
     * @return string
     */
    public function getHash($methodName, $transactionID)
    {
        return md5($transactionID.$methodName.$this->login.$this->password);
    }

    /**
     * Генерация XML-запроса
     *
     * @param $methodName
     * @param $transactionID
     * @return string
     */
    public function getRequest($methodName, $transactionID)
    {
        $hash = $this->getHash($methodName, $transactionID);

        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<Request>
   <Authentication>
       <Login>{$this->login}</Login>
       <TransactionID>{$transactionID}</TransactionID>
       <MethodName>{$methodName}</MethodName>
       <Hash>{$hash}</Hash>
   </Authentication> 
   <Parameters>   
   </Parameters>  
</Request>
XML;

        return $xml;
    }


    /**
     * Собственно запрос
     *
     * @param $endpoint
     * @param $methodName
     * @param int $transactionID
     * @return mixed
     */
    public function post($endpoint, $methodName, $transactionID = 1)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,            $this->url.$endpoint );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_POST,           1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $this->getRequest($methodName, $transactionID) );
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain'));

        $result=curl_exec ($ch);

        return $result;
    }

    /**
     * Парсинг ответа
     *
     * @param $xml
     * @return \SimpleXMLElement
     */
    public function parseXML($xml)
    {
        return simplexml_load_string($xml);
    }

    /**
     * Формирование массива данных для сохранения в таблицу категорий
     *
     * @param $xml
     * @return array
     */
    public function parseCategories($xml)
    {
        $arr = [];
        $xml = $this->parseXML($xml);

        foreach($xml->Categories->Category as $category) {
            $arr[] = [
                'id' => $category->attributes()->id,
                'parent_id' => $category->attributes()->parentId,
                'name' => $category->name
            ];
        }

        return $arr;
    }

    /**
     * Формирование массива данных для сохранения в таблицу продуктов
     *
     * @param $xml
     * @return array
     */
    public function parseProducts($xml)
    {
        $arr = [];
        $xml = $this->parseXML($xml);

        foreach($xml->Products->Product as $product) {
            $arr[] = [
                'id' => $product->Id,
                'cid' => $product->Category->attributes()->id,
                'name' => $product->Name,
                'price' => $product->Price
            ];
        }

        return $arr;
    }

    /**
     * Построим каталог в виде дерева
     */
    public function getTree()
    {
        $db = new \Models\Model();

        $res = $db->getGoods();

        foreach($res as $row)
        {
            $arr[] = $row;
        }

        $tree = $this->form_tree($arr);

        echo $this->build_tree($tree, 0);
    }

    public function form_tree($mess)
    {
        if (!is_array($mess)) {
            return false;
        }
        $tree = array();
        foreach ($mess as $value) {
            $tree[$value['pid']][] = $value;
        }
        return $tree;
    }


    public function build_tree($cats, $parent_id)
    {
        if (is_array($cats) && isset($cats[$parent_id])) {
            $tree = '<ul '.(!$parent_id ? "class='root'" : "class='list-group'").'>';
            foreach ($cats[$parent_id] as $cat) {
                $tree .= '<li class="list-group-item"><span>' .'['.$cat['id'].'] '.$cat['name'];
                $tree .= $this->getPriceString($cat);
                $tree .= '</span>';
                $tree .= $this->build_tree($cats, $cat['id']);
                $tree .= '</li>';
            }
            $tree .= '</ul>';
        } else {
            return false;
        }
        return $tree;
    }

    /**
     * Формирование строки Цена
     *
     * @param array $cat
     * @return string
     */
    public function getPriceString($cat = [])
    {
        if(!$cat || !$cat['price']) {
            return '';
        }

        $in_cart  = key_exists($cat['id'], $_SESSION['order']) ? true : false;

        $string = sprintf('&nbsp;<span class="price btn btn-secondary btn-sm">&#8381; %s</span>&nbsp;<a class="item %s btn btn-%s btn-sm" href="#" id="%s">%s</a>',
            $cat['price'],
            ($in_cart ? ' in-cart' : ''),
            ($in_cart ? 'danger' : 'success'),
            $cat['id'],
            ($in_cart ? 'удалить из корзины' : 'в корзину')
        );

        return $string;
    }

    /**
     * Добавить товар в корзину
     *
     * @param null $id
     * @return null
     */
    public function addItem($id = null)
    {
        if(!$id) {
            return null;
        }

        if(!key_exists($id, $_SESSION['order'])) {
            $db = new \Models\Model();
            $_SESSION['order'][$id] = $db->findById($id);
        } else {
            unset($_SESSION['order'][$id]);
        }

        return $id;
    }

    /**
     * Получить корзину
     *
     * @return array
     */
    public function getCart()
    {
        if(!isset($_SESSION['order'])) {
            return [];
        }

        return $_SESSION['order'];
    }

    /**
     * Создать заказ
     *
     * @return bool|int|string
     */
    public function setOrder()
    {
        if(!isset($_SESSION['order'])) {
            return false;
        }

        $db = new \Models\Model();

        $order_id = $db->saveOrder(serialize($_SESSION['order']));

        if($order_id) {
            $_SESSION['order'] = [];
        }

        return $order_id;
    }

    public function dump($arr = [])
    {
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }
}



