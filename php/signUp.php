<?php
    echo'
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="../css/signUp.css">
            <title>SignUp</title>
        </head>
        <body>
            <div class="page">
                 <div class="page">
                <header>
                    <div><a href="welcome.php"><img style="width: 180px;height: 120px;margin-left: 20px;" src="../images/logoSign.png"></a></div>
                    <nav>
                        <a href="welcome.php">Home</a>
                        <a href="contactUs.php">Contact</a>
                        <a href="reviewUs.php">Review</a>
                    </nav>
                </header>
                <div class="container">
                    <div class="title">Sign Up</div>
                    <div class="subtitle">Already a member? <a href="signIn.php">signin</a></div>
                    
                    <form method="post" action="signUpAction.php">
                        <input class="input" type="text" placeholder="Username" name="username" required><br>
                        <input class="input" type="email" placeholder="Email" name="email" required><br>
                        <input class="input" type="password" placeholder="Password" name="password" required><br>
                        <button type="submit" class="signupBtn">Sign Up</button>
                    </form>
                </div>
            </div>            
        </body>
        </html>
    ';
?>