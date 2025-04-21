<?php include '../../header.php'; ?>
<?php

// התחברות למסד נתונים
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// טווח תאריכים מהטופס
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$where = "";
$params = [];
$types = "";

if ($from && $to) {
    $where = "WHERE date BETWEEN ? AND ?";
    $params = [$from, $to];
    $types = "ss";
}

$query = "SELECT date, available_spots FROM Availability $where ORDER BY date ASC";
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="he">
<head>
  <meta charset="UTF-8">
  <title>צפייה בזמינות</title>
  <style>
    body { font-family: Arial, sans-serif; direction: rtl; padding: 30px; background-color: #f9f9f9; }
    table {
      width: 100%; border-collapse: collapse; max-width: 600px; margin: auto;
      background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px; border: 1px solid #ccc; text-align: center;
    }
    th {
      background-color: #34495e; color: white;
    }
    form {
      max-width: 600px; margin: auto; background: white; padding: 15px;
      margin-bottom: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    input, button {
      padding: 10px; margin: 5px; border-radius: 5px; border: 1px solid #ccc;
    }
  
a, button {
  display: inline-block;
  transition: transform 0.1s ease-in-out;
}
a:active, button:active {
  transform: scale(0.95);
}

</style>
</head>
<body>
  <h2 style="text-align:center;">טבלת זמינות תאריכים</h2>

  <form method="get">
    <label>מתאריך:</label>
    <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" required>
    <label>עד תאריך:</label>
    <input type="date" name="to" value="<?= htmlspecialchars($to) ?>" required>
    <button type="submit">חפש</button>
  </form>
<div style="text-align:center; margin-bottom: 20px;">
  <a href="export_availability.php" style="background:#27ae60;color:white;padding:10px 20px;border-radius:8px;text-decoration:none;">
    📤 ייצוא לאקסל
  </a>
</div>


  <table>
    <tr>
      <th>תאריך</th>
      <th>מספר מקומות פנויים</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['date']}</td><td>{$row['available_spots']}</td></tr>";
        }
    } else {
        echo "<tr><td colspan='2'>לא נמצאו תאריכים בטווח הנבחר</td></tr>";
    }
    ?>
  </table>
</body>
</html>
