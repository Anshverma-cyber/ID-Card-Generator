<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "visitor_cards";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("DELETE FROM visitors WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "id" => $id]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}

$conn->close();
