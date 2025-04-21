<?php include '../includes/header.php'; ?>
<?php
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=availability_export.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "תאריך	מקומות פנויים
";

$result = $conn->query("SELECT date, available_spots FROM Availability ORDER BY date ASC");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "{$row['date']}	{$row['available_spots']}
";
    }
} else {
    echo "לא נמצאו תאריכים
";
}

$conn->close();
?>