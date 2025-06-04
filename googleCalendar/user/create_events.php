<?php include '../../header.php'; ?>
<?php
session_start();


// create_events.php - דף לסנכרון הזמנות טיפוח ושהייה בגוגל קלנדר:


//  * אימות הרשאות והתחברות
//  * חיבור למסד הנתונים
//  * אנימציית טעינה ראשונית וסרגל התקדמות
//  * ניקוי אירועים לא רלוונטים (תורים שבוטלו, הזמנות שנמחקו)
//  * יצירת אירועים חדשים עם מניעת כפילויות
//  * הצגת סיכום מפורט למשתמש


// בדיקה שהמשתמש מחובר לגוגל - אם לא, מפנה לדף ההרשאה
//  * Google OAuth2 בודק האם המשתמש עבר את תהליך ההרשאה של
//  * ה-access_token נשמר בסשן לאחר הרשאה מוצלחת בקובץ oauthcallback.php
//  * אם הטוקן לא קיים - המשתמש מופנה אוטומטית לדף authorize.php להתחלת תהליך ההרשאה מחדש
if (!isset($_SESSION['access_token'])) {
    header('Location: authorize.php');
    exit;
}


//  * פונקציה לשליחת בקשות ל- Google Calendar API

//    פרמטרים:
//  * $url - כתובת ה-API
//   https://www.googleapis.com/calendar/v3/calendars/primary/events
//   הכתובת הרישמית של Google Calendar API ליצירה, קריאה, עדכון ומחיקה של אירועים בלוח השנה הראשי של המשתמש המחובר

//  * $method - סוג הבקשה (GET/POST/DELETE)
//  *   - 'GET' (ברירת מחדל) - לקריאת נתונים
//  *   - 'POST' - ליצירת אירוע חדש
//  *   - 'DELETE' - למחיקת אירוע קיים


//  * array $data - נתונים לשליחה (אופציונלי)

//    החזרה: 
//  *  array - תשובה מה-API בפורמט מילון

function makeCalendarRequest($url, $method = 'GET', $data = null) {
    // שליפת טוקן הגישה מהסשן
    $access_token = $_SESSION['access_token'];
    
    // הגדרת בקשת cURL
    //  Client URL היא ספרייה ב־ PHP שמאפשרת לשלוח ולקבל בקשות בין שרתים

    // פתיחת "שיחה" עם שרת של גוגל
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); // קובע את הכתובת לבקשה
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // במקום להדפיס ישירות את התגובה, מחזיר אותה לתוך משתנה

    // חלק חשוב בפרוטוקול התקשורת בין שרתים לשרתים בשביל שגוגל תבין את הפורמט שאני שולח ותדע מי אני
    // כמו מעטפה חיצונית של מכתב - מי השולח? לאן לשלוח? סוג תוכן
    curl_setopt($ch, CURLOPT_HTTPHEADER, [ //  לתת מידע נלווה שישלח עם הבקשה   
        'Authorization: Bearer ' . $access_token, // זיהוי המשתמש והאפליקציה מול גוגל
        'Content-Type: application/json' // הצהרה שהבקשה שתשלח תהיה בפורמט JSON בשביל שזה יקרא נכון על ידי ה Googlr API
    ]);
    
    // התאמת הבקשה לפי סוג המתודה
    // שליחת מידע ליצירת אירוע חדש
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true); // אומרים ל curl שהולכת להיות בקשת POST
        if ($data) {
            // המרת מערך PHP ל-JSON לפני השליחה
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

    // תישלח בקשת HTTP DELETE – מחיקת אירוע לפי מזהה
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE'); // אומרים ל curl שהולכת להיות בקשת DELETE
    }
    
    // ביצוע הבקשה
    $response = curl_exec($ch);  // ביצוע וקבלת תשובה
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); //  קוד סטטוס HTTP
    curl_close($ch); // סגירת החיבור
    
    // טיפול בשגיאות
    if ($http_code === 401) {
        throw new Exception('טוקן הגישה פג תוקף. אנא התחבר מחדש.');
    }
    
    if ($http_code >= 400) {
        throw new Exception('שגיאה ב-API: HTTP ' . $http_code . ' - ' . $response);
    }
    
    // Google מחזירה JSON
    // php ממיר את ה- JSON למילון
    return json_decode($response, true);
}


//  * בודק אם אירוע עם כותרת וזמן מסוים כבר קיים ביומן
//  * למניעת יצירת אירועים כפולים בסנכרונים חוזרים
//   1. מגדיר חלון זמן של 24 שעות (היום הספציפי)
//   2. מחפשת אירועים עם כותרת זהה בחלון הזמן
//   3.  true אם נמצא לפחות אירוע אחד - מחזירה


//  * $title - כותרת האירוע
        //  טיפוח: רחצה וסירוק

//  * $start_time - זמן התחלת האירוע

//  *  מזהה היומן - היומן הראשי (ברירת מחדל)

//  * return  האם האירוע קיים
        // true - האירוע קיים, לא ליצור שוב
        // false - האירוע לא קיים, אפשר ליצור
 
function eventExists($title, $start_time, $calendar_id = 'primary') {
    // הגדרת טווח החיפוש - היום הספציפי בלבד
    // המרת זמן התחלה לתאריך (YYYY-MM-DD)
    $target_date = date('Y-m-d', strtotime($start_time)); // לדוגמה: 2025-06-03
    $timeMin = fixTimezone($target_date . ' 00:00:00'); // מ-00:00 של היום 
    $timeMax = fixTimezone($target_date . ' 23:59:59'); /// עד 23:59 של היום 
    
    // בניית URL לחיפוש
    // יוצר כתובת לבקשה מה־ API של Google Calendar, עם הפרמטרים שיבואו
    // משתמשים ב-events endpoint עם פרמטרי סינון
    $url = "https://www.googleapis.com/calendar/v3/calendars/{$calendar_id}/events?" . http_build_query([

        'timeMin' => $timeMin,
        'timeMax' => $timeMax,
        'q' => $title // חיפוש לפי הכותרת
    ]);
    
    try {
        // ביצוע החיפוש
        $result = makeCalendarRequest($url);

        // בדיקה אם נמצאו תוצאות
        // items - מערך האירועים שנמצאו
        return isset($result['items']) && count($result['items']) > 0;
    } catch (Exception $e) {
        return false; // במקרה של שגיאה, מניחים שהאירוע לא קיים
    }
}


