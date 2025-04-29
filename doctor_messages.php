<?php
session_start();
include 'db_connect.php';

// Ensure doctor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'doctor') {
    header("Location: doctor.php");
    exit();
}
$doctor_id = $_SESSION['user_id'];

// Fetch the most recent patient contact if no specific user is selected
$patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;
if (!$patient_id) {
    $recent_contact_query = "SELECT DISTINCT users.id FROM messages 
        JOIN users ON (messages.sender_id = users.id OR messages.receiver_id = users.id)
        WHERE (messages.sender_id = ? OR messages.receiver_id = ?) AND users.user_type = 'patient' 
        ORDER BY messages.sent_at DESC LIMIT 1";
    $stmt = $conn->prepare($recent_contact_query);
    $stmt->bind_param("ii", $doctor_id, $doctor_id);
    $stmt->execute();
    $stmt->bind_result($recent_contact_id);
    if ($stmt->fetch()) {
        $patient_id = $recent_contact_id;
    }
    $stmt->close();
}

// Get the patient's name
$patient_name = "Select a patient to chat";
if ($patient_id) {
    $stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $stmt->bind_result($patient_name);
    $stmt->fetch();
    $stmt->close();
}

// Fetch recent patient contacts
$recent_contacts_query = "SELECT DISTINCT users.id, users.full_name, users.profile_picture FROM messages 
    JOIN users ON (messages.sender_id = users.id OR messages.receiver_id = users.id)
    WHERE (messages.sender_id = ? OR messages.receiver_id = ?) AND users.user_type = 'patient'
    ORDER BY messages.sent_at DESC LIMIT 10";
$stmt = $conn->prepare($recent_contacts_query);
$stmt->bind_param("ii", $doctor_id, $doctor_id);
$stmt->execute();
$recent_contacts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Messages | HealthHub</title>
    <link rel="stylesheet" href="styles/chats.css">
    <script src="script/chat.js"></script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="chat-container">
    <button class="toggle-sidebar">☰</button>

        <div class="sidebar">
            <input type="text" id="searchUser" placeholder="Search patients...">
            <div id="userResults"></div>
            <div id="notif-bell" onclick="fetchNotifications()">🔔</div>
<div id="notification-list"></div>

            <h3>Recent Chats</h3>
            <div id="recentChats">
                <?php foreach ($recent_contacts as $contact): ?>
                    <div class="user-item" data-id="<?= $contact['id'] ?>">
                        <img src="<?= $contact['profile_picture'] ?? 'default.png' ?>" alt="Profile" width="30">
                        <span><?= $contact['full_name'] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div> <a href="doctor-dashboard.php" class="back-button">← Back to dashboard</a> </div>
        </div>

        <div class="chat-window">
            <div id="chatHeader">
                <?= htmlspecialchars($patient_name) ?>
            </div>
            <div id="chatMessages"></div>

            <?php if ($patient_id): ?>
            <div class="message-input">
    <textarea id="messageInput" placeholder="Type a message..."></textarea>
    <input type="file" id="fileInput" accept="image/*,.pdf,.doc,.docx">
    <button id="sendBtn">Send</button>
</div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function loadChat() {
    let senderId = <?= $doctor_id ?>;
    let receiverId = <?= $patient_id ?? 'null' ?>;

    if (!receiverId) return;

    $.get(`fetch_messages.php?sender_id=${senderId}&receiver_id=${receiverId}`, function(data) {
        let messages = JSON.parse(data);
        let output = "";
        messages.forEach(msg => {
            let className = (msg.sender_id == senderId) ? "sent" : "received";
            let messageContent = "";

            if (msg.file_path) {
                let fileExt = msg.file_path.split('.').pop().toLowerCase();
                if (["jpg", "jpeg", "png", "gif"].includes(fileExt)) {
                    // Display image
                    messageContent = `<img src="${msg.file_path}" class="chat-image" alt="Sent Image">`;
                } else {
                    // Display file download link
                    messageContent = `<a href="${msg.file_path}" download class="chat-file">Download File</a>`;
                }
            } else {
                // Regular text message
                messageContent = msg.message;
            }

            output += `<div class="message ${className}">${messageContent}</div>`;
        });

        $("#chatMessages").html(output).scrollTop($("#chatMessages")[0].scrollHeight);
    });
}


    $("#sendBtn").click(function() {
    let message = $("#messageInput").val().trim();
    let senderId = <?= $doctor_id ?>;
    let receiverId = <?= $patient_id ?? 'null' ?>;
    let formData = new FormData();

    formData.append("sender_id", senderId);
    formData.append("receiver_id", receiverId);
    formData.append("message", message);

    let fileInput = $("#fileInput")[0].files[0]; // Get the selected file
    if (fileInput) {
        formData.append("file", fileInput);
    }

    if ((message !== "" || fileInput) && receiverId) {
        $.ajax({
            url: "send_message.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $("#messageInput").val(""); // Clear input
                    $("#fileInput").val(""); // Clear file input
                    loadChat(); // Reload chat
                }
            }
        });
    }
});


    $("#searchUser").on("keyup", function() {
        let query = $(this).val();
        if (query.length > 0) {
            $.get(`search_users.php?query=${query}&user_type=patient`, function(data) {
                let users = JSON.parse(data);
                let output = "";
                users.forEach(user => {
                    output += `<div class="user-item" data-id="${user.id}">
                        <img src="${user.profile_picture || 'default.png'}" width="30">
                        <span>${user.full_name}</span>
                    </div>`;
                });
                $("#userResults").html(output);
            });
        } else {
            $("#userResults").html("");
        }
    });

    $("#userResults, #recentChats").on("click", ".user-item", function() {
        let userId = $(this).data("id");
        if (userId) {
            window.location.href = `doctor_messages.php?patient_id=${userId}`;
        }
    });

    loadChat();
    setInterval(loadChat, 3000);
    </script>
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  <!-- Ensure jQuery is included -->
<script>
    $(document).ready(function () {
        $(".toggle-sidebar").click(function () {
            $(".sidebar").toggleClass("active");
        });
    });
</script>
<script>
    function scrollToBottom() {
        var chatMessages = document.getElementById("chatMessages");
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    $(document).ready(function () {
        scrollToBottom(); // Scroll on page load

        // Also scroll down when a new message is received
        setInterval(scrollToBottom, 1000);
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $(".user-item").click(function () {
            $(".user-item").removeClass("active"); // Remove highlight from others
            $(this).addClass("active"); // Add highlight to the clicked user
        });
    });
</script>
<script>
function fetchNotifications() {
    fetch('fetch_notifications.php')
    .then(response => response.json())
    .then(data => {
        let notifContainer = document.getElementById('notification-list'); // Your notification dropdown container
        notifContainer.innerHTML = ''; // Clear old notifications

        if (data.length === 0) {
            notifContainer.innerHTML = '<p>No new notifications</p>';
            return;
        }

        data.forEach(notif => {
            let notifItem = document.createElement('div');
            notifItem.classList.add('notification-item');
            notifItem.innerHTML = `<strong>${notif.content}</strong> <br> <small>${notif.time}</small>`;
            notifContainer.appendChild(notifItem);
        });

        document.getElementById('notif-bell').classList.add('has-notifications'); // Highlight bell if new messages
    })
    .catch(error => console.error('Error fetching notifications:', error));
}

// Refresh notifications every 10 seconds
setInterval(fetchNotifications, 10000);
fetchNotifications(); // Fetch once on page load
</script>

</body>
</html>
