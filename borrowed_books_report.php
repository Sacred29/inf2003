<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<!--?php include 'includes/head.php'; ?> -->

<head>
    <title>Borrowed Book Report</title>
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


    <?php
    include 'inc/nav.php';

    //if not admin send to index.php
    if (isset($_SESSION['type'])) {
        if ($_SESSION['type'] != 'Admin') {
            echo "<script>windows.location.href = 'index.php'</script>";
        }
    }
    require_once __DIR__ . '/config.php';
    ?>

    <div class="w3-container">
        <br>
        <h2>Borrowed Books Report</h2>
        <table class="w3-table-all">
            <tr class="w3-theme">
                <th>Book ID</th>
                <th>Book Name</th>
                <th>Borrowed Quantity</th>
            </tr>

            <?php
            //to be removed currently hardcoded to test query
            // $_SESSION['userId'] = 1;
            // $userId = $_SESSION['userId'];
            // require_once __DIR__ . '/config.php';

            getTotalBorrowedList();

            function getTotalBorrowedList()
            {
                global $userId;


                $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

                //check connection
                if ($conn->connect_error) {
                    $errormsg = "Connection Failed";
                    return;
                } else {

                    //prepare statement
                    $stmt = $conn->prepare("SELECT Borrowed.ISBN, Booklist.bookTitle, COUNT(*) AS totalBorrowed
                                                    FROM Borrowed
                                                    JOIN Booklist ON Borrowed.ISBN = Booklist.ISBN
                                                    WHERE MONTH(Borrowed.borrowedDate) = MONTH(CURRENT_DATE())
                                                    AND YEAR(Borrowed.borrowedDate) = YEAR(CURRENT_DATE())
                                                    GROUP BY Borrowed.ISBN, Booklist.bookTitle;");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        foreach ($result as $row) {
                            $bookID = $row['ISBN'];
                            $bookTitle = $row['bookTitle'];
                            $totalBorrowed = $row['totalBorrowed'];

                            echo "<tr>";
                            echo "<td>" . $bookID . "</td>";
                            echo "<td>" . $bookTitle . "</td>";
                            echo "<td>" . $totalBorrowed . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No Borrowed Books</td></tr>";
                    }

                    $conn->close();
                }
            }

            ?>
        </table>
    </div>

</body>

</html>