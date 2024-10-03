<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION)) {
     session_start();
}

$isbn = $_REQUEST['isbn'];
$bookTitle = $language = $publisher = $publishDate = "";
$quantity = $pageCount = 0;
$authors = $genres = array();

$langOptions = $genreOptions = array();

function getBookDetails()
{
     global $isbn, $bookTitle, $language, $publisher, $publishDate, $quantity, $pageCount, $authors, $genres;

     $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

     //check connection
     if ($conn->connect_error) {
          $errormsg = "Connection Failed";
          return;
     } else {
          //prepare statement
          $stmt = $conn->prepare("SELECT b.ISBN, 
	                              MAX(b.bookTitle) AS bookTitle, MAX(b.quantity) AS quantity, 
	                              MAX(b.language) AS language, MAX(b.publisher) AS publisher, 
	                              MAX(b.publishDate) AS publishDate, MAX(b.pageCount) AS pageCount, 
                                   GROUP_CONCAT(DISTINCT a.authorName ORDER BY a.authorName SEPARATOR '/') AS authors, 
                                   GROUP_CONCAT(DISTINCT g.genreName ORDER BY g.genreName SEPARATOR '/') AS genres 
                                   FROM Booklist b 
                                   INNER JOIN bookAuthor ba ON b.ISBN = ba.book_id 
                                   INNER JOIN Authors a ON ba.author_id = a.authorID 
                                   INNER JOIN bookGenre bg ON b.ISBN = bg.book_id 
                                   INNER JOIN Genres g ON bg.genre_id = g.genreID 
                                   WHERE b.ISBN = ? 
                                   GROUP BY b.ISBN");
          $stmt->bind_param('s', $isbn);
          $stmt->execute();
          $result = $stmt->get_result();
          if ($result->num_rows > 0) {
               $row = $result->fetch_assoc();

               $bookTitle = $row["bookTitle"];
               $publishDate = $row["publishDate"];
               $publisher = $row["publisher"];
               $language = $row["language"];
               $pageCount = $row["pageCount"];
               $quantity = $row["quantity"];

               $authors = explode("/", $row["authors"]);
               $genres = explode("/", $row["genres"]);
          } else {
               echo "<tr><td colspan='3'>Book List is Empty!</td></tr>";
          }

          $conn->close();
     }
}

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

getBookDetails();
getLangOption();
getGenreOption();
?>

<head>
     <title>Edit Book Information</title>
     <meta charset="UTF-8">
     <link rel="stylesheet" href="css/bookForms.css">
     <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
     <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-black.css">
     <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">

     <!-- Add jQuery script here -->
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script src="js/process_authors.js"></script>
     <script src="js/process_genres.js"></script>
</head>

<?php include 'inc/nav.php'; ?>

<body>
     <div class="form-wrapper">
          <h2>ISBN: <?php echo $isbn ?></h2>

          <!-- Book form with border -->
          <div class="form-container">
               <form action="update_book_process.php?isbn=<?php echo $isbn ?>" method="POST">

                    <label for="bookTitle">Book Title:</label>
                    <input type="text" id="bookTitle" name="bookTitle" value="<?php echo $bookTitle ?>" required><br>

                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="0" value="<?php echo $quantity ?>" required><br>

                    <label for="language">Language:</label>
                    <select name="language" id="language">
                         <?php foreach ($langOptions as $langOption): ?>
                              <?php if ($langOption == $language) { ?>
                                   <option value="<?php echo $langOption ?>" selected><?php echo $langOption ?></option>
                              <?php } else { ?>
                                   <option value="<?php echo $langOption ?>"><?php echo $langOption ?></option>
                              <?php } ?>
                         <?php endforeach; ?>
                    </select><br>

                    <label for="publisher">Publisher:</label>
                    <input type="text" id="publisher" name="publisher" value="<?php echo $publisher ?>" required><br>

                    <label for="publishDate">Publish Date:</label>
                    <input type="date" id="publishDate" name="publishDate" value="<?php echo $publishDate ?>" required><br>

                    <label for="pageCount">Page Count:</label>
                    <input type="number" id="pageCount" name="pageCount" min="0" value="<?php echo $pageCount ?>" required><br>

                    <input type="hidden" id="authorsArray" name="authorsArray" value='<?php echo implode('/', $authors) ?>'>
                    <input type="hidden" id="genresArray" name="genresArray" value='<?php echo implode('/', $genres) ?>'>

                    <input type="submit" value="Submit">
               </form>
          </div>

          <!-- Author form with border -->
          <div class="form-container">
               <form method="POST" id="authorForm">
                    <label for="author">Author Name:</label>
                    <input type="text" id="author" name="author" required><br>
                    <button type="submit" name="addAuthor">Add Author</button>
               </form>

               <ul id="authorsList">
                    <?php foreach ($authors as $author): ?>
                         <li>
                              <?php echo $author; ?>
                              <button class="deleteAuthorBtn" data-author="<?php echo $author; ?>">Delete</button>
                         </li>
                    <?php endforeach; ?>
               </ul>
          </div>

          <!-- Genre form with border -->
          <div class="form-container">
               <form method="POST" id="genreForm">
                    <label for="genre">Genre Name:</label>
                    <select name="genre" id="genre">
                         <?php foreach ($genreOptions as $genreOption): ?>
                              <option value="<?php echo $genreOption ?>"><?php echo $genreOption ?></option>
                         <?php endforeach; ?>
                    </select><br>
                    <button type="submit" name="addGenre">Add Genre</button>
               </form>

               <ul id="genresList">
                    <?php foreach ($genres as $genre): ?>
                         <li>
                              <?php echo $genre; ?>
                              <button class="deleteGenreBtn" data-genre="<?php echo $genre; ?>">Delete</button>
                         </li>
                    <?php endforeach; ?>
               </ul>
          </div>
     </div>
</body>