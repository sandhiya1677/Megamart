<?php
require __DIR__ . '/../db.php';

if (isset($_GET['logout'])) {
    unset($_SESSION['admin']);
    header("Location: index.php");
    exit;
}

$err = '';
if (isset($_POST['login'])) {
    if (($_POST['username'] ?? '') === 'admin' && ($_POST['password'] ?? '') === 'admin123') {
        $_SESSION['admin'] = true;
        header("Location: index.php");
        exit;
    }
    $err = 'Wrong username or password.';
}

if (!isset($_SESSION['admin'])) { ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin login | MegaMart</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-login">
  <form method="post" class="form-card">
    <div class="logo center">Mega<span>Mart</span></div>
    <h2>Admin login</h2>
    <?php if ($err) { ?><p class="error"><?= e($err) ?></p><?php } ?>
    <label for="u">Username</label>
    <input type="text" id="u" name="username" required>
    <label for="p">Password</label>
    <input type="password" id="p" name="password" required>
    <button type="submit" name="login" class="btn btn-lg btn-block">Log in</button>
    <p class="alt-link"><a href="../index.php">Back to shop</a></p>
  </form>
</body>
</html>
<?php
    exit;
}

$products = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0];
$orders   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0];
$users    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
$revenue  = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'Cancelled'"))[0];
$pending  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE status = 'Pending'"))[0];
$recent   = mysqli_query($conn, "SELECT * FROM orders ORDER BY id DESC LIMIT 6");
$low      = mysqli_query($conn, "SELECT id, name, stock FROM products WHERE stock <= 10 ORDER BY stock ASC LIMIT 6");

$active = 'dash';
include 'header.php';
?>
<h1>Dashboard</h1>
<div class="stats">
  <div class="stat"><span>Revenue</span><b><?= money($revenue) ?></b></div>
  <div class="stat"><span>Orders</span><b><?= $orders ?></b><small><?= $pending ?> pending</small></div>
  <div class="stat"><span>Products</span><b><?= $products ?></b></div>
  <div class="stat"><span>Customers</span><b><?= $users ?></b></div>
</div>

<div class="two-col">
  <div>
    <h3>Recent orders</h3>
    <table>
      <tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th></tr>
      <?php if (mysqli_num_rows($recent) == 0) { ?><tr><td colspan="4">No orders yet.</td></tr><?php } ?>
      <?php while ($o = mysqli_fetch_assoc($recent)) { ?>
      <tr>
        <td>#<?= $o['id'] ?></td>
        <td><?= e($o['customer_name']) ?></td>
        <td><?= money($o['total']) ?></td>
        <td><span class="status st-<?= strtolower(e($o['status'])) ?>"><?= e($o['status']) ?></span></td>
      </tr>
      <?php } ?>
    </table>
  </div>
  <div>
    <h3>Low stock</h3>
    <table>
      <tr><th>Product</th><th>Left</th></tr>
      <?php if (mysqli_num_rows($low) == 0) { ?><tr><td colspan="2">All products are well stocked.</td></tr><?php } ?>
      <?php while ($l = mysqli_fetch_assoc($low)) { ?>
      <tr><td><a href="products.php?edit=<?= $l['id'] ?>"><?= e($l['name']) ?></a></td><td><?= (int)$l['stock'] ?></td></tr>
      <?php } ?>
    </table>
  </div>
</div>
<?php include 'footer.php'; ?>
