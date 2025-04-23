<?php include '../../header.php'; ?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>דשבורד משתמש</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #3498db;
      --secondary-color: #2980b9;
      --accent-color: #f1c40f;
      --text-color: #2c3e50;
      --light-gray: #ecf0f1;
      --border-radius: 12px;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f5f7fa;
      color: var(--text-color);
      line-height: 1.6;
      padding: 20px;
      direction: rtl;
    }
    
    .container {
      max-width: 800px;
      margin: 40px auto;
    }
    
    .dashboard {
      background: white;
      padding: 30px;
      border-radius: var(--border-radius);
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .dashboard-header {
      margin-bottom: 30px;
      border-bottom: 2px solid var(--light-gray);
      padding-bottom: 15px;
    }
    
    .dashboard-header h2 {
      font-size: 28px;
      color: var(--text-color);
      margin-bottom: 10px;
    }
    
    .dashboard-header p {
      color: #7f8c8d;
      font-size: 16px;
    }
    
    .actions-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      margin-top: 25px;
    }
    
    .action-button {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      padding: 20px;
      height: 100px;
      background: white;
      color: var(--text-color);
      border-radius: var(--border-radius);
      text-decoration: none;
      font-weight: 600;
      font-size: 16px;
      border: 1px solid var(--light-gray);
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    
    .action-button i {
      font-size: 24px;
      margin-left: 15px;
      color: var(--primary-color);
      transition: all 0.3s ease;
      width: 30px;
      text-align: center;
    }
    
    .action-button:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      border-color: var(--primary-color);
    }
    
    .action-button:hover i {
      transform: scale(1.2);
    }
    
    .action-button:active {
      transform: translateY(0) scale(0.98);
    }
    
    .logout-button {
      background-color: #f8f9fa;
      color: #7f8c8d;
    }
    
    .logout-button i {
      color: #e74c3c;
    }
    
    @media (max-width: 600px) {
      .actions-grid {
        grid-template-columns: 1fr;
      }
      
      .action-button {
        height: 80px;
      }
      
      .dashboard {
        padding: 20px;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="dashboard">
      <div class="dashboard-header">
        <h2>ברוך הבא לדשבורד המשתמש</h2>
        <p>ניהול חשבון והזמנות במקום אחד</p>
      </div>
      
      <div class="actions-grid">
        <a href="my_orders.php" class="action-button">
          <i class="fas fa-box"></i>
          <span>צפייה בהזמנות שלי</span>
        </a>
        <a href="../../reservation/user/reservation.php" class="action-button">
          <i class="fas fa-plus-circle"></i>
          <span>הזמנה חדשה</span>
        </a>
        <a href="update_profile_secured.php" class="action-button">
          <i class="fas fa-user-edit"></i>
          <span>עדכון פרטים אישיים</span>
        </a>
        <a href="../logout.php" class="action-button logout-button">
          <i class="fas fa-sign-out-alt"></i>
          <span>התנתק</span>
        </a>
      </div>
    </div>
  </div>
</body>
</html>