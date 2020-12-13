<?php

use App\NumberHelper;
use App\QueryBuilder;
use App\Table;

define('PER_PAGE', 20);

require '../vendor/autoload.php';

$pdo = new PDO("sqlite:../products.db", null, null, [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$query = (new QueryBuilder($pdo))->from('products');

// Search by city
if (!empty($_GET['q'])) {
    $query
        ->where('city LIKE :city')
        ->setParam('city', '%' . $_GET['q'] . '%');
}

$table = (new Table($query, $_GET))
->setColumns([
    'id' => 'ID',
    'name' => 'Name',
    'city' => 'City',
    'address' => 'Address',
    'price' => 'Price'
])
->sortable('id', 'city')
->format('price', function ($value) {
    return NumberHelper::price($value);
})
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

<?php $table->render() ?>

</body>
</html>