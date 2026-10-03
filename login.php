<?php
require 'db.php';
$err = '';
$next = $_GET['next'] ?? ($_POST['next'] ?? 'index.php');
if (!in_array($next, ['index.php', 'checkout.php', 'cart.php', 'orders.php'])) $next = 'index.php';

if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $st = mysqli_prepare($conn, "SELECT id, name, password FROM users WHERE email = ?");
    mysqli_stmt_bind_param($st, "s", $email);
    mysqli_stmt_execute($st);
    mysqli_stmt_bind_result($st, $uid, $uname, $uhash);
    if (mysqli_stmt_fetch($st) && password_verify($pass, $uhash)) {
        $_SESSION['user_id'] = $uid;
        $_SESSION['user_name'] = explode(' ', $uname)[0];
        flash('Welcome back, ' . $_SESSION['user_name'] . '!');
        header("Location: " . $next);
        exit;
    }
    $err = 'Email or password is incorrect.';
}

$title = 'Login';
include 'includes/header.php';
?>
<main class="container narrow">
  <form method="post" class="form-card">
    <h2>Log in</h2>
    <?php if ($err) { ?><p class="error"><?= e($err) ?></p><?php } ?>
    <input type="hidden" name="next" value="<?= e($next) ?>">
    <label for="lemail">Email</label>
    <input type="email" id="lemail" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
    <label for="lpass">Password</label>
    <input type="password" id="lpass" name="password" required>
    <button type="submit" name="login" class="btn btn-lg btn-block">Log in</button>
    <p class="alt-link">New here? <a href="register.php">Create an account</a></p>
  </form>
</main>
<?php include 'includes/footer.php'; ?>
