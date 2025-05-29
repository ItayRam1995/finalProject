<?php


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

// בדיקה שהבקשה היא POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // כדי למנוע המשך פעולות שלא אמורות להתבצע כשיש שגיאה
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'שיטת בקשה לא נתמכת'
    ]);
    // עוצר את המשך הריצה של הקוד מיד לאחר שליחת התגובה
    exit;
}

// פונקציה שתפקידה לבדוק ולנקות (הגנה על הקלט) כל שדה לפי סוגו
// מערך עם המידע
// שם השדה לבדיקה
// סוג השדה
// האם זה שדה חובה
function validateAndSanitize($data, $field_name, $type = 'string', $required = true) {
    // בדיקה אם השדה קיים ולא ריק במקרה שהוא חובה
    if (!isset($data[$field_name]) || ($required && empty(trim($data[$field_name])))) {
        return ['valid' => false, 'error' => "השדה '$field_name' הוא חובה"];
    }
    
    // מסיר רווחים מיותרים מההתחלה והסוף
    $value = trim($data[$field_name]);
    
    // ולידציה לפי סוג השדה
    switch ($type) {
        case 'string':
            // הסרת תגי HTML ותווים מסוכנים
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            // מגביל לאורך 255 תווים
            if (strlen($value) > 255) {
                return ['valid' => false, 'error' => "השדה '$field_name' ארוך מדי"];
            }
            break;
            
        case 'text':
            // טקסט ארוך - הסרת תגי HTML
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            // דומה ל־ string אך מאפשר אורך גדול יותר
            if (strlen($value) > 1000) {
                return ['valid' => false, 'error' => "השדה '$field_name' ארוך מדי"];
            }
            break;
            
        case 'number':
            // בודק שהקלט הוא מספר חיובי
            if (!is_numeric($value) || $value < 0) {
                return ['valid' => false, 'error' => "השדה '$field_name' חייב להיות מספר חיובי"];
            }
            $value = floatval($value);
            break;
            
        case 'phone':
            // ולידציה בסיסית לטלפון -מכיל רק מספרים, רווחים, פלוס וסוגריים
            if (!preg_match('/^[0-9\-\s\+\(\)]+$/', $value)) {
                return ['valid' => false, 'error' => "השדה '$field_name' מכיל תווים לא חוקיים"];
            }
            break;
            
        case 'chip':
            // ולידציה למספר שבב - רק מספרים
            if (!preg_match('/^[0-9]+$/', $value)) {
                return ['valid' => false, 'error' => "מספר השבב חייב להכיל רק ספרות"];
            }

            // דורש אורך של בין 10 ל־20 ספרות
            if (strlen($value) < 10 || strlen($value) > 20) {
                return ['valid' => false, 'error' => "מספר שבב חייב להיות באורך 10-20 ספרות"];
            }
            break;
            
        case 'boolean':
            // המרה לערך בוליאני
            // ממיר ערכים שונים ל־1 או 0 לפי רשימה מקובלת של ערכים תקינים
            $value = in_array($value, ['1', 'true', 'yes', 'כן']) ? 1 : 0;
            break;
    }
    
    // מחזיר את הערך המעובד כשהשדה תקין
    return ['valid' => true, 'value' => $value];
}

