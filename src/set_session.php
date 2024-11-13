<?php
// Start the session
session_start();

// New Entry
if (isset($_POST['search']) && isset($_POST['searchType'])) {
    // Store the search term and type in session variables
    $_SESSION['search'] = $_POST['search'];
    $_SESSION['searchType'] = $_POST['searchType'];
    unset($_SESSION['page']);
} 

// new page
if (isset($_POST['page'])) {

    $_SESSION['page'] = $_POST['page'];
} 
?>
