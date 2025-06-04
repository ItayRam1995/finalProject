<?php

// כדי שנוכל לשמור את הטוקן
session_start();


// oauth2callback.php - טיפול בתשובה מגוגל:
// השלב השני בתהליך ההרשאה 
// כאן אנחנו מקבלים את קוד ההרשאה שגוגל מחזירה לנו, וממירים אותו ל־ access token ו־refresh token.

// קבלת קוד ההרשאה והחלפתו בטוקן גישה
// בדיקות אבטחה (state validation)
// טיפול בשגיאות והצגת הודעות מתאימות
// הפניה אוטומטית במקרה של הצלחה

// הגדרות OAuth של Google - מקבלים את הערכים האלה מ- Google Cloud Console
$client_id = '929814436812-f7k03a3sq083jivb20qrppevh5fpbm2b.apps.googleusercontent.com'; // // מזהה ייחודי של האפליקציה
$client_secret = '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'; // 
$redirect_uri = 'https://itayrm.mtacloud.co.il/Itay-testing-zone/finalProject/googleCalendar/user/oauth2callback.php'; // כתובת החזרה אחרי ההרשאה

// משתנים לשמירת מצב השגיאה/הצלחה
$error = null;
$success = false;


// אם גוגל החזירה שגיאה (למשל המשתמש ביטל את ההרשאה)
if (isset($_GET['error'])) {
    $error = "שגיאה מ Google: " . $_GET['error'];

    // אם התשובה שהתקבלה מגוגל לא כוללת מחרוזת אבטחה, או שמחרוזת האבטחה לא תואמת למחרוזת שנשמרה בסשן כששלחנו בהתחלה את הבקשה לגוגל
    // Cross-Site Request Forgery בדיקת אבטחה לצורך מניעת התקפות 
} elseif (!isset($_GET['state']) || $_GET['state'] != $_SESSION['oauth_state']) {
    $error = "שגיאת אבטחה: מחרוזת האבטחה לא תקינה ולא תואמת למשתנה הסשן";

  // בדיקה שקיבלנו קוד הרשאה מגוגל
} elseif (!isset($_GET['code'])) {
    $error = "לא התקבל קוד הרשאה מ Google";

} else {
    // אם הכל תקין, ממשיכים לעיבוד
    $code = $_GET['code'];
    
    //  שליחת הבקשה בשביל להמיר את הקוד לטוקן
    // השלב השני של OAuth - החלפת הקוד בטוקן גישה
    // זאת הכתובת של גוגל שאליה שולחים את הבקשה כדי להחליף את הקוד הרשאה בטוקן גישה
    $token_url = 'https://oauth2.googleapis.com/token';
    // בונה את הנתונים שיישלחו לגוגל
    $post_data = array(
        'client_id' => $client_id, // מזהה את האפליקציה מול גוגל
        'client_secret' => $client_secret, // מפתח סודי בין האפליקציה לגוגל
        'code' => $code, // זה קוד ההרשאה שגוגל החזירה לאחר קבלת ההרשאה (בשלב הקודם). משתמשים בשביל לקבל טוקן הרשאה
        'grant_type' => 'authorization_code', // איזה סוג של תהליך הרשאה אנחנו מבצעים
        'redirect_uri' => $redirect_uri // חייב להיות זהה לזה ששלחנו בשלב הראשון - לאן גוגל תחזיר את המשתמש
    );

    // הכנת בקשת cURL להחלפת הקוד בטוקן
    // פתיחת "שיחה" עם שרת של גוגל
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url); // קובע את הכתובת לבקשה
    curl_setopt($ch, CURLOPT_POST, true); //  מגדיר שהבקשה תישלח כ־ HTTP POST – ולא GET, בשביל שהנתונים לא יהיו חלק מהכתובת
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data)); // תוכן הבקשה
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // במקום להדפיס ישירות את התגובה, מחזיר אותה לתוך משתנה
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // במערכת production כדאי להפעיל את זה

    // ביצוע הבקשה
    // מבצע בפועל את הבקשה לגוגל ומחזיר את הבקשה כמחרוזת JSON
    $response = curl_exec($ch);

    // שולף את קוד התגובה מהשרת של גוגל אליו פנינו: 200, 400,401,500
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);


    // טיפול בתגובה מהשרת של גוגל אחרי שליחת הבקשה
    // בדיקת שגיאות בבקשה
    // אם הייתה שגיאת חיבור או תקלה טכנית
    if (curl_error($ch)) {
        $error = "שגיאת cURL: " . curl_error($ch);

        // אם גוגל החזירה קוד שגיאה
        // 200 = הצלחה
    } elseif ($http_code != 200) {
        $error = "שגיאה בקבלת טוקן (HTTP $http_code): " . $response;


        // אם אין שגיאה עד כה – מפענחים את התשובה מ־ JSON למערך
    } else {
        // המרה מ JSON למילון
        $token_data = json_decode($response, true);
        
        // אם הפענוח נכשל או אם יש שדה שגיאה בתשובה שהתקבלה מגוגל
        if (!$token_data || isset($token_data['error'])) {
            $error = "שגיאה בהמרה לטוקן";
            if (isset($token_data['error_description'])) {
                $error .= ": " . $token_data['error_description'];
            }

            // שמירת טוקן הגישה בסשן – כך שנוכל להשתמש בו בבקשות לגוגל קאלאנדר
        } else {
            $_SESSION['access_token'] = $token_data['access_token']; // טוקן הגישה לשימוש ב - API

            // אם גוגל שלחה גם טוקן לשחזור שומרים אותו גם כן
            if (isset($token_data['refresh_token'])) {
                // refresh token מאפשר לקבל טוקן חדש כשהקיים יפוג בלי שהמשתמש יצטרך להתחבר שוב
                $_SESSION['refresh_token'] = $token_data['refresh_token'];
            }
            //  סימון שהכול עבר בהצלחה
            $success = true;
        }
    }
    
    // סיום השיחה מול השרת של גוגל
    curl_close($ch);
}

