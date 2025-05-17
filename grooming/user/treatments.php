<?php include '../../header.php'; ?>
<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="utf-8"/>
    <title>שירותי טיפוח לכלבים</title>
    <style>
        /* עיצוב כללי של גוף הדף – רקע אפור בהיר מימין לשמאל */
        body {
            font-family: 'Segoe UI', sans-serif;
            direction: rtl;
            margin: 0;
            padding: 0;
            background: #f9f9f9;
            color: #333;
        }

        /* כותרת עליונה – רקע גרדיאנט, יישור למרכז, ריפוד וצל  */
        header {
            background: linear-gradient(to right, #a3d8f4, #d6f0fc);
            padding: 40px 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        /* כותרת ראשית גדולה – צבע כחול  */
        header h1 {
            margin: 0;
            font-size: 2.5em;
            color: #05445e;
        }

        /* פסקת משנה בכותרת – טקסט אפור */
        header p {
            font-size: 1.2em;
            color: #555;
        }

        /* קונטיינר של כרטיסי הטיפוח  */
        .treatments-container {
            /* מאפשר סידור אלמנטים אופקי כברירת מחדל */
            display: flex;
            /* מאפשר שבירה לשורות כאשר אין מספיק מקום לרוחב */
            flex-wrap: wrap;
            gap: 30px;
            padding: 40px 30px;
            justify-content: center;
        }

        /* כרטיס טיפול – עיצוב בסיסי עם הצללה */
        .treatment-card {
            background: transparent;
            width: 300px;
            height: 546px;
            display: flex;
            /* כל התוכן (תמונה, כותרת, תיאור, כפתור) מוצג בטור מלמעלה למטה */
            flex-direction: column;
            border-radius: 16px;
            /* צל להבלטת הכרטיס */
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            /* מונע גלילה פנימית וחותך תוכן חריג */
            overflow: hidden;
            /* האלמנטים יצמדו להתחלה (למעלה), ולא יתפרסו לגובה */
            justify-content: flex-start;
            transition: all 0.3s ease;
            /* מאפשר מיקום יחסי של אלמנטים פנימיים כמו החץ או תגיות נוספות */
            position: relative;
        }

        /* כרטיס טיפול שנפתח, כלומר כשהמשתמש לוחץ עליו ורוצה לראות את התיאור המורחב והכפתור להזמנה */
        /* treatment-card יורש גם את כל הסגנונות שהיו ב */
        .treatment-card.active {
            /* משנה את הגובה של הכרטיס מגובה קבוע (546) ל־גובה אוטומטי שמתאים לתוכן בפנים */
            height: auto;
            /* צבע רקע תכלת־בהיר */
            background-color: #eafaff;
            /* מוסיף צל עמוק יותר לכרטיס הפתוח */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        /* התמונה בראש כל כרטיס טיפול */
        .treatment-card img {
            /* גורם לתמונה לתפוס את כל רוחב הכרטיס */
            width: 100%;
            /* שומר על יחס הגובה-רוחב המקורי של התמונה */
            height: auto;
            /* מותח את התמונה כך שתתאים למסגרת בלי להיחתך, גם אם יש אזור ריק */
            object-fit: contain;
            background: transparent;
            /* מבטל רווחים מיותרים מתחתיה */
            display: block;

            /* מעגל את הפינות העליונות של התמונה – כדי שיתאימו לעיגול של הכרטיס כולו */
            border-top-right-radius: 16px;
            border-top-left-radius: 16px;
        }

        /* החלק שבתוך כרטיס טיפוח – שמכיל מצד אחד את שם הטיפול והמחיר, ומהצד השני את החץ שנפתח/נסגר. */
        .treatment-info {
            /*  דוחף את אזור המידע לתחתית הכרטיס - מנצל את כל מה שנשאר ודוחף את זה למטה*/
            margin-top: auto;
            padding: 20px;
            /* כדי לשלוט על סידור פנימי של האלמנטים */
            display: flex;
            /* מציב את שם הטיפול והמחיר מצד ימין, ואת החץ בצד שמאל – עם רווח מקסימלי ביניהם */
            justify-content: space-between;
            align-items: center;
        }

        /* קובע כמה מקום האלמנט יתפוס מתוך השורה יחסית לאלמנטים לאחרים */
        .treatment-text {
            /* הטקסט שמכיל את שם הטיפול והמחיר מרחיק את החץ לצד השני ומותח את עצמו עד כמה שאפשר */
            flex-grow: 1;
        }

        /* שם הטיפול בתוך כל כרטיס טיפוח */
        .treatment-info h3 {
            margin: 0 0 5px 0;
            font-size: 1.4em;
            color: #05445e;
        }

        /* מחיר הטיפול בתוך כל כרטיס טיפוח */
        .treatment-info p {
            margin: 0;
            font-weight: bold;
            color: #189ab4;
        }

        /* החץ בתוך כל כרטיס טיפוח */
        .arrow {
            font-size: 1.5em;
            color: #189ab4;
            transition: transform 0.3s ease;
        }

        /* אנימציית סיבוב לחץ כאשר כרטיס טיפוח נפתח */
        .treatment-card.active .arrow {
            transform: rotate(180deg);
        }

        /* תיאור הטיפול שמופיע כשכרטיס טיפוח נפתח */
        .treatment-description {
            /* active התיאור מוסתר עד שהכרטיס מקבל את המחלקה */
            display: none;
            padding: 15px 20px;
            background-color: #f1faff;
            border-top: 1px solid #e0f2f7;
            font-size: 0.95em;
            color: #333;
        }

        /* תיאור הטיפול שנמצא ספציפית בתוך כרטיס טיפול שהוא פעיל */
        .treatment-card.active .treatment-description {
            /* משנה את מצב התצוגה ממוסתר לנראה ויתפוס את כל רוחב השורה */
            display: block;
        }

        /* כפתור "הזמן עכשיו" שמופיע בתוך כל כרטיס טיפוח לאחר פתיחה */
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

        /*  הכותרת התחתונה של הדף */
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
        
        /* מסך טעינה עם אנימציית סיבוב שנועד להופיע מעל כל הדף בזמן שהמערכת מטפלת בבקשה*/
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            /* מכסה את כל הדף */
            width: 100%;
            height: 100%;
            /* יוצר אפקט של מסך קפוא" */
            background-color: rgba(255, 255, 255, 0.7);

            /* מבטיח שהמסך יהיה מעל כל האלמנטים האחרים */
            /* האלמנט יקבל עדיפות גבוהה מאוד בשכבות של הדף */
            z-index: 1000;

            /* ממרכז את הספינר במרכז המסך */
            justify-content: center;
            align-items: center;
        }
        
        .loading-spinner {
            /* מגדיר טבעת עבה אפורה */
            border: 5px solid #f3f3f3;
            /* החלק העליון בצבע תכלת */
            border-top: 5px solid #189ab4;
            /* הופך את האלמנט לעיגול */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            /* מפעיל אנימציית סיבוב קבועה */
            /* הסיבוב מתבצע כל שנייה */
            animation: spin 1s linear infinite;
        }
        
        /* מגדיר את אנימציית הסיבוב של העיגול (מ-0 עד 360) */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <header>
        <h1>שירותי טיפוח לכלבים</h1>
        <p>בחרו את הטיפול המושלם עבור החבר הכי טוב שלכם 🐶</p>
    </header>

    <!-- מסך טעינה כשהמערכת מטפלת בבקשה - בהתחלה הוא מוסתר -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- אלמנט העטיפה הראשי לכל הכרטיסים -->
    <div class="treatments-container">
        <!-- מחליף את המחלקה active כדי להציג תיאור וכפתור -->
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
                <!--  toggleCard() בשביל שלא כל לחיצה על "הזמן עכשיו" גם תפתח/תסגור את הכרטיס באמצעות -->
                <!-- מפעיל את הפונקציה ששולחת את הנתונים לשרת -->
                <button class="order-button" onclick="event.stopPropagation(); orderGroomingService('רחצה וסירוק', 80)">הזמן עכשיו</button>
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
                <button class="order-button" onclick="event.stopPropagation(); orderGroomingService('תספורת מקצועית', 120)">הזמן עכשיו</button>
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
                <button class="order-button" onclick="event.stopPropagation(); orderGroomingService('גזיזת ציפורניים', 40)">הזמן עכשיו</button>
            </div>
        </div>
        
        <div style="width: 100%; text-align: center; font-size: 1.3em; font-weight: bold; color: #05445e; margin: 40px 0 10px;">טיפולים נוספים מומלצים</div>
        
        <div class="treatment-card" onclick="toggleCard(this)">
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
                <button class="order-button" onclick="event.stopPropagation(); orderGroomingService('ניקוי אוזניים', 30)">הזמן עכשיו</button>
            </div>
        </div>
        
        <div class="treatment-card" onclick="toggleCard(this)">
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
                <button class="order-button" onclick="event.stopPropagation(); orderGroomingService('צחצוח שיניים', 35)">הזמן עכשיו</button>
            </div>
        </div>
        
        <div class="treatment-card" onclick="toggleCard(this)">
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
                <button class="order-button" onclick="event.stopPropagation(); orderGroomingService('טיפול בקרציות', 60)">הזמן עכשיו</button>
            </div>
        </div>
    </div>
    
    <footer>
        © 2025 כל הזכויות שמורות לפנסיון הכלבים שלנו
    </footer>
    
    <script>

        // ניהול פתיחה וסגירה של כרטיס טיפוח כולל דאגה לכך שרק כרטיס אחד ייפתח בכל רגע.
        // selectedCard הוא הכרטיס שנלחץ
        function toggleCard(selectedCard) {
          // מאתר את כל הכרטיסים בדף
            const allCards = document.querySelectorAll('.treatment-card');
            // עובר על כל הכרטיסים
            allCards.forEach(card => {
              // active אם הכרטיס הנוכחי לא זה שנלחץ הוא מסיר ממנו את המחלקה 
                if (card !== selectedCard) {
                    card.classList.remove('active');
                }
            });
            // מוסיף או מסיר את המחלקה active מהכרטיס שנלחץ
            selectedCard.classList.toggle('active');
        }
        
        // פונקציה להזמנת שירות טיפוח
        function orderGroomingService(type, price) {
            // הצגת מסך הטעינה והספינר
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            // יצירת בקשת fetch לשמירת סוג הטיפוח והמחיר ב-SESSION
            // לשלוח מידע לשרת או לקבל ממנו תגובה, בלי לרענן את הדף
            fetch('saveGroomingType.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                // JSON מצרף לגוף הבקשה אובייקט 
                body: JSON.stringify({
                    grooming_type: type,
                    grooming_price: price
                })
            })
            // ממירה את תשובת השרת (JSON) לאובייקט JavaScript
            .then(response => response.json())
            .then(data => {
                // בדיקה אם השמירה הצליחה
                if (data.success) {
                    // מעבר לדף הזמנת תור
                    window.location.href = '../user/doGroomingAppointment.php';
                } else {
                    // הצגת הודעת שגיאה
                    alert('אירעה שגיאה: ' + data.error);
                    document.getElementById('loadingOverlay').style.display = 'none';
                }
            })
            // מופעל רק אם הבקשה עצמה נכשלה כי השרת לא זמין או שאין חיבור לרשת
            .catch(error => {
                console.error('שגיאה:', error);
                alert('אירעה שגיאה בתהליך ההזמנה. אנא נסה שנית מאוחר יותר.');
                document.getElementById('loadingOverlay').style.display = 'none';
            });
        }
    </script>
</body>
</html>