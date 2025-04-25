<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'שגיאה בחיבור למסד הנתונים']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['day']) || !isset($data['time'])) {
    http_response_code(400);
    echo json_encode(['error' => 'יש להזין תאריך ושעה']);
    exit;
}

// וידוא שיש ערך ב-user_code, אם לא - נשתמש בערך ריק
$user_code = isset($data['user_code']) ? $data['user_code'] : '';

// בדיקה אם כבר קיימת הזמנה לשעה הזו
$check = $conn->prepare("SELECT id FROM grooming_appointments WHERE day = ? AND time = ? AND isTaken = 1");
$check->bind_param("ss", $data['day'], $data['time']);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(['error' => 'השעה הזו כבר תפוסה']);
    $check->close();
    $conn->close();
    exit;
}
$check->close();

$confirmation = strtoupper(bin2hex(random_bytes(3))); // לדוגמה: A1F2C3

// עדכון השאילתה כך שתכלול את user_code
$stmt = $conn->prepare("INSERT INTO grooming_appointments (day, time, confirmation, isTaken, user_code) VALUES (?, ?, ?, 1, ?)");
$stmt->bind_param("ssss", $data['day'], $data['time'], $confirmation, $user_code);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'confirmation' => $confirmation]);
} else {
    echo json_encode(['error' => 'שגיאה בהוספת ההזמנה: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>