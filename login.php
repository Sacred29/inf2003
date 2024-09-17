<!-- index.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Borrowed Book List</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/gallery.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-black.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">

</head>

<body>
    <?php
    include 'inc/nav.php';
    //require_once __DIR__ . '/config.php';
    ?>


    <main class="container">
        <input type="checkbox" id="flip">
        <div class="cover">
            <div class="front">
                <img src="images/loginbg.jpg" alt="">
                <div class="text">
                    <span class="text-1">Discover a world of knowledge<br> at your fingertips</span>
                    <span class="text-2">Access anytime, anywhere</span>
                </div>
            </div>
            <div class="back">
                <img src="images/loginbg.jpg" alt="">
                <div class="text">
                </div>
            </div>
        </div>
        <div class="forms">
            <div class="form-content">
                <div class="login-form">
                    <h1 class="title">Login</h1>
                    <form action="loginProcess.php" method="post" enctype="multipart/form-data">
                        <div class="input-boxes">
                            <div class="input-box">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="Lemail" name="Lemail" placeholder="Enter your email" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="Lpwd" name="Lpwd" placeholder="Enter your password" required>
                            </div>
                            <div class="text"><a href="/ForgetPW.php">Forgot password?</a></div>
                            <div class="button input-box">
                                <input type="submit" value="submit">
                            </div>
                            <div class="text sign-up-text">Don't have an account? <label for="flip">Signup now</label></div>
                        </div>
                    </form>
                </div>
                <div class="signup-form">
                    <div class="title">Signup</div>
                    <form action="registerProcess.php" method="post" enctype="multipart/form-data">
                        <div class="input-boxes">
                            <div class="input-box">
                                <i class="fas fa-id-card"></i>
                                <input type="text" id="fname" name="fname" placeholder="Enter your first name" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-id-card"></i>
                                <input type="text" id="lname" name="lname" placeholder="Enter your last name" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="pwd" name="pwd" placeholder="Enter your password" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="confirm_pwd" name="confirm_pwd" placeholder="Confirm your password" required>
                            </div>
                            <div class="button input-box">
                                <input type="submit" value="submit">
                            </div>
                        </div>
                    </form>
                    <div class="text sign-up-text">Already have an account? <label for="flip">Login now</label></div>
                </div>
            </div>
        </div>
    </main>


</body>