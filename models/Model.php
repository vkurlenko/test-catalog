<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 12.08.2020
 * Time: 20:20
 */

namespace Models;


class Model
{
    public $db_name = 'test';
    public $db_user = 'root';
    public $db_password = '';
    public $db_host = 'localhost';
    public $db;

    public function __construct()
    {
        $mysqli = new \mysqli($this->db_host, $this->db_user, $this->db_password, $this->db_name);

        if ($mysqli -> connect_error) {
            printf("Соединение не удалось: %s\n", $mysqli -> connect_error);
            exit();
        };

        $this->db = $mysqli;
    }

    /**
     * Запись в БД категорий
     *
     * @param array $categories
     */
    public function setCategories($categories = [])
    {
        $count = 0;

        foreach($categories as $row)
        {
            if($this->addCategory($row))
            {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param array $row
     */
    public function addCategory($row = [])
    {
        $parent_id = $row['parent_id'] ? $row['parent_id'] : 0;

        $sql = "insert into `goods` (id, pid, name) values ({$row['id']}, {$parent_id}, '{$row['name']}')";

        return mysqli_query($this->db, $sql);
    }

    /**
     * Запись в БД товаров
     *
     * @param array $products
     */
    public function setProducts($products = [])
    {
        $count = 0;

        foreach($products as $row)
        {
            if($this->addProduct($row)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param array $row
     */
    public function addProduct($row = [])
    {
        $cid = $row['cid'] ? $row['cid'] : 0;

        $sql = "insert into `goods` (id, pid, name, price) values ({$row['id']}, {$cid}, '{$row['name']}', {$row['price']})";

        return mysqli_query($this->db, $sql);
    }

    /**
     * Чтение из БД всех записей (категории + товары)
     *
     * @return bool|\mysqli_result
     */
    public function getGoods()
    {
        $sql = "SELECT * FROM goods ORDER BY name ASC";

        $res = mysqli_query($this->db, $sql);

        return $res;
    }

    /**
     * Поиск товара по ID
     *
     * @param null $id
     * @return array|bool|null
     */
    public function findById($id = null)
    {
        if(!$id) {
            return false;
        }

        $sql = "SELECT * FROM `goods` WHERE id = {$id}";
        $res = mysqli_query($this->db, $sql);

        if($res) {
            return mysqli_fetch_assoc($res);
        }

        return false;
    }

    /**
     * Запись заказа в БД (возвращает ID заказа или false)
     *
     * @param string $order
     * @return bool|int|string
     */
    public function saveOrder($order = '')
    {
        if(!$order) {
            return false;
        }

        $sql = "INSERT INTO `orders` (items) VALUES ('{$order}')";

        //return $sql;
        $res = mysqli_query($this->db, $sql);

        if($res) {
            return mysqli_insert_id($this->db);
        }

        return false;
    }
}