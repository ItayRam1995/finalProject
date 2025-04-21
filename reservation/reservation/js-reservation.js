class Reservation {
    constructor(startDate, endDate) {
        this.startDate = new Date(startDate.split('/').reverse().join('-'));
        this.endDate = new Date(endDate.split('/').reverse().join('-'));
    }

    getDateRange() {
        let dates = [];
        let currentDate = new Date(this.startDate);
        while (currentDate <= this.endDate) {
            dates.push(this.formatDate(currentDate));
            currentDate.setDate(currentDate.getDate() + 1);
        }
        return dates;
    }

    formatDate(date) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }
}

$(document).ready(function() {
    const dateFormat = 'dd/mm/yy';
    let unavailableDates = [];

    $('#start-date, #end-date').datepicker({
        dateFormat: dateFormat,
        onSelect: function() {
            const startDate = $('#start-date').val();
            const endDate = $('#end-date').val();
    
            // תמיד נעדכן את מספר הימים
            updateTotalDays(startDate, endDate);
    
            // בדיקה האם התאריך תקין
            if (startDate && endDate && !isValidDateRange(startDate, endDate)) {
                alert("תאריך היציאה חייב להיות לאחר תאריך הכניסה.");
                $('#end-date').val('');
                updateTotalDays(startDate, ''); // לעדכן את הימים אם אין תאריך סיום
            }
        }
    });
    //שליחה לבדיקת תאריכים זמינים
    $.getJSON('get_unavailable_dates.php', function(data) {
        unavailableDates = data;
        $("#start-date, #end-date").datepicker("option", "beforeShowDay", function(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const formatted = `${day}/${month}/${year}`;
            if (unavailableDates.includes(formatted)) {
                return [false, "unavailable-date", "תאריך תפוס"];
            }
            return [true, "", ""];
        });
    });
    //שליחה של הימים והמשך הזמנה
    $('#submit').on('click', function(e) {
        e.preventDefault();
        const startDate = $('#start-date').val();
        const endDate = $('#end-date').val();
        if (startDate && endDate) {
            $.post('reservation.php', {
                start_date: startDate,
                end_date: endDate
            }, function(response) {
                if (response.success) {
                    window.location.href = "../services/services.html";
                } else {
                    $('#message').text(response.error || "שגיאה בלתי צפויה");
                }
            }, 'json');
        } else {
            $('#message').text('אנא מלא את כל השדות.');
        }
    });

    function isValidDateRange(startDate, endDate) {
        const start = new Date(startDate.split('/').reverse().join('-'));
        const end = new Date(endDate.split('/').reverse().join('-'));
        return start <= end;
    }
    //פונקציית שמעדכנת את כמות הימים שבחרו
    function updateTotalDays(start, end) {
        // לבדוק אם אחד התאריכים לא קיים
        if (!start || !end) {
            $('#total-days').text('0 ימים');
            return;
        }
    
        // המרה לסטרינג את התאריך
        const startParts = start.split("/");
        const endParts = end.split("/");
        const startDate = new Date(startParts[2], startParts[1] - 1, startParts[0]); // שנה, חודש (מאופס), יום
        const endDate = new Date(endParts[2], endParts[1] - 1, endParts[0]);
        //בדיקת התאריכים
        console.log("Start Date:", startDate);
        console.log("End Date:", endDate);

        // בדיקת תאריכים אם לא זמינים
        if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
            $('#total-days').text('0 ימים');
            return;
        }
    
        // חישוב ההפרש בין התאריכים
        const timeDiff = endDate - startDate; // ההפרש במילי שניות
        const dayDiff = Math.floor(timeDiff / (1000 * 3600 * 24)); // ממירים ממ' מילי שניות לימים
        //בדיקה של החישוב תאריכים
        console.log("Time Difference (ms):", timeDiff);
        // מעדכנים את כמות הימים
        if (dayDiff >= 0) {
            $('#total-days').text(dayDiff + ' ימים');
        } else {
            $('#total-days').text('0 ימים');
        }
    }
});
