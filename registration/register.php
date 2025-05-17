<?php
// התחברות למסד הנתונים
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'שגיאה בחיבור למסד הנתונים']);
    exit;
}

// בדיקת שדות חובה
// עובר על כל שדות החובה ובודק שהם לא ריקים
$required_fields = ['username', 'password', 'first_name', 'last_name', 'city', 'street', 'house_number', 'zip_code', 'email', 'phone'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        die("שגיאה: שדה חסר - " . $field);
    }
}

$username = $_POST['username'];
$password = $_POST['password'];

// בדיקה אם הסיסמה כבר קיימת
$check = $conn->prepare("SELECT * FROM users WHERE password = ?");
// להציב את הסיסמא שהתקבלה מהטופס בשאילתה
$check->bind_param("s", $password);
// להריץ את השאילתה
$check->execute();
// מכיל את השורות שהתקבלו מהשאילתה
$result = $check->get_result();
if ($result->num_rows > 0) {
    die("שגיאה: הסיסמה כבר קיימת. אנא השתמש בסיסמה אחרת.");
}

// יצירת user_code ייחודי בגודל של שש ספרות
function generateUserCode($length = 6) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return 'USR-' . $code;
}


//  דואג לייצר קוד משתמש ייחודי
// ממשיך לייצר קוד משתמש עד שהוא לא מוצא קוד משתמש כזה יותר במסד הנתונים
// יוצר קוד משתמש רנדומלי 
$user_code = generateUserCode();
$check_code = $conn->prepare("SELECT * FROM users WHERE user_code = ?");
$check_code->bind_param("s", $user_code);
$check_code->execute();
while ($check_code->get_result()->num_rows > 0) {
    // אם הוא מצא קוד משתמש כזה במסד נתונים הוא מייצר שוב קוד משתמש
    $user_code = generateUserCode();
    $check_code->bind_param("s", $user_code);
    $check_code->execute();
}

// שאילתה להכנסת משתמש חדש לטבלת המשתמשים
$stmt = $conn->prepare("INSERT INTO users (username, password, first_name, last_name, city, street, house_number, zip_code, email, phone, user_type, user_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)");
$stmt->bind_param("sssssssisss",
    $_POST['username'],
    $_POST['password'],
    $_POST['first_name'],
    $_POST['last_name'],
    $_POST['city'],
    $_POST['street'],
    $_POST['house_number'],
    $_POST['zip_code'],
    $_POST['email'],
    $_POST['phone'],
    $user_code
);

// אם הרישום למסד הנתונים הצליח
if ($stmt->execute()) {
    // שומר את שם המשתמש והסיסמה ב־ sessionStorage של הדפדפן
    // מפנה את המשתמש לעמוד ההתחברות
    // בעמוד ההתחברות הוא שולף את השם והסיסמא מהדפדפן 

    // מוסיף \ לפני תווים שעלולים לשבור קוד addslashes
            echo "
    <script>
      sessionStorage.setItem('username', '" . addslashes($_POST['username']) . "');
      sessionStorage.setItem('password', '" . addslashes($_POST['password']) . "');
      window.location.href = 'login.html';
    </script>
    ";
    
} else {
    echo "שגיאה בהרשמה: " . $stmt->error;
}

$conn->close();
?>
