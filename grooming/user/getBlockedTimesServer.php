<?php
header('Content-Type: application/json');

// הגדרת אזור הזמן לישראל
date_default_timezone_set('Asia/Jerusalem');

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

// קבלת השעות התפוסות ממסד הנתונים
$stmt = $conn->prepare("SELECT time FROM grooming_appointments WHERE day = ? AND isTaken = 1");
$stmt->bind_param("s", $day);
$stmt->execute();
$result = $stmt->get_result();

$blockedTimes = [];
while ($row = $result->fetch_assoc()) {
    $blockedTimes[] = $row['time'];
}

// בדיקה אם התאריך המבוקש הוא היום הנוכחי
$today = date('Y-m-d');
if ($day === $today) {
    // קבלת השעה הנוכחית בפורמט 24 שעות
    $currentHour = (int)date('H');
    $currentMinute = (int)date('i');
    
    // נוסיף חצי שעה (30 דקות) לזמן הנוכחי
    $bufferMinutes = $currentMinute + 30;
    $bufferHour = $currentHour;
    
    // אם הוספנו יותר מ-60 דקות, נעדכן את השעה בהתאם
    if ($bufferMinutes >= 60) {
        $bufferHour += 1;
        $bufferMinutes -= 60;
    }
    
    // כל השעות האפשריות במערכת
    $allTimes = [
        '8:00', '8:30', '9:00', '9:30', '10:00', '11:00', '11:30',  // בוקר
        '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30'  // צהריים
    ];
    
    // חסימת כל השעות שכבר עברו להיום וגם חצי שעה קדימה
    foreach ($allTimes as $time) {
        // פירוק השעה לשעות ודקות
        list($timeHour, $timeMinute) = explode(':', $time);
        
        // המרה למספרים שלמים כדי להשוות בקלות
        $timeHour = (int)$timeHour;
        $timeMinute = (int)$timeMinute;
        
        // בדיקה אם השעה המבוקשת כבר עברה או נמצאת בתוך חצי השעה הבאה:
        // 1. השעה קטנה משעת הבאפר 
        // או
        // 2. השעה זהה לשעת הבאפר, והדקות קטנות או שוות לדקות הבאפר
        if ($timeHour < $bufferHour || ($timeHour === $bufferHour && $timeMinute <= $bufferMinutes)) {
            // הוספה לרשימת השעות החסומות אם היא כבר לא נמצאת שם
            if (!in_array($time, $blockedTimes)) {
                $blockedTimes[] = $time;
            }
        }
    }
}

// החזרת כל השעות החסומות
echo json_encode(['blockedTimes' => $blockedTimes]);

$stmt->close();
$conn->close();
?>