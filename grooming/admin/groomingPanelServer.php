<?php

// JSON מגדיר שהתגובה תחזור בפורמט 
header('Content-Type: application/json');

// פרטי התחברות למסד הנתונים
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

// פונקציה לניהול שגיאות
function handleError($message, $code = 500) {
    http_response_code($code);
    //  שליחת תגובת JSON עם קוד שגיאה רלוונטי 
    echo json_encode([
        'success' => false,
        'error' => $message
    ]);
    exit;
}

// יצירת חיבור למסד הנתונים
$conn = new mysqli($servername, $username, $password, $dbname);

// בדיקת תקינות חיבור
if ($conn->connect_error) {
    // אם החיבור נכשל – מפעיל את handleError
    handleError('שגיאה בחיבור למסד הנתונים: ' . $conn->connect_error);
}

// הגדרה לעברית
// כדי למנוע בעיות תצוגה או שמירה של טקסטים בעברית
$conn->set_charset("utf8");

// שאילתה לשליפת ההזמנות הפעילות עם פרטי המשתמש ושם הכלב
// רק הזמנות שהתאריך והשעה שלהן גדולים מהתאריך והשעה הנוכחיים
// ורק הזמנות טיפוח שהזמנת הפנסיון המקושרת אליהן עדיין קיימת
$query = "SELECT g.id, g.day, g.time, g.confirmation, g.created_at, 
                g.grooming_type, g.grooming_price, g.dog_id, g.connected_reservation_id,
                u.first_name, u.last_name, u.phone, 
                d.dog_name,
                r.id as reservation_id, r.start_date as reservation_start, r.end_date as reservation_end
          FROM grooming_appointments g
          LEFT JOIN users u ON g.user_code = u.user_code
          LEFT JOIN dogs d ON g.dog_id = d.dog_id
          INNER JOIN reservation r ON g.connected_reservation_id = r.id
          WHERE g.isTaken = 1 
          AND (
              g.day > CURDATE() 
              OR (g.day = CURDATE() AND g.time > CURTIME())
          )
          ORDER BY g.day, g.time";

// הרצת השאילתה
$result = $conn->query($query);

// טיפול בשגיאה אם השאילתה נכשלה
if (!$result) {
    handleError('שגיאה בשליפת הנתונים: ' . $conn->error);
}

// המרת התוצאות למערך של מילונים, כל הזמנה היא מילון
$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

// סגירת החיבור
$conn->close();

// החזרת מערך ההזמנות
// נועד לספק רשימה גולמית של נתונים לצורך תצוגת טבלה
// לא מחזיר הודעת הצלחה כי אין בה צורך כי אין פעולה עם תוצאה לוגית
echo json_encode($appointments);
?>