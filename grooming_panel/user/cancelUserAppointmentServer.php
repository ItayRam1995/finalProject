<?php
// בדיקת התחברות משתמש
// אם המשתמש לא מחובר מחזיר שגיאה שתוצג בעמוד groomingPanelUser
session_start();
if (!isset($_SESSION['user_code'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'נדרשת התחברות למערכת'
    ]);
    exit;
}

// קבלת קוד המשתמש מהסשן
$user_code = $_SESSION['user_code'];

// JSON מגדיר שהתגובה תחזור בפורמט 
header('Content-Type: application/json');

// בדיקה שהבקשה היא POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'שיטת הבקשה חייבת להיות POST'
    ]);
    exit;
}

// פרטי התחברות למסד הנתונים
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

// בדיקה שהנתונים התקבלו בפורמט JSON
//  גוף הבקשה הגולמית שנשלחה לשרת
// אנחנו מצפים לקבל בקשת POST עם תוכן JSON
// קורא את התוכן של הבקשה
// אם אין תוכן, מחזיר שגיאה
$raw_data = file_get_contents('php://input');
// אם לא התקבל בכלל תוכן (אם הבקשה ריקה)
if (empty($raw_data)) {
    echo json_encode([
        'success' => false,
        'error' => 'לא התקבלו נתונים בבקשה'
    ]);
    exit;
}

// פענוח של הנתונים שהגיעו בבקשת JSON ומוודא שהם תקינים
try {
     // מפענח את ה־ JSON
    //  לפענח את הטקסט הגולמי שהגיע מהלקוח למילון
    $data = json_decode($raw_data, true);
    
    // בודק האם קרתה שגיאה בעת הפענוח
    // בדיקה אם ה-JSON תקין
    if (json_last_error() !== JSON_ERROR_NONE) {
        // זורק שגיאה עם הסבר מפורט
        throw new Exception('נתונים לא תקינים: ' . json_last_error_msg());
    }
    
    // בדיקה שיש מספר אישור
    // בודק האם קיים מפתח בשם 'confirmation' ואם הוא לא ריק
    // כל הלוגיקה בנויה על מספר האישור הזה
    if (!isset($data['confirmation']) || empty($data['confirmation'])) {
        throw new Exception('מספר אישור חסר או לא תקין');
    }
    // שומר את מספר האישור במשתנה
    $confirmation = $data['confirmation'];
    
    // אם אחת השורות נכשלה, נזרקים לכאן
} catch (Exception $e) {
    // מחזיר ללקוח תשובת שגיאה עם הודעה
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}

// חיבור למסד הנתונים
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // בדיקת חיבור
    if ($conn->connect_error) {
        throw new Exception('שגיאה בחיבור למסד הנתונים: ' . $conn->connect_error);
    }
    
    // הגדרה לעברית
    // כדי למנוע בעיות תצוגה או שמירה של טקסטים בעברית
    $conn->set_charset("utf8");
    
    // לבדוק האם קיימת הזמנת טיפוח פעילה עם מספר אישור מסוים ושייכת למשתמש הנוכחי
    $check_stmt = $conn->prepare("
        SELECT g.id, g.user_code, g.dog_id, d.dog_name
        FROM grooming_appointments g
        LEFT JOIN dogs d ON g.dog_id = d.dog_id
        WHERE g.confirmation = ? AND g.isTaken = 1 AND g.user_code = ?
    ");
    
    if (!$check_stmt) {
        throw new Exception('שגיאה בהכנת השאילתה: ' . $conn->error);
    }
    
    // קישור פרמטרים - מספר אישור וקוד המשתמש
    $check_stmt->bind_param("ss", $confirmation, $user_code);
    // מריץ את השאילתה
    $check_stmt->execute();
    // שולף את התוצאה של השאילתה
    $check_result = $check_stmt->get_result();
    
    // אם לא נמצאה שורת תוצאה (כלומר, אין הזמנה כזו או שהיא לא פעילה או לא שייכת למשתמש) – זורק שגיאה
    if ($check_result->num_rows === 0) {
        throw new Exception('לא נמצאה הזמנה פעילה עם מספר האישור שצוין או שאינך מורשה לבטל הזמנה זו');
    }
    
    // שמירת פרטי ההזמנה לשימוש בהודעה
    $appointment_info = $check_result->fetch_assoc();
    // סוגר את השאילתה
    $check_stmt->close();
    
    // עדכון סטטוס ההזמנה - רק אם היא שייכת למשתמש הנוכחי
    $update_stmt = $conn->prepare("UPDATE grooming_appointments SET isTaken = 0 WHERE confirmation = ? AND user_code = ?");
    
    // אם השאילתה לא הוכנה כראוי – זורק שגיאה
    if (!$update_stmt) {
        throw new Exception('שגיאה בהכנת שאילתת העדכון: ' . $conn->error);
    }

    // מקשר את מספר האישור וקוד המשתמש לעדכון
    $update_stmt->bind_param("ss", $confirmation, $user_code);
    // מריץ את השאילתה
    $update_stmt->execute();
    
    // בודק האם העדכון שינה שורה במסד. אם לא ייתכן שכבר בוטלה קודם או שאינה שייכת למשתמש
    if ($update_stmt->affected_rows === 0) {
        throw new Exception('ההזמנה לא עודכנה. ייתכן שכבר בוטלה או שאינך מורשה לבטל הזמנה זו.');
    }
    
    $update_stmt->close();
    
    // הכנת הודעת החזרה עם מידע נוסף
    $success_message = 'ההזמנה בוטלה בהצלחה';
    
    if (!empty($appointment_info['dog_name'])) {
        $success_message .= ' עבור הכלב: ' . $appointment_info['dog_name'];
    }
    
    // החזרת תשובה חיובית
    // שולח תגובת JSON
    // עם הצלחה, מסר, מספר אישור ופרטי ההזמנה
    echo json_encode([
        'success' => true,
        'message' => $success_message,
        'confirmation' => $confirmation,
        'appointment_info' => [
        'dog_name' => $appointment_info['dog_name'] ?? ''
        ]
    ]);
    
    // אם משהו נכשל באחד השלבים, נתפסת החריגה ונשלחת הודעת שגיאה מפורטת ללקוח
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    
} finally {
    // סגירת החיבור במידה וקיים
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>