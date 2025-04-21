<?php include $_SERVER['DOCUMENT_ROOT'] . 'in pross 23/includes/header.php'; ?>
<!DOCTYPE html>
<html lang="he">
<head>
  <meta charset="UTF-8">
  <title>דשבורד משתמש</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      padding: 30px;
      direction: rtl;
    }
    .dashboard {
      background: white;
      padding: 25px;
      border-radius: 10px;
      max-width: 600px;
      margin: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      color: #2c3e50;
    }
    .actions {
      margin-top: 20px;
    }
    .actions a {
      display: block;
      margin: 10px 0;
      padding: 10px;
      background: #3498db;
      color: white;
      text-align: center;
      border-radius: 8px;
      text-decoration: none;
    }
  
a {
  display: inline-block;
  transition: transform 0.1s ease-in-out;
}
a:active {
  transform: scale(0.95);
}

</style>
</head>

<body>
<?php

if (isset($_SESSION['first_name'])) {
  echo "<div style='position:absolute;top:10px;right:15px;color:white;font-weight:bold;'>שלום, " . htmlspecialchars($_SESSION['first_name']) . "</div>";
}
?>

<!-- <div style='background:#2c3e50;padding:15px;'>
  <a href='user_dashboard_secured.php' style='color:white;margin-left:20px;text-decoration:none;'>דשבורד</a>
  <a href='my_orders.php' style='color:white;margin-left:20px;text-decoration:none;'>הזמנות</a>
  <a href='reservation.html' style='color:white;margin-left:20px;text-decoration:none;'>הזמנה חדשה</a>
  <a href='update_profile_secured.php' style='color:white;margin-left:20px;text-decoration:none;'>עדכון פרטים</a>
  <a href='../logout.php' style='color:white;float:left;text-decoration:none;'>🚪 התנתק</a>
</div> -->

  <div class="dashboard">
    <h2>ברוך הבא לדשבורד המשתמש</h2>
    <p>בחר פעולה:</p>
    <div class="actions">
      <a href="my_orders.php">📦 צפייה בהזמנות שלי</a>
      <a href="reservation.html">📝 הזמנה חדשה</a>
      <a href="update_profile_secured.php">🔧 עדכון פרטים אישיים</a>
      <a href="../logout.php" style="background:#7f8c8d;">🚪 התנתק</a>
    </div>
  </div>
</body>
</html>
