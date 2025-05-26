<?php include '../../header.php'; ?>

<!DOCTYPE html>
<html dir="rtl" lang="he">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>סיכום הזמנת טיפוח</title>
    <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet"/>
    <style>

        /* רקע אפור בהיר, ריווח פנימי, מרכז את התוכן אופקית */
        body {
            font-family: 'Varela Round', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }

        /* הקופסה הראשית שמכילה את כל פרטי הסיכום */
        /* רקע לבן, ריווח פנימי, פינות מעוגלות, צל וטקסט ממורכז, רוחב מקסימלי */
        .summary-container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 20px;
        }

        /* העיגול הירוק עם סימן */
        /* רקע ירוק, טקסט לבן ממורכז  */
        .success-icon {
            width: 60px;
            height: 60px;
            background-color: #28a745;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 30px;
            font-weight: bold;
        }

        /* כותרת עליונה */
        /* צבע ירוק, מרווח תחתון */
        h1 {
            color: #28a745;
            margin-bottom: 10px;
            font-size: 1.8em;
        }
        /* כותרת משנה */
        /* צבע אפור בהיר, וריווח מתחת */
        .subtitle {
            color: #6c757d;
            margin-bottom: 25px;
            font-size: 1em;
        }

        /* התיבה עם מספר האישור */
        /* רקע ירוק בהיר, גבול ירוק, פינות מעוגלות, ריווח פנימי */
        .confirmation-box {
            background-color: #e8f5e8;
            border: 1px solid #28a745;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        /* מספר האישור עצמו */
        /* טקסט ירוק כהה, מודגש וגדול */
        .confirmation-code {
            font-size: 1.5em;
            font-weight: bold;
            color: #28a745;
            margin: 10px 0;
            letter-spacing: 2px;
        }

        /* בלוק שמכיל את פרטי ההזמנה */
        /* רקע אפור בהיר, פינות מעוגלות, ריווח פנימי, וטקסט מיושר לימין */
        .details-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: right;
        }

        /* כל שורת מידע */
        /* תצוגת שורה עם ריווח בין התווית לערך, קו תחתון דק */
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        /* השורה האחרונה בטבלה */
        /* מסירה את קו הגבול התחתון */
        .detail-row:last-child {
            border-bottom: none;
        }

        /* תווית של שורת הפרטים */
        /* צבע אפור כהה */
        .detail-label {
            color: #6c757d;
            font-weight: normal;
        }

        /* הערך של שורת הפרטים */
        /* צבע כהה, טקסט מודגש */
        .detail-value {
            color: #333;
            font-weight: bold;
        }

        /* תצוגת המחיר	 */
        /* רקע כחול, טקסט לבן, ריווח פנימי ועיגול פינות */
        .price-highlight {
            background-color: #007bff;
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 1em;
        }

        /* מיכל של הכפתורים	 */
        /* שורות של כפתורים עם ריווח */
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* כל כפתור */
        /* ריווח, עיגול פינות, גודל טקסט */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 0.9em;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            min-width: 120px;
            transition: background-color 0.3s;
        }

        /* כפתור לתשלום */
        /* רקע כחול, טקסט לבן */
        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        /* כפתור ההזמנות שלי */
        /* רקע אפור כהה, טקסט לבן */
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        /* כפתור הזמן טיפוח נוסף */
        /* רקע ירוק כהה */
        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        /* תיבה שמציינת אם יש קישור להזמנת פנסיון */
        /* רקע כחול בהיר, פינות מעוגלות, ריווח פנימי וטקסט כחול כהה */
        .reservation-link {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #1565c0;
            font-size: 0.9em;
        }

        /* בלוק שמציג את פרטי הכלב */
        /* רקע אפור בהיר, גבול אפור, פינות מעוגלות, וריווח פנימי */
        .dog-info {
            background-color: #f1f3f4;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid #dee2e6;
        }

        /* שם הכלב */
        /* טקסט כהה, מודגש וגדול מעט */
        .dog-name {
            font-size: 1.1em;
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }

        /* מזהה הכלב */
        /* טקסט אפור */
        .dog-id {
            color: #6c757d;
            font-size: 0.9em;
        }

        @media (max-width: 768px) {
            /* הקופסה הראשית שמכילה את כל פרטי הסיכום */
            .summary-container {
                padding: 20px;
                margin: 10px;
            }

            /* אזור הכפתורים */
            /* מסדר את הכפתורים בטור במקום בשורה */
            .action-buttons {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                min-width: auto;
                width: 100%;
                margin-bottom: 5px;
            }
            
            /* שורת פרטים */
            /* משנה לתצוגת טור */
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 3px;
            }
        }
    </style>
