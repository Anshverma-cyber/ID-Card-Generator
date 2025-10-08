<?php
// Debugging ke liye (production me hata dena)
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

$host = "localhost";
$user = "root";
$pass = "";
$db   = "visitor_cards";

// connect
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["status"=>"error","message"=>"DB connect failed: " . $conn->connect_error]);
    exit;
}

// read JSON body
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
if ($data === null) {
    echo json_encode(["status"=>"error","message"=>"Invalid JSON received", "raw"=>$raw]);
    exit;
}

// sanitize / set vars
$card_title     = $data['title'] ?? '';
$name           = $data['name'] ?? '';
$company        = $data['company'] ?? '';
$purpose        = $data['purpose'] ?? '';
$contact        = $data['contact'] ?? '';
$valid_until    = $data['validUntil'] ?? '';
$additionalInfo = $data['additionalInfo'] ?? '';
$includeQR      = isset($data['includeQR']) ? intval($data['includeQR']) : 0;
$header_color   = $data['headerColor'] ?? '';

// ---------------- Generate Student ID ----------------
$currentYear = date("Y");

// last student_id nikalna
$sql_last = "SELECT student_id FROM visitors WHERE student_id LIKE ? ORDER BY id DESC LIMIT 1";
$like = $currentYear . "-%";
$stmt_last = $conn->prepare($sql_last);
$stmt_last->bind_param("s", $like);
$stmt_last->execute();
$result_last = $stmt_last->get_result();

$serial = 1; // default
if ($row_last = $result_last->fetch_assoc()) {
    $lastId = $row_last['student_id']; // e.g. 2025-0007
    $parts = explode("-", $lastId);
    if (count($parts) == 2) {
        $serial = intval($parts[1]) + 1;
    }
}
$stmt_last->close();

// new student_id generate
$student_id = $currentYear . "" . str_pad($serial, 4, "0", STR_PAD_LEFT);

// -----------------------------------------------------

// prepare & execute
$sql = "INSERT INTO visitors
(student_id, card_title, name, company, purpose, contact, valid_until, additional_info, include_qr, header_color)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["status"=>"error","message"=>"Prepare failed: ".$conn->error]);
    exit;
}

$bind = $stmt->bind_param("ssssssssss",
    $student_id, $card_title, $name, $company, $purpose, $contact, $valid_until,
    $additionalInfo, $includeQR, $header_color
);

if (!$bind) {
    echo json_encode(["status"=>"error","message"=>"Bind failed: ".$stmt->error]);
    exit;
}

if ($stmt->execute()) {
    echo json_encode([
        "status"=>"success",
        "message"=>"Visitor saved successfully!",
        "student_id"=>$student_id,
        "insert_id"=>$stmt->insert_id
    ]);
} else {
    echo json_encode(["status"=>"error","message"=>"Execute failed: ".$stmt->error]);
}

$stmt->close();
$conn->close();
?>
