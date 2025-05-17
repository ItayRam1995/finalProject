<?php include '../../header.php'; ?>
<!DOCTYPE html>
<html lang="he">
<head>
  <meta charset="UTF-8">
  <title>דשבורד מנהל</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #eef3f7;
      padding: 30px;
      direction: rtl;
    }
    .dashboard {
      background: white;
      padding: 25px;
      border-radius: 10px;
      max-width: 700px;
      margin: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      color: #c0392b;
    }
    .actions a {
      display: block;
      margin: 10px 0;
      padding: 10px;
      background: #e74c3c;
      color: white;
      text-align: center;
      border-radius: 8px;
      text-decoration: none;
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
  <div class="dashboard">
    <h2>ברוך הבא מנהל</h2>
    <p>בחר פעולה:</p>
    <div class="actions">
      <a href="users_list.php">👥 רשימת משתמשים</a>
      <a href="all_orders.php">📦 כל ההזמנות</a>
      <a href="update_availability.php">📅 עדכון זמינות</a>
      <a href="delete_order.php">❌ מחיקת הזמנה</a>
      <a href="logout.php" style="background:#7f8c8d;">🚪 התנתק</a>
    </div>
  </div>
</body>
</html>
