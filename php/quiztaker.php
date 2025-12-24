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
while ($r = $res->fetch_assoc()) $categories[] = $r; //array takes categories

$quizzes_by_cat = [];
$res2 = $conn->query("SELECT id, title, category_id FROM quizzes ORDER BY title"); 
while ($r = $res2->fetch_assoc()) $quizzes_by_cat[$r['category_id']][] = $r; // grouping quizzes by relevent category

// FIXED: removed isset($_POST['take_quiz']) because submit answers does not contain it
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // <--- FIX HERE so times_taken can update
    $quiz_id = (int)$_POST['quiz_id']; 
    $questions = [];
    
    $stmt = $conn->prepare("SELECT id, question_text FROM questions WHERE quiz_id = ?"); 
    $stmt->bind_param('i', $quiz_id); 
    $stmt->execute();
    $resq = $stmt->get_result();
    while ($qq = $resq->fetch_assoc()) $questions[] = $qq; //array takes qstions

    if (isset($_POST['answers'])) {
        $answers = $_POST['answers']; 
        $score = 0; 
        
        foreach ($answers as $qid => $optid) {
            $optid = (int)$optid; 
            $q = $conn->prepare("SELECT is_correct FROM options WHERE id = ? LIMIT 1"); 
            $q->bind_param('i', $optid);
            $q->execute();
            $rr = $q->get_result()->fetch_assoc();
            
            if ($rr && $rr['is_correct']) $score++; 
        }
        
        $user_id = 1; 
        $ins = $conn->prepare("INSERT INTO attempts (user_id, quiz_id, score) VALUES (?, ?, ?)"); 
        $ins->bind_param('iii', $user_id, $quiz_id, $score);
        $ins->execute();
        
        $upd = $conn->prepare("UPDATE quizzes SET times_taken = times_taken + 1 WHERE id = ?"); // now this finally runs!
        $upd->bind_param('i', $quiz_id);
        $upd->execute();
        
        echo "<div class='notice'>You scored: <strong>" . intval($score) . "</strong></div>"; 
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
                <a href="explore.php"><img style="width: 130px;height: 110px;margin-left: 20px;" src="../images/logo.png" alt="Logo" class="logo"></a>
            </div>
            <nav class="nav-links">
                <a href="explore.php">Explore</a>
                <a href="quiztaker.php"style="background: rgb(20, 20, 235); color: white;">Quiz Taker</a>
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
            echo '<li>
                <form method="post" style="display:inline">
                    <input type="hidden" name="quiz_id" value="' . (int)$q['id'] . '"> <!-- sneaky hidden input -->
                    <button type="submit" name="take_quiz">Take: ' . htmlspecialchars($q['title']) . '</button> <!-- take quiz button -->
                </form>
            </li>';
        }
    } else {
        echo '<li>No quizzes</li>'; 
    }
    
    echo '</ul>
    </section>';
}

if (isset($questions) && !empty($questions)) {
    echo '<section>
        <h2>Quiz Questions</h2>
        <form method="post">
            <input type="hidden" name="quiz_id" value="' . (int)$quiz_id . '">'; 

    foreach ($questions as $idx => $q) {
        $opts = $conn->query("SELECT id, option_text FROM options WHERE question_id = " . (int)$q['id']); 
        echo '<div class="question">
            <p><strong>' . ($idx + 1) . '. ' . htmlspecialchars($q['question_text']) . '</strong></p>'; 
        
        while ($o = $opts->fetch_assoc()) {
            echo '<label>
                <input type="radio" name="answers[' . (int)$q['id'] . ']" value="' . (int)$o['id'] . '"> ' . htmlspecialchars($o['option_text']) . ' <!-- each option is a small warrior -->
            </label><br>';
        }
        
        echo '</div>';
    }
    
    echo '<button type="submit">Submit Answers</button> <!-- final boss button -->
        </form>
    </section>';
}

echo '</main>
    <footer class="footer">
    <footer>
                <div class="footer">
                    <div class="bottomLeft"><img src="../images/logo.png" style="width: 170px;height:95px;margin-top:-10px;"></div>
                </div>
            </footer>
</div>
</body>
</html>';
?>
