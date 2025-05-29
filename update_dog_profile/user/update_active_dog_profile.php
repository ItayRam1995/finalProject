<?php include '../../header.php'; ?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>עדכון פרטי כלב</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        /* מגדיר משתנים של צבעים לשימוש חוזר */
        :root {
            --primary-color: #4A6FA5;
            --secondary-color: #166088;
            --accent-color: #4FC1E3;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --success-color: #47B881;
            --error-color: #EC5766;
            --warning-color: #F7D154;
            --update-color: #8E44AD;
        }
        
        /* מאפס מרווחים פנימיים/חיצוניים */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', 'Arial', sans-serif;
        }
        
        /* רקע בהיר, צבע טקסט כהה, מרווח שורות */
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        /* תיבת התוכן המרכזית של הדף */
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* ממקם את הכותרת במרכז, קו תחתון עדין */
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
        
        /* תיבת מידע עם הסבר קצר על טופס עדכון הכלב */
         /* רקע סגול */
        .update-banner {
            background-color: rgba(142, 68, 173, 0.15);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 5px solid var(--update-color);
        }
        
        /* הכותרת שנמצאת בתוך תיבת המידע */
        /* כותרת סגולה */
        .update-banner h2 {
            color: var(--update-color);
            font-size: 20px;
            margin-bottom: 10px;
        }
        
        /* פסקה שנמצאת בתוך תיבת המידע */
        .update-banner p {
            color: #555;
            margin-bottom: 5px;
        }
        
        /* אזור מידע על הכלב הנוכחי */
        .current-dog-info {
            /* צבע רקע כחול מדורג */
            background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
            /* טקסט לבן */
            color: white;
            /* ריווח פנימי */
            padding: 20px;
            /* מעגל את הפינות של הקופסה */
            border-radius: 12px;
            /* מוסיף ריווח חיצוני מתחת לקופסה */
            margin-bottom: 30px;
            /* מוסיף צל  מתחת */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* כותרת מידע הכלב הנוכחי */
        .current-dog-info h3 {
            /* גודל הטקסט של הכותרת */
            font-size: 24px;
            margin-bottom: 15px;
            /* מאפשר סידור של תוכן פנימי בשורה אחת */
            display: flex;
            /* מוודא שהתוכן הפנימי של הכותרת  (האייקון והשם של הכלב) מיושר אנכית למרכז השורה */
            align-items: center;
        }
        
        /* האייקון של הכלב שמופיע לצד שם הכלב */
        .current-dog-info h3 i {
            margin-left: 10px;
            font-size: 28px;
        }
        
        /* (הקונטיינר של כל הנתונים המצומצמים על הכלב (גזע, גיל, משקל, מין */
        .dog-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        /* העיצוב של כל תיבת מידע בודדת בתוך סיכום הכלב */
        .dog-summary-item {
            /* רקע שקוף לבן עם שקיפות של 10% */
            background: rgba(255, 255, 255, 0.1);
            padding: 10px;
            border-radius: 8px;
            /* מוסיף טשטוש של הרקע שמאחורי התיבה */
            backdrop-filter: blur(10px);
        }
        
        /* הכיתוב הקטן שמסביר איזה נתון מוצג */
        .dog-summary-item strong {
            /* הופך את האלמנט לבלוק — כך שהוא יופיע בשורה נפרדת מעל הטקסט */
            display: block;
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        
        /* הערך עצמו של הנתון */
        .dog-summary-item span {
            font-size: 16px;
            font-weight: 600;
        }
        
       
        /* ארגון אזורי השדות של הטופס בשורות של שניים בשורה אחת על מסכים רחבים, ותעבור לשורה אחת מתחת לשנייה במסכים צרים */
        .form-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        
         /* אזורי תוכן נפרדים בטופס */
        /* רקע בהיר עם צל קל */
        .form-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            width: 100%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        /* מעצב את הכותרות שמופיעות בתוך כל סקשן של טופס */
        .form-section h3 {
            /* כחול כהה */
            color: var(--secondary-color);
            margin-bottom: 15px;
            /* ליישר את האייקון והטקסט בשורה אחת בצורה גמישה */
            display: flex;
            /* מיישר את תכולת השורה */
            align-items: center;
        }
        
        /* מעצב את האייקונים שנמצאים בתוך כותרות של סקשני הטופס */
        .form-section h3 i {
            margin-left: 10px;
            color: var(--accent-color);
        }
        
        /* שדה טופס בגודל חצי-מסך */
        /* מיכל לשדה טופס בודד */
        /* מאפשר הנחת שני שדות בשורה אחת של הסקשן */
        .form-group {
            margin-bottom: 15px;
            /* תופס חצי שורה ברוחב */
            width: 48%;
            position: relative;
        }
        
        /* שדה ברוחב מלא */
        /* שדות טופס שאמורים לתפוס את כל רוחב השורה, כמו תיבות טקסט גדולות */
        .form-group-full {
            width: 100%;
            position: relative;
        }
        
        /* מיכל לשדות טופס (.form-group / .form-group-full) */
        /* לארגן שדות זה לצד זה או בשורות שונות לפי רוחב המסך */
        .form-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            width: 100%;
        }
        
        
        label {
            /* מבטיח שהכיתוב לא יתנגש עם שדות הטופס */
            /* יתפוס שורה מלאה מעל שדה הקלט */
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        
        /* שדות טקסט בטופס - נותן עיצוב אחיד */
        input[type="text"],
        input[type="number"],
        input[type="tel"],
        select,
        textarea {
            /* כל שדה יתפוס את כל רוחב המיכל שלו (.form-group או .form-group-full) */
            width: 100%;
            /* ריווח פנימי בתוך השדה */
            padding: 12px;
            /* מסגרת בצבע אפור בהיר */
            border: 1px solid #ddd;
            /* מעגל את הפינות של השדה */
            border-radius: 6px;
            font-size: 16px;
            /* מוסיף אפקט מעבר חלק כשמשנים את גבול השדה (כשהוא מקבל פוקוס) */
            transition: border 0.3s;
        }
        
        /* התנהגות שדות הקלט כאשר הם מקבלים פוקוס - כשמשתמש מקליק או מקליד בתוכם */
        /* אפקט בפוקוס: מדגיש שדה פעיל */
        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="tel"]:focus,
        select:focus,
        textarea:focus {
            /* משנה את צבע המסגרת של השדה לצבע תכלת מודגש */
            border-color: var(--accent-color);
            /* מסיר את מסגרת ברירת־המחדל של הדפדפן */
            outline: none;
            /* מוסיף צל בצבצ תכלת סביב השדה */
            box-shadow: 0 0 0 3px rgba(79, 193, 227, 0.2);
        }
        
        /* העלאת תמונת כלב - קופסה עם גבול מקווקו, תצוגה מקדימה מוסתרת, נחשפת כשיש תמונה */
        /* האלמנט שבו המשתמש לוחץ כדי לבחור תמונה חדשה לכלב */
        /* האלמנט בפועל הוא תווית שעוטפת את אזור העלאה, ואליו משויכת פעולת בחירת הקובץ */
        .image-upload {
            /* מסדר את התוכן (אייקון + טקסט) */
            display: flex;
            /* התוכן מופיע בטור (למעלה למטה) */
            flex-direction: column;
            /* יישור למרכז אופקי של התוכן */
            align-items: center;
            padding: 20px;
            /* רקע אפור */
            background-color: #f8f9fa;
            border-radius: 8px;
            /* גבול מקווקו שמסמן אזור העלאה */
            /* גבול מקווקו בצבע אפור בהיר */
            border: 2px dashed #ddd;
            margin-bottom: 20px;
            /* הסמן משתנה ליד */
            cursor: pointer;
            transition: all 0.3s;
        }
        
        /* כאשר המשתמש עובר עם העכבר – משנה את צבע הגבול לצבע תכלת כהדגשה */
        .image-upload:hover {
            border-color: var(--accent-color);
        }
        
        /* אייקון גדול, אפור, עם ריווח מתחתיו */
        .image-upload i {
            font-size: 48px;
            color: #aaa;
            margin-bottom: 10px;
        }
        
        /* טקסט הסבר באזור ההעלאה – באפור */
        .image-upload p {
            color: #777;
            text-align: center;
        }

        /* אזור להצגת התמונה שהועלתה – תצוגה מקדימה */
        /* האלמנט שמציג תצוגה מקדימה של התמונה החדשה שהמשתמש בחר להעלות */
        .image-preview {
            /* קובע רוחב קבוע */
            width: 150px;
            /* קובע גובה קבוע */
            height: 150px;
            /* מיישר את התמונה לאמצע האופקי של הקונטיינר */
            margin: 15px auto;
            border-radius: 8px;
            /* כל תוכן של התמונה שגולש מעבר לגבולות – ייחתך */
            overflow: hidden;
            /* התצוגה מוסתרת כברירת מחדל — היא תיחשף רק כאשר המשתמש יבחר תמונה */
            display: none;
            /* מוסיף מסגרת */
            border: 3px solid var(--light-color);
        }
        
        /* ממלא את כל הריבוע */
        .image-preview img {
            /* גורם לתמונה למלא את כל רוחב האלמנט */
            width: 100%;
            height: 100%;
            /* גורמת לתמונה להתאים לגודל של הריבוע בלי לעוות אותה */
            object-fit: cover;
        }
        
        /* תמונה נוכחית של הכלב */
        .current-image {
            /* מציג את האלמנט כבלוק */
            display: block;
            /* קובע גובה ורוחב קבועים לתמונה */
            width: 150px;
            height: 150px;
            /* ממרכז את התמונה אופקית */
            margin: 15px auto;
            border-radius: 8px;
            /* כל תוכן של התמונה שגולש מעבר לגבולות – ייחתך */
            overflow: hidden;
            /* מסגרת תכלת בהיר */
            border: 3px solid var(--accent-color);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .current-image img {
            /* גורם לתמונה להתפרס לכל רוחב המיכל */
            width: 100%;
            /* גורם לתמונה להתפרס לכל גובה המיכל */
            height: 100%;
            /* ממלאת את כל הריבוע גם אם צריך לחתוך חלקים מהקצוות */
            object-fit: cover;
        }
        
        /* הטקסט שמופיע מעל התמונה הנוכחית */
        .current-image-label {
            /* ממרכז את הטקסט אופקית בתוך האלמנט */
            text-align: center;
            font-size: 14px;
            /* צבע אפור כהה */
            color: #666;
            margin-bottom: 10px;
        }
        
        /* כפתור כללי בדף */
        .btn {
            /* מאפשר להציג אותו בשורה אחת עם טקסט אם צריך, אך גם לתפקד כמו בלוק */
            display: inline-block;
            /* כחול כהה */
            background-color: var(--primary-color);
            /* טקסט לבן */
            color: white;
            padding: 12px 24px;
            /* מסיר גבול חיצוני ברירת-מחדל של הדפדפן */
            border: none;
            border-radius: 6px;
            /* משנה את סמן העכבר ליד בעת מעבר מעל הכפתור */
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            /* מיישר את תוכן הכפתור */
            text-align: center;
            transition: all 0.3s;
        }
        
        /* כאשר העכבר מרחף מעל כפתור כללי */
        .btn:hover {
            /* כחול כהה יותר */
            background-color: var(--secondary-color);
            /* מזיז את הכפתור קצת כלפי מעלה */
            transform: translateY(-2px);
            /* מוסיף צל */
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        
        /* כפתור העדכון */
        /* יורשת את רוב התכונות מ־.btn */
        .btn-update {
            /* צבע רקע סגול */
            background-color: var(--update-color);
            margin-top: 20px;
            /* הכפתור יתפוס את מלוא רוחב הקונטיינר שלו */
            width: 100%;
            padding: 15px;
            font-size: 18px;
        }
        
        .btn-update:hover {
            /* סגול כהה יותר */
            background-color: #7D3C98;
        }
        
        /* מסתיר את אלמנט הקלט לקובץ */
        /* המשתמש לא רואה את תיבת הבחירה */
        /* כשמשתמש לוחץ על ה־ label, זה פותח את חלון בחירת הקובץ */
        /* בזכות ה־ for="file-input" */
        #file-input {
            display: none;
        }
        
        /* הודעות מערכת מעוצבות */
        /* משמשת להצגת הודעות מערכת למשתמש — כמו הצלחה, שגיאה, או מידע לאחר שליחת הטופס */
        .status-message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            /* ממרכז את הטקסט בתוך ההודעה */
            text-align: center;
            font-weight: 600;
            /* ברירת המחדל -  ההודעה מוסתרת */
            display: none;
        }
        
        /* הודעת הצלחה */
        .status-success {
            background-color: rgba(71, 184, 129, 0.2);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }
        
        /* הודעת שגיאה */
        .status-error {
            /* צבע רקע ירוק */
            background-color: rgba(236, 87, 102, 0.2);
            /* צבע הטקסט ירוק */
            color: var(--error-color);
            /* מוסיף גבול דק באותו גוון ירוק */
            border: 1px solid var(--error-color);
        }
        
        /* הודעת מידע כללי */
        /* "לא בוצעו שינויים", "לא הועלתה תמונה חדשה" או "אין נתונים להצגה" */
        .status-info {
            /* צבע רקע תכלת־בהיר */
            background-color: rgba(79, 193, 227, 0.2);
            /* צבע טקסט תכלת */
            color: var(--accent-color);
            /* גבול דק בצבע תכלת */
            border: 1px solid var(--accent-color);
        }
        
        /* הצגת שגיאה מקומית ליד שדה ספציפי בטופס */
        .field-error {
            /* צבע טקסט אדום */
            color: var(--error-color);
            font-size: 14px;
            margin-top: 5px;
            /* ההודעה מוסתרת כברירת מחדל */
            display: none;
            font-weight: 600;
        }
        
        /* שדה קלט כאשר יש בו שגיאה */
        /* מחלקה שמתווספת לשדות הקלט עצמם במצב שגיאה */
        .input-error {
            /* גבול אדום סביב שדה הקלט */
            border: 1px solid var(--error-color) !important;
            /* רקע אדום בהיר */
            background-color: rgba(236, 87, 102, 0.05);
        }
        
        /* כוכבית אדומה לשדות חובה */
        /* מוסיף תוכן אחרי האלמנט, מבלי לשנות את ה־ HTML בפועל */
        .required::after {
            content: " *";
            color: var(--error-color);
        }
        
        /* שדה ריק שאינו תקין*/
        /* עטיפה סביב שדות חובה בטופס */
        .required-field-error {
            /* כדי שהמיקום יהיה יחסית לאלמנט */
            position: relative;
        }
        
        /* הודעת שגיאה מובנית מתחת לשדה שלא מולא */
        .required-field-error::after {
            /* משרשר את הטקסט "שדה חובה!" באופן אוטומטי */
            content: "שדה חובה!";
            /* ממקם את ההודעה מתחת לאלמנט */
            position: absolute;
            bottom: -20px;
            /* מישר לימין */
            right: 0;
            /* טקסט בצבע אדום */
            color: var(--error-color);
            font-size: 12px;
            font-weight: bold;
            /* רקע אדום בהיר */
            background-color: rgba(236, 87, 102, 0.1);
            padding: 2px 8px;
            border-radius: 4px;
            /* מבטיח שההודעה תהיה מעל לשדות או מסגרות אחרות */
            z-index: 10;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        /* סגנון כפתורי עזרה */
        /* כפתור מילוי אוטומטי */
        .btn-auto-fill {
            /* צהוב-כתום */
            background-color: var(--warning-color);
            /* צבע טקסט אפור כהה */
            color: #333;
        }
        
        .btn-auto-fill:hover {
            /* משנה את צבע הרקע לגוון צהוב־כתום מעט כהה יותר */
            background-color: #e9c13d;
        }
        
        /* כפתור איפוס שינויים */
        .btn-reset {
            /* צבע רקע אפור בהיר */
            background-color: var(--light-color);
            /* צבע טקסט כהה */
            color: var(--dark-color);
        }
        
        .btn-reset:hover {
            background-color: #e2e6ea;
        }
        
        /* כפתור חזרה לדשבורד */
        .btn-back {
            /* צבע רקע אפור כהה */
            background-color: #6c757d;
            /* צבע טקסט לבן  */
            color: white;
            /* מבטל קו תחתון שיש לקישורים */
            text-decoration: none;
            /* מציג את האלמנט בשורה אחת עם אייקון וטקסט */
            display: inline-flex;
            /* ממרכז את האייקון והטקסט לגובה אחד */
            align-items: center;
            margin-bottom: 20px;
        }
        
        .btn-back:hover {
            /* צבע רקע אפור כהה יותר */
            background-color: #5a6268;
            color: white;
            /* שלא יופיע קו תחתון */
            text-decoration: none;
        }
        
        /* עיצוב לאייקון בתוך כפתור החזרה */
        .btn-back i {
            margin-left: 8px;
        }
        
        /* אזור טעינה */
        /* הצגת מסך טעינה חוסם שמכסה את כל הדף בעת פעולה כמו שליחת טופס או טעינת נתונים */
        .loading-overlay {
            /* מקבע את האלמנט ביחס לחלון הדפדפן כולו */
            position: fixed;
            /* כיסוי מלא של כל העמוד */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* יוצר אפקט של "מסך כהה" בזמן טעינה */
            background-color: rgba(0, 0, 0, 0.5);
            /* מוסתר כברירת מחדל — מוצג רק כשיש צורך */
            display: none;
            /* מרכז את תוכן הטעינה (כמו הספינר) גם אופקית וגם אנכית */
            justify-content: center;
            align-items: center;
            /* מוודא שהמסך הזה יעלה על כל אלמנט אחר בדף */
            z-index: 9999;
        }
        
        /* עיצוב תיבת התוכן שמופיעה במרכז מסך הטעינה */
        .loading-content {
            /* צבע רקע לבן */
            background: white;
            padding: 30px;
            border-radius: 12px;
            /* מרכז את כל התוכן בתוך התיבה */
            text-align: center;
            /* יוצר הצללה */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        /* ספינר עגול מסתובב במרכז המסך */
        /* אנימציית טעינה */
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            /* הצלע העליונה - החלק המסתובב בצע כחול */
            border-top: 5px solid var(--accent-color);
            /* הופך את הריבוע לעיגול */
            border-radius: 50%;
            /* מפעיל את האנימציה */
            /* 
            1 שנייה לסיבוב מלא.

            linear – מהירות קבועה לאורך כל הסיבוב.

            infinite – הסיבוב לא נגמר   
            */
            animation: spin 1s linear infinite;
            /* ממרכז את הספינר אופקית */
            margin: 0 auto 20px;
        }
        
        /* אנימציה שנועדה לגרום לאלמנט להסתובב סיבוב מלא ב־360 מעלות בלולאה אינסופית */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            /* כל קבוצה של שדות בטופס תתפוס 100% מרוחב המסך */
            .form-group {
                width: 100%;
            }
            
            .container {
                padding: 15px;
                margin: 15px;
                width: auto;
            }
            
            /* מתצוגה של עמודות מרובות לעמודה אחת בלבד */
            .dog-summary {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- אזור טעינה -->
    <!-- מוסתר כברירת מחדל — מוצג רק כשיש צורך  -->
    <div class="loading-overlay" id="loading-overlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <p>טוען את פרטי כלב...</p>
        </div>
    </div>

    <div class="container">
        <!-- כפתור חזרה -->
        <a href="../../registration/user/user_dashboard_secured.php" class="btn btn-back">
            <i class="fas fa-arrow-right"></i> חזרה לדשבורד
        </a>
        
        <header>
            <h1>עדכון פרטי כלב</h1>
            <p>עדכן את הפרטים של הכלב שלך</p>
        </header>
        
        <div class="update-banner">
            <h2><i class="fas fa-edit"></i> עדכון פרטי כלב</h2>
            <p>כאן תוכל לעדכן את כל הפרטים של הכלב שלך.</p>
            <p>שדות המסומנים בכוכבית (*) הם שדות חובה.</p>
        </div>
        
        <!-- מידע על הכלב הנוכחי -->
        <div class="current-dog-info" id="current-dog-info" style="display: none;">
            <h3><i class="fas fa-dog"></i> <span id="current-dog-name"></span></h3>
            <div class="dog-summary">
                <div class="dog-summary-item">
                    <strong>גזע:</strong>
                    <span id="current-breed"></span>
                </div>
                <div class="dog-summary-item">
                    <strong>גיל:</strong>
                    <span id="current-age"></span> שנים
                </div>
                <div class="dog-summary-item">
                    <strong>משקל:</strong>
                    <span id="current-weight"></span> ק"ג
                </div>
                <div class="dog-summary-item">
                    <strong>מין:</strong>
                    <span id="current-gender"></span>
                </div>
            </div>
        </div>
        
        <div id="status-message" class="status-message"></div>
        
        <!-- כפתורי עזרה - מילוי אוטומטי ואיפוס שינויים -->
        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
            <button id="auto-fill-btn" class="btn btn-auto-fill">
                <i class="fas fa-magic"></i> מילוי אוטומטי
            </button>
            <button id="reset-form-btn" class="btn btn-reset">
                <i class="fas fa-undo"></i> איפוס שינויים
            </button>
        </div>
        
        <!-- enctype="multipart/form-data	מאפשר שליחת קבצים -->
        <!-- novalidate	אומר לדפדפן לא לבצע את בדיקות התוקף (ולידציה) האוטומטיות של שדות הטופס בעת שליחה -->
        <!-- שדות עם required לא יגרמו לטופס להיעצר אוטומטית אם ריקים. -->
        <!-- ולא יופיעו הודעות שגיאה אוטומטיות של הדפדפן -->
        <form id="dog-update-form" enctype="multipart/form-data" novalidate>
            <!-- שדה מוסתר למזהה הכלב -->
            <input type="hidden" id="dog-id" name="dog_id" value="">
            
            <!-- אזורי תוכן נפרדים בטופס  -->
            <div class="form-section">
                <h3><i class="fas fa-paw"></i> פרטים בסיסיים</h3>

                <!-- לארגן שדות זה לצד זה או בשורות שונות לפי רוחב המסך  -->
                <div class="form-row">

                    <!-- תופס חצי שורה ברוחב  -->
                    <div class="form-group">
                        <label for="dog-name" class="required">שם הכלב</label>
                        <input type="text" id="dog-name" name="dog_name" required>
                        <div class="field-error" id="dog-name-error">יש להזין את שם הכלב</div>
                    </div>
                    <!-- תופס חצי שורה ברוחב  -->
                    <div class="form-group">
                        <label for="gender" class="required">מין</label>
                        <select id="gender" name="gender" required>
                            <option value="" disabled>בחרו מין</option>
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
                <!-- תמונה נוכחית -->
                <div class="current-image-label">תמונה נוכחית:</div>
                <div class="current-image" id="current-image-container" style="display: none;">
                    <img id="current-image" src="#" alt="תמונה נוכחית של הכלב">
                </div>
                
                <!-- העלאת תמונה חדשה -->
                 <!--  כשמשתמש לוחץ על ה־ label, זה פותח את חלון בחירת הקובץ  -->
                <label for="file-input" class="image-upload">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>לחצו כאן להעלאת תמונה חדשה של הכלב</p>
                    <p class="small">(פורמטים נתמכים: JPG, PNG, GIF)</p>
                    <p class="small">השאירו ריק אם אינכם רוצים לשנות את התמונה</p>
                </label>
                <!--  כברירת מחדל -מסתיר את אלמנט הקלט לקובץ  -->
                <input type="file" id="file-input" name="dog_image" accept="image/*">
                 <!--  ההודעה מוסתרת כברירת מחדל  -->
                <div class="field-error" id="file-input-error">פורמט קובץ לא נתמך</div>
                <!-- תצוגה מקדימה של תמונה חדשה -->
                <div class="image-preview" id="image-preview">
                    <img id="preview-img" src="#" alt="תצוגה מקדימה של התמונה החדשה">
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
            
            <!-- כפתור השליחה של הטופס -->
            <button type="submit" class="btn btn-update"><i class="fas fa-save"></i> עדכן פרטי כלב</button>
        </form>
    </div>

    <script>

        // רק כאשר כל תוכן הדף נטען במלואו
        $(document).ready(function() {
            // משתנה לשמירת הנתונים המקוריים של הכלב
            let originalDogData = {};
            
            // טעינת נתוני הכלב הפעיל מהסשן
            loadDogData();
            
            // פונקציית לטעינת נתוני הכלב
            function loadDogData() {
                // הצגת אזור הטעינה, כדי להבהיר למשתמש שהנתונים נטענים
                $('#loading-overlay').show();
                
                // לתקשר עם השרת מבלי לרענן את הדף כולו
                // השרת ישתמש ב-active_dog_id מהסשן
                $.ajax({
                    url: 'get_dog_data.php', // קובץ PHP שיחזיר את נתוני הכלב
                    type: 'GET',  // אין שליחת נתונים מהמשתמש, זו בקשה לנתונים מהשרת
                    dataType: 'json', // מצפה לקבל תשובה מסוג JSON
                    
                    // תתבצע כאשר השרת יחזיר תגובה תקינה
                    success: function(response) {
                        // הסתרת אזור הטעינה
                        $('#loading-overlay').hide();
                        
                        // בודק אם התשובה מהשרת מצביעה על הצלחה
                        if (response.status === 'success') {
                            // שמירת הנתונים המקוריים
                            originalDogData = response.data;
                            
                            // מילוי השדות בטופס עם הנתונים הקיימים
                            // פונקציית מילוי הטופס עם נתוני הכלב
                            populateForm(response.data);
                            
                            // הצגת המידע של הכלב הנוכחי
                            // פונקציית הצגת המידע של הכלב הנוכחי
                            displayCurrentDogInfo(response.data);
                            
                        } else {
                            // אם status לא היה "success" – מציג הודעת שגיאה למשתמש
                            showStatusMessage('שגיאה בטעינת נתוני הכלב: ' + response.message, 'error');
                            // // אם אין כלב פעיל, נחזור לדשבורד
                            // setTimeout(function() {
                            //     window.location.href = '../../registration/user/user_dashboard_secured.php';
                            // }, 3000);
                        }
                    },
                    
                    // אם הבקשה נכשלה  מכיוון שהשרת לא נגיש
                    error: function(xhr, status, error) {
                        // הסתרת אזור הטעינה
                        $('#loading-overlay').hide();
                        console.error("שגיאת AJAX:", status, error, xhr.responseText);
                        // מציג הודעת שגיאה ידידותית למשתמש
                        showStatusMessage('לא ניתן לטעון את נתוני הכלב. אנא נסה שנית.', 'error');
                    }
                });
            }
            
            // פונקציית מילוי הטופס עם נתוני הכלב
            function populateForm(dogData) {
                $('#dog-id').val(dogData.dog_id);
                $('#dog-name').val(dogData.dog_name);
                $('#gender').val(dogData.gender);
                $('#chip-number').val(dogData.chip_number);
                $('#breed').val(dogData.breed);
                $('#age').val(dogData.age);
                $('#weight').val(dogData.weight);
                $('#color').val(dogData.color);
                $('#vaccinations').val(dogData.vaccinations_updated);
                $('#personality').val(dogData.dog_personality);
                $('#health-notes').val(dogData.health_notes);
                $('#food-type').val(dogData.food_type);
                $('#food-amount').val(dogData.daily_food_amount);
                $('#vet-name').val(dogData.veterinarian_name);
                $('#vet-phone').val(dogData.veterinarian_phone);
                $('#general-notes').val(dogData.general_notes);
                
                // הצגת התמונה הנוכחית אם קיימת
                if (dogData.image_url && dogData.image_url.trim() !== '') {
                    $('#current-image').attr('src', dogData.image_url);
                    $('#current-image-container').show();
                }
            }
            
            // פונקציית הצגת מידע הכלב הנוכחי
            function displayCurrentDogInfo(dogData) {
                $('#current-dog-name').text(dogData.dog_name);
                $('#current-breed').text(dogData.breed);
                $('#current-age').text(dogData.age);
                $('#current-weight').text(dogData.weight);
                $('#current-gender').text(dogData.gender);
                //  הצגת מידע מצומצם על הכלב הנוכחי 
                $('#current-dog-info').show();
            }
            
            // תצוגה מקדימה של התמונה החדשה
            // מאזין לשינויים בשדה הקלט של הקובץ (כשהמשתמש בוחר קובץ)
            $('#file-input').change(function() {
                // הקובץ הראשון שנבחר
                const file = this.files[0];
                // FileReader() בודק שאכן נבחר קובץ ויוצר אובייקט
                if (file) {
                    // מאפשר לקרוא את תוכן הקובץ בצד לקוח ללא העלאה לשרת
                    const reader = new FileReader();
                    // כשהקובץ נטען הפונקציה מתבצעת
                    reader.onload = function(e) {
                        // תוצאת הקריאה של הקובץ – כלומר התוכן של הקובץ נשמר בטופס במקום של הכתובת
                        $('#preview-img').attr('src', e.target.result);
                        // תיבת התצוגה עם התמונה החדשה
                        $('#image-preview').show();
                    }
                    // קורא את תוכן הקובץ, וממיר אותו למחרוזת כדי להציג את התמונה מיידית מבלי להעלות לשרת
                    reader.readAsDataURL(file);
                }
            });
            
            // פונקציית מילוי אוטומטי של הטופס
            $('#auto-fill-btn').click(function(e) {
                // למנוע מהדפדפן לבצע פעולה אוטומטית בעקבות הקלקה
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
            
            // פונקציית איפוס הטופס לנתונים המקוריים
            $('#reset-form-btn').click(function(e) {
                // למנוע מהדפדפן לבצע פעולה אוטומטית בעקבות הקלקה
                e.preventDefault();
                
                // החזרת הנתונים המקוריים לטופס
                if (originalDogData && Object.keys(originalDogData).length > 0) {
                    populateForm(originalDogData);
                    
                    // איפוס תצוגת התמונה החדשה
                    $('#image-preview').hide();
                    $('#preview-img').attr('src', '#');
                    $('#file-input').val('');
                    
                    // הסרת סימוני שגיאה אם היו
                    $('.field-error').hide();
                    $('input, select, textarea').removeClass('input-error');
                    $('.form-group, .form-group-full').removeClass('required-field-error');
                    
                    // הודעה למשתמש
                    showStatusMessage('הטופס הוחזר לנתונים המקוריים', 'info');
                } else {
                    showStatusMessage('אין נתונים מקוריים לשחזור', 'error');
                }
            });
            
            // ולידציית הטופס לפני שליחה
            $('#dog-update-form').submit(function(e) {
                // למנוע מהדפדפן לבצע פעולה אוטומטית של שליחת הטופס בעקבות הקלקה
                e.preventDefault();
                
                // איפוס כל הודעות השגיאה
                $('.field-error').hide();
                $('input, select, textarea').removeClass('input-error');
                $('.form-group, .form-group-full').removeClass('required-field-error');
                $('#status-message').hide();
                
                // בדיקת תקינות הטופס
                let isValid = true;
                // שישמור את השדה הראשון עם שגיאה (לצורך גלילה אליו)
                let firstErrorField = null;
                // מערך שבו יישמרו שמות השדות החסרים
                let emptyFields = [];
                
                // מיפוי שדות חובה עם שמות תצוגה 
                // מערך אובייקטים שכל אחד מהם מייצג שדה חובה בטופס
                const requiredFields = [
                    { id: 'dog-name', name: 'שם הכלב', errorMsg: 'יש להזין את שם הכלב' },
                    { id: 'gender', name: 'מין', errorMsg: 'יש לבחור את מין הכלב' },
                    { id: 'chip-number', name: 'מספר שבב', errorMsg: 'יש להזין את מספר השבב' },
                    { id: 'breed', name: 'גזע', errorMsg: 'יש להזין את גזע הכלב' },
                    { id: 'age', name: 'גיל', errorMsg: 'יש להזין את גיל הכלב' },
                    { id: 'weight', name: 'משקל', errorMsg: 'יש להזין את משקל הכלב' },
                    { id: 'color', name: 'צבע', errorMsg: 'יש להזין את צבע הכלב' },
                    { id: 'vaccinations', name: 'חיסונים עדכניים', errorMsg: 'יש לבחור האם החיסונים עדכניים' },
                    { id: 'personality', name: 'אופי הכלב', errorMsg: 'יש להזין את אופי הכלב' },
                    { id: 'health-notes', name: 'הערות בריאותיות', errorMsg: 'יש להזין הערות בריאותיות' },
                    { id: 'food-type', name: 'סוג מזון', errorMsg: 'יש להזין את סוג המזון' },
                    { id: 'food-amount', name: 'כמות אוכל יומית', errorMsg: 'יש להזין את כמות האוכל היומית' },
                    { id: 'vet-name', name: 'שם הווטרינר', errorMsg: 'יש להזין את שם הווטרינר' },
                    { id: 'vet-phone', name: 'מספר טלפון של הווטרינר', errorMsg: 'יש להזין את מספר הטלפון של הווטרינר' },
                    { id: 'general-notes', name: 'הערות כלליות', errorMsg: 'יש להזין הערות כלליות' }
                ];
                
                // בדיקת כל שדות החובה
                // לולאה שעוברת על כל שדה חובה במערך
                for (const field of requiredFields) {
                    // מציאת האלמנט המתאים למזהה של השדה
                    const $field = $('#' + field.id);
                    // הערך הנוכחי של השדה
                    let value = $field.val();
                    // הגדרת משתנה שיקבע אם השדה ריק
                    let isEmpty = false;
                    
                    // אם הערך הוא מחרוזת, בודק גם שהיא לא מכילה רק רווחים או שהערך של הקלט ריק
                    isEmpty = !value || (typeof value === 'string' && !value.trim());
                    
                    if (isEmpty) {
                        // סימון השדה כשגוי ותצוגת הודעת שגיאה
                        // מוסיף מסגרת אדומה ורקע
                        $field.addClass('input-error');
                        
                        // אם יש אלמנט שגיאה מתחת לשדה - להציג אותו
                        // נכון לעכשיו לכל האלמנטים יש אלמנט שגיאה
                        const $errorElement = $('#' + field.id + '-error');
                        // בודק אם האלמנט באמת קיים בדף, כלומר אם נמצא לפחות אלמנט אחד עם אותו ID
                        if ($errorElement.length) {
                            $errorElement.show();
                        } else {
                            // אם אין אלמנט שגיאה, להוסיף אפקט ויזואלי
                            // ברגע שמחלקת ההורה מקבלת את המחלקה, מופיעה תצוגה של "שדה חובה!" מתחת לשדה
                            $field.parent().addClass('required-field-error');
                        }
                        
                        // מסמן שהטופס אינו תקין
                        isValid = false;
                        // שומר את השדה הראשון שבו נמצאה שגיאה, לצורך גלילה אליו מאוחר יותר
                        // זה מבטיח שנגלול תמיד לשדה הבעייתי הראשון בלבד, ולא לכל השדות ביחד
                        // אם firstErrorField כבר הוגדר קודם – שמור אותו כמו שהוא
                        // firstErrorField אחרת – הגדר אותו עכשיו כ־ $field
                        firstErrorField = firstErrorField || $field;
                        // מוסיף את שם השדה אחר כך כדי להציג למשתמש הודעה עם כל השדות הבעייתים 
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
                        // מחבר את כל שמות השדות יחד לרשימה עם פסיקים ביניהם, וכל אחד עטוף בתג <strong>
                        errorMessage = 'השדות הבאים חסרים לצורך עדכון הכלב: <strong>' + emptyFields.join('</strong>, <strong>') + '</strong>';
                    }
                    
                    // הצגת הודעת שגיאה ספציפית באדום
                    $('#status-message').html(errorMessage).removeClass().addClass('status-message status-error').show();
                    
                    // גלילה לשדה הראשון עם שגיאה
                    if (firstErrorField) {
                        $('html, body').animate({
                            scrollTop: firstErrorField.offset().top - 250
                        }, 400);
                    }
                    
                    return false;
                }
                
                // אם הטופס תקין, ממשיך לשליחה
                showStatusMessage('מעדכן את נתוני הכלב הפעיל...', 'info');
                
                const formData = new FormData(this);
                
                // לתקשר עם השרת מבלי לרענן את הדף כולו
                $.ajax({
                    url: 'dog_update_server.php',
                    type: 'POST',
                    // מכיל את כל שדות הטופס כולל קבצים
                    data: formData,
                    contentType: false,
                    processData: false,
                    
                    success: function(response) {
                        // מדפיס את התגובה של השרת בקונסול
                        console.log("תשובה התקבלה:", response); // לוג לדיבוג
                        
                        try {
                            // בדיקה אם התשובה מכילה שגיאת SQL
                            if (response.includes("SQL syntax") || response.includes("error in your SQL")) {
                                // הצגת הודעה ידידותית למשתמש במקום שגיאת SQL
                                showStatusMessage('אירעה שגיאה בעדכון הנתונים. אנא בדוק את השדות שהזנת ונסה שנית.', 'error');
                                return;
                            }
                            // ממשיך לנסות לנתח את התשובה שהתקבלה מהשרת כ־ JSON
                            const result = JSON.parse(response);
                            if (result.status === 'success') {
                                showStatusMessage('פרטי הכלב עודכנו בהצלחה! מעביר אותך לדשבורד...', 'success');
                                
                                // הפניה לדשבורד המשתמש לאחר 3 שניות
                                setTimeout(function() {
                                    window.location.href = '../../registration/user/user_dashboard_secured.php';
                                }, 3000);

                                // אם הוא לא הצליח לקרוא את קובץ ה JSON
                            } else {
                                // הודעת שגיאה ידידותית למשתמש
                                let errorMsg = 'אירעה שגיאה בעדכון הנתונים: ';
                                
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
                                        // לא לשנות כאן את ה dog_image
                                        'dog_image': 'תמונת הכלב',
                                        'dog_personality': 'אופי הכלב',
                                        'health_notes': 'הערות בריאותיות',
                                        'food_type': 'סוג מזון',
                                        'daily_food_amount': 'כמות אוכל יומית',
                                        'veterinarian_name': 'שם הווטרינר',
                                        'veterinarian_phone': 'מספר טלפון של הווטרינר',
                                        'general_notes': 'הערות כלליות'
                                    };
                                    // אם יש מפתח במילון שמתאים לשם שהשרת החזיר אז השתמש בו, אחרת (למקרה שאין תרגום במילון) השתמש בשם המקורי כמו שהוא
                                    const fieldName = fieldNames[result.field] || result.field;
                                    errorMsg += 'שדה "' + fieldName + '" אינו תקין. ';
                                    
                                    // הדגשת השדה השגוי בטופס
                                    // מחליף קו תחתון בקו מקשר כדי להתאים לשמות המזהים בדף
                                    $('#' + result.field.replace('_', '-')).addClass('input-error');
                                } 
                                
                                // הוספת הודעת השגיאה הספציפית אם קיימת
                                if (result.message) {
                                    errorMsg += result.message;
                                }
                                
                                showStatusMessage(errorMsg, 'error');
                            }
                            // אם התגובה מהשרת אינה JSON תקין
                        } catch(e) {
                            console.error("שגיאת JSON:", e, "תשובה מקורית:", response);
                            
                            // הודעות שגיאה ידידותיות למשתמש במקום שגיאות טכניות
                            if (response.includes("Warning") || response.includes("Fatal error") || response.includes("Notice")) {
                                showStatusMessage('אירעה שגיאה בעדכון הנתונים. אנא נסה שנית מאוחר יותר.', 'error');
                            } else {
                                showStatusMessage('אירעה שגיאה בעדכון הנתונים. אנא ודא שכל השדות תקינים ונסה שנית.', 'error');
                            }
                        }
                    },
                    // לא הצליח בכלל לתקשר עם השרת
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
                // מכניס את תוכן ההודעה עצמה לתוך האלמנט
                statusMessage.html(message);
                // ניקוי עיצובים ישנים
                statusMessage.removeClass('status-success status-error status-info');
                // הוספת המחלקה הרלוונטית
                statusMessage.addClass('status-' + type);
                statusMessage.show();
                
                // גלילה אל ההודעה
                $('html, body').animate({
                    scrollTop: statusMessage.offset().top - 200
                }, 500);
                
                // הסתרת ההודעה אחרי 5 שניות רק אם זה לא הודעת שגיאה
                if (type !== 'error') {
                    setTimeout(function() {
                        statusMessage.fadeOut(1000);
                    }, 5000);
                }
            }
        });
    </script>
</body>
</html>