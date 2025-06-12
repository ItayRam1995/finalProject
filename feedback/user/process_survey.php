<?php include '../../header.php'; ?>
<?php
// התחברות למסד הנתונים
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);

// בדיקת חיבור
if ($conn->connect_error) {
    die("חיבור למסד הנתונים נכשל: " . $conn->connect_error);
}

$showThankYou = false; // משתנה שיקבע אם להציג את הודעת התודה
$errorMessage = "";

// אם נשלח טופס
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $overallExperience = $_POST['overallExperience'];
    $treatmentExperience = $_POST['treatmentExperience'];
    $stayExperience = $_POST['stayExperience'];
    $staffExperience = $_POST['staffExperience'];
    $additionalFeedback = $_POST['additionalFeedback'];

    // בדיקה אם כל השדות החובה מלאים
    if (empty($overallExperience) || empty($treatmentExperience) || empty($stayExperience) || empty($staffExperience)) {
        $errorMessage = "אנא ודא שכל השדות החובה מלאים.";
    } else {
        // הוספת הנתונים לטבלה
        $sql = "INSERT INTO feedback (overallExperience, treatmentExperience, stayExperience, staffExperience, additionalFeedback) 
                VALUES (?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssss", $overallExperience, $treatmentExperience, $stayExperience, $staffExperience, $additionalFeedback);
            if ($stmt->execute()) {
                $showThankYou = true;
                header("refresh:3;url=user_dashboard_secured.php"); // הפניה לדף הדשבורד
            } else {
                $errorMessage = "שגיאה בביצוע השאילתה. נסה שוב מאוחר יותר.";
            }
            $stmt->close();
        } else {
            $errorMessage = "שגיאה בהכנת השאילתה: " . htmlspecialchars($conn->error);
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>מילוי סקר</title>
    <style>
        body {
            font-family: 'Alef', Arial, sans-serif;
            background: linear-gradient(135deg, #eef7fc, #c9e1f5);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #2c3e50;
        }
        .container {
            text-align: center;
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 500px;
        }
        .thank-you-message {
            font-size: 1.8em;
            margin-bottom: 10px;
            color: #2980b9;
            font-weight: bold;
        }
        .sub-message {
            font-size: 1.2em;
            margin-bottom: 20px;
            color: #34495e;
        }
        .error-message {
            color: #e74c3c;
            font-size: 1.1em;
            margin-bottom: 20px;
        }
        form label {
            font-size: 1.1em;
            color: #34495e;
            display: block;
            margin-top: 10px;
        }
        form input, form textarea, form button {
            width: 100%;
            margin-top: 10px;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
        }
        form input:focus, form textarea:focus {
            border-color: #2980b9;
            box-shadow: 0 0 5px rgba(41, 128, 185, 0.3);
        }
        form button {
            background: #2980b9;
            color: #ffffff;
            border: none;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        form button:hover {
            background: #1a5276;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($showThankYou): ?>
            <div class="thank-you-message">
             תודה על מילוי המשוב - נשלח בהצלחה
            </div>
            <div class="sub-message">
                .המשוב שלך חשוב לנו על מנת שנוכל להשתפר תמיד
            </div>
        <?php else: ?>
            <?php if (!empty($errorMessage)): ?>
                <div class="error-message"><?= htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>
            <form method="POST">
                <label>חוויה כללית:</label>
                <input type="text" name="overallExperience" placeholder="כתוב את החוויה שלך" required>
                
                <label>טיפולים:</label>
                <input type="text" name="treatmentExperience" placeholder="תאר את חווית הטיפולים" required>
                
                <label>שהייה:</label>
                <input type="text" name="stayExperience" placeholder="תאר את חווית השהייה" required>
                
                <label>צוות:</label>
                <input type="text" name="staffExperience" placeholder="מה דעתך על הצוות?" required>
                
                <label>משוב נוסף:</label>
                <textarea name="additionalFeedback" placeholder="הוסף משוב נוסף" rows="4"></textarea>
                
                <button type="submit">שלח משוב</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

