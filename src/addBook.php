<?php
require_once __DIR__ . '/config.php';
require 'vendor/autoload.php';

if (!isset($_SESSION)) {
     session_start();
}

$isbn = $bookTitle = $language = $publisher = $publishDate = "";
$quantity = $pageCount = 0;
$authors = $genres = array();

$langOptions = $genreOptions = array();

function getLangOption()
{
     global $langOptions;

     // Connect to Database
     $client = new MongoDB\Client("mongodb+srv://inf2003-mongodev:toor@inf2003-part2.i7agx.mongodb.net/");
     $db = $client->eLibDatabase;
     $bookCollection = $db->books;

     $uniqueLanguages = $bookCollection->distinct('language');

     // echo count($uniqueLanguages);
     if (count($uniqueLanguages) > 0) {
          $langOptions = is_array($uniqueLanguages) ? $uniqueLanguages : (array)$uniqueLanguages;
     }
}

function getGenreOption()
{
     global $genreOptions;

     // Connect to Database
     $client = new MongoDB\Client("mongodb+srv://inf2003-mongodev:toor@inf2003-part2.i7agx.mongodb.net/");
     $db = $client->eLibDatabase;
     $bookCollection = $db->books;

     // Define the aggregation pipeline
     $genrePipeline = [
          ['$unwind' => '$genres'], // Deconstructs the 'language' array
          ['$group' => ['_id' => '$genres']], // Groups by unique 'language' values
          ['$sort' => ['_id' => 1]] // Sorts results in ascending order
     ];

     $uniqueGenres = $bookCollection->distinct("genres");

     if (count($uniqueGenres) > 0) {
          $genreOptions = is_array($uniqueGenres) ? $uniqueGenres : (array)$uniqueGenres;
     }
}

getLangOption();
getGenreOption();
?>

<head>
     <title>Add New Book</title>
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
          <br>
          <!-- Book form with border -->
          <div class="form-container">
               <form action="insert_book_process.php" method="POST">
                    <label for="isbn">ISBN:</label>
                    <input type="text" id="isbn" name="isbn" required><br>

                    <label for="bookTitle">Book Title:</label>
                    <input type="text" id="bookTitle" name="bookTitle" required><br>

                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="0" required><br>

                    <label for="language">Language:</label>
                    <select name="language" id="language">
                         <?php foreach ($langOptions as $langOption): ?>
                              <option value="<?php echo $langOption ?>"><?php echo $langOption ?></option>
                         <?php endforeach; ?>
                    </select><br>

                    <label for="publisher">Publisher:</label>
                    <input type="text" id="publisher" name="publisher" required><br>

                    <label for="publishDate">Publish Date:</label>
                    <input type="date" id="publishDate" name="publishDate" required><br>

                    <label for="pageCount">Page Count:</label>
                    <input type="number" id="pageCount" name="pageCount" min="0" required><br>

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