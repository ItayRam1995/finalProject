<?php 
session_start();
include '../../header.php'; 
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>סנכרון עם Google Calendar</title>
  <style>
    
    /* עיצוב גוף הדף - רקע סגול */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* מסגול בהיר לכהה */
      min-height: 100vh; /* גובה מינימלי של 100% מהמסך */
    }
    
    /* מיכל ראשי - קופסה לבנה במרכז העמוד */
    .container {
      max-width: 800px;
      margin: 0 auto; /* מרכוז אוטומטי */
      background: white;
      padding: 40px;
      border-radius: 15px; /* פינות מעוגלות */
      box-shadow: 0 10px 30px rgba(0,0,0,0.3); /* צל  */
      text-align: center;
    }
    
    /* אזור כותרת העמוד */
    .header {
      margin-bottom: 40px;
    }
    .header h1 {
      color: #333;
      font-size: 2.5em;
      margin-bottom: 10px;
    }
    .header p {
      color: #666;
      font-size: 1.2em;
      margin: 0;
    }
    
    /* רשת של כרטיסי אפשרויות - מתאימה את עצמה למסך */
    .options {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); /* כמות עמודות מקסימלית שנכנסת בעמוד */
      gap: 30px; /* רווח בין הכרטיסים */
      margin: 40px 0;
    }
    
    /* עיצוב כל כרטיס אפשרות */
    .option-card {
      background: #f8f9fa; /* רקע אפור בהיר */
      padding: 30px;
      border-radius: 15px;
      border: 2px solid #e9ecef;
      transition: all 0.3s ease; 
      position: relative;
    }
    
    /* אפקט ריחוף על כרטיס */
    .option-card:hover {
      transform: translateY(-5px); /* הרמה קלה של הכרטיס */
      box-shadow: 0 10px 25px rgba(0,0,0,0.15); /* הגברת הצל */
      border-color: #007bff; /* שינוי צבע המסגרת לכחול */
    }
    
    /* אייקון בכרטיס */
    .option-icon {
      font-size: 3em;
      margin-bottom: 20px;
    }
    
    /* כותרת הכרטיס */
    .option-title {
      font-size: 1.5em;
      font-weight: bold;
      color: #333;
      margin-bottom: 15px;
    }
    
    /* תיאור הכרטיס */
    .option-description {
      color: #666;
      margin-bottom: 25px;
      line-height: 1.6;
    }
    
    /* עיצוב כפתורים */
    .btn {
      display: inline-block;
      background-color: #4285f4; /* כחול  */
      color: white;
      padding: 15px 30px;
      text-decoration: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
    }
    
    /* אפקט ריחוף על כפתור */
    .btn:hover {
      background-color: #3367d6; /* כחול כהה יותר */
      transform: translateY(-2px); /* הרמה קלה */
      box-shadow: 0 5px 15px rgba(66, 133, 244, 0.4); /* צל כחול */
    }
    
    /* כפתור משני בצבע תכלת */
    .btn-info {
      background-color: #17a2b8;
    }
    .btn-info:hover {
      background-color: #138496;
      box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
    }
    
    /* הודעת אזהרה צהובה */
    .alert {
      background-color: #fff3cd;
      border: 1px solid #ffeaa7;
      color: #856404;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 30px;
      text-align: center;
    }
    
    /* אייקון בהודעת האזהרה */
    .alert-icon {
      font-size: 2em;
      margin-bottom: 10px;
    }
    
    /* עיצוב רספונסיבי למובייל */
    @media (max-width: 768px) {
      .container {
        margin: 10px;
        padding: 20px;
      }
      .options {
        grid-template-columns: 1fr; /* עמודה אחת במובייל */
        gap: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- כותרת ראשית של העמוד -->
    <div class="header">
      <h1>🗓️ מערכת Google Calendar</h1>
      <p>סנכרן את ההזמנות שלך עם Google Calendar</p>
    </div>
    
    <!-- הודעת אזהרה מותנית - מוצגת רק אם המשתמש לא מחובר לגוגל -->
    <?php if (!isset($_SESSION['access_token'])): ?>
    <div class="alert">
      <div class="alert-icon">⚠️</div>
      <strong>שים לב:</strong> עליך להתחבר עם חשבון Google שלך כדי להשתמש בתכונות המתקדמות
    </div>
    <?php endif; ?>
    
    <!-- רשת של כרטיסי אפשרויות -->
    <div class="options">
      <!-- כרטיס ראשון - סנכרון הזמנות -->
      <div class="option-card">
        <div class="option-icon">🔄</div>
        <div class="option-title">סנכרון ההזמנות</div>
        <div class="option-description">
          העבר את כל תורי הטיפוח והזמנות הפנסיון שלך ל-Google Calendar
          <br><br>
          • העברה אוטומטית של כל ההזמנות<br>
        </div>
        <!-- כפתור מוביל לדף ההרשאה -->
        <a href="authorize.php" class="btn">התחבר עם Google והעבר הזמנות</a>
      </div>
      
      <!-- כרטיס שני - קישור ישיר לגוגל קלנדר -->
      <div class="option-card">
        <div class="option-icon">🌐</div>
        <div class="option-title">פתח ב-Google Calendar</div>
        <div class="option-description">
          עבור ישירות ל-Google Calendar לצפייה מלאה ועריכה
        </div>
        <!-- קישור חיצוני שנפתח בטאב חדש -->
        <a href="https://calendar.google.com" target="_blank" class="btn btn-info">פתח Google Calendar</a>
      </div>
    </div>
    
  </div>
</body>
</html>