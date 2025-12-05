<?php
    echo'
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="../css/contactUs.css">
            <title>SignUp</title>
        </head>
        <body>
            <div class="page">
                <header>
                    <div><img style="width: 130px;height: 110px;margin-left: 20px;" src="../images/logoSign.png"></div>
                    <nav>
                        <a href="welcome.php">Home</a>
                        <a href="aboutUs.php">About</a>
                    </nav>
                </header>
                <div class="container">
                    <div class="left">
                        <div  class="left-title">
                            <div>Send a message</div>
                            <div><img style="width: 35px;height: 35px; " src="../images/mail.png"></div>
                        </div>
                        
                        <form method="post" action="signUpAction.php">
                            <input class="input" type="text" placeholder="First name" name="firstname" required>
                            <input class="input" type="text" placeholder="Last name" name="lastname" required><br>
                            <input class="input" type="text" placeholder="Phone" name="phone" required>
                            <input class="input" type="email" placeholder="Email" name="email" required><br>
                            <div class="left-subtitle">How can we help you?</div>
                            <textarea style="width: 230px;height: 80px;"></textarea><br>
                            <button type="submit" class="submitBtn"><img style="width: 25px;height: 20px;margin-left: -45px;margin-right: 15px;margin-top: 8px;" src="../images/send.png"><p style="margin-top: -22px;margin-right: -25px;">Submit</p></button>
                        </form>
                    </div>

                    <div class="right">
                        <div class="right-title">Contact Info</div>
                        <div class="phone">
                            <img style="width: 28px;height: 28px;margin-right: 12px;margin-left: 10px;" src="../images/phone.png">
                            +961 70 529 471
                        </div>
                        <div class="email">
                            <img style="width: 25px;height: 28px;margin-right: 15px;margin-left: 10px;" src="../images/email .png">
                            ThinkByte@outlook.com
                        </div>
                        
                    </div>
                    
                </div>
            </div>            
        </body>
        </html>
    ';
?>