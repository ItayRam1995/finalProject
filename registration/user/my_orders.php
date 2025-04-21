<?php include 'header.php'; ?>
<?php
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// לדוגמה - כאן נניח ששם המשתמש קבוע, אבל בפועל יש להשתמש ב-session
$current_user = 'demo_user';

echo "<h2>הזמנות שלי</h2>";

$sql = "SELECT * FROM reservation WHERE created_at IN (SELECT id FROM users WHERE username = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $current_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>הזמנה מ-{$row['start_date']} עד {$row['end_date']} - בתאריך יצירה {$row['created_at']}</li>";
    }
    echo "</ul>";
} else {
    echo "אין הזמנות להצגה.";
}
$conn->close();
?>