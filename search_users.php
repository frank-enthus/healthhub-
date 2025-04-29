<?php
include 'db_connect.php';

if (isset($_GET['query'])) {
    $search = "%" . $_GET['query'] . "%";

    // Fetch users and join with patients table to get profile pictures correctly
    $stmt = $conn->prepare("
        SELECT users.id, users.full_name, 
            CASE 
                WHEN users.user_type = 'doctor' THEN users.profile_picture
                WHEN users.user_type = 'patient' THEN patients.profile_picture
                ELSE NULL 
            END AS profile_picture,
            users.user_type 
        FROM users
        LEFT JOIN patients ON users.id = patients.id
        WHERE users.full_name LIKE ? 
        LIMIT 10
    ");

    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode($users);
}
?>
