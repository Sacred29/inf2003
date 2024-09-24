<?php

use GrahamCampbell\ResultType\Success;

include 'inc/nav.php';
require_once __DIR__ . '/config.php';


$profile_fname = $profile_lname = $profile_email = $new_pwd = $new_pwd_hash = "";
$update_profile_error_msg = array();
$success = true;

// Helper function that checks input for malicious or unwanted content.
function sanitize_input($data)
{
     $data = trim($data);
     $data = stripslashes($data);
     $data = htmlspecialchars($data);
     return $data;
}

if (empty($_POST["profile_fname"])) {
     array_push($update_profile_error_msg, "First Name is required!");
     $success = false;
} else {
     $profile_fname = sanitize_input($_POST["profile_fname"]);
     if (preg_match('/(&amp;|&lt;|&gt;|&quot;|&#39;)/', $profile_fname)) {
          array_push($update_profile_error_msg, "First Name contains illegal characters!");
          $success = false;
     }
}

if (empty($_POST["profile_lname"])) {
     array_push($update_profile_error_msg, "Last Name is required!");
     $success = false;
} else {
     $profile_lname = sanitize_input($_POST["profile_lname"]);
     if (preg_match('/(&amp;|&lt;|&gt;|&quot;|&#39;)/', $profile_lname)) {
          array_push($update_profile_error_msg, "Last Name contains illegal characters!");
          $success = false;
     }
}

if (empty($_POST["profile_email"])) {
     array_push($update_profile_error_msg, "Email is required!");
     $success = false;
} else {
     $profile_email = sanitize_input($_POST["profile_email"]);
     // Additional check to make sure e-mail address is well-formed.
     if (!filter_var($profile_email, FILTER_VALIDATE_EMAIL)) {
          array_push($update_profile_error_msg, "Invalid email entered!");
          $success = false;
     }
}

if (!empty($_POST["new_password"]) && empty($_POST["confirm_new_password"])) {
     array_push($update_profile_error_msg, "Password and Confirm Password are required!");
     $success = false;
} else if (empty($_POST["new_password"]) && !empty($_POST["confirm_new_password"])) {
     array_push($update_profile_error_msg, "Password and Confirm Password are required!");
     $success = false;
} else if (empty($_POST["new_password"]) && empty($_POST["confirm_new_password"])) {
     $new_pwd = $_SESSION['pwd'];
} else {
     $new_pwd = $_POST["new_password"];
     $new_pwd_confirm = $_POST["confirm_new_password"];
     // Additional check to make sure e-mail address is well-formed.
     /*if (!preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).*$/", $pwd)) {
          array_push($update_profile_error_msg, "Passwords must contain at least 1 uppercase letter, 1 lowercase letter and 1 number!");
          $success = false;
     } else */
     if ($new_pwd != $new_pwd_confirm) {
          array_push($update_profile_error_msg, "Passwords does not match!");
          $success = false;
     } else {
          $new_pwd_hash = password_hash($new_pwd, PASSWORD_DEFAULT);
     }
}

function checkEmailExist()
{
     global $profile_email, $update_profile_error_msg, $success;

     $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

     //check connection
     if ($conn->connect_error) {
          array_push($update_profile_error_msg, "Connection failed: " . $conn->connect_error);
          $success = false;
     } else {
          $stmt = $conn->prepare("SELECT * FROM Users where email=?");
          $stmt->bind_param("s", $profile_email);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0) {
               array_push($update_profile_error_msg, "Email is already in used!");
               $success = false;
          }
     }
}

function updateProfile()
{
     global $profile_fname, $profile_lname, $profile_email, $new_pwd_hash, $update_profile_error_msg, $success;

     $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

     //check connection
     if ($conn->connect_error) {
          array_push($update_profile_error_msg, "Connection failed: " . $conn->connect_error);
          $success = false;
     } else {
          $stmt = $conn->prepare("
                         UPDATE Users 
                         SET firstName = ?, lastName = ?, email = ?, password = ? 
                         WHERE userID = ?
                         ");
          $stmt->bind_param("ssssi", $profile_fname, $profile_lname, $profile_email, $new_pwd_hash, $_SESSION['userId']);
          $stmt->execute();
          if (!$stmt->execute()) {
               array_push($register_error_msg, "Execute failed: (" . $stmt->errno . ") " .  $stmt->error);
               $success = false;
          }

          $stmt->close();
     }
     $conn->close();
}

checkEmailExist();
if ($success) {
     updateProfile();
}
?>

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

<main data-scroll-section>
     <div class="d-flex flex-column justify-content-center align-items-center" style="height: 75vh;">
          <?php
          if ($success) {
               echo "<h4>Profile updated successfully!</h4>";
               echo '<a href="profile.php"><button class="btn btn-success mb-4" type="submit">Return to Profile</button></a>';
          } else {
               echo "<h1>Oops! Profile update failed!</h1>";
               echo "<p class='h4'>The following input errors were detected:</p>";
               echo "<p>";
               foreach ($update_profile_error_msg as $error) {
                    echo $error . "<br>";
               };
               echo "</p>";
               echo "<form style='display: inline' action='profile.php' method='get'><button class='btn btn-danger mb-4 btn-lg'>Return to Profile</button></form>";
          }
          ?>
     </div>
</main>