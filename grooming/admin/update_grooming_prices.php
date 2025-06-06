<?php include '../../header.php'; ?>
<?php

// הגדרות חיבור למסד נתונים
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

// התחברות למסד נתונים
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("שגיאה בחיבור למסד הנתונים: " . $conn->connect_error);
}

// תמיכה בעברית
$conn->set_charset("utf8");

// משתנה לאחסון הודעות למשתמש
$message = "";

// טיפול בשליחת הטופס ושהמשתמש אישר את העדכון 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_prices'])) {
    $success = true;
    $errors = [];
    
    // לולאה על כל המחירים שנשלחו
    // עוברים על כל הערכים שנשלחו בטופס מהשדות name = prices[id]
    // $id הוא המזהה של סוג הטיפוח
    // $price הוא הערך המספרי החדש שהוזן בטופס לאותו סוג טיפוח
    foreach ($_POST['prices'] as $id => $price) {
        // וידוא שהמחיר הוא מספר חיובי
        if (!is_numeric($price) || $price < 0) {
            // מוסיפים הודעת שגיאה למערך 
            $errors[] = "מחיר לא תקין עבור שורה $id";
            $success = false;
            // מדלג על שורה זו
            continue;
        }
        
        // עדכון המחיר בבסיס הנתונים
        // יצירת שאילתה
        $stmt = $conn->prepare("UPDATE grooming_prices SET grooming_price = ? WHERE id = ?");
        // קישור ערכים למשתני השאילתה
        // d – מספר עשרוני
        // i – מספר שלם
        $stmt->bind_param("di", $price, $id);
        
        // מבצעים את השאילתה.
        if (!$stmt->execute()) {
            // מוסיפים שגיאה עם הפירוט מהשרת 
            $errors[] = "שגיאה בעדכון שורה $id: " . $stmt->error;
            $success = false;
        }
        $stmt->close();
    }
    
    // הצגת הודעה מתאימה בהתאם לתוצאה
    if ($success && empty($errors)) {
        $message = "<div class='alert alert-success'>המחירים עודכנו בהצלחה!</div>";
    } else {
        $message = "<div class='alert alert-danger'>שגיאות בעדכון:<br>" . implode("<br>", $errors) . "</div>";
    }
}