</head>
<body>
    <!-- תיבת הצלחה -->
    <div class="summary-container">
        
        <div class="success-icon">✓</div>
        
        <h1>הזמנת הטיפוח אושרה בהצלחה!</h1>
        <p class="subtitle">הזמנת הטיפוח שלך נרשמה במערכת</p>

        <!-- תיבת מספר אישור -->
        <div class="confirmation-box">
            <h3 style="margin: 0 0 10px 0; color: #495057;">מספר אישור</h3>
            <div class="confirmation-code">
                <?php 
                // אם מספר אישור ההזמנה קיים בסשן
                // מדפיס את מספר האישור על המסך
                $confirmation = isset($_SESSION['last_grooming_confirmation']) ? $_SESSION['last_grooming_confirmation'] : 'לא זמין';
                echo $confirmation;
                ?>
            </div>
            <p style="margin: 10px 0 0 0; color: #6c757d; font-size: 0.9em;">
                שמרו מספר זה לצורך פניות עתידיות
            </p>
        </div>

        <!-- כל אחד מהמשתנים האלה שואב ערכים מהסשן שנשמרו בעמוד groomingAppServer -->
        <?php
        $grooming_type = isset($_SESSION['last_grooming_type']) ? $_SESSION['last_grooming_type'] : 'טיפוח כללי';
        $grooming_price = isset($_SESSION['last_grooming_price']) ? $_SESSION['last_grooming_price'] : 0;
        $appointment_day = isset($_SESSION['last_appointment_day']) ? $_SESSION['last_appointment_day'] : date('Y-m-d');
        $appointment_time = isset($_SESSION['last_appointment_time']) ? $_SESSION['last_appointment_time'] : '';
        $dog_name = isset($_SESSION['active_dog_name']) ? $_SESSION['active_dog_name'] : 'הכלב שלך';
        $connected_reservation_id = isset($_SESSION['last_connected_reservation_id']) ? $_SESSION['last_connected_reservation_id'] : null;

        // תרגום תאריך ושם יום לשפה העברית
        //  משתנה לאחסון התאריך המעוצב בעברית
        $formatted_date = '';
        if ($appointment_day) {
            $date_obj = new DateTime($appointment_day);
            // ממיר את אובייקט התאריך לפורמט קריא
            $formatted_date = $date_obj->format('d/m/Y');
            // שורה זו שולפת את שם היום באנגלית מתוך התאריך
            $day_name = $date_obj->format('l');
            
            $hebrew_days = [
                'Sunday' => 'ראשון',
                'Monday' => 'שני', 
                'Tuesday' => 'שלישי',
                'Wednesday' => 'רביעי',
                'Thursday' => 'חמישי',
                'Friday' => 'שישי',
                'Saturday' => 'שבת'
            ];
            
            //  לשלוף את שם היום בעברית ואם המפתח באנגלית לא קיים במערך אז להשתמש בשם היום באנגלית
            $hebrew_day = isset($hebrew_days[$day_name]) ? $hebrew_days[$day_name] : $day_name;
            // יוצר את המחרוזת הסופית שתוצג למשתמש
            $formatted_date = "יום {$hebrew_day}, {$formatted_date}";
        }
        ?>

        <!-- תצוגת הכלב -->
        <div class="dog-info">
            <div class="dog-name"><?php echo htmlspecialchars($dog_name); ?></div>
            <div class="dog-id">מזהה כלב: <?php echo htmlspecialchars($_SESSION['active_dog_id'] ?? 'לא זמין'); ?></div>
        </div>

        <!-- תצוגת פרטי הזמנה -->
        <div class="details-section">
            <h3 style="margin: 0 0 15px 0; color: #495057; text-align: center;">פרטי ההזמנה</h3>
            
            <div class="detail-row">
                <span class="detail-label">סוג טיפוח:</span>
                <span class="detail-value"><?php echo htmlspecialchars($grooming_type); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">תאריך:</span>
                <span class="detail-value"><?php echo $formatted_date; ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">שעה:</span>
                <span class="detail-value"><?php echo htmlspecialchars($appointment_time); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">משך הטיפול:</span>
                <span class="detail-value">30 דקות</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">עלות:</span>
                <span class="detail-value">
                    <span class="price-highlight">₪<?php echo number_format($grooming_price); ?></span>
                </span>
            </div>
        </div>

         <!-- אם ההזמנה קשורה להזמנת פנסיון – מציג את המזהה של הזמנת השהייה -->
        <?php if ($connected_reservation_id): ?>
        <div class="reservation-link">
            <strong> קשור להזמנת פנסיון מספר: <?php echo htmlspecialchars($connected_reservation_id); ?>#</strong><br>
        </div>
        <?php endif; ?>

        <!-- כפתורים להמשך שימוש: תשלום, הזמנה נוספת, צפייה בהזמנות קודמות -->
        <div class="action-buttons">
            <a href="treatments.php" class="btn btn-success">הזמן טיפוח נוסף</a>
            <a href="" class="btn btn-primary"> עבור לתשלום </a>
            <a href="../../grooming_panel/user/groomingPanelUser.php" class="btn btn-secondary">הזמנות הטיפוח שלי</a>
        </div>

    </div>

          <div>
        <?php
        // בדיקה אם אחד ממשתני הסשן של ההזמנה ריק 
        if (!(isset($_SESSION['last_grooming_confirmation']) &&
              isset($_SESSION['last_grooming_type']) &&
              isset($_SESSION['last_grooming_price']) &&
              isset($_SESSION['last_appointment_day']) &&
              isset($_SESSION['last_appointment_time']) &&
              isset($_SESSION['last_connected_reservation_id']))) 
            {
                // אם אחד מהמשתנים לא מוגדר בסשן אז תפנה בחזרה לדף בחירת טיפוח
                echo "<script>window.location.href = 'treatments.php';</script>";
            } 
        ?>
      </div>

</body>
</html>

<?php
// ניקוי נתוני הסשן של ההזמנה לאחר הצגת הסיכום
unset($_SESSION['last_grooming_confirmation']);
unset($_SESSION['last_grooming_type']);
unset($_SESSION['last_grooming_price']);
unset($_SESSION['last_appointment_day']);
unset($_SESSION['last_appointment_time']);
unset($_SESSION['last_connected_reservation_id']);
?>