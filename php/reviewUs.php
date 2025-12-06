<?php
    echo'
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="../css/reviewUs.css">
            <title>SignUp</title>
        </head>
        <body>
            <div class="page">
                <header>
                    <div><img style="width: 130px;height: 110px;margin-left: 20px;" src="../images/logoSign.png"></div>
                </header>
                <div class="container">
                    <div class="left">
                        <div class="left-title">
                            <div>Send a message</div>
                            <div><img style="width: 35px;height: 35px;" src="../images/mail.png"></div>
                        </div>
                        
                     <form method="post" action="reviewUsAction.php">
    <input class="input" type="email" placeholder="Email" name="email" required><br>

    <div class="rating-section">
        <label class="rating-label">Rate us:</label>
        <div class="stars">
            <input type="radio" id="star5" name="rating" value="5" />
            <label for="star5" title="5 stars">&#9733;</label>

            <input type="radio" id="star4" name="rating" value="4" />
            <label for="star4" title="4 stars">&#9733;</label>

            <input type="radio" id="star3" name="rating" value="3" />
            <label for="star3" title="3 stars">&#9733;</label>

            <input type="radio" id="star2" name="rating" value="2" />
            <label for="star2" title="2 stars">&#9733;</label>

            <input type="radio" id="star1" name="rating" value="1" />
            <label for="star1" title="1 star">&#9733;</label>
        </div>
    </div>

    <div class="left-subtitle">Leave your review?</div>
    <textarea style="width: 230px;height: 80px;" name="review"></textarea><br>

    <button type="submit" class="submitBtn">
        <img style="width: 25px;height: 20px;margin-left: -45px;margin-right: 15px;margin-top: 8px;" src="../images/send.png">
        <p style="margin-top: -22px;margin-right: -25px;">Submit</p>
    </button>
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
