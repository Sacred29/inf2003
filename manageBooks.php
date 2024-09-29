<?php
require_once __DIR__ . '/config.php';

function getAllBooksDetails()
{
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
                                        GROUP_CONCAT(DISTINCT a.authorName ORDER BY a.authorName) authors, 
                                        GROUP_CONCAT(DISTINCT g.genreName ORDER BY g.genreName) genres
                                   FROM Booklist b
                                   INNER JOIN bookAuthor ba ON b.ISBN = ba.book_id 
                                   INNER JOIN Authors a ON ba.author_id = a.authorID
                                   INNER JOIN bookGenre bg ON b.ISBN = bg.book_id 
                                   INNER JOIN Genres g ON bg.genre_id = g.genreID 
                                   GROUP BY b.ISBN;");
          $stmt->execute();
          $result = $stmt->get_result();
          if ($result->num_rows > 0) {
               foreach ($result as $row) {
                    echo "<tr onclick=\"window.location='editBook.php?isbn=" . $row['ISBN'] . "'\">";
                    echo "<td>" . $row['ISBN'] . "</td>";
                    echo "<td>" . $row['bookTitle'] . "</td>";
                    echo "<td>" . $row['publishDate'] . "</td>";
                    echo "</tr>";
               }
          } else {
               echo "<tr><td colspan='3'>Book List is Empty!</td></tr>";
          }

          $conn->close();
     }
}
?>

<head>
     <title>Manage All Books</title>
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
     <div class="w3-container">
          <br>
          <div class="book-header">
               <h2>All Books</h2>
               <a href="addBook.php">
                    <button class="btn-primary">Add new Book</button>
               </a>
          </div>
          <table class="w3-table-all">
               <tr class="w3-theme">
                    <th>ISBN</th>
                    <th>Book Title</th>
                    <th>Publish Date</th>
               </tr>
               <?php getAllBooksDetails(); ?>
          </table>
     </div>
</body>