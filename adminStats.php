<?php
session_start();
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


<?php
include 'inc/nav.php';

//if not admin send to index.php
if (isset($_SESSION['type'])){
    if($_SESSION['type'] != 'Admin'){
        echo "<script>window.location.href = 'index.php'</script>";
    }
}
?>

<body>
    <div class="w3-container w3-padding-64">
        <h2>Admin Dashboard</h2>

        <div class="stat-box">
            <h3>Total Books Borrowed This Month</h3>

            <a href="borrowed_books_report.php">View Details</a>
        </div>

        <div class="stat-box">
            <h3>Most Borrowed Genre</h3>

            <a href="genre_report.php">View Genre Stats</a>
        </div>

        <div class="stat-box">
            <h3>Top Borrower</h3>

            <a href="top_borrowers_report.php">View Top Borrowers</a>
        </div>
    </div>
</body>