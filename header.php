<?php $active = $active ?? ''; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | MegaMart</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <script src="../assets/js/script.js" defer></script>
</head>
<body class="admin">
<aside class="sidebar">
  <div class="logo">Mega<span>Mart</span><small>Admin</small></div>
  <a class="<?= $active == 'dash' ? 'on' : '' ?>" href="index.php">Dashboard</a>
  <a class="<?= $active == 'products' ? 'on' : '' ?>" href="products.php">Products</a>
  <a class="<?= $active == 'orders' ? 'on' : '' ?>" href="orders.php">Orders</a>
  <a href="../index.php" target="_blank">View shop</a>
  <a href="index.php?logout=1">Log out</a>
</aside>
<section class="admin-main">
<?php show_flash(); ?>
