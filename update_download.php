<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$pass = "";
$db   = "visitor_cards";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}

// check post
if (!isset($_POST['id'])) {
    echo "❌ Error: ID not received";
    exit;
}

$id = intval($_POST['id']);

// just to check
echo "🔎 Got ID = $id\n";

// update query
$sql = "UPDATE visitors SET card_downloaded='Yes' WHERE id=?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "❌ Prepare failed: " . $conn->error;
    exit;
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "✅ Success: Visitor ID $id updated";
    } else {
        echo "⚠️ No row updated (maybe ID not found)";
    }
} else {
    echo "❌ Execute failed: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
