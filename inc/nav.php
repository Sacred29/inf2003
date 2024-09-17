<!-- includes/navbar.php -->
<link rel="stylesheet" href="css/nav.css">

<nav class="navbar">
    <div class="logo">
        <a href="index.php">MySite</a>
    </div>
    <ul class="nav-links">
        <?php
        session_start();

        if (isset($_SESSION['userId'])) {
            echo '<li><a href="index.php">Home</a></li>
        <li><a href="borrowing.php">Books Borrowed</a></li>
        <li><a href="profile.php">Profile</a></li>';
        } else {
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