try {

    // קבלת מזהה המשתמש והכלב הפעיל
    $user_code = $_SESSION['user_code'];
    
    // בדיקה שיש כלב פעיל בסשן
    if (!isset($_SESSION['active_dog_id']) || empty($_SESSION['active_dog_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'לא נמצא כלב פעיל במערכת. אנא בחר כלב תחילה.'
        ]);
        exit;
    }
    
    // ממיר את המזהה למספר שלם
    $dog_id = intval($_SESSION['active_dog_id']);
    
    // ולידציה של כל השדות
    // רשימת כל השדות לבדיקה עם סוגם והאם הם חובה
    $fields_to_validate = [
        'dog_name' => ['type' => 'string', 'required' => true],
        'gender' => ['type' => 'string', 'required' => true],
        'chip_number' => ['type' => 'chip', 'required' => true],
        'breed' => ['type' => 'string', 'required' => true],
        'age' => ['type' => 'number', 'required' => true],
        'weight' => ['type' => 'number', 'required' => true],
        'color' => ['type' => 'string', 'required' => true],
        'vaccinations_updated' => ['type' => 'boolean', 'required' => true],
        'dog_personality' => ['type' => 'text', 'required' => true],
        'health_notes' => ['type' => 'text', 'required' => true],
        'food_type' => ['type' => 'string', 'required' => true],
        'daily_food_amount' => ['type' => 'string', 'required' => true],
        'veterinarian_name' => ['type' => 'string', 'required' => true],
        'veterinarian_phone' => ['type' => 'phone', 'required' => true],
        'general_notes' => ['type' => 'text', 'required' => true]
    ];
    
    // מערך ריק שיאחסן את הערכים התקינים והמסוננים של כל השדות
    // בסופו של דבר, זה המערך שישמש בעדכון למסד הנתונים
    $validated_data = [];
    
    // ולידציה של כל השדות
    // הלולאה עוברת על כל שדה שהוגדר מראש במערך $fields_to_validate
    // $field – שם השדה
    // $config – מילון עם סוג השדה והאם השדה הוא חובה
    foreach ($fields_to_validate as $field => $config) {
        // $_POST - כל מערך הקלט מהטופס
        // $field - שם השדה שכרגע נבדק
        // $config['type'] - סוג השדה – קובע את סוג הולידציה שתבוצע
        // $config['required'] - האם השדה חובה או לא
        $result = validateAndSanitize($_POST, $field, $config['type'], $config['required']);
        
        // בודק האם הולידציה נכשלה
        if (!$result['valid']) {
            echo json_encode([
                'status' => 'error',
                'message' => $result['error'],
                'field' => $field
            ]);
            exit;
        }
        
        // שומר את הערך שעבר סינון וולידציה במערך שישמש לעדכון
        $validated_data[$field] = $result['value'];
    }
    
    // חיבור למסד נתונים
    $servername = "localhost";
    $username = "itayrm_ItayRam";
    $password = "itay0547862155";
    $dbname = "itayrm_dogs_boarding_house";
    
    // אובייקט שעוזר להתחבר למסד נתונים, להריץ שאילתות ולקבל תוצאות
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
    // מוודאים שהמשתמש באמת בעל הכלב שהוא מנסה לעדכן
    $stmt = $pdo->prepare("SELECT dog_id FROM dogs WHERE dog_id = :dog_id AND user_code = :user_code");
    // הרצת השאילתה
    $stmt->execute([
        ':dog_id' => $dog_id,
        ':user_code' => $user_code
    ]);
    
    // בדיקה אם לא התקבלו תוצאות
    if (!$stmt->fetch()) {
        // החזרת שגיאה
        echo json_encode([
            'status' => 'error',
            'message' => 'אין הרשאה לעדכן כלב זה'
        ]);
        exit;
    }
    
    
    // טיפול בהעלאת תמונה חדשה (לא שדה חובה)
    // טיפול בהעלאת תמונה חדשה של הכלב

    // משתנה שיכיל את הנתיב של התמונה שהועלתה
    $new_image_path = null;

    // בודק האם השדה אכן קיים בטופס. כלומר, האם בכלל נשלח קובץ מהלקוח
    // וגם בודק האם לא התרחשה שום שגיאת העלאה
    if (isset($_FILES['dog_image']) && $_FILES['dog_image']['error'] === UPLOAD_ERR_OK) {
        
        
        // בדיקת סוג הקובץ
        // רשימת סוגי קבצים מותרים
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        // זיהוי סוג הקובץ שהועלה בפועל
        $file_type = $_FILES['dog_image']['type'];
        
        // בודק האם סוג הקובץ שזוהה לא נמצא בתוך הרשימה המותרת של סוגי קבצים
        if (!in_array($file_type, $allowed_types)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'סוג קובץ לא נתמך. אנא העלה קובץ JPG, PNG או GIF',
                'field' => 'dog_image'
            ]);
            exit;
        }
        
        // בדיקה אם גודל הקובץ שהועלה חורג מהמגבלה
        if ($_FILES['dog_image']['size'] > 5 * 1024 * 1024) {
            echo json_encode([
                'status' => 'error',
                'message' => 'גודל הקובץ גדול מדי. מקסימום 5MB',
                'field' => 'dog_image'
            ]);
            exit;
        }
        
        // יצירת שם קובץ ייחודי
        //  שם קובץ ייחודי עבור התמונה שהועלתה, כדי למנוע התנגשויות ושמירת קבצים עם שמות כפולים
        // מחזיר את השם המקורי של הקובץ שהועלה ואז מחלץ ממנו את הסיומת
        $file_extension = pathinfo($_FILES['dog_image']['name'], PATHINFO_EXTENSION);
        //  יצירת שם חדש ייחודי
        $new_filename = 'dog_' . $dog_id . '_' . time() . '.' . $file_extension;
        
        // נתיב לשמירת הקובץ
        $upload_dir = '../../dog_registration/user/uploads/dogs/';
        
        // יצירת התיקייה אם לא קיימת
        if (!is_dir($upload_dir)) {
            // אם הנתיב כולל כמה תיקיות שלא קיימות, ייצור את כולן
            mkdir($upload_dir, 0755, true);
        }
        
        //  יצירת הנתיב המלא לשמירת הקובץ
        // מחבר את נתיב התיקייה עם שם הקובץ
        $new_image_path = $upload_dir . $new_filename;
        
        // שמירת קובץ התמונה שהועלה לתוך התיקייה בשרת
        if (!move_uploaded_file($_FILES['dog_image']['tmp_name'], $new_image_path)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'שגיאה בשמירת התמונה',
                'field' => 'dog_image'
            ]);
            exit;
        }
        
        // שמירת הנתיב היחסי למסד הנתונים
        $new_image_path = '../../dog_registration/user/uploads/dogs/' . $new_filename;
        
    }
    
    // בניית שאילתת העדכון
    // רשימה של כל השדות במסד הנתונים שצריך לעדכן
    $update_fields = [
        'dog_name = :dog_name',
        'gender = :gender',
        'chip_number = :chip_number',
        'breed = :breed',
        'age = :age',
        'weight = :weight',
        'color = :color',
        'vaccinations_updated = :vaccinations_updated',
        'dog_personality = :dog_personality',
        'health_notes = :health_notes',
        'food_type = :food_type',
        'daily_food_amount = :daily_food_amount',
        'veterinarian_name = :veterinarian_name',
        'veterinarian_phone = :veterinarian_phone',
        'general_notes = :general_notes',
        'updated_at = NOW()'
    ];
    
    // הוספת שדה התמונה רק אם הועלתה תמונה חדשה
    if ($new_image_path) {
        // מוסיפים את שדה התמונה לרשימת השדות במסד הנתונים שצריך לעדכן
        $update_fields[] = 'image_url = :image_url';
        // שמים את הנתיב של התמונה ב־ $validated_data
        $validated_data['image_url'] = $new_image_path;
    }
    
    // יצירת השאילתה
    $sql = "UPDATE dogs SET " . implode(', ', $update_fields) . " WHERE dog_id = :dog_id AND user_code = :user_code";
    
    // הכנת השאילתה להרצה
    $stmt = $pdo->prepare($sql);
    
    // בניית מערך הפרמטרים
    // משתמשים במערך שהכיל את כל השדות התקינים
    $params = $validated_data;
    // מוסיפים אליו את שני הפרמטרים של התנאי
    $params['dog_id'] = $dog_id;
    $params['user_code'] = $user_code;
    
    
    // הרצת השאילתה
    // הוספת הפרמטרים לשאילתה 
    // מבצע את השאילתה עם כל הערכים שנשלחו מהטופס ועברו סינון
    $result = $stmt->execute($params);
    
    // בדיקת הצלחת השאילתה
    if ($result) {
        // מחזיר את מספר השורות שהשתנו בפועל
        $affected_rows = $stmt->rowCount();
        
        // אם שורה אחת לפחות עודכנה
        if ($affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'פרטי הכלב עודכנו בהצלחה',
                'dog_id' => $dog_id
            ]);

            // אם לא בוצע עדכון (כלומר, הערכים זהים למה שכבר קיים)
        } else {
            echo json_encode([
                'status' => 'info',
                'message' => 'לא בוצעו שינויים - הנתונים זהים לקיימים'
            ]);
        }
        // אם השאילתה נכשלה
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'שגיאה בביצוע העדכון'
        ]);
    }

    // שגיאת PDO - מסד נתונים
} catch (PDOException $e) {
    // שגיאה במסד הנתונים
    // בדיקה אם זו שגיאת מפתח כפול (מספר שבב כבר קיים)
    if ($e->getCode() == 23000) {
        echo json_encode([
            'status' => 'error',
            'message' => 'מספר השבב כבר קיים במערכת',
            'field' => 'chip_number'
        ]);

    // שגיאה כללית במסד הנתונים
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'שגיאה בעדכון הנתונים במסד הנתונים: ' . $e->getMessage()
        ]);
    }
    
} catch (Exception $e) {
    // שגיאה כללית
    echo json_encode([
        'status' => 'error',
        'message' => 'שגיאה כללית בעדכון פרטי הכלב: ' . $e->getMessage()
    ]);
}
?>