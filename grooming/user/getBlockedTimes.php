<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'שגיאה בהתחברות למסד הנתונים']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['day'])) {
    http_response_code(400);
    echo json_encode(['error' => 'חסר תאריך']);
    exit;
}

$day = $data['day']; // Expected format: YYYY-MM-DD
$stmt = $conn->prepare("SELECT time FROM grooming_appointments WHERE day = ? AND isTaken = 1");
$stmt->bind_param("s", $day);
$stmt->execute();
$result = $stmt->get_result();

$times = [];
while ($row = $result->fetch_assoc()) {
    $times[] = $row['time'];
}

echo json_encode(['blockedTimes' => $times]);

$stmt->close();
$conn->close();
?>