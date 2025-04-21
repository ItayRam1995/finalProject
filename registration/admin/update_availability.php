<?php
include '../includes/header.php';

// התחברות למסד נתונים
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// טיפול בטופס
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date']) && isset($_POST['available_spots'])) {
    $date = $_POST['date'];
    $spots = $_POST['available_spots'];

    // בדיקה אם התאריך קיים בטבלה
    $stmt = $conn->prepare("SELECT * FROM Availability WHERE date = ?");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // עדכון שורה קיימת
        $update = $conn->prepare("UPDATE Availability SET available_spots = ? WHERE date = ?");
        $update->bind_param("is", $spots, $date);
        $update->execute();
        $message = "הזמינות עודכנה בהצלחה.";
    } else {
        // הוספת תאריך חדש
        $insert = $conn->prepare("INSERT INTO Availability (date, available_spots) VALUES (?, ?)");
        $insert->bind_param("si", $date, $spots);
        $insert->execute();
        $message = "נוספה זמינות חדשה.";
    }
}
?>

<!DOCTYPE html>
<html lang="he">
<head>
  <meta charset="UTF-8">
  <title>עדכון זמינות תאריכים</title>
  <style>
    body { font-family: Arial, sans-serif; direction: rtl; padding: 20px; background-color: #f6f6f6; }
    form {
      background: white; padding: 20px; border-radius: 10px;
      max-width: 500px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    input { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px; }
    button { width: 100%; padding: 12px; background: #e67e22; color: white; border: none; border-radius: 8px; }
    .msg { text-align: center; color: green; margin-bottom: 15px; }
  
a, button {
  display: inline-block;
  transition: transform 0.1s ease-in-out;
}
a:active, button:active {
  transform: scale(0.95);
}

</style>
</head>
<body>
  <form method="post">
    <h2>עדכון זמינות בתאריכים</h2>
    <?php if (isset($message)) echo "<div class='msg'>$message</div>"; ?>
    <input type="date" name="date" required>
    <input type="number" name="available_spots" placeholder="מספר מקומות זמינים" required>
    <button type="submit">שמור זמינות</button>
  </form>
</body>
</html>
