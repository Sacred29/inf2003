<?php
if (session_status() == PHP_SESSION_NONE) {
     session_start();
}
require_once __DIR__ . '/config.php';

use MongoDB\BSON\UTCDateTime;

$isbn = $bookTitle = $language = $publisher = $publishDate = "";
$quantity = $pageCount = 0;
$authors = $genres = $new_book_detail_error_msg = array();
$success = true;

if (
     empty($_POST["isbn"]) or empty($_POST["bookTitle"]) or empty($_POST["language"]) or empty($_POST["publisher"]) or
     empty($_POST["publishDate"]) or empty($_POST["quantity"]) or empty($_POST["pageCount"])
) {
     $success = false;
     array_push($new_book_detail_error_msg, "Missing book informaitons!");
} else {
     $isbn = $_POST['isbn'];
     $bookTitle = $_POST["bookTitle"];
     $language = $_POST["language"];
     $publisher = $_POST["publisher"];
     $publishDate = $_POST["publishDate"];
     $quantity = $_POST["quantity"];
     $pageCount = $_POST["pageCount"];
}

if (empty($_POST["authorsArray"]) or empty($_POST["genresArray"])) {
     $success = false;
     array_push($new_book_detail_error_msg, "Missing genres/authors informaitons!");
} else {
     $authors = explode("/", $_POST["authorsArray"]);
     $genres = explode("/", $_POST["genresArray"]);
}

function addNewBookDetails()
{
     global $isbn, $bookTitle, $language, $publisher, $publishDate, $quantity, $pageCount, $authors, $genres, $success, $new_book_detail_error_msg;

     $publicationDate = new UTCDateTime((new DateTime($publishDate))->getTimestamp() * 1000);

     $newBookInfo = [
          'isbn' => (int)$isbn,
          'title' => $bookTitle,
          'publisher' => $publisher,
          'publication_date' => $publicationDate,
          'language' => $language,
          'quantity' => (int)$quantity,
          'page_count' => (int)$pageCount,
          'authors' => $authors,
          'genres' => $genres,
     ];

     // Connect to Database
     $client = new MongoDB\Client("mongodb+srv://inf2003-mongodev:toor@inf2003-part2.i7agx.mongodb.net/");
     $db = $client->eLibDatabase;
     $bookCollection = $db->books;

     $books = $bookCollection->find(['isbn' => (int)$isbn])->toArray();
     if (count($books) > 0) {
          $success = false;
          array_push($new_book_detail_error_msg, "Book Exists!");
     } else {
          $insertNewBook = $bookCollection->insertOne($newBookInfo);

          // Check if the insert was successful
          if ($insertNewBook->getInsertedCount() === 0) {
               $success = false;
               array_push($new_book_detail_error_msg, "Book insertion failed!");
          }
     }
}


if ($success) {
     addNewBookDetails();
}
?>

<head>
     <title>Add New Book</title>
     <meta charset="UTF-8">
     <link rel="stylesheet" href="css/gallery.css">
     <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
     <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-black.css">
     <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<?php include 'inc/nav.php'; ?>

<main data-scroll-section>
     <div class="d-flex flex-column justify-content-center align-items-center" style="height: 75vh;">
          <?php
          if ($success) {
               echo "<h4>Book was successfully added!</h4>";
               echo '<a href="manageBooks.php"><button class="btn btn-success mb-4" type="submit">Return to Manage Book</button></a>';
          } else {
               echo "<h1>Oops! Book update failed!</h1>";
               echo "<p class='h4'>The following input errors were detected:</p>";
               echo "<p>";
               foreach ($new_book_detail_error_msg as $error) {
                    echo $error . "<br>";
               };
               echo "</p>";
               echo "<form style='display: inline' action='addBook.php' method='get'><button class='btn btn-danger mb-4 btn-lg'>Return to Add Book</button></form>";
          }
          ?>
     </div>
</main>