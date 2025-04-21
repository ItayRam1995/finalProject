<?php include '../includes/header.php'; ?>
<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// session_start();

// $message = '';
// $error = false;

// if (!isset($_SESSION['username'])) {
//     header("Location: ../login.html");
//     exit;
// }

$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$current_user = $_SESSION['username'];
$user_data = [];

$stmt = $conn->prepare("SELECT first_name, last_name, email, phone FROM users WHERE username = ?");
$stmt->bind_param("s", $current_user);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $missing_fields = [];
  foreach (["first_name", "last_name", "email", "phone"] as $field) {
    if (empty($_POST[$field])) {
      $missing_fields[] = $field;
    }
  }

  if (!empty($missing_fields)) {
    $message = "אנא מלא את השדה: " . implode(", ", $missing_fields);
    $error = true;
  } else {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, phone=? WHERE username=?");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $phone, $current_user);
    $stmt->execute();

    $message = "הפרטים עודכנו בהצלחה";
    $error = false;
    $_SESSION['first_name'] = $first_name;

    $user_data = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'phone' => $phone
    ];
  }
}
?>
<!DOCTYPE html>
<html lang="he">
<head>
  <meta charset="UTF-8">
  <title>עדכון פרטים אישיים</title>
  <style>
    body { font-family: Arial, sans-serif; background-color: #ffffff; padding: 30px; direction: rtl; }
    form {
      background: white; padding: 20px; border-radius: 10px; max-width: 500px; margin: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    input { width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ccc; }
    button {
  transition: transform 0.1s ease-in-out;
}
button:active {
  transform: scale(0.97);
}
      width: 100%; padding: 12px;
      display: flex; align-items: center; justify-content: center;
      gap: 8px; font-size: 16px;
      border-radius: 8px; border: none;
      cursor: pointer;
    }
    .message {
      text-align: center;
      color: <?= $error ? 'red' : 'green' ?>;
      font-weight: bold;
      margin-bottom: 20px;
    }
    .menu {
      background:#2c3e50;padding:15px;text-align:right;
    }
    .menu a {
      color:white;margin-left:20px;text-decoration:none;
    }
  
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

<!--<div class="menu">-->
<!--  <a href='user_dashboard_secured.php'>דשבורד</a>-->
<!--  <a href='my_orders.php'>הזמנות</a>-->
<!--  <a href='reservation.html'>הזמנה חדשה</a>-->
<!--  <a href='update_profile_secured.php'>עדכון פרטים</a>-->
<!--  <a href='../logout.php' style='float:left;'>🚪 התנתק</a>-->
<!--</div>-->

<form action="update_profile_secured.php" method="post">
  <h2>עדכון פרטים</h2>
  <?php if (!empty($message)) echo "<div class='message' id='msgBox'>$message</div>"; ?>
  <input name="first_name" placeholder="שם פרטי חדש" value="<?php echo htmlspecialchars($user_data['first_name'] ?? '') ?>" required />
  <input name="last_name" placeholder="שם משפחה חדש" value="<?php echo htmlspecialchars($user_data['last_name'] ?? '') ?>" required />
  <input name="email" type="email" placeholder="אימייל חדש" value="<?php echo htmlspecialchars($user_data['email'] ?? '') ?>" required />
  <input name="phone" placeholder="טלפון חדש" value="<?php echo htmlspecialchars($user_data['phone'] ?? '') ?>" required />
  <button type="submit">✅ שמור שינויים</button>
  <button type="button" onclick="resetFields()" style="background:#c0392b; margin-top:10px;">🔄 אפס שדות</button>
</form>

<script>
function resetFields() {
  const fields = ['first_name', 'last_name', 'email', 'phone'];
  fields.forEach(id => {
    document.getElementsByName(id)[0].value = '';
  });
  const msg = document.getElementById('msgBox');
  if (msg) msg.style.display = 'none';
}
</script>

</body>
</html>
