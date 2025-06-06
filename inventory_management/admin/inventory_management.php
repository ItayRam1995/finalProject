<?php include '../../header.php'; ?>
<?php
// דף ניהול מלאי לפנסיון כלבים - גישה למנהלים בלבד




// התחברות למסד הנתונים
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// בדיקת חיבור למסד הנתונים
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// חישוב תאריכים רלוונטיים
$currentDate = date('Y-m-d');
$currentMonthStart = date('Y-m-01');
$currentMonthEnd = date('Y-m-t');
$nextMonthStart = date('Y-m-01', strtotime('+1 month'));
$nextMonthEnd = date('Y-m-t', strtotime('+1 month'));

// שליפת מספר ההזמנות הפעילות לחודש הנוכחי
$activeReservationsCurrentMonth = $conn->query("
    SELECT COUNT(*) as count 
    FROM reservation 
    WHERE status != 'deleted' 
    AND ((start_date BETWEEN '$currentMonthStart' AND '$currentMonthEnd') 
         OR (end_date BETWEEN '$currentMonthStart' AND '$currentMonthEnd') 
         OR (start_date <= '$currentMonthStart' AND end_date >= '$currentMonthEnd'))
    AND end_date >= '$currentDate'
")->fetch_assoc()['count'];

// שליפת מספר ההזמנות הפעילות לחודש הבא
$activeReservationsNextMonth = $conn->query("
    SELECT COUNT(*) as count 
    FROM reservation 
    WHERE status != 'deleted' 
    AND ((start_date BETWEEN '$nextMonthStart' AND '$nextMonthEnd') 
         OR (end_date BETWEEN '$nextMonthStart' AND '$nextMonthEnd') 
         OR (start_date <= '$nextMonthStart' AND end_date >= '$nextMonthEnd'))
    AND end_date >= '$currentDate'
")->fetch_assoc()['count'];

// שליפת כמות הימים הכוללת של שהייה בחודש הנוכחי
$totalDaysCurrentMonth = $conn->query("
    SELECT SUM(
        CASE
            WHEN start_date <= '$currentMonthStart' AND end_date >= '$currentMonthEnd' 
                THEN DATEDIFF('$currentMonthEnd', '$currentMonthStart') + 1
            WHEN start_date <= '$currentMonthStart' AND end_date <= '$currentMonthEnd' AND end_date >= '$currentMonthStart' 
                THEN DATEDIFF(end_date, '$currentMonthStart') + 1
            WHEN start_date >= '$currentMonthStart' AND start_date <= '$currentMonthEnd' AND end_date >= '$currentMonthEnd' 
                THEN DATEDIFF('$currentMonthEnd', start_date) + 1
            WHEN start_date >= '$currentMonthStart' AND end_date <= '$currentMonthEnd' 
                THEN DATEDIFF(end_date, start_date) + 1
            ELSE 0
        END
    ) as total_days
    FROM reservation
    WHERE status != 'deleted'
    AND ((start_date BETWEEN '$currentMonthStart' AND '$currentMonthEnd') 
         OR (end_date BETWEEN '$currentMonthStart' AND '$currentMonthEnd') 
         OR (start_date <= '$currentMonthStart' AND end_date >= '$currentMonthEnd'))
    AND end_date >= '$currentDate'
")->fetch_assoc()['total_days'];

// שליפת כמות הימים הכוללת של שהייה בחודש הבא
$totalDaysNextMonth = $conn->query("
    SELECT SUM(
        CASE
            WHEN start_date <= '$nextMonthStart' AND end_date >= '$nextMonthEnd' 
                THEN DATEDIFF('$nextMonthEnd', '$nextMonthStart') + 1
            WHEN start_date <= '$nextMonthStart' AND end_date <= '$nextMonthEnd' AND end_date >= '$nextMonthStart' 
                THEN DATEDIFF(end_date, '$nextMonthStart') + 1
            WHEN start_date >= '$nextMonthStart' AND start_date <= '$nextMonthEnd' AND end_date >= '$nextMonthEnd' 
                THEN DATEDIFF('$nextMonthEnd', start_date) + 1
            WHEN start_date >= '$nextMonthStart' AND end_date <= '$nextMonthEnd' 
                THEN DATEDIFF(end_date, start_date) + 1
            ELSE 0
        END
    ) as total_days
    FROM reservation
    WHERE status != 'deleted'
    AND ((start_date BETWEEN '$nextMonthStart' AND '$nextMonthEnd') 
         OR (end_date BETWEEN '$nextMonthStart' AND '$nextMonthEnd') 
         OR (start_date <= '$nextMonthStart' AND end_date >= '$nextMonthEnd'))
    AND end_date >= '$currentDate'
")->fetch_assoc()['total_days'];

if ($totalDaysCurrentMonth === null) $totalDaysCurrentMonth = 0;
if ($totalDaysNextMonth === null) $totalDaysNextMonth = 0;

// פונקציה להמרת חודש למספר לשם חודש בעברית
function getHebrewMonth($monthNum) {
    $hebrewMonths = [
        1 => 'ינואר',
        2 => 'פברואר',
        3 => 'מרץ',
        4 => 'אפריל',
        5 => 'מאי',
        6 => 'יוני',
        7 => 'יולי',
        8 => 'אוגוסט',
        9 => 'ספטמבר',
        10 => 'אוקטובר',
        11 => 'נובמבר',
        12 => 'דצמבר'
    ];
    
    return $hebrewMonths[$monthNum];
}

// חישוב פרטי החודשים
$currentMonthName = getHebrewMonth(date('n'));
$currentYear = date('Y');
$nextMonthName = getHebrewMonth(date('n', strtotime('+1 month')));
$nextYear = date('Y', strtotime('+1 month'));


// נקה כפילויות קיימות לפני הבדיקה
$conn->query("
    DELETE t1 FROM inventory t1
    INNER JOIN inventory t2 
    WHERE 
        t1.id > t2.id 
        AND t1.name = t2.name
");


// בדיקה אם הטופס נשלח
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $redirectNeeded = false;
    
    // עדכון כמות מלאי
    if (isset($_POST['update_inventory'])) {
        $id = $_POST['item_id'];
        $current_stock = $_POST['current_stock'];
        $min_required = $_POST['min_required'];
        
        $updateQuery = "UPDATE inventory SET current_stock = ?, minimum_required = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("iii", $current_stock, $min_required, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "המלאי עודכן בהצלחה";
            $redirectNeeded = true;
        } else {
            $_SESSION['error_message'] = "שגיאה בעדכון המלאי: " . $conn->error;
            $redirectNeeded = true;
        }
        
        $stmt->close();
    }
    
    // הוספת פריט חדש
    if (isset($_POST['add_inventory'])) {
        $name = $_POST['name'];
        $category = $_POST['category'];
        $description = $_POST['description'];
        $current_stock = $_POST['current_stock'];
        $min_required = $_POST['min_required'];
        $unit_type = $_POST['unit_type'];
        $unit_per_dog_per_day = $_POST['unit_per_dog_per_day'];
        $price = $_POST['price'];
        $purchase_url = $_POST['purchase_url'];
        
        // טיפול בתמונה
        $image_url = 'images/inventory/default.jpg'; // תמונת ברירת מחדל
        
        // בודק אם המשתמש הכניס תמונה ולא הייתה בעיה בהעלת הקובץ
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = 'images/inventory/';
            
            // וודא שהתיקייה קיימת
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            
            // העלאת התמונה
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_url = $target_file;
            }
        }
        
        // בדיקה אם שם הפריט כבר קיים במערכת
        $checkQuery = "SELECT COUNT(*) as count FROM inventory WHERE name = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("s", $name);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $exists = $result->fetch_assoc()['count'] > 0;
        $checkStmt->close();
        
        if ($exists) {
            $_SESSION['error_message'] = "פריט בשם זה כבר קיים במערכת";
            $redirectNeeded = true;
        } else {
            $insertQuery = "INSERT INTO inventory 
                            (name, category, description, current_stock, minimum_required, unit_type, unit_per_dog_per_day, price, image_url, purchase_url) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("sssidsddss", 
                $name, $category, $description, $current_stock, $min_required, $unit_type, $unit_per_dog_per_day, $price, $image_url, $purchase_url
            );
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "הפריט נוסף בהצלחה";
                $redirectNeeded = true;
            } else {
                $_SESSION['error_message'] = "שגיאה בהוספת הפריט: " . $conn->error;
                $redirectNeeded = true;
            }
            
            $stmt->close();
        }
    }
    
    // מחיקת פריט
    if (isset($_POST['delete_inventory'])) {
        $id = $_POST['item_id'];
        
        $deleteQuery = "DELETE FROM inventory WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "הפריט נמחק בהצלחה";
            $redirectNeeded = true;
        } else {
            $_SESSION['error_message'] = "שגיאה במחיקת הפריט: " . $conn->error;
            $redirectNeeded = true;
        }
        
        $stmt->close();
    }
    
    // ביצוע הפניה אם נדרש כדי למנוע שליחה כפולה בעת ריענון
    if ($redirectNeeded) {
        header("Location: inventory_management.php");
        // exit;
    
    }
}