// שליפת נתוני המחירים הנוכחיים מהטבלה
$sql = "SELECT id, grooming_type, grooming_price FROM grooming_prices ORDER BY id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>עדכון מחירי טיפוח</title>
    <style>

        /* עיצוב כללי של הדף */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /*סגול*/
            margin: 0;
            padding: 20px;
            min-height: 100vh; /* גובה מינימלי של מסך מלא */
        }
        
        /* מיכל ראשי */
        .container {
            max-width: 800px; /* רוחב מקסימלי למיכל */
            margin: 0 auto; /* מרכוז המיכל */
            background: white; /* רקע לבן */
            border-radius: 15px; /* פינות מעוגלות */
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden; /* מסתיר תוכן שחורג */
        }
        
        /* כותרת הדף */
        .header {
            background: linear-gradient(45deg, #4CAF50, #45a049); /*ירוק*/
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 2.5em; /* גודל פונט גדול לכותרת */
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3); /* צל לטקסט */
        }
        
        /* אזור התוכן */
        .content {
            padding: 30px;
        }
        
        /* הודעות התראה */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px; /* פינות מעוגלות */
            font-weight: bold;
        }
        
        /* התראת הצלחה - רקע ירוק בהיר */
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        /* התראת שגיאה - רקע אדום בהיר */
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* טבלת המחירים */
        .price-table {
            width: 100%;
            border-collapse: collapse; /* ביטול רווחים בין תאים */
            margin-top: 20px;
            background: white;
            border-radius: 10px; /* פינות מעוגלות לטבלה */
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        /* כותרות הטבלה */
        .price-table th {
            background: linear-gradient(45deg, #2196F3, #1976D2); /* רקע כחול */
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 1.1em;
        }
        
        /* תאי הטבלה */
        .price-table td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #eee; /* קו תחתון */
        }
        
        .price-table tr:hover {
            background-color: #f8f9fa; /* רקע בהיר בעת ריחוף */
            transform: scale(1.01); /* הגדלה קלה */
            transition: all 0.3s ease; /* מעבר חלק */
        }
        
        /* שדות קלט המחיר */
        .price-input {
            width: 100px;
            padding: 10px;
            border: 2px solid #ddd; /* מסגרת אפורה */
            border-radius: 25px; /* שדה מעוגל לחלוטין */
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        /* מצב פוקוס על שדה הקלט */
        .price-input:focus {
            outline: none; /* ביטול מסגרת ברירת מחדל */
            border-color: #4CAF50; /* מסגרת ירוקה */
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.3); /* זוהר ירוק */
            transform: scale(1.05); /* הגדלה קלה */
        }
        
        /* עיצוב סוג הטיפוח */
        .grooming-type {
            font-weight: bold;
            color: #333;
            font-size: 1.1em;
        }
        
        /* מיכל הכפתור */
        .btn-container {
            text-align: center;
            margin-top: 30px;
        }
        
        /* כפתור העדכון */
        .btn-update {
            background: linear-gradient(45deg, #FF6B6B, #FF5252); /*אדום-כתום*/
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 50px; /* כפתור עגול */
            font-size: 18px;
            font-weight: bold;
            cursor: pointer; /* סמן יד */
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3); /* צל אדמדם */
        }
        
        .btn-update:hover {
            transform: translateY(-3px); /* הרמה קלה */
            box-shadow: 0 10px 25px rgba(255, 107, 107, 0.4); /* צל עמוק יותר */
        }
        
        /* אפקט לחיצה על הכפתור */
        .btn-update:active {
            transform: translateY(-1px); /* ירידה קלה */
        }
        
        /* עיצוב סמל המטבע */
        .currency {
            color: #4CAF50; /* ירוק */
            font-weight: bold;
        }
        
        /* אנימציית כניסה */
        @keyframes fadeIn {
            
            from { 
                opacity: 0;  /* שקיפות מלאה - אלמנט לא נראה */
                transform: translateY(20px); /* הזזה למטה */
                }

            to {
                opacity: 1; /* אטימות מלאה - אלמנט נראה לגמרי */
                transform: translateY(0); /* חזרה למיקום */
            }
        }
        
        /* הפעלת האנימציה על שורות הטבלה */
        .price-table tr {
            animation: fadeIn 0.5s ease forwards;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- אזור הכותרת -->
        <div class="header">
            <h1> עדכון מחירי טיפוח </h1>
        </div>
        
        <!-- אזור התוכן הראשי -->
        <div class="content">
            <?php echo $message; ?> <!-- הצגת הודעות מהשרת (הצלחה/שגיאה) -->
            
            <!-- טופס עדכון המחירים -->
            <form method="POST" action="">
                <?php if ($result && $result->num_rows > 0): ?>
                    <!-- טבלת המחירים - מציגה את כל סוגי הטיפוח והמחירים -->
                    <table class="price-table">
                        <thead>
                            <tr>
                                <th>מזהה</th>
                                <th>סוג טיפוח</th>
                                <th>מחיר נוכחי</th>
                                <th>מחיר חדש</th>
                            </tr>
                        </thead>
                        <tbody>
                             <!-- לולאת PHP לשליפת כל השורות מהמסד -->
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <!-- תא מזהה - מספר סידורי -->
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                     <!-- תא סוג הטיפוח - שם השירות -->
                                    <td class="grooming-type"><?php echo htmlspecialchars($row['grooming_type']); ?></td>
                                    <!-- תא מחיר נוכחי - עם סמל מטבע -->
                                    <td><span class="currency">₪<?php echo number_format($row['grooming_price'], 0); ?></span></td>
                                    <!-- תא מחיר חדש - שדה קלט לעדכון -->
                                    <td>
                                        <!-- שדה קלט עם ולידציות -->
                                        <input type="number" 
                                               name="prices[<?php echo $row['id']; ?>]" 
                                               value="<?php echo $row['grooming_price']; ?>" 
                                               class="price-input" 
                                               min="0"  
                                               step="1" 
                                               required>
                                        <span class="currency">₪</span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    
                    <!-- כפתור שליחה עם הודעת בקשה לאישור -->
                    <div class="btn-container">
                        <button type="submit" name="update_prices" class="btn-update" onclick="return confirm('האם אתה בטוח שברצונך לעדכן את המחירים?')">
                            💾 עדכן מחירים
                        </button>
                    </div>
                <?php else: ?>
                    <!-- הודעת שגיאה במקרה שאין נתונים -->
                    <div class="alert alert-danger">
                        לא נמצאו נתונים בטבלת המחירים
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <script>
        // הוספת אפקטים - שינוי צבע המסגרת והרקע בהתאם לערך
        document.addEventListener('DOMContentLoaded', function() {
            // בחירת כל שדות הקלט של המחירים
            const inputs = document.querySelectorAll('.price-input');
            
            // הוספת מאזין לכל שדה קלט
            inputs.forEach(input => {
                // מאזין לאירוע 'input' - כל שינוי בערך השדה
                input.addEventListener('input', function() {
                     // שינוי צבע המסגרת והרקע בהתאם לערך
                    if (this.value && this.value > 0) {
                        // ערך תקין - צבע ירוק
                        this.style.borderColor = '#4CAF50';
                        this.style.backgroundColor = '#f8fff8';
                    } else {
                         // ערך לא תקין - צבע אדום
                        this.style.borderColor = '#ff4444';
                        this.style.backgroundColor = '#fff8f8';
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>