<?php include '../../header.php'; ?>
<!DOCTYPE html>

<html lang="he">
<head>
<meta charset="utf-8"/>
<title>שירותי טיפוח לכלבים</title>
<style>
    body {
      font-family: 'Segoe UI', sans-serif;
      direction: rtl;
      margin: 0;
      padding: 0;
      background: #f9f9f9;
      color: #333;
    }

    header {
      background: linear-gradient(to right, #a3d8f4, #d6f0fc);
      padding: 40px 20px;
      text-align: center;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }

    header h1 {
      margin: 0;
      font-size: 2.5em;
      color: #05445e;
    }

    header p {
      font-size: 1.2em;
      color: #555;
    }

    .treatments-container {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      padding: 40px 30px;
      justify-content: center;
    }

    .treatment-card {
      background: transparent;
      width: 280px;
      height: 546px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      overflow: hidden;
      border-radius: 16px;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      width: 300px;
      /* height: 350px; */
      transition: all 0.3s ease;
      position: relative;
    }

    .treatment-card.active {
      height: auto;
      background-color: #eafaff;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .treatment-card img {
      width: 100%;
      height: auto;
      object-fit: contain;
      background: transparent;
      display: block;
      
      border-top-right-radius: 16px;
      border-top-left-radius: 16px;
    }

    .treatment-info {
      margin-top: auto;
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .treatment-text {
      flex-grow: 1;
    }

    .treatment-info h3 {
      margin: 0 0 5px 0;
      font-size: 1.4em;
      color: #05445e;
    }

    .treatment-info p {
      margin: 0;
      font-weight: bold;
      color: #189ab4;
    }

    .arrow {
      font-size: 1.5em;
      color: #189ab4;
      transition: transform 0.3s ease;
    }

    .treatment-card.active .arrow {
      transform: rotate(180deg);
    }

    .treatment-description {
      display: none;
      padding: 15px 20px;
      background-color: #f1faff;
      border-top: 1px solid #e0f2f7;
      font-size: 0.95em;
      color: #333;
    }

    .treatment-card.active .treatment-description {
      display: block;
    }

    .order-button {
      margin: 5px 15px 8px;
      padding: 8px 16px;
      background-color: #189ab4;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1em;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }

    .order-button:hover {
      background-color: #0f7391;
    }

    footer {
      text-align: center;
      padding: 20px;
      background: #05445e;
      color: white;
    }

    @media (max-width: 768px) {
      .treatments-container {
        padding: 20px;
        gap: 20px;
      }

      .treatment-card {
        width: 100%;
        height: 320px;
      }

      .treatment-card.active {
        height: auto;
      }

      .treatment-info {
      margin-top: auto;
        padding: 15px;
      }

      .treatment-info h3 {
        font-size: 1.2em;
      }

      .treatment-info p {
        font-size: 0.95em;
      }

      .treatment-description {
        font-size: 0.9em;
        padding: 10px 15px;
      }

      .order-button {
        font-size: 0.95em;
        padding: 8px 16px;
        margin: 10px 15px 15px;
      }

      .arrow {
        font-size: 1.2em;
      }
    }
  </style>
</head>
<body>
<header>
<h1>שירותי טיפוח לכלבים</h1>
<p>בחרו את הטיפול המושלם עבור החבר הכי טוב שלכם 🐶</p>
</header>
<div class="treatments-container">
<div class="treatment-card" onclick="toggleCard(this)">
<img alt="רחצה וסירוק" src="images/bath.png"/>
<div class="treatment-info">
<div class="treatment-text">
<h3>רחצה וסירוק</h3>
<p>₪80</p>
</div>
<div class="arrow">▼</div>
</div>
<div class="treatment-description">
        רחצה יסודית עם שמפו טבעי, סירוק מקצועי להסרת קשרים, וניחוח נפלא שיישאר לאורך זמן.
        <br/><br/>
<button class="order-button" onclick="event.stopPropagation(); alert('רחצה וסירוק נוסף להזמנה!')">הזמן עכשיו</button>
</div>
</div>
<div class="treatment-card" onclick="toggleCard(this)">
<img alt="תספורת מקצועית" src="images/cut.png"/>
<div class="treatment-info">
<div class="treatment-text">
<h3>תספורת מקצועית</h3>
<p>₪120</p>
</div>
<div class="arrow">▼</div>
</div>
<div class="treatment-description">
        תספורת לפי סטנדרט גזע או בקשה אישית, עם ציוד מתקדם והתאמה אישית לגודל וסוג הפרווה.
        <br/><br/>
<button class="order-button" onclick="event.stopPropagation(); alert('תספורת מקצועית נוספה להזמנה!')">הזמן עכשיו</button>
</div>
</div>
<div class="treatment-card" onclick="toggleCard(this)">
<img alt="גזיזת ציפורניים" src="images/nails.png"/>
<div class="treatment-info">
<div class="treatment-text">
<h3>גזיזת ציפורניים</h3>
<p>₪40</p>
</div>
<div class="arrow">▼</div>
</div>
<div class="treatment-description">
        גזיזת ציפורניים עדינה ובטוחה עם ציוד מקצועי, לשמירה על נוחות ובריאות כפות הרגליים.
        <br/><br/>
<button class="order-button" onclick="event.stopPropagation(); alert('גזיזת ציפורניים נוספה להזמנה!')">הזמן עכשיו</button>
</div>
</div>
<div style="width: 100%; text-align: center; font-size: 1.3em; font-weight: bold; color: #05445e; margin: 40px 0 10px;">טיפולים נוספים מומלצים</div><div class="treatment-card" onclick="toggleCard(this)">
<img alt="ניקוי אוזניים" src="images/ear.png"/>
<div class="treatment-info">
<div class="treatment-text">
<h3>ניקוי אוזניים</h3>
<p>₪30</p>
</div>
<div class="arrow">▼</div>
</div>
<div class="treatment-description">
    ניקוי יסודי ועדין של תעלות האוזניים למניעת דלקות וריחות לא נעימים.
    <br/><br/>
<button class="order-button" onclick="event.stopPropagation(); alert('ניקוי אוזניים נוסף להזמנה!')">הזמן עכשיו</button>
</div>
</div><div class="treatment-card" onclick="toggleCard(this)">
<img alt="צחצוח שיניים" src="images/teeath.png"/>
<div class="treatment-info">
<div class="treatment-text">
<h3>צחצוח שיניים</h3>
<p>₪35</p>
</div>
<div class="arrow">▼</div>
</div>
<div class="treatment-description">
    טיפול שיניים הכולל הסרת רובד, חיזוק חניכיים וריח פה רענן.
    <br/><br/>
<button class="order-button" onclick="event.stopPropagation(); alert('צחצוח שיניים נוסף להזמנה!')">הזמן עכשיו</button>
</div>
</div><div class="treatment-card" onclick="toggleCard(this)">
<img alt="טיפול בקרציות" src="images/tick.png"/>
<div class="treatment-info">
<div class="treatment-text">
<h3>טיפול בקרציות</h3>
<p>₪60</p>
</div>
<div class="arrow">▼</div>
</div>
<div class="treatment-description">
    טיפול מונע או משמיד נגד טפילים חיצוניים באמצעות תכשירים בטוחים לכלבים.
    <br/><br/>
<button class="order-button" onclick="event.stopPropagation(); alert('טיפול בקרציות ופרעושים נוסף להזמנה!')">הזמן עכשיו</button>
</div>
</div></div>
<footer>
    © 2025 כל הזכויות שמורות לפנסיון הכלבים שלנו
  </footer>
<script>
    function toggleCard(selectedCard) {
      const allCards = document.querySelectorAll('.treatment-card');
      allCards.forEach(card => {
        if (card !== selectedCard) {
          card.classList.remove('active');
        }
      });
      selectedCard.classList.toggle('active');
    }
  </script>
</body>
</html>
