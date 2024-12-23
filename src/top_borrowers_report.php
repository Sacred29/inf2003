<?php include 'inc/nav.php';?>
<?php require 'vendor/autoload.php';

use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;
?>
<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<!--?php include 'includes/head.php'; ?> -->

<head>
    <title>Top Borrower Report</title>
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
                <th>User ID</th>
                <th>Name</th>
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

                $client = new MongoDB\Client("mongodb+srv://inf2003-mongodev:toor@inf2003-part2.i7agx.mongodb.net/");
                $db = $client->eLibDatabase;
                $borrowedCollection = $db->Borrowed;
                $booklistCollection = $db->books;
                $currentMonth = date('m');
                $currentYear = date('Y');

                // Calculate the start and end of the current month as MongoDB UTCDateTime objects
                $startDate = new UTCDateTime(strtotime("$currentYear-$currentMonth-01 00:00:00") * 1000);
                $endDate = new UTCDateTime(strtotime("$currentYear-" . ($currentMonth + 1) . "-01 00:00:00") * 1000);

                $pipeline = [
                    [
                        '$match' => [
                            'borrowedDate' => [
                                '$gte' => $startDate,
                                '$lt' => $endDate
                            ]
                        ]
                    ],
                    [
                        '$lookup' => [
                            'from' => 'Users',
                            'localField' => 'userID',
                            'foreignField' => 'userID',
                            'as' => 'userDetails' 
                        ]

                    ],
                    [
                        '$unwind' => '$userDetails'
                    ],
                    [
                        '$group' => [
                            '_id' => [
                                'userID' => '$userID',
                                'borrowerName' => ['$concat' => ['$userDetails.firstName', ' ', '$userDetails.lastName']]
                            ],
                            'totalBorrowed' => ['$sum' => 1]
                        ]
                    ],

                    [
                        '$sort' => [
                            'totalBorrowed' => -1
                        ]
                    ],
                    [
                        '$limit' => 5
                    ]
                ];

                // Execute the pipeline
    $result = $borrowedCollection->aggregate($pipeline);
    $resultArray = iterator_to_array($result);

    // Display the results
    if (count($resultArray) > 0) {
        foreach ($resultArray as $row) {
            $userID = $row['_id']['userID'];
            $borrowerName = $row['_id']['borrowerName'];
            $totalBorrowed = $row['totalBorrowed'];

            echo "<tr>";
            echo "<td>" . $userID . "</td>";
            echo "<td>" . $borrowerName . "</td>";
            echo "<td>" . $totalBorrowed . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No Borrowed Books</td></tr>";
    }
            }

            ?>
        </table>
    </div>

</body>

</html>