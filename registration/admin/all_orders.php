<?php include '../../header.php'; ?>
<?php
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

echo "<h2>כל ההזמנות</h2>";

$result = $conn->query("SELECT * FROM reservation");

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