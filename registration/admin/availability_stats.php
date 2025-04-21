<?php
include '../includes/header.php';

// התחברות למסד נתונים
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// שליפות
$total_days = $conn->query("SELECT COUNT(*) as total FROM Availability")->fetch_assoc()['total'];
$total_spots = $conn->query("SELECT SUM(available_spots) as sum FROM Availability")->fetch_assoc()['sum'];
$max_row = $conn->query("SELECT date, available_spots FROM Availability ORDER BY available_spots DESC LIMIT 1")->fetch_assoc();
$min_row = $conn->query("SELECT date, available_spots FROM Availability ORDER BY available_spots ASC LIMIT 1")->fetch_assoc();
$avg_row = $conn->query("SELECT AVG(available_spots) as avg FROM Availability")->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="he">
<head>
  <meta charset="UTF-8">
  <title>סיכום סטטיסטי של זמינות</title>
  <style>
    body { font-family: Arial, sans-serif; direction: rtl; background-color: #f5f5f5; padding: 30px; }
    .card {
      background: white; max-width: 600px; margin: auto;
      padding: 20px; border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 { text-align: center; color: #2c3e50; }
    ul { list-style-type: none; padding: 0; }
    li { padding: 10px; border-bottom: 1px solid #eee; }
  
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
  <div class="card">
    <h2>סיכום סטטיסטי של זמינות</h2>
    <ul>
      <li><strong>סה"כ תאריכים קיימים:</strong> <?= $total_days ?></li>
      <li><strong>סה"כ מקומות זמינים:</strong> <?= $total_spots ?></li>
      <li><strong>ממוצע מקומות לתאריך:</strong> <?= round($avg_row['avg'], 2) ?></li>
      <li><strong>תאריך עם הכי הרבה מקומות:</strong> <?= $max_row['date'] ?> (<?= $max_row['available_spots'] ?>)</li>
      <li><strong>תאריך עם הכי פחות מקומות:</strong> <?= $min_row['date'] ?> (<?= $min_row['available_spots'] ?>)</li>
    </ul>
  </div>
</body>
</html>
