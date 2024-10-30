<?php
if (!isset($_SESSION)) {
    session_start();
}

if (strpos($_SERVER['REQUEST_URI'], "editBook.php") == True) {
} else if (strpos($_SERVER['REQUEST_URI'], "addBook.php") == True) {
} else {
    if (isset($_SESSION['authors'])) {
        unset($_SESSION['authors']);
    }
    if (isset($_SESSION['genres'])) {
        unset($_SESSION['genres']);
    }
}
?>

<!-- includes/navbar.php -->
<link rel="stylesheet" href="css/nav.css">


<nav class="navbar">
    <div class="logo">
        <a href="index.php?unset_search=true">MySite</a>
    </div>
    <ul class="nav-links">
        <?php
        if (isset($_SESSION['userId']) && $_SESSION['type'] == "User") {
            echo '<li><a href="index.php?unset_search=true">Home</a></li>';
            echo '<li><a href="borrowing.php">Books Borrowed</a></li>';
            echo '<li><a href="profile.php">Profile</a></li>';
            echo '<li><a href="logout_process.php">Logout</a></li>';
        } else if (isset($_SESSION['userId']) && $_SESSION['type'] == "Admin") {
            echo '<li><a href="manageBooks.php">Manage Books</a></li>';
            echo '<li><a href="adminStats.php">Dashboard</a></li>';
            echo '<li><a href="logout_process.php">Logout</a></li>';
        } else {
            echo '<li><a href="index.php?unset_search=true">Home</a></li>';
            echo '<li><a href="login.php">Login/Register</a></li>';
        }
        ?>
    </ul>
    <div class="hamburger" onclick="toggleMenu()">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </div>
</nav>

<!-- JavaScript for mobile menu toggle and window resize handling -->
<script>
    function toggleMenu() {
        const navLinks = document.querySelector('.nav-links');
        navLinks.style.display = (navLinks.style.display === 'flex') ? 'none' : 'flex';
    }

    // Ensure that the nav links display correctly when resizing the window
    window.addEventListener('resize', function() {
        const navLinks = document.querySelector('.nav-links');
        if (window.innerWidth > 768) {
            navLinks.style.display = 'flex'; // Always show menu for larger screens
        } else {
            navLinks.style.display = 'none'; // Hide menu for smaller screens
        }
    });

    // Initial check on page load
    window.addEventListener('load', function() {
        const navLinks = document.querySelector('.nav-links');
        if (window.innerWidth > 768) {
            navLinks.style.display = 'flex'; // Always show menu for larger screens
        } else {
            navLinks.style.display = 'none'; // Hide menu for smaller screens
        }
    });
</script>