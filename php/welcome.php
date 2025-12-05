<?php
echo'
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/welcome.css">
        <title>ThinkByte</title>
    </head>
    <body>
        <div class="mainBox"> 

            <div class="topSide">
                <div class="topLeft"><img class="logo" src="../images/logo.png"></div>
                <div class="topRight"><button class="signin">Sign in</button></div>
            </div>

            <div class="middleSide">
                <div class="middleLeft">
                    <div class="title">Think Byte</div>
                    <div class="subtitle">A sleek, interactive website where programmers<br> test and 
                        improve their coding skills through timed quizzes,<br> 
                        practical challenges, and competitive programming.
                    </div>
                    <div><button class="start">Start Learning</button></div>
                </div>
                <div class="middleRight"><img src="../images/welcomePage.png" style="width: 650px; height: 444px;"></div>
            </div>

            <hr>
            <footer>
                <div class="footer">
                    <div class="bottomLeft"><img src="../images/logo.png" style="width: 170px;height:95px;margin-top:-10px;"></div>
                    <div class="bottomRight">
                        <a href="aboutUs.php">About us&nbsp;&nbsp;</a>
                        <span class="separator">|</span>
                        <a href="contactUs.php">&nbsp;&nbsp;Contact us&nbsp;&nbsp;</a>
                        <span class="separator">|</span>
                        <a href="reviewUs.php">&nbsp;&nbsp;Review us</a>
                    </div>
                </div>
            </footer>

        </div>
    </body>
    </html>
    ';
?>