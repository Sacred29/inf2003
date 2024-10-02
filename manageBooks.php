<?php
require_once __DIR__ . '/config.php';

$conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

     // pagination
     echo '<link rel="stylesheet" href="css/pagination.css">';
    // Number of records to show per page
    $limit = 14;

    // Get the current page number from the query string or default to 1
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;
    $count = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Booklist
                                   ");
     $row = mysqli_fetch_assoc($count);
     $total_records = $row['total'];
     $total_pages = ceil($total_records / $limit);

function getAllBooksDetails($conn, $limit, $page, $offset)
{
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
                                   GROUP BY b.ISBN
                                   LIMIT $limit OFFSET $offset;
                                   ");
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
     <!-- Pagination -->
     <?php
          // Pagination logic
          $adjacents = 2; // Number of adjacent pages to show
          $start = ($page > $adjacents) ? $page - $adjacents : 1;
          $end = ($page < $total_pages - $adjacents) ? $page + $adjacents : $total_pages;

          echo "<div class='pagination'>";

          // Previous button
          if ($page > 1) {
               echo "<a href='?page=" . ($page - 1) . "' class='prev-next'>&laquo;</a>";
          }

          // First page link if not in the range
          if ($start > 1) {
               echo "<a href='?page=1'>1</a>";
               if ($start > 2) {
                    echo "<span>...</span>"; // Ellipsis for skipped pages
               }
          }

    // Page number links
          for ($i = $start; $i <= $end; $i++) {
               if ($i == $page) {
                    echo "<strong>$i</strong>"; // Highlight current page
               } else {
                    echo "<a href='?page=$i'>$i</a>";
               }      
          }

    // Last page link if not in the range
          if ($end < $total_pages) {
               if ($end < $total_pages - 1) {
                    echo "<span>...</span>"; // Ellipsis for skipped pages
               }
               echo "<a href='?page=$total_pages'>$total_pages</a>";
          }

    // Next button
          if ($page < $total_pages) {
               echo "<a href='?page=" . ($page + 1) . "' class='prev-next'>&raquo;</a>";
          }

     echo "</div>";
    ?>
          <table class="w3-table-all">
               <tr class="w3-theme">
                    <th>ISBN</th>
                    <th>Book Title</th>
                    <th>Publish Date</th>
               </tr>
               <?php getAllBooksDetails($conn, $limit, $page, $offset); ?>
          </table>
     </div>
</body>