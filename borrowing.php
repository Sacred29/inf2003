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

                $client = new MongoDB\Client("mongodb+srv://inf2003-mongodev:toor@inf2003-part2.i7agx.mongodb.net/");
                $db = $client->eLibDatabase;
                $borrowedCollection = $db->Borrowed;
                $booklistCollection = $db->booklist;
                $date = date("Y-m-d");

                //update first
                $expiredRecords = $borrowedCollection->aggregate([
                    ['$match' => [
                        'userID' => $userId,
                        'expiryDate' => ['$lt' => $date],
                        'status' => 'Borrowed'
                    ]],
                    ['$lookup' => [
                        'from' => 'books',           // The collection to join
                        'localField' => 'isbn',          // Field in `borrowed` to match
                        'foreignField' => 'isbn',        // Field in `booklist` to match
                        'as' => 'bookDetails'            // Output array field
                    ]],
                    ['$unwind' => '$bookDetails'],       // Flatten the joined array
                ]);

                // Step 2: Process each record and update fields in borrowed and booklist
                foreach ($expiredRecords as $record) {
                    // Update the status in borrowed collection
                    $borrowedCollection->updateOne(
                        ['_id' => $record['_id']],
                        ['$set' => ['status' => 'Returned']]
                    );

                    // Increase the quantity in booklist collection
                    $borrowedCollection->updateOne(
                        ['isbn' => $record['isbn']],
                        ['$inc' => ['quantity' => 1]]
                    );
                }

                //show borrowed collection
                $borrowedRecords = $borrowedCollection->aggregate([
                        ['$match' => ['userID' => $userId]],
                        ['$lookup' => [
                            'from' => 'books',           // The collection to join
                            'localField' => 'isbn',          // Field in `borrowed` to match
                            'foreignField' => 'isbn',        // Field in `booklist` to match
                            'as' => 'bookDetails'            // Output array field
                        ]],
                        ['$unwind' => '$bookDetails'],       // Flatten the joined array
                        ['$sort' => ['expiryDate' => -1]]    // Sort by expiryDate in descending order
                    ]);

                // Step 4: Display results
                foreach ($borrowedRecords as $row) {
                    $borrowedID = $row['borrowID'];
                    $bookID = $row['isbn'];
                    $bookTitle = $row['bookDetails']['bookTitle'];
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
            }

            ?>
        </table>
        <br>
    </div>

</body>

</html>