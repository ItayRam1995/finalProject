<?php include '../../header.php'; ?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>ניהול הזמנות</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 40px;
      background-color: #f8f8f8;
    }
    h1 {
      text-align: center;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
    }
    th, td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: center;
    }
    th {
      background-color: #007bff;
      color: white;
    }
    button {
      padding: 6px 12px;
      background-color: #dc3545;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    button:hover {
      background-color: #c82333;
    }
  </style>
</head>
<body>
  <h1>מערכת ניהול הזמנות טיפוח</h1>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>תאריך</th>
        <th>שעה</th>
        <th>מספר אישור</th>
        <th>נוצר בתאריך</th>
        <th>פעולה</th>
      </tr>
    </thead>
    <tbody id="appointments-table">
      <tr><td colspan="6">טוען נתונים...</td></tr>
    </tbody>
  </table>

  <script>
    function fetchAppointments() {
      fetch('groomingPanelServer.php')
        .then(res => res.json())
        .then(data => {
          const tbody = document.getElementById('appointments-table');
          tbody.innerHTML = '';

          if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6">אין הזמנות פעילות</td></tr>';
            return;
          }

          data.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${row.id}</td>
              <td>${row.day}</td>
              <td>${row.time}</td>
              <td>${row.confirmation}</td>
              <td>${row.created_at}</td>
              <td><button onclick="cancelAppointment('${row.confirmation}', this)">בטל</button></td>
            `;
            tbody.appendChild(tr);
          });
        })
        .catch(err => {
          console.error(err);
          document.getElementById('appointments-table').innerHTML = '<tr><td colspan="6">שגיאה בטעינת נתונים</td></tr>';
        });
    }

    function cancelAppointment(confirmation, btn) {
      if (!confirm('האם אתה בטוח שברצונך לבטל את ההזמנה?')) return;

      fetch('cancelAppointmentServer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ confirmation })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          fetchAppointments(); // טען מחדש
        } else {
          alert('שגיאה: ' + data.error);
        }
      })
      .catch(() => alert('שגיאה בביטול ההזמנה.'));
    }

    fetchAppointments();
  </script>
</body>
</html>
