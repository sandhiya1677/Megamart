<?php
require 'db.php';
unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['cart']);
flash('You have been logged out.');
header("Location: index.php");
exit;
