<?php
session_start();
require 'vendor/autoload.php';

// pagination and styling
echo '<link rel="stylesheet" href="css/pagination.css">';
echo '<link rel="stylesheet" href="css/searchbar.css">';

// Connect to Database
$client = new MongoDB\Client("mongodb+srv://inf2003-mongodev:toor@inf2003-part2.i7agx.mongodb.net/");
$db = $client->eLibDatabase;
$bookCollection = $db->books;

// Number of records to show per page
$limit = 14;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search parameters
$searchType = isset($_GET['searchType']) ? $_GET['searchType'] : 'title';
$searchText = isset($_GET['search']) ? $_GET['search'] : '';

// Build the search query
$filter = [];
if (!empty($searchText)) {
    switch ($searchType) {
        case 'author':
            $filter['authors'] = new MongoDB\BSON\Regex("^$searchText", 'i');
            break;
        case 'genre':
            $filter['genres'] = new MongoDB\BSON\Regex("^$searchText", 'i');
            break;
        default:
            $filter['title'] = new MongoDB\BSON\Regex("^$searchText", 'i');
            break;
    }
}

// Count documents and calculate total pages
$count = $bookCollection->countDocuments($filter);
$total_pages = ceil($count / $limit);

// Fetch books based on the filter
$bookList = $bookCollection->find(
    $filter,
    [
        'skip' => $offset,
        'limit' => $limit,
        'sort' => ['isbn' => 1]
    ]
)->toArray();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="css/gallery.css">
</head>

<body>
    <?php include 'inc/nav.php'; ?>

    <h1>Book List</h1>

    <!-- Search Bar -->
    <div class="search-bar">
        <form method="get" action="index.php">
            <input type="text" id="search" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($searchText); ?>" autocomplete="off">
            <div>
                <input type="radio" id="by-title" name="searchType" value="title" <?php echo $searchType === 'title' ? 'checked' : ''; ?>>
                <label for="by-title">Title</label>

                <input type="radio" id="by-author" name="searchType" value="author" <?php echo $searchType === 'author' ? 'checked' : ''; ?>>
                <label for="by-author">Author</label>

                <input type="radio" id="by-genre" name="searchType" value="genre" <?php echo $searchType === 'genre' ? 'checked' : ''; ?>>
                <label for="by-genre">Genre</label>
            </div>
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="w3-container">
        <!-- Pagination -->
        <?php
        $adjacents = 2;
        $start = ($page > $adjacents) ? $page - $adjacents : 1;
        $end = ($page < $total_pages - $adjacents) ? $page + $adjacents : $total_pages;

        echo "<div class='pagination'>";

        if ($page > 1) {
            echo "<a href='?page=" . ($page - 1) . "&search=$searchText&searchType=$searchType' class='prev-next'>&laquo;</a>";
        }

        if ($start > 1) {
            echo "<a href='?page=1&search=$searchText&searchType=$searchType'>1</a>";
            if ($start > 2) {
                echo "<span>...</span>";
            }
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($i == $page) {
                echo "<strong>$i</strong>";
            } else {
                echo "<a href='?page=$i&search=$searchText&searchType=$searchType'>$i</a>";
            }
        }

        if ($end < $total_pages) {
            if ($end < $total_pages - 1) {
                echo "<span>...</span>";
            }
            echo "<a href='?page=$total_pages&search=$searchText&searchType=$searchType'>$total_pages</a>";
        }

        if ($page < $total_pages) {
            echo "<a href='?page=" . ($page + 1) . "&search=$searchText&searchType=$searchType' class='prev-next'>&raquo;</a>";
        }

        echo "</div>";
        ?>
    </div>

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
