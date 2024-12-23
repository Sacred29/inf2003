<?php include 'inc/nav.php'; ?>
<!-- index.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <title>User Profile</title>
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

<?php
require_once __DIR__ . '/config.php';

$userId = $_SESSION['userId'];
$firstname = $lastname = $email = "";

getUserInfo();

function getUserInfo()
{
    global $userId, $firstname, $lastname, $email;

    // Connect to Database
    $client = new MongoDB\Client("mongodb+srv://inf2003-mongodev:toor@inf2003-part2.i7agx.mongodb.net/");
    $db = $client->eLibDatabase;
    $userCollection = $db->Users;

    $user = $userCollection->findOne(['userID' => (int)$userId]);

    if ($user === null) {
        $firstname = 'Not Found';
        $lastname = 'Not Found';
        $email = 'Not Found';
    } else {
        $firstname = $user['firstName'];
        $lastname = $user['lastName'];
        $email = $user['email'];
        $_SESSION['pwd'] = $user['password'];
    }
}

?>

<body>
    <br>
    <div class="w3-container">
        <br>
        <div class="profile-container">
            <h2>User Profile</h2>
            <form action="profile_update_process.php" method="post">
                <!-- First Name -->
                <div class="form-group">
                    <label for="first-name">First Name:</label>
                    <input type="text" id="profile_fname" name="profile_fname" value=<?php echo '"' . $firstname . '"'; ?> required>
                </div>

                <!-- Last Name -->
                <div class="form-group">
                    <label for="last-name">Last Name:</label>
                    <input type="text" id="profile_lname" name="profile_lname" value=<?php echo '"' . $lastname . '"'; ?> required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="profile_email" name="profile_email" value=<?php echo '"' . $email . '"'; ?> required>
                </div>

                <h3>Update Password</h3>

                <!-- New Password -->
                <div class="form-group">
                    <label for="new-password">New Password:</label>
                    <input type="password" id="new_password" name="new_password">
                </div>

                <!-- Confirm New Password -->
                <div class="form-group">
                    <label for="confirm-password">Confirm New Password:</label>
                    <input type="password" id="confirm_new_password" name="confirm_new_password">
                </div>

                <!-- Submit Button -->
                <div class="form-group">
                    <input type="submit" value="Update Profile">
                </div>
            </form>
        </div>
    </div>

</body>