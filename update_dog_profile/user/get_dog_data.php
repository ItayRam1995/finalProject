<?php

//  לשלוף נתוני כלב מסוים עבור משתמש מחובר, על בסיס מזהה הכלב הפעיל
// JSON מגדיר שהתגובה תחזור בפורמט 
header('Content-Type: application/json; charset=utf-8');

session_start();

// בדיקה שהמשתמש מחובר למערכת
if (!isset($_SESSION['user_code'])) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'המשתמש אינו מחובר למערכת'
    ]);
    exit;
}

// בדיקה שיש כלב פעיל בסשן
if (!isset($_SESSION['active_dog_id']) || empty($_SESSION['active_dog_id'])) {
    // כדי למנוע המשך פעולות שלא אמורות להתבצע כשיש שגיאה
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'לא נמצא כלב פעיל במערכת. אנא בחר כלב תחילה.'
    ]);
    // עוצר את המשך הריצה של הקוד מיד לאחר שליחת התגובה
    exit;
}

// קבלת מזהה הכלב מהסשן
$dog_id = intval($_SESSION['active_dog_id']);
// קבלת קוד המשתמש מסשן
$user_code = $_SESSION['user_code'];

try {

    // חיבור למסד נתונים
    $servername = "localhost";
    $username = "itayrm_ItayRam";
    $password = "itay0547862155";
    $dbname = "itayrm_dogs_boarding_house";
    
    // אובייקט שעוזר להתחבר למסד נתונים, להריץ שאילתות ולקבל תוצאות
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    // מאפשר להשתמש ב־ try/catch כדי לטפל בשגיאות בצורה מסודרת
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // שאילתה לקבלת פרטי הכלב - רק אם הכלב שייך למשתמש המחובר
    $stmt = $pdo->prepare("
        SELECT 
            dog_id,
            dog_name,
            gender,
            chip_number,
            breed,
            age,
            weight,
            color,
            vaccinations_updated,
            image_url,
            dog_personality,
            health_notes,
            food_type,
            daily_food_amount,
            veterinarian_name,
            veterinarian_phone,
            general_notes,
            created_at,
            updated_at
        FROM dogs 
        WHERE dog_id = :dog_id 
        AND user_code = :user_code
    ");
    
    // הרצת השאילתה עם הפרמטרים
    $stmt->execute([
        ':dog_id' => $dog_id,
        ':user_code' => $user_code
    ]);
    
    // קבלת התוצאה
    $dog_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // בדיקה אם נמצא כלב עם המזהה הנתון
    if (!$dog_data) {
        echo json_encode([
            'status' => 'error',
            'message' => 'לא נמצא כלב עם המזהה הנתון או שאינך מורשה לגשת אליו'
        ]);
        exit;
    }
    
    // המרת נתוני החיסונים לטקסט מובן
    $dog_data['vaccinations_updated'] = $dog_data['vaccinations_updated'] ? '1' : '0';
    
    // להתאים את כתובת התמונה של הכלב כך שתהיה תקינה עבור הדפדפן, גם אם מדובר בנתיב יחסי ולא כתובת מלאה
    if (!empty($dog_data['image_url'])) {
        //  לבדוק את סוג הנתיב
        // בודקים האם הנתיב אינו URL מוחלט
        // אם הנתיב לא מתחיל ב-http, מניחים שזה נתיב יחסי ומוסיפים את הבסיס
        // אם התוצאה היא 0, סימן שהמחרוזת מתחילה בדיוק באותו רצף
        if (strpos($dog_data['image_url'], 'http://') !== 0 && strpos($dog_data['image_url'], 'https://') !== 0) {
            // התאמת הנתיב בהתאם למבנה התיקיות של האתר
            // יוצרים נתיב יחסי מלא לקובץ התמונה, שמצביע על התיקייה שבה מאוחסנות תמונות הכלבים
            $dog_data['image_url'] = '../../dog_registration/user/uploads/dogs/' . basename($dog_data['image_url']);
        }
    }
    
    // החזרת הנתונים בהצלחה
    echo json_encode([
        'status' => 'success',
        'data' => $dog_data,
        'message' => 'נתוני הכלב נטענו בהצלחה'
    ]);

    // טיפול בשגיאה שיכולה להתרחש בזמן התחברות למסד הנתונים 
    // האובייקט של החריגה, שמכיל מידע על השגיאה
} catch (PDOException $e) {
    // שגיאה במסד הנתונים
    error_log("Database error in get_dog_data.php: " . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'message' => 'שגיאה בגישה למסד הנתונים'
    ]);
    
} catch (Exception $e) {
    // שגיאה כללית
    error_log("General error in get_dog_data.php: " . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'message' => 'שגיאה כללית בטעינת נתוני הכלב'
    ]);
}
?>