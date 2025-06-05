<?php include '../../header.php'; ?>
<?php

// התחברות למסד הנתונים
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

// יוצר חיבור למסד
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// מקבל את קוד המשתמש מהסשן
$current_user = $_SESSION['user_code'];
// מערך ריק כדי לאחסן את נתוני המשתמש
$user_data = [];

//  שאילתה מוכנה מראש לבחירת הנתונים האישיים של משתמש מתוך טבלת המשתמשים
$stmt = $conn->prepare("SELECT first_name, last_name, email, phone, city, street, house_number, zip_code FROM users WHERE user_code = ?");
$stmt->bind_param("s", $current_user);
// מריץ את השאילתה עם הפרמטרים
$stmt->execute();
// שולף את תוצאות השאילתה כאובייקט של תוצאה
$result = $stmt->get_result();

// אם נמצא תוצאה עבור משתמש עם קוד כזה
if ($result->num_rows > 0) {
  // שואב את השורה הראשונה כתוצאה ומחזיר אותה כמילון
    $user_data = $result->fetch_assoc();
}

// בודק האם הדף נטען כתוצאה משליחת טופס
// כך מבדילים בין טעינה רגילה של הדף לבין שליחה של טופס עדכון
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // יוצר מערך ריק שישמש לרשימת כל השדות החסרים (כלומר שדות שלא מולאו בטופס)
  $missing_fields = [];
  // עובר על רשימת שמות השדות בטופס שאמורים להישלח
  foreach (["first_name", "last_name", "email", "phone", "city", "street", "house_number", "zip_code"] as $field) {
    // בודק אם הערך של השדה הזה בטופס ריק
    if (empty($_POST[$field])) {
      // אם השדה ריק, מוסיפים את שמו לרשימת השדות החסרים
      $missing_fields[] = $field;
    }
  }

  if (!empty($missing_fields)) {
    // מחברת את כל איברי המערך למחרוזת אחת, כאשר היא שמה פסיק ורווח בין כל ערך
    $message = "אנא מלא את השדה: " . implode(", ", $missing_fields);
    $error = true;
  } else {
    // אם לא חסר אף שדה — שואב את כל הערכים מהטופס ומכניס אותם למשתנים עם שמות נוחים
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $street = $_POST['street'];
    $house_number = $_POST['house_number'];
    $zip_code = $_POST['zip_code'];

    // שאילתה לעדכון כל שדות המשתמש לפי קוד המשתמש
    $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, phone=?, city=?, street=?, house_number=?, zip_code=? WHERE user_code=?");
    $stmt->bind_param("sssssssss", $first_name, $last_name, $email, $phone, $city, $street, $house_number, $zip_code, $current_user);
    $stmt->execute();

    //  הודעת הצלחה
    $message = "הפרטים עודכנו בהצלחה";
    // לצורך הצגת עיצוב מתאים בהודעה למשתמש
    $error = false;
    // מעדכן את שם המשתמש בסשן
    $_SESSION['first_name'] = $first_name;

    // יוצר מערך מעודכן עם כל הפרטים שהוזנו בטופס
    // המערך הזה ישמש לטעינה מחודשת של הטופס עם הערכים המעודכנים
    $user_data = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'phone' => $phone,
        'city' => $city,
        'street' => $street,
        'house_number' => $house_number,
        'zip_code' => $zip_code
    ];
  }
}
?>
<!DOCTYPE html>
<html lang="he">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>עדכון פרטים אישיים</title>
  <style>
    /* מרכז את התוכן באמצע, מוסיף רקע אפור, מיישר לימין ומסיר שוליים */
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      padding: 20px;
      box-sizing: border-box;
    }

    /* רקע לבן עם ריווח, פינות עגולות, צל ומיישר את התוכן לימין */
    form {
      background: white;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 500px;
      direction: rtl;
    }

    /* כותרת הטופס - ממקם את הכותרת במרכז עם רווח מלמטה */
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #2c3e50;
      font-size: 24px;
    }

    /* שדות הטופס - ריווח , מסגרת אפורה ופינות עגולות */
    input {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-sizing: border-box;
      font-size: 16px;
      direction: rtl;
      text-align: right;
    }

    /* עיצוב לשדות לא תקינים */
    /* 
    צובע באדום את שדה הטופס אם המשתמש התחיל למלא את השדה עם ערך לא חוקי לפי ההגדרות של הטופס
     
    input:invalid — מזהה שדה עם ערך לא חוקי לפי ההגדרות של הטופס.

    :not(:placeholder-shown) — בודק שהשדה לא ריק והמשתמש הקליד משהו

    */
    input:invalid:not(:placeholder-shown) {
      border: 2px solid #e74c3c;
    }

    /* מצב מיקוד - מסגרת כחולה */
    input:focus {
      border: 2px solid #3498db;
      box-shadow: 0 0 15px rgba(52, 152, 219, 0.3);
      outline: none;
    }

    /* הודעות שגיאה */
    .error-message {
      color: #e74c3c;
      font-size: 12px;
      margin-top: -12px;
      margin-bottom: 10px;
      display: none;
      font-weight: 500;
    }

    /* הודעת הצלחה/שגיאה */
    .message {
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: 500;
      text-align: center;
    }

    .message.success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .message.error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    /* כפתורי פעולה */
    .button-group {
      display: flex;
      gap: 10px;
      margin-bottom: 10px;
    }

    /* כפתור איפוס */
    /* כפתור רחב, כהה עם אפקט לחיצה  */
    button {
      width: 100%;
      padding: 12px;
      background-color: #2c3e50;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: transform 0.1s ease-in-out;
      margin-bottom: 10px;
    }

   /* אנימציה קצרה של "התכווצות" לכפתור בעת לחיצה */
    button:active {
      transform: scale(0.97);
    }

    /* כפתור איפוס לערכים הנוכחיים במסד הנתונים */
    button.reset {
      background-color: #95a5a6;
    }

    /* כפתור ניקוי שדות */
    button.clear {
      background-color: #e67e22;
    }

    /* כפתור עדכון פרטים */
    button.submit {
      width: 100%;
      background-color: #3498db;
      margin-top: 0px;
    }

    button:hover {
      opacity: 0.9;
    }

    /* רספונסיביות */
    @media (max-width: 600px) {
      body {
        padding: 10px;
      }
      
      form {
        padding: 20px;
      }
      
      h2 {
        font-size: 20px;
      }
      
      .button-group {
        flex-direction: column;
      }
    }


    ::placeholder {
      color: #7f8c8d;
    }

    /* אנימציה לטעינה */
    .loading button.submit {
      background-color: #95a5a6;
      cursor: not-allowed;
    }

    .loading input {
      opacity: 0.6;
    }

    /* כפתור כללי בדף */
    .btn {
        /* מאפשר להציג אותו בשורה אחת עם טקסט אם צריך, אך גם לתפקד כמו בלוק */
        display: inline-block;
        /* כחול כהה */
        background-color: var(--primary-color);
        /* טקסט לבן */
        color: white;
        padding: 12px 24px;
        /* מסיר גבול חיצוני ברירת-מחדל של הדפדפן */
        border: none;
        border-radius: 6px;
        /* משנה את סמן העכבר ליד בעת מעבר מעל הכפתור */
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        /* מיישר את תוכן הכפתור */
        text-align: center;
        transition: all 0.3s;
    }
    /* כפתור חזרה לאזור האישי  */
    .btn-back {
        /* צבע רקע אפור כהה */
        background-color: #6c757d;
        /* צבע טקסט לבן  */
        color: white;
        /* מבטל קו תחתון שיש לקישורים */
        text-decoration: none;
        /* מציג את האלמנט בשורה אחת עם אייקון וטקסט */
        display: inline-flex;
        /* ממרכז את האייקון והטקסט לגובה אחד */
        align-items: center;
        margin-bottom: 20px;
    }
    
    .btn-back:hover {
        /* צבע רקע אפור כהה יותר */
        background-color: #5a6268;
        color: white;
        /* שלא יופיע קו תחתון */
        text-decoration: none;
    }
    
    /* עיצוב לאייקון בתוך כפתור החזרה */
    .btn-back i {
        margin-left: 8px;
    }
  </style>
