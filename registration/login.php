<?php
session_start();

// הגדרת header להחזרת תשובת JSON
header('Content-Type: application/json; charset=utf-8');

// התחברות למסד הנתונים
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);

// מאפשר עבודה עם טקסטים בעברית
$conn->set_charset("utf8");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'שגיאה בחיבור למסד הנתונים']);
    exit;
}

// מוודא שכל השדות הנדרשים הגיעו מהטופס
if (!isset($_POST['username'], $_POST['password'], $_POST['user_type'])) {
    echo json_encode(['success' => false, 'error' => 'שגיאה: נתונים חסרים מהטופס']);
    exit;
}

$username = $_POST['username'];
$password = $_POST['password'];
$user_type = $_POST['user_type'];

//  חיפוש של משתמש במסד בטבלה לפי שם משתמש, סיסמה וסוג משתמש
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ? AND user_type = ?");
// להציב את הפרמטרים בשאילתה
$stmt->bind_param("ssi", $username, $password, $user_type);
// להריץ את השאילתה
$stmt->execute();
// מכיל את השורות שהתקבלו מהשאילתה
$result = $stmt->get_result();

// אם התקבלו תוצאות מהשאילתה
if ($result->num_rows > 0) {
    // מערך של מילונים, כל מילון הוא שורה בטבלה של השאילתה
    $user = $result->fetch_assoc();
    
    // שמירת נתוני המשתמש ב-SESSION 
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_type'] = $user['user_type'];
    
    // אם קיים user_code בטבלת users, שמור אותו ב-SESSION
    if (isset($user['user_code'])) {
        $_SESSION['user_code'] = $user['user_code'];
    } 
   
    // קביעת הנתיב לפי סוג המשתמש
    $redirect_url = '';
    if ($user['user_type'] == 1) {
        // אפ סוג המשתמש הוא מנהל - מפנה אותו לדשבורד המנהל
        $redirect_url = 'admin/admin_dashboard_secured.php';
    } else {
        // אפ סוג המשתמש הוא משתמש רגיל - מפנה אותו לאזור האישי של המשתמש
        $redirect_url = 'user/user_dashboard_secured.php';
    }
    
    echo json_encode(['success' => true, 'redirect' => $redirect_url]);
} else {
    echo json_encode(['success' => false, 'error' => 'שם משתמש או סיסמה שגויים']);
}
$conn->close();
?>