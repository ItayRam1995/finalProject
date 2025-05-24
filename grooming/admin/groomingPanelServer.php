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
                g.status, g.isTaken,
                u.first_name, u.last_name, u.phone, 
                d.dog_name,
                r.id as reservation_id, r.start_date as reservation_start, r.end_date as reservation_end,
                CASE 
                    WHEN g.isTaken = 1 THEN 'active'
                    WHEN g.isTaken = 0 THEN 'cancelled'
                    ELSE 'unknown'
                END as appointment_status,
                CASE 
                    WHEN g.status = 'paid' THEN 'paid'
                    WHEN g.status = 'unpaid' THEN 'unpaid'
                    ELSE 'unpaid'
                END as payment_status
          FROM grooming_appointments g
          LEFT JOIN users u ON g.user_code = u.user_code
          LEFT JOIN dogs d ON g.dog_id = d.dog_id
          LEFT JOIN reservation r ON g.connected_reservation_id = r.id
          WHERE (
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
     // מיפוי הנתונים עם השדות החדשים
    $appointment = [
        'id' => $row['id'],
        'day' => $row['day'],
        'time' => $row['time'],
        'confirmation' => $row['confirmation'],
        'created_at' => $row['created_at'],
        'grooming_type' => $row['grooming_type'],
        'grooming_price' => $row['grooming_price'],
        'dog_id' => $row['dog_id'],
        'connected_reservation_id' => $row['connected_reservation_id'],
        'status' => $row['appointment_status'], // סטטוס ההזמנה (פעילה/בוטלה)
        'payment_status' => $row['payment_status'], // סטטוס התשלום
        'first_name' => $row['first_name'],
        'last_name' => $row['last_name'],
        'phone' => $row['phone'],
        'dog_name' => $row['dog_name'],
        'reservation_id' => $row['reservation_id'],
        'reservation_start' => $row['reservation_start'],
        'reservation_end' => $row['reservation_end']
    ];
    
    $appointments[] = $appointment;
}

// סגירת החיבור
$conn->close();

// החזרת מערך ההזמנות
// נועד לספק רשימה גולמית של נתונים לצורך תצוגת טבלה
// לא מחזיר הודעת הצלחה כי אין בה צורך כי אין פעולה עם תוצאה לוגית
echo json_encode($appointments);
?>