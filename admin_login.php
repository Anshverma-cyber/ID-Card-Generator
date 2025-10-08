<?php
session_start();
if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {
    header("Location: get_visitors.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background-image: linear-gradient(to top, #accbee 0%, #e7f0fd 100%);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-box {
      background: #fff;
      padding: 30px 25px;
      border-radius: 12px;
      box-shadow: 0px 4px 15px rgba(0,0,0,0.2);
      width: 320px;
      text-align: center;
      animation: fadeIn 0.8s ease;
    }
    .login-box h2 {
      margin-bottom: 20px;
      color: #333;
    }
    .login-box input[type="text"],
    .login-box input[type="password"] {
      width: 90%;
      padding: 12px;
      margin: 8px 0 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      outline: none;
      font-size: 14px;
    }
    .login-box input:focus {
      border-color: #007bff;
    }
    .login-box button {
      width: 100%;
      padding: 12px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      cursor: pointer;
      transition: 0.3s;
    }
    .login-box button:hover {
      background: #0056b3;
    }
    .error {
      color: red;
      font-size: 14px;
      margin-bottom: 10px;
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(-20px);}
      to {opacity: 1; transform: translateY(0);}
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Admin Login</h2>
    <?php if(isset($_GET['err'])): ?>
      <div class="error"><?= htmlspecialchars($_GET['err']) ?></div>
    <?php endif; ?>
    <form action="admin_auth.php" method="post">
      <input type="text" name="username" placeholder="Enter Username" required>
      <input type="password" name="password" placeholder="Enter Password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
