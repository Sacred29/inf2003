<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<!--?php include 'includes/head.php'; ?> -->
<link rel="stylesheet" href="css/gallery.css">

<body>
    <?php include 'inc/nav.php'; ?>

    <h1>Welcome to MySite</h1>
     <!-- Book Gallery -->
     <div class="gallery-container">
        <div class="book" data-book="1">
            <img src="book1.jpg" alt="Book 1 Cover">
            <h3>Book Title 1</h3>
            <p>Author Name 1</p>
        </div>
        <div class="book" data-book="2">
            <img src="book2.jpg" alt="Book 2 Cover">
            <h3>Book Title 2</h3>
            <p>Author Name 2</p>
        </div>
        <div class="book" data-book="3">
            <img src="book3.jpg" alt="Book 3 Cover">
            <h3>Book Title 3</h3>
            <p>Author Name 3</p>
        </div>
    </div>

    <!-- Overlay Section -->
    <div class="overlay" id="overlay">
        <div class="overlay-content">
            <span class="close-btn" id="close-btn">&times;</span>
            <img id="overlay-img" src="" alt="Book Cover">
            <h3 id="overlay-title"></h3>
            <p id="overlay-author"></p>
            <p id="overlay-description"></p>
        </div>
    </div>

    <script src="js/gallery.js"></script>
</body>
</html>
