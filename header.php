<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../../registration/login.html");
    exit;
}

$first_name = htmlspecialchars($_SESSION['first_name'] ?? "אורח");
$user_type = $_SESSION['user_type'] ?? 0;

$links = $user_type == 1
    ? [
        '../admin/admin_dashboard_secured.php' => 'דשבורד מנהל',
        '../admin/users_list.php' => 'משתמשים',
        '../admin/all_orders.php' => 'הזמנות',
        '../admin/update_availability.php' => 'עדכון זמינות',
        '../admin/delete_order.php' => 'מחיקת הזמנה',
    ]
    : [
        '../../registration/user/user_dashboard_secured.php' => 'דשבורד',
        '../../registration/user/my_orders.php' => 'הזמנות',
        '../../reservation/user/reservation.php' => 'הזמנה חדשה',
        '../../registration/user/update_profile_secured.php' => 'עדכון פרטים',
        '../../grooming/admin/index.php' => 'הזמנת טיפוח',
    ];
?>

<style>
/*  */

  /* דוחף את כל האלמנטים שמתחת להדר */
  html body > *:not(.header-container) {
    margin-top: 100px;
  }


.header-container {
    position: fixed;
    width: 100%;
    top: 0;
    left: 0;
    background: <?= $user_type == 1 ? '#c0392b' : '#2c3e50' ?>;
    color: white;
    padding: 15px 20px;
    font-family: Arial, sans-serif;
    font-size: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
    
  }
/*  */


  .header-name {
    position: absolute;
    top: 10px;
    right: 20px;
    background: white;
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: bold;
    background: <?= $user_type == 1 ? '#c0392b' : '#2c3e50' ?>;
    color: black;
    padding: 5px 20px 0;
    font-family: Arial, sans-serif;
    font-size: 16px;
    text-align: right;
  }

  .header-bar {
  flex-direction: row;
    background: <?= $user_type == 1 ? '#c0392b' : '#2c3e50' ?>;
    color: white;
    padding: 10px 20px 15px;
    font-family: Arial, sans-serif;
    font-size: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    padding-right: 130px;
    padding-top: 20px;
  }

  .header-links {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    align-items: center;
  }

  .header-links a {
    color: white;
    text-decoration: none;
  }

  
@media (max-width: 600px) {
  .header-bar {
    flex-direction: column;
    align-items: flex-end;
  }
  .header-links {
    width: 100%;
    margin-top: 10px;
  }

    .header-bar {
  flex-direction: row;
      flex-direction: column;
      align-items: flex-end;
    }

    .header-links {
      flex-direction: column;
      width: 100%;
      margin-top: 10px;
    }
  }
</style>

<!--  -->
<div class="header-container">
<!--  -->
<div class="header-name" style="background:white; color:black; border-radius:5px; font-weight:bold; position:absolute; top:10px; right:150px;">
  שלום, <?= $first_name ?>
</div>

<div class="header-bar">
  <div class="header-links">
    <?php foreach ($links as $href => $label): ?>
      <a href="<?= $href ?>"><?= $label ?></a>
    <?php endforeach; ?>
  </div>

  <!--  -->
  </div>
  <!--  -->
  <div>
    <a href="../logout.php" style="color:white; text-decoration:none;"> התנתק🚪</a>
  </div>
</div>

