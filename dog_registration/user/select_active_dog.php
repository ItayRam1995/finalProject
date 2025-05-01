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
        
        // אפשר להפנות לדשבורד או להישאר באותו עמוד
        echo "<script>window.location.href = '../../registration/user/user_dashboard_secured.php';</script>";
exit;
    } else {
        $error_message = "הכלב שנבחר אינו שייך למשתמש זה.";
    }
    
    $check_stmt->close();
}

// קבלת רשימת הכלבים של המשתמש
// בדיקת שגיאות מפורטת
$sql = "SELECT dog_id, dog_name, breed, image_url, age FROM dogs WHERE user_code = ? ORDER BY dog_id DESC";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("s", $user_code);

if (!$stmt->execute()) {
    die("Error executing query: " . $stmt->error);
}

$result = $stmt->get_result();



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
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', 'Arial', sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }
        
        header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-color);
        }
        
        header h1 {
            color: var(--secondary-color);
            margin-bottom: 10px;
        }
        
        header p {
            color: #777;
        }
        
        .status-message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
        }
        
        .status-error {
            background-color: rgba(236, 87, 102, 0.2);
            color: var(--error-color);
            border: 1px solid var(--error-color);
        }
        
        .status-success {
            background-color: rgba(71, 184, 129, 0.2);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }
        
        .dog-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .dog-card {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }
        
        .dog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        
        .dog-card.active {
            border: 3px solid var(--success-color);
        }
        
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
        
        .dog-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background-color: #f0f0f0;
        }
        
        .dog-details {
            padding: 15px;
        }
        
        .dog-name {
            font-size: 20px;
            font-weight: bold;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }
        
        .dog-breed {
            color: #666;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .dog-age {
            color: #777;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
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
        
        .select-btn:hover {
            background-color: var(--primary-color);
        }
        
        .select-btn.active {
            background-color: var(--success-color);
        }
        
        .select-btn.active:hover {
            background-color: #3ca574; /* מעט כהה יותר מ-success-color */
        }
        
        .add-new-dog {
            text-align: center;
            margin-top: 40px;
        }
        
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
        
        .add-new-dog-btn:hover {
            background-color: var(--secondary-color);
        }
        
        .no-dogs-message {
            text-align: center;
            padding: 40px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .no-dogs-message h2 {
            color: var(--secondary-color);
            margin-bottom: 15px;
        }
        
        .no-dogs-message p {
            color: #666;
            margin-bottom: 20px;
        }
        
        .debug-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
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
        
        <?php if (empty($user_code)): ?>
            <div class="status-message status-error">
                לא נמצא קוד משתמש בסשן. יש להתחבר שוב למערכת.
                <br><a href="../../registration/login.html">לחץ כאן להתחברות</a>
            </div>
        <?php endif; ?>
        
        <?php if ($result->num_rows == 0): ?>
            <div class="no-dogs-message">
                <h2>אין לך כלבים רשומים במערכת</h2>
                <p>כדי להשתמש בשירותי האתר, עליך קודם לרשום לפחות כלב אחד.</p>
                <a href="dog_registration.php" class="add-new-dog-btn">
                    <i class="fas fa-plus-circle"></i> הוסף כלב חדש
                </a>
            </div>
            
            <?php if (!empty($user_code)): ?>
            <div class="debug-info">
                <p>מידע דיבוג:</p>
                <p>קוד משתמש: <?= htmlspecialchars($user_code) ?></p>
                <p>מספר כלבים שנמצאו: <?= $result->num_rows ?></p>
                <p>שאילתה: SELECT dog_id, dog_name, breed, image_url, age FROM dogs WHERE user_code = '<?= htmlspecialchars($user_code) ?>' ORDER BY dog_id DESC</p>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="dog-cards">
                <?php while ($dog = $result->fetch_assoc()): ?>
                    <div class="dog-card <?= ($dog['dog_id'] == $active_dog_id) ? 'active' : '' ?>">
                        <?php if ($dog['dog_id'] == $active_dog_id): ?>
                            <div class="active-badge">פעיל כעת</div>
                        <?php endif; ?>
                        
                        <?php if (!empty($dog['image_url'])): ?>
                            <img src="<?= htmlspecialchars($dog['image_url']) ?>" alt="<?= htmlspecialchars($dog['dog_name']) ?>" class="dog-image">
                        <?php else: ?>
                            <div class="dog-image" style="display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-dog" style="font-size: 64px; color: #aaa;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="dog-details">
                            <div class="dog-name"><?= htmlspecialchars($dog['dog_name']) ?></div>
                            <div class="dog-breed"><?= htmlspecialchars($dog['breed']) ?></div>
                            <div class="dog-age">גיל: <?= htmlspecialchars($dog['age']) ?> שנים</div>
                            
                            <form method="post" action="">
                                <input type="hidden" name="selected_dog_id" value="<?= $dog['dog_id'] ?>">
                                <button type="submit" class="select-btn <?= ($dog['dog_id'] == $active_dog_id) ? 'active' : '' ?>">
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
            
            <?php if (isset($_SESSION["active_dog_id"])): ?>
<div class="add-new-dog">
                <a href="dog_registration.php" class="add-new-dog-btn">
                    <i class="fas fa-plus-circle"></i> הוסף כלב חדש
                </a>
            </div>
<?php endif; ?>
        <?php endif; ?>
    </div>
    
    <script>
        // סקריפט להסתרת הודעות סטטוס לאחר 5 שניות
        $(document).ready(function() {
            setTimeout(function() {
                $('.status-success').fadeOut(1000);
            }, 5000);
        });
    </script>
</body>
</html>

<?php
// סגירת חיבור מסד הנתונים
$stmt->close();
$conn->close();
?>