<?php
$email = $_POST['email'] ?? '';
$review = $_POST['review'] ?? '';
$rating = $_POST['rating'] ?? '';

if(empty($email) || empty($review) || empty($rating)){
    echo "<script>alert('All fields are required!'); window.history.back();</script>";
    exit;
}

$host = 'localhost';
$dbname = 'quiz-app';
$user = 'root';
$pass = '';
$port = 3315;

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if($conn->connect_error){
    die("<script>alert('Database connection failed: {$conn->connect_error}'); window.history.back();</script>");
}

$rating=(int)$rating;
if($rating < 1 || $rating > 5){
    echo "<script>alert('Rating must be between 1 and 5'); window.history.back();</script>";
    exit;
}

$stmt = $conn->prepare("INSERT INTO reviews (email, reviews, rate) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $email, $review, $rating);


if($stmt->execute()){
    header("Location: welcome.php?msg=thankyou");
    exit;
} else {
    echo "<script>alert('Failed to submit review: {$stmt->error}'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
