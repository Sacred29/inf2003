<?php
session_start();
?>

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
    // pagination 
    echo '<link rel="stylesheet" href="css/pagination.css">';
    // Number of records to show per page
    $limit = 14;

    // Get the current page number from the query string or default to 1
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Get total number of records for pagination
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Booklist");
    $row = mysqli_fetch_assoc($result);
    $total_records = $row['total'];
    $total_pages = ceil($total_records / $limit);

    // Retrieve the records for the current page
    $bookList = mysqli_query($conn, "
        SELECT b.ISBN, 
        MAX(b.bookTitle) AS bookTitle, MAX(b.quantity) AS quantity, 
		MAX(b.language) AS language, MAX(b.publisher) AS publisher, 
		MAX(b.publishDate) AS publishDate, MAX(b.pageCount) AS pageCount, 
        GROUP_CONCAT(DISTINCT a.authorName ORDER BY a.authorName SEPARATOR ', ') authors, 
        GROUP_CONCAT(DISTINCT g.genreName ORDER BY g.genreName  SEPARATOR ' / ') genres
        FROM Booklist b
        INNER JOIN bookAuthor ba ON b.ISBN = ba.book_id 
        INNER JOIN Authors a ON ba.author_id = a.authorID
        INNER JOIN bookGenre bg ON b.ISBN = bg.book_id 
        INNER JOIN Genres g ON bg.genre_id = g.genreID 
        GROUP BY b.ISBN
        LIMIT $limit OFFSET $offset
    ");


    // $bookList = mysqli_query($conn, "Select * from Booklist inner join bookAuthor on bookAuthor.book_id = ISBN inner join Authors on bookAuthor.author_id = Authors.authorID");
}


if (isset($_POST['form-isbn']) && isset($_SESSION['userId'])) { //check if form was submitted
    $isbn = $_POST['form-isbn'];
    $borrowDate = $_POST['form-borrowdate'];
    $expiryDate = $_POST['form-expirydate'];
    $quantity = $_POST['form-quantity'];
    $status = "Borrowed";

    if ($conn->connect_error) {
        $errormsg = "Connection Failed";
        return;
    } else if ($quantity > 0) {

        //check if user has already borrowed the book
        $stmt = $conn->prepare("Select count(*) from Borrowed where ISBN = ? AND userID = ? AND expiryDate > ?");
        $date = date("Y-m-d");
        $stmt->bind_param('sis', $isbn, $_SESSION['userId'], $date);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            //add to borrow table
            $stmt = $conn->prepare("INSERT INTO Borrowed (ISBN, userID, borrowedDate, expiryDate, status) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $isbn, $_SESSION['userId'], $borrowDate, $expiryDate, $status);
            $stmt->execute();
            echo "<script>console.log('added')</script>";
            //update the book count
            $stmt = $conn->prepare("UPDATE Booklist SET quantity = ? where ISBN = ?");
            $quantity = $quantity - 1;
            $stmt->bind_param("is", $quantity, $isbn);
            $stmt->execute();
        } else {
            echo "<script>alert('You have already borrowed this book');</script>";
        }
    }
} else if (isset($_POST['form-isbn'])) {
    echo "<script>alert('Please login first');</script>";
}



?>

<body>
    <?php include 'inc/nav.php'; ?>

    <h1>Welcome to MySite</h1>


    <!-- Pagination -->
    <?php
    // Pagination logic
    $adjacents = 2; // Number of adjacent pages to show
    $start = ($page > $adjacents) ? $page - $adjacents : 1;
    $end = ($page < $total_pages - $adjacents) ? $page + $adjacents : $total_pages;

    echo "<div class='pagination'>";

    // Previous button
    if ($page > 1) {
        echo "<a href='?page=" . ($page - 1) . "' class='prev-next'>&laquo;</a>";
    }

    // First page link if not in the range
    if ($start > 1) {
        echo "<a href='?page=1'>1</a>";
        if ($start > 2) {
            echo "<span>...</span>"; // Ellipsis for skipped pages
        }
    }

    // Page number links
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $page) {
            echo "<strong>$i</strong>"; // Highlight current page
        } else {
            echo "<a href='?page=$i'>$i</a>";
        }
    }

    // Last page link if not in the range
    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            echo "<span>...</span>"; // Ellipsis for skipped pages
        }
        echo "<a href='?page=$total_pages'>$total_pages</a>";
    }

    // Next button
    if ($page < $total_pages) {
        echo "<a href='?page=" . ($page + 1) . "' class='prev-next'>&raquo;</a>";
    }

    echo "</div>";
    ?>

    <!-- Book Gallery -->
    <div class="gallery-container">
        <?php

        while ($row = mysqli_fetch_assoc($bookList)) {
            echo "<div class='book'>\n";
            // echo "    <img src='' alt='" . $row['bookTitle'] . " Cover'>\n";
            echo "    <h3 class='bookTitle' data-author='" . $row['authors'] . "'>" . $row['bookTitle'] . "</h3>\n";
            echo "    <p class='ISBN'>" . $row['ISBN'] . "</p>\n";
            echo "    <p class='publisher'>" . $row['publisher'] . "</p>\n";
            echo "    <p class='quantity'>" . $row['quantity'] . "</p>\n";
            echo "    <p class='language'>" . $row['language'] . "</p>\n";
            echo "    <p class='publishDate'>" . $row['publishDate'] . "</p>\n";
            echo "    <p class='pageCount'>" . $row['pageCount'] . "</p>\n";
            echo "    <p class='genre' style='display: none;'>" . $row['genres'] . "</p>\n";
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
            <p id="overlay-genre"></p>
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