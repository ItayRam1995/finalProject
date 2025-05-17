<?php
// התחלת SESSION
session_start();


// בקובץ treatmets.php שלחנו
// קובץ json
// עם סוג הטיפוח והמחיר
//  עכשיו קובץ teatments.php ממתין שהשרת ישלח אליו חזרה גם
// קובץ json
//  ולפי התשובה ש treatmets.php יקבל 
// הוא ידע לנווט לעמוד של הזמנת תור


// הגדרת סוג התוכן להיות JSON
header('Content-Type: application/json');

try {
    // קבלת הנתונים שנשלחו ב-POST
    // מקבל את גוף הבקשה שהגיע מ־ fetch
    $data = json_decode(file_get_contents("php://input"), true);
    
    // בדיקה שהתקבלו הנתונים הנדרשים
    if (!isset($data['grooming_type']) || !isset($data['grooming_price'])) {
        throw new Exception("חסרים נתונים");
    }
    
    // שמירת הנתונים ב-SESSION
    $_SESSION['grooming_type'] = $data['grooming_type'];
    $_SESSION['grooming_price'] = intval($data['grooming_price']);
    
    // החזרת תשובה חיובית ל treatments.php
    echo json_encode([
        'success' => true,
        'message' => 'הנתונים נשמרו בהצלחה'
    ]);
    
} catch (Exception $e) {
    // במקרה של שגיאה
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>