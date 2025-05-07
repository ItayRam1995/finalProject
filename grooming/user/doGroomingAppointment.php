<?php include '../../header.php'; ?>
<!DOCTYPE html>

<html dir="rtl" lang="he">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>הזמנת טיפוח לכלבך</title>
<link href="https://fonts.googleapis.com/css2?family=Varela+Round&amp;display=swap" rel="stylesheet"/>
<style>
    body {
      font-family: 'Varela Round', sans-serif;
      margin: 0;
      padding: 40px 20px;
      background: linear-gradient(to bottom left, #f0f8ff, #ffffff);
      display: flex;
      justify-content: center;
    }
    .booking-section {
      max-width: 800px;
      width: 100%;
      background: white;
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      animation: fadeIn 1s ease;
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }
    h2 {
      text-align: center;
      color: #007bff;
      margin-bottom: 20px;
    }
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
    .calendar, .time-slots {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: center;
    }
    .calendar button, .time-slots div {
      padding: 10px 14px;
      border: none;
      border-radius: 8px;
      background-color: #e3f2fd;
      font-weight: bold;
      transition: all 0.2s;
      cursor: pointer;
    }
    .calendar button:hover, .time-slots div:hover {
      background-color: #bbdefb;
      transform: scale(1.05);
    }
    .calendar button.active {
      background-color: #007bff;
      color: white;
    }
    .calendar button.today {
      border: 2px solid #28a745;
    }
    .calendar button.disabled,
    .time-slots div.disabled {
      background-color: #999 !important;
      color: white;
      cursor: not-allowed;
      opacity: 0.7;
    }
    .time-slots div.selected {
      background-color: #28a745;
      color: white;
    }
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
    .submit-button:disabled {
      background-color: #cccccc;
      cursor: not-allowed;
      opacity: 0.7;
    }
    .link-button {
      background-color: #6c757d;
      text-align: center;
      display: block;
      margin: 30px auto 0;
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
  
    .time-slots {
      justify-content: right;
    }
    
    .booking-section {
      max-width: 1200px !important;
    }
    
    /* לוודא שהחצים תמיד בתוך התחום הלבן */
    .calendar-wrapper {
      width: 100%;
      background-color: #f8f9fa;
      border-radius: 10px;
      padding: 10px;
      margin: 15px 0;
      position: relative;
      overflow: hidden; /* מונע גלישה */
    }

    .calendar-container {
      display: flex;
      align-items: center;
      width: 100%;
      margin: 0 auto;
      position: relative;
    }

    .calendar-inner {
      display: flex;
      flex-wrap: nowrap;
      justify-content: center;
      flex: 1;
      margin: 0 auto;
      padding: 0 10px;
    }

    /* עיצוב לחצים */
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

    .calendar-inner button:hover {
      background-color: #bbdefb;
      transform: scale(1.05);
    }

    .calendar-inner button.active {
      background-color: #007bff;
      color: white;
    }

    /*   מיכל הזמנות פנסיון */
    .page-container {
      display: flex;
      gap: 30px;
      max-width: 1400px;
      margin: 0 auto;
      justify-content: space-between;
    }
    
    .reservations-section {
      flex: 0 0 300px;
      background: white;
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      max-height: 600px;
      overflow-y: auto;
    }
    
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
    
    .reservation-card.selected {
      background-color: #cce5ff;
      border-left: 4px solid #28a745;
    }
    
    .reservation-date {
      font-weight: bold;
      color: #007bff;
    }
    
    .reservation-id {
      font-size: 0.9em;
      color: #6c757d;
    }
    
    .no-reservations {
      text-align: center;
      color: #6c757d;
      padding: 20px;
    }
    
    /* הודעת התראה כאשר אין הזמנות פעילות */
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
    
    /* הזמנה לא פעילה */
    .inactive-reservation {
      opacity: 0.7;
      border-left-color: #6c757d;
    }
    
    /* התאמות נוספות לטלפונים ניידים */
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
            // הפניה לדף בחירת טיפוח
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
    <a class="submit-button link-button" href="adminPanel.html">ניהול הזמנות</a>
  </div>
</div>

<script>
    const calendarContainer = document.getElementById('calendar-buttons');
    const selectedDayDisplay = document.getElementById('selected-day');
    const reservationList = document.getElementById('reservation-list');
    const noActiveReservationsAlert = document.getElementById('no-active-reservations-alert');
    const submitButton = document.getElementById('submit-button');
    let selectedSlot = null;
    let selectedDay = null;
    let currentWeek = 0;
    let selectedReservation = null;
    let hasActiveReservations = false;
    const maxWeeks = 4;
    const timeSlots = document.querySelectorAll('.time-slots div');
    // קבלת user_code ו-active_dog_id מה-SESSION
    const userCode = "<?php echo htmlspecialchars($_SESSION['user_code'] ?? ''); ?>";
    const activeDogId = "<?php echo htmlspecialchars($_SESSION['active_dog_id'] ?? ''); ?>";

    // בדיקה אם כל שעות הטיפוח תפוסות ביום שנבחר
    function checkAndDisplayTimeAvailability() {
      // בדיקה אם כל חלונות הזמן מושבתים עבור היום שנבחר
      const morningSlots = document.querySelectorAll('#morning-slots div');
      const afternoonSlots = document.querySelectorAll('#afternoon-slots div');
      const allSlots = [...morningSlots, ...afternoonSlots];
      
      // ספירת חלונות זמן מושבתים
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

    // כדי להפעיל אוטומטית את התאריך הנכון
    function loadReservations() {
      if (!activeDogId) {
        reservationList.innerHTML = '<div class="no-reservations">לא נבחר כלב פעיל</div>';
        return;
      }
      
      fetch('getReservationsForDog.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
          dog_id: activeDogId,
          user_code: userCode
        })
      })
      .then(res => res.json())
      .then(data => {
        // עדכון משתנה שמציין אם יש הזמנות פעילות
        hasActiveReservations = data.has_active_reservations;
        
        // הצגת או הסתרת התראה לפי מצב ההזמנות הפעילות
        noActiveReservationsAlert.style.display = hasActiveReservations ? 'none' : 'block';
        
        // הפיכת כפתור השליחה ללא פעיל אם אין הזמנות פעילות
        submitButton.disabled = !hasActiveReservations;
        
        if (data.reservations && data.reservations.length > 0) {
          let html = '';
          data.reservations.forEach(res => {
            // קביעת סגנון שונה להזמנות לא פעילות
            const isActive = new Date(res.end_date) >= new Date();
            const cardClass = isActive ? 'reservation-card' : 'reservation-card inactive-reservation';
            const startDate = new Date(res.start_date);
            const endDate = new Date(res.end_date);
            
            html += `
              <div class="${cardClass}" data-id="${res.id}" data-start="${res.start_date}" data-end="${res.end_date}" data-active="${isActive}">
                <div class="reservation-date">
                  ${startDate.toLocaleDateString('he-IL')} - ${endDate.toLocaleDateString('he-IL')}
                </div>
                <div class="reservation-id">הזמנה מס' ${res.id} ${!isActive ? '(הסתיימה)' : ''}</div>
              </div>
            `;
          });
          reservationList.innerHTML = html;
          
          // הוספת אירועי לחיצה להזמנות
          document.querySelectorAll('.reservation-card').forEach(card => {
            card.addEventListener('click', () => {
              // הסרת סימון מכל הכרטיסים
              document.querySelectorAll('.reservation-card').forEach(c => 
                c.classList.remove('selected'));
              
              // סימון הכרטיס הנוכחי
              card.classList.add('selected');
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
            if (activeCard) {
              activeCard.click();
            } else {
              document.querySelector('.reservation-card').click();
            }
          } else {
            // אם אין הזמנות פעילות, עדיין נבחר את הראשונה לתצוגה אך נשבית את האפשרות להזמין
            document.querySelector('.reservation-card').click();
          }
        } else {
          reservationList.innerHTML = '<div class="no-reservations">אין הזמנות פנסיון לכלב זה</div>';
        }
      })
      .catch(err => {
        reservationList.innerHTML = '<div class="no-reservations">שגיאה בטעינת ההזמנות</div>';
        console.error('Error loading reservations:', err);
      });
    }
    
    //  עדכון לוח השנה עם החצים בפנים
    function updateCalendarForReservation(startDate, endDate) {
      console.log("Updating calendar with reservation dates only:", startDate, "to", endDate);
      
      if (!startDate || !endDate) {
        console.error("Missing start or end date");
        return;
      }
      
      try {
        // המרת התאריכים לאובייקטי Date
        const startDateObj = new Date(startDate);
        const endDateObj = new Date(endDate);
        
        // חישוב מספר הימים בטווח ההזמנה
        const timeDiff = endDateObj.getTime() - startDateObj.getTime();
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
        
        for (let i = 0; i < Math.min(dayDiff, maxDisplayDays); i++) {
          const currentDate = new Date(startDateObj);
          currentDate.setDate(startDateObj.getDate() + startingDay + i);
          
          if (currentDate > endDateObj) break;
          
          const currentIsoDate = currentDate.toISOString().split('T')[0];
          const isSelected = i === 0;
          
          daysArray.push({
            date: currentIsoDate,
            display: currentDate.toLocaleDateString('he-IL', { 
              weekday: 'short', 
              day: 'numeric', 
              month: 'short' 
            }),
            isToday: currentDate.toDateString() === new Date().toDateString(),
            isSelected: isSelected
          });
        }
        
        // יצירת מיכל לכל החלקים
        let html = '<div class="calendar-wrapper">';
        
        // יצירת קונטיינר פנימי לחצים ותאריכים
        html += '<div class="calendar-container">';
        
        // החץ הקודם
        html += '<button class="week-nav" id="prev-dates" disabled>→</button>';
        
        // אזור התאריכים
        html += '<div class="calendar-inner">';
        
       daysArray.forEach(day => {
          let classList = [];
          if (day.isToday) classList.push('today');
          if (day.isSelected) classList.push('active');
          
          html += `<button data-date="${day.date}" class="${classList.join(' ')}">${day.display}</button>`;
        });
        
        html += '</div>';
        
        // החץ הבא
        html += `<button class="week-nav" id="next-dates" ${dayDiff > maxDisplayDays ? '' : 'disabled'}>←</button>`;
        
        // סגירת הקונטיינרים
        html += '</div></div>';
        
        // עדכון התצוגה
        calendarContainer.innerHTML = html;
        
        // שמירת מידע לניווט
        calendarContainer.dataset.startIndex = 0;
        calendarContainer.dataset.startDate = startDate;
        calendarContainer.dataset.endDate = endDate;
        calendarContainer.dataset.totalDays = dayDiff;
        calendarContainer.dataset.maxDays = maxDisplayDays;
        
        // הוספת מאזינים
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
      document.querySelectorAll('.calendar-inner button').forEach(btn => {
        btn.addEventListener('click', () => {
          // הסרת סימון מכל הכפתורים
          document.querySelectorAll('.calendar-inner button').forEach(b => b.classList.remove('active'));
          
          // סימון הכפתור הנוכחי
          btn.classList.add('active');
          
          // עדכון התאריך הנבחר
          selectedDay = btn.getAttribute('data-date');
          const dateObj = new Date(selectedDay);
          
          // עדכון הכותרת
          selectedDayDisplay.textContent = dateObj.toLocaleDateString('he-IL', { 
            weekday: 'long', 
            day: 'numeric', 
            month: 'long',
            year: 'numeric'
          });
          
          // עדכון זמני התורים
          updateAvailableTimes();
          // הפונקציה updateAvailableTimes כבר כוללת את הבדיקה לזמינות תורים
        });
      });
      
      // הגדרת אירועי ניווט בין קבוצות תאריכים
      const prevBtn = document.getElementById('prev-dates');
      const nextBtn = document.getElementById('next-dates');
      
      // טיפול בכפתור 'הקודם'
      if (prevBtn) {
          prevBtn.addEventListener('click', () => {
          let currentIndex = parseInt(calendarContainer.dataset.startIndex || 0);
          
          if (currentIndex > 0) {
            // הזזת החלון אחורה
            currentIndex = Math.max(0, currentIndex - maxDisplay);
            navigateToDateRange(startDate, endDate, currentIndex, maxDisplay);
            
            // עדכון כפתורי הניווט
            document.getElementById('next-dates').disabled = false;
            prevBtn.disabled = (currentIndex === 0);
          }
        });
      }
      
      // טיפול בכפתור 'הבא'
      if (nextBtn) {
        nextBtn.addEventListener('click', () => {
          let currentIndex = parseInt(calendarContainer.dataset.startIndex || 0);
          const totalDays = parseInt(calendarContainer.dataset.totalDays || 0);
          
          if (currentIndex + maxDisplay < totalDays) {
            // הזזת החלון קדימה
            currentIndex = Math.min(totalDays - maxDisplay, currentIndex + maxDisplay);
            navigateToDateRange(startDate, endDate, currentIndex, maxDisplay);
            
            // עדכון כפתורי הניווט
            document.getElementById('prev-dates').disabled = false;
            nextBtn.disabled = (currentIndex + maxDisplay >= totalDays);
          }
        });
      }
    }
    
    // ניווט לקבוצת תאריכים מסוימת בטווח ההזמנה
    function navigateToDateRange(startDate, endDate, startIndex, maxDisplay) {
      const startDateObj = new Date(startDate);
      const endDateObj = new Date(endDate);
      const totalDays = parseInt(calendarContainer.dataset.totalDays || 0);
      
      // יצירת מערך של תאריכים לתצוגה
      const daysArray = [];
      
      for (let i = 0; i < maxDisplay; i++) {
        const currentDate = new Date(startDateObj);
        currentDate.setDate(startDateObj.getDate() + startIndex + i);
        
        // בדיקה שלא חרגנו מתאריך הסיום
        if (currentDate > endDateObj) break;
        
        const currentIsoDate = currentDate.toISOString().split('T')[0];
        
        daysArray.push({
          date: currentIsoDate,
          display: currentDate.toLocaleDateString('he-IL', { 
            weekday: 'short', 
            day: 'numeric', 
            month: 'short' 
          }),
          isToday: currentDate.toDateString() === new Date().toDateString(),
          isSelected: i === 0 // היום הראשון נבחר כברירת מחדל
        });
      }
      
      // עדכון הלוח עם מבנה מקונן 
      // הוספת אזור התאריכים בלבד, הקונטיינר כבר קיים
      const calendarInner = document.querySelector('.calendar-inner');
      let innerHtml = '';
      
      daysArray.forEach(day => {
        let classList = [];
        if (day.isToday) classList.push('today');
        if (day.isSelected) classList.push('active');
        
        innerHtml += `<button data-date="${day.date}" class="${classList.join(' ')}">${day.display}</button>`;
      });
      
      // עדכון רק תוכן האזור הפנימי
      calendarInner.innerHTML = innerHtml;
      
      // עדכון מצב כפתורי הניווט
      document.getElementById('prev-dates').disabled = (startIndex <= 0);
      document.getElementById('next-dates').disabled = (startIndex + maxDisplay >= totalDays);
      
      // עדכון אינדקס הנוכחי
      calendarContainer.dataset.startIndex = startIndex;
      
      // בחירת היום הראשון
      if (daysArray.length > 0) {
        selectedDay = daysArray[0].date;
        
        // עדכון הכותרת
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
          selectedDay = btn.getAttribute('data-date');
          const dateObj = new Date(selectedDay);
          
          // עדכון הכותרת
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

    function updateAvailableTimes() {
      // איפוס בחירת שעה קודמת
      timeSlots.forEach(slot => slot.classList.remove('disabled', 'selected'));
      selectedSlot = null;
      
      // אם אין הזמנות פעילות, נשבית את כל השעות
      if (!hasActiveReservations) {
        timeSlots.forEach(slot => slot.classList.add('disabled'));
        // בדיקה אם כל השעות מושבתות
        checkAndDisplayTimeAvailability();
        return;
      }
      
      fetch('getBlockedTimesServer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ day: selectedDay })
      })
      .then(res => res.json())
      .then(data => {
        if (data.blockedTimes && Array.isArray(data.blockedTimes)) {
          data.blockedTimes.forEach(time => {
            document.querySelectorAll('.time-slots div').forEach(slot => {
              if (slot.textContent.trim() === time.trim()) {
                slot.classList.add('disabled');
              }
            });
          });
          
          // בדיקה אם כל השעות מושבתות אחרי עדכון השעות החסומות
          checkAndDisplayTimeAvailability();
        }
      });
    }

    timeSlots.forEach(slot => {
      slot.onclick = () => {
        // אם אין הזמנות פעילות או השעה כבר תפוסה, לא נאפשר בחירה
        if (slot.classList.contains('disabled') || !hasActiveReservations) return;
        
        timeSlots.forEach(s => s.classList.remove('selected'));
        slot.classList.add('selected');
        selectedSlot = slot.textContent;
      }
    });

    function submitAppointment() {
      // וידוא שיש הזמנות פעילות לפני הגשת הטופס
      if (!hasActiveReservations) {
        alert('לא ניתן להזמין טיפוח ללא הזמנת פנסיון פעילה.');
        return;
      }
      
      if (!selectedDay || !selectedSlot) {
        alert('אנא בחר יום ושעה.');
        return;
      }
      
      // הוספת סוג הטיפוח והמחיר אם קיים בסשן
      const appointmentData = { 
        day: selectedDay, 
        time: selectedSlot,
        user_code: userCode,
        dog_id: activeDogId
      };
      
      // הוספת נתוני הטיפוח אם זמינים
      <?php if (isset($_SESSION['grooming_type']) && isset($_SESSION['grooming_price'])) { ?>
        appointmentData.grooming_type = "<?php echo addslashes($_SESSION['grooming_type']); ?>";
        appointmentData.grooming_price = <?php echo intval($_SESSION['grooming_price']); ?>;
      <?php } ?>
      
      if (selectedReservation) {
        appointmentData.reservation_id = selectedReservation.id;
      }
      
      // הודעה שמבקשת מהמשתמש לאשר את ההזמנה
      let confirmMessage = `האם לאשר הזמנת טיפוח ליום ${new Date(selectedDay).toLocaleDateString('he-IL')} בשעה ${selectedSlot}?`;
      
      <?php if (isset($_SESSION['grooming_type']) && isset($_SESSION['grooming_price'])) { ?>
        confirmMessage = `האם לאשר הזמנת טיפוח "${<?php echo json_encode($_SESSION['grooming_type']); ?>}" ליום ${new Date(selectedDay).toLocaleDateString('he-IL')} בשעה ${selectedSlot}?`;
      <?php } ?>
      
      if (!confirm(confirmMessage)) {
        return;
      }
      
      fetch('groomingAppServer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(appointmentData)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert(`ההזמנה נקלטה! מספר אישור: ${data.confirmation}`);
          updateAvailableTimes();
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