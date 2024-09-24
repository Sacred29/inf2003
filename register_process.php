<?php
include 'inc/nav.php';
require_once __DIR__ . '/config.php';

$fname = $lname = $email = $pwd = $pwd_hash = "";

$register_error_msg = array();
$success = true;

// Helper function that checks input for malicious or unwanted content.
function sanitize_input($data)
{
     $data = trim($data);
     $data = stripslashes($data);
     $data = htmlspecialchars($data);
     return $data;
}

if (empty($_POST["fname"])) {
     array_push($register_error_msg, "First Name is required!");
     $success = false;
} else {
     $fname = sanitize_input($_POST["fname"]);
     if (preg_match('/(&amp;|&lt;|&gt;|&quot;|&#39;)/', $fname)) {
          array_push($register_error_msg, "First Name contains illegal characters!");
          $success = false;
     }
}

if (empty($_POST["lname"])) {
     array_push($register_error_msg, "Last Name is required!");
     $success = false;
} else {
     $lname = sanitize_input($_POST["lname"]);
     if (preg_match('/(&amp;|&lt;|&gt;|&quot;|&#39;)/', $lname)) {
          array_push($register_error_msg, "Last Name contains illegal characters!");
          $success = false;
     }
}

if (empty($_POST["email"])) {
     array_push($register_error_msg, "Email is required!");
     $success = false;
} else {
     $email = sanitize_input($_POST["email"]);
     // Additional check to make sure e-mail address is well-formed.
     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          array_push($register_error_msg, "Invalid email entered!");
          $success = false;
     }
}

if (empty($_POST["pwd"]) || empty($_POST["confirm_pwd"])) {
     array_push($register_error_msg, "Password and Confirm Password are required!");
     $success = false;
} else {
     $pwd = $_POST["pwd"];
     $pwd_confirm = $_POST["confirm_pwd"];
     // Additional check to make sure e-mail address is well-formed.
     /*if (!preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).*$/", $pwd)) {
          array_push($register_error_msg, "Passwords must contain at least 1 uppercase letter, 1 lowercase letter and 1 number!");
          $success = false;
     } else */
     if ($pwd != $pwd_confirm) {
          array_push($register_error_msg, "Passwords does not match!");
          $success = false;
     } else {
          $pwd_hash = password_hash($pwd, PASSWORD_DEFAULT);
     }
}

function register()
{
     global $fname, $lname, $email, $pwd_hash, $register_error_msg, $success;

     $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

     if ($conn->connect_error) {
          array_push($register_error_msg, "Connection failed: " . $conn->connect_error);
          $success = false;
     } else {
          // Prepare the statement:
          $stmt = $conn->prepare("
                         INSERT INTO Users (firstName, lastName, email, password) 
                         VALUES (?, ?, ?, ?)
                         ");
          // Bind & execute the query statement:
          $stmt->bind_param("ssss", $fname, $lname, $email, $pwd_hash);

          if (!$stmt->execute()) {
               array_push($register_error_msg, "Execute failed: (" . $stmt->errno . ") " . $stmt->error);
               $success = false;
          }

          $stmt->close();
     }

     $conn->close();
}

if ($success) {
     register();
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
               echo "<h4>Registration successful!</h4>";
               echo "<h5>Thank you for signing up, " . $_POST["email"] .  ".</h5>";
               echo '<a href="login.php"><button class="btn btn-success mb-4" type="submit">Login</button></a>';
          } else {
               echo "<h1>Oops! Registration failed!</h1>";
               echo "<p class='h4'>The following input errors were detected:</p>";
               echo "<p>";
               foreach ($register_error_msg as $error) {
                    echo $error . "<br>";
               };
               echo "</p>";
               echo "<form style='display: inline' action='register.php' method='get'><button class='btn btn-danger mb-4 btn-lg'>Return to Sign Up</button></form>";
          }
          ?>
     </div>
</main>