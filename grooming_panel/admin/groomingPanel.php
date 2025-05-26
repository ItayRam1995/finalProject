<?php include '../../header.php'; ?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ניהול הזמנות טיפוח</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>

    /* משתנים גלובליים לצבעים */
    :root {
      --primary: #4e73df;
      --primary-dark: #3a5fc8;
      --secondary: #f8f9fc;
      --danger: #e74a3b;
      --danger-dark: #c93a2c;
      --success: #1cc88a;
      --success-dark: #18a978;
      --warning: #f6c23e;
      --dark: #5a5c69;
      --light-gray: #f8f9fc;
    }
    
    /* ריווח פנימי ואיפוס שוליים */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--secondary);
      padding: 20px;
      margin: 0;
    }
    
    /* מוסיף רקע לבן, הצללה, ריווח פנימי ופינות מעוגלות */
    .container {
      max-width: 1775px; /*  לתמוך ביותר עמודות בטבלה */
      margin: 0 auto;
      background: white;
      border-radius: 8px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }
    
    /* מרכז את הטקסט, מוסיף קו תחתון */
    h1 {
      text-align: center;
      color: var(--dark);
      margin-bottom: 30px;
      border-bottom: 2px solid var(--primary);
      padding-bottom: 10px;
    }
    
    /* אזור בקרה וסינון */
    /* סידור של שדות הסינון בשורה עם ריווחים */
    .controls {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
    }
    
    /* תיבת חיפוש */
    /* סידור האייקון ושדה טקסט בשורה אחת עם ריווח */
    .search-box {
      display: flex;
      gap: 10px;
      align-items: center;
    }
    
    /* שדות קלט ובחירה */
    /* עיצוב גובה, ריווח, מסגרת, ופינות מעוגלות */
    input[type="text"], select {
      padding: 8px 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 1rem;
    }
    
    /* אזור מסננים נוספים	 */
    /* סידור האלמנטים של פילטרים לפי תאריך, סוג טיפוח ומחיר */
    .status-filter {
      display: flex;
      gap: 10px;
      align-items: center;
      flex-wrap: wrap;
    }
    
    /* עיצוב תג של היום ומחר בעמודה תאריך */
    .badge {
      padding: 2px 6px;
      border-radius: 12px;
      color: white;
      font-size: 0.7rem;
      font-weight: bold;
      display: inline-block;
      margin-bottom: 2px;
      white-space: nowrap;
    }
    
    /* הצגת התאריך בשורות נפרדות */
    .date-cell {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 2px;
      font-size: 0.85rem;
    }
    
    /* תג בצבע כחול ראשי	 */
    .badge-primary {
      background-color: var(--primary);
    }
    
    /*תג לתאריך מחר*/
    .badge-tomorrow {
      background-color: #36b9cc;
    }
    
    /* תגי סטטוס הזמנה */
    .status-active {
      background-color: var(--success);
      color: white;
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.8rem;
      font-weight: bold;
    }
    
    .status-cancelled {
      background-color: #6c757d;
      color: white;
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.8rem;
      font-weight: bold;
    }
    
    /* תגי סטטוס תשלום */
    .status-paid {
      background-color: #198754;
      color: white;
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.8rem;
      font-weight: bold;
    }
    
    .status-unpaid {
      background-color: var(--danger);
      color: white;
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.8rem;
      font-weight: bold;
    }
    
    /* מיכל הטבלה הראשי עם כותרות קבועות */
    .table-wrapper {
      /* מסגרת אפורה */
      border: 1px solid #ddd;
      /* מעגל את הפינות */
      border-radius: 8px;
      /* מוסיף צל */
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-top: 20px;
       /* חותך כל תוכן שגולש החוצה מהקונטיינר */
      overflow: hidden; 
    }

    /* כותרות קבועות מחוץ לאזור הגלילה */
    .table-header {
      background-color: var(--primary);
      position: relative;
      z-index: 10;
    }

    .table-header table {
      margin: 0;
      /* מאחד את הגבולות של התאים בטבלה, כך שבין כל שני תאים סמוכים יהיה קו גבול אחד בלבד */
      border-collapse: collapse;
      width: 100%;
    }

    /* תא כותרת ראשית */
    .table-header th {
      padding: 12px 15px;
      text-align: center;
      color: white;
      font-weight: 600;
      border-bottom: 1px solid #eee;
      background-color: var(--primary);
      position: relative;
    }

    /* גוף הטבלה עם גלילה */
    .table-body {
      max-height: 500px; /* גובה מקסימלי לגוף הטבלה */
      overflow-y: auto; /* גלילה אנכית */
      overflow-x: auto; /* גלילה אופקית במקרה הצורך */
    }

    .table-body table {
      margin: 0;
      border-collapse: collapse;
      width: 100%;
    }

    /* עיצוב הכותרות הנסתרות בגוף הטבלה */
    /* הכותרת האמיתית מוצגת באזור נפרד
    נועד כדי לשמור על יישור עמודות, אבל הוא מוסתר לגמרי */
    .table-body thead {
      /* מסתיר את הכותרת, אבל היא עדיין תופסת מקום בדפדפן */
      visibility: hidden; /* מסתיר את הכותרות בגוף הטבלה */
      /* מבטל את הגובה שלה לחלוטין */
      height: 0;
    }

    /* מבטל כל ריווח/גבול/גודל בתוך התאים עצמם, כדי שהכותרת לא תשפיע בשום צורה על הגובה או מבנה השורות */
    .table-body thead th {
      height: 0;
      padding: 0;
      border: none;
    }

    /* עיצוב סרגל הגלילה */
    /* אורך ורוחב סרגל הגלילה */
    .table-body::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }

    /* רקע המסילה - אפור */
    .table-body::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }

    /* העיצוב של הגלילה עצמה (החלק שנע) */
    .table-body::-webkit-scrollbar-thumb {
      background: var(--primary);
      border-radius: 4px;
    }

    /* משנה צבע כשעוברים עם העכבר על פס הגלילה */
    .table-body::-webkit-scrollbar-thumb:hover {
      background: var(--primary-dark);
    }

    /* אנימציה חלקה בגלילה */
    .table-body {
      scroll-behavior: smooth;
    }

    /* עיצוב לקונטיינר של כפתורי ניווט בטבלה */
    .table-navigation {
      display: flex;
      /* מציב את הכפתורים אחד ליד השני בשורה אופקית */
      justify-content: space-between;
      /* שם את הכפתור הראשון בצד שמאל ואת השני בקצה השני */
      margin: 10px 0;
      /*  פיקסלים מעל ומתחת לאזור הכפתורים 10*/
      gap: 10px;
    }

    /* טבלת הנתונים */
    /*  טבלה ברוחב מלא, ללא רווח בין תאים, עם הצללה */
    table {
      width: 100%;
      /* מאחד את הגבולות של התאים הסמוכים כך שייראה גבול אחד משותף */
      border-collapse: collapse;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    /* תאים בטבלה */
    /* ריווח פנימי, יישור מרכזי, מסגרת תחתונה */
    th, td {
      padding: 12px 15px;
      text-align: center;
      border-bottom: 1px solid #eee;
    }
    
    /* רק התא של הכותרת */
    th {
      background-color: var(--primary);
      color: white;
      font-weight: 600;
     /* הכותרות בטבלה יישארו מוצגות למעלה גם כשגוללים את הטבלה למטה */
      position: sticky;
      top: 0;
    }
    
    /* שורה בטבלה במעבר עכבר */
    /* רקע כחול בהיר לשם הבהרה */
    tbody tr:hover {
      background-color: rgba(78, 115, 223, 0.05);
    }

    /* אפקט hover משופר לשורות */
    .table-body tbody tr:hover {
      background-color: rgba(78, 115, 223, 0.08);
      transform: translateX(-2px); /* תזוזה קלה בהובר */
      transition: all 0.2s ease;
    }
    
    /* כפתורים */
    /* טקסט לבן עם ריווח פנימי, פינות מעוגלות */
    .btn {
      padding: 6px 12px;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.2s;
      font-size: 0.9rem;
      /* יישור אלמנטים אופקית */
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    
    /* כפתור ביטול */
    /* רקע אדום */
    .btn-danger {
      background-color: var(--danger);
    }
    
    /* כהות צבע רקע */
    .btn-danger:hover {
      background-color: var(--danger-dark);
    }
    
    /* כפתור רענן */
    .btn-info {
      background-color: var(--primary);
    }
    
    /* שינוי גוון לכחול כהה */
    .btn-info:hover {
      background-color: var(--primary-dark);
    }
    
    /* תא כפתורים בטבלה */
    /* מסדר את כפתורי הפעולה במרכז, עם ריווח ביניהם */
    .actions {
      display: flex;
      gap: 5px;
      justify-content: center;
    }
    
    /* הודעות */
    /* תיבות הודעה עם ריווח ופינות מעוגלות, מוסתרות כברירת מחדל */
    .alert {
      padding: 10px 15px;
      border-radius: 4px;
      margin-bottom: 20px;
      display: none;
    }
    
    /* תיבת הצלחה */
    /* טקסט ירוק כהה, מסגרת ירוקה, רקע ירוק */
    .alert-success {
      background-color: rgba(28, 200, 138, 0.2);
      color: var(--success-dark);
      border: 1px solid var(--success);
    }
    
    /* תיבת שגיאה */
    /* רקע אדום בהיר, טקסט אדום כהה, מסגרת אדומה */
    .alert-danger {
      background-color: rgba(231, 74, 59, 0.2);
      color: var(--danger-dark);
      border: 1px solid var(--danger);
    }
    
    /* רשת של כרטיסים בגודל משתנה עם ריווחים */
    /* לוח סטטיסטיקות */
    .stats-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    
    /* תיבת מידע עם הצללה, ריווח פנימי */
    /* כרטיס סטטיסטי */
    .card {
      background: white;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      padding: 20px;
      display: flex;
      /* כל דבר מופיע אחד מתחת לשני */
      flex-direction: column;
      align-items: center;
      transition: transform 0.2s;
    }
    
    .card:hover {
      transform: translateY(-5px);
    }
    
    /* עיגול אייקון בכרטיס */
    /* רקע כחול, עיגול עם אייקון לבן */
    .card-icon {
      background: var(--primary);
      color: white;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      margin-bottom: 15px;
    }
    
    /* טקסט תיאור הכרטיס */
    .card-title {
      color: var(--dark);
      font-size: 0.9rem;
      margin-bottom: 5px;
    }
    
    /* ערך מספרי בכרטיס */
    /* טקסט מודגש וגדול  */
    .card-value {
      font-size: 1.8rem;
      font-weight: bold;
      color: var(--primary);
    }
    
    /* רספונסיביות במוביל */
    @media (max-width: 768px) {

      /* אזור בקרה וסינון */
      .controls {
        /* משנה את הסידור לאנכי במסכים קטנים */
        flex-direction: column;
        gap: 10px;
      }
      
      /* תיבת חיפוש ואזור מסננים נוספים */
      .search-box, .status-filter {
        width: 100%;
      }
      
      /* רספונסיביות משופרת */
      .table-body {
        max-height: 400px; /* גובה קטן יותר במובייל */
      }
      
      /* הוספת גלילה אופקית במובייל */
      .table-body table {
        min-width: 1200px; /*  לתמוך ביותר עמודות - רוחב מינימלי של הטבלה */
        display: block;
        /* מוסיף סרגל גלילה אופקי אם הטבלה רחבה מדי */
        /* overflow-x: auto; */
        /* מונע שבירת שורות בתוך התאים כך שהשורות יזוזו אופקית במקום להישבר לגובה */
        white-space: nowrap;
      }

      .table-header table {
        min-width: 1200px; /* לתמוך ביותר עמודות - רוחב מינימלי של הטבלה */
      }
      
      /* מציג טור אחד בלבד — כלומר, כל כרטיס יוצג בשורה נפרדת */
      .stats-cards {
        grid-template-columns: 1fr;
      }

      /* כפתורי ניווט במובייל */
      .table-navigation {
        flex-direction: column;
        gap: 5px;
      }
      
        .table-header th:nth-child(2), .table-body td:nth-child(2) { width: 140px; min-width: 140px; } /* תאריך רחב יותר במובייל */
  
      .date-cell {
        font-size: 0.8rem;
      }
      
      .badge {
        font-size: 0.65rem;
        padding: 1px 4px;
      }
      
    }
    
    /* סמל טעינה */
    /* עיגול מסתובב בצבע כחול, משמש לטעינה */
    .loader {
      display: inline-block;
      width: 30px;
      height: 30px;
      border: 3px solid rgba(0,0,0,0.1);
      border-radius: 50%;
      border-top-color: var(--primary);
      animation: spin 1s ease-in-out infinite;
    }
    
    /* אנימציית סמל טעינה */
    @keyframes spin {
       /* אנימציית סיבוב */
      to { transform: rotate(360deg); }
    }
    
    /* פתקית פרטים */
    /* שולט במיקום ובסגנון של תיבת הפרטים */
    .tooltip {
      position: relative;
      display: inline-block;
    }
    
    /* תיבה כהה שקופצת עם טקסט מרכזי. מוסתרת, נראית שוב בהצבעה */
    .tooltip .tooltip-text {
      visibility: hidden;
      width: 200px;
      background-color: #555;
      color: #fff;
      text-align: center;
      border-radius: 6px;
      padding: 10px;
      position: absolute;
      z-index: 1;
      bottom: 125%;
      left: 50%;
      margin-left: -100px;
      opacity: 0;
      transition: opacity 0.3s;
    }
    
    .tooltip:hover .tooltip-text {
      visibility: visible;
      opacity: 1;
    }
    

    /* הודעה כשאין נתונים */
    .empty-state {
      text-align: center;
      padding: 40px 0;
      color: var(--dark);
    }
    
    /* העיצוב של אייקון שמופיע כאשר אין תוצאות להצגה */
    .empty-state i {
      font-size: 3rem;
      color: #ddd;
      margin-bottom: 15px;
    }

    /* אזור חיפוש מורחב */
    /* סידור גמיש של שדות חיפוש  */
    .search-options {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      align-items: center;
      margin-bottom: 10px;
    }

    /* תא עם פרטי לקוח */
    /* מציג שם וטלפון בעמודה אחת */
    .customer-info {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 5px;
    }

    /* שם לקוח */
    /* שם מודגש */
    .customer-name {
      font-weight: bold;
    }

    /* מספר טלפון של הלקוח */
    /* טקסט מודגש */
    .customer-phone {
      font-size: 0.9rem;
      color: #666;
    }

    /* שם הכלב */
    /* תגית עגולה עם רקע כחול בהיר */
    .dog-name {
      display: inline-block;
      background-color: #f0f7ff;
      border: 1px solid #d0e3ff;
      border-radius: 15px;
      padding: 3px 10px;
      font-size: 0.85rem;
      color: var(--primary-dark);
    }
    
    /* רוחבים קבועים לעמודות - 13 עמודות */
    .table-header th:nth-child(1), .table-body td:nth-child(1) { width: 50px; min-width: 50px; } /* # */
    .table-header th:nth-child(2), .table-body td:nth-child(2) { width: 120px; min-width: 120px; } /* תאריך */
    .table-header th:nth-child(3), .table-body td:nth-child(3) { width: 80px; min-width: 80px; } /* שעה */
    .table-header th:nth-child(4), .table-body td:nth-child(4) { width: 100px; min-width: 100px; } /* מספר אישור */
    .table-header th:nth-child(5), .table-body td:nth-child(5) { width: 150px; min-width: 150px; } /* פרטי לקוח */
    .table-header th:nth-child(6), .table-body td:nth-child(6) { width: 120px; min-width: 120px; } /* שם הכלב */
    .table-header th:nth-child(7), .table-body td:nth-child(7) { width: 120px; min-width: 120px; } /* סוג טיפוח */
    .table-header th:nth-child(8), .table-body td:nth-child(8) { width: 80px; min-width: 80px; } /* מחיר */
    .table-header th:nth-child(9), .table-body td:nth-child(9) { width: 100px; min-width: 100px; } /* סטטוס הזמנה */
    .table-header th:nth-child(10), .table-body td:nth-child(10) { width: 100px; min-width: 100px; } /* סטטוס תשלום */
    .table-header th:nth-child(11), .table-body td:nth-child(11) { width: 120px; min-width: 120px; } /* פנסיון מקושר */
    .table-header th:nth-child(12), .table-body td:nth-child(12) { width: 120px; min-width: 120px; } /* נוצר בתאריך */
    .table-header th:nth-child(13), .table-body td:nth-child(13) { width: 150px; min-width: 150px; } /* ביטול הזמנה */
    
    
    /*...................................*/
    

  </style>
</head>
<body>
  <div class="container">
    <h1>
      <i class="fas fa-cut"></i>
      מערכת ניהול הזמנות טיפוח
    </h1>
    
    <!-- הודעות מערכת -->
     <!-- מוסתרות כברירת מחדל -->
    <div id="alert-success" class="alert alert-success">
      <i class="fas fa-check-circle"></i>
      <span id="success-message"></span>
    </div>
    
    <div id="alert-danger" class="alert alert-danger">
      <i class="fas fa-exclamation-circle"></i>
      <span id="error-message"></span>
    </div>
    
    <!-- רשת כרטיסי סטטיסטיקה -->
    <div class="stats-cards">
        <div class="card">
          <div class="card-icon">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="card-title">הזמנות טיפוח פעילות</div>
          <div class="card-value" id="active-count">0</div>
        </div>
        
        <div class="card">
          <div class="card-icon">
            <i class="fas fa-calendar-day"></i>
          </div>
          <div class="card-title">הזמנות טיפוח להיום</div>
          <div class="card-value" id="today-count">0</div>
        </div>
        
        <div class="card">
          <div class="card-icon">
            <i class="fas fa-calendar-week"></i>
          </div>
          <div class="card-title">הזמנות לשבוע הקרוב</div>
          <div class="card-value" id="week-count">0</div>
        </div>

        <div class="card">
          <div class="card-icon">
            <i class="fas fa-paw"></i>
          </div>
          <div class="card-title">סה"כ כלבים</div>
          <div class="card-value" id="dogs-count">0</div>
        </div>

        <!--  תשלומים ששולמו -->
        <div class="card">
          <div class="card-icon">
            <i class="fas fa-credit-card"></i>
          </div>
          <div class="card-title">הזמנות ששולמו</div>
          <div class="card-value" id="paid-count">0</div>
        </div>

        <!--  תשלומים שלא שולמו -->
        <div class="card">
          <div class="card-icon">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <div class="card-title">הזמנות שלא שולמו</div>
          <div class="card-value" id="unpaid-count">0</div>
        </div>
    </div>
    
    <!-- כלי סינון ושליטה  -->
    <!-- אזור חיפוש מורחב  -->
    <div class="search-options">

      <!-- תיבת הסינון -->
      <div class="search-box">
        <i class="fas fa-search"></i>
        <!-- מפעיל את פונקציית הסינון בכל שינוי בתוכן השדה -->
        <!-- מבצעת סינון לפי הפרמטרים שנבחרו -->
        <input type="text" id="search-input" placeholder="חיפוש..." oninput="filterAppointments()">
      </div>
      
      <!-- סלקטור שקובע לפי איזה שדה תתבצע ההתאמה של הטקסט שהוזן בתיבת החיפוש -->
      <div>
        <select id="search-type" onchange="filterAppointments()">
          <option value="confirmation">לפי מספר אישור</option>
          <option value="customer">לפי שם לקוח</option>
          <option value="dog">לפי שם כלב</option>
          <option value="phone">לפי טלפון</option>
        </select>
      </div>

    </div>

    <!-- אזור סינון מתקדם להזמנות הטיפוח, לפי תאריך, סוג טיפוח, מחיר, סטטוס הזמנה וסטטוס תשלום. וכפתור רענון -->
    <div class="controls">

      <!-- מיכל עוטף לכלי הסינון -->
      <div class="status-filter">

       <!-- סינון לפי תאריך בתוך אזור הסינון -->
        <!-- מחבר בין התווית לשדה לפי המזהה של הרשימה, ומאפשר גם לחיצה על התווית שתפתח את הרשימה -->
        <label for="date-filter">סנן לפי תאריך:</label>
        <!-- הרשימה עם המזהה -->
         <!-- בכל שינוי מופעלת הפונקציה שמבצעת סינון מחדש -->
        <select id="date-filter" onchange="filterAppointments()">
          <!-- ברירת המחדל היא כל ההזמנות -->
          <option value="all">הכל</option>
          <!-- יסנן להזמנות ש־ app.day תואם לתאריך הנוכחי -->
          <option value="today">היום</option>
          <!-- הזמנות שתקופתן מחר בלבד -->
          <option value="tomorrow">מחר</option>
          <!-- הזמנות שהיום שלהן בטווח של 7 ימים קדימה מהיום -->
          <option value="week">השבוע</option>
          <!-- כל ההזמנות עד לסוף החודש הנוכחי -->
          <option value="month">החודש</option>
        </select>
        
        <!-- סינון לפי סוג טיפוח בתוך אזור הסינון -->
         <!-- מחבר בין התווית לשדה לפי המזהה של הרשימה, ומאפשר גם לחיצה על התווית שתפתח את הרשימה -->
        <label for="grooming-type-filter" style="margin-right: 15px;">סוג טיפוח:</label>
        <!-- בכל שינוי בבחירה, מופעלת הפונקציה שמסננת את ההזמנות בהתאם לסוג הטיפוח שנבחר. -->
        <select id="grooming-type-filter" onchange="filterAppointments()">
          <!-- אין סינון לפי סוג טיפוח -->
          <option value="all">הכל</option>
          <!-- יתמלא דינמית מ JS -->
          <!-- שאר האפשרויות נטענות דינמית מהשרת על בסיס הנתונים שקיימים בפועל -->
          <!-- updateGroomingTypeFilter(data) -->
          <!-- עוברת על כל נתוני ההזמנות ומוציאה מהם את כל ערכי סוגי הטיפוח הייחודיים, ממיינת אותם, ואז מוסיפה אותם לרשימה -->
        </select>
        
        <!-- הסינון לפי טווח מחירים בתוך אזור הסינון -->
        <!-- מחבר בין התווית לשדה לפי המזהה של הרשימה, ומאפשר גם לחיצה על התווית שתפתח את הרשימה -->
        <label for="price-filter" style="margin-right: 15px;">טווח מחירים:</label>
        <!-- בכל שינוי בבחירה, מופעלת הפונקציה שמסננת את ההזמנות בהתאם לטווח המחירים שנבחר. -->
        <select id="price-filter" onchange="filterAppointments()">
          <!-- אין סינון לפי מחיר -->
          <option value="all">הכל</option>
          <option value="0-100">עד 100 ₪</option>
          <option value="100-200">100-200 ₪</option>
          <option value="200-300">200-300 ₪</option>
          <option value="300+">מעל 300 ₪</option>
        </select>

        <!-- סינון לפי סטטוס הזמנה בתוך אזור הסינון -->
        <label for="appointment-status-filter" style="margin-right: 15px;">סטטוס הזמנה:</label>
        <!-- בכל שינוי בבחירה, מופעלת הפונקציה שמסננת את ההזמנות בהתאם לסטטוס ההזמנה שנבחר. -->
        <select id="appointment-status-filter" onchange="filterAppointments()">
          <!-- אין סינון לפי סטטוס הזמנה -->
          <option value="all">הכל</option>
          <option value="active">פעילה</option>
          <option value="cancelled">בוטלה</option>
        </select>

        <!-- סינון לפי סטטוס תשלום בתוך אזור הסינון -->
        <label for="payment-status-filter" style="margin-right: 15px;">סטטוס תשלום:</label>
        <!-- בכל שינוי בבחירה, מופעלת הפונקציה שמסננת את ההזמנות בהתאם לסטטוס התשלום שנבחר. -->
        <select id="payment-status-filter" onchange="filterAppointments()">
          <!-- אין סינון לפי סטטוס תשלום -->
          <option value="all">הכל</option>
          <option value="paid">שולם</option>
          <option value="unpaid">לא שולם</option>
        </select>
        
        <!-- כפתור רענון הנתונים בעמוד ניהול ההזמנות -->
        <!-- לוודא שהמידע הוא העדכני ביותר -->
        <!-- מפעיל את הפונקציה שטוענת מחדש את כל הנתונים מהשרת -->
        <button class="btn btn-info" onclick="fetchAppointments()" style="margin-right: 15px;">
          <i class="fas fa-sync"></i>
          רענן
        </button>
      </div>
    </div>
    
    <!-- הצגת תוצאות הסינון -->
    <!-- updateFilterResults(count) -->
    <!-- לאחר סינון כלשהו:

    אם נמצאו תוצאות מוצגת השורה: נמצאו 14 הזמנות

    אם לא מוצג: נמצאו 0 הזמנות -->
    <div id="filter-results" style="margin: 10px 0; font-size: 0.9rem; color: #666; display: none;"></div>
    
    <!-- החלפת הכותרות של הטבלה הקיימת עם כותרות חיצוניות -->
    <div class="table-wrapper" id="table-wrapper">
      <!-- /* כותרות קבועות מחוץ לאזור הגלילה */ -->
      <div class="table-header">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>תאריך</th>
              <th>שעה</th>
              <th>מספר אישור</th>
              <th>פרטי לקוח</th>
              <th>שם הכלב</th>
              <th>סוג טיפוח</th>
              <th>מחיר</th>
              <th>סטטוס הזמנה</th>
              <th>סטטוס תשלום</th>
              <th>הזמנת שהייה מקושרת</th>
              <th>נוצר בתאריך</th>
              <th>ביטול הזמנה</th>
            </tr>
          </thead>
        </table>
      </div>
      
      <!-- גוף הטבלה עם גלילה -->
      <div class="table-body" id="table-body">
        <!--  טבלת הזמנות הטיפוח -->
        <!-- מציג את כל ההזמנות שמתקבלות מהשרת, כולל מידע כמו תאריך, שעה, לקוח, כלב, סוג טיפוח -->
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>תאריך</th>
              <th>שעה</th>
              <th>מספר אישור</th>
              <th>פרטי לקוח</th>
              <th>שם הכלב</th>
              <th>סוג טיפוח</th>
              <th>מחיר</th>
              <th>סטטוס הזמנה</th>
              <th>סטטוס תשלום</th>
              <th>הזמנת שהייה מקושרת</th>
              <th>נוצר בתאריך</th>
              <th>ביטול הזמנה</th>
            </tr>
          </thead>
          <!-- renderAppointments() החלק שמכיל את כל השורות הדינמיות. נבנה באמצעות הפונקציה -->
          <tbody id="appointments-table">
            <tr>
              <!-- שורה אחת בודדת שמכסה את כל העמודות ומציגה טוען נתונים עם עיגול מסתובב -->
              <td colspan="13" class="text-center">
                <div class="loader"></div>
                טוען נתונים...
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- כפתורי ניווט בטבלה -->
    <div class="table-navigation">
      <button class="btn btn-info" onclick="scrollToTop()">
        <i class="fas fa-arrow-up"></i>
        תחילת הטבלה
      </button>
      <button class="btn btn-info" onclick="scrollToBottom()">
        <i class="fas fa-arrow-down"></i>
        סוף הטבלה
      </button>
    </div>
  </div>
  
  <script>
    // ממיר את התאריך לפורמט הצגה 
    // DD/MM/YYYY
    function formatDate(dateStr) {
      // מחלקת את המחרוזת למערך חלקים
      const parts = dateStr.split('-');
      // בונה מחרוזת חדשה על בסיס הסדר של חלקי התאריך
      return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }
    
    // להמיר מחרוזת תאריך לאובייקט date של JS
    function parseDate(dateStr) {
       // מחלק את המחרוזת למערך חלקים
      const parts = dateStr.split('-');
      return new Date(parts[0], parts[1] - 1, parts[2]);
    }
    
    // פונקציה לעיצוב סטטוס הזמנה
    function formatAppointmentStatus(status) {
      if (status === 'active') {
        return '<span class="status-active">פעילה</span>';
      } else if (status === 'cancelled') {
        return '<span class="status-cancelled">בוטלה</span>';
      } else {
        return '<span class="status-cancelled">לא ידוע</span>';
      }
    }
    
    // פונקציה לעיצוב סטטוס תשלום
    function formatPaymentStatus(status) {
      if (status === 'paid') {
        return '<span class="status-paid">שולם</span>';
      } else if (status === 'unpaid') {
        return '<span class="status-unpaid">לא שולם</span>';
      } else {
        return '<span class="status-unpaid">לא ידוע</span>';
      }
    }
    
    // משתנה לשמירת נתוני ההזמנות
    let appointmentsData = [];
    
    // פונקציה לטעינת ההזמנות מהשרת
    function fetchAppointments() {

      // מנקה את הטבלה ומציג שורת טעינה עם אנימציה
      document.getElementById('appointments-table').innerHTML = '<tr><td colspan="13" class="text-center"><div class="loader"></div> טוען נתונים...</td></tr>';
      // שליחת בקשת GET ל־ PHP
      fetch('groomingPanelServer.php')
         // JSON כאשר מתקבלת תשובה, הופכים אותה לאובייקט 
        .then(res => res.json())
        .then(data => {
          // שומר את הנתונים שהתקבלו מהשרת בזיכרון גלובלי
          appointmentsData = data;
          //  מחשב כמה הזמנות יש היום/השבוע הקרוב, וכמה כלבים ייחודיים
          updateStatistics(data);
          // ממלא את הרשימה הנפתחת של סוגי הטיפוח
          updateGroomingTypeFilter(data);
          // מריץ פילטרים קיימים (אם יש חיפוש פעיל או סינון)
          filterAppointments();
        })
        // במקרה של שגיאה 
        .catch(err => {
          // מדפיס את השגיאה לקונסול
          console.error(err);
          // מחליף את שורת הטעינה בהודעת שגיאה בטבלה
          document.getElementById('appointments-table').innerHTML = '<tr><td colspan="13" class="text-center"><i class="fas fa-exclamation-triangle"></i> שגיאה בטעינת נתונים</td></tr>';
          // מציג הודעת שגיאה למשתמש בעמוד
          showAlert('error', 'אירעה שגיאה בטעינת הנתונים. נסה שוב מאוחר יותר.');
        });
    }
    
    // פונקציה לעדכון רשימת סוגי הטיפוח בפילטר
    function updateGroomingTypeFilter(data) {
      // קישור לתיבת הבחירה של סוג הטיפוח מהדף לפי המזהה 
      const groomingTypeFilter = document.getElementById('grooming-type-filter');
      
      // שומר את הבחירה הנוכחית של המשתמש (כדי שנוכל להחזיר אותה אחרי עדכון הרשימה)
      const currentValue = groomingTypeFilter.value;
      
     
      // איסוף כל סוגי הטיפוח הייחודיים מתוך רשימת ההזמנות
      // מבנה נתונים שמכיל רק ערכים ייחודיים, כלומר לא ניתן להכניס אליו את אותו ערך פעמיים
      const groomingTypes = new Set();
      // data עובר על כל אובייקט הזמנה במערך 
      data.forEach(app => {
        // האם בכלל קיים שדה
        // נועד לוודא שלא נוסיף לרשימה ערכים שמכילים רק רווחים
        if (app.grooming_type && app.grooming_type.trim() !== '') {
          // מוסיפים את סוג הטיפוח
          groomingTypes.add(app.grooming_type);
        }
      });
      
      // מיון סוגי הטיפוח
      const sortedTypes = Array.from(groomingTypes).sort();
      
      // שמירת האפשרות הראשונה (הכל)
      const allOption = groomingTypeFilter.options[0];
      
      // ניקוי אפשרויות קיימות
      groomingTypeFilter.innerHTML = '';
      
      // הוספת האפשרות הראשונה בחזרה לרשימה כי מקודם ניקינו את הרשימה
      groomingTypeFilter.appendChild(allOption);
      
      // הוספת אפשרות 'רגיל' אם אין סוגי טיפוח מיוחדים
      if (sortedTypes.length === 0) {
        const defaultOption = document.createElement('option');
        defaultOption.value = 'regular';
        defaultOption.textContent = 'רגיל';
        groomingTypeFilter.appendChild(defaultOption);
      }
      
      //  הוספת האפשרויות הדינמיות לרשימה לפי סוג הטיפוח, לאחר שמיינו את הסוגים
      sortedTypes.forEach(type => {
        const option = document.createElement('option');
        option.value = type;
        option.textContent = type;
        groomingTypeFilter.appendChild(option);
      });
      
      // שחזור הבחירה הקודמת של המשתמש ברשימת הסינון לאחר שעודכנה מחדש
      // בודק האם יש ברשימה החדשה אפשרות עם אותו ערך
      if (currentValue && Array.from(groomingTypeFilter.options).some(opt => opt.value === currentValue)) {
        // מחזיר את הבחירה של המשתמש לתפריט במידה והיא עדיין קיימת ברשימה
        groomingTypeFilter.value = currentValue;
      }
    }
    
    // עדכון לוח הסטטיסטיקות העליון בדף  – כרטיסים שמציגים מידע כולל על הזמנות פעילויות, היום, לשבוע הקרוב, כמות כלבים ייחודיים וסטטוס תשלומים
    function updateStatistics(data) {
      // יוצר אובייקט תאריך עבור היום הנוכחי
      const today = new Date();
      // מאפס את השעה כדי שנוכל להשוות תאריכים לפי יום בלבד 
      today.setHours(0, 0, 0, 0);
      
      // מייצר תאריך עבור עוד 7 ימים קדימה
      const nextWeek = new Date(today);
      nextWeek.setDate(today.getDate() + 7);
      
      // סינון הזמנות פעילות בלבד מכיוון שכל הכריטיסים מתבססים על הזמנות פעילות בלבד
      const activeAppointments = data.filter(app => app.status === 'active');
      
      // מציב את כמות הזמנות הטיפוח הפעילות בכרטיס
      document.getElementById('active-count').textContent = activeAppointments.length;
      
      // ספירת הזמנות להיום (רק פעילות)
      // מסנן את ההזמנות שתאריך ההזמנה שלהן הוא היום
      const todayCount = activeAppointments.filter(app => {
        // פונקציה שממירה את app.day ל - Date 
        const appDate = parseDate(app.day);
        // רק הזמנות טיפוח שהתאריך שלהן שווה להיום, יעברו את הסינון
        return appDate.getTime() === today.getTime();
        // סופר את מספר ההזמנות שפולטרו
      }).length;

      // מציב את מספר ההזמנות של היום בכרטיס הסטטיסטיקה המתאים
      document.getElementById('today-count').textContent = todayCount;
      
      // ספירת הזמנות לשבוע הקרוב (רק פעילות)
      const weekCount = activeAppointments.filter(app => {
        // פונקציה שממירה את app.day ל - Date 
        const appDate = parseDate(app.day);
        // כולל רק תאריכים החל מהיום
        // עד שבוע קדימה
        return appDate >= today && appDate < nextWeek;
         // סופר את מספר ההזמנות שפולטרו
      }).length;
       // מציב את מספר ההזמנות לשבוע הקרוב בכרטיס הסטטיסטיקה המתאים
      document.getElementById('week-count').textContent = weekCount;
      
      // ספירת כלבים ייחודיים לפי dog_id (רק מהזמנות פעילות)
      // מאפשר לאגור רק ערכים ייחודיים
      const uniqueDogs = new Set();
      activeAppointments.forEach(app => {
        if (app.dog_id) {
          uniqueDogs.add(app.dog_id);
        }
      });
       // מציב את כמות הכלבים הייחודים בכרטיס הסטטיסטיקה המתאים
       // במידה ויש שני כלבים שונים עם אותו שם, הוא סופר אותם 
      document.getElementById('dogs-count').textContent = uniqueDogs.size;
      
      // ספירת הזמנות ששולמו (רק מהזמנות פעילות)
      const paidCount = activeAppointments.filter(app => app.payment_status === 'paid').length;
      document.getElementById('paid-count').textContent = paidCount;
      
      // ספירת הזמנות שלא שולמו (רק מהזמנות פעילות)
      const unpaidCount = activeAppointments.filter(app => app.payment_status === 'unpaid').length;
      document.getElementById('unpaid-count').textContent = unpaidCount;
    }
    
    // פונקציה לסינון וסידור הזמנות
    // לסנן את רשימת ההזמנות לפי מילת חיפוש, תאריך, סוג טיפוח, טווח מחירים, סטטוס הזמנה וסטטוס תשלום, ואז להציג אותן בטבלה
    function filterAppointments() {

      // תיבת החיפוש
      const searchTerm = document.getElementById('search-input').value.toLowerCase();
      //  סלקטור שקובע לפי איזה שדה תתבצע ההתאמה של הטקסט שהוזן בתיבת החיפוש 
      const searchType = document.getElementById('search-type').value;
      // רשימת סינון לפי תאריך
      const dateFilter = document.getElementById('date-filter').value;
      // רשימת סינון לפי סוג הטיפוח
      const groomingTypeFilter = document.getElementById('grooming-type-filter').value;
      // רשימת סינון לפי טווח המחירים
      const priceFilter = document.getElementById('price-filter').value;
      // רשימת סינון לפי סטטוס הזמנה
      const appointmentStatusFilter = document.getElementById('appointment-status-filter').value;
      // רשימת סינון לפי סטטוס תשלום
      const paymentStatusFilter = document.getElementById('payment-status-filter').value;
      

      // תאריכים מוגדרים מראש להשוואה עם תאריכי ההזמנות

      // התאריך והשעה הנוכחיים של הדפדפן
      const today = new Date();
      // מאפס את השעה של אותו תאריך לתחילת היום
      today.setHours(0, 0, 0, 0);
      
      // התאריך של מחר שמתבסס על התאריך של היום
      // יוצר עותק חדש של האובייקט today , כדי שלא נשנה את המשתנה המקורי
      const tomorrow = new Date(today);
      tomorrow.setDate(today.getDate() + 1);
      
      // התאריך בעוד שבוע שמתבסס על התאריך של היום
      const nextWeek = new Date(today);
      nextWeek.setDate(today.getDate() + 7);
      
      //  התאריך של החודש הבא שמתבסס על התאריך של היום
      const nextMonth = new Date(today);
      nextMonth.setMonth(today.getMonth() + 1);
      
      // סינון בהתאם לפילטרים
      // עבור הזמנות הטיפוח שהתקבלו מהשרת, עוברים הזמנה אחר הזמנה, ולוקחים אותה רק אם היא עוברת את כל הסינונים שנבחרו בדף
      let filteredData = appointmentsData.filter(app => {

        //  חיפוש טקסטואלי
        // סינון לפי סוג סלקטור חיפוש
        // יוצא מנקודת הנחה שאין התאמה לחיפוש
        let matchesSearch = false;
        
        if (searchType === 'confirmation') {
          //searchTerm  תיבת החיפוש
          // confirmation אם המשתמש בחר לחפש לפי מספר אישור, בודקים האם מחרוזת החיפוש מופיעה בתוך הערך של השדה 
          // בודקים גם עבור שדה ריק בשביל למנוע שגיאה 
          matchesSearch = (app.confirmation || '').toLowerCase().includes(searchTerm);
        } else if (searchType === 'customer') {
          // אם החיפוש לפי שם לקוח, מצרפים את שם פרטי ושם משפחה למחרוזת אחת ומחפשים את מחרוזת החיפוש בה
          const fullName = `${app.first_name || ''} ${app.last_name || ''}`.toLowerCase();
          matchesSearch = fullName.includes(searchTerm);
        } else if (searchType === 'dog') {
          // dog_name אם החיפוש לפי שם כלב, מחפשים את המונח בשדה
          matchesSearch = (app.dog_name || '').toLowerCase().includes(searchTerm);
          // phone אם החיפוש לפי טלפון, בודקים את המונח בשדה
        } else if (searchType === 'phone') {
          matchesSearch = (app.phone || '').toLowerCase().includes(searchTerm);
        }
        
        // אם אין מונח חיפוש, אז הזמנת הטיפוח הנוכחית עוברת את הסינון הטקסטואלי
        if (searchTerm === '') {
          matchesSearch = true;
        }
        
        // סינון הזמנות טיפוח לפי תאריך
        // ממיר את הערך app.day לאוביקט Date שיהיה ניתן להשוות
        const appDate = parseDate(app.day);
        // מגדיר תחילה שההזמנה מתאימה כברירת מחדל כדי למנוע חסימה אם לא נבחר סינון תאריכים בכלל
        let matchesDate = true;
        
        // בודק אם ההזמנה היא בדיוק היום
        if (dateFilter === 'today') {
          matchesDate = appDate.getTime() === today.getTime();
          // בודק אם ההזמנה היא מחר בדיוק
        } else if (dateFilter === 'tomorrow') {
          matchesDate = appDate.getTime() === tomorrow.getTime();
          // בודק אם ההזמנה בתוך 7 ימים הקרובים
        } else if (dateFilter === 'week') {
          matchesDate = appDate >= today && appDate < nextWeek;
          // בודק אם ההזמנה בין היום ל־30 יום קדימה
        } else if (dateFilter === 'month') {
          matchesDate = appDate >= today && appDate < nextMonth;
        }
        
        // סינון לפי סוג טיפוח
         // מגדיר תחילה שההזמנה מתאימה כברירת מחדל כדי למנוע חסימה אם לא נבחר סינון סוג טיפוח בכלל
        let matchesGroomingType = true;
        //  אם המשתמש בחר ערך מסוים ולא את "הכל", נמשיך לסנן
        if (groomingTypeFilter !== 'all') {
           // מצב מיוחד עבור 'רגיל' - כאשר שדה סוג הטיפוח ריק או לא קיים
          if (groomingTypeFilter === 'regular') {
           
            matchesGroomingType = !app.grooming_type || app.grooming_type.trim() === '';
            // משווים את סוג הטיפוח שמופיע בהזמנה בדיוק מול מה שנבחר
          } else {
            matchesGroomingType = app.grooming_type === groomingTypeFilter;
          }
        }
        
        // סינון לפי טווח מחירים
        // מגדיר תחילה שההזמנה מתאימה כברירת מחדל כדי למנוע חסימה אם לא נבחר סינון לפי טווח מחירים
        let matchesPrice = true;
        //  אם המשתמש בחר ערך מסוים ולא את "הכל", נמשיך לסנן
        if (priceFilter !== 'all') {
          const price = parseInt(app.grooming_price) || 0;
          
          // מתאים למחירים מ-0 עד 100 כולל
          if (priceFilter === '0-100') {
            matchesPrice = price >= 0 && price <= 100;
            // בין 101 ל־200 כולל
          } else if (priceFilter === '100-200') {
            matchesPrice = price > 100 && price <= 200;
            // בין 201 ל־300 כולל
          } else if (priceFilter === '200-300') {
            matchesPrice = price > 200 && price <= 300;
            // רק מחירים מעל 300
          } else if (priceFilter === '300+') {
            matchesPrice = price > 300;
          }
        }
        
        // סינון לפי סטטוס הזמנה
        let matchesAppointmentStatus = true;
        if (appointmentStatusFilter !== 'all') {
          matchesAppointmentStatus = app.status === appointmentStatusFilter;
        }
        
        // סינון לפי סטטוס תשלום
        let matchesPaymentStatus = true;
        if (paymentStatusFilter !== 'all') {
          matchesPaymentStatus = app.payment_status === paymentStatusFilter;
        }
        
        // אם כל הסינונים מצליחים ההזמנה נשמרת
        return matchesSearch && matchesDate && matchesGroomingType && matchesPrice && 
               matchesAppointmentStatus && matchesPaymentStatus;
      });
      
      // מיון לפי תאריך ושעה
      // a מייצג הזמנה אחת
      // b מייצג הזמנה אחרת
      // מקבלת פונקציה עם שני פרמטרים ומשווה ביניהם לפי תאריך כדי לקבוע איזה מהם יבוא קודם
      //  אם חזר ערך שלילי, אז הזמנה a תבוא קודם
      // אם חזר 0, אז לא משנים את הסדר
      // אם חזר ערך חיובי, אז הזמנה b תבוא קודם
      filteredData.sort((a, b) => {
        // Date ממיר את השדה "יום" של כל הזמנה לאובייקט 
        const dateA = parseDate(a.day);
        const dateB = parseDate(b.day);
        
        // אם התאריכים שונים, מחזיר את ההפרש ביניהם — מה שמאפשר מיון עולה (מהתאריך המוקדם ביותר לאחרון)
        if (dateA.getTime() !== dateB.getTime()) {
          return dateA - dateB;
        }
        
        // אם התאריכים שווים, עוברים להשוות בין השעות
        return a.time.localeCompare(b.time);
      });
      
      // מציג את ההזמנות המסוננות בטבלה
      renderAppointments(filteredData);
      
      // מעדכן את מספר התוצאות שנמצאו
      updateFilterResults(filteredData.length);
    }
    
    // פונקציה להצגת מספר תוצאות החיפוש
    function updateFilterResults(count) {
      // מוצא את אלמנט הטקסט שבו יוצג מספר התוצאות
      const resultsEl = document.getElementById('filter-results');
      // בודק שהאלמנט באמת קיים בדף למניעת שגיאה
      if (resultsEl) {
        // משנה את תוכן הטקסט של האלמנט לתוצאה
        resultsEl.textContent = `נמצאו ${count} הזמנות`;
        // מוודא שהאלמנט גלוי
        resultsEl.style.display = 'block';
      }
    }
    
    // פונקציה להצגת ההזמנות בטבלה באופן דינמי לאחר סינון או טעינה
    function renderAppointments(data) {
      // מוצא את גוף הטבלה שבו יוצגו ההזמנות
      const tbody = document.getElementById('appointments-table');
      // איפוס הטבלה
      tbody.innerHTML = '';
      
      // תצוגה ריקה אם אין תוצאות
      if (data.length === 0) {
        // אם אין תוצאות מוסיף שורה אחת בטבלה עם אייקון וטקסט המודיעים שאין הזמנות
        tbody.innerHTML = `
          <tr>
            <td colspan="13" class="empty-state">
              <i class="fas fa-calendar-times"></i>
              <p>לא נמצאו הזמנות מתאימות</p>
            </td>
          </tr>
        `;
        return;
      }

      
      // עובר על כל ההזמנות ויוצר שורה בטבלה עבור כל הזמנה
      // row = אובייקט של הזמנה אחת מתוך המערך
      // index = המיקום של אותה שורה במערך
      data.forEach((row, index) => {
        // בדיקה אם התאריך הוא היום
        const appDate = parseDate(row.day);
        // התאריך והשעה הנוכחיים של הדפדפן
        const today = new Date();
         // מאפס את השעה של אותו תאריך לתחילת היום
        today.setHours(0, 0, 0, 0);
        // משווה את תאריך ההזמנה מול התאריך של היום 
        const isToday = appDate.getTime() === today.getTime();
        
        // בדיקה אם התאריך הוא מחר
        const tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1);
        // משווה את תאריך ההזמנה מול התאריך של מחר 
        const isTomorrow = appDate.getTime() === tomorrow.getTime();
        
        // לעצב את התאריך לפורמט קריא ונוח להצגה
        const formattedDate = formatDate(row.day);
        
        // הכנת תצוגת תאריך עם תגית מתאימה
        // מוסיף תגית עם טקסט "היום" או "מחר" בצבע שונה בהתאם
        let dateDisplay = formattedDate;
        if (isToday) {
          dateDisplay = `
            <div class="date-cell">
              <span class="badge badge-primary">היום</span>
              <span>${formattedDate}</span>
            </div>
          `;
        } else if (isTomorrow) {
          dateDisplay = `
            <div class="date-cell">
              <span class="badge badge-tomorrow">מחר</span>
              <span>${formattedDate}</span>
            </div>
          `;
        } else {
          dateDisplay = `<div class="date-cell"><span>${formattedDate}</span></div>`;
        }
        
        // יצירת שורת הזמנה בטבלה
        /*
         מספר רץ (#)
          תאריך
          שעה
          קוד אישור
          שם וטלפון לקוח
          שם כלב
         סוג טיפוח 
          מחיר 
          סטטוס הזמנה 
          סטטוס תשלום 
          הזמנת שהייה מקושרת  
          תאריך יצירה
          כפתור ביטול + כפתור מידע עם tooltip
        */
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${index + 1}</td>
          <td>${dateDisplay}</td>
          <td>${row.time}</td>
          <td>${row.confirmation}</td>
          <td>
            <div class="customer-info">
              <div class="customer-name">${row.first_name || ''} ${row.last_name || ''}</div>
              <div class="customer-phone">${row.phone || '-'}</div>
            </div>
          </td>
          <td>
            ${row.dog_name ? `<span class="dog-name">${row.dog_name}</span>` : '-'}
          </td>
          <td>${row.grooming_type || 'רגיל'}</td>
          <td>${row.grooming_price ? row.grooming_price + ' ₪' : '-'}</td>
          <td>${formatAppointmentStatus(row.status)}</td>
          <td>${formatPaymentStatus(row.payment_status)}</td>
          <td>${row.connected_reservation_id ? '#' + row.connected_reservation_id : '-'}</td>
          <td>${row.created_at}</td>
          <td class="actions">
            <button class="btn btn-danger" onclick="cancelAppointment('${row.confirmation}', this)">
              <i class="fas fa-times"></i>
              ביטול
            </button>
            <div class="tooltip">
              <button class="btn btn-info">
                <i class="fas fa-info-circle"></i>
              </button>
              <span class="tooltip-text">
                פרטי הזמנה ${row.confirmation}<br>
                בעלים: ${row.first_name || ''} ${row.last_name || ''}<br>
                טלפון: ${row.phone || '-'}<br>
                כלב: ${row.dog_name || '-'}<br>
                תאריך: ${formattedDate}<br>
                שעה: ${row.time}<br>
                סטטוס הזמנה: ${row.status || '-'}<br>
                סטטוס תשלום: ${row.payment_status || '-'}<br>
                הזמנת שהייה מקושרת: ${row.connected_reservation_id ? '#' + row.connected_reservation_id : '-'}
              </span>
            </div>
          </td>
        `;
        
        // בכל סיבוב של הלולאה יוצרים שורה חדשה עם נתוני הזמנת הטיפוח ומוסיפים אותה לגוף הטבלה 
        tbody.appendChild(tr);
      });
    }
    
    // פונקציה לביטול הזמנת טיפוח
    // מבצעת בדיקה מול המשתמש, שולחת בקשה לשרת ומעדכנת את הטבלה בהתאם לתוצאה
    // confirmation – מזהה ההזמנה שצריך לבטל
    // btn – הכפתור שנלחץ (כדי לשנות את המראה שלו ולהפוך אותו לטעינה)
    function cancelAppointment(confirmation, btn) {

      // שואל את המשתמש אם הוא בטוח שהוא רוצה לבטל את ההזמנה
      if (!confirm('האם אתה בטוח שברצונך לבטל את ההזמנה?')) return;
      
      // הפוך את הכפתור ללואדר
      // מחליף את תוכן הכפתור לספינר
      btn.innerHTML = '<div class="loader"></div>';
      // משבית את הכפתור כדי למנוע לחיצה כפולה
      btn.disabled = true;
      
      setTimeout(() => {
        // שולח בקשת POST לשרת 
        // JSON הנתונים נשלחים כ 
      fetch('cancelAppointmentServer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ confirmation })
      })
      .then(res => res.json())
      .then(data => {
        // אם השרת מחזיר תשובה חיובית, מוצגת הודעת הצלחה, ורשימת ההזמנות מתרעננת
        if (data.success) {
          showAlert('success', data.message);
          fetchAppointments(); // לשלוף מחדש את רשימת ההזמנות מהשרת ולהציג אותן בטבלה

           // אם השרת מחזיר תשובה שלילית, מוצגת הודעת שגיאה והכפתור מוחזר למצבו הרגיל
        } else {
          // מוצגת הודעת שגיאה והכפתור מוחזר למצבו הרגיל
          showAlert('error', 'שגיאה: ' + data.error);
          // החזר את הכפתור למצב רגיל
          btn.innerHTML = '<i class="fas fa-times"></i> ביטול';
          btn.disabled = false;
        }
      })
      // טיפול בשגיאה כללית
      // במקרה של תקלה בבקשה (למשל אין חיבור לשרת)
      .catch(err => {
        console.error(err);
        showAlert('error', 'שגיאה בביטול ההזמנה.');
        // החזר את הכפתור למצב רגיל
        btn.innerHTML = '<i class="fas fa-times"></i> ביטול';
        btn.disabled = false;
      });
      }, 1500); // השהיה של 1500 מילישניות (שנייה וחצי)
    }
    
    // פונקציה להצגת הודעה
    // מציגה הודעת מערכת למשתמש, בהתאם לתוצאה של פעולה כלשהי כמו ביטול הזמנה, שגיאה בשרת וכו
    function showAlert(type, message) {
      // התיבה הירוקה
      const alertSuccess = document.getElementById('alert-success');
      // התיבה האדומה
      const alertDanger = document.getElementById('alert-danger');
      
      // אם זו הודעת הצלחה
      if (type === 'success') {
        // מכניס את הטקסט לתוך האלמנט של הודעת ההצלחה
        document.getElementById('success-message').textContent = message;
        // מציג את התיבה הירוקה
        alertSuccess.style.display = 'block';
        // מוודא שהתיבה האדומה מוסתרת
        alertDanger.style.display = 'none';
        
        // הסתר אחרי 5 שניות
        setTimeout(() => {
          alertSuccess.style.display = 'none';
        }, 5000);
      } else {
        // מכניס את הטקסט לתוך האלמנט של הודעת השגיאה
        document.getElementById('error-message').textContent = message;
        // מציג את התיבה האדומה
        alertDanger.style.display = 'block';
         // מוודא שהתיבה הירוקה מוסתרת
        alertSuccess.style.display = 'none';
        
        // הסתר אחרי 5 שניות
        setTimeout(() => {
          alertDanger.style.display = 'none';
        }, 5000);
      }
    }

    // פונקציה לגלילה חלקה לתחילת הטבלה
    function scrollToTop() {
      // מוצא את האלמנט של גוף הטבלה עם הגלילה
      const tableBody = document.getElementById('table-body');
      // בודק שהאלמנט באמת קיים בדף
      if (tableBody) {
        //  גולל את התוכן למעלה
        tableBody.scrollTo({
          top: 0,
          // גורם לגלילה להיות חלקה ואיטית
          behavior: 'smooth'
        });
      }
    }

    // פונקציה לגלילה חלקה לסוף הטבלה
    function scrollToBottom() {
      // מוצא את האלמנט של גוף הטבלה עם הגלילה
      const tableBody = document.getElementById('table-body');
      // בודק שהאלמנט באמת קיים בדף
      if (tableBody) {
        // מביא את הגלילה לנקודה הכי תחתונה בתוכן
        tableBody.scrollTo({
          top: tableBody.scrollHeight,
          // גורם לגלילה להיות חלקה ואיטית
          behavior: 'smooth'
        });
      }
    }
    
    // טעינת הזמנות בעת טעינת העמוד
    // תשלוף את ההזמנות מהשרת ותציג אותן בטבלה
    document.addEventListener('DOMContentLoaded', function() {
      // טעינת ההזמנות
      fetchAppointments();
    });
  </script>
</body>
</html>