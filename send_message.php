<?php
include 'db_connect.php';
session_start(); // Ensure session is started

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'] ?? ''; // Allow empty message if a file is sent
    $file_path = null;
    $file_type = null;

    // Fetch sender's full name from database
    $stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt->bind_param("i", $sender_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sender_name = ($row = $result->fetch_assoc()) ? $row['full_name'] : "Unknown User";
    $stmt->close();

    // Check if a file was uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $target_dir = "uploads/"; // Ensure this directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create if not exists
        }

        $file_name = time() . "_" . basename($_FILES["file"]["name"]); // Unique filename
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Allowed file types (images & documents)
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx'];

        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $file_path = $target_file; // Store file path in DB
            } else {
                echo json_encode(["success" => false, "error" => "File upload failed"]);
                exit;
            }
        } else {
            echo json_encode(["success" => false, "error" => "Invalid file type"]);
            exit;
        }
    }

    // Insert into database (message or file)
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, file_path, file_type) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $sender_id, $receiver_id, $message, $file_path, $file_type);

    if ($stmt->execute()) {
        // ✅ Insert Notification AFTER message is saved
        $content = "New message from $sender_name.";

        $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, type, content) VALUES (?, 'message', ?)");
        $notif_stmt->bind_param("is", $receiver_id, $content);
        $notif_stmt->execute();
        $notif_stmt->close();

        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Database insert failed"]);
    }

    $stmt->close();
    $conn->close();
}
?>
