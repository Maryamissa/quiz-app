<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'quiz-app';
$user = 'root';
$pass = '';
$port = 3315;

$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) die('Connection failed: ' . $conn->connect_error);

$room_id = (int)($_GET['room_id'] ?? 0);
$username = trim($_GET['username'] ?? '');

if (!$room_id || !$username) {
    die("Error: Room ID and Username are required. <a href='chatrooms.php'>Go back</a>");
}

$u = $conn->prepare("SELECT id, username FROM users WHERE username = ? LIMIT 1");
$u->bind_param('s', $username);
$u->execute();
$userRow = $u->get_result()->fetch_assoc();

if (!$userRow) {
    echo "User '$username' not found.<br>";
    echo "Available users: ";
    $allUsers = $conn->query("SELECT username FROM users");
    $users = [];
    while ($row = $allUsers->fetch_assoc()) {
        $users[] = $row['username'];
    }
    echo implode(', ', $users);
    echo "<br><a href='chatrooms.php'>Go back</a>";
    exit;
}

$user_id = $userRow['id'];

$room = $conn->prepare("SELECT * FROM chatrooms WHERE id = ? LIMIT 1");
$room->bind_param('i', $room_id);
$room->execute();
$roomResult = $room->get_result()->fetch_assoc();

if (!$roomResult) {
    die("Room not found. <a href='chatrooms.php'>Go back</a>");
}

$check_member = $conn->prepare("SELECT id FROM chatroom_users WHERE chatroom_id = ? AND user_id = ?");
$check_member->bind_param('ii', $room_id, $user_id);
$check_member->execute();
if (!$check_member->get_result()->fetch_assoc()) {
    $join = $conn->prepare("INSERT INTO chatroom_users (chatroom_id, user_id) VALUES (?, ?)");
    $join->bind_param('ii', $room_id, $user_id);
    $join->execute();
    
    $upd = $conn->prepare("UPDATE chatrooms SET user_count = user_count + 1 WHERE id = ?");
    $upd->bind_param('i', $room_id);
    $upd->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_room'])) {
    $room_id = (int)$_POST['room_id'];
    $created_by = 1; // Change this to match your actual user ID
    
    error_log("Attempting to delete room ID: $room_id by user: $created_by");
    
    // Check ownership before deleting
    $check = $conn->prepare("SELECT id FROM chatrooms WHERE id = ? AND created_by = ?");
    $check->bind_param('ii', $room_id, $created_by);
    $check->execute();
    $result = $check->get_result();
    
    error_log("Ownership check result: " . $result->num_rows . " rows found");
    
    if ($result->num_rows === 1) {
        // Delete chatroom_users entries first (foreign key constraint)
        $delete_users = $conn->prepare("DELETE FROM chatroom_users WHERE chatroom_id = ?");
        $delete_users->bind_param('i', $room_id);
        $users_deleted = $delete_users->execute();
        $delete_users->close();
        
        error_log("Chatroom users deleted: " . ($users_deleted ? 'Yes' : 'No'));
        
        // Delete the chatroom
        $delete_room = $conn->prepare("DELETE FROM chatrooms WHERE id = ?");
        $delete_room->bind_param('i', $room_id);
        
        if ($delete_room->execute()) {
            error_log("Room deleted successfully");
            $success = "Room deleted successfully!";
        } else {
            error_log("Failed to delete room: " . $delete_room->error);
            $error = "Failed to delete room: " . $delete_room->error;
        }
        $delete_room->close();
    } else {
        error_log("User $created_by doesn't own room $room_id or room doesn't exist");
        $error = "You can only delete rooms you created!";
    }
    $check->close();
}


echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room: ' . htmlspecialchars($roomResult['name']) . '</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:linear-gradient(135deg, #162e94 100%, rgb(20, 20, 235) 00%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .chat-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .chat-header {
            background: rgb(20, 20, 235);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .room-info h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .user-info {
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .back-btn {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            transition: background 0.3s;
        }
        
        .back-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        #messages {
            height: 500px;
            overflow-y: auto;
            padding: 20px;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .message {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .message.received {
            background: white;
            border: 1px solid #e0e0e0;
            align-self: flex-start;
            border-top-left-radius: 5px;
        }
        
        .message.sent {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            align-self: flex-end;
            border-top-right-radius: 5px;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 0.9em;
        }
        
        .username {
            font-weight: bold;
        }
        
        .time {
            color: #888;
            font-size: 0.8em;
        }
        
        .message.sent .time {
            color: rgba(255,255,255,0.8);
        }
        
        .message-content {
            word-wrap: break-word;
            line-height: 1.4;
        }
        
        .chat-input-container {
            padding: 20px;
            background: white;
            border-top: 1px solid #eee;
        }
        
        #sendForm {
            display: flex;
            gap: 10px;
        }
        
        #msgInput {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 1rem;
            transition: border-color 0.3s;
            outline: none;
        }
        
        #msgInput:focus {
            border-color: #4facfe;
        }
        
        #sendForm button {
            padding: 15px 30px;
            background: rgb(20, 20, 235);
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        #sendForm button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        }
        
        #sendForm button:active {
            transform: translateY(0);
        }
        
        #messages::-webkit-scrollbar {
            width: 8px;
        }
        
        #messages::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        #messages::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        #messages::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        .typing-indicator {
            padding: 10px;
            font-style: italic;
            color: #888;
            display: none;
        }
        
        .online-users {
            padding: 10px 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <div class="room-info">
                <h1>💬 ' . htmlspecialchars($roomResult['name']) . '</h1>
                <div class="user-info">👤 ' . htmlspecialchars($username) . '</div>
            </div>
            <a href="chatrooms.php" class="back-btn">← Back to Rooms</a>
        </div>
        
        <div class="online-users">
            Room ID: ' . $room_id . ' | Members: ' . $roomResult['user_count'] . '
        </div>
        
        <div id="messages">
            <div style="text-align: center; padding: 20px; color: #888;">
                Loading messages...
            </div>
        </div>
        
        <div class="chat-input-container">
            <form id="sendForm">
                <input type="text" id="msgInput" placeholder="Type your message here..." required autocomplete="off">
                <button type="submit">Send</button>
            </form>
        </div>
    </div>

    <script>
        const roomId = ' . $room_id . ';
        const userId = ' . $user_id . ';
        const username = "' . addslashes($username) . '";
        
        loadMessages();
        
        setInterval(loadMessages, 1500);
        
        function scrollToBottom() {
            const messagesDiv = document.getElementById("messages");
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }
        
        function loadMessages() {
            fetch("load_messages.php?room_id=" + roomId)
                .then(response => {
                    if (!response.ok) throw new Error("Network response was not ok");
                    return response.text();
                })
                .then(html => {
                    const messagesDiv = document.getElementById("messages");
                    messagesDiv.innerHTML = html;
                    
                    const messages = messagesDiv.querySelectorAll(".message");
                    messages.forEach(msg => {
                        const usernameElement = msg.querySelector(".username");
                        if (usernameElement) {
                            const msgUsername = usernameElement.textContent.trim();
                            if (msgUsername === username) {
                                msg.classList.add("sent");
                                msg.classList.remove("received");
                            } else {
                                msg.classList.add("received");
                                msg.classList.remove("sent");
                            }
                        }
                    });
                    
                    scrollToBottom();
                })
                .catch(error => {
                    console.error("Error loading messages:", error);
                    document.getElementById("messages").innerHTML = 
                        "<div style=\"text-align: center; padding: 20px; color: #ff4444;\">Error loading messages. Please refresh.</div>";
                });
        }
        
        document.getElementById("sendForm").addEventListener("submit", function(e) {
            e.preventDefault();
            
            const msgInput = document.getElementById("msgInput");
            const message = msgInput.value.trim();
            
            if (!message) return;
            
            msgInput.disabled = true;
            const submitBtn = this.querySelector("button");
            submitBtn.disabled = true;
            submitBtn.textContent = "Sending...";
            
            const formData = new URLSearchParams();
            formData.append("room_id", roomId);
            formData.append("user_id", userId);
            formData.append("message", message);
            
            fetch("send_message.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error("Failed to send message");
                
                msgInput.value = "";
                
                loadMessages();
            })
            .catch(error => {
                console.error("Error sending message:", error);
                alert("Failed to send message. Please try again.");
            })
            .finally(() => {
                msgInput.disabled = false;
                submitBtn.disabled = false;
                submitBtn.textContent = "Send";
                msgInput.focus();
            });
        });
        
        document.getElementById("msgInput").focus();
        
        document.getElementById("msgInput").addEventListener("keydown", function(e) {
            if (e.key === "Enter" && !e.shiftKey) {
                e.preventDefault();
                document.getElementById("sendForm").dispatchEvent(new Event("submit"));
            }
        });
    </script>
</body>
</html>';
?>