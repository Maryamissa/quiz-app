<?php
$host = 'localhost';
$dbname = 'quiz-app';
$user = 'root';
$pass = '';
$port = 3315;

$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) die('Connection failed: ' . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_delete'])) {
    $quiz_id = (int)$_POST['quiz_id'];

    $stmt = $conn->prepare("DELETE FROM attempts WHERE quiz_id = ?");
    $stmt->bind_param('i', $quiz_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM options WHERE question_id IN (SELECT id FROM questions WHERE quiz_id = ?)");
    $stmt->bind_param('i', $quiz_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM questions WHERE quiz_id = ?");
    $stmt->bind_param('i', $quiz_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
    $stmt->bind_param('i', $quiz_id);
    $stmt->execute();

    echo 'OK';
    exit;
}

$categories = [];
$res = $conn->query("SELECT id, name FROM categories ORDER BY name");
while ($r = $res->fetch_assoc()) $categories[] = $r;

$quizzes_by_cat = [];
$res2 = $conn->query("SELECT id, title, category_id FROM quizzes ORDER BY title");
while ($r = $res2->fetch_assoc()) $quizzes_by_cat[$r['category_id']][] = $r;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quiz_id']) && !isset($_POST['ajax_delete'])) {
    $quiz_id = (int)$_POST['quiz_id'];
    $questions = [];

    $stmt = $conn->prepare("SELECT id, question_text FROM questions WHERE quiz_id = ?");
    $stmt->bind_param('i', $quiz_id);
    $stmt->execute();
    $resq = $stmt->get_result();
    while ($qq = $resq->fetch_assoc()) $questions[] = $qq;

    if (isset($_POST['answers'])) {
        $score = 0;
        foreach ($_POST['answers'] as $optid) {
            $optid = (int)$optid;
            $q = $conn->prepare("SELECT is_correct FROM options WHERE id = ?");
            $q->bind_param('i', $optid);
            $q->execute();
            $r = $q->get_result()->fetch_assoc();
            if ($r && $r['is_correct']) $score++;
        }

        $user_id = 1;
        $conn->prepare("INSERT INTO attempts (user_id, quiz_id, score) VALUES ($user_id,$quiz_id,$score)")->execute();
        $conn->prepare("UPDATE quizzes SET times_taken = times_taken + 1 WHERE id = $quiz_id")->execute();

        echo "<div class='notice'>You scored: <strong>$score</strong></div>";
    }
}

echo '<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Quiz Taker</title>
<link rel="stylesheet" href="../css/quizTaker.css">
</head>
<body>
<div class="mainBox">
<header class="header">
<div class="logo-container">
<a href="explore.php"><img src="../images/logo.png" style="width:180px;height:120px;margin-left:20px;"></a>
</div>
<nav class="nav-links">
<a href="explore.php">Explore</a>
<a href="quiztaker.php" style="background:rgb(20,20,235);color:white;">Quiz Taker</a>
<a href="quizmaker.php">Quiz Maker</a>
<a href="chatrooms.php">Chatrooms</a>
<a href="profile.php">Profile</a>
</nav>
</header>
<main>';

foreach ($categories as $category) {
    echo '<section>
    <h2>' . htmlspecialchars($category['name']) . '</h2>
    <ul>';

    if (isset($quizzes_by_cat[$category['id']])) {
        foreach ($quizzes_by_cat[$category['id']] as $q) {
            echo '<li id="quiz-' . (int)$q['id'] . '">
            <form method="post" style="display:inline">
            <input type="hidden" name="quiz_id" value="' . (int)$q['id'] . '">
            <button type="submit">Take: ' . htmlspecialchars($q['title']) . '</button>
            </form>

            <button onclick="deleteQuiz(' . (int)$q['id'] . ')" 
            style="margin-left:10px;background:#d32f2f;color:white;border:none;padding:6px 12px;border-radius:4px;cursor:pointer;">
            Delete
            </button>
            </li>';
        }
    } else {
        echo '<li>No quizzes</li>';
    }

    echo '</ul></section>';
}

if (isset($questions) && !empty($questions)) {
    echo '<section>
    <h2>Quiz Questions</h2>
    <form method="post">
    <input type="hidden" name="quiz_id" value="' . (int)$quiz_id . '">';

    foreach ($questions as $i => $q) {
        $opts = $conn->query("SELECT id, option_text FROM options WHERE question_id=" . (int)$q['id']);
        echo '<div class="question">
        <p><strong>' . ($i + 1) . '. ' . htmlspecialchars($q['question_text']) . '</strong></p>';
        while ($o = $opts->fetch_assoc()) {
            echo '<label><input type="radio" name="answers[' . (int)$q['id'] . ']" value="' . (int)$o['id'] . '"> ' . htmlspecialchars($o['option_text']) . '</label><br>';
        }
        echo '</div>';
    }

    echo '<button type="submit">Submit Answers</button>
    </form></section>';
}

echo '</main>
<footer><img src="../images/logo.png" style="width:170px;height:95px;margin-top:-10px;"></footer>
</div>

<script>
function deleteQuiz(id){
    if(!confirm("Delete this quiz?")) return;
    fetch("", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "ajax_delete=1&quiz_id=" + id
    }).then(r => r.text()).then(t => {
        if(t.trim() === "OK"){
            document.getElementById("quiz-" + id).remove();
        }
    });
}
</script>

</body>
</html>';
?>
