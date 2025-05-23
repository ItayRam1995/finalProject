<?php
include '../../header.php';

// נתוני חיבור לבסיס הנתונים
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

// יצירת חיבור לבסיס הנתונים
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

/*
$host="127.0.0.1";
$port=3306;
$socket="";
$user="root";
$password="";
$dbname="itayrm_dogs_boarding_house";

$conn = new mysqli($host, $user, $password, $dbname, $port, $socket)
	or die ('Could not connect to the database server' . mysqli_connect_error());
*/

// בדיקת חיבור
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_code = '';
// נסה לקבל מ-session אם קיים
if (isset($_SESSION['user_code'])) {
    $user_code = $_SESSION['user_code'];
} 
// אם אין גישה למשתמש, נציג הודעה מתאימה בהמשך
else if (isset($_SESSION['username'])) {
    $user_code = $_SESSION['username'];
}

// טיפול בביטול הזמנה
$cancel_message = '';
if (isset($_POST['cancel_reservation']) && isset($_POST['reservation_id'])) {
    $reservation_id = $_POST['reservation_id'];
    
    // וידוא שההזמנה שייכת למשתמש המחובר
    if (!empty($user_code)) {
        $check_sql = "SELECT COUNT(*) as count FROM reservation WHERE id = ? AND user_code = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("is", $reservation_id, $user_code);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $check_row = $check_result->fetch_assoc();
        
        if ($check_row['count'] > 0) {
            // ביטול ההזמנה
            $cancel_sql = "DELETE FROM reservation WHERE id = ?";
            $cancel_stmt = $conn->prepare($cancel_sql);
            $cancel_stmt->bind_param("i", $reservation_id);
            
            if ($cancel_stmt->execute()) {
                $cancel_message = '<div class="alert alert-success">ההזמנה בוטלה בהצלחה!</div>';
                // רענון העמוד אחרי 2 שניות
                echo '<script>setTimeout(function() { window.location.href = window.location.pathname; }, 2000);</script>';
            } else {
                $cancel_message = '<div class="alert alert-error">אירעה שגיאה בביטול ההזמנה.</div>';
            }
        } else {
            $cancel_message = '<div class="alert alert-error">אין הרשאה לבטל הזמנה זו או שההזמנה לא קיימת.</div>';
        }
    } else {
        $cancel_message = '<div class="alert alert-error">עליך להתחבר כדי לבטל הזמנות.</div>';
    }
}

// שליפת הזמנות לפי קוד משתמש
$orders = [];
if (!empty($user_code)) {
    $sql = "SELECT *, 
            DATEDIFF(end_date, start_date) + 1 AS total_days,
            CASE 
                WHEN end_date >= CURDATE() THEN 'active'
                ELSE 'completed'
            END AS status
            FROM reservation 
            WHERE user_code = ?
            ORDER BY start_date DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ההזמנות שלי - פנסיון לכלבים</title>
    <style>
        .my-orders-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fafafa;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            color: #333;
            line-height: 1.5;
        }

        /* כותרת מרכזית */
        .my-orders-title {
            text-align: center;
            margin-bottom: 40px;
            font-size: 2em;
            color: #2c3e50;
            font-weight: bold;
        }

        /* הקופסה של כל הזמנה */
        .order-item {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .order-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }

        /* כותרת ההזמנה + תאריכים */
        .order-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .order-dates {
            font-size: 1.1em;
            font-weight: bold;
            color: #34495e;
        }

        .order-details {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .order-detail-row {
            display: flex;
            justify-content: space-between;
            width: 100%;
            padding: 8px 12px;
            border-radius: 6px;
            background-color: #f0f0f0;
            transition: background-color 0.2s;
            font-size: 0.95em;
        }

        .order-detail-row:nth-child(even) {
            background-color: #e9ecef;
        }

        .order-detail-row:hover {
            background-color: #dfe6e9;
        }

        /* תוויות וערכים */
        .detail-label {
            font-weight: 600;
            color: #555;
        }

        .detail-value {
            font-weight: 700;
            color: #222;
            margin-top: 5px;
        }
        
        .order-actions {
            text-align: left;
            margin-top: 10px;
        }
        
        /* תגים לסטטוס ההזמנה */
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-completed {
            background-color: #e2e3e5;
            color:rgb(7, 135, 240);
        }

        .status-canceled {
            background-color: #e2e3e5;
            color:rgb(245, 71, 40);
        }

        /* כפתורי פעולות */
        .order-actions {
            margin-top: 20px;
            display: flex;
            gap: 15px;
        }

        .btn-cancel {
            background-color: #e74c3c;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.2s, transform 0.2s;
        }

        .btn-cancel:hover {
            background-color: #c0392b;
            transform: scale(1.02);
        }

        .btn-success {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.2s, transform 0.2s;
        }

        .btn-success:hover {
            background-color: #2980b9;
            transform: scale(1.02);
        }
        
        .no-orders {
            text-align: center;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
            color: #7f8c8d;
            margin-top: 30px;
        }
        
        /* הודעות סטטוס (הצלחה, שגיאה) */
        .alert {
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            font-size: 1em;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        
        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
            }
            
            .order-detail-row {
                flex-direction: column;
                border-bottom: 1px solid #eee;
            }
            
            .detail-value {
                margin-top: 5px;
            }
        }

    </style>
