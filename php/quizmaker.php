<?php
$host = 'localhost';
$dbname = 'quiz-app';
$user = 'root';
$pass = '';
$port = 3315;

$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) die('Connection failed: ' . $conn->connect_error);

$categories = [];
$res = $conn->query("SELECT id, name FROM categories ORDER BY name");
while ($r = $res->fetch_assoc()) $categories[] = $r;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == '1') {
    $title = trim($_POST['title']);
    $category_id = (int)$_POST['category_id'];
    $n = max(1, min(50, (int)$_POST['n_questions']));
    $step = 2;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == '2') {
    $title = trim($_POST['title']);
    $category_id = (int)$_POST['category_id'];
    $n = (int)$_POST['n_questions'];
    $user_id = 1;

    $ins = $conn->prepare("INSERT INTO quizzes (title, category_id, created_by) VALUES (?,?,?)");
    $ins->bind_param('sii', $title, $category_id, $user_id);
    $ins->execute();
    $quiz_id = $ins->insert_id;

    for ($i = 1; $i <= $n; $i++) {
        $qtext = trim($_POST["q_$i"] ?? '');
        if ($qtext === '') continue;

        $insq = $conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?,?)");
        $insq->bind_param('is', $quiz_id, $qtext);
        $insq->execute();
        $qid = $insq->insert_id;

        for ($j = 1; $j <= 4; $j++) {
            $opt = trim($_POST["q_{$i}_opt_$j"] ?? '');
            if ($opt === '') continue;

            $is_correct = (isset($_POST["q_{$i}_correct"]) && $_POST["q_{$i}_correct"] == "opt_$j") ? 1 : 0;
            $inso = $conn->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?,?,?)");
            $inso->bind_param('isi', $qid, $opt, $is_correct);
            $inso->execute();
        }
    }

    echo "<div class='notice'>Quiz created successfully.</div>";
}

echo '<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quiz Maker</title>
    <link rel="stylesheet" href="../css/QuizMaker.css">
    <script src="js/quizmaker.js" defer></script>
</head>
<body>
<div class="mainBox">
  <header class="header">
            <div class="logo-container">
                <a href="explore.php"><img style="width: 180px;height: 120px;margin-left: 20px;" src="../images/logo.png" alt="Logo" class="logo"></a>
            </div>
            <nav class="nav-links">
                <a href="explore.php">Explore</a>
                <a href="quiztaker.php">Quiz Taker</a>
                <a href="quizmaker.php" style="background: rgb(20, 20, 235); color: white;">Quiz Maker</a>
                <a href="chatrooms.php">Chatrooms</a>
                <a href="profile.php">Profile</a>
            </nav>
    </header>
    <main>
        <h1>Create a Quiz</h1>';

    if (empty($step) || $step == 1) {
    echo '<form method="post">
        <input type="hidden" name="step" value="1">
        <label>Title: <input name="title" required></label><br>
        <label>Category: <select name="category_id">';
    foreach ($categories as $c) {
        echo '<option value="' . (int)$c['id'] . '">' . htmlspecialchars($c['name']) . '</option>';
    }
    echo '</select></label><br>
        <label>Number of questions: <input name="n_questions" type="number" value="5" min="1" max="50"></label><br>
        <button type="submit">Next</button>
    </form>';
}

if (!empty($step) && $step == 2) {
    echo '<form method="post">
        <input type="hidden" name="step" value="2">
        <input type="hidden" name="title" value="' . htmlspecialchars($title) . '">
        <input type="hidden" name="category_id" value="' . (int)$category_id . '">
        <input type="hidden" name="n_questions" value="' . (int)$n . '">
        <h2>Quiz: ' . htmlspecialchars($title) . '</h2>';

    for ($i = 1; $i <= $n; $i++) {
        echo '<fieldset>
            <legend>Question ' . $i . '</legend>
            <label>Question text:<br><textarea name="q_' . $i . '" required></textarea></label><br>';
        for ($j = 1; $j <= 4; $j++) {
            echo '<label>Option ' . $j . ': <input name="q_' . $i . '_opt_' . $j . '" required></label><br>';
        }
        echo '<label>Correct option:
            <select name="q_' . $i . '_correct">
                <option value="opt_1">Option 1</option>
                <option value="opt_2">Option 2</option>
                <option value="opt_3">Option 3</option>
                <option value="opt_4">Option 4</option>
            </select>
        </label>
        </fieldset>';
    }

    echo '<button type="submit">Finish & Create Quiz</button>
    </form>';
}

echo '</main>
   <footer>
                <div class="footer">
                    <div class="bottomLeft"><img src="../images/logo.png" style="width: 170px;height:95px;margin-top:-10px;"></div>
                </div>
            </footer>
</div>
</body>
</html>';
?>
