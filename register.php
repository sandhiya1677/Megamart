<?php
require 'db.php';
$err = '';

if (isset($_POST['register'])) {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[0-9]{10}$/', $phone) || strlen($pass) < 6) {
        $err = 'Enter your name, a valid email, a 10-digit phone number and a password of at least 6 characters.';
    } else {
        $st = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($st, "s", $email);
        mysqli_stmt_execute($st);
        mysqli_stmt_store_result($st);
        if (mysqli_stmt_num_rows($st) > 0) {
            $err = 'This email is already registered. Try logging in.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $ins = mysqli_prepare($conn, "INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($ins, "ssss", $name, $email, $phone, $hash);
            mysqli_stmt_execute($ins);
            $_SESSION['user_id'] = mysqli_insert_id($conn);
            $_SESSION['user_name'] = explode(' ', $name)[0];
            flash('Welcome to MegaMart, ' . $_SESSION['user_name'] . '!');
            header("Location: index.php");
            exit;
        }
    }
}

$title = 'Create account';
include 'includes/header.php';
?>
<main class="container narrow">
  <form method="post" class="form-card" onsubmit="return validateRegister();">
    <h2>Create your account</h2>
    <?php if ($err) { ?><p class="error"><?= e($err) ?></p><?php } ?>
    <label for="rname">Full name</label>
    <input type="text" id="rname" name="name" value="<?= e($_POST['name'] ?? '') ?>">
    <label for="remail">Email</label>
    <input type="email" id="remail" name="email" value="<?= e($_POST['email'] ?? '') ?>">
    <label for="rphone">Phone number</label>
    <input type="text" id="rphone" name="phone" maxlength="10" value="<?= e($_POST['phone'] ?? '') ?>">
    <label for="rpass">Password</label>
    <input type="password" id="rpass" name="password" placeholder="At least 6 characters">
    <button type="submit" name="register" class="btn btn-lg btn-block">Create account</button>
    <p class="alt-link">Already have an account? <a href="login.php">Log in</a></p>
  </form>
</main>
<?php include 'includes/footer.php'; ?>
