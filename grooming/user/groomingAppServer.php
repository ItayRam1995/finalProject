<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

// התחברות למסד נתונים
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'שגיאה בחיבור למסד הנתונים']);
    exit;
}

// קבלת נתוני הבקשה
// לקרוא נתוני JSON
// שנשלחו ולפרש אותם למערך שמתפקד כמו מילון
$data = json_decode(file_get_contents("php://input"), true);
// בודק האם המערך $data לא כולל מפתח בשם day או מפתח בשם time
// אם לא – אין טעם להמשיך
if (!isset($data['day']) || !isset($data['time'])) {
     // מציין שהבקשה שהתקבלה שגויה 
    http_response_code(400);
    // מחזיר תשובת שגיאה למשתמש בפורמט JSON
    echo json_encode(['error' => 'יש להזין תאריך ושעה']);

    // עוצר מיד את ריצת הסקריפט
    exit;
}

// לבדוק אם כבר קיימת הזמנת טיפוח שתפסה את אותו יום ושעה
$check = $conn->prepare("SELECT id FROM grooming_appointments WHERE day = ? AND time = ? AND isTaken = 1");
$check->bind_param("ss", $data['day'], $data['time']);
$check->execute();
// לאחסן בזיכרון המקומי של השרת את תוצאות השאילתה
$check->store_result();

// לבדוק האם התקבלו תוצאות בכלל
if ($check->num_rows > 0) {

    // מחזיר תשובת שגיאה למשתמש בפורמט JSON
    echo json_encode(['error' => 'השעה הזו כבר תפוסה']);
    $check->close();
    $conn->close();

    // עוצר מיד את ריצת הסקריפט
    exit;
}
$check->close();

// ליצור מזהה אישור אקראי, קצר וייחודי להזמנת הטיפוח
$confirmation = strtoupper(bin2hex(random_bytes(3))); // לדוגמה: A1F2C3

// קבלת סוג הטיפוח והמחיר מה-SESSION
$grooming_type = isset($_SESSION['grooming_type']) ? $_SESSION['grooming_type'] : 'טיפול כללי';
$grooming_price = isset($_SESSION['grooming_price']) ? intval($_SESSION['grooming_price']) : 0;

// קבלת קוד המשתמש מה-SESSION
$user_code = isset($_SESSION['user_code']) ? $_SESSION['user_code'] : '';

// קבלת מזהה הכלב הפעיל מה-SESSION
$dog_id = null;
if (isset($_SESSION['active_dog_id'])) {
    $dog_id = $_SESSION['active_dog_id'];
}

// קבלת reservation_id מהנתונים שנשלחו (אם קיים)
$connected_reservation_id = null;
if (isset($data['reservation_id']) && !empty($data['reservation_id'])) {
    $connected_reservation_id = intval($data['reservation_id']);
}

// הכנסת ההזמנה עם קישור להזמנת הפנסיון
$stmt = $conn->prepare("INSERT INTO grooming_appointments (day, time, confirmation, isTaken, user_code, grooming_type, grooming_price, dog_id, connected_reservation_id) VALUES (?, ?, ?, 1, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssiii", $data['day'], $data['time'], $confirmation, $user_code, $grooming_type, $grooming_price, $dog_id, $connected_reservation_id);

if ($stmt->execute()) {
    // שמירת נתוני ההזמנה בסשן לעמוד הסיכום (לא מוחקים את הנתונים המקוריים עדיין)
    $_SESSION['last_grooming_confirmation'] = $confirmation;
    $_SESSION['last_grooming_type'] = $grooming_type;
    $_SESSION['last_grooming_price'] = $grooming_price;
    $_SESSION['last_appointment_day'] = $data['day'];
    $_SESSION['last_appointment_time'] = $data['time'];
    $_SESSION['last_connected_reservation_id'] = $connected_reservation_id;
    
    
    // אם ההזמנה הצליחה, מוחקים את המידע מה-SESSION כדי שלא יישמר להזמנה הבאה
    // למקרה שהמשתמש יעשה כפתור הקודם בדפדפן

    // בעמוד doGrommingAppointment.php בשורה 427
    // יש שורה שמפנה חזרה לעמוד treatments.php במידה וסוג הטיפוח והמחיר לא מוגדרים
    unset($_SESSION['grooming_type']);
    unset($_SESSION['grooming_price']);
    
    // להחזיר למשתמש קובץ JSON שמעיד שהזמנת הטיפוח הצליחה
    echo json_encode([
        'success' => true, 
        'confirmation' => $confirmation, 
        'dog_id' => $dog_id,
        'connected_reservation_id' => $connected_reservation_id
    ]);
} else {
    // להחזיר למשתמש קובץ JSON עם שגיאה עם סיבת התקלה שקשורה במסד הנתונים
    echo json_encode(['error' => 'שגיאה בהוספת ההזמנה: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>