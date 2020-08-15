<?php
session_start();

require_once './controllers/TestController.php';
require_once './models/Model.php';

$test = new \Controllers\TestController();
$db = new \Models\Model();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="/assets/style.css">
    <script src="/assets/script.js"></script>
</head>

<body>

    <div class="container">

        <nav class="navbar navbar-light bg-light">
            <form class="form-inline">
                <a href="/" class="btn btn-primary  mr-sm-2" type="button">Каталог</a>
            </form>
        </nav>

        <div class="row">
            <div class="col-md-12">

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">ID</th>
                            <th scope="col">Наименование</th>
                            <th scope="col">Цена</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        $cart = $test->getCart();

                        $i = 1;
                        foreach($cart as $item):
                        ?>
                        <tr>
                            <th scope="row"><?=$i++?></th>
                            <td><?=$item['id']?></td>
                            <td><?=$item['name']?></td>
                            <td><?=$item['price']?></td>
                        </tr>
                        <?php
                        endforeach;
                        ?>

                    </tbody>
                </table>

                <?php
                if($cart):
                ?>
                <button type="button" class="btn btn-success order-button">Заказать</button>
                <?php
                else:?>
                    <div class="alert alert-warning" role="alert">Ваша корзина пуста</div>
                <?php
                endif;
                ?>
            </div>
        </div>
    </div>

</body>
</html>

