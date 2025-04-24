<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(['error' => 'שגיאה במסד הנתונים']);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['confirmation'])) {
  http_response_code(400);
  echo json_encode(['error' => 'מספר אישור חסר']);
  exit;
}

$stmt = $conn->prepare("UPDATE grooming_appointments SET isTaken = 0 WHERE confirmation = ?");
$stmt->bind_param("s", $data['confirmation']);
$stmt->execute();

if ($stmt->affected_rows > 0) {
  echo json_encode(['success' => true, 'message' => 'ההזמנה בוטלה בהצלחה']);
} else {
  echo json_encode(['error' => 'לא נמצאה הזמנה מתאימה']);
}

$stmt->close();
$conn->close();
?>