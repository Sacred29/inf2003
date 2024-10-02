<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION)) {
     session_start();
}

$isbn = $bookTitle = $language = $publisher = $publishDate = "";
$quantity = $pageCount = 0;
$authors = $genres = [];

$langOptions = $genreOptions = [];

function getLangOption()
{
     global $langOptions;

     $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

     //check connection
     if ($conn->connect_error) {
          $errormsg = "Connection Failed";
          return;
     } else {
          $stmt = $conn->prepare("SELECT DISTINCT language FROM Booklist;");
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0) {
               foreach ($result as $row) {
                    array_push($langOptions, $row['language']);
               }
          } else {
               echo "<tr><td colspan='3'>Language List is Empty!</td></tr>";
          }
     }
}

function getGenreOption()
{
     global $genreOptions;

     $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

     //check connection
     if ($conn->connect_error) {
          $errormsg = "Connection Failed";
          return;
     } else {
          $stmt = $conn->prepare("SELECT DISTINCT genreName FROM Genres;");
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0) {
               foreach ($result as $row) {
                    array_push($genreOptions, $row['genreName']);
               }
          } else {
               echo "<tr><td colspan='3'>Genre List is Empty!</td></tr>";
          }
     }
}

getLangOption();
getGenreOption();

if (isset($_SESSION['authors'])) {
     $authors = $_SESSION['authors'];
}
// Add an author to the array if the form is submitted
if (isset($_POST['addAuthor'])) {
     $newAuthor = $_POST['author'];
     if (!empty($newAuthor)) {
          array_push($authors, $newAuthor);
          $_SESSION['authors'] = $authors;
     }
}
// Remove an author if the delete action is performed
if (isset($_POST['deleteAuthor'])) {
     $authorToDelete = $_POST['authorToDelete'];
     if (($key = array_search($authorToDelete, $authors)) !== false) {
          unset($authors[$key]);
          $_SESSION['authors'] = $authors;
     }
}

if (isset($_SESSION['genres'])) {
     $genres = $_SESSION['genres'];
}
// Add an genre to the array if the form is submitted
if (isset($_POST['addGenre'])) {
     $newGenre = $_POST['genre'];
     if (!empty($newGenre)) {
          array_push($genres, $newGenre);
          $_SESSION['genres'] = $genres;
     }
}
// Remove an genre if the delete action is performed
if (isset($_POST['deleteGenre'])) {
     $genreToDelete = $_POST['genreToDelete'];
     if (($key = array_search($genreToDelete, $genres)) !== false) {
          unset($genres[$key]);
          $_SESSION['genres'] = $genres;
     }
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

<body>
     <h2><?php echo $isbn ?></h2>
     <form action="insert_book_process.php" method="POST">
          <label for="isbn">ISBN:</label>
          <input type="text" id="isbn" name="isbn" required><br><br>

          <label for="bookTitle">Book Title:</label>
          <input type="text" id="bookTitle" name="bookTitle" required><br><br>

          <label for="quantity">Quantity:</label>
          <input type="number" id="quantity" name="quantity" min="0" required><br><br>

          <label for="language">Language:</label>
          <select name="language" id="language">
               <?php foreach ($langOptions as $langOption): ?>
                    <option value="<?php echo $langOption ?>"><?php echo $langOption ?></option>
               <?php endforeach; ?>
          </select>
          <br><br>

          <label for="publisher">Publisher:</label>
          <input type="text" id="publisher" name="publisher" required><br><br>

          <label for="publishDate">Publish Date:</label>
          <input type="date" id="publishDate" name="publishDate" required><br><br>

          <label for="pageCount">Page Count:</label>
          <input type="number" id="pageCount" name="pageCount" min="0" required><br><br>

          <input type="hidden" id="authorsArray" name="authorsArray" value='<?php echo implode('/', $authors) ?>'>
          <input type="hidden" id="genresArray" name="genresArray" value='<?php echo implode('/', $genres) ?>'>

          <input type="submit" value="Submit">
     </form>

     <br>
     <!-- Form to add a new author -->
     <form method="POST" id="authorForm">
          <label for="author">Author Name:</label>
          <input type="text" id="author" name="author" required>
          <button type="submit" name="addAuthor">Add Author</button>
     </form>
     <!-- List of authors added so far -->
     <ul id="authorsList">
          <?php foreach ($authors as $author): ?>
               <li>
                    <?php echo $author; ?>
                    <form method="POST" style="display: inline;">
                         <input type="hidden" name="authorToDelete" value="<?php echo $author; ?>">
                         <button type="submit" name="deleteAuthor">Delete</button>
                    </form>
               </li>
          <?php endforeach; ?>
     </ul>

     <br>
     <!-- Form to add a new author -->
     <form method="POST" id="genreForm">
          <label for="genre">Genre Name:</label>
          <select name="genre" id="genre">
               <?php foreach ($genreOptions as $genreOption): ?>
                    <option value="<?php echo $genreOption ?>"><?php echo $genreOption ?></option>
               <?php endforeach; ?>
          </select>
          <!-- <input type="text" id="genre" name="genre" required> -->
          <button type="submit" name="addGenre">Add Genre</button>
     </form>
     <!-- List of authors added so far -->
     <ul id="genresList">
          <?php foreach ($genres as $genre): ?>
               <li>
                    <?php echo $genre; ?>
                    <form method="POST" style="display: inline;">
                         <input type="hidden" name="genreToDelete" value="<?php echo $genre; ?>">
                         <button type="submit" name="deleteGenre">Delete</button>
                    </form>
               </li>
          <?php endforeach; ?>
     </ul>
</body>