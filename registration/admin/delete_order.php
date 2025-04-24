<?php include '../../header.php'; ?>
<?php
// טופס מחיקת הזמנה לפי ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $servername = "localhost";
    $username = "itayrm_ItayRam";
    $password = "itay0547862155";
    $dbname = "itayrm_dogs_boarding_house";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $id = $_POST['order_id'];
    $stmt = $conn->prepare("DELETE FROM reservation WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "ההזמנה נמחקה בהצלחה.";
    } else {
        echo "שגיאה במחיקת ההזמנה.";
    }
    $conn->close();
}
?>

<h2>מחיקת הזמנה</h2>
<form method="post">
    <label>מספר הזמנה למחיקה:</label>
    <input type="number" name="order_id" required>
    <button type="submit">מחק</button>
</form>