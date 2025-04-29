<?php
session_start();
include 'db_connect.php';

// Ensure patient is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: patient.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Fetch the most recent doctor contact if no specific doctor is selected
$receiver_id = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : null;
if (!$receiver_id) {
    $recent_contact_query = "SELECT DISTINCT users.id FROM messages 
        JOIN users ON (messages.sender_id = users.id OR messages.receiver_id = users.id)
        WHERE (messages.sender_id = ? OR messages.receiver_id = ?) 
        AND users.user_type = 'doctor' AND users.id != ?
        ORDER BY messages.sent_at DESC LIMIT 1";
    $stmt = $conn->prepare($recent_contact_query);
    $stmt->bind_param("iii", $user_id, $user_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($recent_contact_id);
    if ($stmt->fetch()) {
        $receiver_id = $recent_contact_id;
    }
    $stmt->close();
}

// Get doctor's name from users table
$receiver_name = "Select a doctor to chat";
if ($receiver_id) {
    $stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ? AND user_type = 'doctor'");
    $stmt->bind_param("i", $receiver_id);
    $stmt->execute();
    $stmt->bind_result($receiver_name);
    $stmt->fetch();
    $stmt->close();
}

// Fetch recent doctor contacts
$recent_contacts_query = "SELECT DISTINCT users.id, users.full_name, users.profile_picture FROM messages 
    JOIN users ON (messages.sender_id = users.id OR messages.receiver_id = users.id)
    WHERE (messages.sender_id = ? OR messages.receiver_id = ?) AND users.user_type = 'doctor'
    ORDER BY messages.sent_at DESC LIMIT 10";
$stmt = $conn->prepare($recent_contacts_query);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$recent_contacts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | HealthHub</title>
    <link rel="stylesheet" href="styles/chats.css">
   

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    
    <div class="chat-container">
    <button class="toggle-sidebar">☰</button>
        <div class="sidebar">
       

            <input type="text" id="searchDoctor" placeholder="Search doctors...">
            <div id="doctorResults"></div>
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
             <a href="patient-dashboard.php" class="back-button">← Back to dashboard</a> 
        </div>

        <div class="chat-window">
            <div id="chatHeader">
                <?= htmlspecialchars($receiver_name) ?>
            </div>
            <div id="chatMessages"></div>

            <?php if ($receiver_id): ?>
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
    let senderId = <?= $user_id ?>;
    let receiverId = <?= $receiver_id ?? 'null' ?>;
    
    if (!receiverId) return;

    $.get(`fetch_messages.php?sender_id=${senderId}&receiver_id=${receiverId}`, function(data) {
        let messages = JSON.parse(data);
        let output = "";
        messages.forEach(msg => {
            let className = (msg.sender_id == senderId) ? "sent" : "received";
            let messageContent = "";
            
            if (msg.message) {
                messageContent += `<p>${msg.message}</p>`;
            }
            
            if (msg.file_path) {
                let fileExt = msg.file_path.split('.').pop().toLowerCase();
                if (["jpg", "jpeg", "png", "gif"].includes(fileExt)) {
                    // Display images
                    messageContent += `<img src="uploads/${msg.file_path}" class="chat-image" alt="Sent Image">`;
                } else {
                    // Display file as a downloadable link
                    messageContent += `<a href="uploads/${msg.file_path}" target="_blank" class="chat-file">Download File</a>`;
                }
            }
            
            output += `<div class="message ${className}">${messageContent}</div>`;
        });
        
        $("#chatMessages").html(output).scrollTop($("#chatMessages")[0].scrollHeight);
    });
}


    $("#sendBtn").click(function() {
    let message = $("#messageInput").val().trim();
    let file = $("#fileInput")[0].files[0]; // Get selected file
    let senderId = <?= $user_id ?>;
    let receiverId = <?= $receiver_id ?? 'null' ?>;

    if (!receiverId) return;

    let formData = new FormData();
    formData.append("sender_id", senderId);
    formData.append("receiver_id", receiverId);
    if (message) formData.append("message", message);
    if (file) formData.append("file", file);

    $.ajax({
        url: "send_message.php",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            $("#messageInput").val("");
            $("#fileInput").val(""); // Reset file input
            loadChat();
        }
    });
});



    $("#searchDoctor").on("keyup", function() {
        let query = $(this).val();
        if (query.length > 0) {
            $.get(`search_users.php?query=${query}&user_type=doctor`, function(data) {
                let users = JSON.parse(data);
                let output = "";
                users.forEach(user => {
                    output += `<div class="user-item" data-id="${user.id}">
                        <img src="${user.profile_picture || 'default.png'}" width="30">
                        <span>${user.full_name}</span>
                    </div>`;
                });
                $("#doctorResults").html(output);
            });
        } else {
            $("#doctorResults").html("");
        }
    });

    $("#doctorResults, #recentChats").on("click", ".user-item", function() {
        let userId = $(this).data("id");
        if (userId) {
            window.location.href = `patient_messages.php?doctor_id=${userId}`;
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