// האם תהליך קבלת הטוקן הצליח
//  האם עדיין אפשר לשלוח כותרות
// אם כבר נשלח תוכן לדפדפן לא ניתן לשלוח עוד תוכן לדפדפן כי אחרת זה יגרום לשגיאה

if ($success && !headers_sent()) {
    //  מפנים לדף יצירת האירועים בגוגל קאלאנדר של המשתמש
    header('Location: create_events.php');
    exit(); // מפסיק את הריצה של הסקריפט מייד כך שלא יתבצע שום קוד נוסף בטעות (ולא יודפס כלום למסך)
}
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>תוצאת הרשאה</title>
    <style>
        /* עיצוב בסיסי של הדף */
        body { 
            font-family: Arial, sans-serif; 
            max-width: 600px; 
            margin: 50px auto; 
            padding: 20px; 
            text-align: center; 
        }
        
        /* עיצוב הודעת הצלחה */
        .success { 
            background: #d4edda; /* רקע ירוק בהיר */
            padding: 20px; 
            border-radius: 5px; 
            color: #155724; /* טקסט ירוק כהה */
            border: 1px solid #c3e6cb; 
        }
        
        /* עיצוב הודעת שגיאה */
        .error { 
            background: #f8d7da; /* רקע אדום בהיר */
            padding: 20px; 
            border-radius: 5px; 
            color: #721c24; /* טקסט אדום כהה */
            border: 1px solid #f5c6cb; 
        }
        
        /* עיצוב כפתורים */
        .btn { 
            display: inline-block; 
            background: #007bff; 
            color: white; 
            padding: 15px 30px; 
            text-decoration: none; 
            border-radius: 5px; 
            margin: 10px; 
        }
    </style>
</head>
<body>
    <?php if ($success): ?>
        <!-- הודעת הצלחה -->
        <div class="success">
            <h2>🎉 הרשאה הצליחה!</h2>
            <p>הטוקן נשמר בהצלחה. עכשיו נוכל ליצור אירועים בלוח השנה שלך.</p>
            <a href="create_events.php" class="btn">המשך ליצירת אירועים</a>
        </div>
    <?php else: ?>
        <!-- הודעת שגיאה -->
        <div class="error">
            <h2>❌ שגיאה בהרשאה</h2>
            <p><?php echo htmlspecialchars($error); ?></p>
            <a href="index.html" class="btn">חזור לדף הבית</a>
            <a href="authorize.php" class="btn">נסה שוב</a>
        </div>
    <?php endif; ?>
</body>
</html>