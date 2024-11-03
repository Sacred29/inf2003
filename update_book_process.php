<?php include 'inc/nav.php'; ?>
<?php
require_once __DIR__ . '/config.php';

use MongoDB\BSON\UTCDateTime;

$isbn = $_REQUEST['isbn'];
$bookTitle = $language = $publisher = $publishDate = "";
$quantity = $pageCount = 0;
$authors = $genres = $book_detail_error_msg = array();
$success = true;

$bookTitle = $_POST["bookTitle"];
$language = $_POST["language"];
$publisher = $_POST["publisher"];
$publishDate = $_POST["publishDate"];
$quantity = $_POST["quantity"];
$pageCount = $_POST["pageCount"];
$authors = explode("/", $_POST["authorsArray"]);
$genres = explode("/", $_POST["genresArray"]);


if (
     empty($_POST["bookTitle"]) or empty($_POST["language"]) or empty($_POST["publisher"]) or
     empty($_POST["publishDate"]) or empty($_POST["quantity"]) or empty($_POST["pageCount"])
) {
     $success = false;
     array_push($book_detail_error_msg, "Missing book informaitons!");
} else {
     $bookTitle = $_POST["bookTitle"];
     $language = $_POST["language"];
     $publisher = $_POST["publisher"];
     $publishDate = $_POST["publishDate"];
     $quantity = $_POST["quantity"];
     $pageCount = $_POST["pageCount"];
}

if (empty($_POST["authorsArray"]) or empty($_POST["genresArray"])) {
     $success = false;
     array_push($book_detail_error_msg, "Missing genres/authors informaitons!");
} else {
     $authors = explode("/", $_POST["authorsArray"]);
     $genres = explode("/", $_POST["genresArray"]);
}

function updateBookDetails()
{
     global $isbn, $bookTitle, $language, $publisher, $publishDate, $quantity, $pageCount, $authors, $genres, $success, $book_detail_error_msg;

     $publicationDate = new UTCDateTime((new DateTime($publishDate))->getTimestamp() * 1000);

     $editedBookInfo = [
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

     // Perform the update
     $updateResult = $bookCollection->updateOne(
          ['isbn' => (int)$isbn],           // Filter: Find document by ISBN
          ['$set' => $editedBookInfo]       // Update operation: Set new values
     );

     // Check if the update was successful
     if ($updateResult->getMatchedCount() === 0) {
          array_push($book_detail_error_msg, "No book found with the specified ISBN, or no changes were made.");
          $success = false;
     }
}

if ($success) {
     updateBookDetails();
}
?>

<head>
     <title>Updating</title>
     <meta charset="UTF-8">
     <link rel="stylesheet" href="css/gallery.css">
     <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
     <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-black.css">
     <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>



<main data-scroll-section>
     <div class="d-flex flex-column justify-content-center align-items-center" style="height: 75vh;">
          <?php
          if ($success) {
               echo "<h4>Book details updated successfully!</h4>";
               echo '<a href="manageBooks.php"><button class="btn btn-success mb-4" type="submit">Return to Manage Book</button></a>';
          } else {
               echo "<h1>Oops! Book update failed!</h1>";
               echo "<p class='h4'>The following input errors were detected:</p>";
               echo "<p>";
               foreach ($book_detail_error_msg as $error) {
                    echo $error . "<br>";
               };
               echo "</p>";
               echo "<form style='display: inline' action='editBook.php?isbn=" . urlencode($isbn) . "' method='post'><button class='btn btn-danger mb-4 btn-lg'>Return to Edit Book</button></form>";
          }
          ?>
     </div>
</main>