<?php
session_start();
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (!isset($_POST['username'], $_POST['password'], $_POST['user_type'])) {
    die("שגיאה: נתונים חסרים מהטופס.");
}


$username = $_POST['username'];
$password = $_POST['password'];
$user_type = $_POST['user_type'];

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ? AND user_type = ?");
$stmt->bind_param("ssi", $username, $password, $user_type);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_type'] = $user['user_type'];

    if ($user['user_type'] == 1) {
        header("Location: admin/admin_dashboard_secured.php");
    } else {
        header("Location: user/user_dashboard_secured.php");
    }
    exit;
} else {
    echo "שם משתמש או סיסמה שגויים";
}
$conn->close();
?>