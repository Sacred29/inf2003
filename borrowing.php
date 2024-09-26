<?php
session_start();
?>

<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<!--?php include 'includes/head.php'; ?> -->

<head>
    <title>Borrowed Book List</title>
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


    <?php include 'inc/nav.php'; ?>
    <div class="w3-container">
        <br>
        <table class="w3-table-all">
            <tr class="w3-theme">
                <th>Borrow ID</th>
                <th>Book ID</th>
                <th>Book Name</th>
                <th>Borrowed Date</th>
                <th>Expiry Date</th>
                <th>Status</th>
            </tr>

            <?php
            //to be removed currently hardcoded to test query
            $userId = $_SESSION['userId'];
            require_once __DIR__ . '/config.php';

            getBorrowedList();

            function getBorrowedList()
            {
                global $userId;


                $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

                //check connection
                if ($conn->connect_error) {
                    $errormsg = "Connection Failed";
                    return;
                } else {

                    //update expired
                    $stmt = $conn->prepare("UPDATE Borrowed INNER JOIN Booklist ON Borrowed.ISBN = Booklist.ISBN set quantity = quantity+1, status = 'Returned' where userID=? AND expiryDate < ? AND status ='Borrowed'");
                    $date = date("Y-m-d");
                    $stmt->bind_param("ss", $userId, $date);
                    $stmt->execute();

                    //get all records of borrowed from user
                    $stmt = $conn->prepare("SELECT * FROM Borrowed INNER JOIN Booklist ON Borrowed.ISBN = Booklist.ISBN where userID=? order by expiryDate desc");
                    $stmt->bind_param("s", $userId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    foreach ($result as $row) {
                        $borrowedID = $row['borrowID'];
                        $bookID = $row['ISBN'];
                        $bookTitle = $row['bookTitle'];
                        $borrowedDate = $row['borrowedDate'];
                        $expiryDate = $row['expiryDate'];
                        $status = $row['status'];

                        echo "<tr>";
                        echo "<td>" . $borrowedID . "</td>";
                        echo "<td>" . $bookID . "</td>";
                        echo "<td>" . $bookTitle . "</td>";
                        echo "<td>" . $borrowedDate . "</td>";
                        echo "<td>" . $expiryDate . "</td>";
                        echo "<td>" . $status . "</td>";
                        echo "</tr>";
                    }
                    $conn->close();
                }
            }

            ?>
        </table>
    </div>

</body>

</html>