<?php
// Enable error reporting for debugging
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

// Validate inputs
if (!$room_id || !$username) {
    die("Error: Room ID and Username are required. <a href='chatrooms.php'>Go back</a>");
}

// Get user info
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

// Get room info
$room = $conn->prepare("SELECT * FROM chatrooms WHERE id = ? LIMIT 1");
$room->bind_param('i', $room_id);
$room->execute();
$roomResult = $room->get_result()->fetch_assoc();

if (!$roomResult) {
    die("Room not found. <a href='chatrooms.php'>Go back</a>");
}

// Ensure user is in chatroom
$check_member = $conn->prepare("SELECT id FROM chatroom_users WHERE chatroom_id = ? AND user_id = ?");
$check_member->bind_param('ii', $room_id, $user_id);
$check_member->execute();
if (!$check_member->get_result()->fetch_assoc()) {
    // Add user to chatroom
    $join = $conn->prepare("INSERT INTO chatroom_users (chatroom_id, user_id) VALUES (?, ?)");
    $join->bind_param('ii', $room_id, $user_id);
    $join->execute();
    
    // Update user count
    $upd = $conn->prepare("UPDATE chatrooms SET user_count = user_count + 1 WHERE id = ?");
    $upd->bind_param('i', $room_id);
    $upd->execute();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room: <?php echo htmlspecialchars($roomResult['name']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: linear-gradient(to right, #4facfe 0%, #00f2fe 100%);
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
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
        
        /* Scrollbar styling */
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
                <h1>💬 <?php echo htmlspecialchars($roomResult['name']); ?></h1>
                <div class="user-info">👤 <?php echo htmlspecialchars($username); ?></div>
            </div>
            <a href="chatrooms.php" class="back-btn">← Back to Rooms</a>
        </div>
        
        <div class="online-users">
            Room ID: <?php echo $room_id; ?> | Members: <?php echo $roomResult['user_count']; ?>
        </div>
        
        <div id="messages">
            <!-- Messages will be loaded here -->
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
        const roomId = <?php echo $room_id; ?>;
        const userId = <?php echo $user_id; ?>;
        const username = "<?php echo addslashes($username); ?>";
        
        // Load messages immediately
        loadMessages();
        
        // Auto-refresh every 1.5 seconds
        setInterval(loadMessages, 1500);
        
        // Auto-scroll to bottom
        function scrollToBottom() {
            const messagesDiv = document.getElementById('messages');
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }
        
        // Load messages from server
        function loadMessages() {
            fetch(`load_messages.php?room_id=${roomId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.text();
                })
                .then(html => {
                    const messagesDiv = document.getElementById('messages');
                    messagesDiv.innerHTML = html;
                    
                    // Add message classes based on sender
                    const messages = messagesDiv.querySelectorAll('.message');
                    messages.forEach(msg => {
                        const usernameElement = msg.querySelector('.username');
                        if (usernameElement) {
                            const msgUsername = usernameElement.textContent.trim();
                            if (msgUsername === username) {
                                msg.classList.add('sent');
                                msg.classList.remove('received');
                            } else {
                                msg.classList.add('received');
                                msg.classList.remove('sent');
                            }
                        }
                    });
                    
                    scrollToBottom();
                })
                .catch(error => {
                    console.error('Error loading messages:', error);
                    document.getElementById('messages').innerHTML = 
                        '<div style="text-align: center; padding: 20px; color: #ff4444;">Error loading messages. Please refresh.</div>';
                });
        }
        
        // Send message
        document.getElementById('sendForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const msgInput = document.getElementById('msgInput');
            const message = msgInput.value.trim();
            
            if (!message) return;
            
            // Disable input and show loading
            msgInput.disabled = true;
            const submitBtn = this.querySelector('button');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';
            
            // Create form data
            const formData = new URLSearchParams();
            formData.append('room_id', roomId);
            formData.append('user_id', userId);
            formData.append('message', message);
            
            // Send to server
            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Failed to send message');
                
                // Clear input
                msgInput.value = '';
                
                // Reload messages
                loadMessages();
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert('Failed to send message. Please try again.');
            })
            .finally(() => {
                // Re-enable input
                msgInput.disabled = false;
                submitBtn.disabled = false;
                submitBtn.textContent = 'Send';
                msgInput.focus();
            });
        });
        
        // Focus input on page load
        document.getElementById('msgInput').focus();
        
        // Send message on Enter (but allow Shift+Enter for new line)
        document.getElementById('msgInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('sendForm').dispatchEvent(new Event('submit'));
            }
        });
    </script>
</body>
</html>