</head>
<body>

  <form id="updateForm" action="update_User_profile.php" method="post" novalidate>

        <!-- כפתור חזרה -->
        <a href="../../registration/user/user_dashboard_secured.php" class="btn btn-back">
            <i class="fas fa-arrow-right"></i> חזרה לאזור האישי
        </a>

    <h2>עדכון פרטים אישיים</h2>
    
    <?php if (!empty($message)): ?>
      <div class="message <?= $error ? 'error' : 'success' ?>" id="msgBox">
        <?= $message ?>
      </div>
    <?php endif; ?>
    
    <!-- מגדיר כלל לבדיקה  עבור שדה בטופס — מתי הערך נחשב תקין -->
    <!-- הדפדפן בודק אם הוא תואם את ה־ pattern -->
    <!-- כששדה <input> לא עומד בתנאי שהוגדר ב־ pattern,  הוא יקבל את המצב :invalid -->
    <input id="first_name" name="first_name" placeholder="שם פרטי" 
           value="<?php echo htmlspecialchars($user_data['first_name'] ?? '') ?>" 
           required pattern="^[\u0590-\u05FF\s]{2,50}$" />
    <div class="error-message" id="first_name-error">יש להזין שם פרטי תקין בעברית (2-50 תווים)</div>
    
    <input id="last_name" name="last_name" placeholder="שם משפחה" 
           value="<?php echo htmlspecialchars($user_data['last_name'] ?? '') ?>" 
           required pattern="^[\u0590-\u05FF\s]{2,50}$" />
    <div class="error-message" id="last_name-error">יש להזין שם משפחה תקין בעברית (2-50 תווים)</div>
    
    <input id="email" name="email" type="email" placeholder="כתובת אימייל" 
           value="<?php echo htmlspecialchars($user_data['email'] ?? '') ?>" 
           required pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" />
    <div class="error-message" id="email-error">יש להזין כתובת אימייל תקינה</div>
    
    <input id="phone" name="phone" placeholder="מספר טלפון" 
           value="<?php echo htmlspecialchars($user_data['phone'] ?? '') ?>" 
           required pattern="^(05[0-9]{8}|0[2-4,8-9][0-9]{7,8})$" type="tel" />
    <div class="error-message" id="phone-error">יש להזין מספר טלפון ישראלי תקין (לדוגמה: 0501234567)</div>
    
    <input id="city" name="city" placeholder="עיר מגורים" 
           value="<?php echo htmlspecialchars($user_data['city'] ?? '') ?>" 
           required pattern="^[\u0590-\u05FFa-zA-Z\s]{2,50}$" />
    <div class="error-message" id="city-error">יש להזין שם עיר תקין (2-50 תווים, אותיות בלבד)</div>
    
    <input id="street" name="street" placeholder="רחוב" 
           value="<?php echo htmlspecialchars($user_data['street'] ?? '') ?>" 
           required pattern="^[\u0590-\u05FFa-zA-Z0-9\s]{2,100}$" />
    <div class="error-message" id="street-error">יש להזין שם רחוב תקין (2-100 תווים)</div>
    
    <input id="house_number" name="house_number" placeholder="מספר בית" 
           value="<?php echo htmlspecialchars($user_data['house_number'] ?? '') ?>" 
           required pattern="^[0-9]{1,4}[a-zA-Z\u0590-\u05FF]?$" type="text" />
    <div class="error-message" id="house_number-error">יש להזין מספר בית תקין (עד 4 ספרות, אופציונלי אות)</div>
    
    <input id="zip_code" name="zip_code" placeholder="מיקוד" 
           value="<?php echo htmlspecialchars($user_data['zip_code'] ?? '') ?>" 
           required pattern="^[0-9]{5,7}$" type="text" />
    <div class="error-message" id="zip_code-error">יש להזין מיקוד תקין (5-7 ספרות)</div>
    
    <button type="button" class="reset" onclick="resetForm()">איפוס לערכים מקוריים</button>
    <button type="button" class="clear" onclick="clearAllFields()">נקה את כל השדות</button>
    <button type="submit" class="submit">עדכן פרטים</button>
  </form>

