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
            position: relative;
        }
        
        .form-group-full {
            width: 100%;
            position: relative;
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
        
        .field-error {
            color: var(--error-color);
            font-size: 14px;
            margin-top: 5px;
            display: none;
            font-weight: 600;
        }
        
        .input-error {
            border: 1px solid var(--error-color) !important;
            background-color: rgba(236, 87, 102, 0.05);
        }
        
        .required::after {
            content: " *";
            color: var(--error-color);
        }
        
        /* סגנון של שדה ריק שאינו תקין */
        .required-field-error {
            position: relative;
        }
        
        .required-field-error::after {
            content: "שדה חובה!";
            position: absolute;
            bottom: -20px;
            right: 0;
            color: var(--error-color);
            font-size: 12px;
            font-weight: bold;
            background-color: rgba(236, 87, 102, 0.1);
            padding: 2px 8px;
            border-radius: 4px;
            z-index: 10;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        /* סגנון כפתורי עזרה */
        /* כפתור מילוי אוטומטי */
        .btn-auto-fill {
            background-color: var(--warning-color);
            color: #333;
        }
        
        .btn-auto-fill:hover {
            background-color: #e9c13d;
        }
        /* כפתור איפוס */
        .btn-reset {
            background-color: var(--light-color);
            color: var(--dark-color);
        }
        
        .btn-reset:hover {
            background-color: #e2e6ea;
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
        
        <!-- כפתורי עזרה - מילוי אוטומטי ואיפוס -->
        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
            <button id="auto-fill-btn" class="btn btn-auto-fill">
                <i class="fas fa-magic"></i> מילוי אוטומטי
            </button>
            <button id="reset-form-btn" class="btn btn-reset">
                <i class="fas fa-eraser"></i> איפוס טופס
            </button>
        </div>
        
        <form id="dog-registration-form" enctype="multipart/form-data" novalidate>
            <div class="form-section">
                <h3><i class="fas fa-paw"></i> פרטים בסיסיים</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="dog-name" class="required">שם הכלב</label>
                        <input type="text" id="dog-name" name="dog_name" required>
                        <div class="field-error" id="dog-name-error">יש להזין את שם הכלב</div>
                    </div>
                    <div class="form-group">
                        <label for="gender" class="required">מין</label>
                        <select id="gender" name="gender" required>
                            <option value="" disabled selected>בחרו מין</option>
                            <option value="זכר">זכר</option>
                            <option value="נקבה">נקבה</option>
                        </select>
                        <div class="field-error" id="gender-error">יש לבחור את מין הכלב</div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="chip-number" class="required">מספר שבב</label>
                        <input type="text" id="chip-number" name="chip_number" placeholder="הזן מספר שבב" required>
                        <div class="field-error" id="chip-number-error">יש להזין את מספר השבב</div>
                    </div>
                    <div class="form-group">
                        <label for="breed" class="required">גזע</label>
                        <input type="text" id="breed" name="breed" required>
                        <div class="field-error" id="breed-error">יש להזין את גזע הכלב</div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="age" class="required">גיל (בשנים)</label>
                        <input type="number" id="age" name="age" step="0.1" min="0" max="30" required>
                        <div class="field-error" id="age-error">יש להזין את גיל הכלב</div>
                    </div>
                    <div class="form-group">
                        <label for="weight" class="required">משקל (קילוגרם)</label>
                        <input type="number" id="weight" name="weight" step="0.1" min="0" max="100" required>
                        <div class="field-error" id="weight-error">יש להזין את משקל הכלב</div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="color" class="required">צבע</label>
                        <input type="text" id="color" name="color" required>
                        <div class="field-error" id="color-error">יש להזין את צבע הכלב</div>
                    </div>
                    <div class="form-group">
                        <label for="vaccinations" class="required">חיסונים עדכניים</label>
                        <select id="vaccinations" name="vaccinations_updated" required>
                            <option value="">בחר אפשרות</option>
                            <option value="1">כן</option>
                            <option value="0">לא</option>
                        </select>
                        <div class="field-error" id="vaccinations-error">יש לבחור האם החיסונים עדכניים</div>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="fas fa-image"></i> תמונת הכלב</h3>
                <label for="file-input" class="image-upload required">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>לחצו כאן להעלאת תמונה של הכלב</p>
                    <p class="small">(פורמטים נתמכים: JPG, PNG, GIF)</p>
                </label>
                <input type="file" id="file-input" name="dog_image" accept="image/*" required>
                <div class="field-error" id="file-input-error">יש להעלות תמונה של הכלב</div>
                <div class="image-preview" id="image-preview">
                    <img id="preview-img" src="#" alt="תצוגה מקדימה של התמונה">
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="fas fa-heart"></i> אופי ובריאות</h3>
                <div class="form-group form-group-full">
                    <label for="personality" class="required">אופי הכלב</label>
                    <textarea id="personality" name="dog_personality" rows="3" placeholder="תיאור אופי הכלב שלכם" required></textarea>
                    <div class="field-error" id="personality-error">יש להזין את אופי הכלב</div>
                </div>
                
                <div class="form-group form-group-full">
                    <label for="health-notes" class="required">הערות בריאותיות</label>
                    <textarea id="health-notes" name="health_notes" rows="3" placeholder="רגישויות, מחלות, טיפולים מיוחדים וכדומה" required></textarea>
                    <div class="field-error" id="health-notes-error">יש להזין הערות בריאותיות</div>
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="fas fa-utensils"></i> תזונה</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="food-type" class="required">סוג מזון</label>
                        <input type="text" id="food-type" name="food_type" placeholder="מזון יבש, רטוב, ביתי, וכדומה" required>
                        <div class="field-error" id="food-type-error">יש להזין את סוג המזון</div>
                    </div>
                    <div class="form-group">
                        <label for="food-amount" class="required">כמות אוכל יומית</label>
                        <input type="text" id="food-amount" name="daily_food_amount" placeholder="למשל: 300 גרם, 2 כוסות וכדומה" required>
                        <div class="field-error" id="food-amount-error">יש להזין את כמות האוכל היומית</div>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="fas fa-stethoscope"></i> פרטי וטרינר</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="vet-name" class="required">שם הווטרינר המטפל</label>
                        <input type="text" id="vet-name" name="veterinarian_name" required>
                        <div class="field-error" id="vet-name-error">יש להזין את שם הווטרינר</div>
                    </div>
                    <div class="form-group">
                        <label for="vet-phone" class="required">מספר טלפון של הווטרינר</label>
                        <input type="tel" id="vet-phone" name="veterinarian_phone" dir="ltr" required>
                        <div class="field-error" id="vet-phone-error">יש להזין את מספר הטלפון של הווטרינר</div>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="fas fa-sticky-note"></i> הערות כלליות</h3>
                <div class="form-group form-group-full">
                    <label for="general-notes" class="required">הערות כלליות</label>
                    <textarea id="general-notes" name="general_notes" rows="3" placeholder="כל מידע נוסף שחשוב לכם לציין" required></textarea>
                    <div class="field-error" id="general-notes-error">יש להזין הערות כלליות</div>
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
            
            // פונקציית מילוי אוטומטי של הטופס
            $('#auto-fill-btn').click(function(e) {
                e.preventDefault();
                
                // פונקציה ליצירת מספר שבב אקראי
                function generateRandomChipNumber() {
                    // מספרי שבב מתחילים בדרך כלל ב-900 או 972 ואחריהם 12 ספרות
                    const prefix = Math.random() < 0.5 ? "900" : "972";
                    let randomDigits = "";
                    
                    // יצירת 12 ספרות אקראיות
                    for (let i = 0; i < 12; i++) {
                        randomDigits += Math.floor(Math.random() * 10);
                    }
                    
                    return prefix + randomDigits;
                }
                
                // נתונים לדוגמה למילוי אוטומטי
                const demoData = {
                    dog_name: "רקסי",
                    gender: "זכר",
                    chip_number: generateRandomChipNumber(), // מספר שבב אקראי
                    breed: "לברדור רטריבר",
                    age: 3.5,
                    weight: 28.4,
                    color: "חום בהיר",
                    vaccinations_updated: "1",
                    dog_personality: "ידידותי, אנרגטי ואוהב לשחק. טוב עם ילדים וחיות אחרות. נהנה במיוחד מטיולים בפארק ומשחקי אפורט.",
                    health_notes: "אלרגיה קלה למזון ים. יש לו נטייה לדלקות אוזניים, יש לבדוק פעם בחודש.",
                    food_type: "מזון יבש פרימיום",
                    daily_food_amount: "400 גרם (200 גרם בוקר ו-200 גרם ערב)",
                    veterinarian_name: "ד״ר שרון לוי",
                    veterinarian_phone: "052-1234567",
                    general_notes: "אוהב צעצועים רכים ומשמיעי קול. יש לטייל איתו לפחות פעמיים ביום למשך 30 דקות לפחות."
                };
                
                // מילוי השדות בטופס
                $('#dog-name').val(demoData.dog_name);
                $('#gender').val(demoData.gender);
                $('#chip-number').val(demoData.chip_number);
                $('#breed').val(demoData.breed);
                $('#age').val(demoData.age);
                $('#weight').val(demoData.weight);
                $('#color').val(demoData.color);
                $('#vaccinations').val(demoData.vaccinations_updated);
                $('#personality').val(demoData.dog_personality);
                $('#health-notes').val(demoData.health_notes);
                $('#food-type').val(demoData.food_type);
                $('#food-amount').val(demoData.daily_food_amount);
                $('#vet-name').val(demoData.veterinarian_name);
                $('#vet-phone').val(demoData.veterinarian_phone);
                $('#general-notes').val(demoData.general_notes);
                
                // הסרת סימוני שגיאה אם היו
                $('.field-error').hide();
                $('input, select, textarea').removeClass('input-error');
                $('.form-group, .form-group-full').removeClass('required-field-error');
                
                // הצגת הודעת הצלחה
                showStatusMessage('הטופס מולא בהצלחה עם נתונים לדוגמה!', 'success');
            });
            
            // פונקציית איפוס הטופס
            $('#reset-form-btn').click(function(e) {
                e.preventDefault();
                
                // איפוס כל שדות הטופס
                $('#dog-registration-form')[0].reset();
                
                // איפוס תצוגת התמונה
                $('#image-preview').hide();
                $('#preview-img').attr('src', '#');
                
                // הסרת סימוני שגיאה אם היו
                $('.field-error').hide();
                $('input, select, textarea').removeClass('input-error');
                $('.form-group, .form-group-full').removeClass('required-field-error');
                
                // הודעה למשתמש
                showStatusMessage('הטופס אופס בהצלחה', 'info');
            });
            
            // ולידציית הטופס לפני שליחה
            $('#dog-registration-form').submit(function(e) {
                e.preventDefault();
                
                // איפוס כל הודעות השגיאה
                $('.field-error').hide();
                $('input, select, textarea').removeClass('input-error');
                $('.form-group, .form-group-full').removeClass('required-field-error');
                $('#status-message').hide();
                
                // בדיקת תקינות הטופס
                let isValid = true;
                let firstErrorField = null;
                let emptyFields = [];
                
                // מיפוי שדות חובה עם שמות תצוגה 
                const requiredFields = [
                    { id: 'dog-name', name: 'שם הכלב', errorMsg: 'יש להזין את שם הכלב' },
                    { id: 'gender', name: 'מין', errorMsg: 'יש לבחור את מין הכלב' },
                    { id: 'chip-number', name: 'מספר שבב', errorMsg: 'יש להזין את מספר השבב' },
                    { id: 'breed', name: 'גזע', errorMsg: 'יש להזין את גזע הכלב' },
                    { id: 'age', name: 'גיל', errorMsg: 'יש להזין את גיל הכלב' },
                    { id: 'weight', name: 'משקל', errorMsg: 'יש להזין את משקל הכלב' },
                    { id: 'color', name: 'צבע', errorMsg: 'יש להזין את צבע הכלב' },
                    { id: 'vaccinations', name: 'חיסונים עדכניים', errorMsg: 'יש לבחור האם החיסונים עדכניים' },
                    { id: 'file-input', name: 'תמונת הכלב', errorMsg: 'יש להעלות תמונה של הכלב' },
                    { id: 'personality', name: 'אופי הכלב', errorMsg: 'יש להזין את אופי הכלב' },
                    { id: 'health-notes', name: 'הערות בריאותיות', errorMsg: 'יש להזין הערות בריאותיות' },
                    { id: 'food-type', name: 'סוג מזון', errorMsg: 'יש להזין את סוג המזון' },
                    { id: 'food-amount', name: 'כמות אוכל יומית', errorMsg: 'יש להזין את כמות האוכל היומית' },
                    { id: 'vet-name', name: 'שם הווטרינר', errorMsg: 'יש להזין את שם הווטרינר' },
                    { id: 'vet-phone', name: 'מספר טלפון של הווטרינר', errorMsg: 'יש להזין את מספר הטלפון של הווטרינר' },
                    { id: 'general-notes', name: 'הערות כלליות', errorMsg: 'יש להזין הערות כלליות' }
                ];
                
                // בדיקת כל שדות החובה
                for (const field of requiredFields) {
                    const $field = $('#' + field.id);
                    let value = $field.val();
                    let isEmpty = false;
                    
                    // טיפול מיוחד בשדה קובץ
                    if (field.id === 'file-input') {
                        isEmpty = !$field[0].files || !$field[0].files.length;
                    } else {
                        isEmpty = !value || (typeof value === 'string' && !value.trim());
                    }
                    
                    if (isEmpty) {
                        // סימון השדה כשגוי ותצוגת הודעת שגיאה
                        $field.addClass('input-error');
                        
                        // אם יש אלמנט שגיאה מתחת לשדה - להציג אותו
                        const $errorElement = $('#' + field.id + '-error');
                        if ($errorElement.length) {
                            $errorElement.show();
                        } else {
                            // אם אין אלמנט שגיאה, להוסיף אפקט ויזואלי
                            $field.parent().addClass('required-field-error');
                        }
                        
                        isValid = false;
                        firstErrorField = firstErrorField || $field;
                        emptyFields.push(field.name);
                    }
                }
                
                // אם יש שגיאות, מציג הודעה מדויקת בהתאם לשדות החסרים
                if (!isValid) {
                    let errorMessage = '';
                    
                    if (emptyFields.length === 1) {
                        // אם חסר רק שדה אחד - הצג את שם השדה בהדגשה
                        errorMessage = 'נא למלא את השדה: <strong>' + emptyFields[0] + '</strong>';
                    } else {
                        // אם חסרים מספר שדות - הצג רשימה של כל השדות החסרים
                        errorMessage = 'השדות הבאים חסרים לצורך סיום ההרשמה : <strong>' + emptyFields.join('</strong>, <strong>') + '</strong>';
                    }
                    
                    // הצגת הודעת שגיאה ספציפית באדום
                    $('#status-message').html(errorMessage).removeClass().addClass('status-message status-error').show();
                    
                    // גלילה לשדה הראשון עם שגיאה
                    if (firstErrorField) {
                        $('html, body').animate({
                            scrollTop: firstErrorField.offset().top - 200
                        }, 3000);
                    }
                    
                    return false;
                }
                
                // אם הטופס תקין, ממשיך לשליחה
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
                            // בדיקה אם התשובה מכילה שגיאת SQL
                            if (response.includes("SQL syntax") || response.includes("error in your SQL")) {
                                // הצגת הודעה ידידותית למשתמש במקום שגיאת SQL
                                showStatusMessage('אירעה שגיאה בשמירת הנתונים. אנא בדוק את השדות שהזנת ונסה שנית.', 'error');
                                return;
                            }
                            
                            const result = JSON.parse(response);
                            if (result.status === 'success') {
                                showStatusMessage('הכלב נרשם בהצלחה!', 'success');
                                $('#dog-registration-form')[0].reset();
                                $('#image-preview').hide();
                            } else {
                                // הודעת שגיאה ידידותית למשתמש
                                let errorMsg = 'אירעה שגיאה בשמירת הנתונים: ';
                                
                                // אם התקבל שדה ספציפי שגוי
                                if (result.field) {
                                    const fieldNames = {
                                        'dog_name': 'שם הכלב',
                                        'gender': 'מין',
                                        'chip_number': 'מספר שבב',
                                        'breed': 'גזע',
                                        'age': 'גיל',
                                        'weight': 'משקל',
                                        'color': 'צבע',
                                        'dog_image': 'תמונת הכלב',
                                        'dog_personality': 'אופי הכלב',
                                        'health_notes': 'הערות בריאותיות',
                                        'food_type': 'סוג מזון',
                                        'daily_food_amount': 'כמות אוכל יומית',
                                        'veterinarian_name': 'שם הווטרינר',
                                        'veterinarian_phone': 'מספר טלפון של הווטרינר',
                                        'general_notes': 'הערות כלליות'
                                    };
                                    
                                    const fieldName = fieldNames[result.field] || result.field;
                                    errorMsg += 'שדה "' + fieldName + '" אינו תקין. ';
                                    
                                    // הדגשת השדה השגוי בטופס
                                    $('#' + result.field.replace('_', '-')).addClass('input-error');
                                } 
                                
                                // הוספת הודעת השגיאה הספציפית אם קיימת
                                if (result.message) {
                                    errorMsg += result.message;
                                }
                                
                                showStatusMessage(errorMsg, 'error');
                            }
                        } catch(e) {
                            console.error("שגיאת JSON:", e, "תשובה מקורית:", response);
                            
                            // הודעות שגיאה ידידותיות למשתמש במקום שגיאות טכניות
                            if (response.includes("Warning") || response.includes("Fatal error") || response.includes("Notice")) {
                                showStatusMessage('אירעה שגיאה בשמירת הנתונים. אנא נסה שנית מאוחר יותר.', 'error');
                            } else {
                                showStatusMessage('אירעה שגיאה בשמירת הנתונים. אנא ודא שכל השדות תקינים ונסה שנית.', 'error');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("שגיאת AJAX:", status, error, xhr.responseText);
                        showStatusMessage('לא ניתן להתחבר לשרת. אנא בדוק את החיבור לאינטרנט ונסה שנית.', 'error');
                    }
                });
            });
            
            // ולידציה בזמן אמת
            $('input, select, textarea').on('input change', function() {
                // הסרת סימון שגיאה כאשר מתחילים להקליד
                $(this).removeClass('input-error');
                $(this).parent().removeClass('required-field-error');
                // הסתרת הודעת השגיאה הספציפית אם קיימת
                const fieldId = $(this).attr('id');
                $('#' + fieldId + '-error').hide();
            });
            
            function showStatusMessage(message, type) {
                const statusMessage = $('#status-message');
                statusMessage.html(message);
                statusMessage.removeClass('status-success status-error status-info');
                statusMessage.addClass('status-' + type);
                statusMessage.show();
                
                // גלילה אל ההודעה
                $('html, body').animate({
                    scrollTop: statusMessage.offset().top - 200
                }, 500);
                
                // הסתרת ההודעה אחרי 10 שניות רק אם זה לא הודעת שגיאה
                if (type !== 'error') {
                    setTimeout(function() {
                        statusMessage.fadeOut(1000);
                    }, 10000);
                }
            }
        });
    </script>
</body>
</html>