// בדיקה להצגת הודעות הצלחה/שגיאה מהסשן
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $errorMessage = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// שליפת נתוני המלאי - עם בדיקות נוספות למניעת כפילויות
// שאילתה ששולפת את כל פרטי המלאי מהטבלה inventory
$inventoryQuery = "SELECT id, name, category, description, current_stock, minimum_required, unit_type, unit_per_dog_per_day, price, image_url, purchase_url, created_at FROM inventory ORDER BY id DESC, category, name";

// מערך שורות
$inventoryResult = $conn->query($inventoryQuery);




// יצירת מערך קטגוריות למלאי
$categories = [];
// רשימת פריטים ללא כפילויות - מערך של מילונים. כל איבר במערך הוא מילון
$inventoryItems = [];

//  אם הבקשה הצליחה ויש לפחות שורה אחת
if ($inventoryResult && $inventoryResult->num_rows > 0) {
    // משתנה עזר לשמירת מזהים שכבר טופלו – כדי למנוע כפילויות
    $processedIds = [];
    
    // עוברים שורה שורה על תוצאות השאילתה. כל שורה היא פריט.
    while ($row = $inventoryResult->fetch_assoc()) {
        // בדיקה שהמזהה לא עובד כבר
        if (!in_array($row['id'], $processedIds)) {
            $inventoryItems[] = $row;
            $processedIds[] = $row['id'];
            
            // הוספת הקטגוריה לרשימת הקטגוריות אם היא חדשה
            if (!in_array($row['category'], $categories)) {
                $categories[] = $row['category'];
            }
        }
    }
    
}



// יצירת קטגוריות ברירת מחדל   
$defaultCategories = ['מזון', 'טיפוח', 'ניקיון', 'צעצועים', 'ציוד', 'בריאות', 'חטיפים', 'ביגוד', 'מיטות', 'כלי אוכל'];
foreach ($defaultCategories as $cat) {
    if (!in_array($cat, $categories)) {
        $categories[] = $cat;
    }
}

// ספירת פריטים בחוסר
$lowStockCount = 0;

// חישוב כמויות מלאי נדרשות לפי הזמנות פעילות ועדכון סטטוס המלאי
// הפניה למערך המקורי של הפריטים
//  אם נשנה ערך בתוך $item, הוא ישתנה גם בתוך המערך המקורי.
foreach ($inventoryItems as &$item) {
    // חישוב הכמות הנדרשת עבור החודש הנוכחי
    $requiredCurrentMonth = $item['unit_per_dog_per_day'] * $totalDaysCurrentMonth;
    
    // חישוב הכמות הנדרשת עבור החודש הבא
    $requiredNextMonth = $item['unit_per_dog_per_day'] * $totalDaysNextMonth;
    
    // חישוב סה"כ כמות נדרשת
    $totalRequired = $requiredCurrentMonth + $requiredNextMonth;
    
    // מוסיף מפתחות חדשים למילון ושם בהם ערכים
    $item['required_current_month'] = round($requiredCurrentMonth, 1);
    $item['required_next_month'] = round($requiredNextMonth, 1);
    $item['total_required'] = round($totalRequired, 1);
    
    // קביעת סטטוס המלאי

    // אם המלאי מתחת לרמת המינימום
    if ($item['current_stock'] < $item['minimum_required']) {
        $item['stock_level'] = 'critical';
        $item['status_text'] = 'חסר קריטי';
        $lowStockCount++;

//   אם המלאי הקיים גדול ממלאי המינימום הנדרש, כלומר נשארו עוד כמה ימים להתארגן על מלאי נוסף
    } elseif ($item['current_stock'] < $totalRequired) {
        $item['stock_level'] = 'low';
        $item['status_text'] = 'חסר';
        $lowStockCount++;

    // אם יש מספיק מלאי לחודשיים הקרובים
    } else {
        $item['stock_level'] = 'ok';
        $item['status_text'] = 'תקין';
    }
    
    // חישוב אחוז רמת המלאי
    // אחוז כיסוי המלאי של פריט מסוים ביחס לצורך שלו, תוך כדי מניעה מלעבור את ה־100%
    $stockPercentage = ($item['current_stock'] / max($totalRequired, $item['minimum_required'])) * 100;
    $item['stock_percentage'] = min($stockPercentage, 100);
}