//  * מוצא אירועים לפי כותרת ותאריך - חיפוש אירועים למחיקה
//    פרמטרים: 
//  *  $title - כותרת האירוע
//  *  $date - התאריך לחיפוש
//  *  $calendar_id - מזהה היומן
//     החזרה: 
//  *  array - רשימת אירועים שנמצאו

function findEventsByTitleAndDate($title, $date, $calendar_id = 'primary') {
    // הגדרת טווח החיפוש - היום הספציפי בלבד
    // המרת זמן התחלה לתאריך (YYYY-MM-DD)
    $target_date = date('Y-m-d', strtotime($date)); // לדוגמה: 2025-06-03
    $timeMin = fixTimezone($target_date . ' 00:00:00'); // מ-00:00 של היום 
    $timeMax = fixTimezone($target_date . ' 23:59:59'); /// עד 23:59 של היום 

    // בניית URL לחיפוש
    // יוצר כתובת לבקשה מה־ API של Google Calendar, עם הפרמטרים שיבואו
    // משתמשים ב-events endpoint עם פרמטרי סינון
    $url = "https://www.googleapis.com/calendar/v3/calendars/{$calendar_id}/events?" . http_build_query([
        'timeMin' => $timeMin,
        'timeMax' => $timeMax,
        'q' => $title // חיפוש לפי הכותרת
    ]);
    
    try {
        // ביצוע החיפוש
        // $result -  מכיל את התשובה שהתקבלה מהגוגל קלנדר בהתאם לכתובת עם הפרמטרים שהוכנה כאן
        //  המשתנה "תוצאה" מכיל את תשובת החזרה בפורמט של מילון שהתקבל מגוגל
        // גוגל החזירה תשובה בפורמט JSON והפונקציה makeCalendarRequest המירה אותו למילון ושלחה אותו לכאן
        $result = makeCalendarRequest($url);
        //   מחזירה מערך עם כל האירועים שנמצאו בגוגל קלנדר או שהיא מחזירה מערך ריק
        return isset($result['items']) ? $result['items'] : [];
    } catch (Exception $e) {
        return [];
    }
}


//  * מוצא אירועי פנסיון בטווח תאריכים
//     פרמטרים: 
//  *  $start_date - תאריך התחלה
//  *  $end_date - תאריך סיום
//  *  $calendar_id - מזהה היומן
//     החזרה: 
//  *   array - רשימת אירועי פנסיון

function findBoardingEventsByDateRange($start_date, $end_date, $calendar_id = 'primary') {
    // הגדרת טווח החיפוש - היום הספציפי בלבד
    //  המרת זמן התחלה לתאריך (YYYY-MM-DD)
    $timeMin = fixTimezone($start_date . ' 00:00:00'); // מ-00:00 של היום 
    $timeMax = fixTimezone($end_date . ' 23:59:59'); /// עד 23:59 של היום 

    // בניית URL לחיפוש
    // יוצר כתובת לבקשה מה־ API של Google Calendar, עם הפרמטרים שיבואו
    // משתמשים ב-events endpoint עם פרמטרי סינון
    $url = "https://www.googleapis.com/calendar/v3/calendars/{$calendar_id}/events?" . http_build_query([
        'timeMin' => $timeMin,
        'timeMax' => $timeMax,
        'q' => '🏠 שהייה בפנסיון' // חיפוש לפי כותרת ספציפית
    ]);
    
    try {
        // ביצוע החיפוש
        // $result -  מכיל את התשובה שהתקבלה מהגוגל קלנדר בהתאם לכתובת עם הפרמטרים שהוכנה כאן
        //  המשתנה "תוצאה" מכיל את תשובת החזרה בפורמט של מילון שהתקבל מגוגל
        // גוגל החזירה תשובה בפורמט JSON והפונקציה makeCalendarRequest המירה אותו למילון ושלחה אותו לכאן
        $result = makeCalendarRequest($url);
        //   מחזירה מערך עם כל האירועים שנמצאו בגוגל קלנדר או שהיא מחזירה מערך ריק
        return isset($result['items']) ? $result['items'] : [];
    } catch (Exception $e) {
        return [];
    }
}

//  * מוחק אירוע מהיומן
//  *  $event_id - מזהה האירוע 
//       מתקבל מ- Google בעת יצירה או חיפוש

//  *  $calendar_id - מזהה היומן
//  * return  האם המחיקה הצליחה
        // true - המחיקה הצליחה
        // false - המחיקה לא הצליחה

function deleteEvent($event_id, $calendar_id = 'primary') {
    // בניית URL למחיקת אירוע ספציפי
    $url = "https://www.googleapis.com/calendar/v3/calendars/{$calendar_id}/events/{$event_id}";
    
    try {
        makeCalendarRequest($url, 'DELETE');
        return true;
    } catch (Exception $e) {
        return false;
    }
}


//  * מתקן את ה-timezone לישראל
//  *  $datetime_string - מחרוזת תאריך וזמן

//   החזרה :
//  *  תאריך וזמן בפורמט ISO 8601 עם אזור זמן ישראלי

