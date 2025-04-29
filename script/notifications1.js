$(document).ready(function () {
    console.log("Doctor notifications script loaded! ✅");

    function fetchDoctorNotifications() {
        console.log("Fetching doctor notifications... 🔄");

        $.ajax({
            url: "fetch_notifications.php",
            method: "GET",
            dataType: "json",
            success: function (notifications) {
                console.log("Doctor Notifications Fetched: ", notifications);

                let count = notifications.length;
                $("#notification-count").text(count > 0 ? count : "0");

                let notificationList = "";
                if (count > 0) {
                    notifications.forEach(notification => {
                        notificationList += `<li>${notification.content}</li>`;
                    });
                } else {
                    notificationList = "<li>No new notifications</li>";
                }

                $("#notification-dropdown").html(notificationList);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching notifications:", error);
            }
        });
    }

    // Fetch notifications every 5 seconds
    setInterval(fetchDoctorNotifications, 5000);
    fetchDoctorNotifications();

    // When bell is clicked, mark notifications as read and show the dropdown
    $("#notification-bell").click(function () {
        console.log("Doctor clicked bell! 🔔");

        $("#messages").toggle(); // Show or hide notifications

        $.ajax({
            url: "mark_notifications_read.php",
            method: "POST",
            success: function () {
                console.log("Doctor notifications marked as read!");
                $("#notification-count").text("0");
            }
        });
    });
});
