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
                
                <div class="topRight">       
                        <a href="#aboutUs">About us&nbsp;&nbsp;</a>
                        <a href="contactUs.php">&nbsp;&nbsp;Contact us&nbsp;&nbsp;</a>
                        <a href="reviewUs.php">&nbsp;&nbsp;Review us</a>
                        
                        <button class="signin" onclick="window.location.href="signin.php" " >Sign in</button>
                </div>
            </div>

            <div class="middle1">
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

            <div class="middle2">
                <div id="aboutUs"></div>
                <div class="middle2-top">
                    <h1>Our Mission</h1>
                    <p>Empowering the next generation of developers through practice and community.</p>
                </div>

            </div>
            
            <footer>
                <div class="footer">
                    <div class="bottomLeft"><img src="../images/logo.png" style="width: 170px;height:95px;margin-top:-10px;"></div>
                </div>
            </footer>

        </div>
    </body>
    </html>
    ';
?>