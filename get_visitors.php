<?php
session_start();
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$host = "localhost"; 
$user = "root";       
$pass = "";           
$db   = "visitor_cards";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM visitors ORDER BY id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Visitor Records - Admin Panel</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-image: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
        margin: 0;
        padding: 0;
    }
    header {
        background: linear-gradient(90deg, #007bff, #6610f2);
        color: #fff;
        padding: 15px 20px;
        text-align: center;
    }
    header h1 {
        margin: 0;
        font-size: 22px;
    }
    .container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 20px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    .top-bar h2 {
        color: #333;
        margin: 0;
    }
    
    .reset-btn {
  display: inline-block;
  background: linear-gradient(90deg,#e11d48,#b91c1c); /* Red gradient */
  color: #fff;
  padding: 8px 14px;
  border-radius: 6px;
  border: none;
  font-weight: 600;
  cursor: pointer;
  transition: transform .12s ease, box-shadow .12s ease;
  margin-left: 10px;
}
.reset-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(185,28,28,0.25);
}
.reset-btn:active {
  transform: translateY(0);
}
.top-bar a {
        text-decoration: none;
        padding: 8px 14px;
        background: #dc3545;
        color: #fff;
        border-radius: 5px;
        transition: 0.3s;
    }
    .top-bar a:hover {
        background: #c82333;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        overflow: hidden;
        border-radius: 10px;
    }
    th, td {
        padding: 12px;
        text-align: center;
    }
    th {
        background: #007bff;
        color: white;
    }
    tr:nth-child(even) {
        background: #f2f2f2;
    }
    tr:hover {
        background: #e9ecef;
    }
    .action-buttons {
  display: flex;
  gap: 8px;  /* buttons ke beech space */
  justify-content: center;
}

.edit-btn, .delete-btn {
  padding: 6px 12px;
  font-size: 14px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s;
}

/* Edit button */
.edit-btn {
  background-color: #4caf50;
  color: white;
  text-decoration: none !important;
}
.edit-btn:hover {
  background-color: #43a047;
}

/* Delete button */
.delete-btn {
  background-color: #dc3545;
  color: white;
}
.delete-btn:hover {
  background-color: #c82333;
}

</style>
</head>
<body>

<header>
    <h1>Admin Panel - Student Records</h1>
</header>

<div class="container">
    <div class="top-bar">
        <h2>Saved Student Data</h2>
        <form method="POST" action="reset_ids.php" 
      onsubmit="return confirm('⚠️ Are you sure? This will DELETE all visitor records and reset IDs to 1. This cannot be undone.')" 
      style="display:inline-block;">
  <button type="submit" class="reset-btn">🗑️ Reset Database</button>
</form>

        <a href="logout.php">Logout</a>
    </div>

    <table>
        <tr>
            <th>Admission No.</th>
            <th>Card Title</th>
            <th>Student Name</th>
            <th>College</th>
            <th>Course</th>
            <th>D.O.B.</th>
            <th>Valid Until</th>
            <th>Contact No.</th>
            <th>Include QR</th>
            <th>Student ID</th>
            <th>Student ID Card Downloaded</th>
            <th>Action</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['card_title']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['company']) ?></td>
                    <td><?= htmlspecialchars($row['purpose']) ?></td>
                    <td><?= htmlspecialchars($row['contact']) ?></td>
                    <td><?= htmlspecialchars($row['valid_until']) ?></td>
                    <td><?= htmlspecialchars($row['additional_info']) ?></td>
                    <td><?= $row['include_qr'] ? "Yes" : "No" ?></td>
                    <td><?= htmlspecialchars($row['student_id']) ?></td>
                    <td><?php echo $row['card_downloaded']; ?></td>
                    <td class="action-buttons">
  <a href="edit.php?id=<?= $row['id']; ?>" class="edit-btn">✏️ Edit</a>
  
  <button class="delete-btn" onclick="deleteVisitor(<?php echo $row['id']; ?>, this)">🗑️ Delete</button>
</td>






                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="12">No Student records found</td></tr>
        <?php endif; ?>
    </table>
    <script>
function deleteVisitor(id, btn) {
  if (!confirm("Are you sure you want to delete this record?")) return;

  fetch("delete_visitor.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "id=" + encodeURIComponent(id)
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      btn.closest("tr").remove();
      alert("Record deleted successfully!");
    } else {
      alert("Delete failed: " + (data.message || "unknown error"));
    }
  })
  .catch(err => {
    console.error("Error:", err);
    alert("Request failed. See console.");
  });
}
</script>

</div>

</body>
</html>

<?php $conn->close(); ?>


