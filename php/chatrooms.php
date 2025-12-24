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


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_room'])) {
    $name = trim($_POST['name']);
    $created_by = 1; 
    
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO chatrooms (name, created_by, user_count) VALUES (?, ?, 1)");
        $stmt->bind_param('si', $name, $created_by);
        
        if ($stmt->execute()) {
            $room_id = $stmt->insert_id;
            
            $join_stmt = $conn->prepare("INSERT INTO chatroom_users (chatroom_id, user_id) VALUES (?, ?)");
            $join_stmt->bind_param('ii', $room_id, $created_by);
            $join_stmt->execute();
            $join_stmt->close();
            
            $success = "Room '$name' created successfully!";
        } else {
            $error = "Failed to create room: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Room name cannot be empty!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_room'])) {
    $room_id = (int)$_POST['room_id'];
    $username = trim($_POST['username']);
    
    if ($room_id > 0 && !empty($username)) {
        
        $u = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
        $u->bind_param('s', $username);
        $u->execute();
        $userResult = $u->get_result();
        
        if ($userResult->num_rows > 0) {
            $userRow = $userResult->fetch_assoc();
            $user_id = $userRow['id'];
            
            
            $check = $conn->prepare("SELECT id FROM chatroom_users WHERE chatroom_id = ? AND user_id = ?");
            $check->bind_param('ii', $room_id, $user_id);
            $check->execute();
            $checkResult = $check->get_result();
            
            if ($checkResult->num_rows === 0) {
                $join = $conn->prepare("INSERT INTO chatroom_users (chatroom_id, user_id) VALUES (?, ?)");
                $join->bind_param('ii', $room_id, $user_id);
                
                if ($join->execute()) {
                    $upd = $conn->prepare("UPDATE chatrooms SET user_count = user_count + 1 WHERE id = ?");
                    $upd->bind_param('i', $room_id);
                    $upd->execute();
                    $upd->close();
                    
                    // go to chatroom by get
                    header("Location: chatroom.php?room_id=$room_id&username=" . urlencode($username));
                    exit;
                }
                $join->close();
            } else {
                
                header("Location: chatroom.php?room_id=$room_id&username=" . urlencode($username));
                exit;
            }
        } else {
            $error = "User '$username' not found!";
        }
        $u->close();
    } else {
        $error = "Please provide both room ID and username!";
    }
}

// Get rooms
$rooms = [];
$res = $conn->query("
    SELECT 
        c.id, 
        c.name, 
        c.user_count, 
        c.created_at,
        u.username as creator_name
    FROM chatrooms c
    LEFT JOIN users u ON c.created_by = u.id
    ORDER BY c.created_at DESC
");

while ($r = $res->fetch_assoc()) {
    $rooms[] = $r;
}

// Get all users for dropselect
$users = [];
$users_res = $conn->query("SELECT username FROM users ORDER BY username");
while ($u = $users_res->fetch_assoc()) {
    $users[] = $u['username'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/chatrooms.css">
    <title>Chatrooms</title>
    <style>
      
    </style>
</head>
<body>
    <div class="main-container">
        <header class="header">
            <div class="logo-container">
                <img style="width: 130px;height: 110px;margin-left: 20px;" src="../images/logo.png" alt="Logo" class="logo">
            </div>
            <nav class="nav-links">
                <a href="explore.php">Explore</a>
                <a href="quiztaker.php">Quiz Taker</a>
                <a href="quizmaker.php">Quiz Maker</a>
                <a href="chatrooms.php" style="background: #667eea; color: white;">Chatrooms</a>
                <a href="profile.php">Profile</a>
            </nav>
        </header>
        
        <main>
            <h1 style="color: white; text-align: center; margin-bottom: 30px; font-size: 2.5rem;">💬 Chat Rooms</h1>
            
            <?php if (isset($success)): ?>
                <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="main-content">
                <section class="section">
                    <h2>➕ Create New Room</h2>
                    <form method="post">
                        <div class="form-group">
                            <label for="room_name">Room Name</label>
                            <input type="text" id="room_name" name="name" required 
                                   placeholder="Enter room name (e.g., AI Discussions)">
                        </div>
                        <button type="submit" name="create_room" class="btn">Create Room</button>
                    </form>
                </section>
                
                <section class="section">
                    <h2>📢 Available Rooms (<?php echo count($rooms); ?>)</h2>
                    
                    <?php if (empty($rooms)): ?>
                        <p style="text-align: center; color: #666; padding: 20px;">
                            No chat rooms available. Create the first one!
                        </p>
                    <?php else: ?>
                        <ul class="room-list">
                            <?php foreach ($rooms as $room): ?>
                                <li class="room-item">
                                    <div class="room-info">
                                        <h3>#<?php echo htmlspecialchars($room['name']); ?></h3>
                                        <div class="room-meta">
                                            Created by <?php echo htmlspecialchars($room['creator_name'] ?? 'Unknown'); ?> •
                                            <?php echo date('M d, Y', strtotime($room['created_at'])); ?>
                                            <span class="user-count">👥 <?php echo (int)$room['user_count']; ?> members</span>
                                        </div>
                                        
                                        <form method="post" class="join-form">
                                            <input type="hidden" name="room_id" value="<?php echo (int)$room['id']; ?>">
                                            <select name="username" required>
                                                <option value="">Select your username</option>
                                                <?php foreach ($users as $user): ?>
                                                    <option value="<?php echo htmlspecialchars($user); ?>">
                                                        <?php echo htmlspecialchars($user); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" name="join_room">Join</button>
                                        </form>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>
            </div>
        </main>
        
        <footer class="footer">
            <p>© <?php echo date('Y'); ?> thanks for visiting!</p>
            <p style="margin-top: 10px; font-size: 0.9em;"></p>
        </footer>
    </div>
    
    <script>
        // Auto-focus on room name input
        document.getElementById('room_name')?.focus();
        
        // Show confirmation before creating room
        document.querySelector('button[name="create_room"]')?.addEventListener('click', function(e) {
            const roomName = document.getElementById('room_name').value.trim();
            if (!roomName) {
                e.preventDefault();
                alert('Please enter a room name!');
                document.getElementById('room_name').focus();
            }
        });
        
        // Smooth scroll for messages
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>