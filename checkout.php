<?php
require 'db.php';

if (empty($_SESSION['cart'])) { header("Location: cart.php"); exit; }
if (!isset($_SESSION['user_id'])) {
    flash('Please log in to place your order.');
    header("Location: login.php?next=checkout.php");
    exit;
}

list($items, $sub) = cart_items($conn);
$delivery = delivery_fee($sub);
$total = $sub + $delivery;
$uid = (int)$_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, phone FROM users WHERE id = $uid"));
$err = '';

if (isset($_POST['place'])) {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $pay     = $_POST['payment'] ?? 'Cash on Delivery';
    if (!in_array($pay, ['Cash on Delivery', 'UPI', 'Card'])) $pay = 'Cash on Delivery';

    if ($name === '' || $address === '' || !preg_match('/^[0-9]{10}$/', $phone)) {
        $err = 'Please enter your name, a 10-digit phone number and a delivery address.';
    } else {
        $n = mysqli_real_escape_string($conn, $name);
        $ph = mysqli_real_escape_string($conn, $phone);
        $ad = mysqli_real_escape_string($conn, $address);
        mysqli_query($conn, "INSERT INTO orders (user_id, customer_name, phone, address, payment_method, total)
                             VALUES ($uid, '$n', '$ph', '$ad', '$pay', $total)");
        $oid = mysqli_insert_id($conn);
        foreach ($items as $p) {
            $pid = (int)$p['id']; $qty = (int)$p['qty']; $pr = (float)$p['price'];
            mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($oid, $pid, $qty, $pr)");
            mysqli_query($conn, "UPDATE products SET stock = GREATEST(stock - $qty, 0) WHERE id = $pid");
        }
        unset($_SESSION['cart']);
        flash("Order #$oid placed successfully. Thank you for shopping with us!");
        header("Location: orders.php");
        exit;
    }
}

$title = 'Checkout';
include 'includes/header.php';
?>
<main class="container">
  <h2 class="sec-title">Checkout</h2>
  <div class="cart-layout">
    <form method="post" class="form-card" onsubmit="return validateCheckout();">
      <h3>Delivery details</h3>
      <?php if ($err) { ?><p class="error"><?= e($err) ?></p><?php } ?>
      <label for="name">Full name</label>
      <input type="text" id="name" name="name" value="<?= e($_POST['name'] ?? $user['name']) ?>">
      <label for="phone">Phone number</label>
      <input type="text" id="phone" name="phone" maxlength="10" value="<?= e($_POST['phone'] ?? $user['phone']) ?>">
      <label for="address">Delivery address</label>
      <textarea id="address" name="address"><?= e($_POST['address'] ?? '') ?></textarea>

      <h3>Payment method</h3>
      <label class="radio"><input type="radio" name="payment" value="Cash on Delivery" checked> Cash on delivery</label>
      <label class="radio"><input type="radio" name="payment" value="UPI"> UPI (pay when the order arrives)</label>
      <label class="radio"><input type="radio" name="payment" value="Card"> Card on delivery</label>

      <button type="submit" name="place" class="btn btn-lg btn-block">Place order &middot; <?= money($total) ?></button>
    </form>

    <aside class="summary">
      <h3>Your items</h3>
      <?php foreach ($items as $p) { ?>
        <div class="mini">
          <img src="<?= e(img($p)) ?>" alt="">
          <span><?= e($p['name']) ?> <small>x <?= $p['qty'] ?></small></span>
          <b><?= money($p['price'] * $p['qty']) ?></b>
        </div>
      <?php } ?>
      <div class="sum-row"><span>Subtotal</span><span><?= money($sub) ?></span></div>
      <div class="sum-row"><span>Delivery</span><span><?= $delivery == 0 ? 'Free' : money($delivery) ?></span></div>
      <div class="sum-row total"><span>Total</span><span><?= money($total) ?></span></div>
    </aside>
  </div>
</main>
<?php include 'includes/footer.php'; ?>
