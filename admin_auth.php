<?php
session_start();

// Simple admin username & password (badme DB me bhi store kar sakte ho)
$admin_user = "admin";
$admin_pass = "12345"; // change kar lena strong password se

if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
    $_SESSION['admin_logged'] = true;
    header("Location: get_visitors.php"); // admin page
    exit;
} else {
    echo "Invalid login. <a href='admin_login.php'>Try again</a>";
}
