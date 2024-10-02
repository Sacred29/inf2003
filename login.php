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

<?php include 'inc/nav.php'; ?>

<body>
    <br>
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
                    <form action="login_process.php?type=user" method="post">
                        <div class="input-boxes">
                            <input type="hidden" name="form" value="login">
                            <div class="input-box">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="login_email" name="login_email" placeholder="Enter your email" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="login_pwd" name="login_pwd" placeholder="Enter your password" required>
                            </div>
                            <div class="text"><a href="">Forgot password?</a></div>
                            <div class="button input-box">
                                <input type="submit" value="Login">
                            </div>
                            <div class="text sign-up-text">Don't have an account? <a href="register.php">Sign up now</a></div>
                            <div class="text sign-up-text">Are you an Admin? <label for="flip">Click here</label></div>
                        </div>
                    </form>
                </div>
                <div class="admin-login-form">
                    <h1 class="title">Admin Login</h1>
                    <form action="login_process.php?type=admin" method="post">
                        <div class="input-boxes">
                            <input type="hidden" name="form" value="login">
                            <div class="input-box">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="login_email" name="login_email" placeholder="Enter your email" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="login_pwd" name="login_pwd" placeholder="Enter your password" required>
                            </div>
                            <div class="button input-box">
                                <input type="submit" value="Login">
                            </div>
                            <div class="text sign-up-text">Are you a User? <label for="flip">Click here</label></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>