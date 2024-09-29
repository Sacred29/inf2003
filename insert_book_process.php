<?php
require_once __DIR__ . '/config.php';

$isbn = $_REQUEST['isbn'];
$bookTitle = $language = $publisher = $publishDate = "";
$quantity = $pageCount = 0;
$authors = $genres = $new_book_detail_error_msg = [];
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

function addNewBookDetails()
{
     global $isbn, $bookTitle, $language, $publisher, $publishDate, $quantity, $pageCount, $success, $new_new_new_book_detail_error_msg;

     $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

     //check connection
     if ($conn->connect_error) {
          array_push($new_book_detail_error_msg, "Connection Failed");
          return;
     } else {
          $stmt = $conn->prepare("
                         INSERT INTO Booklist (ISBN, bookTitle, language, publisher, publishDate, quantity, pageCount) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)
                         ");
          $stmt->bind_param('sssssii', $isbn, $bookTitle, $language, $publisher, $publishDate, $quantity, $pageCount);
          if (!$stmt->execute()) {
               array_push($new_book_detail_error_msg, "Execute failed: (" . $stmt->errno . ") " .  $stmt->error);
               $success = false;
          }

          $stmt->close();
     }
     $conn->close();
}

function insertBookAuthors()
{
     global $isbn, $authors, $success, $new_book_detail_error_msg;

     $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

     //check connection
     if ($conn->connect_error) {
          array_push($new_book_detail_error_msg, "Connection Failed");
          return;
     } else {
          foreach ($authors as $author) {
               $stmt1 = $conn->prepare("
                         SELECT authorID FROM Authors 
                         WHERE authorName = ?
                         ");
               $stmt1->bind_param('s', $author);
               $stmt1->execute();
               $result = $stmt1->get_result();
               if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $authorID = $row['authorID'];

                    insertSelectedBookAuthor($authorID);
               } else {
                    array_push($new_book_detail_error_msg, "Execute failed: (" . $stmt1->errno . ") " .  $stmt1->error);
                    $success = false;
               }

               $stmt1->close();
          }
     }
     $conn->close();
}

function insertBookGenres()
{
     global $isbn, $genres, $success, $new_book_detail_error_msg;

     $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

     //check connection
     if ($conn->connect_error) {
          array_push($new_book_detail_error_msg, "Connection Failed");
          return;
     } else {
          foreach ($genres as $genre) {
               $stmt1 = $conn->prepare("
                         SELECT genreID FROM Genres 
                         WHERE genreName = ?
                         ");
               $stmt1->bind_param('s', $genre);
               $stmt1->execute();
               $result = $stmt1->get_result();
               if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $genreID = $row['genreID'];

                    insertSelectedBookGenre($genreID);
               } else {
                    array_push($new_book_detail_error_msg, "Execute failed: (" . $stmt1->errno . ") " .  $stmt1->error);
                    $success = false;
               }

               $stmt1->close();
          }
     }
     $conn->close();
}

if ($success) {
     addNewBookDetails();
     insertBookAuthors();
     insertBookGenres();
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
               echo "<h4>Book Details updated successfully!</h4>";
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