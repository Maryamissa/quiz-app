<?php



$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';


if(empty($username) || empty($email) || empty($password)){
    echo "<script>alert('All fields are required!'); window.history.back();</script>";
    exit;
}


$hashed_password = password_hash($password, PASSWORD_DEFAULT);


$host = 'localhost';
$dbname = 'quiz-app';
$user = 'root';       
$pass = '';           
$port = 3315;

$conn = new mysqli($host, $user, $pass, $dbname, $port);


if($conn->connect_error){
    die("<script>alert('Database connection failed: {$conn->connect_error}'); window.history.back();</script>");
}


$stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $hashed_password);


if($stmt->execute()){
   
    header("Location: signIn.php");
    exit;
} else {
  
    echo "<script>alert('Sign up failed: {$stmt->error}'); window.history.back();</script>";
}


$stmt->close();
$conn->close();
?>
