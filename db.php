<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "megamart_db");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function cart_count() { return isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; }

function img($p, $root = '') {
    $f = !empty($p['image']) ? $p['image'] : 'default.svg';
    if (!file_exists(__DIR__ . '/images/products/' . $f)) { $f = 'default.svg'; }
    return $root . 'images/products/' . $f;
}

function discount($p) {
    if (!empty($p['old_price']) && $p['old_price'] > $p['price']) {
        return round((($p['old_price'] - $p['price']) / $p['old_price']) * 100);
    }
    return 0;
}

function stars($r) {
    $n = (int)round($r);
    return str_repeat('★', $n) . str_repeat('☆', 5 - $n);
}

function money($n) { return 'Rs. ' . number_format((float)$n, 0); }

function flash($msg) { $_SESSION['flash'] = $msg; }

function show_flash() {
    if (!empty($_SESSION['flash'])) {
        echo '<div class="flash" id="flash">' . e($_SESSION['flash']) . '</div>';
        unset($_SESSION['flash']);
    }
}

function upload_image($file) {
    if (!$file || $file['error'] !== 0) return '';
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) return '';
    $name = time() . '_' . rand(100, 999) . '.' . $ext;
    move_uploaded_file($file['tmp_name'], __DIR__ . '/images/products/' . $name);
    return $name;
}

function cart_items($conn) {
    $items = []; $sub = 0;
    if (!empty($_SESSION['cart'])) {
        $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
        $res = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");
        while ($p = mysqli_fetch_assoc($res)) {
            $p['qty'] = $_SESSION['cart'][$p['id']];
            $items[] = $p;
            $sub += $p['price'] * $p['qty'];
        }
    }
    return [$items, $sub];
}

function delivery_fee($sub) { return ($sub == 0 || $sub >= 999) ? 0 : 50; }
?>
