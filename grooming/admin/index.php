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
      
    }
    
    .time-slots {
      justify-content: right;
    }
    
    .booking-section {
      max-width: 1200px !important;
    }
    .calendar {
      justify-content: space-between;
      flex-wrap: nowrap;
    }
    .calendar button {
      flex-shrink: 0;
      min-width: 100px;
    }
    </style>
</head>
<body>
<div class="booking-section">
<h2>הזמנת טיפוח לכלבך</h2>
<div class="service-box">
<img alt="dog grooming" src="images/DogShower.png" style="border-radius: 10px; max-height: 80px;"/>
<div>
<strong>טיפוח מקצועי</strong>
<div>תספורת גזע מותאמת אישית. החל מ־₪50 • 30 דקות</div>
</div>
</div>
<h3 id="selected-day">בחר יום</h3>
<div class="calendar" id="calendar-buttons"></div>
<h4>בוקר</h4>
<div class="time-slots" id="morning-slots">
<div>8:00</div><div>8:30</div><div>9:00</div><div>9:30</div><div>10:00</div><div>11:00</div><div>11:30</div>
</div>
<h4>אחר הצהריים</h4>
<div class="time-slots" id="afternoon-slots">
<div>12:00</div><div>12:30</div><div>13:00</div><div>13:30</div><div>14:00</div><div>14:30</div><div>15:00</div><div>15:30</div><div>16:00</div><div>16:30</div>
</div>
<button class="submit-button" onclick="submitAppointment()">אשר הזמנה</button>
<a class="submit-button link-button" href="adminPanel.html">ניהול הזמנות</a>
</div>
<script>
    const calendarContainer = document.getElementById('calendar-buttons');
    const selectedDayDisplay = document.getElementById('selected-day');
    let selectedSlot = null;
    let selectedDay = null;
    let currentWeek = 0;
    const maxWeeks = 4;
    const timeSlots = document.querySelectorAll('.time-slots div');

    function generateWeek(weekOffset) {
      const start = new Date();
      start.setDate(start.getDate() + weekOffset * 7);
      let html = '<button class="week-nav" id="prev-week">→</button>';
      for (let i = 0; i < 7; i++) {
        const d = new Date(start);
        d.setDate(start.getDate() + i);
        const isoDate = d.toISOString().split('T')[0];
        const display = d.toLocaleDateString('he-IL', { weekday: 'short', day: 'numeric', month: 'short' });
        const isToday = new Date().toDateString() === d.toDateString();
        html += `<button data-date="${isoDate}" class="${isToday ? 'today' : ''}">${display}</button>`;
      }
      html += '<button class="week-nav" id="next-week">←</button>';
      calendarContainer.innerHTML = html;
      setupCalendarEvents();
    }

    function setupCalendarEvents() {
      const buttons = calendarContainer.querySelectorAll('button');
      buttons.forEach(btn => {
        if (!btn.classList.contains('week-nav')) {
          btn.addEventListener('click', () => {
            buttons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedDay = btn.getAttribute('data-date');
            const dateObj = new Date(selectedDay);
            selectedDayDisplay.textContent = dateObj.toLocaleDateString('he-IL', { weekday: 'long', day: 'numeric', month: 'long' });
            updateAvailableTimes();
          });
        }
      });
      document.getElementById('next-week').onclick = () => {
        if (currentWeek < maxWeeks - 1) {
          currentWeek++;
          generateWeek(currentWeek);
        }
      };
      document.getElementById('prev-week').onclick = () => {
        if (currentWeek > 0) {
          currentWeek--;
          generateWeek(currentWeek);
        }
      };
    }

    function updateAvailableTimes() {
      timeSlots.forEach(slot => slot.classList.remove('disabled', 'selected'));
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
        }
      });
    }

    timeSlots.forEach(slot => {
      slot.onclick = () => {
        if (slot.classList.contains('disabled')) return;
        timeSlots.forEach(s => s.classList.remove('selected'));
        slot.classList.add('selected');
        selectedSlot = slot.textContent;
      }
    });

    function submitAppointment() {
      if (!selectedDay || !selectedSlot) {
        alert('אנא בחר יום ושעה.');
        return;
      }
      fetch('groomingAppServer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ day: selectedDay, time: selectedSlot })
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

    generateWeek(currentWeek);
  </script>
</body>
</html>
