<?php
echo'
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/Welcome.css">
        <title>ThinkByte</title>
    </head>
    <body>
        <div class="mainBox"> 

            <div class="topSide">
                <div class="topLeft"><img class="logo" src="../images/logo.png"></div>
                <div class="topRight">
                    <a href="#about">About us&nbsp;&nbsp;</a>
                    <a href="contactUs.php">&nbsp;&nbsp;Contact us&nbsp;&nbsp;</a>
                    <a href="reviewUs.php">&nbsp;&nbsp;Review us</a>
                    <button class="signin"><a class="sign" href="signIn.php">Sign in</a></button>
                </div>
            </div>

            <div class="middle1">
                <div class="middleLeft">
                    <div class="title">Think Byte</div>
                    <div class="subtitle">A sleek, interactive website where programmers<br> test and 
                        improve their coding skills through timed quizzes,<br> 
                        practical challenges, and competitive programming.
                    </div>
                    <div><button class="start"><a class="sign" href="signUp.php">Start Learning</a></button></div>
                </div>
                <div class="middleRight"><img src="../images/welcomePage.png" style="width: 650px; height: 450px;"></div>
            </div>

            <div class="middle2">
                <div class="middle2-left">
                    <p class="h2">Quality Over<br> ClickBait</p>
                    <p id="about" class="m2-subtitle">While other platforms reward clicks and completions,<br>Think Byte values 
                        <strong>genuine skill development</strong>.<br> Our program ensures that 
                        programmers truly learn from<br> problem breakdowns and system design explanations, <br>
                        contributing to real engineering growth.
                    </p>
                </div>
                <div class="middle2-right">
                    <div class="f1">
                        <p class="t">Clear & Friendly Design</p>
                        <p class="st">Clean buttons, readable text, and a consistent style make the app<br>
                         easy to use. A friendly interface encourages users to explore<br>
                          more pages and participate in discussions.</p>
                    </div>
                    <div class="f2">
                        <p class="t">Engaging Interactions</p>
                        <p class="st">Achievements, comments, and discussions make learning fun.<br>
                         These interactions motivate users to return and stay active.</p>
                    </div>
                    <div class="f3">
                        <p class="t">Reliable Performance</p>
                        <p class="st">Quizzes should load every time, discussions should send instantly,<br>
                         and profiles should display correctly. Reliable behavior helps users trust the platform.</p>
                    </div>
                    <div class="f4">
                        <p class="t">Efficient Database</p>
                        <p class="st">Fast, optimized queries ensure quizzes, results, and user data load instantly,<br> even as the platform grows. A strong backend keeps everything running smoothly.</p>
                    </div>
                </div>
            </div>

            <footer>
                <div class="footer">
                    <div class="bottomLeft"><img src="../images/logo.png" style="width: 170px;height:95px;margin-top:-10px;"></div>
                    <p class="cr">&copy; 2025 <span class="tb">ThinkByte</span>. All rights reserved.</p>
                </div>
            </footer>

        </div>
    </body>
    </html>
    ';
?>