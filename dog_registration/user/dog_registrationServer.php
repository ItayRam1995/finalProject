<?php
session_start();


// קבלת ה-user_code מה-session
$user_code = $_SESSION['user_code'];


$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// הגדרת לעברית
$conn->set_charset("utf8mb4");

// טיפול בהעלאת תמונה
$image_url = '';
try {
    if (isset($_FILES['dog_image']) && $_FILES['dog_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/dogs/'; // ווידוא שהתקייה קיימת ויש לה הרשאות כתיבה
        
        // יצירת התיקייה אם היא לא קיימת
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                throw new Exception("לא ניתן ליצור את תיקיית ההעלאות. בדוק הרשאות.");
            }
        }
        
        // בדיקה שהתיקייה ניתנת לכתיבה
        if (!is_writable($upload_dir)) {
            throw new Exception("תיקיית ההעלאות קיימת אך אין הרשאות כתיבה");
        }
        
        // בדיקה שנשלח קובץ תקין
        if ($_FILES['dog_image']['size'] == 0) {
            throw new Exception("הקובץ שנשלח ריק");
        }
        
        // קבלת סיומת הקובץ
        $file_extension = strtolower(pathinfo($_FILES['dog_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        // בדיקת סוג הקובץ
        if (!in_array($file_extension, $allowed_extensions)) {
            throw new Exception('סוג קובץ לא נתמך. יש להעלות קובץ מסוג JPG, PNG או GIF.');
        }
        
        // יצירת שם קובץ ייחודי
        $new_file_name = uniqid('dog_') . '.' . $file_extension;
        $upload_path = $upload_dir . $new_file_name;
        
        // העברת הקובץ עם בדיקת שגיאות
        if (!move_uploaded_file($_FILES['dog_image']['tmp_name'], $upload_path)) {
            $upload_error = error_get_last();
            throw new Exception('שגיאה בהעלאת התמונה: ' . ($upload_error ? $upload_error['message'] : 'סיבה לא ידועה'));
        }
        
        $image_url = $upload_path;
    }
} catch (Exception $e) {
    error_log('Image Upload Error: ' . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'שגיאה בהעלאת התמונה: ' . $e->getMessage()
    ]);
    exit;
}

// איסוף נתונים מהטופס
$dog_name = $conn->real_escape_string($_POST['dog_name']);
$gender = $conn->real_escape_string($_POST['gender']);
$chip_number = isset($_POST['chip_number']) ? $conn->real_escape_string($_POST['chip_number']) : '';
$breed = isset($_POST['breed']) ? $conn->real_escape_string($_POST['breed']) : '';
$age = isset($_POST['age']) && $_POST['age'] !== '' ? floatval($_POST['age']) : NULL;
$weight = isset($_POST['weight']) && $_POST['weight'] !== '' ? floatval($_POST['weight']) : NULL;
$color = isset($_POST['color']) ? $conn->real_escape_string($_POST['color']) : '';
$vaccinations_updated = isset($_POST['vaccinations_updated']) ? intval($_POST['vaccinations_updated']) : 0;
$health_notes = isset($_POST['health_notes']) ? $conn->real_escape_string($_POST['health_notes']) : '';
$general_notes = isset($_POST['general_notes']) ? $conn->real_escape_string($_POST['general_notes']) : '';
$dog_personality = isset($_POST['dog_personality']) ? $conn->real_escape_string($_POST['dog_personality']) : '';
$food_type = isset($_POST['food_type']) ? $conn->real_escape_string($_POST['food_type']) : '';
$daily_food_amount = isset($_POST['daily_food_amount']) ? $conn->real_escape_string($_POST['daily_food_amount']) : '';
$veterinarian_name = isset($_POST['veterinarian_name']) ? $conn->real_escape_string($_POST['veterinarian_name']) : '';
$veterinarian_phone = isset($_POST['veterinarian_phone']) ? $conn->real_escape_string($_POST['veterinarian_phone']) : '';

// הכנת שאילתת SQL להוספת הנתונים
$sql = "INSERT INTO dogs (
    user_code, image_url, dog_name, gender, chip_number, age, weight, breed, color, 
    vaccinations_updated, health_notes, general_notes, dog_personality, 
    food_type, daily_food_amount, veterinarian_name, veterinarian_phone
) VALUES (
    '$user_code', '$image_url', '$dog_name', '$gender', '$chip_number', $age, $weight, '$breed', '$color', 
    $vaccinations_updated, '$health_notes', '$general_notes', '$dog_personality', 
    '$food_type', '$daily_food_amount', '$veterinarian_name', '$veterinarian_phone'
)";

// תיקון לערך NULL בשדות מספריים
// תיקון ערכים ריקים
$sql = preg_replace('#,\s*,#', ', NULL,', $sql);
$sql = preg_replace('#=\s*,#', '= NULL,', $sql);

try {
    // ביצוע השאילתא
    if ($conn->query($sql) === TRUE) {
        echo json_encode([
            'status' => 'success',
            'message' => 'הכלב נרשם בהצלחה!',
            'dog_id' => $conn->insert_id
        ]);
    } else {
        throw new Exception($conn->error);
    }
} catch (Exception $e) {
    error_log('SQL Error: ' . $e->getMessage() . ' | Query: ' . $sql);
    echo json_encode([
        'status' => 'error',
        'message' => 'שגיאה בהוספת הכלב למסד הנתונים: ' . $e->getMessage()
    ]);
} finally {
    // סגירת החיבור למסד הנתונים 
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>