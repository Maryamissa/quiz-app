<?php
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if(empty($email) || empty($password)){
    echo "<script>alert('Email and Password are required!'); window.history.back();</script>";
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

$hashed_password = md5($password);

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
$stmt->bind_param("ss", $email, $hashed_password);
$stmt->execute();
$stmt->store_result();

if( $hashed_password = $password ){
    header("Location: explore.php");
    exit;
} else {
    echo "<script>alert('Login failed: Incorrect email or password'); window.history.back();</script>";
}

$conn->close();
?>