</head>
<body>
    <div class="my-orders-container">
        <h2 class="my-orders-title">ההזמנות שלי</h2>
        
        <?php echo $cancel_message; ?>
        
        <?php if (empty($user_code)): ?>
            <div class="alert alert-warning">עליך להתחבר למערכת כדי לצפות בהזמנות שלך.</div>
            <div class="no-orders">
                <p>אין הזמנות להצגה. אנא התחבר למערכת תחילה.</p>
            </div>
        <?php elseif (empty($orders)): ?>
            <div class="no-orders">
                <p>אין הזמנות להצגה</p>
                <p>ניתן להזמין שהייה חדשה בפנסיון דרך דף "הזמנה חדשה"</p>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $row): ?>
                <?php
                    $status = $row['status'];
                    if ($status == 'active') {
                        $status_text = 'פעילה';
                        $status_class = 'status-active';
                    } elseif ($status == 'paid') {
                        $status_text = 'שולמה';
                        $status_class = 'status-completed';
                    } else {
                        $status_text = 'בוטלה';
                        $status_class = 'status-canceled';
                    }
                    
                    // פורמט תאריכים
                    $start_date = date("d/m/Y", strtotime($row['start_date']));
                    $end_date = date("d/m/Y", strtotime($row['end_date']));
                    $created_at = date("d/m/Y H:i", strtotime($row['created_at']));
                ?>
                <div class="order-item">
                    <div class="order-header">
                        <div class="order-dates">תאריכי שהייה: <?php echo $start_date; ?> - <?php echo $end_date; ?></div>
                        <div>מספר הזמנה: <?php echo $row['id']; ?></div>
                    </div>
                    
                    <div class="order-details">
                        <div class="order-detail-row">
                            <span class="detail-label">סטטוס:</span>
                            <span class="detail-value"><span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></span>
                        </div>
                        
                        <div class="order-detail-row">
                            <span class="detail-label">מספר ימים:</span>
                            <span class="detail-value"><?php echo $row['total_days']; ?></span>
                        </div>
                        
                        <div class="order-detail-row">
                            <span class="detail-label">תאריך יצירה:</span>
                            <span class="detail-value"><?php echo $created_at; ?></span>
                        </div>

                        <div class="order-detail-row">
                            <span class="detail-label">סכום לתשלום:</span>:</span>
                            <span class="detail-value"><?php echo $row['total_payments'] ?></span>
                        </div>
                    </div>
                    
                    <?php if ($status == 'active'): ?>
                    <div class="order-actions">
                        <form method="post" onsubmit="return confirm('האם אתה בטוח שברצונך לבטל הזמנה זו?')">
                            <input type="hidden" name="reservation_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="cancel_reservation" class="btn-cancel">ביטול הזמנה</button>
                        </form>
                        <form action="../../payment/payment.php" method="get" onsubmit="return confirm('ברצונך לשלם עבור הזמנה זו ?')">
                            <input type="hidden" name="reservation_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="total_payments" value="<?php echo $row['total_payments']; ?>">
                            <button type="submit" name="pay_reservation" class="btn-success">לתשלום הזמנה</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <?php
    $conn->close();
    ?>
</body>
</html>