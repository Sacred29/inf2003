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
    $error = false;
    require_once __DIR__ . '/config.php';


    if (isset($_POST['Lemail'])) {
        echo "<script>console.log('posted')</script>";
        login(); //here goes the function call
    }
    function login()
    {
        global $error;


        $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

        //check connection
        if ($conn->connect_error) {
            $errormsg = "Connection Failed";
            return;
        } else {

            //prepare statement
            $stmt = $conn->prepare("SELECT * FROM Users where email=?");
            $stmt->bind_param("s", $_POST['Lemail']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                //verify password
                if (!password_verify($_POST['Lpwd'], $row['password'])) {
                    $error = true;
                    echo "<script>console.log('wrong existence')</script>";

                } else {
                    $userId = $row['userID'];
                    $email = $row['email'];
                    $_SESSION['userID'] = $userId;
                }
            }
            $conn->close();
        }
    }

    ?>

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
                    <?php if (isset($error) && $error == true) echo "Wrong Email or Password Try Again";?>
                    <form method="post" enctype="multipart/form-data">
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