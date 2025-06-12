<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../../registration/login.html");
    exit;
}

// שינוי אזור הזמן לישראל
date_default_timezone_set('Asia/Jerusalem');

// הגדרת משתני המשתמש
$first_name = htmlspecialchars($_SESSION['first_name'] ?? "אורח");
$user_type = $_SESSION['user_type'] ?? 0;
$username = $_SESSION['username'] ?? "";
$user_code = $_SESSION['user_code'] ?? "";

// בדיקה אם יש למשתמש כלבים רשומים (רק למשתמשים רגילים)
if ($user_type == 0) {
    // חיבור למסד הנתונים
    $servername = "localhost";
    $username_db = "itayrm_ItayRam";
    $password_db = "itay0547862155";
    $dbname = "itayrm_dogs_boarding_house";
    
    $conn = new mysqli($servername, $username_db, $password_db, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // הגדרה לעברית
    $conn->set_charset("utf8mb4");
    
    // בדיקה אם יש כלבים רשומים למשתמש
    $check_dogs_query = "SELECT COUNT(*) as dog_count FROM dogs WHERE user_code = ?";
    $stmt = $conn->prepare($check_dogs_query);
    $stmt->bind_param("s", $user_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // אם אין כלבים רשומים, העבר לעמוד רישום כלב
    if ($row['dog_count'] == 0) {
        // בדיקה אם הדף הנוכחי אינו כבר עמוד הרישום כלבים
        $current_page = basename($_SERVER['PHP_SELF']);
        if ($current_page != "dog_registration_without_dogs.php" && $current_page != "dog_registration.php") {
            header("Location: ../../dog_registration/user/dog_registration_without_dogs.php");
            exit;
        }
    } else {
        // בדיקה אם יש כלב פעיל בסשן
        if (!isset($_SESSION['active_dog_id'])) {
            // בדיקה אם הדף הנוכחי אינו כבר עמוד בחירת כלב
            $current_page = basename($_SERVER['PHP_SELF']);
            if ($current_page != "select_active_dog.php") {
                header("Location: ../../dog_registration/user/select_active_dog.php");
                exit;
            }
        } else {
            // קבלת פרטי הכלב הפעיל כולל התמונה
            $active_dog_id = $_SESSION['active_dog_id'];
            $get_dog_query = "SELECT dog_name, image_url FROM dogs WHERE dog_id = ? AND user_code = ?";
            $stmt = $conn->prepare($get_dog_query);
            $stmt->bind_param("is", $active_dog_id, $user_code);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $active_dog_name = htmlspecialchars($row['dog_name']);
                $active_dog_image = '../../dog_registration/user/'. $row['image_url']; // נתיב לתמונת הכלב
            } else {
                // אם הכלב לא נמצא, הסר אותו מהסשן ונתב מחדש
                unset($_SESSION['active_dog_id']);
                unset($_SESSION['active_dog_name']);
                header("Location: ../../dog_registration/user/select_active_dog.php");
                exit;
            }
        }
    }
    
    $stmt->close();
    $conn->close();
}

// הגדרת תפריטים לפי סוג משתמש
// תנאי קצר (ternary operator)
$links = $user_type == 1
    ? [
        '../../registration/admin/admin_dashboard_secured.php' => 'דשבורד מנהל',
        '../../grooming_panel/admin/groomingPanel.php' => 'הזמנות טיפוח',
        '../../grooming/admin/update_grooming_prices.php' => 'עדכון מחירי שירותי טיפוח',
        '../../services/admin/updateServicePrice.php' => 'עדכון מחירי שירותים',
        '../../registration/admin/clientOrders.php' => 'הזמנות הלינה',
        '../../inventory_management/admin/inventory_management.php' => 'ניהול מלאי',
         '../../feedback/admin/feedback_summary.php' => 'סיכום משובים',
    ]
    : [
        '../../registration/user/user_dashboard_secured.php' => 'האזור האישי (פעולות נוספות)',
        '../../googleCalendar/user/calendar.php' => 'google Calendar',
        '../../reservation/user/reservation.php' => ' בצע הזמנת פנסיון חדשה',
        '../../registration/user/my_orders.php' => 'הזמנות הפנסיון שלי',
        // '../../registration/user/update_User_profile.php' => 'עדכון פרטים',
        '../../grooming/user/treatments.php' => 'בצע הזמנת טיפוח חדשה',
        '../../grooming_panel/user/groomingPanelUser.php' => 'הזמנות הטיפוח שלי',
        // '../../dog_registration/user/dog_registration.php' => 'רישום כלב חדש',
        // '../../update_dog_profile/user/update_active_dog_profile.php' => 'עדכון פרטי כלב פעיל',
        // '../../dog_registration/user/select_active_dog.php' => 'החלפת כלב פעיל',
    ];

// קביעת צבעים לפי סוג משתמש
$headerBgColor = $user_type == 1 ? '#1a365d' : '#2c3e50'; // כחול כהה למנהל, כחול רגיל למשתמש
$headerAccentColor = $user_type == 1 ? '#e53e3e' : '#3182ce'; // אדום למנהל, כחול בהיר למשתמש

// חישוב גובה הכותרת (הערכה)
$headerHeight = 140; // גובה ממוצע בפיקסלים     
?>

<!-- 
    הסגנון מוגדר עם  !important בכל מקום כדי להבטיח  
    שלא יידרס על ידי סגנונות אחרים באתר
-->
<style>
    /* איפוס סגנונות לאלמנטים בתוך הכותרת בלבד */
    .doggy-header-container *,
    .doggy-header-container *::before,
    .doggy-header-container *::after {
        box-sizing: border-box !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* יצירת מרווח בגוף הדף בגובה של הכותרת */
    body {
        padding-top: <?= $headerHeight ?>px !important;
        margin-top: 0 !important;
    }
    
    /*  הכותרת הראשית - הכותרת נשארת במקום גם בגלילה*/
    .doggy-header-container {
        position: fixed !important; /* קיבוע הכותרת בחלק העליון */
        top: 0 !important; /* מיקום בחלק העליון של המסך */
        left: 0 !important; /* מיקום מצד שמאל */
        width: 100% !important; /* רוחב מלא של המסך */
        background: <?= $headerBgColor ?> !important; /* צבע רקע דינמי לפי סוג משתמש */
        color: white !important; /* צבע טקסט לבן לניגודיות */
        font-family: 'Assistant', 'Rubik', Arial, sans-serif !important;
        font-size: 16px !important;
        z-index: 1000 !important;  /* להבטחת תצוגה מעל כל התוכן */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
        direction: rtl !important; /* כיוון עברית מימין לשמאל */
        display: flex !important;
        flex-direction: column !important; /* סידור אלמנטים בעמודה */
    }
    
    /* סרגל עליון עם לוגו ופרטי משתמש */
    /* מכיל את הלוגו, פרטי המשתמש וכפתור התנתקות
    רקע כהה יותר להבחנה מהתפריט */
    .doggy-header-top {
        display: flex !important;
        justify-content: space-between !important; /* פיזור אלמנטים בין הקצוות */
        align-items: flex-start !important; /* יישור לחלק העליון */
        padding: 10px 20px !important;
        background-color: rgba(0, 0, 0, 0.1) !important; /*רקע כהה יותר*/
        position: relative !important; /* למיקום מוחלט של תגית סוג המשתמש */
    }
    
    /* לוגו האתר */
    /* כולל אייקון כלב וטקסט
    קישור לאזור האישי של המשתמש */
    .doggy-header-logo {
        font-weight: bold !important; /* טקסט מודגש */
        font-size: 20px !important; /* גודל פונט בולט */
        color: white !important; /* צבע לבן */
        text-decoration: none !important; /* ביטול קו תחתון */
        display: flex !important; 
        align-items: center !important; /* יישור אנכי למרכז */
        margin-top: 5px !important; /* הוספת מרווח קטן מלמעלה ליישור טוב יותר */
    }
    
    /* אייקון הכלב בלוגו */
    .doggy-header-logo-icon {
        margin-left: 8px !important; /* מרווח מהטקסט */
        font-size: 24px !important;
    }
    
    /* מידע על המשתמש*/
    /* מכיל שם המשתמש, פרטי כלב פעיל והתנתקות */
    .doggy-header-user-info {
        display: flex !important;
        flex-direction: column !important; /* סידור בעמודה */
        align-items: flex-end !important; /* יישור לימין */
        gap: 8px !important; /* מרווח בין אלמנטים */
        padding-top: 15px !important; /* מרווח מלמעלה כדי לפנות מקום לתגית סוג המשתמש */
    }
    
    /* שורה עם השם הפרטי והתנתקות */
    /* מכילה את השם הפרטי, כלב פעיל וכפתור התנתקות */
    .doggy-header-user-controls {
        display: flex !important;
        align-items: center !important; /* יישור אנכי למרכז */
        gap: 15px !important; /* מרווח אחיד בין אלמנטים */
    }
    
    /* הודעת ברוכים הבאים */
    /* מציגה את השם הפרטי של המשתמש
    עיצוב בולט עם רקע לבן וטקסט כהה */
    .doggy-header-welcome {
        background: white !important; /* רקע לבן */
        color: <?= $headerBgColor ?> !important; /* טקסט בצבע הכותרת */
        padding: 6px 12px !important;
        border-radius: 5px !important; /* פינות מעוגלות */
        font-weight: bold !important; /* טקסט מודגש */
        display: flex !important;
        align-items: center !important; /* יישור אנכי למרכז */
    }
    
    /* אייקון היד בהודעת ברוכים הבאים */
    .doggy-header-welcome-icon {
        margin-left: 5px !important; /* מרווח מהטקסט */
    }
    
    /* סגנון לאזור הכלב הפעיל */
    /* מציג תמונה והשם של הכלב הפעיל
       רקע שקוף עם מסגרת */
    .doggy-header-active-dog {
        background: rgba(255, 255, 255, 0.15) !important; /* רקע שקוף */
        color: white !important; /* טקסט לבן */
        padding: 6px 12px !important; /* ריווח פנימי */
        border-radius: 5px !important; /* פינות מעוגלות */
        font-weight: bold !important; /* טקסט מודגש */
        display: flex !important;
        align-items: center !important; /* יישור אנכי למרכז */
        margin-right: 10px !important; /* מרווח מימין */
        gap: 8px !important; /* מרווח בין התמונה לטקסט */
    }
    
    /* תמונת הכלב הפעיל */
    .doggy-header-dog-image {
        width: 35px !important; /* רוחב קבוע */
        height: 35px !important; /* גובה קבוע */
        border-radius: 50% !important; /* עיגול התמונה */
        object-fit: cover !important; /* שמירה על פרופורציות התמונה */
        border: 2px solid rgba(255, 255, 255, 0.3) !important; /* מסגרת לבנה */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important; /* צל */
    }
    
    /* אייקון כלב כברירת מחדל */
    /* מוצג כאשר אין תמונה או שהיא לא נטענת */
    .doggy-header-dog-icon {
        margin-left: 5px !important; /* מרווח מהטקסט */
        font-size: 20px !important; /* גודל אייקון */
    }
    
    /* מיכל לטקסט הכלב */
    /* מכיל את הטקסט עם שם הכלב הפעיל */
    .doggy-header-dog-text {
        display: flex !important;
        align-items: center !important; /* יישור אנכי למרכז */
        gap: 5px !important; /* מרווח קטן */
    }
    
    /* כפתור התנתקות */
    .doggy-header-logout {
        color: white !important; /* טקסט לבן */
        text-decoration: none !important; /* ביטול קו תחתון */
        padding: 6px 12px !important; /* ריווח פנימי */
        border-radius: 5px !important; /* פינות מעוגלות */
        background-color: rgba(255, 255, 255, 0.1) !important; /* רקע שקוף */
        transition: background-color 0.3s ease !important; /* אפקט מעבר חלק */
        display: flex !important;
        align-items: center !important; /* יישור אנכי למרכז */
    }
    
    .doggy-header-logout:hover {
        background-color: rgba(255, 255, 255, 0.2) !important; /* רקע בהיר יותר */
    }
    
    /* אייקון דלת בכפתור התנתקות */
    .doggy-header-logout-icon {
        margin-right: 5px !important; /* מרווח מהטקסט */
    }
    
    /* תפריט הניווט */
    /* מכיל את כל הקישורים הראשיים של המערכת */
    .doggy-header-nav {
        padding: 10px 20px !important; /* ריווח פנימי */
        display: flex !important;
        justify-content: center !important; /* יישור למרכז */
    }
    
    /* מיכל קישורי הניווט */
    /* מסדר את הקישורים בשורות עם מעבר אוטומטי */
    .doggy-header-links {
        display: flex !important;
        gap: 5px !important; /* מרווח קטן בין קישורים */
        flex-wrap: wrap !important; /* מעבר לשורה חדשה כשצריך */
        justify-content: center !important; /* יישור למרכז */
        max-width: 1200px !important; /* רוחב מקסימלי */
    }
    
    /* כל קישור בתפריט הניווט */
    .doggy-header-link {
        color: white !important; /* טקסט לבן */
        text-decoration: none !important; /* ביטול קו תחתון */
        padding: 8px 15px !important; /* ריווח פנימי  */
        border-radius: 5px !important; /* פינות מעוגלות */
        transition: all 0.3s ease !important; /* אפקט מעבר חלק */
        white-space: nowrap !important; /* מניעת שבירת טקסט */
        font-weight: 500 !important;
    }
    
    .doggy-header-link:hover {
        background-color: <?= $headerAccentColor ?> !important; /* צבע רקע דינמי */
    }
    
    /* אינדיקציה לסוג משתמש */
    /* תגית קטנה המציינת אם המשתמש הוא מנהל או משתמש רגיל
    ממוקמת בפינה השמאלית העליונה */
    .doggy-header-user-type {
        position: absolute !important; /* מיקום מוחלט */
        top: 0 !important; /* צמוד לחלק העליון */
        left: 0 !important; /* צמוד לצד שמאל */
        background-color: <?= $headerAccentColor ?> !important; /* צבע דינמי */
        color: white !important; /* טקסט לבן */
        font-size: 11px !important; /* פונט קטן */
        padding: 2px 8px !important; /* ריווח פנימי קטן */
        border-bottom-right-radius: 5px !important; /* עיגול רק בפינה הימנית התחתונה */
    }
    
    /* תיבה לארגון הלוגו + הרמקול*/
    .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
    
    /* התאמה למובייל */
    @media (max-width: 768px) {

        /* הגדלת הריווח העליון למסכים קטנים */
        body {
            padding-top: 500px !important; /* הגדלת הריווח למסכים קטנים בגלל פריסה אנכית */
        }
        
        /* סרגל עליון במובייל */
        .doggy-header-top {
            flex-direction: column !important; /* סידור בעמודה במקום בשורה */
            align-items: stretch !important; /* מתיחה לרוחב מלא */
            gap: 10px !important; /* מרווח גדול יותר בין אלמנטים */
            padding: 15px 10px !important; /* ריווח מותאם למובייל */
        }
        
         /* מידע משתמש במובייל */
        .doggy-header-user-info {
            align-items: stretch !important; /* מתיחה לרוחב מלא */
            padding-top: 20px !important; /* מרווח גדול יותר מלמעלה */
        }
        
        .doggy-header-user-controls {
            justify-content: space-between !important;
            flex-wrap: wrap !important;
        }
        
        /* קישורי ניווט במובייל */
        .doggy-header-links {
            flex-direction: column !important; /* סידור בעמודה */
            width: 100% !important; /* רוחב מלא */
            gap: 5px !important; /* מרווח גדול יותר בין קישורים */
        }
        
        /* קישור יחיד במובייל */
        .doggy-header-link {
            text-align: center !important; /* יישור טקסט למרכז */
            padding: 10px !important;
        }
        
        /* כלב פעיל במובייל */
        .doggy-header-active-dog {
            margin-top: 5px !important; /* מרווח עליון */
            margin-right: 0 !important; /* ביטול מרווח ימני */
        }
        
        /* הקטנת התמונה במובייל */
        .doggy-header-dog-image {
            width: 30px !important;
            height: 30px !important;
        }
    }
    
    /* סקריפט JavaScript להתאמת padding-top בזמן ריענון הדף */
    .js-header-height-script {
        display: none !important; /* מחלקה מוסתרת לסקריפט */
    }
</style>

<div class="doggy-header-container">
    <!-- סרגל עליון -->
    <!-- מכיל לוגו, פרטי משתמש וכפתור התנתקות -->
    <div class="doggy-header-top">
        <!-- אינדיקציה לסוג משתמש  -->
        <div class="doggy-header-user-type">
            <?= $user_type == 1 ? 'מנהל' : 'משתמש' ?>
        </div>
        
         <!-- לוגו -->
         <div class="logo-section">
            <a href="<?= $user_type == 1 ? '../../registration/admin/admin_dashboard_secured.php' : '../../registration/user/user_dashboard_secured.php' ?>" class="doggy-header-logo">
                <span>פנסיון כלבים</span>
                <span class="doggy-header-logo-icon">🐕</span>
              
            </a>
            
            <!--תיבת נגינה-->
            <audio id="bgMusic" loop>
              <source src="../../sounds/loading-music.mp3" type="audio/mpeg">
              הדפדפן שלך אינו תומך בניגון מוזיקה.
            </audio>
            
            <button id="musicToggleBtn" onclick="toggleMusic()" style="margin-right: 20px; background: none; border: none; cursor: pointer;font-size: 32px;">
              🔊
            </button>
        </div>
        
        <!-- מידע משתמש וכפתור התנתקות -->
        <div class="doggy-header-user-info">
            <!-- שורת עם שם פרטי, כלב פעיל והתנתקות -->
            <div class="doggy-header-user-controls">
                <!-- הודעת ברוכים הבאים -->
                <div class="doggy-header-welcome">
                    <span class="doggy-header-welcome-icon">👋</span>
                    <span>שלום, <?= $first_name ?></span>
                </div>
                
                <!-- מידע על הכלב הפעיל -->
                <?php if ($user_type == 0 && isset($active_dog_name)): ?>
                <div class="doggy-header-active-dog">
                    <!-- בדיקה אם קיימת תמונה עבור הכלב הפעיל -->
                    <!-- הצגת תמונת הכלב אם קיימת, אחרת אייקון -->
                    <?php if (!empty($active_dog_image) && file_exists($active_dog_image)): ?>
                        <!-- הצגת תמונת הכלב האמיתית -->
                        <img src="<?= htmlspecialchars($active_dog_image) ?>" 
                             alt="תמונת <?= $active_dog_name ?>" 
                             class="doggy-header-dog-image"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                        <!-- כדי להסתיר את התמונה אם היא נכשלה בטעינה ולהציג במקום זאת את האלמנט הבא אחריה -->

                        <!-- אייקון גיבוי שיוצג רק אם התמונה לא נטענת (onerror JavaScript) -->
                        <span class="doggy-header-dog-icon" style="display: none;">🦮</span>


                    <?php else: ?>
                        <!-- אם אין תמונה או שהקובץ לא קיים - הצגת אייקון ברירת מחדל -->
                        <span class="doggy-header-dog-icon">🦮</span>
                    <?php endif; ?>
                    
                    <!-- מיכל לטקסט עם שם הכלב הפעיל -->
                    <div class="doggy-header-dog-text">
                        <span>שם הכלב הפעיל: <?= $active_dog_name ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- כפתור התנתקות -->
                <a href="../../registration/logout.php" class="doggy-header-logout">
                    <span>התנתקות</span>
                    <span class="doggy-header-logout-icon">🚪</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- סרגל ניווט -->
    <!-- מכיל את כל הקישורים הראשיים של המערכת -->
     <!-- הקישורים משתנים לפי סוג המשתמש (מנהל/משתמש רגיל) -->
    <nav class="doggy-header-nav">
        <div class="doggy-header-links">
            <?php foreach ($links as $href => $label): ?>
                <a href="<?= $href ?>" class="doggy-header-link"><?= $label ?></a>
            <?php endforeach; ?>
        </div>
    </nav>
</div>

<!-- סקריפט להתאמת גובה הכותרת בזמן אמת -->

<!--  מחשב את הגובה האמיתי של הכותרת ומתאים את הריווח של גוף הדף -->
<!-- מופעל בטעינת הדף ובכל שינוי גודל חלון  -->
<script class="js-header-height-script">

    /*
     - פונקציה להתאמת הריווח העליון של הגוף לפי גובה הכותרת
     - מחשבת את הגובה האמיתי ומתאימה בהתאם
     */
document.addEventListener('DOMContentLoaded', function() {
    function adjustPadding() {
        const headerHeight = document.querySelector('.doggy-header-container').offsetHeight;
        document.body.style.paddingTop = headerHeight + 'px';
    }
    
    // התאמה ראשונית בטעינת הדף
    adjustPadding();
    
    // התאמה בכל פעם שחלון הדפדפן משתנה
    window.addEventListener('resize', adjustPadding);
});

  //  טיפול בתיבת הנגינה
  let isPlaying = false;
  const audio = document.getElementById('bgMusic');
  const toggleBtn = document.getElementById('musicToggleBtn');

  function toggleMusic() {
    if (!audio) return;
    
    if (isPlaying) {
      audio.pause();
      toggleBtn.textContent = '🔇';
    } else {
      audio.play().catch(err => {
        console.error("לא ניתן להפעיל מוזיקה אוטומטית:", err);
      });
      toggleBtn.textContent = '🔊';
    }
    isPlaying = !isPlaying;
  }


</script>