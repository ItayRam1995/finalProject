<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../../registration/login.html");
    exit;
}

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
            // קבלת פרטי הכלב הפעיל
            $active_dog_id = $_SESSION['active_dog_id'];
            $get_dog_query = "SELECT dog_name FROM dogs WHERE dog_id = ? AND user_code = ?";
            $stmt = $conn->prepare($get_dog_query);
            $stmt->bind_param("is", $active_dog_id, $user_code);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $active_dog_name = htmlspecialchars($row['dog_name']);
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
        '../../grooming/admin/groomingPanel.php' => 'הזמנות טיפוח',
        '../../registration/admin/users_list.php' => 'משתמשים',
        '../../registration/admin/all_orders.php' => 'הזמנות',
        '../../registration/admin/update_availability.php' => 'עדכון זמינות',
        '../../registration/admin/delete_order.php' => 'מחיקת הזמנה',
        '../../inventory_management/admin/inventory_management.php' => 'ניהול מלאי'
    ]
    : [
        '../../registration/user/user_dashboard_secured.php' => 'דשבורד',
        '../../registration/user/my_orders.php' => 'הזמנות',
        '../../reservation/user/reservation.php' => 'הזמנה חדשה',
        '../../registration/user/update_profile_secured.php' => 'עדכון פרטים',
        '../../grooming/user/treatments.php' => 'הזמנת טיפוח',
        '../../dog_registration/user/dog_registration.php' => 'רישום כלב חדש',
        '../../dog_registration/user/select_active_dog.php' => 'החלפת כלב פעיל',
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
    
    /*  הכותרת הראשית - fixed בחזרה לראש הדף */
    .doggy-header-container {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        background: <?= $headerBgColor ?> !important;
        color: white !important;
        font-family: 'Assistant', 'Rubik', Arial, sans-serif !important;
        font-size: 16px !important;
        z-index: 1000 !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
        direction: rtl !important;
        display: flex !important;
        flex-direction: column !important;
    }
    
    /* סרגל עליון עם לוגו ופרטי משתמש */
    .doggy-header-top {
        display: flex !important;
        justify-content: space-between !important;
        align-items: flex-start !important; 
        padding: 10px 20px !important;
        background-color: rgba(0, 0, 0, 0.1) !important;
        position: relative !important; 
    }
    
    /* לוגו האתר */
    .doggy-header-logo {
        font-weight: bold !important;
        font-size: 20px !important;
        color: white !important;
        text-decoration: none !important;
        display: flex !important;
        align-items: center !important;
        margin-top: 5px !important; /* הוספת מרווח קטן מלמעלה ליישור טוב יותר */
    }
    
    .doggy-header-logo-icon {
        margin-left: 8px !important;
        font-size: 24px !important;
    }
    
    /* מידע על המשתמש*/
    .doggy-header-user-info {
        display: flex !important;
        flex-direction: column !important; 
        align-items: flex-end !important; /* יישור לימין */
        gap: 8px !important;
        padding-top: 15px !important; /* מרווח מלמעלה כדי לפנות מקום לתגית סוג המשתמש */
    }
    
    /* שורה עם השם הפרטי והתנתקות */
    .doggy-header-user-controls {
        display: flex !important;
        align-items: center !important;
        gap: 15px !important;
    }
    
    .doggy-header-welcome {
        background: white !important;
        color: <?= $headerBgColor ?> !important;
        padding: 6px 12px !important;
        border-radius: 5px !important;
        font-weight: bold !important;
        display: flex !important;
        align-items: center !important;
    }
    
    .doggy-header-welcome-icon {
        margin-left: 5px !important;
    }
    
    /* סגנון לאזור הכלב הפעיל */
    .doggy-header-active-dog {
        background: rgba(255, 255, 255, 0.15) !important;
        color: white !important;
        padding: 6px 12px !important;
        border-radius: 5px !important;
        font-weight: bold !important;
        display: flex !important;
        align-items: center !important;
        margin-right: 10px !important;
    }
    
    .doggy-header-dog-icon {
        margin-left: 5px !important;
    }
    
    .doggy-header-logout {
        color: white !important;
        text-decoration: none !important;
        padding: 6px 12px !important;
        border-radius: 5px !important;
        background-color: rgba(255, 255, 255, 0.1) !important;
        transition: background-color 0.3s ease !important;
        display: flex !important;
        align-items: center !important;
    }
    
    .doggy-header-logout:hover {
        background-color: rgba(255, 255, 255, 0.2) !important;
    }
    
    .doggy-header-logout-icon {
        margin-right: 5px !important;
    }
    
    /* תפריט הניווט */
    .doggy-header-nav {
        padding: 10px 20px !important;
        display: flex !important;
        justify-content: center !important;
    }
    
    .doggy-header-links {
        display: flex !important;
        gap: 5px !important;
        flex-wrap: wrap !important;
        justify-content: center !important;
        max-width: 1200px !important;
    }
    
    .doggy-header-link {
        color: white !important;
        text-decoration: none !important;
        padding: 8px 15px !important;
        border-radius: 5px !important;
        transition: background-color 0.3s ease !important;
        white-space: nowrap !important;
        font-weight: 500 !important;
    }
    
    .doggy-header-link:hover {
        background-color: <?= $headerAccentColor ?> !important;
    }
    
    /* אינדיקציה לסוג משתמש */
    .doggy-header-user-type {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        background-color: <?= $headerAccentColor ?> !important;
        color: white !important;
        font-size: 11px !important;
        padding: 2px 8px !important;
        border-bottom-right-radius: 5px !important;
    }
    
    /* התאמה למובייל */
    @media (max-width: 768px) {
        body {
            padding-top: 190px !important; /* הגדלת הריווח למסכים קטנים */
        }
        
        .doggy-header-top {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 10px !important;
            padding: 10px !important;
        }
        
        .doggy-header-user-info {
            align-items: stretch !important;
            padding-top: 20px !important; /* הגדלת המרווח במובייל */
        }
        
        .doggy-header-user-controls {
            justify-content: space-between !important;
            flex-wrap: wrap !important;
        }
        
        .doggy-header-links {
            flex-direction: column !important;
            width: 100% !important;
            gap: 5px !important;
        }
        
        .doggy-header-link {
            text-align: center !important;
            padding: 10px !important;
        }
        
        .doggy-header-active-dog {
            margin-top: 5px !important;
            margin-right: 0 !important;
        }
    }
    
    /* סקריפט JavaScript להתאמת padding-top בזמן ריענון הדף */
    .js-header-height-script {
        display: none !important;
    }
</style>

<div class="doggy-header-container">
    <!-- סרגל עליון -->
    <div class="doggy-header-top">
        <!-- אינדיקציה לסוג משתמש  -->
        <div class="doggy-header-user-type">
            <?= $user_type == 1 ? 'מנהל' : 'משתמש' ?>
        </div>
        
        <!-- לוגו -->
        <a href="<?= $user_type == 1 ? '../../registration/admin/admin_dashboard_secured.php' : '../../registration/user/user_dashboard_secured.php' ?>" class="doggy-header-logo">
            <span>פנסיון כלבים</span>
            <span class="doggy-header-logo-icon">🐕</span>
          
        </a>
        
        <!-- מידע משתמש וכפתור התנתקות -->
        <div class="doggy-header-user-info">
            <!-- שורת עם שם פרטי, כלב פעיל והתנתקות -->
            <div class="doggy-header-user-controls">
                <div class="doggy-header-welcome">
                    <span class="doggy-header-welcome-icon">👋</span>
                    <span>שלום, <?= $first_name ?></span>
                </div>
                
                <?php if ($user_type == 0 && isset($active_dog_name)): ?>
                <div class="doggy-header-active-dog">
                    <span class="doggy-header-dog-icon">🦮</span>
                    <span>כלב פעיל: <?= $active_dog_name ?></span>
                </div>
                <?php endif; ?>
                
                <a href="../../registration/logout.php" class="doggy-header-logout">
                    <span>התנתקות</span>
                    <span class="doggy-header-logout-icon">🚪</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- סרגל ניווט -->
    <nav class="doggy-header-nav">
        <div class="doggy-header-links">
            <?php foreach ($links as $href => $label): ?>
                <a href="<?= $href ?>" class="doggy-header-link"><?= $label ?></a>
            <?php endforeach; ?>
        </div>
    </nav>
</div>

<!-- סקריפט להתאמת גובה הכותרת בזמן אמת -->
<script class="js-header-height-script">
document.addEventListener('DOMContentLoaded', function() {
    function adjustPadding() {
        const headerHeight = document.querySelector('.doggy-header-container').offsetHeight;
        document.body.style.paddingTop = headerHeight + 'px';
    }
    
    // התאמה ראשונית
    adjustPadding();
    
    // התאמה בכל פעם שחלון הדפדפן משתנה
    window.addEventListener('resize', adjustPadding);
});
</script>