<script>
  // פונקציה לאיפוס הטופס וחזרה לערכים המקוריים במסד
  function resetForm() {
    // שמירת הערכים המקוריים מהשרת
    const originalValues = {
      first_name: "<?php echo htmlspecialchars($user_data['first_name'] ?? '') ?>",
      last_name: "<?php echo htmlspecialchars($user_data['last_name'] ?? '') ?>",
      email: "<?php echo htmlspecialchars($user_data['email'] ?? '') ?>",
      phone: "<?php echo htmlspecialchars($user_data['phone'] ?? '') ?>",
      city: "<?php echo htmlspecialchars($user_data['city'] ?? '') ?>",
      street: "<?php echo htmlspecialchars($user_data['street'] ?? '') ?>",
      house_number: "<?php echo htmlspecialchars($user_data['house_number'] ?? '') ?>",
      zip_code: "<?php echo htmlspecialchars($user_data['zip_code'] ?? '') ?>"
    };
    
    // החזרת הערכים המקוריים
    // originalValues הוא אובייקט שמכיל את הערכים המקוריים של הטופס
    // מחלץ ממנו את כל שמות השדות
    // עובר על כל מפתח
    Object.keys(originalValues).forEach(key => {
      // עבור כל שדה הוא מנסה למצוא אלמנט עם מזהה בשם של המפתח
      const element = document.getElementById(key);
      // בודק שאכן נמצא אלמנט כזה בדף
      if (element) {
        // שותל את הערך לתוך שדה הטופס המתאים
        element.value = originalValues[key];
      }
    });
    
    // איפוס כל הודעות השגיאה
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach(message => {
      message.style.display = 'none';
    });
    
    // הסתרת הודעת הצלחה/שגיאה
    const msgBox = document.getElementById('msgBox');
    if (msgBox) {
      msgBox.style.display = 'none';
    }
  }

  // פונקציה לניקוי כל השדות
  function clearAllFields() {
    // ניקוי כל השדות לריק
    const fields = ['first_name', 'last_name', 'email', 'phone', 'city', 'street', 'house_number', 'zip_code'];
    
    fields.forEach(fieldName => {
      const element = document.getElementById(fieldName);
      if (element) {
        element.value = '';
      }
    });
    
    // איפוס כל הודעות השגיאה
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach(message => {
      message.style.display = 'none';
    });
    
    // הסתרת הודעת הצלחה/שגיאה
    const msgBox = document.getElementById('msgBox');
    if (msgBox) {
      msgBox.style.display = 'none';
    }
    
    // פוקוס על השדה הראשון
    const firstField = document.getElementById('first_name');
    if (firstField) {
      firstField.focus();
    }
  }

  // פונקציה לבדיקת תקינות של שדה
  function validateField(field) {
    // מאתר את אלמנט השגיאה שקשור לשדה
    const errorElement = document.getElementById(`${field.id}-error`);
    
    // בודק עם הדפדפן האם השדה עומד בכל התנאים שלו
    // required, pattern
    if (field.checkValidity()) {

      // אם תקין מסתיר את הודעת השגיאה
      errorElement.style.display = 'none';
      return true;
    } else {
      // אחרת מציג את הודעת השגיאה של האלמנט
      errorElement.style.display = 'block';
      return false;
    }
  }

  // בדיקת תקינות לכל השדות
  function validateAllFields() {
    // שולף את כל שדות הטופס
    const fields = document.querySelectorAll('input[required]');
    let isValid = true;
    
    // עובר על כל השדות בטופס, ובודק אם כל שדה תקין
    fields.forEach(field => {
      if (!validateField(field)) {
        isValid = false;
      }
    });
    
    // בדיקה נוספת לשדה מיקוד
    const zipField = document.getElementById('zip_code');
    // בודק אם המשתמש כתב משהו בשדה
    //  בודק אם הערך לא תקין לפי התנאים שהוגדרו בטופס
    if (zipField.value.trim() && !zipField.checkValidity()) {
      validateField(zipField);
      isValid = false;
    }
    
    return isValid;
  }

  // הוספת מאזינים לאירועים

  // מחכה שכל הדף ייטען לפני שמריצים את הקוד
  document.addEventListener('DOMContentLoaded', function() {
    // הוספת ולידציה בעת שינוי בשדות
    // שולף את כל שדות הטופס
    const inputs = document.querySelectorAll('input');

    // לכל שדה מוסיפים: אירוע בדיקת שדה תקין כאשר המשתמש מקליד ואירוע כאשר המשתמש עובר לשדה אחר
    inputs.forEach(input => {
    // אירוע בדיקת שדה תקין כאשר המשתמש מקליד
      input.addEventListener('input', function() {
        validateField(this);
      });
      
    // אירוע בדיקת שדה תקין כאשר המשתמש עובר לשדה אחר
      input.addEventListener('blur', function() {
        validateField(this);
      });
    });
    
    // ולידציה בעת שליחת הטופס
    document.getElementById('updateForm').addEventListener('submit', function(e) {
      if (!validateAllFields()) {
        // עוצר את פעולת הגשת הטופס
        e.preventDefault();
        alert('אנא תקן את השגיאות בטופס לפני עדכון הפרטים');
        return false;
      }
      
      // הוספת מצב טעינה
      this.classList.add('loading');
      const submitBtn = this.querySelector('.submit');
      submitBtn.textContent = 'מעדכן...';
      submitBtn.disabled = true;
    });
  });

  // הסרת הודעה אחרי חמש וחצי שניות
  setTimeout(() => {
    const msg = document.getElementById('msgBox');
    if (msg) {
      // מוריד את השקיפות של ההודעה ל־0
      msg.style.opacity = '0';
      // מגדיר שהשינוי ייעשה בצורה חלקה תוך 0.5 שניות
      msg.style.transition = 'opacity 0.5s ease';
      setTimeout(() => {
        // להסיר את ההודעה מהדף אחרי חצי שנייה
        msg.style.display = 'none';
      }, 500);
    }
    // אחרי 5 שניות מתחילה האנימציה, כלומר כל החמש שניות ההודעה מופיעה
  }, 5000);
</script>

</body>
</html>