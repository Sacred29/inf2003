<?php include 'inc/nav.php'; ?>
<?php
require 'vendor/autoload.php';
require_once __DIR__ . '/config.php';

// pagination
echo '<link rel="stylesheet" href="css/pagination.css">';

// Connect to Database
$client = new MongoDB\Client("mongodb+srv://inf2003-mongodev:toor@inf2003-part2.i7agx.mongodb.net/");
$db = $client->eLibDatabase;
$bookCollection = $db->books;

// Number of records to show per page
$limit = 14;

// Get the current page number from the query string or default to 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$count = $bookCollection->countDocuments();
$total_pages = ceil($count / $limit);

function getAllBooksDetails($limit, $offset)
{
     // Connect to Database
     $client = new MongoDB\Client("mongodb+srv://inf2003-mongodev:toor@inf2003-part2.i7agx.mongodb.net/");
     $db = $client->eLibDatabase;
     $bookCollection = $db->books;

     // Fetch all books from the database
     $bookList = $bookCollection->find(
          [],
          [
               'skip' => $offset,
               'limit' => $limit,
               'sort' => ['isbn' => 1] // Sort in ascending order by _id
          ]
     )->toArray();

     if (count($bookList) > 0) {
          foreach ($bookList as $book) {
               echo "<tr onclick=\"window.location='editBook.php?isbn=" . $book['isbn'] . "'\">";
               echo "<td>" . $book['isbn'] . "</td>";
               echo "<td>" . $book['title'] . "</td>";
               echo "<td>" . $book['publication_date']->toDateTime()->format('Y-m-d') . "</td>";
               echo "</tr>";
          }
     } else {
          echo "<tr><td colspan='3'>Book List is Empty!</td></tr>";
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
               <?php getAllBooksDetails($limit, $offset); ?>
          </table>
     </div>
</body>