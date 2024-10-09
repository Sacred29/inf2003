<?php
session_start();

$searchType = isset($_SESSION['searchType']) ? $_SESSION['searchType'] : 'title';
$searchText = isset($_SESSION['search']) ? $_SESSION['search'] : '';

?>

<?php
function getBookList ($conn, $searchType, $searchText) {
    

    // pagination    
    if (isset($_POST['page'])) {
        // Update session state with the page number
        $_SESSION['page'] = (int)$_POST['page'];
    } 
    $limit = 14;
    
    // Get the current page number from the query string or default to 1
    $page = isset($_SESSION['page']) ? (int)$_SESSION['page'] : 1;
    $offset = ($page - 1) * $limit;

    // query
    $sqlQuery = "
    SELECT b.ISBN, 
        b.bookTitle, b.quantity, 
        b.language, b.publisher, 
        b.publishDate, b.pageCount,  
        GROUP_CONCAT(DISTINCT a.authorName ORDER BY a.authorName SEPARATOR ', ') authors, 
        GROUP_CONCAT(DISTINCT g.genreName ORDER BY g.genreName  SEPARATOR ' / ') genres
        FROM Booklist b
        INNER JOIN bookAuthor ba ON b.ISBN = ba.book_id 
        INNER JOIN Authors a ON ba.author_id = a.authorID
        INNER JOIN bookGenre bg ON b.ISBN = bg.book_id 
        INNER JOIN Genres g ON bg.genre_id = g.genreID
        ";
    $sqlGroupBy = "
    GROUP BY b.ISBN
    ";
    if (!empty($searchText)){
        switch ($searchType) {
            case 'author':
                $sqlQuery .=" WHERE a.authorName like '$searchText%' ";
                break;
            case 'genre':
                $sqlQuery .= " WHERE g.genreName like '$searchText%' ";
                break;
            case 'title':
                default:
                $sqlQuery .= " WHERE b.bookTitle like '$searchText%' ";
                break;
        }
    }
    $sqlQuery .= $sqlGroupBy;
    // Get total number of records for pagination
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM ($sqlQuery) as subquery");
    $row = mysqli_fetch_assoc($result);
    $total_records = $row['total'];
    $total_pages = ceil($total_records / $limit);

    $bookList = mysqli_query($conn, $sqlQuery . " LIMIT $limit OFFSET $offset");

    return ['total_pages'=>$total_pages, 'bookList'=>$bookList, 'total_records' => $total_records, 'page'=> $page,];
}
?>

<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<!--?php include 'includes/head.php'; ?> -->
<link rel="stylesheet" href="css/gallery.css">
<link rel="stylesheet" href="css/pagination.css">
<link rel="stylesheet" href="css/searchbar.css">

<!-- Get Booklist -->
<?php

require_once __DIR__ . '/config.php';
$conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

if ($conn->connect_error) {
    $errormsg = "Connection Failed";
    return;
} else {
    
    $response = getBookList ($conn, $searchType, $searchText);
    $bookList = $response['bookList'];
    $total_pages = $response['total_pages'];
    $total_records = $response['total_records'];
    $page = $response['page'];
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

    <!-- Search Bar -->
    <div class="search-bar">
        <form method="get" action="index.php">
            <input type="text" id="search" name="search" placeholder="Search..." autocomplete="off">
            <div>
                <input type="radio" id="by-title" name="searchType" value="title" 
                    <?php echo $searchType === 'title' ? 'checked' : ''; ?>>
                <label for="by-title">Title</label>

                <input type="radio" id="by-author" name="searchType" value="author"
                    <?php echo $searchType === 'author' ? 'checked' : ''; ?>>
                <label for="by-author">Author</label>

                <input type="radio" id="by-genre" name="searchType" value="genre"
                    <?php echo $searchType === 'genre' ? 'checked' : ''; ?>>
                <label for="by-genre">Genre</label>
            </div>
                <button type="submit">Search</button>
            </form>
    </div>

    <!-- Pagination -->
    <form method="POST" action="">
    <?php
    // Pagination logic
    $adjacents = 2; // Number of adjacent pages to show
    $start = ($page > $adjacents) ? $page - $adjacents : 1;
    $end = ($page < $total_pages - $adjacents) ? $page + $adjacents : $total_pages;

    echo "<div class='pagination'>";

    // Previous button
    if ($page > 1) {
        echo "<button type='submit' name='page' value='" . ($page - 1) . "' class='prev-next'>&laquo;</button>";
    }

    // First page link if not in the range
    if ($start > 1) {
        echo "<button type='submit' name='page' value='1'>1</button>";
        if ($start > 2) {
            echo "<span>...</span>"; // Ellipsis for skipped pages
        }
    }

    // Page number links
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $page) {
            echo "<strong>$i</strong>"; // Highlight current page
        } else {
            echo "<button type='submit' name='page' value='$i'>$i</button>";
        }
    }

    // Last page link if not in the range
    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            echo "<span>...</span>"; // Ellipsis for skipped pages
        }
        echo "<button type='submit' name='page' value='$total_pages'>$total_pages</button>";
    }

    // Next button
    if ($page < $total_pages) {
        echo "<button type='submit' name='page' value='" . ($page + 1) . "' class='prev-next'>&raquo;</button>";
    }

    echo "</div>";
    ?>
    </form>

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
    <script src="js/searchbar.js"></script>

    <form id="borrowForm" style="display: none;" method="post">
        <input type="text" id="form-isbn" name="form-isbn">
        <input type="text" id="form-quantity" name="form-quantity">
        <input type="text" id="form-borrowdate" name="form-borrowdate">
        <input type="text" id="form-expirydate" name="form-expirydate">
    </form>
</body>

</html>