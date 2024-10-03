<?php
require_once __DIR__ . '/config.php';

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

function insertSelectedBookAuthor($author_id)
{
     global $isbn, $success, $new_book_detail_error_msg;

     $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

     //check connection
     if ($conn->connect_error) {
          array_push($new_book_detail_error_msg, "Connection Failed");
          return;
     } else {
          $stmt = $conn->prepare("
                         INSERT INTO bookAuthor (book_id, author_id) 
                         VALUES (?, ?)
                         ");
          $stmt->bind_param('ss', $isbn, $author_id);
          if (!$stmt->execute()) {
               array_push($new_book_detail_error_msg, "Execute failed: (" . $stmt->errno . ") " .  $stmt->error);
               $success = false;
          }

          $stmt->close();
     }
     $conn->close();
}

function insertSelectedBookGenre($genre_id)
{
     global $isbn, $success, $new_book_detail_error_msg;

     $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
     //check connection
     if ($conn->connect_error) {
          array_push($new_book_detail_error_msg, "Connection Failed");
          return;
     } else {
          $stmt = $conn->prepare("
                         INSERT INTO bookGenre (book_id, genre_id) 
                         VALUES (?, ?)
                         ");
          $stmt->bind_param('ss', $isbn, $genre_id);
          if (!$stmt->execute()) {
               array_push($new_book_detail_error_msg, "Execute failed: (" . $stmt->errno . ") " .  $stmt->error);
               $success = false;
          }

          $stmt->close();
     }
     $conn->close();
}

function getAuthorID($authorName)
{
     global $success, $new_book_detail_error_msg;

     $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

     //check connection
     if ($conn->connect_error) {
          array_push($new_book_detail_error_msg, "Connection Failed");
          return;
     } else {
          $stmt = $conn->prepare("SELECT authorID FROM Authors WHERE authorName = ?");
          $stmt->bind_param('s', $authorName);
          $stmt->execute();
          $result = $stmt->get_result();
          if ($result->num_rows > 0) {
               $row = $result->fetch_assoc();
               $authorID = $row['authorID'];
          } else if ($result->num_rows == 0) {
               $authorID = -1;
          } else {
               array_push($new_book_detail_error_msg, "Execute failed: (" . $stmt->errno . ") " .  $stmt->error);
               $success = false;
          }

          $stmt->close();
     }
     $conn->close();

     return $authorID;
}

function getGenreID($genreName)
{
     global $genres, $success, $new_book_detail_error_msg;

     $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

     //check connection
     if ($conn->connect_error) {
          array_push($new_book_detail_error_msg, "Connection Failed");
          return;
     } else {
          $stmt = $conn->prepare("SELECT genreID FROM Genres WHERE genreName = ?");
          $stmt->bind_param('s', $genreName);
          $stmt->execute();
          $result = $stmt->get_result();
          if ($result->num_rows > 0) {
               $row = $result->fetch_assoc();
               $genreID = $row['genreID'];
          } else if ($result->num_rows == 0) {
               $genreID = -1;
          } else {
               array_push($new_book_detail_error_msg, "Execute failed: (" . $stmt->errno . ") " .  $stmt->error);
               $success = false;
               $genreID = -1;
          }

          $stmt->close();
     }
     $conn->close();

     return $genreID;
}

function deleteAllSelectedBookAuthors()
{
     global $isbn, $success, $book_detail_error_msg;

     $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
     if ($conn->connect_error) {
          array_push($book_detail_error_msg, "Connection Failed");
          return;
     } else {
          $stmt = $conn->prepare("
                         DELETE FROM bookAuthor 
                         WHERE book_id = ?
                         ");
          $stmt->bind_param('s', $isbn);
          $stmt->execute();
          if (!$stmt->execute()) {
               array_push($book_detail_error_msg, "Execute failed: (" . $stmt->errno . ") " .  $stmt->error);
               $success = false;
          }
          $stmt->close();
     }
}

function deleteAllSelectedBookGenres()
{
     global $isbn, $success, $book_detail_error_msg;

     $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
     if ($conn->connect_error) {
          array_push($book_detail_error_msg, "Connection Failed");
          return;
     } else {
          $stmt = $conn->prepare("
                         DELETE FROM bookGenre 
                         WHERE book_id = ?
                         ");
          $stmt->bind_param('s', $isbn);
          $stmt->execute();
          if (!$stmt->execute()) {
               array_push($book_detail_error_msg, "Execute failed: (" . $stmt->errno . ") " .  $stmt->error);
               $success = false;
          }
          $stmt->close();
     }
}

function updateBookDetails()
{
     global $isbn, $bookTitle, $language, $publisher, $publishDate, $quantity, $pageCount, $success, $book_detail_error_msg;

     $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

     //check connection
     if ($conn->connect_error) {
          array_push($book_detail_error_msg, "Connection Failed");
          return;
     } else {
          $stmt = $conn->prepare("
                         UPDATE Booklist 
                         SET bookTitle = ?, language = ?, publisher = ?, publishDate = ?, quantity = ?, pageCount= ? 
                         WHERE ISBN = ?
                         ");
          $stmt->bind_param('ssssiis', $bookTitle, $language, $publisher, $publishDate, $quantity, $pageCount, $isbn);
          if (!$stmt->execute()) {
               array_push($book_detail_error_msg, "Execute failed: (" . $stmt->errno . ") " .  $stmt->error);
               $success = false;
          }

          $stmt->close();
     }
     $conn->close();
}

function updateBookAuthors($insert)
{
     global $authors, $success, $book_detail_error_msg;

     deleteAllSelectedBookAuthors();
     foreach ($authors as $author) {
          $authorID = getAuthorID($author);
          if ($authorID >= 0) {
               if ($insert === true) {
                    insertSelectedBookAuthor($authorID);
               }
          } else {
               array_push($book_detail_error_msg, "Failed to insert Book-Author Entry!");
               $success = false;
          }
     }
}

function updateBookGenres()
{
     global $genres, $success, $book_detail_error_msg;

     deleteAllSelectedBookGenres();
     foreach ($genres as $genre) {
          $genreID = getGenreID($genre);
          if ($genreID >= 0) {
               insertSelectedBookGenre($genreID);
          } else {
               array_push($book_detail_error_msg, "Failed to insert Book-Author Entry!");
               $success = false;
          }
     }
}

if ($success) {
     updateBookAuthors(false);
     updateBookDetails();
     updateBookAuthors(true);
     updateBookGenres();
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

<?php include 'inc/nav.php'; ?>

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
               echo "<form style='display: inline' action='update_book_process.php?isbn=" . $isbn . "' method='get'><button class='btn btn-danger mb-4 btn-lg'>Return to Edit Book</button></form>";
          }
          ?>
     </div>
</main>