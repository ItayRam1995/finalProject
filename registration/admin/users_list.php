<?php include '../../header.php'; ?>
<?php
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

echo "<h2>רשימת משתמשים</h2>";

$result = $conn->query("SELECT id, username, first_name, last_name, email, phone, user_type FROM users");

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>
            <tr><th>ID</th><th>שם משתמש</th><th>שם פרטי</th><th>שם משפחה</th><th>אימייל</th><th>טלפון</th><th>סוג</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $type = $row['user_type'] == 1 ? 'מנהל' : 'משתמש רגיל';
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['username']}</td>
                <td>{$row['first_name']}</td>
                <td>{$row['last_name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['phone']}</td>
                <td>$type</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "לא נמצאו משתמשים.";
}
$conn->close();
?>