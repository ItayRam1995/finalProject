<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    echo json_encode(['error' => 'שגיאה בחיבור למסד הנתונים']);
    exit;
}

// קבלת נתוני הבקשה
// לקרוא נתוני JSON
// שנשלחו ולפרש אותם למערך שמתפקד כמו מילון
$data = json_decode(file_get_contents("php://input"), true);

// אם חסרים נתונים
if (!isset($data['dog_id']) || !isset($data['user_code'])) {
    echo json_encode(['error' => 'חסרים פרטים']);
    exit;
}

$dog_id = $data['dog_id'];
$user_code = $data['user_code'];
$today = date('Y-m-d'); // התאריך של היום

// שליפת רק הזמנות פעילות - כאלה שתאריך הסיום שלהן גדול או שווה לתאריך של היום
$stmt = $conn->prepare("SELECT id, start_date, end_date, created_at 
                       FROM reservation 
                       WHERE dog_id = ? AND user_code = ? AND end_date >= ? 
                       ORDER BY start_date ASC");
$stmt->bind_param("iss", $dog_id, $user_code, $today);
$stmt->execute();
$result = $stmt->get_result();

$reservations = [];
while ($row = $result->fetch_assoc()) {
    $today = new DateTime();
$start_date = new DateTime($row['start_date']);
$end_date = new DateTime($row['end_date']);

// במידה ותאריך ההתחלה המקורי של ההזמנה קטן מהתאריך של היום, תעדכן את תאריך ההתחלה של הההזמנה להיות התאריך של היום
// עושים את זה בשביל להימנע מלהציג תאריכים ישנים בהזמנת הטיפוח, בשביל אחר כך למנוע מהמשתמש להזמין הזמנת טיפוח על תאריך שחלף
if ($start_date < $today) {
    $start_date = $today;
}

$reservations[] = [
    'id' => $row['id'],
    'start_date' => $start_date->format('Y-m-d'),
    'end_date' => $end_date->format('Y-m-d'),
    'created_at' => $row['created_at']
];
}

$stmt->close();
$conn->close();

// מחזיר את ההזמנות הפעילות בלבד
echo json_encode([
    'reservations' => $reservations,
    'has_active_reservations' => count($reservations) > 0
]);
?>