function fixTimezone($datetime_string) {
   // יצירת אובייקט DateTime עם אזור זמן ישראלי
    $dt = new DateTime($datetime_string, new DateTimeZone('Asia/Jerusalem'));
    return $dt->format('c'); // ISO 8601 format - הפורמט התקני לתאריכים ושעות באינטרנט וב־ APIs כמו Google Calendar
}
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>סנכרון Google Calendar</title>
    <style>

        /* עיצוב כללי של הדף */
        body { 
            font-family: Arial, sans-serif; 
            /* מגביל את רוחב הדף ל־800 פיקסלים */
            max-width: 800px; 
            /* ממרכז את הדף אופקית */
            margin: 20px auto; 
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* רקע  סגול */
            min-height: 100vh; /* גובה מינימלי */
            color: #333;
        }
        
        /* אנימציית טעינה ראשונית במסך מלא */
        .initial-loader {
            /* מכסה את כל המסך */
            position: fixed; /* מיקום קבוע על המסך */
            top: 0; left: 0; right: 0; bottom: 0; /* כיסוי מלא */
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* רקע  סגול */
            /* ממורכז אנכית ואופקית */
            display: flex;
            flex-direction: column; /* סידור אנכי */
            justify-content: center; /* מרכוז אנכי */
            align-items: center; /* מרכוז אופקי */
            z-index: 9999; /* מעל הכול */
            color: white;   /* טקסט בצבע לבן */
        }
        
        /* ספינר טעינה ראשי */
        /* עיגול עם גבול מסתובב */
        .main-spinner {
            width: 60px; height: 60px; /* גודל הספינר */
            border: 6px solid rgba(255,255,255,0.3); /* גבול שקוף למחצה */
            border-top: 6px solid white; /* חלק עליון לבן */
            border-radius: 50%;  /* הפיכה לעיגול */
            animation: spin 1s linear infinite; /* אנימציית סיבוב */
            margin-bottom: 30px; /* רווח מהטקסט */
        }
        
        /* טקסט במסך הטעינה */
        .loader-text {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
        
        /* טקסט משני עם אנימציית הבהוב */
        .loader-subtext {
            font-size: 16px;
            opacity: 0.8;
            text-align: center;
            animation: pulse 2s ease-in-out infinite;
        }
        
        /* אנימציית הבהוב */
        @keyframes pulse {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 1; }
        }
        
        /* מיכל התוכן העיקרי */
        /* מיכל לבן עם פינות מעוגלות וצל, מוסתר בהתחלה */
        /* מוצג לאחר שהטעינה מסתיימת */
        .main-content {
            /* רקע לבן */
            background: white;
            /* ריווח פנימי */
            padding: 30px;
            /* פינות מעוגלות */
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            display: none; /* מוסתר בהתחלה */
        }
        
        /* ספינר טעינה קטן */
        .loading { 
            /* מאפשר לשים את הספינר בתוך טקסט או כפתור */
            display: inline-block; 
            width: 20px; height: 20px; 
            /* אפור בהיר */
            border: 3px solid #f3f3f3; 
            /* גבול עליון בצבצ כחול */
            border-top: 3px solid #3498db; 
            /* הפיכה לעיגול מושלם */
            border-radius: 50%; 
            animation: spin 1s linear infinite; 
            margin-left: 10px;
        }
        
        /* אנימציית סיבוב */
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        /* סגנונות להודעות מסוגים שונים */
        .success { 
            background: #d4edda; /* רקע ירוק בהיר */
            padding: 15px; /* ריווח פנימי */
            border-radius: 5px;  /* פינות מעוגלות */
            color: #155724; /* צבע טקסט ירוק כהה */
            border: 1px solid #c3e6cb; /* מסגרת ירוקה דקה */
            margin: 10px 0; /* רווח מעל ומתחת */
            }
        .error { 
            background: #f8d7da;  /* רקע אדום בהיר */
            padding: 15px;
            border-radius: 5px;
            color: #721c24;
            border: 1px solid #f5c6cb;
            margin: 10px 0;
            }
        .info { 
            background: #d1ecf1; /* רקע כחול בהיר */
            padding: 15px;
            border-radius: 5px;
            color: #0c5460;
            border: 1px solid #bee5eb;
            margin: 10px 0;
            }
        .warning {
            background: #fff3cd; /* רקע כתום בהיר */
            padding: 15px;
            border-radius: 5px;
            color: #856404;
            border: 1px solid #ffeaa7;
            margin: 10px 0;
            }
        
        /* עיצוב כפתורים */
        .btn { 
            display: inline-block; /* התנהגות של בלוק בתוך שורה */
            padding: 15px 30px;
            text-decoration: none; /* בלי קו תחתון (לקישורים) */
            border-radius: 5px; /* פינות מעוגלות */
            margin: 10px;/* רווחים בין כפתורים */
            color: white; /* טקסט לבן */
            transition: all 0.3s; /* אנימציית מעבר חלקה */
            cursor: pointer;/* סמן יד */
            }
            
        .btn-primary { background: #007bff; } /* צבע רקע כחול */
        .btn-success { background: #28a745; }/* צבע רקע ירוק */
        
        /* סרגל התקדמות */
        .progress-bar { 
            width: 100%;                         /* רוחב מלא */
            background-color: #e0e0e0;         /* רקע אפור בהיר */
            border-radius: 10px;                 /* פינות מעוגלות */
            margin: 20px 0;                      /* רווח מעל ומתחת */
            overflow: hidden;                    /* הסתרת תוכן חורג */
            }
            .progress-fill { 
            height: 20px;                        /* גובה קבוע */
            background-color: #4caf50;         /* ירוק מלא */
            border-radius: 10px;                 /* פינות מעוגלות */
            width: 0%;                           /* מתחיל ריק – משתנה דינמית ב-JS */
            transition: width 0.3s;              /* מעבר חלק בעת שינוי רוחב */
            }
        
        /* אזור הסטטוס */
        #status { margin: 20px 0; } /* רווחים */
    </style>
</head>
<body>

<!-- אנימציית טעינה ראשונית -->
 <!-- שכבת טעינה על כל המסך (מופיעה מיד כשהעמוד נטען) -->
<div class="initial-loader" id="initialLoader">
    <!-- ספינר מסתובב – אנימציית טעינה -->
    <div class="main-spinner"></div>
    <!-- טקסט מרכזי -->
    <div class="loader-text">🗓️ מכין את Google Calendar</div>
    <!-- טקסט משני -->
    <div class="loader-subtext">טוען נתונים ומתחבר למערכת...</div>
</div>

<!-- תוכן עיקרי -->
 <!-- גוף הדף – מוסתר בהתחלה כדי להציג אותו רק אחרי שהטעינה הסתיימה -->
<div class="main-content" id="mainContent">

<h1>🗓️ סנכרון עם Google Calendar</h1>

<!-- אזור הצגת סטטוס וסרגל התקדמות -->
<div id="status">
    <div class="info">
        <!-- טקסט סטטוס מתחלף -->
        <span id="status-text">מתחיל סנכרון...</span>
        <!-- ספינר קטן ליד הטקסט -->
        <span class="loading" id="loading-spinner"></span>
    </div>
    <!-- פס התקדמות מלא -->
    <div class="progress-bar">
        <!-- החלק שמתמלא ב־ JS (בשלב סנכרון) – הרוחב שלו משתנה לפי התקדמות בפועל -->
        <div class="progress-fill" id="progress"></div>
    </div>
</div>

<!-- אזור להצגת תוצאות -->
<div id="results"></div>
<!-- 
        מיכל להצגת פלטים לאחר הסנכרון:

        הודעות הצלחה

        אירועים שנוספו ללוח השנה

        שגיאות, אם יש -->

</div> <!-- סגירת main-content -->

<!-- כפתורי ניווט בתחתית העמוד -->
<div style='text-align: center; margin-top: 30px;'>
    <a href='calendar.php' class="btn btn-primary">חזור לעמוד הקודם</a>
    <a href='https://calendar.google.com' target='_blank' class="btn btn-success">פתח את Google Calendar</a>
</div>

<script>
// משתנים גלובליים לניהול ההתקדמות

// אחוז ההתקדמות הכללי של הסנכרון
// משתנה זה יעדכן את הרוחב של סרגל ההתקדמות
let progress = 0; 

// מספר כולל של האירועים שצריך לסנכרן מתוך מסד הנתונים
let totalEvents = 0;

// כמה אירועים כבר טופלו בפועל (נשלחו, נוספו או נבדקו)
let processedEvents = 0;

// הסתרת מסך הטעינה הראשוני והצגת התוכן העיקרי אחרי 0.5 שניות
// לעבור אוטומטית ממסך טעינה לתוכן הראשי לאחר המתנה קצרה
setTimeout(function() {
    document.getElementById('initialLoader').style.display = 'none';
    document.getElementById('mainContent').style.display = 'block';
}, 500);


//  * עדכון ויזואלי של טקסט הסטטוס וסרגל ההתקדמות
//  *  text - הטקסט להצגה
//  *  percentage - אחוז ההתקדמות

function updateProgress(text, percentage) {
    // עדכון הטקסט
    document.getElementById('status-text').innerHTML = text;
    // עדכון רוחב הסרגל
    document.getElementById('progress').style.width = percentage + '%';
}


//  * הוספת תוצאה חדשה לאזור התוצאות
//  * html - תוכן HTML להצגה
//  * type - סוג ההודעה (info/success/error/warning)
//  *   - 'info' (ברירת מחדל) - כחול, מידע כללי
//  *   - 'success' - ירוק, פעולה הצליחה
//  *   - 'error' - אדום, שגיאה
//  *   - 'warning' - צהוב, אזהרה

function addResult(html, type = 'info') {
    // יצירת אלמנט חדש
    const div = document.createElement('div');
    // הגדרת העיצוב לפי הסוג
    div.className = type;
    // הכנסת התוכן
    div.innerHTML = html;
    // הוספה לתחתית אזור התוצאות
    document.getElementById('results').appendChild(div);
}

//  הסתרת ספינר הקטן ליד טקסט הסטטוס
function hideLoading() {
    document.getElementById('loading-spinner').style.display = 'none';
}
</script>

<?php
// אם האלמנטים ש־ JS מנסה לעדכן לא קיימים עדיין ב־ HTML, הדפדפן יזרוק שגיאה.
// לכן ה php -כאן ממוקם אחרי ה JS 
// אני לא מחכה שכל הדף  והקוד יטען, אני מעדכן תוך כדי ריצה את המשתמש בזמן אמת


// תהליך הסנכרון
try {
    // חיבור למסד הנתונים
    $pdo = new PDO("mysql:host=localhost;dbname=itayrm_dogs_boarding_house;charset=utf8", "itayrm_ItayRam", "itay0547862155");
    // הגדרת מצב שגיאות - זרוק חריגות במקום להחזיר false
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // הודעת הצלחה על החיבור
    // כדי להציג בזמן אמת מה קורה בתהליך
    // מדפיס סקריפט ללקוח שמציג הודעה בדף בזמן אמת
    echo "<script>addResult('✅ חיבור למסד הנתונים הצליח', 'success');</script>";

    // מאפשר למשתמש לראות בזמן אמת את שלבי הסנכרון
    // מדפיס סקריפט שמריץ ומעדכן את סרגל ההתקדמות באחוזים ובטקסט
    echo "<script>updateProgress('מתחבר למסד נתונים...', 10);</script>";

    // הפונקציה שולחת את הפלט מיידית לדפדפן (גם אם הדף עדיין לא הסתיים)
    flush(); // שליחה מיידית לדפדפן


    // בדיקות אבטחה ווידוא נתונים
    // בדיקת קוד משתמש ייחודי
    if (!isset($_SESSION['user_code'])) {
        throw new Exception("לא נמצא קוד משתמש. אנא התחבר מחדש למערכת.");
    }

    // בדיקת שם משתמש
    if (!isset($_SESSION['username'])) {
        throw new Exception("לא נמצא שם משתמש. אנא התחבר מחדש למערכת.");
    }

    // בדיקת שם הכלב הפעיל
    if (!isset($_SESSION['active_dog_name'])) {
        throw new Exception("לא נמצא שם הכלב הפעיל. אנא התחבר מחדש למערכת.");
    }

    // שליפת נתוני המשתמש מה-  session
    $username = $_SESSION['username'];
    $dog_name = $_SESSION['active_dog_name'];
    $user_code = $_SESSION['user_code'];
    
    // מונים לסטטיסטיקה
    $events_created = 0; // כמה אירועים נוצרו
    $events_skipped = 0; // כמה דולגו (כבר קיימים)
    $events_deleted = 0; // כמה נמחקו

    // מזהה היומן
    $calendar_id = 'primary'; // היומן הראשי של המשתמש

    // שלב 1: מחיקת אירועים שכבר לא רלוונטיים
    //  1. תורי טיפוח לא תפוסים
    //  2. תורים עם הפניה להזמנה לא קיימת
    //  3. הזמנות פנסיון שנמחקו מהמערכת

    echo "<script>updateProgress('מחפש אירועים למחיקה...', 5);</script>";
    echo "<script>addResult('<h3>🗑️ מחיקת אירועים שבוטלו או לא רלוונטיים...</h3>');</script>";
    flush();

    // מחיקת תורי טיפוח שלא תפוסים (isTaken = 0)
    $stmt = $pdo->prepare("SELECT * FROM grooming_appointments WHERE user_code = ? AND isTaken = 0");
    $stmt->execute([$user_code]);
    // מציאת תורי הטיפוח למחיקה כפי שהם שמורים בשרת שלנו
    $grooming_to_delete = $stmt->fetchAll();

    // לולאה על כל התורים למחיקה
    foreach ($grooming_to_delete as $grooming) {
        //  מייצר מחרוזת תאריך ושעה וכותרת לאירוע – לצורך חיפוש תואם ביומן של גוגל
        $event_datetime = $grooming['day'] . ' ' . $grooming['time'];
        $event_title = "🐕 טיפוח: " . $grooming['grooming_type'];
        
        // חיפוש האירוע ביומן של גוגל קלנדר
        // מכיל את כל האירועים שנמצאו בגוגל קלנדר (והמזהים שלהם) שזהים לאירוע ששמור בשרת שלנו
        $events = findEventsByTitleAndDate($event_title, $event_datetime, $calendar_id);
        
        // מחיקת כל האירועים שנמצאו בגוגל קלנדר לפי המזהה של האירוע (מזהה פנימי של גוגל, לא שלנו) כפי שהוא רשום בגוגל קלנדר
        foreach ($events as $event) {
            // הפונקציה deleteEvent מכינה את הכתובת ליצירת הבקשה לממשק של גוגל עם המזהה של האירוע למחיקה
            // אחר כך היא שולחת לפונקציה : makeCalendarRequest($url, 'DELETE');
            // הפונקציה makeCalendarRequest לוקחת את הפרמטרים שהיא קיבלה ומכינה מהם בקשה לממשק של גוגל
            // הפונקציה makeCalendarRequest שולחת את הבקשה שהיא יצרה לממשק של גוגל
            // הממשק של גוגל מוחק את האירוע מהיומן של google Calendar לפי המזהה של האירוע שנשלח אליו
            if (deleteEvent($event['id'], $calendar_id)) {
                if (deleteEvent($event['id'], $calendar_id)) {
                    $events_deleted++;
                    echo "<script>addResult('🗑️ נמחק תור טיפוח לא תפוס: {$grooming['grooming_type']} ב-{$grooming['day']} {$grooming['time']}', 'warning');</script>";
                    flush();
                }
            }
        }
    }

    // מחיקת תורי טיפוח עם הפניה להזמנת פנסיון שלא קיימת
    $stmt = $pdo->prepare("
        SELECT ga.* 
        FROM grooming_appointments ga 
        LEFT JOIN reservation r ON ga.connected_reservation_id = r.id 
        WHERE ga.user_code = ? 
        AND ga.connected_reservation_id IS NOT NULL 
        AND (r.id IS NULL OR r.status = 'deleted')
    ");
    $stmt->execute([$user_code]);
    // מציאת תורי הטיפוח למחיקה כפי שהם שמורים בשרת שלנו
    $grooming_invalid_reservation = $stmt->fetchAll();

    // לולאה על כל התורים למחיקה
    foreach ($grooming_invalid_reservation as $grooming) {
        $event_datetime = $grooming['day'] . ' ' . $grooming['time'];
        $event_title = "🐕 טיפוח: " . $grooming['grooming_type'];
        
        // חיפוש האירוע ביומן של גוגל קלנדר
        // מכיל את כל האירועים שנמצאו בגוגל קלנדר (והמזהים שלהם) שזהים לאירוע ששמור בשרת שלנו
        $events = findEventsByTitleAndDate($event_title, $event_datetime, $calendar_id);
        
        // מחיקת כל האירועים שנמצאו בגוגל קלנדר לפי המזהה של האירוע (מזהה פנימי של גוגל, לא שלנו) כפי שהוא רשום בגוגל קלנדר
        foreach ($events as $event) {
            // הפונקציה deleteEvent מכינה את הכתובת ליצירת הבקשה לממשק של גוגל עם המזהה של האירוע למחיקה
            // אחר כך היא שולחת לפונקציה : makeCalendarRequest($url, 'DELETE');
            // הפונקציה makeCalendarRequest לוקחת את הפרמטרים שהיא קיבלה ומכינה מהם בקשה לממשק של גוגל
            // הפונקציה makeCalendarRequest שולחת את הבקשה שהיא יצרה לממשק של גוגל
            // הממשק של גוגל מוחק את האירוע מהיומן של google Calendar לפי המזהה של האירוע שנשלח אליו
            if (deleteEvent($event['id'], $calendar_id)) {
                $events_deleted++;
                echo "<script>addResult('🗑️ נמחק תור טיפוח עם הזמנת פנסיון לא תקפה: {$grooming['grooming_type']} ב-{$grooming['day']} {$grooming['time']}', 'warning');</script>";
                flush();
            }
        }
    }

    // מחיקת הזמנות פנסיון שלא קיימות יותר במערכת
    //   1. שליפת כל ההזמנות התקפות מהמסד
    //   2. חיפוש כל אירועי הפנסיון ב-Google Calendar
    //   3. השוואה ומחיקת אירועים שאין להם הזמנה תואמת

    //    אין קשר ישיר בין ID במסד ל-ID ב-Google
    //    צריך לחלץ את מספר ההזמנה מתוך תיאור האירוע בגוגל קלנדר


    // שלב א': שליפת כל הזמנות הפנסיון הפעילות של המשתמש במסד שלנו
    $stmt = $pdo->prepare("SELECT id, start_date, end_date FROM reservation WHERE user_code = ? AND status != 'deleted'");
    $stmt->execute([$user_code]);
    $valid_reservations = $stmt->fetchAll();
    // חילוץ רשימת המזהים של ההזמנות מהמסד
    $valid_ids = array_column($valid_reservations, 'id'); // מערך של כל המזהים של הזמנות הפנסיון הפעילות במסד

    // שלב ב': חיפוש כל אירועי הפנסיון ביומן של גוגל קלנדר (3 שנים - שנה אחורה ושנתיים קדימה)
    $current_year = date('Y'); // השנה הנוכחית
    $start_search = ($current_year - 1) . '-01-01'; // הראשון בינואר שנה שעברה
    $end_search = ($current_year + 1) . '-12-31';   // 31 בדצמבר בעוד שנתיים


    //  ביצוע החיפוש ב-Google Calendar
    //  הפונקציה תחזיר את כל האירועים עם הכותרת "🏠 שהייה בפנסיון"
    //  בטווח התאריכים שהגדרנו - 3 שנים
    $boarding_events = findBoardingEventsByDateRange($start_search, $end_search, $calendar_id);

    // שלב ג': בדיקה ומחיקה של אירועים לא תקפים
    // לולאה על כל אירועי הפנסיון שנמצאו ב- Google Calendar
    foreach ($boarding_events as $event) {
        // חילוץ מספר ההזמנה מתוך תיאור האירוע
        // התוצאה נשמרת ב- $matches[1]
        if (preg_match('/הזמנת שהייה מספר: (\d+)/', $event['description'], $matches)) {
            // המרה למספר שלם
            $event_reservation_id = intval($matches[1]);
            
            // אם המזהה שחולץ מהתיאור לא נמצא ברשימת ההזמנות התקפות, מוחקים את האירוע מגוגל קלנדר
            if (!in_array($event_reservation_id, $valid_ids)) {

                // הפונקציה deleteEvent מכינה את הכתובת ליצירת הבקשה לממשק של גוגל עם המזהה של האירוע למחיקה
                // אחר כך היא שולחת לפונקציה : makeCalendarRequest($url, 'DELETE');
                // הפונקציה makeCalendarRequest לוקחת את הפרמטרים שהיא קיבלה ומכינה מהם בקשה לממשק של גוגל
                 // הפונקציה makeCalendarRequest שולחת את הבקשה שהיא יצרה לממשק של גוגל
                // הממשק של גוגל מוחק את האירוע מהיומן של google Calendar לפי המזהה של האירוע שנשלח אליו
                if (deleteEvent($event['id'], $calendar_id)) {
                    $events_deleted++;
                    // הודעה למשתמש עם פרטי ההזמנה שנמחקה
                    echo "<script>addResult('🗑️ נמחקה הזמנת פנסיון לא תקפה (ID: {$event_reservation_id})', 'warning');</script>";
                    flush(); // עדכון מיידי של הדפדפן
                }
            }
        }
    }

    // עדכון סטטוס

    //    1. סופרים כמה אירועים צריך ליצור
    //    2. מעדכנים את סרגל ההתקדמות
    echo "<script>updateProgress('בודק הזמנות קיימות...', 20);</script>";
    echo "<script>addResult('🔍 מתחיל לבדוק הזמנות עבור המשתמש: $username', 'info');</script>";

    flush(); // שליחה מיידית לדפדפן כדי שהמשתמש יראה שמשהו קורה

    // ספירת סה"כ אירועים לעיבוד
    // ספירת תורי טיפוח תקפים
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM grooming_appointments ga 
        LEFT JOIN reservation r ON ga.connected_reservation_id = r.id 
        WHERE ga.user_code = ? 
        AND ga.isTaken = 1 
        AND ga.connected_reservation_id IS NOT NULL
        AND r.id IS NOT NULL
        AND r.status != 'deleted'
    ");
    $stmt->execute([$user_code]);
    $grooming_total = $stmt->fetchColumn(); // מחזיר את התוצאה היחידה - הספירה של כמות תורי הטיפוח התקפים

    // ספירת הזמנות פנסיון
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservation WHERE user_code = ? AND status != 'deleted'");
    $stmt->execute([$user_code]);
    $reservation_total = $stmt->fetchColumn();

    $total_events = $grooming_total + $reservation_total; // חישוב הסכום הכולל
    
    // הצגת סטטיסטיקה
    // מציגים:
    //  1. כמה תורי טיפוח נמצאו
    //  2. כמה הזמנות פנסיון נמצאו
    //  3. מעדכנים את המשתנה הגלובלי totalEvents ב-JavaScript
    //  4. מתחילים את השלב הבא בסרגל ההתקדמות (30%)
    echo "<script>
        totalEvents = $total_events;
        addResult('📊 נמצאו $grooming_total תורי טיפוח ו-$reservation_total הזמנות פנסיון', 'info');
        updateProgress('מעבד תורי טיפוח...', 30);
    </script>";
    flush(); // שליחה מיידית לדפדפן כדי שהמשתמש יראה שמשהו קורה


    // שלב 2: יצירת תורי טיפוח ביומן
    //  1. שליפת כל תורי הטיפוח התקפים
    //  2. בדיקת כפילויות לכל תור
    //  3. יצירת אירוע ב- Google Calendar
    //  4. עדכון התקדמות בזמן אמת
    echo "<script>addResult('<h3>🐕 מעביר תורי טיפוח...</h3>');</script>";
    
    // שליפת תורי טיפוח תקפים בלבד
    $stmt = $pdo->prepare("
        SELECT ga.* 
        FROM grooming_appointments ga 
        LEFT JOIN reservation r ON ga.connected_reservation_id = r.id 
        WHERE ga.user_code = ? 
        AND ga.isTaken = 1 
        AND ga.connected_reservation_id IS NOT NULL
        AND r.id IS NOT NULL
        AND r.status != 'deleted'
        ORDER BY ga.day, ga.time
    ");
    $stmt->execute([$user_code]);
    

    // עיבוד כל תור טיפוח
    //  fetch() - מחזיר שורה אחת בכל פעם (חוסך זיכרון)
    //  הלולאה תמשיך עד שאין יותר שורות (fetch מחזיר false)
    while ($row = $stmt->fetch()) {

        // עדכון מונה האירועים שעובדו
        $processedEvents++;
        // חישוב אחוז ההתקדמות
        // הסבר:
        //   מתחילים מ-30% (סיימנו את שלבי ההכנה)
        //   מוסיפים עד 40% נוספים (30% עד 70%)
        //   היחס מחושב לפי כמה אירועים עיבדנו מתוך הסה"כ
        //  
        //  דוגמה:
        //  עיבדנו 5 מתוך 10 אירועים = 50%
        //  30 + (0.5 * 40) = 30 + 20 = 50%
        $progress_percent = 30 + (($processedEvents / $total_events) * 40); // חישוב אחוז התקדמות
        
        try {
            // הכנת נתוני האירוע
            // שרשור תאריך ושעה למחרוזת אחת
            $event_datetime = $row['day'] . ' ' . $row['time'];
            $start_datetime = fixTimezone($event_datetime); // תיקון לזמן ישראל
            // חישוב זמן הסיום
            $end_datetime = fixTimezone($event_datetime . ' +30 minutes'); // משך של 30 דקות
            
            // כותרת האירוע עם אמוג'י וסוג הטיפוח
            $event_title = "🐕 טיפוח: " . $row['grooming_type'];
            
            // בדיקה אם אירוע דומה כבר קיים
            // הבדיקה:
            //  - מחפשת אירוע עם אותה כותרת
            //  - באותו יום
            if (eventExists($event_title, $event_datetime)) {
                $events_skipped++; // עדכון מונה הדילוגים

                // הודעה מפורטת למשתמש
                echo "<script>
                    addResult('⏭️ דילוג על האירוע: {$row['grooming_type']} ב-{$row['day']} {$row['time']}, מכיוון שנקבע כבר תור עם סוג טיפוח זהה לאותו היום', 'warning');
                    updateProgress('מעבד תור טיפוח ($processedEvents/$total_events)...', $progress_percent);
                </script>";
                flush(); // שליחה מיידית לדפדפן כדי שהמשתמש יראה שמשהו קורה

                continue; // דילוג לאיטרציה הבאה בלולאה
            }

            // יצירת נתונים לאירוע חדש

             /*
             * מבנה נתוני האירוע לפי Google Calendar API v3
             * https://developers.google.com/calendar/api/v3/reference/events
             * --------------------------------------------
             * 
             * שדות חובה:
             * - summary: כותרת האירוע
             * - start: זמן התחלה
             * - end: זמן סיום
             * 
             * שדות אופציונליים:
             * - description: תיאור מפורט
             *    כולל:
                - מספר אישור
                - שם המשתמש
                - שם הכלב
             */



            $event_data = [
                'summary' => $event_title, // כותרת האירוע
                'description' => "📋 אישור: {$row['confirmation']}\n👤 שם משתמש: {$username}\n🐕 שם הכלב: {$dog_name}", // תיאור מפורט
                // זמן התחלה
                'start' => [
                    'dateTime' => $start_datetime, //  זמן מדויק בפורמט ISO 8601
                    'timeZone' => 'Asia/Jerusalem' // חובה לציין timezone
                ],
                'end' => [
                    'dateTime' => $end_datetime,
                    'timeZone' => 'Asia/Jerusalem' // חובה לציין timezone
                ]
            ];

            // שליחת בקשה ליצירת האירוע בגוגל קלנדר
            $url = "https://www.googleapis.com/calendar/v3/calendars/{$calendar_id}/events";
            $result = makeCalendarRequest($url, 'POST', $event_data);

            //  $result מכיל את פרטי האירוע שנוצר:
            //   - id: מזהה ייחודי
            //   - htmlLink: קישור לאירוע
            //   - created: זמן היצירה
            //   - updated: זמן העדכון
            
            $events_created++;  // עדכון מונה אירועים חדשים

            // הודעת הצלחה למשתמש
            echo "<script>
                addResult('✅ נוצר אירוע טיפוח: {$row['grooming_type']} ב-{$row['day']} {$row['time']}', 'success');
                updateProgress('מעבד תור טיפוח ($processedEvents/$total_events)...', $progress_percent);
            </script>";
            flush(); // שליחה מיידית לדפדפן כדי שהמשתמש יראה שמשהו קורה

            
            // טיפול בשגיאות ביצירת אירוע
        } catch (Exception $e) {
            echo "<script>
                addResult('❌ שגיאה ביצירת אירוע טיפוח: " . addslashes($e->getMessage()) . "', 'error');
                updateProgress('מעבד תור טיפוח ($processedEvents/$total_events)...', $progress_percent);
            </script>";
            flush(); // שליחה מיידית לדפדפן כדי שהמשתמש יראה שמשהו קורה

            // ממשיכים לאירוע הבא גם אם היה כישלון
        }
        
        // השהיה קצרה למניעת עומס על ה- API
        // Google מגבילה את קצב הבקשות
        usleep(200000); // 0.2 שניות
    }

    // שלב 3: יצירת הזמנות פנסיון ביומן

    //  1. שליפת כל הזמנות השהייה הפעילות
    //  2. בדיקת כפילויות לכל הזמנה
    //  3. יצירת אירוע ב- Google Calendar
    //  4. עדכון התקדמות בזמן אמת
    //  5. אירועי יום שלם 

    echo "<script>
        addResult('<h3>🏠 מעביר הזמנות פנסיון...</h3>');
        updateProgress('מעבד הזמנות פנסיון...', 70);
    </script>";
    flush();
    
    //  שליפת כל הזמנות הפנסיון הפעילות - עם מיון לפי תאריך התחלה
    $stmt = $pdo->prepare("SELECT * FROM reservation WHERE user_code = ? AND status != 'deleted' ORDER BY start_date");
    $stmt->execute([$user_code]);
    
    // עיבוד כל הזמנת פנסיון
    while ($row = $stmt->fetch()) {
        // עדכון מונה האירועים שעובדו
        $processedEvents++;

        // חישוב אחוז ההתקדמות
        // הסבר:
        //   מתחילים מ-70% (סיימנו את שלבי ההכנה + אירועי הטיפוח)
        //   מוסיפים עד 25% נוספים (70% עד 95%)
        //   היחס מחושב לפי כמה אירועים עיבדנו מתוך הסה"כ
        // * משאירים 5% לסיכום
        //  
        //  דוגמה:
        //  עיבדנו 7 מתוך 10 אירועים = 70%
        //  70 + (0.7 * 25) = 70 + 17.5 = 87.5%
        $progress_percent = 70 + (($processedEvents / $total_events) * 25);
        
        // הכנת תאריכים לאירוע יום שלם
        try {
            // הכנת תאריכים - הוספת יום לתאריך הסיום כי גוגל מחשב עד התאריך (לא כולל)

        /*
         דוגמה:
         - במסד: 1/1/2025 עד 3/1/2025 (3 לילות)
         - ב-Google: start: 1/1/2025, end: 4/1/2025
          בלי ההוספה של יום, ההזמנה תיראה קצרה ביום
         */
            $start_date = date('Y-m-d', strtotime($row['start_date']));
            $end_date = date('Y-m-d', strtotime($row['end_date'] . ' +1 day'));
            
            $event_title = "🏠 שהייה בפנסיון";
            
            // בדיקה אם ההזמנה כבר קיימת
            //  - בודקים לפי תאריך ההתחלה
            //  - אם יש כבר אירוע פנסיון שמתחיל באותו יום - מדלגים
            if (eventExists($event_title, $row['start_date'])) {
                $events_skipped++;
                echo "<script>
                    addResult('⏭️ דילוג על רישום הזמנת פנסיון שכבר קיימת בתאריכים:  {$start_date} עד " . date('Y-m-d', strtotime($row['end_date'])) . "', 'warning');
                    updateProgress('מעבד הזמנת פנסיון ($processedEvents/$total_events)...', $progress_percent);
                </script>";
                flush();
                continue;
            }

            // יצירת נתונים לאירוע יום שלם
            /*
             * מבנה נתוני האירוע לפי Google Calendar API v3
             * https://developers.google.com/calendar/api/v3/reference/events
             * --------------------------------------------
             * 
            //  ההבדל העיקרי מתור טיפוח:
            //  * - משתמשים ב-'date' במקום 'dateTime'
            //  * - אין צורך ב- timeZone (יום שלם הוא אוניברסלי)
            //  * - האירוע יופיע בראש היום ביומן

            */

            $event_data = [
                'summary' => $event_title,
              /*
              תיאור עם מספר ההזמנה:
               - מספר ההזמנה משמש אותנו בתהליך המחיקה
              */
                'description' => "📋 הזמנת שהייה מספר: {$row['id']}\n👤 שם משתמש: {$username}\n🐕 שם הכלב: {$dog_name}",
                'start' => ['date' => $start_date], // אירוע יום שלם - רק תאריך
                'end' => ['date' => $end_date] // פורמט: YYYY-MM-DD (ללא שעה)
            ];

            // שליחת בקשה ליצירת האירוע
            $url = "https://www.googleapis.com/calendar/v3/calendars/{$calendar_id}/events";
            $result = makeCalendarRequest($url, 'POST', $event_data);
            
            $events_created++;
            
            // הודעת הצלחה עם התאריכים המקוריים
            echo "<script>
                addResult('✅ נוצרה הזמנת פנסיון: {$start_date} עד " . date('Y-m-d', strtotime($row['end_date'])) . "', 'success');
                updateProgress('מעבד הזמנת פנסיון ($processedEvents/$total_events)...', $progress_percent);
            </script>";
            flush();
            
        } catch (Exception $e) {
            echo "<script>
                addResult('❌ שגיאה ביצירת הזמנת פנסיון: " . addslashes($e->getMessage()) . "', 'error');
                updateProgress('מעבד הזמנת פנסיון ($processedEvents/$total_events)...', $progress_percent);
            </script>";
            flush();
        }
        
        usleep(200000); // 0.2 שניות
    }

    // סיכום התהליך
    // השלמת סרגל ההתקדמות ל-100%
    // הסתרת הספינר
    // הודעת סיכום מפורטת
    echo "<script>
        updateProgress('הסנכרון הושלם!', 100);
        hideLoading();
        addResult('<h2>🎉 הסנכרון הושלם בהצלחה!</h2><p>✅ נוצרו: <strong>$events_created</strong> אירועים חדשים</p><p>⏭️ דולגו: <strong>$events_skipped</strong> אירועים קיימים</p><p>🗑️ נמחקו: <strong>$events_deleted</strong> אירועים לא רלוונטיים</p><p>📱 אתה יכול עכשיו לראות את כל ההזמנות ב-Google Calendar!</p>', 'success');
    </script>";
    flush();


    // טיפול בשגיאות כלליות
    //  תופס כל חריגה שלא טופלה בתוך הלולאות
    //  שגיאות אפשריות:
    //   - בעיית חיבור למסד נתונים
    //   - טוקן לא תקין
    //   - בעיית רשת כללית
    //   - שגיאת תכנות
} catch (Exception $e) {
    // טיפול בשגיאות
    // הסתרת הספינר
    echo "<script>
        hideLoading();
        addResult('<h2>❌ שגיאה</h2><p>" . addslashes($e->getMessage()) . "</p>', 'error');
    </script>";
    
    // אם הבעיה היא טוקן שפג תוקף, הצעת התחברות מחדש
    if (strpos($e->getMessage(), 'טוקן הגישה פג תוקף') !== false) {
        echo "<script>addResult('<p><a href=\"authorize.php\" class=\"btn btn-primary\">לחץ כאן להתחברות מחדש</a></p>');</script>";
    }
    flush();
}
?>

</body>
</html>