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
    <link rel="stylesheet" href="css/profile.css">

</head>

<body>
    <?php
    include 'inc/nav.php';
    require_once __DIR__ . '/config.php';

    //$userId = $_SESSION['userId'];
    $userId = 1;
    $firstname = $lastname = $email = "";

    getUserInfo();
    function getUserInfo()
    {
        global $userId, $firstname, $lastname, $email;

        $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

        //check connection
        if ($conn->connect_error) {
            $errormsg = "Connection Failed";
            return;
        } else {

            //prepare statement
            $stmt = $conn->prepare("SELECT * FROM Users where userID=?");
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $firstname = $row['firstName'];
                $lastname = $row['lastName'];
                $email = $row['email'];
            }
        }
        $conn->close();
    }

    ?>

    <br>

    <div class="w3-container">
        <br>
        <div class="profile-container">
            <h2>User Profile</h2>
            <form action="/update_profile" method="post">
                <!-- First Name -->
                <div class="form-group">
                    <label for="first-name">First Name:</label>
                    <input type="text" id="first-name" name="first_name" value=<?php echo '"' . $firstname . '"'; ?> required>
                </div>

                <!-- Last Name -->
                <div class="form-group">
                    <label for="last-name">Last Name:</label>
                    <input type="text" id="last-name" name="last_name" value=<?php echo '"' . $lastname . '"'; ?> required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value=<?php echo '"' . $email . '"'; ?> required>
                </div>

                <h3>Update Password</h3>

                <!-- New Password -->
                <div class="form-group">
                    <label for="new-password">New Password:</label>
                    <input type="password" id="new-password" name="new_password" required>
                </div>

                <!-- Confirm New Password -->
                <div class="form-group">
                    <label for="confirm-password">Confirm New Password:</label>
                    <input type="password" id="confirm-password" name="confirm_password" required>
                </div>

                <!-- Submit Button -->
                <div class="form-group">
                    <input type="submit" value="Update Profile">
                </div>
            </form>
        </div>
    </div>

</body>