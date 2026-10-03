<?php
require 'db.php';
$id = (int)($_GET['id'] ?? 0);
$res = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
$p = mysqli_fetch_assoc($res);
if (!$p) { flash('That product was not found.'); header("Location: index.php"); exit; }

$title = $p['name'];
$d = discount($p);
$cs = mysqli_real_escape_string($conn, $p['category']);
$related = mysqli_query($conn, "SELECT * FROM products WHERE category = '$cs' AND id != $id LIMIT 4");

include 'includes/header.php';
?>
<main class="container">
  <p class="crumbs"><a href="index.php">Home</a> / <a href="index.php?cat=<?= urlencode($p['category']) ?>"><?= e($p['category']) ?></a> / <?= e($p['name']) ?></p>

  <section class="detail">
    <div class="detail-img"><img src="<?= e(img($p)) ?>" alt="<?= e($p['name']) ?>"></div>
    <div class="detail-info">
      <span class="cat"><?= e($p['category']) ?></span>
      <h1><?= e($p['name']) ?></h1>
      <div class="stars"><?= stars($p['rating']) ?> <small><?= e($p['rating']) ?> out of 5</small></div>
      <div class="price-row big">
        <span class="price"><?= money($p['price']) ?></span>
        <?php if ($d > 0) { ?><span class="old"><?= money($p['old_price']) ?></span><span class="tag-off inline"><?= $d ?>% off</span><?php } ?>
      </div>
      <p class="desc"><?= e($p['description']) ?></p>
      <p class="stock <?= $p['stock'] > 0 ? 'ok' : 'no' ?>">
        <?= $p['stock'] > 0 ? ($p['stock'] <= 5 ? 'Only ' . (int)$p['stock'] . ' left in stock' : 'In stock') : 'Out of stock' ?>
      </p>
      <?php if ($p['stock'] > 0) { ?>
      <div class="actions">
        <a class="btn btn-lg" href="cart.php?add=<?= $p['id'] ?>">Add to cart</a>
        <a class="btn btn-lg btn-alt" href="cart.php?add=<?= $p['id'] ?>&go=checkout">Buy now</a>
      </div>
      <?php } ?>
      <ul class="perks">
        <li>Free delivery above Rs. 999</li>
        <li>7-day easy returns</li>
        <li>Cash on delivery available</li>
      </ul>
    </div>
  </section>

  <?php if (mysqli_num_rows($related) > 0) { ?>
  <h2 class="sec-title">More in <?= e($p['category']) ?></h2>
  <div class="grid">
    <?php while ($r = mysqli_fetch_assoc($related)) { ?>
    <article class="card">
      <a class="card-img" href="product.php?id=<?= $r['id'] ?>"><img src="<?= e(img($r)) ?>" alt="<?= e($r['name']) ?>"></a>
      <div class="card-body">
        <h3><a href="product.php?id=<?= $r['id'] ?>"><?= e($r['name']) ?></a></h3>
        <div class="price-row"><span class="price"><?= money($r['price']) ?></span></div>
        <a class="btn btn-block" href="cart.php?add=<?= $r['id'] ?>">Add to cart</a>
      </div>
    </article>
    <?php } ?>
  </div>
  <?php } ?>
</main>
<?php include 'includes/footer.php'; ?>
