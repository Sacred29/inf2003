<?php

use function MongoDB\is_document;
use function PHPSTORM_META\type;

session_start();

include 'inc/nav.php';
require_once __DIR__ . '/config.php';

$login_email = $login_pwd = "";

$login_error_msg = array();
$success = true;

// Helper function that checks input for malicious or unwanted content.
function sanitize_input($data)
{
     $data = trim($data);
     $data = stripslashes($data);
     $data = htmlspecialchars($data);
     return $data;
}

if (empty($_POST["login_email"])) {
     array_push($login_error_msg, "Login email is required!");
     $success = false;
} else {
     $login_email = sanitize_input($_POST["login_email"]);
     // Additional check to make sure e-mail address is well-formed.
     if (!filter_var($login_email, FILTER_VALIDATE_EMAIL)) {
          array_push($login_error_msg, "Invalid email entered!");
          echo $login_email;
          $success = false;
     }
}

if (empty($_POST["login_pwd"])) {
     array_push($login_error_msg, "Login password are required!");
     $success = false;
} else {
     $login_pwd = $_POST["login_pwd"];
}

function login()
{
     global $login_email, $login_pwd, $login_error_msg, $success;

     // Connect to Database
     $client = new MongoDB\Client("mongodb+srv://inf2003-mongodev:toor@inf2003-part2.i7agx.mongodb.net/");
     $db = $client->eLibDatabase;
     $collection = $db->Users;

     // Fetch all books from the database
     $users = $collection->find(['email' => $login_email])->toArray();

     if (count($users) > 0) {
          //verify password
          if (!password_verify($login_pwd, $users[0]['password'])) {
               array_push($login_error_msg, "Invalid email or password!");
               $success = false;
          } else {
               $userId = $users[0]['userID'];
               $_SESSION['userId'] = $userId;
               $_SESSION['type'] = "User";
               echo "<script>console.log('Login Successfull!')</script>";
          }
     } else {
          // User not found
          array_push($login_error_msg, "Invalid email or password!");
          $success = false;
     }
}

function adminLogin()
{
     global $login_email, $login_pwd, $login_error_msg, $success;

     // Connect to Database
     $client = new MongoDB\Client("mongodb+srv://inf2003-mongodev:toor@inf2003-part2.i7agx.mongodb.net/");
     $db = $client->eLibDatabase;
     $collection = $db->Admin;

     // Fetch all books from the database
     $admins = $collection->find(['email' => $login_email])->toArray();

     if (count($admins) > 0) {
          $row = $admins[0];

          //verify password
          if (!password_verify($login_pwd, $row['password'])) {
               array_push($login_error_msg, "Invalid email or password!");
               $success = false;
          } else {
               $userId = $row['adminID'];
               $_SESSION['userId'] = $userId;
               $_SESSION['type'] = "Admin";
               echo "<script>console.log('Login as Admin Successfull!')</script>";
          }
     } else {
          // User not found
          array_push($login_error_msg, "Invalid email or password!");
          $success = false;
     }
}

$type = $_REQUEST['type'];
if ($success) {
     if ($type == 'user') {
          login();
     } else if ($type == 'admin') {
          adminLogin();
     }
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
          if ($success && $type == 'user') {
               echo "<script>window.location.href = 'index.php'</script>";
          } else if ($success && $type == 'admin') {
               echo "<script>window.location.href = 'manageBooks.php'</script>";
          } else {
               echo "<h1>Oops! Login Failed!</h1>";
               echo "<p class='h4'>The following input errors were detected:</p>";
               echo "<p>";
               foreach ($login_error_msg as $error) {
                    echo $error . "<br>";
               };
               echo "</p>";
               echo "<form style='display: inline' action='login.php' method='get'><button class='btn btn-danger mb-4 btn-lg'>Return to Login</button></form>";
          }
          ?>
     </div>
</main>