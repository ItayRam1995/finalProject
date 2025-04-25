<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(['error' => 'שגיאה בחיבור למסד הנתונים']);
  exit;
}

$result = $conn->query("SELECT id, day, time, confirmation, created_at FROM grooming_appointments WHERE isTaken = 1 ORDER BY day, time");

$appointments = [];
while ($row = $result->fetch_assoc()) {
  $appointments[] = $row;
}

echo json_encode($appointments);
$conn->close();
?>