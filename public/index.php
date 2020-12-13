<?php

use App\NumberHelper;
use App\QueryBuilder;
use App\TableHelper;
use App\URLHelper;

define('PER_PAGE', 20);

require '../vendor/autoload.php';


$pdo = new PDO("sqlite:../products.db", null, null, [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$query = (new QueryBuilder($pdo))->from('products');
$sortable = ["id", "name", "city", "price"];

// Search by city
if (!empty($_GET['q'])) {
    $query
        ->where('city LIKE :city')
        ->setParam('city', '%' . $_GET['q'] . '%');
}

$count = (clone $query)->count();

// Sorting
if (!empty($_GET['sort']) && in_array($_GET['sort'], $sortable)) {
   $query->orderBy($_GET['sort'], $_GET['dir'] ?? 'asc');
}

// Pagination
$page = $_GET['p'] ?? 1;
$query
    ->limit(PER_PAGE)
    ->page($page);

$products = $query->fetchAll();

$pages = ceil($count / PER_PAGE);
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
        <input type="text" class="form-control" name="q" placeholder="Enter a city"
               value="<?= htmlentities($_GET['q'] ?? null) ?>">
    </div>
    <button class="btn btn-primary">Search</button>
</form>

<table class="table table-striped">
    <thead>
    <tr>
        <th><?= TableHelper::sort('id', 'ID', $_GET) ?></th>
        <th><?= TableHelper::sort('name', 'Name', $_GET) ?></th>
        <th><?= TableHelper::sort('price', 'Price', $_GET) ?></th>
        <th><?= TableHelper::sort('city', 'City', $_GET) ?></th>
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
<?php if ($pages > 1 && $page > 1): ?>
    <a href="?<?= URLHelper::withParam($_GET, "p", $page - 1) ?>" class="btn btn-primary">Previous page</a>
<?php endif ?>
<?php if ($pages > 1 && $page < $pages) : ?>
    <a href="?<?= URLHelper::withParam($_GET, "p", $page + 1) ?>" class="btn btn-primary">Next page</a>
<?php endif ?>
</body>
</html>