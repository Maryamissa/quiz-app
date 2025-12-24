<?php
$host = 'localhost';
$dbname = 'quiz-app';
$user = 'root';
$pass = '';
$port = 3315;

$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) die('Connection failed: ' . $conn->connect_error);

$user_id = 1;
$user = $conn->query("SELECT id, username, email, avatar FROM users WHERE id = " . $user_id)->fetch_assoc();

$score_row = $conn->query("SELECT SUM(score) AS total_score, COUNT(*) AS attempts FROM attempts WHERE user_id = " . $user_id)->fetch_assoc();
$total_score = (int)($score_row['total_score'] ?? 0);
$attempts = (int)($score_row['attempts'] ?? 0);
$level = 1 + floor($total_score / 10);

$rooms = [];
$r = $conn->prepare("SELECT c.id, c.name FROM chatrooms c JOIN chatroom_users cu ON c.id = cu.chatroom_id WHERE cu.user_id = ?");
$r->bind_param('i', $user_id);
$r->execute();
$resr = $r->get_result();
while ($row = $resr->fetch_assoc()) $rooms[] = $row;

echo '<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profile</title>
    <link rel="stylesheet" href="../css/Profile.css">
</head>
<body>
<div class="mainBox">
   <header class="header">
            <div class="logo-container">
                <a href="welcome.php"><img style="width: 130px;height: 110px;margin-left: 20px;" src="../images/logo.png" alt="Logo" class="logo"></a>
            </div>
            <nav class="nav-links">
                <a href="explore.php">Explore</a>
                <a href="quiztaker.php">Quiz Taker</a>
                <a href="quizmaker.php" >Quiz Maker</a>
                <a href="chatrooms.php">Chatrooms</a>
                <a href="profile.php"style="background: rgb(20, 20, 235); color: white;">Profile</a>
            </nav>
        </header>
    <main>
        <div class="profileCard">
            <img src="../images/profile.png" alt="avatar">
            <h1>' . htmlspecialchars($user['username']) . '</h1>
            <p>Email: ' . htmlspecialchars($user['email']) . '</p>
            <p>Total score: ' . $total_score . '</p>
            <p>Level: ' . $level . '</p>
            <h2>Rooms</h2>
            <ul>';

foreach ($rooms as $rm) {
    echo '<li>' . htmlspecialchars($rm['name']) . '</li>';
}

echo '</ul>
    <form method="post" action="logOut.php">
        <button type="submit" class="logOut">Log Out</button>
    </form>
    
        </div>
    </main>
    <footer>
                <div class="footer">
                    <div class="bottomLeft"><img src="../images/logo.png" style="width: 170px;height:95px;margin-top:-10px;"></div>
                </div>
            </footer>
</div>
</body>
</html>';
?>
