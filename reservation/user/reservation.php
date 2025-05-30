<?php include '../../header.php'; ?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="css-reservation.css">
    <title>מסך הזמנה</title>
</head>
<body>
    <!--<header>-->
    <!--    <nav id="navbar">-->
    <!--        <div class="container">-->
    <!--            <a href="#home" class="logo">-->
    <!--                <img src="mylogo.png" alt="לוגו הפנסיון" class="logo-img"> פנסיון לכלבים שלנו-->
    <!--            </a>-->
    <!--            <ul class="nav-links">-->
    <!--                <li><a href="#home">דף הבית</a></li>-->
    <!--                <li><a href="#about-us">עלינו</a></li>-->
    <!--                <li><a href="#services">שירותים</a></li>-->
    <!--                <li><a href="#booking">הזמנות</a></li>-->
    <!--                <li><a href="#contact">צור קשר</a></li>-->
    <!--                <li><a href="#faq">שאלות נפוצות</a></li>-->
    <!--            </ul>-->
    <!--        </div>-->
    <!--    </nav>        -->
    <!--</header>-->
    <h1>הזמנת מקום בפנסיון</h1>
    <form action="reservationServerUpdate.php" method="POST">
    <div class="reservation-form">
        <div id="dateSelection">
            <h1>בחירת תאריכים</h1>
            <div class="form-group">
                <label for="start-date">תאריך התחלה:</label>
                <input type="text" id="start-date" name="start_date" placeholder="הקלד תאריך התחלה"required>
            </div>
            <div class="form-group">
                <label for="end-date">תאריך סיום:</label>
                <input type="text" id="end-date" name="end_date" placeholder=" הקלד תאריך סיום"required>
            </div>
        </div>
        <div id="booking-summary">
            <h3>סה"כ ימים להזמנה</h3>
            <p id="total-days">0 ימים</p>
        </div>

        <button id="submit" class="submit-button">שמור הזמנה והמשך</button>
    </form>

        <div id="message"></div>
    </div>
    

    <footer style="background-color: #f8f9fa; padding: 20px; text-align: center;">
        <div class="footer-content">
            <h2>פנסיון לכלבים שלנו</h2>
            <p>אנחנו מעניקים שירות מקצועי ואוהב לכלבים שלכם, ודואגים לכל הצרכים שלהם במהלך שהותם אצלנו.</p>
    
            <div class="footer-links">
                <a href="#about-us">עלינו</a>
                <a href="#services">שירותים</a>
                <a href="#contact">צור קשר</a>
                <a href="#faq">שאלות נפוצות</a>
            </div>
    
            <div class="contact-info">
                <h3>צור קשר</h3>
                <p>טלפון: <a href="tel:+123456789">+123456789</a></p>
                <p>דוא"ל: <a href="mailto:info@dogpension.com">info@dogpension.com</a></p>
                <p>כתובת: רחוב הכלב, תל אביב, ישראל</p>
            </div>
    
            <div class="social-media">
                <h3>עקבו אחרינו</h3>
                <a href="https://facebook.com" target="_blank">פייסבוק</a> | 
                <a href="https://instagram.com" target="_blank">אינסטגרם</a> | 
                <a href="https://twitter.com" target="_blank">טוויטר</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 פנסיון לכלבים שלנו. כל הזכויות שמורות.</p>
        </div>
    </footer>
    
    <script src="js-reservation.js"></script>
</body>
</html>
