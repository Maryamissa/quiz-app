<?php

error_reporting(E_ALL);// Enable error report
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'quiz-app';
$user = 'root';
$pass = '';
$port = 3315;

try {
    $conn = new mysqli($host, $user, $pass, $dbname, $port);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    if (!isset($_GET['room_id'])) {
        die("Room ID is required");
    }
    
    $room_id = (int)$_GET['room_id'];
    
    if ($room_id <= 0) {
        die("Invalid room ID");
    }
    
    // Get messages 
    $query = "
        SELECT 
            m.id,
            m.message,
            m.created_at,
            u.id as user_id,
            u.username,
            u.avatar
        FROM chat_messages m
        JOIN users u ON u.id = m.user_id
        WHERE m.chatroom_id = ?
        ORDER BY m.created_at ASC
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param('i', $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo '<div class="no-messages">No messages yet. Be the first to say hello! 👋</div>';
    } else {
        while ($row = $result->fetch_assoc()) {
            $time = date('h:i A', strtotime($row['created_at']));
            $date = date('M j', strtotime($row['created_at']));
            
            echo '<div class="message">';
            echo '<div class="message-header">';
            echo '<span class="username">' . htmlspecialchars($row['username']) . '</span>';
            echo '<span class="time" title="' . htmlspecialchars($row['created_at']) . '">';
            echo htmlspecialchars($time) . ' • ' . htmlspecialchars($date);
            echo '</span>';
            echo '</div>';
            echo '<div class="message-content">' . nl2br(htmlspecialchars($row['message'])) . '</div>';
            echo '</div>';
        }
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo '<div class="error">Error loading messages: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>