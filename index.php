<?php
require 'vendor/autoload.php'; 

// Connect to Database
$client = new MongoDB\Client("mongodb+srv://inf2003-mongodev:toor@inf2003-part2.i7agx.mongodb.net/");
$db = $client->eLibDatabase; 
$collection = $db->books; 

// Fetch all books from the database
$bookList = $collection->find(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/gallery.css">
</head>

<body>
    <?php include 'inc/nav.php'; ?>

    <h1>Book List</h1>

    <!-- Book Gallery -->
    <div class="gallery-container">
        <?php
        foreach ($bookList as $book) {
            $authors = is_array($book['authors']) ? $book['authors'] : (array)$book['authors'];
            $genres = is_array($book['genres']) ? $book['genres'] : (array)$book['genres'];
            
            echo "<div class='book' data-author='" . implode(', ', $authors) . "'>\n";
            echo "    <h3 class='bookTitle'>" . $book['title'] . "</h3>\n";
            echo "    <p class='ISBN'>" . $book['isbn'] . "</p>\n";
            echo "    <p class='publisher'>" . $book['publisher'] . "</p>\n";
            echo "    <p class='quantity'>" . $book['quantity'] . "</p>\n";
            echo "    <p class='language'>" . $book['language'] . "</p>\n";
            echo "    <p class='publishDate'>" . ($book['publication_date'] ? $book['publication_date']->toDateTime()->format('Y-m-d') : 'N/A') . "</p>\n";
            echo "    <p class='pageCount'>" . $book['page_count'] . "</p>\n";
            echo "    <p class='genre' style='display: none;'>" . implode(' / ', $genres) . "</p>\n";
            echo "</div>\n\n";
        }
        ?>
    </div>

    <!-- Overlay Section -->
    <div class="overlay" id="overlay" style="display: none;">
        <div class="overlay-content">
            <span class="close-btn" id="close-btn">&times;</span>
            <h3 id="overlay-title"></h3>
            <p id="overlay-isbn"></p>
            <p id="overlay-publisher"></p>
            <p id="overlay-language"></p>
            <p id="overlay-publishDate"></p>
            <p id="overlay-pageCount"></p>
            <p id="overlay-genre"></p>
            <p id="overlay-authors"></p>
            <button id="overlay-borrow-button" onClick="borrow()">Borrow</button>
        </div>
    </div>

    <script src="js/gallery.js"></script>

    <form id="borrowForm" style="display: none;" method="post">
        <input type="text" id="form-isbn" name="form-isbn">
        <input type="text" id="form-quantity" name="form-quantity">
        <input type="text" id="form-borrowdate" name="form-borrowdate">
        <input type="text" id="form-expirydate" name="form-expirydate">
    </form>
</body>
</html>
