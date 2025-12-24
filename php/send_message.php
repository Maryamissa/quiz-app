<?php

error_reporting(E_ALL);// Enable error report
ini_set('display_errors', 1);

header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'quiz-app';
$user = 'root';
$pass = '';
$port = 3315;

try {
    $conn = new mysqli($host, $user, $pass, $dbname, $port);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    
    if (!isset($_POST['room_id']) || !isset($_POST['user_id']) || !isset($_POST['message'])) {
        throw new Exception("Missing required fields");
    }
    
    $room_id = (int)$_POST['room_id'];
    $user_id = (int)$_POST['user_id'];
    $message = trim($_POST['message']);
    
    if ($room_id <= 0 || $user_id <= 0) {
        throw new Exception("Invalid room or user ID");
    }
    
    if (empty($message)) {
        throw new Exception("Message cannot be empty");
    }
    

    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    
    
    $stmt = $conn->prepare("INSERT INTO chat_messages (chatroom_id, user_id, message) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("iis", $room_id, $user_id, $message);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    
    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully',
        'message_id' => $stmt->insert_id
    ]);
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>