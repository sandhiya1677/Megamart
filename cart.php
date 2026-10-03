<?php
require 'db.php';

if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id, stock FROM products WHERE id = $id"));
    if ($r && $r['stock'] > 0) {
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
        flash('Added to your cart.');
    }
    if (isset($_GET['go']) && $_GET['go'] === 'checkout') { header("Location: checkout.php"); exit; }
    $back = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    header("Location: " . $back);
    exit;
}
if (isset($_GET['inc'])) {
    $id = (int)$_GET['inc'];
    if (isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id]++;
    header("Location: cart.php"); exit;
}
if (isset($_GET['dec'])) {
    $id = (int)$_GET['dec'];
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]--;
        if ($_SESSION['cart'][$id] <= 0) unset($_SESSION['cart'][$id]);
    }
    header("Location: cart.php"); exit;
}
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][(int)$_GET['remove']]);
    flash('Item removed from your cart.');
    header("Location: cart.php"); exit;
}
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php"); exit;
}

list($items, $sub) = cart_items($conn);
$delivery = delivery_fee($sub);
$total = $sub + $delivery;
$title = 'Your cart';
include 'includes/header.php';
?>
<main class="container">
  <h2 class="sec-title">Your cart</h2>

  <?php if (empty($items)) { ?>
    <div class="empty">
      <p>Your cart is empty.</p>
      <a class="btn" href="index.php">Browse products</a>
    </div>
  <?php } else { ?>
  <div class="cart-layout">
    <div class="cart-list">
      <?php foreach ($items as $p) { ?>
      <div class="cart-row">
        <a href="product.php?id=<?= $p['id'] ?>"><img src="<?= e(img($p)) ?>" alt=""></a>
        <div class="cart-info">
          <a href="product.php?id=<?= $p['id'] ?>"><b><?= e($p['name']) ?></b></a>
          <span class="cat"><?= e($p['category']) ?></span>
          <span class="price"><?= money($p['price']) ?></span>
        </div>
        <div class="qty">
          <a href="cart.php?dec=<?= $p['id'] ?>" aria-label="Decrease">&minus;</a>
          <span><?= $p['qty'] ?></span>
          <a href="cart.php?inc=<?= $p['id'] ?>" aria-label="Increase">+</a>
        </div>
        <div class="line-total"><?= money($p['price'] * $p['qty']) ?></div>
        <a class="remove" href="cart.php?remove=<?= $p['id'] ?>" onclick="return confirm('Remove this item?');">Remove</a>
      </div>
      <?php } ?>
      <a class="link-muted" href="cart.php?clear=1" onclick="return confirm('Empty your cart?');">Empty cart</a>
    </div>

    <aside class="summary">
      <h3>Order summary</h3>
      <div class="sum-row"><span>Subtotal</span><span><?= money($sub) ?></span></div>
      <div class="sum-row"><span>Delivery</span><span><?= $delivery == 0 ? 'Free' : money($delivery) ?></span></div>
      <?php if ($delivery > 0) { ?><p class="hint">Add <?= money(999 - $sub) ?> more for free delivery.</p><?php } ?>
      <div class="sum-row total"><span>Total</span><span><?= money($total) ?></span></div>
      <a class="btn btn-lg btn-block" href="checkout.php">Go to checkout</a>
      <a class="link-muted center" href="index.php">Continue shopping</a>
    </aside>
  </div>
  <?php } ?>
</main>
<?php include 'includes/footer.php'; ?>
