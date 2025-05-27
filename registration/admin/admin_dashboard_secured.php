<?php include '../../header.php'; ?>
<?php

// חיבור למסד נתונים
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

try {
    // אובייקט שעוזר להתחבר למסד נתונים, להריץ שאילתות ולקבל תוצאות
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);

    // מאפשר להשתמש ב־ try/catch כדי לטפל בשגיאות בצורה מסודרת
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // טיפול בשגיאה שיכולה להתרחש בזמן התחברות למסד הנתונים 
    // האובייקט של החריגה, שמכיל מידע על השגיאה
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// קבלת נתונים מהטבלאות - עם טיפול בשגיאות
try {
    // 1. סטטיסטיקות כלליות
    // שאילתה כדי לספור את כמות הכלבים הרשומים במערכת
    $totalDogs = $pdo->query("SELECT COUNT(*) as count FROM dogs")->fetch()['count'] ?? 0;
    // שאילתה כדי לספור את כמות המשתמשים הרשומים במערכת
    $totalUsers = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch()['count'] ?? 0;
    // שאילתה כדי לספור את מספר ההזמנות הפעילות במערכת שתאריך הסיום שלהם גדול או שווה לתאריך של היום
    $activeReservations = $pdo->query("
    SELECT COUNT(*) as count FROM reservation WHERE status = 'active' AND end_date >= CURDATE()")->fetch()['count'] ?? 0;

     // שאילתה כדי לקבל את זמינות המקומות עבור התאריך של היום
    $availabilityToday = $pdo->query("SELECT available_spots FROM Availability WHERE date = CURDATE()")->fetch();
    // האם נמצא ערך של זמינות להיום – אם כן, שומר את מספר המקומות הפנויים. אם לא – שומר 50
    // אם מתקבל false, סימן שהשורה לא קיימת בטבלה עבור התאריך של היום, ולכן זה אומר שעבור התאריך של היום יש 50 מקומות פנויים בפנסיון
    $availableSpots = $availabilityToday ? $availabilityToday['available_spots'] : 50;

    // 2. הזמנות פעילות
    $activeReservationsQuery = $pdo->query("
        SELECT r.*, d.dog_name, u.first_name, u.last_name 
        FROM reservation r 
        LEFT JOIN dogs d ON r.dog_id = d.dog_id 
        LEFT JOIN users u ON r.user_code = u.user_code 
        WHERE r.status = 'active' AND r.end_date >= CURDATE()
        ORDER BY r.start_date ASC 
    ");

    // לשלוף את כל התוצאות שהתקבלו ולשמור אותן במילון
    $activeReservationsList = $activeReservationsQuery->fetchAll(PDO::FETCH_ASSOC);

    // 3. תורי טיפוח השבוע - רק עם הזמנות פנסיון קיימות
    $groomingQuery = $pdo->query("
        SELECT g.*, d.dog_name, u.first_name, u.last_name, r.id as reservation_id
        FROM grooming_appointments g 
        LEFT JOIN dogs d ON g.dog_id = d.dog_id 
        LEFT JOIN users u ON g.user_code = u.user_code 
        INNER JOIN reservation r ON g.connected_reservation_id = r.id
        WHERE g.day >= CURDATE() 
        AND g.day <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        AND g.isTaken = 1 
        ORDER BY g.day ASC, STR_TO_DATE(g.time, '%H:%i') ASC
    ");

    // לשלוף את כל התוצאות שהתקבלו ולשמור אותן במילון
    $groomingAppointments = $groomingQuery->fetchAll(PDO::FETCH_ASSOC);

    // 4. מלאי נמוך
    $lowStockQuery = $pdo->query("
        SELECT name, current_stock, minimum_required, unit_type 
        FROM inventory 
        WHERE current_stock <= minimum_required 
        ORDER BY (current_stock - minimum_required) ASC
    ");
    // לשלוף את כל התוצאות שהתקבלו ולשמור אותן במילון
    $lowStockItems = $lowStockQuery->fetchAll(PDO::FETCH_ASSOC);

    // 5. הכנסות חודשיות - סכימה של תשלומי הזמנות + תשלומי טיפוח
    $reservationRevenue = $pdo->query("
        SELECT COALESCE(SUM(total_payments), 0) as revenue 
        FROM reservation 
        WHERE MONTH(created_at) = MONTH(CURDATE()) 
        AND YEAR(created_at) = YEAR(CURDATE())
        AND status = 'paid'
    ")->fetch()['revenue'] ?? 0;

    // הכנסות מטיפוח - רק מהזמנות טיפוח שהזמנת הפנסיון המקושרת קיימת
    $groomingRevenue = $pdo->query("
        SELECT COALESCE(SUM(g.grooming_price), 0) as revenue 
        FROM grooming_appointments g 
        INNER JOIN reservation r ON g.connected_reservation_id = r.id
        WHERE MONTH(g.created_at) = MONTH(CURDATE()) 
        AND YEAR(g.created_at) = YEAR(CURDATE())
        AND g.isTaken = 1
        AND g.status = 'paid'
    ")->fetch()['revenue'] ?? 0;

    $monthlyRevenue = $reservationRevenue + $groomingRevenue;

    // 6. מספר הכלבים החדשים שהצטרפו השבוע
    $newDogsThisWeek = $pdo->query("
        SELECT COUNT(*) as count 
        FROM dogs 
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    ")->fetch()['count'] ?? 0;

    // 7. זמינות מקום לשבוע הקרוב בפנסיון
    $availabilityWeek = $pdo->query("
        SELECT date, available_spots 
        FROM Availability 
        WHERE date >= CURDATE() AND date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        ORDER BY date
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // 8. סוגי טיפוח לשבוע הקרוב - רק מהזמנות טיפוח שהזמנת הפנסיון המקושרת קיימת
    $groomingTypeQuery = $pdo->query("
        SELECT g.grooming_type, COUNT(*) as count
        FROM grooming_appointments g 
        INNER JOIN reservation r ON g.connected_reservation_id = r.id
        WHERE g.day >= CURDATE() AND g.isTaken = 1
        AND g.day <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)  
        GROUP BY g.grooming_type
        ORDER BY count DESC
    ");
     // לשלוף את כל התוצאות שהתקבלו ולשמור אותן במילון
    $groomingTypes = $groomingTypeQuery->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    // במקרה של שגיאה, נציב ערכי ברירת מחדל
    $totalDogs = 0;
    $totalUsers = 0;
    $activeReservations = 0;
    $availableSpots = 0;
    $activeReservationsList = [];
    $groomingAppointments = [];
    $lowStockItems = [];
    $monthlyRevenue = 0;
    $newDogsThisWeek = 0;
    $availabilityWeek = [];
    $error_message = "שגיאה בקריאת נתונים: " . $e->getMessage();
    $groomingTypes = [];
}

?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>דשבורד מנהל - פנסיון כלבים</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>

      /* מאפס מרווחים פנימיים וחיצוניים */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* צבע רקע בהיר */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* צבע גרדיאנט סגול-כחול, צבע טקסט לבן, ריווח, פינות מעוגלות וצל */
        .header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }
        
        /* רשת של כרטיסים */
        /* גריד שמסדר את הכרטיסים בשורות רספונסיביות */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        /* כל כרטיס סטטיסטיקה */
        /* רקע לבן, ריווח פנימי, פינות מעוגלות, צל, יישור למרכז. */
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        
        /* מרים את הכרטיס */
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 3em;
            margin-bottom: 15px;
        }
        
        .stat-value {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #666;
            font-size: 1.1em;
        }
        
        /* גריד לתוכן רחב */
        /* שני טורים או טור אחד */
        .content-grid {
            display: grid;
            /* מגדיר כיצד יתחלקו הטורים בתוך הגריד בצורה רספונסיבית ודינמית */
            /* מנסה להתאים כמה שיותר עמודות לפי הרוחב הקיים במיכל */
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
        }
        
        /* בלוק מידע */
        /* רקע לבן, פינות מעוגלות, צל, ללא גלילה פנימית */
        .section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        /* כותרת עליונה של הבלוק - רקע בהיר וגבול אפור */
        .section-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .section-title {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 5px;
        }
        
        /* גוף הבלוק - ריווח פנימי */
        .section-content {
            padding: 20px;
        }
        
        /* פריט אחד ברשימה */
        /* מסגרת , פינות מעוגלות, רקע לבן, צל , יישור שורה */
        .list-item {
            padding: 15px;
            border: 1px solid #ddd;       
            border-radius: 10px;         
            margin-bottom: 10px;        
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;            
            box-shadow: 0 2px 4px rgba(0,0,0,0.04); 
        }
        
        /* הפריט האחרון - מסיר גבול תחתון */
        .list-item:last-child {
            border-bottom: none;
        }
        
        /* אזור טקסט בפריט */
        /* תופס את כל הרוחב הפנוי */
        .item-info {
            flex: 1;
        }
        
        /* כותרת של פריט */
        .item-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* תיאור קצר */
        .item-details {
            color: #666;
            font-size: 0.9em;
        }
        
        /* תגית סטטוס */
        /* רקע צבעוני, פינות עגולות, טקסט קטן ומודגש */
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
        }
        
        /* סטטוס פעיל - רקע ירוק בהיר וטקסט ירוק */
        .status-active { background: #d4edda; color: #155724; }
        /* סטטוס ממתין	 -  רקע צהוב וטקסט חום */
        .status-pending { background: #fff3cd; color: #856404; }
        /* סטטוס מלאי נמוך	- רקע אדום בהיר וטקסט אדום */
        .status-low { background: #f8d7da; color: #721c24; }
        
        /* תצוגת מחיר */
        /* טקסט בצבע ירוק + מודגש */
        .price {
            color: #28a745;
            font-weight: bold;
        }
        
        /* התראות */

        /* הודעת אזהרה רגילה - רקע צהוב בהיר, מסגרת צהובה, פינות עגולות */
        .alert {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        /* אייקון בהתראה - צבע חום כהה, מרווח מצד שמאל */
        .alert-icon {
            color: #856404;
            margin-left: 10px;
        }
        
        /* הודעת שגיאה - רקע אדום בהיר, מסגרת אדומה, טקסט אדום */
        .error-alert {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        /* אזור עם גלילה */
        /* גלילה אנכית בלבד */
        .scrollable-content {
            max-height: 400px;
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        /* עיצוב של פס הגלילה */
        /* פס גלילה דק */
        .scrollable-content::-webkit-scrollbar {
            width: 8px;
        }
        
        /* מסילה - צבע רקע בהיר */
        .scrollable-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        /* החלק שנע - צבע אפור עם עיצוב מעוגל */
        .scrollable-content::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
            transition: background 0.3s;
        }
        
        .scrollable-content::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        @media (max-width: 768px) {
          /* עובר לטור אחד במקום גריד */
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 10px;
            }
            /* לכרטיסים ניידים - הפחתת גובה מקסימלית */
                        .scrollable-content {
                max-height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- כותרת ראשית -->
        <div class="header">
            <h1><i class="fas fa-paw"></i> דשבורד מנהל הפנסיון</h1>
            <p>מידע מעודכן על פעילות הפנסיון - <?php echo date('d/m/Y H:i'); ?></p>
        </div>

        <!-- בודק אם הודעת שגיאה בהתחברות למסד נתונים -->
        <?php if (isset($error_message)): ?>
        <div class="error-alert">
            <i class="fas fa-exclamation-triangle"></i>
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>

        <!-- מעטפת רשת לכרטיסי המידע -->
        <div class="stats-grid">

             <!-- סה"כ כלבים רשומים למערכת -->
            <div class="stat-card">
                <div class="stat-icon" style="color: #4CAF50;">
                    <i class="fas fa-dog"></i>
                </div>
                <!-- עיצוב מספרי שדואג לשים פסיקים -->
                <div class="stat-value"><?php echo number_format($totalDogs); ?></div>
                <div class="stat-label">סה"כ כלבים רשומים למערכת</div>
            </div>
            
            <!-- הזמנות שהייה פעילות-->
            <div class="stat-card">
                <div class="stat-icon" style="color: #2196F3;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-value"><?php echo number_format($activeReservations); ?></div>
                <div class="stat-label">הזמנות שהייה פעילות</div>
            </div>
            
            <!-- מקומות פנויים היום בפנסיון -->
            <div class="stat-card">
                <div class="stat-icon" style="color: #FF9800;">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div class="stat-value"><?php echo number_format($availableSpots); ?></div>
                <div class="stat-label">מקומות פנויים היום</div>
            </div>
            
            <!-- סה"כ לקוחות רשומים למערכת -->
            <div class="stat-card">
                <div class="stat-icon" style="color: #9C27B0;">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo number_format($totalUsers); ?></div>
                <div class="stat-label">לקוחות רשומים למערכת</div>
            </div>
            

            <!-- סה"כ הכנסות החודש מהזמנות פנסיון ומהזמנות טיפוח -->
            <div class="stat-card" style="position: relative;">
                <div class="stat-icon" style="color: #4CAF50;">
                    <i class="fas fa-shekel-sign"></i>
                </div>
                <div class="stat-value">₪<?php echo number_format($monthlyRevenue); ?></div>
                <div class="stat-label">הכנסות החודש</div>
                
                <!-- פירוט ההכנסות -->
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e9ecef; font-size: 0.85em;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="color: #666;">הזמנות פנסיון:</span>
                        <span style="font-weight: bold; color: #28a745;">₪<?php echo number_format($reservationRevenue); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="color: #666;">טיפוח כלבים:</span>
                        <span style="font-weight: bold; color: #17a2b8;">₪<?php echo number_format($groomingRevenue); ?></span>
                    </div>
                    
                    <!-- גרף עמודות קטן להמחשה -->
                     <!-- מחשב כמה אחוז מההכנסות החודשיות הגיעו מהזמנת פנסיון ו־טיפוח. -->
                      <!-- אם לא היו הכנסות שיהיו אפס -->
                    <?php 
                    $reservationPercentage = $monthlyRevenue > 0 ? ($reservationRevenue / $monthlyRevenue) * 100 : 0;
                    $groomingPercentage = $monthlyRevenue > 0 ? ($groomingRevenue / $monthlyRevenue) * 100 : 0;
                    ?>
                    <div style="margin-top: 10px;">
                        <!-- הפס החיצוני בצבע אפור בהיר -->
                         <!-- בתוך הפס – פס פנימי בצבע ירוק וכחול -->
                        <div style="background: #f8f9fa; height: 8px; border-radius: 4px; overflow: hidden;">
                            <!-- אפקט של פס מפוצל לפי מקור ההכנסה -->
                            <div style="height: 100%; background: linear-gradient(to right, #28a745 <?php echo $reservationPercentage; ?>%, #17a2b8 <?php echo $reservationPercentage; ?>%); border-radius: 4px;"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-top: 5px; font-size: 0.75em; color: #888;">
                            <span><?php echo round($groomingPercentage); ?>% טיפוח</span>
                            <span><?php echo round($reservationPercentage); ?>% פנסיון</span>
                            
                        </div>
                    </div>
                </div>
            </div>            
                     
            <!-- כלבים חדשים שנרשמו השבוע למערכת -->
            <div class="stat-card">
                <div class="stat-icon" style="color: #FF5722;">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="stat-value"><?php echo number_format($newDogsThisWeek); ?></div>
                <div class="stat-label">כלבים חדשים השבוע שנרשמו למערכת</div>
            </div>
            
            
            
            <!-- כרטיס סוגי טיפוח השבוע -->
             <!-- פריסה של סוגי הטיפוח לפי כמות הזמנות טיפוח -->
             <!-- כרטיס זה יתפוס שני עמודות במקום עמודה אחת -->
            <div class="stat-card" style="grid-column: span 2; text-align: left;">
                <div class="section-header" style="background: none; padding: 0; border: none; border-bottom: 1px solid #f0f0f0; margin-bottom: 15px;">
                    <h2 style="font-size: 1.3em; color: #333; display: flex; align-items: center;">
                        <i class="fas fa-cut" style="color: #e74c3c; margin-left: 10px;"></i>
                        פריסה לפי כמות של סוגי הזמנות טיפוח השבוע
                    </h2>
                </div>
                
                <!-- אם אין הזמנות טיפוח השבוע  -->
                <?php if (empty($groomingTypes)): ?>
                    <p style="text-align: center; color: #666; padding: 20px;">אין תורי טיפוח השבוע</p>
                <?php else: ?>
                    <!-- סרגל גלילה אנכי רק אם התוכן חורג מהגובה -->
                    <div style="max-height: 200px; overflow-y: auto;">
                        <!-- מציג רשימה של סוגי טיפוח -->
                         <!-- $groomingTypes הוא מערך של מילונים -->
                          <!-- $index הוא המיקום של המילון בתוך המערך -->
                          <!-- $type הערך עצמו באותו אינדקס, כלומר המילון עצמו מתוך המערף -->
                        <?php foreach ($groomingTypes as $index => $type): ?>
                            <?php
                                // צבעים שונים לכל סוג טיפוח
                                $colors = ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c'];
                                // להקצות צבע שונה לכל מילון, מתוך מערך של צבעים
                                // שארית החלוקה במספר הצבעים 
                                $color = $colors[$index % count($colors)];
                            ?>
                            <!-- שורה של סוג טיפוח -->
                             <!-- justify-content: space-between	מציב את התוכן בשני הצדדים של השורה (ימין ושמאל) -->
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">

                                 <!-- צד ימין של שורת טיפוח -->
                                <div style="display: flex; align-items: center;">
                                    <!-- עיגול צבעוני קטן -->
                                    <span style="display: inline-block; width: 8px; height: 8px; background: <?php echo $color; ?>; border-radius: 50%; margin-left: 8px;"></span>
                                    <!-- שם סוג הטיפוח -->
                                    <span style="font-size: 0.9em;"><?php echo htmlspecialchars($type['grooming_type'] ?? 'לא צוין'); ?></span>
                                </div>
                                
                                <!-- צד שמאל של שורת הטיפוח -->
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <!-- מספר התורים מסוג הזמנת הטיפוח -->
                                    <span class="status-badge status-active">
                                        <?php echo number_format($type['count']); ?> תורים
                                    </span>
                                </div>

                            </div>
                        <?php endforeach; ?>
                        
                        <!-- סיכום של סה"כ הזמנות הטיפוח -->
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 2px solid #e9ecef; text-align: center;">
                            <span style="color: #666; font-size: 0.8em;">
                                <!-- מחשב את הסכום הכולל של כל התורים -->
                                 <!-- מוציא את כל ערכי count ממערך $groomingTypes -->
                                סה"כ: <strong style="color: #333;"><?php echo array_sum(array_column($groomingTypes, 'count')); ?> תורים</strong>
                                <small style="color: #999;">(עם הזמנות פנסיון פעילות)</small>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div> 

        <!-- מעטפת רשת לכרטיסי התוכן המפורט יותר -->
        <div class="content-grid">


            <!-- מציג רשימת הזמנות פנסיון לכלבים שנמצאות כעת בסטטוס פעיל -->
           <!-- /* בלוק מידע */
             /* רקע לבן, פינות מעוגלות, צל, ללא גלילה פנימית */ -->
            <div class="section">
                <!-- כותרות -->
                <div class="section-header">
                    <h2 class="section-title">הזמנות שהייה בפנסיון פעילות</h2>
                </div>
                <!-- אזור התוכן ורישמות ארוכות -->
                <div class="section-content scrollable-content">
                    <!-- האם אין הזמנות פעילות -->
                    <?php if (empty($activeReservationsList)): ?>
                        <p style="text-align: center; color: #666; padding: 20px;">אין הזמנות פעילות כרגע</p>
                    <?php else: ?>
                         <!-- עוברת על רשימת ההזמנות הפעילות ומציגה כל אחת מהן -->
                          <!-- $activeReservationsList	מערך של הזמנות פעילות, כל אחת היא מערך עם פרטים על ההזמנה -->
                           <!-- $index	מספר סידורי -->
                            <!-- $reservation	המידע של הזמנה אחת -->
                        <?php foreach ($activeReservationsList as $index => $reservation): ?>
                             <!-- שורת הזמנה -->
                            <div class="list-item">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                     <!-- מונה שורה – עיגול ממוספר -->
                                    <span style="background: #007bff; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.9em;">
                                        <?php echo $index + 1; ?>
                                    </span>
                                    <!-- פרטי הכלב והבעלים, תאריכי התחלה וסיום -->
                                    <div class="item-info">
                                        <div class="item-title"><?php echo htmlspecialchars($reservation['dog_name'] ?? 'לא צוין'); ?></div>
                                        <div class="item-details">
                                            בעלים: <?php echo htmlspecialchars(($reservation['first_name'] ?? '') . ' ' . ($reservation['last_name'] ?? '')); ?><br>
                                            מ-<?php echo date('d/m/Y', strtotime($reservation['start_date'])); ?> 
                                            עד <?php echo date('d/m/Y', strtotime($reservation['end_date'])); ?><br>
                                            <small style="color: #007bff;">הזמנת פנסיון #<?php echo $reservation['id']; ?></small>
                                        </div>
                                    </div>
                                </div>
                                <span class="status-badge status-active">פעיל</span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- תורי טיפוח השבוע -->
             <!-- כל תור כולל שם כלב, שם בעלים, תאריך, שעה וסוג הטיפוח -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">
                        תורי טיפוח השבוע
                        <small style="font-size: 0.7em; color: #666; display: block; margin-top: 5px;">
                            (רק עם הזמנות פנסיון פעילות)
                        </small>
                    </h2>
                </div>
                <div class="section-content">
                    <!-- אם אין תורי טיפוח השבוע -->
                    <?php if (empty($groomingAppointments)): ?>
                        <p style="text-align: center; color: #666; padding: 20px;">אין תורי טיפוח השבוע עם הזמנות פנסיון פעילות</p>
                    <?php else: ?>
                        <div class="scrollable-content">
                              <!-- עוברת על רשימת ההזמנות הטיפוח לשבוע הקרוב ומציגה כל אחת מהן -->
                            <!-- $groomingAppointments	מערך של הזמנות טיפוח, כל אחת היא מערך עם פרטים על הזמנת הטיפוח -->
                            <!-- $index	מספר סידורי -->
                            <!-- $appointment	המידע של הזמנה אחת -->
                            <?php foreach ($groomingAppointments as $index => $appointment): ?>
                                 <!-- שורת הזמנת טיפוח -->
                                <div class="list-item">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <!-- מונה שורה – עיגול ממוספר -->
                                        <span style="background: #28a745; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.9em;">
                                            <?php echo $index + 1; ?>
                                        </span>
                                        <div class="item-info">
                                            <div class="item-title"><?php echo htmlspecialchars($appointment['dog_name'] ?? 'לא צוין'); ?></div>
                                            <div class="item-details">
                                                בעלים: <?php echo htmlspecialchars(($appointment['first_name'] ?? '') . ' ' . ($appointment['last_name'] ?? '')); ?><br>
                                                תאריך: <?php echo date('d/m/Y', strtotime($appointment['day'])); ?> בשעה <?php echo $appointment['time']; ?><br>
                                                סוג: <?php echo htmlspecialchars($appointment['grooming_type'] ?? 'לא צוין'); ?>
                                                <?php if (isset($appointment['reservation_id'])): ?>
                                                    <br><small style="color: #007bff;">קשור להזמנת פנסיון מספר #<?php echo $appointment['reservation_id']; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="price">₪<?php echo number_format($appointment['grooming_price'] ?? 0); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
           <!-- זמינות מקום בפנסיון לשבוע הקרוב -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">זמינות מקום לשבוע הקרוב</h2>
                </div>
                <div class="section-content">
                    <?php
                    // יצירת מערך של כל התאריכים בשבוע הקרוב
                    $nextWeekDates = [];
                    for ($i = 0; $i < 7; $i++) {
                        // יצירת תאריך עתידי לפי מספר ימים קדימה מהיום
                        $date = date('Y-m-d', strtotime("+$i days"));
                        $nextWeekDates[$date] = 50; // ברירת מחדל - 50 מקומות
                    }
                    
                    //  Availability עדכון הזמינות בהתאם לנתונים בטבלה 
                    // אם קיימים נתוני זמינות מקום לשבוע הקרוב
                    if (!empty($availabilityWeek)) {
                        foreach ($availabilityWeek as $availability) {
                            $date = $availability['date'];
                            // Availability דריסה של הערך בתאריך דיפולטבי שעבורו יש ערך בטבלה 
                            $nextWeekDates[$date] = $availability['available_spots'];
                        }
                    }
                    
                    // מיון  לפי תאריכים בסדר עולה (יש למילון הזה רק מפתח אחד)
                    ksort($nextWeekDates);
                    
                    // המילון הזה לא יכול להיות ריק בשלב הזה אבל החלטנו להשאיר את הבדיקה הזאת
                    if (empty($nextWeekDates)): 
                    ?>
                        <p style="text-align: center; color: #666; padding: 20px;">לא נמצאו נתוני זמינות</p>
                    <?php else: ?>
                        <!-- $date תאריך -->
                        <!-- $available_spots מספר המקומות הפנויים באותו יום -->
                        <?php foreach ($nextWeekDates as $date => $available_spots): ?>
                            <?php 
                                // ממיר את הפורמט לתצוגה של שנה -חודש -יום (21/05/2025)
                                $dateFormatted = date('d/m/Y', strtotime($date));
                                $dayName = '';
                                // מחלץ את שם היום בעברית לפי המספר (0–6)
                                switch(date('w', strtotime($date))) {
                                    case 0: $dayName = 'ראשון'; break;
                                    case 1: $dayName = 'שני'; break;
                                    case 2: $dayName = 'שלישי'; break;
                                    case 3: $dayName = 'רביעי'; break;
                                    case 4: $dayName = 'חמישי'; break;
                                    case 5: $dayName = 'שישי'; break;
                                    case 6: $dayName = 'שבת'; break;
                                }
                                // קביעת מחלקת עיצוב
                                $statusClass = $available_spots > 20 ? 'status-active' : 
                                            ($available_spots > 10 ? 'status-pending' : 'status-low');
                            ?>
                          <!-- שורה אחת של תצוגת זמינות ליום ספציפי, עם פרטי היום, תאריך ומספר המקומות הפנויים -->
                            <div class="list-item">
                                <div class="item-info">
                                    <div class="item-title">יום <?php echo $dayName; ?> - <?php echo $dateFormatted; ?></div>
                                    <div class="item-details">מקומות פנויים בתאריך זה</div>
                                </div>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo number_format($available_spots); ?> מקומות
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- מציג רשימה של פריטים שהמלאי מהם ירד מתחת לרף הנדרש -->
            <div class="section">
               <div class="section-header">
                <!-- אייקון התראה אדום + כותרת מלאי נמוך -->
                   <h2 class="section-title">
                       <i class="fas fa-exclamation-triangle" style="color: #ff6b6b;"></i>
                       מלאי נמוך
                   </h2>
               </div>
               <div class="section-content">
                   <!-- אם אין פריטים חסרים -->
                   <?php if (empty($lowStockItems)): ?>
                       <div style="text-align: center; color: #28a745; padding: 20px;">
                           <i class="fas fa-check-circle" style="font-size: 2em; margin-bottom: 10px;"></i><br>
                           כל הפריטים הם מעל הכמות המנימלית לפריט!
                       </div>

                   <!-- אם יש פריטים עם חוסר -->
                   <?php else: ?>
                       <div class="alert">
                           <i class="fas fa-exclamation-triangle alert-icon"></i>
                           יש <?php echo count($lowStockItems); ?> פריטים שהמלאי שלהם נמוך מהמינימום הנדרש
                       </div>
                       <div class="scrollable-content">
                           <!-- רשימת כל פריט חסר -->
                           <?php foreach ($lowStockItems as $index => $item): ?>
                               <div class="list-item">
                                   <div style="display: flex; align-items: center; gap: 10px;">
                                       <!-- מונה שורה – עיגול ממוספר -->
                                       <span style="background: #dc3545; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.9em;">
                                           <?php echo $index + 1; ?>
                                       </span>
                                       <div class="item-info">
                                           <div class="item-title"><?php echo htmlspecialchars($item['name']); ?></div>
                                           <div class="item-details">
                                               במלאי: <?php echo number_format($item['current_stock']); ?> <?php echo htmlspecialchars($item['unit_type']); ?><br>
                                               נדרש מינימום: <?php echo number_format($item['minimum_required']); ?> <?php echo htmlspecialchars($item['unit_type']); ?>
                                           </div>
                                       </div>
                                   </div>
                                   <span class="status-badge status-low">חסר במלאי</span>
                               </div>
                           <?php endforeach; ?>
                       </div>
                   <?php endif; ?>
               </div>
            </div>

        </div>
    </div>

    <script>
        // רענון אוטומטי של הדף כל 5 דקות
        setTimeout(function() {
            location.reload();
        }, 300000);
    </script>
</body>
</html>