//  עלול לשנות את הפריט האחרון בטעות בשלב מאוחר יותר בקוד, לכן עושים לו ניתוק להפניה
unset($item);
// סגירת החיבור למסד הנתונים
$conn->close();
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">

    <!-- לתצוגה מותאמת למכשירים ניידים -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ניהול מלאי - פנסיון כלבים</title>

     <!-- הגדרת שפה עברית וכיוון RTL (ימין לשמאל) -->
    <!-- Bootstrap RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css">

    <!-- לאיקונים -->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* הגדרת משתני צבע כלליים (לשימוש חוזר בכל הדף) */
        /* משתנים גלובליים שניתן להשתמש בהם בכל מקום בקובץ  */
        :root {
            --primary-color: #4e73df;
            --secondary-color: #6c757d;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --info-color: #36b9cc;
            --dark-color: #2e3f50;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* כותרת דשבורד עם גרדיאנט, קצה מעוגל והצללה  */
        .dashboard-header {
            background: linear-gradient(135deg, #13547a 0%, #80d0c7 100%);
            color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        /* כרטיסי סטטיסטיקה עם אפקט ריחוף ומסגרת */
        .stats-card {
            border-radius: 0.5rem;
            padding: 1.2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
            height: 100%;
            border-top: 5px solid transparent;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-card.reservations {
            border-top-color: #4e73df;
        }
        
        .stats-card.inventory {
            border-top-color: #1cc88a;
        }
        
        .stats-card.alerts {
            border-top-color: #e74a3b;
        }
        
        .stats-card.monthly {
            border-top-color: #f6c23e;
        }
        
        /* עיצוב כרטיסיות ניווט עליון בדף – כמו כפתורי קטגוריות או סינון */
        .nav-tabs .nav-link {
            border: none;
            color: #495057;
            font-weight: 600;
            padding: 1rem 1.5rem;
            margin-right: 0.5rem;
            border-radius: 0.5rem 0.5rem 0 0;
            transition: all 0.2s;
        }
        
        .nav-tabs .nav-link.active {
            background-color: #fff;
            color: #4e73df;
            border-bottom: 3px solid #4e73df;
        }
        
        /* כרטיס פריט מלאי עם צל ותמונה */
        .inventory-card {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            height: 100%;
            position: relative;
        }
        
        .inventory-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .inventory-img-container {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .inventory-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .inventory-card:hover .inventory-img-container img {
            transform: scale(1.05);
        }
        
        /* חיווי בצבעים (ירוק, כתום, אדום) למצב המלאי */
        .inventory-status {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 0.3rem 0.7rem;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.8rem;
            z-index: 2;
        }
        
        .status-ok {
            background-color: rgba(28, 200, 138, 0.9);
            color: white;
        }
        
        .status-low {
            background-color: rgba(246, 194, 62, 0.9);
            color: white;
        }
        
        .status-critical {
            background-color: rgba(231, 74, 59, 0.9);
            color: white;
        }
        
        /* ריווח פנימי בתוך גוף כרטיס המלאי (מתחת לתמונה) */
        .inventory-body {
            padding: 1.5rem;
        }
        
        .inventory-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2e3f50;
            font-size: 1.15rem;
            height: 2.5rem;
            overflow: hidden;
            display: -webkit-box;
            /* משמש להצגת שם הפריט, במגבלת שורות למניעת גלישה */
            -webkit-line-clamp: 2; 
            -webkit-box-orient: vertical;
        }
        
        /* תגית קטגוריה */
        .inventory-category {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            background-color: #e3f2fd;
            color: #1976d2;
            border-radius: 15px;
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }
        
        /* תיאור קצר של הפריט עם הגבלת גובה */
        .inventory-description {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            height: 4rem;
            overflow: hidden;
            display: -webkit-box;
            /* מגביל ל-3 שורות */
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        
        /* משמש לקו סטטוס מלאי שיוצר חיווי ויזואלי לפי אחוז המלאי */
        .inventory-stock {
            display: flex;
            align-items: center;
            margin-bottom: 0.8rem;
        }
        
        /* ציר אופקי אפור שמראה את אחוז כיסוי המלאי */
        .stock-bar {
            flex-grow: 1;
            background-color: #e9ecef;
            border-radius: 20px;
            height: 10px;
            margin: 0 10px;
            overflow: hidden;
        }
        
        /* מייצג את רמת המלאי בפועל */
        .stock-level {
            height: 100%;
            border-radius: 20px;
            transition: width 0.3s;
        }
        
        /* ירוק = מלאי תקין */
        .stock-ok {
            background-color: #1cc88a;
        }
        
        /* כתום = מלאי נמוך */
        .stock-low {
            background-color: #f6c23e;
        }
        
        /* אדום = מלאי קריטי / דחוף להזמנה */
        .stock-critical {
            background-color: #e74a3b;
        }
        
        /* משמש להצגת צריכה צפויה ביחס למלאי */
        .stock-needed {
            background-color: #4e73df;
            opacity: 0.5;
        }
        
        /* עיצוב כפתור הזמן עכשיו" */
        .btn-order {
            display: block;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.3s;
        }
        
        /* כשמעבירים עכבר מעל – הכפתור טיפה מתנפח */
        .btn-order:hover {
            transform: scale(1.05);
        }
        
        /* עיצוב שורת חיפוש עם איקון בפנים */
        .search-bar {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        /*להחיל את העיצוב רק על שדות קלט בתוך אזור החיפוש, ולא על כל .form-control*/
        .search-bar .form-control {
            padding-right: 50px;
            height: 50px;
            border-radius: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: none;
        }
        
        .search-bar .search-icon {
            /* ממקם את האייקון ליד שורת החיפוש */
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        /* הכפתורים בתחתית צריכים להיות מימין לשמאל */
        .modal-footer.modal-footer-rtl {
            flex-direction: row-reverse;
        }
        
        /* כפתור עגול צף בפינה */
        .btn-floating {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
        
        .btn-floating:hover {
            transform: scale(1.1);
        }
        
        /* מוסיף כוכבית אדומה לשדות חובה בטופס */
        .form-label.required::after {
            content: " *";
            color: #e74a3b;
        }
        
        /* עיצוב לפלוס/מינוס מסביב לאינפוט כמות */
        .quantity-control {
            display: flex;
            align-items: center;
        }
        
        .quantity-control .btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            padding: 0;
        }
        
        .quantity-control input {
            width: 60px;
            text-align: center;
            border: none;
            font-weight: 600;
        }
        
        /* תגית צבעונית קטנה שמציגה צריכה צפויה לחודש */
        .required-estimate {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }
        
        .alert-success {
            animation: fadeOut 5s forwards;
        }
        
        /*אנימציה הדרגתית שבה האלמנט נשאר שקוף חלקית למשך רוב הזמן, ואז נעלם בסוף*/
        @keyframes fadeOut {
            0% { opacity: 1; }
            90% { opacity: 1; }
            100% { opacity: 0; }
        }
        
        @media (max-width: 768px) {
            .stats-card {
                margin-bottom: 1rem;
            }
            
            .nav-tabs .nav-link {
                padding: 0.7rem 1rem;
                font-size: 0.9rem;
            }
            
            .inventory-img-container {
                height: 160px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- כותרת ראשית -->
        <div class="dashboard-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="fs-2 fw-bold"><i class="fas fa-box me-2"></i> ניהול מלאי - פנסיון כלבים</h1>
                    <p class="mb-0">ניהול המלאי של פריטים הנדרשים לאחזקת הכלבים בפנסיון</p>
                </div>
                <div class="col-md-4 text-md-start text-center mt-3 mt-md-0">
                    <button class="btn btn-light mb-2 mb-md-0 me-md-2" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
                        <i class="fas fa-plus-circle me-1"></i> הוסף פריט חדש
                    </button>
                </div>
            </div>
        </div>
        

        <!-- אם קיימת הודעת הצלחה, מציג הודעה ירוקה עם אייקון וי וכפתור סגירה -->
        <?php if (isset($successMessage)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?php echo $successMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <!-- אם קיימת הודעת שגיאה, מציג הודעה אדומה עם אייקון שגיאה וכפתור סגירה -->
        <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?php echo $errorMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <!-- קלפי אינפורמציה -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="stats-card bg-white reservations">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted">הזמנות פעילות</h5>
                            <h2 class="fw-bold"><?php echo $activeReservationsCurrentMonth; ?></h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-check text-primary opacity-50 fa-3x"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small">
                        <span>לחודש <?php echo $currentMonthName . ' ' . $currentYear; ?></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="stats-card bg-white monthly">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted">הזמנות לחודש הבא</h5>
                            <h2 class="fw-bold"><?php echo $activeReservationsNextMonth; ?></h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-alt text-warning opacity-50 fa-3x"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small">
                        <span>לחודש <?php echo $nextMonthName . ' ' . $nextYear; ?></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="stats-card bg-white inventory">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted">סה"כ פריטי מלאי</h5>
                            <h2 class="fw-bold"><?php echo count($inventoryItems); ?></h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-boxes text-success opacity-50 fa-3x"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small">
                        <span>ב-<?php echo count($categories); ?> קטגוריות</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card bg-white alerts">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted">פריטים בחוסר</h5>
                            <h2 class="fw-bold"><?php echo $lowStockCount; ?></h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle text-danger opacity-50 fa-3x"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small">
                        <button class="btn btn-sm btn-outline-danger" id="showLowStock">הצג פריטים בחוסר</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- החיפוש והסינון -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="search-bar">
                    <input type="text" class="form-control" id="searchInventory" placeholder="חיפוש פריטים...">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </div>

            <div class="col-md-4">
            <!-- תיבת בחירה -->
                <select class="form-select form-select-lg" id="categoryFilter">
                    <option value="all">כל הקטגוריות</option>

                    <!-- לולאה שמוסיפה <option> לכל קטגוריה שנשמרה במערך $categories מהשרת. -->
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>">
                            <?php echo htmlspecialchars($category); ?>
                        </option>
                    <?php endforeach; ?>
                    <!-- אפשרות ייחודית לסינון פריטים במלאי נמוך או קריטי -->
                    <option value="low-stock">פריטים בחוסר</option>
                </select>
            </div>
        </div>
        
        <!-- צפי צריכה -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i> צפי צריכת מלאי לחודשים הקרובים</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0"><?php echo $currentMonthName . ' ' . $currentYear; ?></h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>מספר הזמנות פעילות</span>
                                        <span class="badge bg-primary rounded-pill"><?php echo $activeReservationsCurrentMonth; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>סה"כ ימי שהייה</span>
                                        <span class="badge bg-primary rounded-pill"><?php echo $totalDaysCurrentMonth; ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0"><?php echo $nextMonthName . ' ' . $nextYear; ?></h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>מספר הזמנות פעילות</span>
                                        <span class="badge bg-warning rounded-pill"><?php echo $activeReservationsNextMonth; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>סה"כ ימי שהייה</span>
                                        <span class="badge bg-warning rounded-pill"><?php echo $totalDaysNextMonth; ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- כרטיסיות הפריטים -->

        <div class="row" id="inventoryItems">
            <?php 
            // מעקב אחרי פריטים שכבר הוצגו
            $displayedItems = [];
            
            $count = 0;
            
            // המשתנה $inventoryItems הוא מערך של מילונים
            foreach ($inventoryItems as $item): 
                // בדיקה שהפריט לא הוצג כבר
                if (in_array($item['id'], $displayedItems)) {
                    continue; // דילוג על פריטים שכבר הוצגו
                }
                
                // הוספת המזהה לרשימת הפריטים שהוצגו
                $displayedItems[] = $item['id'];
                
                // קביעת סטטוס המלאי
                $statusClass = 'status-ok';
                $stockLevelClass = 'stock-ok';
                
                if ($item['stock_level'] == 'critical') {
                    $statusClass = 'status-critical';
                    $stockLevelClass = 'stock-critical';
                } elseif ($item['stock_level'] == 'low') {
                    $statusClass = 'status-low';
                    $stockLevelClass = 'stock-low';
                }

            ?>
            <!-- מעטפת כללית לפריט -->
            <div class="col-lg-4 col-md-6 mb-4 inventory-item" 
                 data-item-id="<?php echo $item['id']; ?>"
                 data-category="<?php echo htmlspecialchars($item['category']); ?>"
                 data-stock-level="<?php echo $item['stock_level']; ?>"
                 data-name="<?php echo htmlspecialchars($item['name']); ?>">

                 <!-- אזור תמונה + סטטוס -->
                <div class="inventory-card">
                    <div class="inventory-img-container">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" onerror="this.src='images/inventory/default.jpg'">
                        <!-- /* חיווי בצבעים (ירוק, כתום, אדום) למצב המלאי */ -->
                        <!-- כאן אנחנו מדפיסים את המחלקה StatusClass למשל status-low ואז הוא יודע איזה צבע לקחת -->
                        <div class="inventory-status <?php echo $statusClass; ?>">
                            <?php echo $item['status_text']; ?>
                        </div>
                    </div>

                    <!-- קטגוריה + שם + תיאור -->
                    <div class="inventory-body">
                        <div class="inventory-category">
                            <?php echo htmlspecialchars($item['category']); ?>
                        </div>
                        <h5 class="inventory-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                        <p class="inventory-description">
                            <?php echo htmlspecialchars($item['description']); ?>
                        </p>
                        
                        <!-- פסי מלאי וצריכה -->
                        <div class="inventory-stock">
                            <span class="fw-bold">מלאי:</span>
                            <!--   נמצא בתוך .stock-bar שהוא הקונטיינר של הרקע (אפור) -->
                            <div class="stock-bar">
                            <!-- בר מלאי עם צבע לפי סטטוס (ירוק/כתום/אדום) -->
                            <!-- יוצרת בר גרפי אופקי שמייצג את מצב המלאי של פריט, בצבע ובאורך שמשקפים את אחוז הכיסוי של הצורך. -->
                                <div class="stock-level <?php echo $stockLevelClass; ?>" 
                                     style="width: <?php echo $item['stock_percentage']; ?>%"></div>
                            </div>
                            <span><?php echo $item['current_stock']; ?> <?php echo htmlspecialchars($item['unit_type']); ?></span>
                        </div>
                        
                        <!-- צריכה נדרשת -->
                        <div class="inventory-stock">
                            <span class="fw-bold">נדרש:</span>
                            <div class="stock-bar">
                                <div class="stock-level stock-needed" 
                                     style="width: 100%"></div>
                            </div>
                            <span><?php echo $item['total_required']; ?> <?php echo htmlspecialchars($item['unit_type']); ?></span>
                        </div>
                        
                        <!-- תצוגת צריכה לפי חודש: -->
                        <div class="d-flex flex-wrap mt-3">
                            <div class="me-3 mb-2">
                                <span class="required-estimate bg-primary">
                                 עבור חודש <?php echo $currentMonthName; ?>: נדרשות <?php echo $item['required_current_month']; ?> יחידות <?php echo $item['unit_type']; ?>
                                </span>
                            </div>
                            <div class="mb-2">
                                <span class="required-estimate bg-warning">
                                  עבור חודש <?php echo $nextMonthName; ?>: נדרשות <?php echo $item['required_next_month']; ?> יחידות <?php echo $item['unit_type']; ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- כפתורי פעולות: -->
                        <div class="d-flex mt-3">
                            

                        <!-- מורה ל־ Bootstrap שהאלמנט מפעיל מודאל -->
                        <!-- מצביע על המודאל שייפתח. זה חייב להתאים ל־ id של המודאל עצמו -->
                        <!-- מעביר ערכים דינמיים (כמו שם, מלאי נוכחי, יחידות) לתוך המודאל דרך data -->
                            <button class="btn btn-sm btn-primary me-2 flex-grow-1" data-bs-toggle="modal" 
                                    data-bs-target="#updateStockModal" 
                                    data-id="<?php echo $item['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($item['name']); ?>"
                                    data-stock="<?php echo $item['current_stock']; ?>"
                                    data-min="<?php echo $item['minimum_required']; ?>"
                                    data-unit="<?php echo htmlspecialchars($item['unit_type']); ?>">
                                <i class="fas fa-edit me-1"></i> עדכן
                            </button>

                            <?php
                              $orderLink = !empty($item['purchase_url']) ? htmlspecialchars($item['purchase_url']) : 'https://www.animalshop.co.il/';
                            ?>
                            <!-- מופיע רק לפריטים בחוסר ומפנה לקישור הרכישה -->

                            <?php if ($item['stock_level'] != 'ok'): ?>
                            <a href="<?php echo $orderLink; ?>" target="_blank" 
                               class="btn btn-sm btn-success flex-grow-1">
                                <!--אייקון של עגלת קניות מספריית Font Awesome-->
                                <i class="fas fa-shopping-cart me-1"></i> הזמן
                            </a>
                            <?php endif; ?>
                        </div>

                        <!-- שורת תחתית – מידע נוסף -->
                        <div class="d-flex justify-content-between mt-3">
                            <div class="small text-muted">מינימום: <?php echo $item['minimum_required']; ?> <?php echo htmlspecialchars($item['unit_type']); ?></div>
                            <div class="small text-muted">מחיר: ₪<?php echo number_format($item['price'], 2); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- תוכן כאשר אין פריטים -->
        <!-- מזהה שבעזרתו ניתן להציג או להסתיר את הקטע באמצעות JavaScript -->
        <!-- d-none – מסתיר את הקטע כברירת מחדל. -->
        <div id="noItems" class="row d-none">
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h4>לא נמצאו פריטים</h4>
                <p>לא נמצאו פריטים התואמים את החיפוש או הסינון שבחרת</p>
                <button class="btn btn-primary mt-3" id="resetFilters">
                    <i class="fas fa-redo me-1"></i> אפס סינון
                </button>
            </div>
        </div>
        
        <!-- כפתור צף להוספת פריט -->
        <button class="btn btn-primary btn-floating" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
            <i class="fas fa-plus"></i>
        </button>
    </div>
    
    <!-- מודאל עדכון כמות מלאי -->
     <!-- חלון קופץ modal fade -->
    <div class="modal fade" id="updateStockModal" tabindex="-1" aria-hidden="true">
    <!-- מגדיר את התיבה המרכזית של המודאל (החלון עצמו) -->
        <div class="modal-dialog">
        <!-- עוטף את כל התוכן של המודאל - בלעדיו העיצוב והעימוד לא ייראו נכון: כותרת, גוף, כפתורים -->
            <div class="modal-content">
            <!-- מגדיר את אזור הכותרת של המודאל -->
                <div class="modal-header">
                    <h5 class="modal-title">עדכון כמות במלאי</h5>
                    <!-- כפתור סגירה קטן  -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- טופס קלט – מאפשר למשתמש להזין ולשלוח מידע לשרת -->
                <!-- הטופס נשלח לדף הנוכחי  -->
                <form method="post" action="">
                    <div class="modal-body">
                    <!-- שדה שלא נראה למשתמש, אך כן נשלח לשרת בעת שליחת הטופס -->
                        <input type="hidden" name="item_id" id="updateItemId">
                        
                        <div class="mb-3">
                            <label class="form-label">שם הפריט</label>
                            <!-- שם פריט נעול לעריכה -->
                            <input type="text" class="form-control" id="updateItemName" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">כמות נוכחית במלאי</label>
                            <!-- עטיפה לקבוצה של שדות קלט וכפתורים – כדי שיראו כקבוצה אחת מעוצבת אופקית -->
                            <div class="input-group">
                            <!-- כפתור בצבע אפור בהיר שמקטין את הכמות בשדה -->
                                <button type="button" class="btn btn-outline-secondary" id="decreaseStock">-</button>
                                <!-- שדה חובה שלא ניתן להכניס לו ערכים שליליים -->
                                <input type="number" class="form-control text-center" name="current_stock" id="updateItemStock" required min="0">
                                <!-- כפתור שמגדיל את הערך בשדה הקלט -->
                                <button type="button" class="btn btn-outline-secondary" id="increaseStock">+</button>
                                <span class="input-group-text" id="updateItemUnit"></span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">כמות מינימום נדרשת</label>
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary" id="decreaseMin">-</button>
                                <input type="number" class="form-control text-center" name="min_required" id="updateItemMin" required min="0">
                                <button type="button" class="btn btn-outline-secondary" id="increaseMin">+</button>
                                <span class="input-group-text" id="updateItemUnitMin"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer modal-footer-rtl">
                        <!-- לסגור את המודאל באופן אוטומטי בעת לחיצה על הכפתור ללא שימוש ב JAVASCRIPT -->
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ביטול</button>
                        <button type="submit" name="update_inventory" class="btn btn-primary">עדכן מלאי</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- מודאל הוספת פריט חדש -->
     <!-- מאפשר למשתמש לסגור את המודאל בלחיצה על Esc -->
    <div class="modal fade" id="addInventoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">הוספת פריט מלאי חדש</h5>
                    <!-- כפתור סגירה קטן  -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- מאפשר לדפדפן לשלוח קבצים + שדות טקסט יחד - חובה כשיש בטופס העלאת קבצים -->
                <form method="post" action="" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">שם הפריט</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label required">קטגוריה</label>
                                    <select class="form-select" name="category" id="categorySelect" required>
                                    <!-- ערך ריק, שמוצג כברירת מחדל. לא יעמוד בתנאי required -->
                                        <option value="">בחר קטגוריה...</option>
                                        <option value="מזון">מזון</option>
                                        <option value="טיפוח">טיפוח</option>
                                        <option value="ניקיון">ניקיון</option>
                                        <option value="צעצועים">צעצועים</option>
                                        <option value="ציוד">ציוד</option>
                                        <option value="בריאות">בריאות</option>
                                        <option value="חטיפים">חטיפים</option>
                                        <option value="ביגוד">ביגוד</option>
                                        <option value="מיטות">מיטות</option>
                                        <option value="כלי אוכל">כלי אוכל</option>
                                    </select>
                                </div>
                                                   
                                <div class="mb-3">
                                    <label class="form-label">תיאור הפריט</label>
                                    <textarea class="form-control" name="description" rows="3"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label required">סוג יחידה</label>
                                    <!-- <input type="text" class="form-control" name="unit_type" required placeholder="לדוגמה: שקים, בקבוקים, יחידות"> -->
                                    <select class="form-select" name="unit_type" required>
                                    <!-- ערך ריק, שמוצג כברירת מחדל. לא יעמוד בתנאי required -->
                                        <option value="">בחר גודל יחידת אירוז</option>
                                        <option value="בודד">בודד</option>
                                        <option value="בקבוק">בקבוק</option>
                                        <option value="קרטון">קרטון</option>
                                        <option value="שק">שק</option>
                                        <option value="קילוגרם">קילוגרם</option>
                                        <option value="גרם">גרם</option>
                                        <option value="מיליליטר">מ"ל</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">כמות נוכחית במלאי</label>
                                    <input type="number" class="form-control" name="current_stock" required min="0" value="0">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label required">כמות מינימום נדרשת</label>
                                    <input type="number" class="form-control" name="min_required" required min="0" value="10">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label required">כמות ליום לכלב</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="unit_per_dog_per_day" required min="0.1" step="0.1" value="0.1">
                                        <span class="input-group-text">יחידות לכלב ליום</span>
                                    </div>
                                    <div class="form-text">כמה יחידות מהפריט נצרכות עבור כלב אחד ביום אחד</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label required">מחיר ליחידה</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="price" required min="0" step="1.00" value="0">
                                        <span class="input-group-text">₪</span>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">תמונה</label>
                                    <input type="file" class="form-control" name="image" accept="image/*">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">קישור לרכישה</label>
                                    <input type="url" class="form-control" name="purchase_url" placeholder="https://www.animalshop.co.il/">
                                    <div class="form-text">קישור לאתר חיצוני לרכישת המוצר</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer modal-footer-rtl">
                        <!-- לסגור את המודאל באופן אוטומטי בעת לחיצה על הכפתור ללא שימוש ב JAVASCRIPT -->
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ביטול</button>
                        <button type="submit" name="add_inventory" class="btn btn-primary">הוסף פריט</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- מודאל מחיקת פריט -->
    <div class="modal fade" id="deleteItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">מחיקת פריט</h5>
                    <!-- כפתור סגירה קטן  -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="" name="delete_inventory_form">
                    <div class="modal-body">
                        <input type="hidden" name="item_id" id="deleteItemId">
                        <p>האם אתה בטוח שברצונך למחוק את הפריט "<span id="deleteItemName"></span>"?</p>
                        <p class="text-danger">פעולה זו אינה ניתנת לביטול.</p>
                    </div>
                    <div class="modal-footer modal-footer-rtl">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ביטול</button>
                        <!-- name="delete_inventory" משמש ב־ PHP כדי לזהות שזו בקשת מחיקה.-->
                        <button type="submit" name="delete_inventory" class="btn btn-danger">מחק פריט</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- מודאל לצפייה בפריט -->
    <div class="modal fade" id="viewItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewItemTitle"></h5>
                    <!-- כפתור סגירה קטן  -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img id="viewItemImage" src="" alt="" class="img-fluid rounded mb-3">
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <span class="badge bg-info mb-2" id="viewItemCategory"></span>
                            </div>
                            <p id="viewItemDescription" class="mb-4"></p>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <h6>כמות במלאי</h6>
                                    <h4 id="viewItemStock"></h4>
                                </div>
                                <div class="col-6">
                                    <h6>כמות מינימום</h6>
                                    <h4 id="viewItemMin"></h4>
                                </div>
                            </div>
                            
                            <h6>צריכה צפויה</h6>
                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2 bg-primary" style="width: 15px; height: 15px; border-radius: 50%;"></div>
                                        <!-- מספר החודש, נקודתויים, רווחים +  ולבסוף הטקסט המחולץ שהינו : כמות הצריכה + "יחידות" + יחידת המידה -->
                                        <span id="viewItemCurrentMonth"></span>:&nbsp&nbsp&nbsp <span id="viewItemRequiredCurrent"></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2 bg-warning" style="width: 15px; height: 15px; border-radius: 50%;"></div>
                                        <span id="viewItemNextMonth"></span>:&nbsp&nbsp&nbsp <span id="viewItemRequiredNext"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex">
                                 <!-- כשלוחצים על כרטיס מלאי , המודאל של צפייה בפריט מתעדכן עם נתונים , ובפרט ההכפתורים של המודאל מתמלאים גם בנתונים -->
                                 <!-- הכפתור "עדכן מלאי" מקבל את הנתונים ספציפית מ JQUREY לפי המזהה viewItemUpdate -->
                                 <!-- viewItemUpdate הכפתור מקבל את הנתונים מ  -->
                                <button class="btn btn-primary me-2" id="viewItemUpdate">
                                    <i class="fas fa-edit me-1"></i> עדכן מלאי
                                </button>

                                 <!-- הכפתור "הזמן עכשיו" מקבל את הנתונים ספציפית מ JQUREY לפי המזהה viewItemOrder -->
                                 <!-- viewItemOrder הכפתור מקבל את הנתונים מ  -->
                                 <!-- רק אחרי שהוא מקבל את הנתונים הוא קופץ לקישור -->
                                <a href="#" class="btn btn-success me-2" id="viewItemOrder" target="_blank">
                                    <i class="fas fa-shopping-cart me-1"></i> הזמן עכשיו
                                </a>

                                  <!-- הכפתור "מחק" מקבל את הנתונים ספציפית מ JQUREY לפי המזהה viewItemDelete -->
                                 <!-- viewItemDelete הכפתור מקבל את הנתונים מ  -->
                                <button class="btn btn-outline-danger" id="viewItemDelete">
                                    <i class="fas fa-trash-alt me-1"></i> מחק
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap & jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // דואג שכל הקוד בפנים ירוץ רק לאחר שה־ HTML נטען במלואו לדפדפן
        $(document).ready(function() {



            // טיפול בלחיצה על כרטיס מלאי - טעינת נתונים למודאל צפייה בפריט
            // שואב מכרטיס הפריט בדף את כל הנתונים מכל האלמנטים שבתוך הכרטיס ושותל אותם במודאל הצפייה בפריט  
            // הגדרת מודאל צפייה בפריט
            // מאזין ללחיצה על כל אלמנט עם class של inventory-card (כל כרטיס מלאי)
            // ברגע שהמשתמש לוחץ על כרטיס מלאי במסך, הוא מועבר לכאן. כאן מתמלאים כל המשתנים לפי המזהים.
            // לאחר מכן מודאל צפייה בפריט מתמלא עם כל המשתנים שכאן בהתאם למשתני המזהים.
            // בנוסף, גם הכפתורים בתוך מודאל הצפייה בפריט מתמלאים עם משתני המזהים

            $('.inventory-card').click(function(e) {
                // מונע פעולה אם הלחיצה הייתה על כפתור או קישור בתוך הכרטיס
                if (!$(e.target).closest('button, a').length) {
                    try {
                        // מחפש את האלמנט האב הקרוב ביותר עם class inventory-item
                        // מחפש את הכפתור בתוך אלמנט האב ושולף ממנו את מזהה הפריט
                        var itemId = $(this).closest('.inventory-item').find('button[data-bs-target="#updateStockModal"]').data('id');
                        var itemName = $(this).closest('.inventory-item').find('button[data-bs-target="#updateStockModal"]').data('name');
                        var itemStock = $(this).closest('.inventory-item').find('button[data-bs-target="#updateStockModal"]').data('stock');
                        var itemMin = $(this).closest('.inventory-item').find('button[data-bs-target="#updateStockModal"]').data('min');
                        var itemUnit = $(this).closest('.inventory-item').find('button[data-bs-target="#updateStockModal"]').data('unit');
                        var itemCategory = $(this).closest('.inventory-item').data('category');
                        var itemStockLevel = $(this).closest('.inventory-item').data('stock-level');
                        // לשלוף את התיאור של הפריט שנמצא בתוך כרטיס פריט שנלחץ עליו – ולהסיר רווחים מיותרים בתחילת ובסוף הטקסט
                        var itemDescription = $(this).find('.inventory-description').text().trim();
                        var itemImage = $(this).find('img').attr('src');
                        // לשלוף את כתובת הקישור להזמנה מתוך כפתור "הזמן" שנמצא בתוך כרטיס פריט, ואם אין קישור – נותנת ברירת מחדל
                        var itemOrderLink = $(this).find('a.btn-success').attr('href') || 'https://www.animalshop.co.il/';
                        
                    // מחלץ את הטקסט מתוך אותו אלמנט שלאחר הנקודותיים
                    // הטקסט המחולץ הינו : כמות הצריכה + "יחידות" + יחידת המידה
                    var requiredCurrent = $(this).find('.required-estimate:first').text().split(':')[1].trim();
                    var requiredNext = $(this).find('.required-estimate:last').text().split(':')[1].trim();

                        
                        // עדכון תוכן המודאל
                        $('#viewItemTitle').text(itemName);
                        $('#viewItemImage').attr('src', itemImage);
                        $('#viewItemCategory').text(itemCategory);
                        $('#viewItemDescription').text(itemDescription);
                        $('#viewItemStock').text(itemStock + ' ' + itemUnit);
                        $('#viewItemMin').text(itemMin + ' ' + itemUnit);
                        $('#viewItemCurrentMonth').text('<?php echo $currentMonthName; ?>');
                        $('#viewItemNextMonth').text('<?php echo $nextMonthName; ?>');
                        // הטקסט המחולץ הינו : כמות הצריכה + "יחידות" + יחידת המידה
                        // שותל את הטקסט המחולץ לתוך מודאל הצפייה בפריט
                        $('#viewItemRequiredCurrent').text(requiredCurrent);
                        $('#viewItemRequiredNext').text(requiredNext);
                        
                       // עדכון והעברת נתונים לכפתור "עדכן מלאי" במודאל צפייה בפריט 
                        $('#viewItemUpdate')
                            .attr('data-id', itemId)
                            .attr('data-name', itemName)
                            .attr('data-stock', itemStock)
                            .attr('data-min', itemMin)
                            .attr('data-unit', itemUnit);
                        
                    // מכניס את הקישור להזמנה (אם יש) לתוך כפתור "הזמן עכשיו" בתוך המודאל צפייה בפריט
                    $('#viewItemOrder').attr('href', itemOrderLink);
                    // אם רמת המלאי תקינה – מסתירים את כפתור ההזמנה (כי אין צורך להזמין)
                    if (itemStockLevel === 'ok') {
                        $('#viewItemOrder').addClass('d-none');
                    } else {
                        $('#viewItemOrder').removeClass('d-none');
                    }
                        
                    // שומר את מזהה ושם הפריט בכפתור "מחק" במודאל צפייה בפריט  – כדי שלאחר מכן שמודאל המחיקה יקבל את הנתונים
                    $('#viewItemDelete').attr('data-id', itemId)
                                        .attr('data-name', itemName);
                    
                    // מציג בפועל את מודאל הצפייה בפריט עם כל הנתונים שמולאו בשורות הקודמות
                    $('#viewItemModal').modal('show');

                    } catch (error) {
                        console.error("שגיאה בעת טעינת נתוני הפריט:", error);
                    }
                }
            });
            
            //  טיפול בלחיצה על כפתור עדכן מלאי במודאל הצפייה 
            $('#viewItemUpdate').on('click', function() {
                // הסתרת מודאל צפייה
                $('#viewItemModal').modal('hide');
                
                // שמירת פרטי הפריט למשתנים
                var itemId = $(this).attr('data-id');
                var itemName = $(this).attr('data-name');
                var itemStock = $(this).attr('data-stock');
                var itemMin = $(this).attr('data-min');
                var itemUnit = $(this).attr('data-unit');
                
                console.log("פרטי הפריט לעדכון:", {
                    id: itemId,
                    name: itemName,
                    stock: itemStock,
                    min: itemMin,
                    unit: itemUnit
                });
                
                // הפעלת פונקציה השהייה כדי לוודא שהמודאל הראשון נסגר לפני פתיחת השני
                setTimeout(function() {

                    // עדכון שדות הטופס במודאל עדכון כמות מלאי
                    $('#updateItemId').val(itemId);
                    $('#updateItemName').val(itemName);
                    $('#updateItemStock').val(itemStock);
                    $('#updateItemMin').val(itemMin);
                    $('#updateItemUnit').text(itemUnit);
                    $('#updateItemUnitMin').text(itemUnit);
                    
                    // פתיחת מודאל עדכון מלאי
                    $('#updateStockModal').modal('show');
                }, 200); // השהיה של 200 מילישניות
            });
            
            /*            
            כאשר לוחצים על כפתור "עדכן מלאי" במודאל הצפייה,
            נרצה שהמודאל של עדכון הכמות יפתח עם הנתונים הנכונים של הפריט הנוכחי.
            אם האירוע המקורי פעיל, הוא עלול לדרוס את הנתונים שהגדרנו בנתונים של כפתור עדכן בכרטסיית הפריט
            */
            $('#updateStockModal').off('show.bs.modal');
            
            
    // טיפול בלחיצה ישירה על כפתורי העדכון בכרטיסיות הפריטים
    $('button[data-bs-target="#updateStockModal"]').on('click', function() {
        var button = $(this);
        var id = button.data('id');
        var name = button.data('name');
        var stock = button.data('stock');
        var min = button.data('min');
        var unit = button.data('unit');
        
        console.log("לחיצה על כפתור עדכן מכרטיסיית פריט:", {
            id: id,
            name: name,
            stock: stock,
            min: min,
            unit: unit
        });
        
        // עדכון שדות הטופס במודאל עדכון כמות מלאי
        $('#updateItemId').val(id);
        $('#updateItemName').val(name);
        $('#updateItemStock').val(stock);
        $('#updateItemMin').val(min);
        $('#updateItemUnit').text(unit);
        $('#updateItemUnitMin').text(unit);
    });
            
            //  טיפול בלחיצה על כפתור מחק במודאל הצפייה 
            $('#viewItemDelete').on('click', function() {
                // הסתרת מודאל צפייה
                $('#viewItemModal').modal('hide');
                
                // שמירת פרטי הפריט
                var itemId = $(this).attr('data-id');
                var itemName = $(this).attr('data-name');
                
                // הפעלת פונקציה מושהית כדי לוודא שהמודאל הראשון נסגר לפני פתיחת השני
                setTimeout(function() {
                    // עדכון שדות הטופס במודאל מחיקת פריט
                    $('#deleteItemId').val(itemId);
                    $('#deleteItemName').text(itemName);
                    
                    // פתיחת מודאל מחיקה
                    $('#deleteItemModal').modal('show');
                }, 200); // השהיה של 200 מילישניות
            });
            
            // מסיר מאפיינים מהכפתורים אחרי אתחול הדף  
            // חשוב לעשות זאת אחרי ש-Bootstrap כבר אתחל את הכפתורים בטעינה של הדף
            setTimeout(function() {
                $('#viewItemUpdate').removeAttr('data-bs-toggle').removeAttr('data-bs-target');
                $('#viewItemDelete').removeAttr('data-bs-toggle').removeAttr('data-bs-target');
            }, 500);
            
             // פונקציות להגדלה והקטנה של כמות המלאי
            $('#increaseStock').click(function() {
                // שואב את הערך הנוכחי מתוך שדה שמכיל את כמות המלאי
                var currentVal = parseInt($('#updateItemStock').val());
                // מעדכן את ערך השדה
                $('#updateItemStock').val(currentVal + 1);
            });
            
            $('#decreaseStock').click(function() {
                var currentVal = parseInt($('#updateItemStock').val());
                if (currentVal > 0) {
                    $('#updateItemStock').val(currentVal - 1);
                }
            });
            
            $('#increaseMin').click(function() {
                var currentVal = parseInt($('#updateItemMin').val());
                $('#updateItemMin').val(currentVal + 1);
            });
            
            $('#decreaseMin').click(function() {
                var currentVal = parseInt($('#updateItemMin').val());
                if (currentVal > 0) {
                    $('#updateItemMin').val(currentVal - 1);
                }
            });
            
            // חיפוש וסינון פריטים
            // מאזין להקלדה/שינוי בתיבת החיפוש
            $('#searchInventory').on('input', function() {
                filterItems();
            });
            
             // מאזין לבחירה בתיבת הסינון לפי קטגוריה
            $('#categoryFilter').change(function() {
                filterItems();
            });
            
             // משנים את ערך תיבת הקטגורייה  ל־ low-stock
            $('#showLowStock').click(function() {
                $('#categoryFilter').val('low-stock');
                filterItems();
            });
            
             // מנקה את תיבת החיפוש ומשנה את ערך הקטגוריה לכל הקטגוריות
            $('#resetFilters').click(function() {
                $('#searchInventory').val('');
                $('#categoryFilter').val('all');
                filterItems();
            });
            
            //  מופעלת בכל פעם שהמשתמש משנה את החיפוש, קטגוריה, או לוחץ על כפתור הצג רק פריטים בחוסר / אפס סינון
            function filterItems() {
                // מה שהמשתמש הקליד בתיבת החיפוש, באותיות קטנות
                var searchQuery = $('#searchInventory').val().toLowerCase();
                // מה שנבחר בתיבת הקטגוריה
                var categoryFilter = $('#categoryFilter').val();

                // ישמש להצגת הודעת אין תוצאות
                var visibleItems = 0;
                
                //  לולאה על כל כרטיס פריט
                $('.inventory-item').each(function() {
                    var itemId = $(this).data('item-id');
                    var itemName = $(this).data('name').toLowerCase();
                    var itemCategory = $(this).data('category');
                    var itemStockLevel = $(this).data('stock-level');
                    var showItem = true;
                    
                    // סינון לפי שם
                    if (searchQuery && itemName.indexOf(searchQuery) === -1) {
                        showItem = false;
                    }
                    
                    // סינון לפי קטגוריה
                    if (categoryFilter !== 'all') {
                        if (categoryFilter === 'low-stock') {
                            if (itemStockLevel === 'ok') {
                                showItem = false;
                            }
                        } 
                        else if (categoryFilter === 'פריטים בחוסר') {
                            if (itemStockLevel === 'ok') {
                                showItem = false;
                            }
                        }
                        else if (itemCategory !== categoryFilter) {
                            showItem = false;
                        }
                    }
                    
                    // אם הפריט עבר את הסינונים – מציגים אותו ומעדכנים את המונה, אחרת מסתירים אותו
                    if (showItem) {
                        $(this).show();
                        visibleItems++;
                    } else {
                        $(this).hide();
                    }
                });
                
                // הצגת הודעה כאשר אין פריטים תואמים
                if (visibleItems === 0) {
                    $('#noItems').removeClass('d-none');
                } else {
                    $('#noItems').addClass('d-none');
                }
                
            }
            
            // הפעלת סינון ראשוני עם טעינת העמוד
            filterItems();
        });
    </script>
</body>
</html> 