<?php
require 'auth.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM products WHERE id = $id"));
    // only delete files that were uploaded by the admin (timestamp names), never the sample images
    if ($r && preg_match('/^\d+_\d+\./', $r['image']) && file_exists(__DIR__ . '/../images/products/' . $r['image'])) {
        unlink(__DIR__ . '/../images/products/' . $r['image']);
    }
    mysqli_query($conn, "DELETE FROM products WHERE id = $id");
    flash('Product deleted.');
    header("Location: products.php");
    exit;
}

if (isset($_POST['save'])) {
    $id    = (int)$_POST['id'];
    $name  = mysqli_real_escape_string($conn, trim($_POST['name']));
    $cat   = mysqli_real_escape_string($conn, trim($_POST['category']));
    $desc  = mysqli_real_escape_string($conn, trim($_POST['description']));
    $price = (float)$_POST['price'];
    $old   = ($_POST['old_price'] !== '') ? (float)$_POST['old_price'] : 'NULL';
    $stock = (int)$_POST['stock'];
    $rate  = (float)$_POST['rating'];
    $img   = upload_image($_FILES['image'] ?? null);

    if ($id > 0) {
        $set = "name='$name', category='$cat', description='$desc', price=$price, old_price=$old, stock=$stock, rating=$rate";
        if ($img !== '') $set .= ", image='$img'";
        mysqli_query($conn, "UPDATE products SET $set WHERE id = $id");
        flash('Product updated.');
    } else {
        mysqli_query($conn, "INSERT INTO products (name, category, description, price, old_price, rating, stock, image)
                             VALUES ('$name', '$cat', '$desc', $price, $old, $rate, $stock, '$img')");
        flash('Product added.');
    }
    header("Location: products.php");
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id = " . (int)$_GET['edit']));
}
$list = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");

$active = 'products';
include 'header.php';
?>
<h1>Products</h1>

<form method="post" enctype="multipart/form-data" class="form-card wide">
  <h3><?= $edit ? 'Edit product' : 'Add a new product' ?></h3>
  <input type="hidden" name="id" value="<?= $edit ? $edit['id'] : 0 ?>">
  <div class="form-grid">
    <div><label>Name</label><input type="text" name="name" required value="<?= e($edit['name'] ?? '') ?>"></div>
    <div><label>Category</label><input type="text" name="category" list="cats" required value="<?= e($edit['category'] ?? '') ?>">
      <datalist id="cats"><option value="Electronics"><option value="Fashion"><option value="Home &amp; Kitchen"></datalist></div>
    <div><label>Price (Rs.)</label><input type="number" step="0.01" name="price" required value="<?= e($edit['price'] ?? '') ?>"></div>
    <div><label>Old price (optional)</label><input type="number" step="0.01" name="old_price" value="<?= e($edit['old_price'] ?? '') ?>"></div>
    <div><label>Stock</label><input type="number" name="stock" required value="<?= e($edit['stock'] ?? 20) ?>"></div>
    <div><label>Rating (0 to 5)</label><input type="number" step="0.1" min="0" max="5" name="rating" value="<?= e($edit['rating'] ?? 4.0) ?>"></div>
  </div>
  <label>Description</label>
  <textarea name="description"><?= e($edit['description'] ?? '') ?></textarea>
  <label>Product image <?= $edit ? '(leave empty to keep the current one)' : '' ?></label>
  <input type="file" name="image" accept="image/*">
  <button type="submit" name="save" class="btn btn-lg"><?= $edit ? 'Save changes' : 'Add product' ?></button>
  <?php if ($edit) { ?><a class="btn btn-alt btn-lg" href="products.php">Cancel</a><?php } ?>
</form>

<h3>All products (<?= mysqli_num_rows($list) ?>)</h3>
<div class="table-wrap">
<table>
  <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
  <?php while ($p = mysqli_fetch_assoc($list)) { ?>
  <tr>
    <td><img class="thumb-sm" src="<?= e(img($p, '../')) ?>" alt=""></td>
    <td><?= e($p['name']) ?></td>
    <td><?= e($p['category']) ?></td>
    <td><?= money($p['price']) ?></td>
    <td><?= (int)$p['stock'] ?></td>
    <td class="row-actions">
      <a class="btn btn-sm" href="products.php?edit=<?= $p['id'] ?>">Edit</a>
      <a class="btn btn-sm btn-danger" href="products.php?delete=<?= $p['id'] ?>" onclick="return confirm('Delete this product?');">Delete</a>
    </td>
  </tr>
  <?php } ?>
</table>
</div>
<?php include 'footer.php'; ?>
