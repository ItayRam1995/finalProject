<?php include '../../header_For_first_Dog_Reg.php'; ?>
<?php

// וידוא שהמשתמש מחובר
if (!isset($_SESSION['username'])) {
    header("Location: ../../registration/login.html");
    exit;
}

// וידוא שהמשתמש הוא משתמש רגיל
if ($_SESSION['user_type'] != 0) {
    echo "<script>window.location.href = '../../registration/admin/admin_dashboard_secured.php';</script>";
exit;
}

// חיבור למסד הנתונים
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// הגדרה לעברית
$conn->set_charset("utf8mb4");

// קבלת ה-user_code מהסשן
$user_code = isset($_SESSION['user_code']) ? $_SESSION['user_code'] : '';


// טיפול בבחירת כלב פעיל
if (isset($_POST['selected_dog_id'])) {
    $selected_dog_id = intval($_POST['selected_dog_id']);
    
    // ווידוא שהכלב שייך למשתמש
    $check_sql = "SELECT dog_id, dog_name FROM dogs WHERE dog_id = ? AND user_code = ?";
    $check_stmt = $conn->prepare($check_sql);
    
    if ($check_stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    
    // קישור ערכים לפרמטרים בשאילתה
    $check_stmt->bind_param("is", $selected_dog_id, $user_code);

    // הרצת השאילתה מול מסד הנתונים
    $check_stmt->execute();

    // קבלת תוצאות מההרצה – התוצאה צפויה להיות שורה אחת אם הכלב קיים ושייך למשתמש
    $check_result = $check_stmt->get_result();
    
    // בדיקה אם התקבלה תוצאה, אם כן, שמים את הערכים שהתקבלו במשתנה
    if ($check_row = $check_result->fetch_assoc()) {
        // שמירת הכלב הנבחר בסשן
        $_SESSION['active_dog_id'] = $check_row['dog_id'];
        $_SESSION['active_dog_name'] = $check_row['dog_name'];
        
        // עדכון הודעת הצלחה והפניה לדשבורד
        $success_message = "הכלב '" . htmlspecialchars($check_row['dog_name']) . "' נבחר בהצלחה!";
        
        // // אפשר להפנות לדשבורד או להישאר באותו עמוד
        // echo "<script>window.location.href = '../../registration/user/user_dashboard_secured.php';</script>";
        // exit;

         $redirect_after_success = true;
    } else {
        $error_message = "הכלב שנבחר אינו שייך למשתמש זה.";
    }
    
    $check_stmt->close();
}

// קבלת רשימת הכלבים של המשתמש
$sql = "SELECT dog_id, dog_name, breed, image_url, age FROM dogs WHERE user_code = ? ORDER BY dog_id DESC";
// כאן מכינים את השאילתה באמצעות הפונקציה prepare של אובייקט החיבור למסד הנתונים ($conn)
$stmt = $conn->prepare($sql);

// אם יש טעות תחבירית ב-SQL
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("s", $user_code);

// הרצת השאילתה עם הפרמטר שהוזן
if (!$stmt->execute()) {
    die("Error executing query: " . $stmt->error);
}

//הפונקציה הזו שולפת את התוצאה של השאילתה שהרצנו.
$result = $stmt->get_result();

// בדיקה שהתוצאה תקינה
if ($result === false) {
    die("Error executing query: " . $stmt->error);
}

$active_dog_id = isset($_SESSION['active_dog_id']) ? $_SESSION['active_dog_id'] : 0;


?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>בחירת כלב פעיל</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        /* הגדרות צבעים כלליים */
        :root {
            --primary-color: #4A6FA5;
            --secondary-color: #166088;
            --accent-color: #4FC1E3;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --success-color: #47B881;
            --error-color: #EC5766;
            --warning-color: #F7D154;
        }
        
        /* איפוס סגנון גלובלי לכל האלמנטים */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', 'Arial', sans-serif;
        }
        
        /* עיצוב רקע כללי, צבע טקסט וגובה שורות של גוף  */
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        /* אלמנט עוטף לכל התוכן המרכזי – קובע רוחב, מרווח פנימי */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }
        
        /* כותרת הדף – מרכזת ומוסיפה גבול תחתון */
        header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-color);
        }
        
        /* כותרת ראשית – צבע כחול משני ומרווח  */
        header h1 {
            color: var(--secondary-color);
            margin-bottom: 10px;
        }
        
        /* תיאור מתחת לכותרת – צבע אפור  */
        header p {
            color: #777;
        }
        
        /* תיבת הודעה כללית – מיושרת, עם רקע ומרווח */
        .status-message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
        }
        
        /* הודעת שגיאה – רקע אדום שקוף, צבע טקסט אדום, גבול אדום */
        .status-error {
            background-color: rgba(236, 87, 102, 0.2);
            color: var(--error-color);
            border: 1px solid var(--error-color);
        }
        
        /* הודעת הצלחה – רקע ירוק שקוף, צבע ירוק, גבול ירוק */
        .status-success {
            background-color: rgba(71, 184, 129, 0.2);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }
        
        /* רשת שמכילה את כל כרטיסי הכלבים – עם מרווחים בין העמודות */
        /* תצוגה של כרטיסים בטורים שמתאימים את עצמם לרוחב המסך */
        .dog-cards {
            display: grid;
            /* כמה עמודות שיכנסו בשורה בהתאם לרוחב המסך */
            /* כל עמודה תהיה לפחות 280, אבל תוכל להתרחב עד שהיא תתפוס את כל המקום הפנוי */
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        
        /* כרטיס של כלב – לבן, עם צל וגבול מעוגל */
        .dog-card {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }
        
        /* אפקט ריחוף – מעלה מעט את הכרטיס ומוסיף צל עמוק יותר */
        .dog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        
        /* כרטיס פעיל – מוסיף גבול ירוק */
        .dog-card.active {
            border: 3px solid var(--success-color);
        }
        
        /* תווית ירוקה שמופיעה בפינה עליונה של כרטיס נבחר */
        .active-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--success-color);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* תמונת הכלב בתוך הכרטיס – גובה קבוע והתאמה למסגרת */
        .dog-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background-color: #f0f0f0;
        }
        
        /* קופסה פנימית בכרטיס עם פרטי הכלב (שם, גזע, גיל וכו') */
        .dog-details {
            padding: 15px;
        }
        
        /* שם הכלב – כותרת מודגשת בצבע כחול */
        .dog-name {
            font-size: 20px;
            font-weight: bold;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }

        /* גזע הכלב – טקסט אפור בהיר */
        .dog-breed {
            color: #666;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        /* גיל הכלב – טקסט אפור עוד יותר */
        .dog-age {
            color: #777;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        /* כפתור בחירה של כלב – רחב, רקע תכלת, כיתוב לבן */
        .select-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        /* שינוי רקע הכפתור כאשר העכבר על הכפתור – רקע כחול כהה יותר */
        .select-btn:hover {
            background-color: var(--primary-color);
        }
        
        /* כפתור שנבחר – רקע ירוק */
        .select-btn.active {
            background-color: var(--success-color);
        }
        
         /* שינוי רקע הכפתור כאשר העכבר על הכפתור – רקע ירוק כהה יותר */
        .select-btn.active:hover {
            background-color: #3ca574; /* מעט כהה יותר מ-success-color */
        }
        
        /* אלמנט עוטף לכפתור הוספת כלב חדש – ממורכז עם מרווח עליון */
        .add-new-dog {
            text-align: center;
            margin-top: 40px;
        }
        
        /* כפתור הוספת כלב – כחול בולט עם עיגול  */
        .add-new-dog-btn {
            display: inline-block;
            padding: 15px 30px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        /* שינוי רקע הכפתור כאשר העכבר על הכפתור – רקע כחול כהה יותר */
        .add-new-dog-btn:hover {
            background-color: var(--secondary-color);
        }
        
        /* קופסה שמוצגת אם אין כלבים רשומים – ממורכזת, לבנה, עם צל */
        .no-dogs-message {
            text-align: center;
            padding: 40px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        /* כותרת בתוך הודעת 'אין כלבים' – בצבע כחול */
        .no-dogs-message h2 {
            color: var(--secondary-color);
            margin-bottom: 15px;
        }
        
        /* טקסט בתוך הודעת 'אין כלבים' – צבע אפור */
        .no-dogs-message p {
            color: #666;
            margin-bottom: 20px;
        }
        
        /* תיבת מידע לדיבוג – רקע אפור בהיר עם גבול אפור */
        .debug-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        /* כללים למסכים קטנים – מציג כל כרטיס בטור אחד */
        @media (max-width: 768px) {
            .dog-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>בחירת הכלב הפעיל שלך</h1>
            <p>בחר את הכלב שברצונך להשתמש בו עבור פעילויות באתר</p>
        </header>
        
        <?php if (isset($success_message)): ?>
            <div class="status-message status-success"><?= $success_message ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="status-message status-error"><?= $error_message ?></div>
        <?php endif; ?>
        
        <!-- כרגע לא בשימוש -->
        <?php if (empty($user_code)): ?>
            <div class="status-message status-error">
                לא נמצא קוד משתמש בסשן. יש להתחבר שוב למערכת.
                <br><a href="../../registration/login.html">לחץ כאן להתחברות</a>
            </div>
        <?php endif; ?>
        
        <!-- כרגע בגלל הפונקציונליות של האתר לא מגיעים למקרה הזה, בגלל שהוא לא יכול להמשיך באתר אלא אם כן יש לו כלב רשום -->
        <?php if ($result->num_rows == 0): ?>
            <div class="no-dogs-message">
                <h2>אין לך כלבים רשומים במערכת</h2>
                <p>כדי להשתמש בשירותי האתר, עליך קודם לרשום לפחות כלב אחד.</p>
                <a href="dog_registration.php" class="add-new-dog-btn">
                    <i class="fas fa-plus-circle"></i> הוסף כלב חדש
                </a>
            </div>
            

        <?php else: ?>

            <!-- אלמנט רשת לכל הכרטיסים -->
            <div class="dog-cards">
                <!-- עוברת על כל שורת כלב מה־ result של שאילתת ה־ SQL -->
                <?php while ($dog = $result->fetch_assoc()): ?>
                    <!-- אם זה הכלב הנבחר תוסיף לו את המחלקה active עם המסגרת הירוקה -->
                    <div class="dog-card <?= ($dog['dog_id'] == $active_dog_id) ? 'active' : '' ?>">

                    <!-- מציג ריבוע קטן עם טקסט "פעיל כעת" בצד הימני העליון של הכרטיס, רק לכלב שנבחר -->
                        <?php if ($dog['dog_id'] == $active_dog_id): ?>
                            <div class="active-badge">פעיל כעת</div>
                        <?php endif; ?>
                        
                        <!-- אם יש כתובת לתמונה היא תוצג -->
                        <?php if (!empty($dog['image_url'])): ?>
                            <img src="<?= htmlspecialchars($dog['image_url']) ?>" alt="<?= htmlspecialchars($dog['dog_name']) ?>" class="dog-image">

                            <!-- אחרת יוצג אייקון כלב מה־ Font Awesome -->
                        <?php else: ?>
                            <div class="dog-image" style="display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-dog" style="font-size: 64px; color: #aaa;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- מציג שם, גזע וגיל של הכלב -->
                        <div class="dog-details">
                            <div class="dog-name"><?= htmlspecialchars($dog['dog_name']) ?></div>
                            <div class="dog-breed"><?= htmlspecialchars($dog['breed']) ?></div>
                            <div class="dog-age">גיל: <?= htmlspecialchars($dog['age']) ?> שנים</div>
                            
                            <!-- טופס ששולח את מזהה הכלב לשרת כדי לסמן אותו ככלב הפעיל -->
                            <form method="post" action="">
                                <input type="hidden" name="selected_dog_id" value="<?= $dog['dog_id'] ?>">

                                <!-- אם הכלב הוא הנבחר, הכפתור יקבל גם active – וישנה צבע לירוק -->
                                <button type="submit" class="select-btn <?= ($dog['dog_id'] == $active_dog_id) ? 'active' : '' ?>">
                                    <!-- הכפתור משנה את התוכן והאיקון שלו בהתאם אם זה כלב פעיל או לא -->
                                    <?php if ($dog['dog_id'] == $active_dog_id): ?>
                                        <i class="fas fa-check"></i> הכלב הפעיל כעת
                                    <?php else: ?>
                                        <i class="fas fa-paw"></i> בחר כלב זה
                                    <?php endif; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <!-- הוספת הכפתור "הוסף כלב חדש" (רק אם כבר יש כלב פעיל) -->
            <?php if (isset($_SESSION["active_dog_id"])): ?>
                <div class="add-new-dog">
                    <a href="dog_registration.php" class="add-new-dog-btn">
                        <i class="fas fa-plus-circle"></i> הוסף כלב חדש
                    </a>
                </div>
             <?php endif; ?>

             <!-- סוגר של התנאי הראשון בשורה 386 -->
        <?php endif; ?>

        <!-- סוגר של ה container -->
        </div>
    

    <script>
        // סקריפט להסתרת הודעות סטטוס לאחר 5 שניות
        $(document).ready(function() {
            setTimeout(function() {
                $('.status-success').fadeOut(1000);
            }, 5000);
        });
    </script>
    
    
        <!-- מבטיח שהפנייה תתרחש רק לאחר רישום מוצלח -->
        <?php if (isset($redirect_after_success) && $redirect_after_success === true): ?>
            <script>
            // המתנה של 3 שניות ואז מעבר לדף הדשבורד
            setTimeout(function() {
                window.location.href = '../../registration/user/user_dashboard_secured.php';
            }, 3000); // 3000 מילישניות = 3 שניות
            </script>
        <?php endif; ?>
</body>
</html>

<?php
// סגירת חיבור מסד הנתונים
$stmt->close();
$conn->close();
?>