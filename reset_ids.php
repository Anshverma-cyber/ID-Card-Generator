<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db   = "visitor_cards";

$conn = new mysqli($host, $user, $pass, $db);
$message = "";
$type = "success";

if ($conn->connect_error) {
    $message = "Database connection failed: " . $conn->connect_error;
    $type = "error";
} else {
    $sql = "TRUNCATE TABLE visitors";
    if ($conn->query($sql) === TRUE) {
        $message = "✅ All student records deleted & IDs reset to 1.";
    } else {
        $message = "❌ Error: " . $conn->error;
        $type = "error";
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Database</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #007bff, #6610f2);
      margin: 0;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .reset-container {
      background: #fff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.1);
      text-align: center;
      max-width: 400px;
      width: 100%;
    }
    .reset-container h1 {
      font-size: 22px;
      margin-bottom: 15px;
      color: #222;
    }
    .message {
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: 500;
    }
    .message.success { background: #dcfce7; color: #166534; }
    .message.error { background: #fee2e2; color: #991b1b; }
    .btn {
      display: inline-block;
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 600;
      transition: all .15s ease;
      cursor: pointer;
    }
    .btn-primary {
      background: linear-gradient(90deg,#3b82f6,#2563eb);
      color: #fff;
      border: none;
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(37,99,235,0.25);
    }
    .btn-secondary {
      background: #db7171ff;
      color: #111827;
      margin-left: 10px;
    }
  </style>
</head>
<body>
  <div class="reset-container">
    <h1>Database Reset</h1>
    <div class="message <?= $type ?>"><?= htmlspecialchars($message) ?></div>
    <div>
      <a href="get_visitors.php" class="btn btn-primary">🔙 Back to Admin Panel</a>
      <a href="admin_login.php" class="btn btn-secondary">Logout</a>
    </div>
  </div>
</body>
</html>
