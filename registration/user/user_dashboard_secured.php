<?php include '../../header.php'; ?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>האזור האישי שלי</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    :root {
    --primary-color: #3498db;       /* כחול */
    --secondary-color: #2980b9;     /* כחול כהה  */
    --accent-color: #f1c40f;        /* צהוב */
    --text-color: #2c3e50;          /* צבע טקסט כהה */
    --light-gray: #ecf0f1;          /* אפור בהיר לרקע */
    --border-radius: 12px;          /* פינות מעוגלות */
    }
    
    /* איפוס עיצוב ברירת מחדל של הדפדפן */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f5f7fa;
      color: var(--text-color); /* כחול */
      line-height: 1.6;
      padding: 20px;              /* ריווח פנימי */
      direction: rtl;             /* תצוגה מימין לשמאל */
    }
    
      /* עיצוב הקונטיינר המרכזי */
    .container {
      max-width: 800px;   /* מגביל את הרוחב */
     margin: 40px auto;  /* מרכז את הקונטיינר */
    }
    
    /* עיצוב תיבת האזור האישי */
    .dashboard {
    background: white;                                /* רקע לבן */
    padding: 30px;                                     /* ריווח פנימי */
    border-radius: var(--border-radius);              /* פינות מעוגלות */
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);          /* צל */
    }
    
    .dashboard-header {
      margin-bottom: 30px;
      border-bottom: 2px solid var(--light-gray); /* קו תחתון להפרדה */
      padding-bottom: 15px;
    }
    
    .dashboard-header h2 {
      font-size: 28px; /* כותרת גדולה */
      color: var(--text-color);
      margin-bottom: 10px;
    }
    
    .dashboard-header p {
      color: #7f8c8d; /* אפור בהיר לטקסט משנה */
      font-size: 16px;
    }
    
    /* רשת כפתורי הפעולה */
    .actions-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr); /* שתי עמודות */
      gap: 20px;                             /* ריווח בין כפתורים */
      margin-top: 25px;
    }
    
    /* כפתור פעולה  */
    .action-button {
      display: flex; /* מאפשר סידור של אייקון וטקסט בשורה אחת */
      align-items: center; /* ממרכז את התוכן אנכית בתוך הכפתור */
      justify-content: flex-start; /* מיישר את התוכן לשמאל, כך שהאייקון יופיע לפני הטקסט */
      padding: 20px; /* ריווח פנימי מכל הצדדים */
      height: 100px;
      background: white; /* רקע לבן */
      color: var(--text-color);
      border-radius: var(--border-radius);
      text-decoration: none; /* מסיר קו תחתון */
      font-weight: 600;
      font-size: 16px;
      border: 1px solid var(--light-gray); /* מסגרת אפורה */
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05); /* צל */
    }
    
    /* עיצוב אייקון בכפתור */
    .action-button i {
      font-size: 24px; /* גודל האייקון */
      margin-left: 15px; /* ריווח מימין לטקסט */
      color: var(--primary-color);
      transition: all 0.3s ease;
      width: 30px;
      text-align: center;  /* ממרכז את האייקון במסגרת */
    }
    
    .action-button:hover {
      transform: translateY(-5px); /* תזוזה כלפי מעלה */
      box-shadow: 0 10px 20px rgba(0,0,0,0.1); /* צל מוגבר */
      border-color: var(--primary-color); /* שינוי צבע מסגרת */
    }
    
    .action-button:hover i {
      transform: scale(1.2);  /* הגדלה של האייקון */
    }
    
    /* אפקט בעת לחיצה על כפתור */
    .action-button:active {
      transform: translateY(0) scale(0.98); /* הכפתור יורד מעט ומוקטן */
    }
    
    /* עיצוב ייחודי לכפתור התנתקות */
    .logout-button {
      background-color: #f8f9fa; /* רקע אפור בהיר */
      color: #7f8c8d;  /* טקסט אפור כהה יותר */
    }
    
    .logout-button i {
      color: #e74c3c; /* אדום */
    }
    
    @media (max-width: 600px) {
      .actions-grid {
        grid-template-columns: 1fr; /* עמודה אחת בלבד */
      }
      
      .action-button {
        height: 80px; /* כפתורים קטנים יותר */
      }
      
      .dashboard {
        padding: 20px;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="dashboard">
      <div class="dashboard-header">
        <h2>ברוך הבא לאזור האישי שלך </h2>
        <p>כאן תוכל למצוא פעולות נוספות לניהול החשבון</p>
      </div>
      
      <!-- רשת כפתורי פעולה, מוצגת בשתי עמודות -->
      <div class="actions-grid">

        <!-- כפתור לרישום כלב חדש -->
        <a href="../../dog_registration/user/dog_registration.php" class="action-button">
          <i class="fa-solid fa-dog fa-xl" style="color:rgb(224, 142, 205);"></i>
          <span>רישום כלב חדש למערכת</span>
        </a>

        <!-- כפתור לעדכון פרטי הכלב הפעיל -->
        <a href="../../update_dog_profile/user/update_active_dog_profile.php" class="action-button">
          <i class="fa-solid fa-pencil fa-xl" style="color: #63E6BE;"></i>
          <span>עדכון פרטי כלב פעיל</span>
        </a>

        <!-- כפתור להחלפת הכלב הפעיל -->
        <a href="../../dog_registration/user/select_active_dog.php" class="action-button">
          <i class="fa-solid fa-repeat fa-xl" style="color:rgb(251, 126, 1);" ></i>
          <span>החלפת כלב פעיל</span>
        </a>

        <!-- כפתור לעדכון פרטים אישיים של המשתמש -->
        <a href="../../registration/user/update_User_profile.php" class="action-button">
          <i class="fas fa-user-edit"></i>
          <span>עדכון פרטים אישיים</span>
        </a>

        <!-- כפתור להוספת משוב על השירות -->
        <a href="../../feedback/user/feedback_form.html" class="action-button">
          <i class="fa-solid fa-clipboard-list" style="color: #FFD43B;"></i>
          <span>הוסף משוב על השירות</span>
        </a>

        <!-- כפתור התנתקות -->
        <a href="../logout.php" class="action-button logout-button">
          <i class="fas fa-sign-out-alt"></i>
          <span>התנתק</span>
        </a>
      </div>
    </div>
  </div>
</body>
</html>