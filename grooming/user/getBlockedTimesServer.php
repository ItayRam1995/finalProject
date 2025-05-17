<?php

// התשובה שתישלח מהשרת היא בפורמט JSON
header('Content-Type: application/json');

// הגדרת אזור הזמן לישראל
date_default_timezone_set('Asia/Jerusalem');

$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

// התחברות למסד נתונים
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'שגיאה בהתחברות למסד הנתונים']);
    exit;
}

// קבלת נתוני הבקשה
// לקרוא נתוני JSON
// שנשלחו ולפרש אותם למערך שמתפקד כמו מילון
$data = json_decode(file_get_contents("php://input"), true);


// בודק האם המערך $data לא כולל מפתח בשם day
// אם לא – אין טעם להמשיך
if (!isset($data['day'])) {
    // מציין שהבקשה שהתקבלה שגויה 
    http_response_code(400);
    // מחזיר תשובה למשתמש בפורמט JSON
    echo json_encode(['error' => 'חסר תאריך']);

    // עוצר מיד את ריצת הסקריפט
    exit;
}

$day = $data['day']; // בפורמט : YYYY-MM-DD

// קבלת השעות התפוסות ממסד הנתונים
// שאילתה שמחזירה את כל השעות שתפוסות בתאריך מסוים בטבלת הטיפוח
$stmt = $conn->prepare("SELECT time FROM grooming_appointments WHERE day = ? AND isTaken = 1");
$stmt->bind_param("s", $day);
$stmt->execute();
$result = $stmt->get_result();

// שליפת כל השעות החסומות מתוך תוצאת השאילתה שנשלפה ממסד הנתונים, והכנסתן למערך
$blockedTimes = [];
// לולאה שרצה על כל שורה בתוצאה
// כל שורה היא מילון
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
        '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30'  // צוהריים
    ];
    
    // חסימת כל השעות שכבר עברו להיום וגם חצי שעה קדימה
    foreach ($allTimes as $time) {
        // פירוק השעה לשעות ודקות
        // מפרק את המחרוזת לפי הנקודותיים 
        list($timeHour, $timeMinute) = explode(':', $time); // time = 14:30
        
        // המרה למספרים שלמים כדי להשוות בקלות
        $timeHour = (int)$timeHour; // 14
        $timeMinute = (int)$timeMinute; //30
        
        // בדיקה אם השעה המבוקשת כבר עברה או נמצאת בתוך חצי השעה הבאה:
        // 1. השעה קטנה משעת החוצץ 
        // או
        // 2. השעה זהה לשעת החוצץ, והדקות קטנות או שוות לדקות החוצץ
        if ($timeHour < $bufferHour || ($timeHour === $bufferHour && $timeMinute <= $bufferMinutes)) {
            // הוספה לרשימת השעות החסומות אם היא כבר לא נמצאת שם
            if (!in_array($time, $blockedTimes)) {
                $blockedTimes[] = $time;
            }
        }
    }
}

// החזרת כל השעות החסומות + כל השעות שחלפו היום (כולל חצי שעה קדימה)
echo json_encode(['blockedTimes' => $blockedTimes]);

$stmt->close();
$conn->close();
?>