<?php

use App\NumberHelper;

require 'vendor/autoload.php';
$pdo = new PDO("sqlite:./products.db", null, null, [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$query = "SELECT * FROM products";
$params = [];

// Search by city
if (!empty($_GET['q'])) {
    $query .= " WHERE city LIKE :city";
    $params['city'] = "%" . $_GET['q'] . "%";
}
$query .= " LIMIT 20";

$statement = $pdo->prepare($query);
$statement->execute($params);
$products = $statement->fetchAll();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
          integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <title>Real estate</title>
</head>
<body class="p-4">

<h1>Real estate offers</h1>
<form action="" class="mb-4">
    <div class="form-group">
        <input type="text" class="form-control" name="q" placeholder="Enter a city" value="<?= htmlentities($_GET['q']) ?? null ?>">
    </div>
    <button class="btn btn-primary">Search</button>
</form>

<table class="table table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Price</th>
        <th>City</th>
        <th>Address</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($products as $product): ?>
        <tr>
            <td>#<?= $product['id'] ?></td>
            <td><?= $product['name'] ?></td>
            <td><?= NumberHelper::price($product['price']) ?></td>
            <td><?= $product['city'] ?></td>
            <td><?= $product['address'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>