<?php
require 'auth.php';

if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $st = $_POST['status'] ?? '';
    if (in_array($st, ['Pending', 'Shipped', 'Delivered', 'Cancelled'])) {
        mysqli_query($conn, "UPDATE orders SET status = '$st' WHERE id = $id");
        flash("Order #$id marked as $st.");
    }
    header("Location: orders.php");
    exit;
}

$orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY id DESC");
$active = 'orders';
include 'header.php';
?>
<h1>Orders</h1>
<?php if (mysqli_num_rows($orders) == 0) { ?><div class="empty"><p>No orders have been placed yet.</p></div><?php } ?>
<?php while ($o = mysqli_fetch_assoc($orders)) {
    $oid = (int)$o['id'];
    $its = mysqli_query($conn, "SELECT oi.*, p.name FROM order_items oi LEFT JOIN products p ON p.id = oi.product_id WHERE oi.order_id = $oid");
?>
<section class="order-card">
  <div class="order-head">
    <div><b>Order #<?= $oid ?> &middot; <?= e($o['customer_name']) ?></b>
      <small><?= e($o['phone']) ?> &middot; <?= e(date('d M Y, h:i A', strtotime($o['order_date']))) ?> &middot; <?= e($o['payment_method']) ?></small></div>
    <form method="post" class="status-form">
      <input type="hidden" name="id" value="<?= $oid ?>">
      <select name="status">
        <?php foreach (['Pending', 'Shipped', 'Delivered', 'Cancelled'] as $s) { ?>
          <option <?= $o['status'] == $s ? 'selected' : '' ?>><?= $s ?></option>
        <?php } ?>
      </select>
      <button type="submit" name="update" class="btn btn-sm">Update</button>
    </form>
  </div>
  <p class="addr">Address: <?= e($o['address']) ?></p>
  <?php while ($it = mysqli_fetch_assoc($its)) { ?>
    <div class="line"><span><?= e($it['name'] ?? 'Removed product') ?> x <?= (int)$it['quantity'] ?></span><b><?= money($it['price'] * $it['quantity']) ?></b></div>
  <?php } ?>
  <div class="order-foot"><span class="status st-<?= strtolower(e($o['status'])) ?>"><?= e($o['status']) ?></span><b>Total <?= money($o['total']) ?></b></div>
</section>
<?php } ?>
<?php include 'footer.php'; ?>
