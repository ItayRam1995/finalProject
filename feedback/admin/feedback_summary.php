<?php include '../../header.php'; ?>
<?php
// התחברות למסד הנתונים
$servername = "localhost";
$username = "itayrm_ItayRam";
$password = "itay0547862155";
$dbname = "itayrm_dogs_boarding_house";

$conn = new mysqli($servername, $username, $password, $dbname);

// בדיקת חיבור
if ($conn->connect_error) {
    die("חיבור למסד הנתונים נכשל: " . $conn->connect_error);
}

// יצירת מערך למיפוי ערכים טקסטואליים לציונים מספריים
$ratingMap = [
    "מצוין" => 4,
    "טוב" => 3,
    "ממוצע" => 2,
    "גרוע" => 1,
];

// משתנים לסינון
$categoryFilter = isset($_POST['category']) ? $_POST['category'] : '';
$valueFilter = isset($_POST['value']) ? $_POST['value'] : '';

// שאילתה דינמית
$sql = "SELECT overallExperience, treatmentExperience, stayExperience, staffExperience, submissionDate FROM feedback";
if (!empty($categoryFilter) && !empty($valueFilter)) {
    $sql .= " WHERE $categoryFilter = '$valueFilter'";
}

$result = $conn->query($sql);

$feedbackData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedbackData[] = $row;
    }
}

// חישוב ממוצעים
function calculateAverage($feedbackData, $key, $ratingMap) {
    $total = 0;
    $count = 0;

    foreach ($feedbackData as $row) {
        if (isset($ratingMap[$row[$key]])) {
            $total += $ratingMap[$row[$key]];
            $count++;
        }
    }

    return $count > 0 ? round($total / $count, 2) : "אין נתונים";
}

$averageScores = [
    'overallExperience' => ['average' => calculateAverage($feedbackData, 'overallExperience', $ratingMap)],
    'treatmentExperience' => ['average' => calculateAverage($feedbackData, 'treatmentExperience', $ratingMap)],
    'stayExperience' => ['average' => calculateAverage($feedbackData, 'stayExperience', $ratingMap)],
    'staffExperience' => ['average' => calculateAverage($feedbackData, 'staffExperience', $ratingMap)],
];

// פונקציה לצביעת רקע לפי ציון
function getScoreColor($score) {
    if (!is_numeric($score)) return "#ffffff"; // ברירת מחדל אם אין נתונים
    if ($score >= 3.5) return "#c8e6c9"; // ירוק
    if ($score >= 2.5) return "#fff9c4"; // צהוב
    return "#ffcdd2"; // אדום
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>סיכום משוב</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            background: linear-gradient(120deg, #e0f7fa, #0288d1);
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #01579b;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        select, button {
            padding: 10px;
            font-size: 1em;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #0288d1;
            color: white;
        }
        td {
            background-color: #f9f9f9;
        }
        .average-row td {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>סיכום משובים</h1>
        <form method="POST">
            <label for="category">בחר קטגוריה:</label>
            <select name="category" id="category">
                <option value="">-- בחר --</option>
                <option value="overallExperience" <?= $categoryFilter == 'overallExperience' ? 'selected' : ''; ?>>חוויה כללית</option>
                <option value="treatmentExperience" <?= $categoryFilter == 'treatmentExperience' ? 'selected' : ''; ?>>טיפולים</option>
                <option value="stayExperience" <?= $categoryFilter == 'stayExperience' ? 'selected' : ''; ?>>שהייה</option>
                <option value="staffExperience" <?= $categoryFilter == 'staffExperience' ? 'selected' : ''; ?>>צוות</option>
            </select>
            <label for="value">בחר ערך:</label>
            <select name="value" id="value">
                <option value="">-- בחר --</option>
                <option value="מצוין" <?= $valueFilter == 'מצוין' ? 'selected' : ''; ?>>מצוין</option>
                <option value="טוב" <?= $valueFilter == 'טוב' ? 'selected' : ''; ?>>טוב</option>
                <option value="ממוצע" <?= $valueFilter == 'ממוצע' ? 'selected' : ''; ?>>ממוצע</option>
                <option value="גרוע" <?= $valueFilter == 'גרוע' ? 'selected' : ''; ?>>גרוע</option>
            </select>
            <button type="submit">סנן</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>חוויה כללית</th>
                    <th>טיפולים</th>
                    <th>שהייה</th>
                    <th>צוות</th>
                    <th>תאריך הגשה</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($feedbackData) > 0): ?>
                    <?php foreach ($feedbackData as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['overallExperience']); ?></td>
                            <td><?= htmlspecialchars($row['treatmentExperience']); ?></td>
                            <td><?= htmlspecialchars($row['stayExperience']); ?></td>
                            <td><?= htmlspecialchars($row['staffExperience']); ?></td>
                            <td><?= htmlspecialchars($row['submissionDate']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="average-row">
                        <td style="background-color: <?= getScoreColor($averageScores['overallExperience']['average']); ?>;">
                            <?= is_numeric($averageScores['overallExperience']['average']) ? "{$averageScores['overallExperience']['average']} / 4" : $averageScores['overallExperience']['average']; ?>
                        </td>
                        <td style="background-color: <?= getScoreColor($averageScores['treatmentExperience']['average']); ?>;">
                            <?= is_numeric($averageScores['treatmentExperience']['average']) ? "{$averageScores['treatmentExperience']['average']} / 4" : $averageScores['treatmentExperience']['average']; ?>
                        </td>
                        <td style="background-color: <?= getScoreColor($averageScores['stayExperience']['average']); ?>;">
                            <?= is_numeric($averageScores['stayExperience']['average']) ? "{$averageScores['stayExperience']['average']} / 4" : $averageScores['stayExperience']['average']; ?>
                        </td>
                        <td style="background-color: <?= getScoreColor($averageScores['staffExperience']['average']); ?>;">
                            <?= is_numeric($averageScores['staffExperience']['average']) ? "{$averageScores['staffExperience']['average']} / 4" : $averageScores['staffExperience']['average']; ?>
                        </td>
                        <td>-</td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="5">לא נמצאו תוצאות לסינון זה.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
