<?php
session_start();
// מוחק את כל המשתנים שנשמרו בסשן
session_unset();
// משמיד את כל הסשן לגמרי
session_destroy();
// מפנה את המשתמש לדף ההתחברות לאחר ההתנתקות
header("Location: login.html");
// מפסיק את הרצת הקוד
exit;
?>