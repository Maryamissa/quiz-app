<?php
$host = 'localhost';
$dbname = 'quiz-app';
$user = 'root';
$pass = '';
$port = 3315;

$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$quizzes = [];
$sql = "SELECT q.id, q.title, q.times_taken, c.name AS category FROM quizzes q JOIN categories c ON q.category_id=c.id ORDER BY q.times_taken DESC LIMIT 10";
$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) $quizzes[] = $row;
}

$rooms = [];
$sql2 = "SELECT id, name, user_count FROM chatrooms ORDER BY user_count DESC LIMIT 10";
$res2 = $conn->query($sql2);
if ($res2) {
    while ($row = $res2->fetch_assoc()) $rooms[] = $row;
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Explore</title>
<link rel="stylesheet" href="../css/Exploree.css">
</head>
<body>
<div class="mainBox">
 <header class="header">
            <div class="logo-container">
                <img style="width: 180px;height: 120px;margin-left: 20px;" src="../images/logo.png" alt="Logo" class="logo">
            </div>
            <nav class="nav-links">
                <a href="explore.php"style="background: rgb(20, 20, 235); color: white;">Explore</a>
                <a href="quiztaker.php">Quiz Taker</a>
                <a href="quizmaker.php">Quiz Maker</a>
                <a href="chatrooms.php">Chatrooms</a>
                <a href="profile.php">Profile</a>
            </nav>
        </header>
<main class="middle1">
<section class="middleLeft">
<h2 class="title">Top Quizzes</h2>
<ul>

<?php foreach ($quizzes as $q): ?>
    <li>
        <form method="post" action="quiztaker.php">
            <input type="hidden" name="quiz_id" value="<?php echo (int)$q['id']; ?>">
            <button type="submit" name="take_quiz"><?php echo htmlspecialchars($q['title']); ?></button>
        </form>
        <span class="stats"><?php echo (int)$q['times_taken']; ?> plays<br>(<?php echo htmlspecialchars($q['category']); ?>)</span>
    </li>
<?php endforeach; ?>

</ul>
</section>


<section class="middleLeft">
<h2 class="title">Featured Chatrooms</h2>
<ul>

<?php foreach ($rooms as $r): ?>
    <li>
        <form method="post" action="chatrooms.php">
            <input type="hidden" name="room_id" value="<?php echo (int)$r['id']; ?>">
            <button type="submit" class="signin"><?php echo htmlspecialchars($r['name']); ?></button>
        </form>
        <span class="stats"><?php echo (int)$r['user_count']; ?> users</span>
    </li>
<?php endforeach; ?>

</ul>
</section>
</main>
<footer>
                <div class="footer">
                    <div class="bottomLeft"><img src="../images/logo.png" style="width: 170px;height:95px;margin-top:-10px;"></div>
                </div>
            </footer>
</div>
</body>
</html>