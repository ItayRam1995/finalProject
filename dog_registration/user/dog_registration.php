<?php include '../../header.php'; ?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>הרשמת כלב חדש</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        :root {
            --primary-color: #4A6FA5;
            --secondary-color: #166088;
            --accent-color: #4FC1E3;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --success-color: #47B881;
            --error-color: #EC5766;
            --warning-color: #F7D154;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', 'Arial', sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-color);
        }
        
        header h1 {
            color: var(--secondary-color);
            margin-bottom: 10px;
        }
        
        header p {
            color: #777;
        }
        
        .form-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        
        .form-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            width: 100%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .form-section h3 {
            color: var(--secondary-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .form-section h3 i {
            margin-left: 10px;
            color: var(--accent-color);
        }
        
        .form-group {
            margin-bottom: 15px;
            width: 48%;
        }
        
        .form-group-full {
            width: 100%;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            width: 100%;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        
        input[type="text"],
        input[type="number"],
        input[type="tel"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="tel"]:focus,
        select:focus,
        textarea:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 193, 227, 0.2);
        }
        
        .image-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 2px dashed #ddd;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .image-upload:hover {
            border-color: var(--accent-color);
        }
        
        .image-upload i {
            font-size: 48px;
            color: #aaa;
            margin-bottom: 10px;
        }
        
        .image-upload p {
            color: #777;
            text-align: center;
        }
        
        .image-preview {
            width: 150px;
            height: 150px;
            margin: 15px auto;
            border-radius: 8px;
            overflow: hidden;
            display: none;
            border: 3px solid var(--light-color);
        }
        
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        
        .btn-submit {
            background-color: var(--success-color);
            margin-top: 20px;
            width: 100%;
            padding: 15px;
            font-size: 18px;
        }
        
        .btn-submit:hover {
            background-color: #3ca574;
        }
        
        #file-input {
            display: none;
        }
        
        .status-message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
            display: none;
        }
        
        .status-success {
            background-color: rgba(71, 184, 129, 0.2);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }
        
        .status-error {
            background-color: rgba(236, 87, 102, 0.2);
            color: var(--error-color);
            border: 1px solid var(--error-color);
        }
        
        .status-info {
            background-color: rgba(79, 193, 227, 0.2);
            color: var(--accent-color);
            border: 1px solid var(--accent-color);
        }
        
        .required::after {
            content: " *";
            color: var(--error-color);
        }
        
        @media (max-width: 768px) {
            .form-group {
                width: 100%;
            }
            
            .container {
                padding: 15px;
                margin: 15px;
                width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>הרשמת כלב חדש למערכת</h1>
            <p>אנא מלאו את הפרטים הבאים כדי להוסיף את הכלב שלכם למערכת.</p>
        </header>
        
        <div id="status-message" class="status-message"></div>
        
        <form id="dog-registration-form" enctype="multipart/form-data">
            <div class="form-section">
                <h3><i class="fas fa-paw"></i> פרטים בסיסיים</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="dog-name" class="required">שם הכלב</label>
                        <input type="text" id="dog-name" name="dog_name" required>
                    </div>
                    <div class="form-group">
                        <label for="gender" class="required">מין</label>
                        <select id="gender" name="gender" required>
                            <option value="" disabled selected>בחרו מין</option>
                            <option value="זכר">זכר</option>
                            <option value="נקבה">נקבה</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="chip-number">מספר שבב</label>
                        <input type="text" id="chip-number" name="chip_number" placeholder="אם קיים">
                    </div>
                    <div class="form-group">
                        <label for="breed">גזע</label>
                        <input type="text" id="breed" name="breed">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="age">גיל (בשנים)</label>
                        <input type="number" id="age" name="age" step="0.1" min="0" max="30">
                    </div>
                    <div class="form-group">
                        <label for="weight">משקל (קילוגרם)</label>
                        <input type="number" id="weight" name="weight" step="0.1" min="0" max="100">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="color">צבע</label>
                        <input type="text" id="color" name="color">
                    </div>
                    <div class="form-group">
                        <label for="vaccinations">חיסונים עדכניים</label>
                        <select id="vaccinations" name="vaccinations_updated">
                            <option value="1">כן</option>
                            <option value="0" selected>לא</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="fas fa-image"></i> תמונת הכלב</h3>
                <label for="file-input" class="image-upload">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>לחצו כאן להעלאת תמונה של הכלב</p>
                    <p class="small">(פורמטים נתמכים: JPG, PNG, GIF)</p>
                </label>
                <input type="file" id="file-input" name="dog_image" accept="image/*">
                <div class="image-preview" id="image-preview">
                    <img id="preview-img" src="#" alt="תצוגה מקדימה של התמונה">
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="fas fa-heart"></i> אופי ובריאות</h3>
                <div class="form-group form-group-full">
                    <label for="personality">אופי הכלב</label>
                    <textarea id="personality" name="dog_personality" rows="3" placeholder="תיאור אופי הכלב שלכם"></textarea>
                </div>
                
                <div class="form-group form-group-full">
                    <label for="health-notes">הערות בריאותיות</label>
                    <textarea id="health-notes" name="health_notes" rows="3" placeholder="רגישויות, מחלות, טיפולים מיוחדים וכדומה"></textarea>
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="fas fa-utensils"></i> תזונה</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="food-type">סוג מזון</label>
                        <input type="text" id="food-type" name="food_type" placeholder="מזון יבש, רטוב, ביתי, וכדומה">
                    </div>
                    <div class="form-group">
                        <label for="food-amount">כמות אוכל יומית</label>
                        <input type="text" id="food-amount" name="daily_food_amount" placeholder="למשל: 300 גרם, 2 כוסות וכדומה">
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="fas fa-stethoscope"></i> פרטי וטרינר</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="vet-name">שם הווטרינר המטפל</label>
                        <input type="text" id="vet-name" name="veterinarian_name">
                    </div>
                    <div class="form-group">
                        <label for="vet-phone">מספר טלפון של הווטרינר</label>
                        <input type="tel" id="vet-phone" name="veterinarian_phone" dir="ltr">
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="fas fa-sticky-note"></i> הערות כלליות</h3>
                <div class="form-group form-group-full">
                    <label for="general-notes">הערות כלליות</label>
                    <textarea id="general-notes" name="general_notes" rows="3" placeholder="כל מידע נוסף שחשוב לכם לציין"></textarea>
                </div>
            </div>
            
            <button type="submit" class="btn btn-submit"><i class="fas fa-plus-circle"></i> הוסף את הכלב למערכת</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // תצוגה מקדימה של התמונה
            $('#file-input').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#preview-img').attr('src', e.target.result);
                        $('#image-preview').show();
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            // שליחת הטופס עם AJAX
            $('#dog-registration-form').submit(function(e) {
                e.preventDefault();
                
                // הצגת הודעה שהטופס נשלח
                showStatusMessage('שולח נתונים...', 'info');
                
                const formData = new FormData(this);
                
                // הוספת לוג של תוכן הטופס לקונסול לדיבוג
                console.log("שולח טופס:");
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + (pair[1] instanceof File ? pair[1].name : pair[1]));
                }
                
                $.ajax({
                    url: 'dog_registrationServer.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log("תשובה התקבלה:", response); // לוג לדיבוג
                        
                        try {
                            const result = JSON.parse(response);
                            if (result.status === 'success') {
                                showStatusMessage('הכלב נרשם בהצלחה!', 'success');
                                $('#dog-registration-form')[0].reset();
                                $('#image-preview').hide();
                            } else {
                                showStatusMessage('שגיאה: ' + result.message, 'error');
                            }
                        } catch(e) {
                            console.error("שגיאת JSON:", e, "תשובה מקורית:", response);
                            
                            if (response.includes("Warning") || response.includes("Fatal error") || response.includes("Notice")) {
                                showStatusMessage('שגיאת PHP בשרת. בדוק את קובץ הלוג.', 'error');
                            } else {
                                showStatusMessage('אירעה שגיאה בעיבוד התשובה מהשרת: ' + e.message, 'error');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("שגיאת AJAX:", status, error, xhr.responseText);
                        showStatusMessage('אירעה שגיאה בשליחת הטופס: ' + error, 'error');
                    }
                });
            });
            
            function showStatusMessage(message, type) {
                const statusMessage = $('#status-message');
                statusMessage.text(message);
                statusMessage.removeClass('status-success status-error');
                statusMessage.addClass('status-' + type);
                statusMessage.show();
                
                // גלילה אל ההודעה
                $('html, body').animate({
                    scrollTop: statusMessage.offset().top - 200
                }, 500);
                
                // הסתרת ההודעה אחרי 10 שניות
                setTimeout(function() {
                    statusMessage.fadeOut(1000);
                }, 5000);
            }
        });
    </script>
</body>
</html>