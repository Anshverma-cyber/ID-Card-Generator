<?php
// DB connect
$host = "localhost";
$user = "root";
$pass = "";
$db   = "visitor_cards";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Agar ID na mile to back bhej do
if (!isset($_GET['id'])) {
    header("Location: get_visitors.php");
    exit;
}

$id = intval($_GET['id']);

// Record fetch karo
$sql = "SELECT * FROM visitors WHERE id=$id";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    echo "Record not found!";
    exit;
}
$row = $result->fetch_assoc();

// Agar form submit hua
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_title     = $_POST['card_title'];
    $name           = $_POST['name'];
    $company        = $_POST['company'];
    $purpose        = $_POST['purpose'];
    $contact        = $_POST['contact'];
    $valid_until    = $_POST['valid_until'];
    $additionalInfo = $_POST['additional_info'];

    $update = "UPDATE visitors 
               SET card_title='$card_title',
                   name='$name',
                   company='$company',
                   purpose='$purpose',
                   contact='$contact',
                   valid_until='$valid_until',
                   additional_info='$additionalInfo'
               WHERE id=$id";

    if ($conn->query($update)) {
        header("Location: get_visitors.php?msg=Record updated successfully");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Visitor</title>
  <link rel="stylesheet" href="style2.css">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">

</head>
<body>
  <div class="container">
    <h2>Edit Visitor Record</h2>
    <form method="POST">
      <label>Card Title</label>
      <input type="text" name="card_title" value="<?php echo $row['card_title']; ?>" required>

      <label>Name</label>
      <input type="text" name="name" value="<?php echo $row['name']; ?>" required>

      <label>College/Institute</label>
      <input type="text" name="company" value="<?php echo $row['company']; ?>">

      <label>Course</label>
      <input type="text" name="purpose" value="<?php echo $row['purpose']; ?>">

      <label>DOB</label>
      <input type="date" name="contact" value="<?php echo $row['contact']; ?>">

      <label>Valid Until</label>
      <input type="date" name="valid_until" value="<?php echo $row['valid_until']; ?>">

      <label>Contact</label>
      <input type="text" name="additional_info" value="<?php echo $row['additional_info']; ?>">

      <button type="submit" class="btn btn-primary">Update</button>
      <a href="get_visitors.php" class="btn btn-secondary">Cancel</a>
    </form>
  </div>
</body>
</html>
