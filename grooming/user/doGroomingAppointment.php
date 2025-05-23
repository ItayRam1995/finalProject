<?php include '../../header.php'; ?>
<!DOCTYPE html>

<html dir="rtl" lang="he">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>הזמנת טיפוח לכלבך</title>
<link href="https://fonts.googleapis.com/css2?family=Varela+Round&amp;display=swap" rel="stylesheet"/>
<style>

    /* מגדיר פונט אחיד, רקע מדורג, ריווח פנימי ויישור למרכז */
    body {
      font-family: 'Varela Round', sans-serif;
      margin: 0;
      padding: 40px 20px;
      background: linear-gradient(to bottom left, #f0f8ff, #ffffff);
      display: flex;
      justify-content: center;
    }

    /* מיכל טופס ההזמנה */
    /* רקע לבן, גבולות מעוגלים, צל, ריווח פנימי ואנימציית הופעה */
    .booking-section {
      max-width: 800px;
      width: 100%;
      background: white;
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      animation: fadeIn 1s ease;
    }
    /* אפקט הופעה מלמטה למעלה */
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }

    /* כותרת ראשית של הטופס */
    /* יישור למרכז, צבע כחול */
    h2 {
      text-align: center;
      color: #007bff;
      margin-bottom: 20px;
    }

    /* תיבת תיאור סוג הטיפוח */
    /* רקע תכלת, גבול מעוגל עם יישור טקסט */
    .service-box {
      display: flex;
      gap: 15px;
      align-items: center;
      padding: 15px;
      border-radius: 12px;
      background: #f0f8ff;
      border: 1px solid #cce5ff;
      margin-bottom: 25px;
    }

    /* תצוגת ימי טיפוח ושעות זמינות */
    /* .calendar – המיכל שמכיל את כפתורי התאריכים הזמינים */
    /* .time-slots – המיכל שמכיל את כפתורי שעות הטיפוח (בוקר / צהריים) */
    /* פריסה בשורות, ריווח בין אלמנטים, יישור למרכז */
    .calendar, .time-slots {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: center;
    }

    /* כל כפתור תאריך/שעה */
    .calendar button, .time-slots div {
      padding: 10px 14px;
      border: none;
      border-radius: 8px;
      /* צבע רקע תכלת בהיר */
      background-color: #e3f2fd;
      font-weight: bold;
      transition: all 0.2s;
      cursor: pointer;
    }

    /* צבע רקע משתנה, אנימציית התרחבות */
    .calendar button:hover, .time-slots div:hover {
      background-color: #bbdefb;
      transform: scale(1.05);
    }

    /* תאריך נבחר */
    /* רקע כחול כהה וטקסט לבן */
    .calendar button.active {
      background-color: #007bff;
      color: white;
    }

    /* תאריך של היום */
    /* גבול ירוק סביב הכפתור */
    .calendar button.today {
      border: 2px solid #28a745;
    }

    /* כפתורים חסומים (שעה תפוסה או תאריך חסום) */
    /* אפור, לא לחיץ, שקיפות 70% */
    .calendar button.disabled,
    .time-slots div.disabled {
      background-color: #999 !important;
      color: white;
      cursor: not-allowed;
      opacity: 0.7;
    }

    /* שעת טיפוח שנבחרה */
    /* רקע ירוק וטקסט לבן */
    .time-slots div.selected {
      background-color: #28a745;
      color: white;
    }

    /* כפתור אשר הזמנה */
    /* רקע כחול, צבע לבן */
    .submit-button {
      display: inline-block;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 12px 24px;
      font-size: 16px;
      font-weight: bold;
      margin-top: 20px;
      transition: background-color 0.3s;
      cursor: pointer;
    }


    .submit-button:hover {
      background-color: #0056b3;
    }

    /* כשהכפתור חסום */
    /* אפור, לא לחיץ, */
    .submit-button:disabled {
      background-color: #cccccc;
      cursor: not-allowed;
      opacity: 0.7;
    }

    @media (max-width: 600px) {
      .calendar, .time-slots {
        justify-content: flex-start;
      }
      .service-box {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  
    /* למקם את השעות בצד ימין של השורה */
    .time-slots {
      justify-content: right;
    }
    
    /* מגביל את הרוחב המרבי של הטופס ל 1200 */
    .booking-section {
      max-width: 1200px !important;
    }
    
    /* לוודא שהחצים תמיד בתוך התחום הלבן */
    /* מיכל החיצוני ללוח השנה */
    .calendar-wrapper {
      width: 100%;
      background-color: #f8f9fa;
      border-radius: 10px;
      padding: 10px;
      margin: 15px 0;
      position: relative;
      overflow: hidden; /* מונע גלישה */
    }

    /* עוטף את לוח השנה והחצים */
    /* יישור אופקי, מאפשר מרווח לחצים מימין ושמאל */
    .calendar-container {
      display: flex;
      align-items: center;
      width: 100%;
      /* ממורכז אופקית עם שוליים שווים משני הצדדים */
      margin: 0 auto;
      position: relative;
    }

    /* תצוגת התאריכים עצמם */
    /* תצוגה אופקית ללא גלישה */
    .calendar-inner {
      display: flex;
      /* מונע ירידה לשורות נוספות */
      flex-wrap: nowrap;
      justify-content: center;
      flex: 1;
      margin: 0 auto;
      padding: 0 10px;
    }

    /* חצים לניווט בין ימים */
    /* גובה ורוחב קבועים, עיצוב עגול, רקע תכלת, מופיע בצדדים של לוח השנה */
    .week-nav {
      position: relative;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 5px;
      flex-shrink: 0;
      background-color: #e6f2ff;
      border-radius: 8px;
      cursor: pointer;
      /* קובע את סדר השכבות של אלמנטים על המסך בציר האנכי */
      z-index: 2;
    }

    /* כפתורי התאריכים */
    .calendar-inner button {
      flex: 0 0 auto;
      padding: 8px 12px;
      border: none;
      border-radius: 8px;
      background-color: #e3f2fd;
      font-weight: bold;
      font-size: 0.9em;
      transition: all 0.2s;
      cursor: pointer;
      white-space: nowrap;
      margin: 0 2px;
    }

    /* כפתור התאריך מתנפח כשעומדים מעליו עם העכבר והצבע שלו משתנה */
    .calendar-inner button:hover {
      background-color: #bbdefb;
      transform: scale(1.05);
    }

    /* כאשר התאריך נבחר הוא נצבע בכחול עם כיתוב לבן */
    .calendar-inner button.active {
      background-color: #007bff;
      color: white;
    }

    /* עוטף את שני החלקים: לוח וטופס */
    /* פריסה אופקית עם רווח בין הצדדים */
    .page-container {
      display: flex;
      gap: 30px;
      max-width: 1400px;
      margin: 0 auto;
      justify-content: space-between;
    }
    
    /* הצד הימני – רשימת הזמנות פעילות */
    /* עיצוב כרטיסים, תצוגת גלילה, צל, גבולות מעוגלים */
    .reservations-section {
      flex: 0 0 300px;
      background: white;
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      max-height: 600px;
      /* אם התוכן גבוה יותר מהאלמנט עצמו – תתווסף גלילה אנכית אוטומטית */
      overflow-y: auto;
    }
    
    /* כרטיס של הזמנת פנסיון פעילה */
    /* רקע תכלת, גבול כחול מצד שמאל */
    .reservation-card {
      background-color: #f0f8ff;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      border-left: 4px solid #007bff;
      cursor: pointer;
      transition: all 0.2s;
    }
    
    .reservation-card:hover {
      background-color: #e3f2fd;
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    /* כרטיס שנבחר */
    /* רקע כחול בהיר יותר, גבול ירוק */
    .reservation-card.selected {
      background-color: #cce5ff;
      border-left: 4px solid #28a745;
    }
    
    /* תאריך ההזמנה בכרטיס */
    /* מודגש, בצבע כחול */
    .reservation-date {
      font-weight: bold;
      color: #007bff;
    }
    
    /* מספר מזהה להזמנה	 */
    /* טקסט אפור וקטן  */
    .reservation-id {
      font-size: 0.9em;
      color: #6c757d;
    }
    
    /* הודעה כאשר אין הזמנות */
    /* טקסט ממורכז באפור */
    .no-reservations {
      text-align: center;
      color: #6c757d;
      padding: 20px;
    }
    
    /* הודעת התראה כאשר אין הזמנות פעילות */
    /* רקע כתום בהיר */
    .alert-warning {
      background-color: #fff3cd;
      color: #856404;
      padding: 15px;
      border-radius: 8px;
      border: 1px solid #ffeeba;
      margin-bottom: 20px;
      font-weight: bold;
      text-align: center;
    }
    
    /* נכון לעכשיו הוא לוקח מהשרת רק הזמנות פעילות אז בינתיים אין לזה שימוש */
    /* הזמנה לא פעילה */
    .inactive-reservation {
      opacity: 0.7;
      border-left-color: #6c757d;
    }
    
    /* התאמות נוספות למסכים קטנים יותר  */
    @media (max-width: 768px) {
      .page-container {
        flex-direction: column;
      }
      
      .reservations-section, 
      .booking-section {
        max-width: 100% !important;
        width: 100% !important;
      }
      
      .calendar-inner button {
        font-size: 0.8em;
        padding: 8px 5px;
      }
    }

    /* תיאור הטיפול */
    /* גודל פונט קטן, צבע אפור כהה */
    .treatment-description {
      margin-top: 8px;
      font-size: 0.95em;
      color: #555;
    }
    </style>
</head>
<body>

<!-- כאן מוסיפים את המיכל שמכיל את שני החלקים -->
<div class="page-container">
  <!-- חלק 1: רשימת הזמנות פנסיון -->
  <div class="reservations-section">
    <h3>הזמנות פנסיון לכלב</h3>
    <div id="reservation-list">
      <!-- כאן יתווספו הזמנות הפנסיון דינמית -->
      <div class="no-reservations">טוען הזמנות...</div>
    </div>
  </div>

  <!-- חלק 2: טופס הזמנת טיפוח -->
  <div class="booking-section">
    <!-- הודעת התראה שתוצג רק כשאין הזמנות פעילות -->
    <div id="no-active-reservations-alert" style="display: none;" class="alert-warning">
      <p>שים לב: אין לך הזמנות פנסיון פעילות עבור הכלב הזה.</p>
      <p>ניתן לראות את כל ההזמנות ברשימה בצד ימין, אך רק כלבים עם הזמנות פנסיון פעילות יכולים לקבל טיפוח.</p>
      <p><a href="../../reservation/user/reservation.php" style="color: #007bff; font-weight: bold;">לחץ כאן להזמנת מקום בפנסיון</a></p>
    </div>
    
    <h2>הזמנת טיפוח לכלבך</h2>
    <div class="service-box">
      <img alt="dog grooming" src="images/DogShower.png" style="border-radius: 10px; max-height: 80px;"/>
      <div>
        <?php
        // בדיקה אם קיים סוג טיפוח בסשן
        if (isset($_SESSION['grooming_type']) && isset($_SESSION['grooming_price'])) {
            $grooming_type = htmlspecialchars($_SESSION['grooming_type']);
            $grooming_price = htmlspecialchars($_SESSION['grooming_price']);
            
            // קבלת תיאור לפי סוג הטיפוח
            $description = '';
            switch ($grooming_type) {
                case 'רחצה וסירוק':
                    $description = 'רחצה יסודית עם שמפו טבעי, סירוק מקצועי להסרת קשרים, וניחוח נפלא שיישאר לאורך זמן.';
                    break;
                case 'תספורת מקצועית':
                    $description = 'תספורת לפי סטנדרט גזע או בקשה אישית, עם ציוד מתקדם והתאמה אישית לגודל וסוג הפרווה.';
                    break;
                case 'גזיזת ציפורניים':
                    $description = 'גזיזת ציפורניים עדינה ובטוחה עם ציוד מקצועי, לשמירה על נוחות ובריאות כפות הרגליים.';
                    break;
                case 'ניקוי אוזניים':
                    $description = 'ניקוי יסודי ועדין של תעלות האוזניים למניעת דלקות וריחות לא נעימים.';
                    break;
                case 'צחצוח שיניים':
                    $description = 'טיפול שיניים הכולל הסרת רובד, חיזוק חניכיים וריח פה רענן.';
                    break;
                case 'טיפול בקרציות':
                    $description = 'טיפול מונע או משמיד נגד טפילים חיצוניים באמצעות תכשירים בטוחים לכלבים.';
                    break;
                default:
                    $description = 'טיפוח מותאם אישית לכלב שלך.';
            }
            
            echo "<strong>{$grooming_type}</strong>";
            echo "<div>{$description}</div>";
            echo "<div style='font-weight: bold; margin-top: 5px;'>₪{$grooming_price} • 30 דקות</div>";
        } else {
            // אם סוג הטיפוח והמחיר שלו לא מוגדרים בסשן אז תפנה בחזרה לדף בחירת טיפוח
    echo "<script>window.location.href = 'treatments.php';</script>";
        }
        ?>
      </div>
    </div>
    <h3 id="selected-day">בחר יום</h3>
    <!-- הוספת הודעה כאשר אין שעות זמינות ביום שנבחר -->
    <div id="no-available-times-message" style="display: none;" class="alert-warning">
      <p>אין שעות טיפוח זמינות עבור התאריך המבוקש, אנא נסה לתאם שוב במועד מאוחר יותר.</p>
    </div>
    <!-- כאן שותלים את התאריכים והחצים שהיו נמצאים במשתנה calendarContainer  -->
    <div id="calendar-buttons"></div>
    <h4>בוקר</h4>
    <div class="time-slots" id="morning-slots">
      <div>8:00</div><div>8:30</div><div>9:00</div><div>9:30</div><div>10:00</div><div>11:00</div><div>11:30</div>
    </div>
    <h4>צהריים</h4>
    <div class="time-slots" id="afternoon-slots">
      <div>12:00</div><div>12:30</div><div>13:00</div><div>13:30</div><div>14:00</div><div>14:30</div><div>15:00</div><div>15:30</div><div>16:00</div><div>16:30</div>
    </div>
    <button class="submit-button" id="submit-button" onclick="submitAppointment()">אשר הזמנה</button>
  </div>
</div>

<script>
  //  משתנים שמייצגים אלמנטים חשובים בדף כדי שנוכל לעבוד איתם אחר כך. למשל לשנות טקסט, להוסיף תוכן או להסתיר/להציג
    
    // האזור שבו נוצרים כפתורי הימים הזמינים בלוח השנה לפי טווח הזמנת הפנסיון שנבחרה
    const calendarContainer = document.getElementById('calendar-buttons');

    // הכותרת מעל לוח השעות
    const selectedDayDisplay = document.getElementById('selected-day');

    // המיכל שבו מוצגות ההזמנות הקיימות של הפנסיון עבור הכלב שנבחר
    const reservationList = document.getElementById('reservation-list');

    // ההתראה שמוצגת כאשר אין הזמנות פנסיון פעילות לכלב
    const noActiveReservationsAlert = document.getElementById('no-active-reservations-alert');

    // הכפתור שמאשר את הזמנת הטיפוח
    const submitButton = document.getElementById('submit-button');

    // שעת הטיפוח שנבחרה על ידי המשתמש
    let selectedSlot = null;
    // היום שנבחר על ידי המשתמש
    let selectedDay = null;
    // מספר השבוע שמוצג בלוח יחסית לשבוע הנוכחי
    let currentWeek = 0;
    // הזמנת הפנסיון הפעילה שנבחרה עבור הכלב
    let selectedReservation = null;
    // האם למשתמש יש הזמנות פנסיון פעילות לכלב
    let hasActiveReservations = false;

    //  קישור לכל שעות הטיפוח בדף
    const timeSlots = document.querySelectorAll('.time-slots div');

    // קבלת user_code ו-active_dog_id מה-SESSION
    const userCode = "<?php echo htmlspecialchars($_SESSION['user_code'] ?? ''); ?>";
    const activeDogId = "<?php echo htmlspecialchars($_SESSION['active_dog_id'] ?? ''); ?>";

    // בדיקה אם כל שעות הטיפוח תפוסות ביום שנבחר
    function checkAndDisplayTimeAvailability() {
      // בדיקה אם כל חלונות הזמן מושבתים עבור היום שנבחר
      // שעות הבוקר
      const morningSlots = document.querySelectorAll('#morning-slots div');
      // שעות הצהריים
      const afternoonSlots = document.querySelectorAll('#afternoon-slots div');

      // ... – האופרטור פורס את תוכן כל אחד מהמאגרים לתוך מערך חדש.
      //  מערך חדש שמכיל את כל השעות ברצף, שומר את הקישור לאלמנטים
      const allSlots = [...morningSlots, ...afternoonSlots];
      
      // ספירת חלונות זמן מושבתים
      // slot זה משתנה כללי ששומר קישור לאלמנט הדיב שמחזיק את השעה בדף
      // בודק אם לאלמנט slot יש את המחלקה disabled – כלומר שהשעה תפוסה או לא זמינה להזמנה
      const disabledSlotsCount = allSlots.filter(slot => slot.classList.contains('disabled')).length;
      
      // קבלת אלמנט ההודעה וכפתור האישור
      const noTimesMessage = document.getElementById('no-available-times-message');
      const submitButton = document.getElementById('submit-button');
      
      // אם כל השעות מושבתות
      if (disabledSlotsCount === allSlots.length) {
        // הצג הודעה
        noTimesMessage.style.display = 'block';
        // השבתת כפתור האישור
        submitButton.disabled = true;
      } else {
        // הסתר הודעה
        noTimesMessage.style.display = 'none';
        
        // שחרר את כפתור האישור רק אם יש הזמנות פעילות
        if (hasActiveReservations) {
          submitButton.disabled = false;
        }
      }
    }

    //לשלוף ולהציג את הזמנות הפנסיון של הכלב שנבחר, לבדוק אם קיימות כאלה, ולשייך את הזמנת הטיפוח לתקופת הפנסיון הפעילה
    function loadReservations() {

      // אם לא נבחר כלב פעיל מציג הודעה מתאימה
      if (!activeDogId) {
        // ההודעה הזו לא מוצגת כרגע מכיוון שהפונקציונליות של האתר מחייבת שתמיד יהיה כלב פעיל
        reservationList.innerHTML = '<div class="no-reservations">לא נבחר כלב פעיל</div>';
        return;
      }
      
      //  לקבל את כל הזמנות הפנסיון הפעילות של הכלב הנבחר
      fetch('getReservationsForDog.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
          dog_id: activeDogId,
          user_code: userCode
        })
      })

      .then(res => res.json())
      // ממיר את התגובה מ־ JSON לאובייקט data
      .then(data => {
        // עדכון משתנה שמציין אם יש הזמנות פעילות
        hasActiveReservations = data.has_active_reservations;
        
        // הצגת או הסתרת התראה לפי מצב ההזמנות הפעילות
        noActiveReservationsAlert.style.display = hasActiveReservations ? 'none' : 'block';
        
        // הפיכת כפתור השליחה ללא פעיל אם אין הזמנות פעילות
        submitButton.disabled = !hasActiveReservations;
        
        // בונה כרטיסים לכל הזמנה ומכניס אותם ל־ reservationList
        if (data.reservations && data.reservations.length > 0) {
          let html = '';
          // לולאה שרצה על כל ההזמנות שהתקבלו מהשרת
          data.reservations.forEach(res => {
            // קביעת סגנון שונה להזמנות לא פעילות
            // אם תאריך הסיום של ההזמנה גדול או שווה להיום, היא נחשבת פעילה
            const isActive = new Date(res.end_date) >= new Date();

            // קביעת מחלקת העיצוב של כרטיס ההזמנה בשביל להבדיל בין הזמנות פעילות ללא פעילות
            const cardClass = isActive ? 'reservation-card' : 'reservation-card inactive-reservation';
            // תאריך ההתחלה
            const startDate = new Date(res.start_date);
            // תאריך הסיום
            const endDate = new Date(res.end_date);
            
            // כרטיס דינמי עבור כל הזמנת פנסיון פעילה של הכלב.
            // מצרף את קטע ה־ HTML הזה למשתנה html כדי שבסוף כל ההזמנות יוצגו בבת אחת
            html += `
              <div class="${cardClass}" data-id="${res.id}" data-start="${res.start_date}" data-end="${res.end_date}" data-active="${isActive}">
                <div class="reservation-date">
                  ${startDate.toLocaleDateString('he-IL')} - ${endDate.toLocaleDateString('he-IL')}
                </div>
                <div class="reservation-id">הזמנה מס' ${res.id} ${!isActive ? '(הסתיימה)' : ''}</div>
              </div>
            `;
          });

          // לוקחת את המחרוזת html ומשתילה אותה ישירות לתוך האלמנט reservationList בדף.
          reservationList.innerHTML = html;
          
          // הוספת אירועי לחיצה להזמנות
          document.querySelectorAll('.reservation-card').forEach(card => {
            // להוסיף לכל הזמנה מאזין לאירוע לחיצה
            card.addEventListener('click', () => {

              // הסרת סימון מכל הכרטיסים
              document.querySelectorAll('.reservation-card').forEach(c => 
                c.classList.remove('selected'));
              
              // סימון הכרטיס הנוכחי
              card.classList.add('selected');
              // שמירה של פרטי ההזמנה שנבחרה מתוך כרטיס ההזמנה שנלחץ עליו, בתוך משתנה
              selectedReservation = {
                id: card.getAttribute('data-id'),
                start: card.getAttribute('data-start'),
                end: card.getAttribute('data-end'),
                active: card.getAttribute('data-active') === 'true'
              };
              
              // עדכון התאריכים בלוח השנה  
              updateCalendarForReservation(selectedReservation.start, selectedReservation.end);
            });
          });
          
          // בחירה אוטומטית של ההזמנה הפעילה הראשונה ברשימה, אם יש כזו
          if (hasActiveReservations) {
            const activeCard = document.querySelector('.reservation-card:not(.inactive-reservation)');
            // מחפש את הכרטיס הראשון שלא מכיל את המחלקה כרטיס לא פעיל, כלומר הזמנה שעדיין בתוקף
            if (activeCard) {
              // אם נמצאה הזמנה פעילה – לוחץ עליה
              activeCard.click();
            } else {
              // אם לא נמצאה אף אחת פעילה  – בוחר את הראשונה
              document.querySelector('.reservation-card').click();
            }
          } else {
            // אם אין הזמנות פעילות, עדיין נבחר את הראשונה לתצוגה אך נשבית את האפשרות להזמין
            document.querySelector('.reservation-card').click();
          }
        } else {
          // אם אין בכלל הזמנות פעילות לכלב
          reservationList.innerHTML = '<div class="no-reservations">אין הזמנות פנסיון פעילות לכלב זה</div>';
        }
      })
      // במקרה של תקלה בתקשורת עם השרת, מוצגת הודעת שגיאה
      .catch(err => {
        reservationList.innerHTML = '<div class="no-reservations">שגיאה בטעינת ההזמנות</div>';
        console.error('Error loading reservations:', err);
      });
    }
    
    //  עדכון לוח התאריכים בהתאם להזמנת הפנסיון שנבחרה
    function updateCalendarForReservation(startDate, endDate) {
      console.log("Updating calendar with reservation dates only:", startDate, "to", endDate);
      
      // בדיקה שהתאריכים שהתקבלו אינם ריקים
      if (!startDate || !endDate) {
        console.error("Missing start or end date");
        return;
      }
      
      try {
        // המרת התאריכים לאובייקטי Date
        // כדי לבצע עליהם פעולות
        const startDateObj = new Date(startDate);
        const endDateObj = new Date(endDate);
        
        // חישוב מספר הימים בטווח ההזמנה
        // הפרש הזמן בין תאריך הסיום לתאריך ההתחלה – במילישניות
        const timeDiff = endDateObj.getTime() - startDateObj.getTime();
        // הפרש הזמן בין תאריך הסיום לתאריך ההתחלה – בימים
        /*1000 מ"ש לשנייה
        3600 שניות בשעה
        24 שעות ביממה*/
        const dayDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; // כולל את היום האחרון
        
        // עדכון כותרת הדף עם תאריך ההתחלה
        selectedDayDisplay.textContent = startDateObj.toLocaleDateString('he-IL', { 
          weekday: 'long', 
          day: 'numeric', 
          month: 'long',
          year: 'numeric'
        });
        
        // קביעת תאריך התחלה כתאריך נבחר כברירת מחדל
        selectedDay = startDate;
        
        // חישוב מספר הימים המקסימלי להצגה
        const screenWidth = window.innerWidth;
        let maxDisplayDays = 5; // פחות ימים כדי לתת מקום לחצים
        
        if (screenWidth < 768) {
          maxDisplayDays = 3; // עוד פחות במסכים קטנים
        }
        
        // יצירת מערך התאריכים
        const daysArray = [];
        let startingDay = 0;
        
        // לולאה ליצירת כל יום
        //  בין 0 לבין מספר הימים להצגה, אך לא יותר ממספר הימים בהזמנה ולא יותר ממקסימום הימים לתצוגה על המסך
        for (let i = 0; i < Math.min(dayDiff, maxDisplayDays); i++) {

          // יוצר עותק של startDateObj כדי לא לשנות את המקור
          const currentDate = new Date(startDateObj);

          // תאריך חדש בכל סיבוב בלולאה, לפי מיקום היום בטווח שבין תאריך ההתחלה לתאריך הסיום של ההזמנה
          currentDate.setDate(startDateObj.getDate() + startingDay + i);
          
          // בודק אם עברנו את תאריך הסיום של ההזמנה – אם כן, מפסיקים את הלולאה
          if (currentDate > endDateObj) break;
          
          // ממיר את התאריך למחרוזת בפורמט YYYY-MM-DD – קל לשליחה לשרת והשוואה
          const currentIsoDate = currentDate.toISOString().split('T')[0];

          // משמש לקבוע האם היום הנוכחי בלולאה הוא היום הראשון בטווח ההזמנה
          const isSelected = i === 0;
          
          // מוסיף את היום שיצרנו למערך daysArray, שמייצג את כל הימים שיוצגו בלוח התאריכים להזמנת טיפוח
          daysArray.push({
            date: currentIsoDate,
            // תצוגת תאריך מותאמת למשתמש בעברית
            display: currentDate.toLocaleDateString('he-IL', { 
              weekday: 'short', 
              day: 'numeric', 
              month: 'short' 
            }),
            // אם התאריך הזה הוא היום הנוכחי
            isToday: currentDate.toDateString() === new Date().toDateString(),

            // האם היום הזה נבחר כברירת מחדל להיות התאריך הראשון בטווח ההזמנה
            isSelected: isSelected
          });
        }
        
        // יצירת מיכל לכל החלקים
        let html = '<div class="calendar-wrapper">';
        
        // יצירת קונטיינר פנימי לחצים ותאריכים
        html += '<div class="calendar-container">';
        
        // החץ הקודם
        // כפתור לגלילה לשבוע הקודם
        // מושבת כברירת מחדל כי אנחנו תמיד מתחילים מההתחלה
        html += '<button class="week-nav" id="prev-dates" disabled>→</button>';
        
        // אזור שבו יוצגו הכפתורים של הימים 
        html += '<div class="calendar-inner">';
        
        
       daysArray.forEach(day => {
          let classList = [];
          if (day.isToday) classList.push('today');
          if (day.isSelected) classList.push('active');
          
          // יצירת כפתור לכל יום
          // מחבר את רשימת הקלאסים למחרוזת אחת, ומצמיד אותה לכפתור, בכל איטרציה, הרשימה הזאת מתאפסת
          // שומר בתוך הכפתור את התאריך המלא בפורמט יום - חודש - שנה
          // הטקסט שמוצג על הכפתור
          html += `<button data-date="${day.date}" class="${classList.join(' ')}">${day.display}</button>`;
        });
        
        html += '</div>'; // סגירת האזור שבו יוצגו הכפתורים של הימים
        
        // החץ הבא
        // יוצר כפתור ניווט שמאלה בלוח השנה – כלומר, מעבר לימים הבאים בטווח ההזמנה
        // אם יש יותר ימים בטווח (dayDiff) מהכמות שמוצגת כרגע (maxDisplayDays), הכפתור יופיע פעיל.
        html += `<button class="week-nav" id="next-dates" ${dayDiff > maxDisplayDays ? '' : 'disabled'}>←</button>`;
        
        // סגירת הקונטיינרים
        html += '</div></div>';
        
        // עדכון התצוגה
        // מציג את לוח השנה בפועל על המסך
         // שותל באזור שבו נוצרים כפתורי הימים הזמינים בלוח השנה לפי טווח הזמנת הפנסיון שנבחרה
        calendarContainer.innerHTML = html;
        
        // שמירת מידע כדי לשלוף את המידע הזה בקלות בפונקציות אחרות
        calendarContainer.dataset.startIndex = 0;
        calendarContainer.dataset.startDate = startDate;
        calendarContainer.dataset.endDate = endDate;
        calendarContainer.dataset.totalDays = dayDiff;
        calendarContainer.dataset.maxDays = maxDisplayDays;
        

        // להגדיר מאזינים לאירועים עבור כפתורי הימים שנטענו זה עתה בטווח התאריכים
        // navigateToDateRange בתוך הפונקציה הזאת מופעלת עוד פונקציה בשם 
        // הפונקציה הזאת יוצרת מידע של תאריכים חדשים ואז מהמידע הזה יוצרת מחדש כפתורי תאריכים עם מאזינים חדשים לכפתורי הימים 
        // calendar-inner לבסוף מעדכנת בדף בלבד את האזור של התאריכים במקום שנוצר בשלב מוקדם יותר שנקרא 
        setupReservationDateEvents(startDate, endDate, maxDisplayDays);
        
        // עדכון זמנים זמינים
        updateAvailableTimes();
        
      } catch (error) {
        console.error("Error updating calendar:", error);
      }
    }
    
    // הגדרת אירועים לניווט בטווח התאריכים
    function setupReservationDateEvents(startDate, endDate, maxDisplay) {
      // הגדרת אירועי לחיצה על כפתורי תאריך
      // calendar-inner כל כפתור של יום שנמצא בתוך האלמנט
      document.querySelectorAll('.calendar-inner button').forEach(btn => {
        btn.addEventListener('click', () => {
          // הסרת סימון מכל הכפתורים
          document.querySelectorAll('.calendar-inner button').forEach(b => b.classList.remove('active'));
          
          // סימון הכפתור הנוכחי
          btn.classList.add('active');
          
          // עדכון התאריך הנבחר
          selectedDay = btn.getAttribute('data-date');
          const dateObj = new Date(selectedDay);
          
          //  עדכון הכותרת עם התאריך הנבחר
          // הכותרת מעל לוח התאריכים 
          selectedDayDisplay.textContent = dateObj.toLocaleDateString('he-IL', { 
            weekday: 'long', 
            day: 'numeric', 
            month: 'long',
            year: 'numeric'
          });
          
          // עדכון זמני התורים
          updateAvailableTimes();
          
        });
      });
      
      // הגדרת אירועי ניווט בין קבוצות תאריכים
      const prevBtn = document.getElementById('prev-dates');
      const nextBtn = document.getElementById('next-dates');
      
      // מוודא שכפתור "הקודם" קיים בדף
      if (prevBtn) {
        // מוסיף לו אירוע לחיצה
          prevBtn.addEventListener('click', () => {
          // שואב את האינדקס הנוכחי של תחילת הטווח המוצג בלוח
          let currentIndex = parseInt(calendarContainer.dataset.startIndex || 0);
          
          // בודק אם ניתן בכלל לזוז אחורה (אם אנחנו לא כבר בקצה ההתחלה)
          if (currentIndex > 0) {

            // מזיז את ההצגה אחורה בהתאם לגודל חלון התצוגה ומספר התאריכים שיכולים להיות מוצגים על המסך
            // כדי לקבוע מאיפה להתחיל להציג את התאריכים בלוח כשעוברים לשבוע הקודם
            currentIndex = Math.max(0, currentIndex - maxDisplay);

            // להציג מחדש את לוח הימים – מנקודת התחלה אחרת בטווח ההזמנה, לפי ניווט אחורה
            navigateToDateRange(startDate, endDate, currentIndex, maxDisplay);
            
            // עדכון כפתורי הניווט
            // מאפשר מחדש את כפתור הבא, כי אם המשתמש חזר אחורה, סימן שיש שוב לאן להתקדם קדימה
            document.getElementById('next-dates').disabled = false;
            // משבית את כפתור הקודם רק אם הגענו לתחילת הטווח, כלומר אין יותר לאן לחזור אחורה
            prevBtn.disabled = (currentIndex === 0);
          }
        });
      }
      
      // מוודא שכפתור "הבא" קיים בדף
      if (nextBtn) {
        // מוסיף לו אירוע לחיצה
        nextBtn.addEventListener('click', () => {
          // שואב את האינדקס הנוכחי של תחילת הטווח המוצג בלוח
          let currentIndex = parseInt(calendarContainer.dataset.startIndex || 0);
          // שואב כמה ימים בסך הכול יש בטווח ההזמנה
          const totalDays = parseInt(calendarContainer.dataset.totalDays || 0);
          
          // אם לא מוצגים עדיין כל הימים, כלומר יש עוד תאריכים קדימה
          if (currentIndex + maxDisplay < totalDays) {
            // חישוב האינדקס החדש שממנו יוצגו הימים הבאים בלוח השנה תוך שמירה על כך שלא נחרוג מעבר לסוף הטווח
            currentIndex = Math.min(totalDays - maxDisplay, currentIndex + maxDisplay);
            navigateToDateRange(startDate, endDate, currentIndex, maxDisplay);
            
            // עדכון כפתורי הניווט
            // מאפשר את הכפתור "הקודם", כי עכשיו אפשר לחזור אחורה
            document.getElementById('prev-dates').disabled = false;
            // משבית את כפתור "הבא" אם הגענו לסוף הטווח
            nextBtn.disabled = (currentIndex + maxDisplay >= totalDays);
          }
        });
      }
    }
    
    // ניווט לקבוצת תאריכים מסוימת בטווח ההזמנה
    // כאשר המשתמש לוחץ על "הבא" או "הקודם", מבצעת את כל שלבי ההצגה מחדש של קטע תאריכים בלבד בלוח הימים
    // מחשבת מחדש את התאריכים הרלוונטים, לאחר מכן יוצרת כפתורי תאריכים חדשים, מוסיפה לכפתורים מאזיני לחיצה חדשים
    // לבסוף מעדכנת ודורסת בדף רק את האזור שהיו בו מוצגים קודם לכן כפתורי הימים הישנים 
    // calendar-inner האזור שמוצגים בו כפתורי הימים בלבד, נקרא 
    // updateCalendarForReservation אזור זה נוצר בהתחלה בפונקציה הראשונית שנקראת 
    function navigateToDateRange(startDate, endDate, startIndex, maxDisplay) {

      //  הכנה לפני בניית לוח התאריכים בטווח המבוקש
      const startDateObj = new Date(startDate);
      const endDateObj = new Date(endDate);
      // שואב את מספר הימים הכולל בטווח ההזמנה מתוך משתנה שנשמר קודם לכן
      const totalDays = parseInt(calendarContainer.dataset.totalDays || 0);
      
      // יצירת מערך חדש של תאריכים לתצוגה
      const daysArray = [];
      
      // רץ לכל היותר עד 3 או 5, כי זה עבור התצוגה העכשווית לאותו רגע
      for (let i = 0; i < maxDisplay; i++) {
        // בכל איטרציה יוצרים יום חדש
        const currentDate = new Date(startDateObj);
        // תאריך חדש בכל סיבוב בלולאה, לפי מיקום היום בטווח שבין תאריך אינדקס ההתחלה הנוכחי לתאריך הסיום של ההזמנה
        // startIndex הוא בעצם currentIndex
        currentDate.setDate(startDateObj.getDate() + startIndex + i);
        
        // בדיקה שלא חרגנו מתאריך הסיום
        // בודק אם עברנו את תאריך הסיום של ההזמנה – אם כן, מפסיקים את הלולאה
        if (currentDate > endDateObj) break;
        
        // ממיר את התאריך למחרוזת בפורמט YYYY-MM-DD – קל לשליחה לשרת והשוואה
        const currentIsoDate = currentDate.toISOString().split('T')[0];
        
        // מוסיף את היום שיצרנו למערך daysArray, שמייצג את כל הימים שיוצגו בלוח התאריכים להזמנת טיפוח
        daysArray.push({
          date: currentIsoDate,
          // תצוגת תאריך מותאמת למשתמש בעברית
          display: currentDate.toLocaleDateString('he-IL', { 
            weekday: 'short', 
            day: 'numeric', 
            month: 'short' 
          }),
          // אם התאריך הזה הוא היום הנוכחי
          isToday: currentDate.toDateString() === new Date().toDateString(),
          // האם היום הזה נבחר כברירת מחדל להיות התאריך הראשון בטווח ההזמנה
          isSelected: i === 0 // היום הראשון נבחר כברירת מחדל
        });
      }
      
      // עדכון הלוח עם מבנה מקונן 
      // הוספת אזור התאריכים בלבד, הקונטיינר כבר קיים
      // לא מחליף את כל הלוח, אלא רק את התאריכים
      const calendarInner = document.querySelector('.calendar-inner');
      let innerHtml = '';
      
      
      daysArray.forEach(day => {
        let classList = [];
        if (day.isToday) classList.push('today');
        if (day.isSelected) classList.push('active');
        

        // יצירת כפתור לכל יום
        // מחבר את רשימת הקלאסים למחרוזת אחת, ומצמיד אותה לכפתור, בכל איטרציה, הרשימה הזאת מתאפסת
        // שומר בתוך הכפתור את התאריך המלא בפורמט יום - חודש - שנה
        // הטקסט שמוצג על הכפתור
        innerHtml += `<button data-date="${day.date}" class="${classList.join(' ')}">${day.display}</button>`;
      });
      
      // עדכון בפועל של כפתורי הימים רק בהחלק בתוך הלוח שבו מוצגים כפתורי הימים בלבד
      calendarInner.innerHTML = innerHtml;
      
      // עדכון מצב כפתורי הניווט

      // משבית את כפתור הקודם רק אם הגענו לתחילת הטווח, כלומר אין יותר לאן לחזור אחורה
      document.getElementById('prev-dates').disabled = (startIndex <= 0);

      // משבית את כפתור "הבא" אם הגענו לסוף הטווח
      document.getElementById('next-dates').disabled = (startIndex + maxDisplay >= totalDays);
      
      // עדכון האינדקס הנוכחי
      // startIndex הוא בעצם currentIndex
      calendarContainer.dataset.startIndex = startIndex;
      
      // בחירת היום הראשון בטווח התאריכים העכשוי שמוצג 
      if (daysArray.length > 0) {
        selectedDay = daysArray[0].date;
        
        // עדכון הכותרת של התאריך הנבחר עם התאריך הראשון בטווח כברירת מחדל
        const dateObj = new Date(selectedDay);
        selectedDayDisplay.textContent = dateObj.toLocaleDateString('he-IL', { 
          weekday: 'long', 
          day: 'numeric', 
          month: 'long',
          year: 'numeric'
        });
      }
      
      // הוספת מאזינים לכפתורים החדשים - רק לכפתורי התאריך
      document.querySelectorAll('.calendar-inner button').forEach(btn => {
        btn.addEventListener('click', () => {
          // הסרת סימון מכל הכפתורים
          document.querySelectorAll('.calendar-inner button').forEach(b => b.classList.remove('active'));
          
          // סימון הכפתור הנוכחי
          btn.classList.add('active');
          
          
          // עדכון התאריך הנבחר
          selectedDay = btn.getAttribute('data-date'); // יום - חודש - שנה
          // מאפשר לעבוד עם התאריך
          const dateObj = new Date(selectedDay);
          
          // עדכון הכותרת של התאריך הנבחר עם התאריך שנלחץ עכשיו
          selectedDayDisplay.textContent = dateObj.toLocaleDateString('he-IL', { 
            weekday: 'long', 
            day: 'numeric', 
            month: 'long',
            year: 'numeric'
          });
          
          // עדכון זמני התורים
          updateAvailableTimes();
        });
      });
      
      // עדכון זמני התורים הזמינים
      updateAvailableTimes();
    }

    // פונקציה שמנהלת את לוגיקת הצגת שעות הטיפוח הזמינות
    function updateAvailableTimes() {
      // מחזיק קישור לכל אלמנטי שעות הטיפוח בדף timeSlots 
      // מאפס את בחירת השעה והחסימות
      timeSlots.forEach(slot => slot.classList.remove('disabled', 'selected'));
      // לאפס את הבחירה הנוכחית של שעת הטיפוח 
      selectedSlot = null;
      
      // אם אין הזמנות פעילות, נשבית את כל השעות
      if (!hasActiveReservations) {
        timeSlots.forEach(slot => slot.classList.add('disabled'));

        // בדיקה האם כל השעות בתצוגה של היום הנבחר תפוסות, ואם כן, להציג הודעה למשתמש
        checkAndDisplayTimeAvailability();
        return;
      }
      
      // שולח בקשה לשרת כדי לקבל את רשימת השעות החסומות ליום שנבחר
      fetch('getBlockedTimesServer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        // היום שנבחר על ידי המשתמש selectedDay משתנה גלובלי 
        body: JSON.stringify({ day: selectedDay }) // יום - חודש -שנה
      })
      // קבלת תשובה מהשרת בפורמט JSON
      .then(res => res.json())
      .then(data => {
        if (data.blockedTimes && Array.isArray(data.blockedTimes)) {
          // סימון השעות החסומות
          // עובר על כל שעה חסומה שהתקבלה מהשרת
          data.blockedTimes.forEach(time => {
            // משווה אותה לטקסט שבתוך כל אחד מהכפתורים
            document.querySelectorAll('.time-slots div').forEach(slot => {
              // כדי להבטיח שהתאמה לא תיכשל בגלל רווחים מיותרים בתחילת/סוף המחרוזות
              if (slot.textContent.trim() === time.trim()) {
                // אם יש התאמה מוסיף מחלקת disabled
                slot.classList.add('disabled');
              }
            });
          });
          
          // בדיקה אם כל השעות מושבתות אחרי עדכון השעות החסומות
          checkAndDisplayTimeAvailability();
        }
      });
    }

    // מטפל בבחירת שעה לטיפוח
    // עובר על כל שעה זמינה בלוח ומאזין ללחיצה על השעה
    timeSlots.forEach(slot => {
      slot.onclick = () => {
        // אם אין הזמנות פעילות או השעה כבר תפוסה, לא נאפשר בחירה
        if (slot.classList.contains('disabled') || !hasActiveReservations) return;
        
        // מסיר את הבחירה מכל השעות
        timeSlots.forEach(s => s.classList.remove('selected'));
        // מסמן את השעה שנלחצה
        slot.classList.add('selected');
        // שומר את הטקסט שלה לשימוש בהמשך של שליחת ההזמנה
        selectedSlot = slot.textContent;
      }
    });

    // טיפול בשליחת הזמנה הטיפוח לשרת כולל התאריך והשעה 
    function submitAppointment() {
      // וידוא שיש הזמנות פעילות לפני הגשת הטופס
      if (!hasActiveReservations) {
        alert('לא ניתן להזמין טיפוח ללא הזמנת פנסיון פעילה.');
        return;
      }
      
      // בודק אם המשתמש בחר גם יום וגם שעה
      if (!selectedDay || !selectedSlot) {
        alert('אנא בחר יום ושעה.');
        return;
      }
      
      // בניית אובייקט הזמנה - המידע הבסיסי שנשלח לשרת
      // הוספת סוג הטיפוח והמחיר אם קיים בסשן
      const appointmentData = { 
        day: selectedDay, 
        time: selectedSlot,
        user_code: userCode,
        dog_id: activeDogId
      };
      
      // מוסיף את סוג הטיפוח והמחיר אם הם קיימים בסשן
      <?php if (isset($_SESSION['grooming_type']) && isset($_SESSION['grooming_price'])) { ?>
        appointmentData.grooming_type = "<?php echo addslashes($_SESSION['grooming_type']); ?>";
        appointmentData.grooming_price = <?php echo intval($_SESSION['grooming_price']); ?>;
      <?php } ?>
      
      // אם יש הזמנת פנסיון מצרף אותה
      // הזמנת הפנסיון הפעילה שנבחרה עבור הכלב
      if (selectedReservation && selectedReservation.id) {
        appointmentData.reservation_id = selectedReservation.id;
      }
      
      // הודעה שמבקשת מהמשתמש לאשר את ההזמנה
      let confirmMessage = `האם לאשר הזמנת טיפוח ליום ${new Date(selectedDay).toLocaleDateString('he-IL')} בשעה ${selectedSlot}?`;
      
      // אם יש סוג טיפוח ומחיר לטיפוח – מעדכן את הטקסט בהודעה
      <?php if (isset($_SESSION['grooming_type']) && isset($_SESSION['grooming_price'])) { ?>
        confirmMessage = `האם לאשר הזמנת טיפוח "${<?php echo json_encode($_SESSION['grooming_type']); ?>}" ליום ${new Date(selectedDay).toLocaleDateString('he-IL')} בשעה ${selectedSlot}?`;
      <?php } ?>
      
      // אישור סופי מהמשתמש לפני שליחת ההזמנה בפועל
      // confirm זאת פונקציה מובנת
      // אם המשתמש לוחץ "ביטול" – הפונקציה מפסיקה ולא נשלח דבר לשרת
      if (!confirm(confirmMessage)) {
        return;
      }
      
      // שליחת אובייקט ההזמנה לשרת
      fetch('groomingAppServer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        // הנתונים שנשלחים הם כל פרטי ההזמנה בפורמט JSON
        body: JSON.stringify(appointmentData)
      })
      // מקבל את התשובה כקובץ JSON מהשרת 
      .then(res => res.json())
      .then(data => {
        // אם ההזמנה הצליחה
        if (data.success) {
          // מציג למשתמש alert עם מספר האישור והזמנת הפנסיון שקושרה
          // alert(`ההזמנה נקלטה! מספר אישור: ${data.confirmation}`);
          let successMessage = `ההזמנה נקלטה! מספר אישור: ${data.confirmation}`;
          if (data.connected_reservation_id) {
            successMessage += `\nההזמנה קושרה להזמנת פנסיון מס' ${data.connected_reservation_id}`;
          }
          alert(successMessage);

          // groomingAppServer כשהזמנת הטיפוח נרשמת בשרת באמצעות 
          // isTaken =1 נוסף לה בטבלת הטיפוח  הסימון 
          // getBlockedTimesServer לאחר מכן הקובץ  
          // שולף את כל הימים והשעות מטבלת הטיפוח עבורן השדה "האם תפוסה" שווה ל-1
          // בפונקציה updateAvailableTimes יש בקשה מהשרת דרך הקובץ getBlockedTimesServer לקבל את השעות החסומות


          // מרענן את שעות הטיפוח כדי לחסום את השעה שנבחרה
          updateAvailableTimes();

          // אם ההזמנה לא הצליחה, מציג את הודעת השגיאה שהשרת החזיר
        } else {
          alert(`שגיאה: ${data.error}`);
        }
      });
    }

    // טעינת הזמנות בעת טעינת העמוד
    window.onload = function() {
      loadReservations();
    };
  </script>
</body>
</html>