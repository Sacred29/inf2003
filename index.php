<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<!--?php include 'includes/head.php'; ?> -->
<link rel="stylesheet" href="css/gallery.css">


<!-- Get Booklist -->
<?php
require_once __DIR__ . '/config.php';
$conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

if ($conn->connect_error) {
    $errormsg = "Connection Failed";
    return;
} else {
    $bookList = mysqli_query($conn, "Select * from Booklist");
}
?>

<body>
    <?php include 'inc/nav.php'; ?>

    <h1>Welcome to MySite</h1>
     <!-- Book Gallery -->
     <div class="gallery-container">
        <?php
        
            while ($row = mysqli_fetch_assoc($bookList)) {
                echo "<div class='book'>\n";
                // echo "    <img src='' alt='" . $row['bookTitle'] . " Cover'>\n";
                echo "    <h3 class='bookTitle'>" . $row['bookTitle'] . "</h3>\n";
                echo "    <p class='ISBN'>" . $row['ISBN'] . "</p>\n";
                echo "    <p class='publisher'>" . $row['publisher'] . "</p>\n";
                echo "    <p class='quantity'>" . $row['quantity'] . "</p>\n";
                echo "    <p class='language'>" . $row['language'] . "</p>\n";
                echo "    <p class='publishDate'>" . $row['publishDate'] . "</p>\n";
                echo "    <p class='pageCount'>" . $row['pageCount'] . "</p>\n";
                echo "</div>\n\n";
            }
        
        ?>
    </div>

    <!-- Overlay Section -->
    <div class="overlay" id="overlay">
        <div class="overlay-content">
            <span class="close-btn" id="close-btn">&times;</span>
            <img id="overlay-img" src="" alt="Book Cover">
            <h3 id="overlay-title"></h3>
            <p id="overlay-isbn"></p>
            <p id="overlay-description"></p>
            <button id="overlay-borrow-button" onClick="borrow()">Borrow</button>
        </div>
    </div>

    <script src="js/gallery.js"></script>
</body>
</html>
