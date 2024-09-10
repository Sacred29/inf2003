<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<!--?php include 'includes/head.php'; ?> -->
<link rel="stylesheet" href="css/gallery.css">

<body>
    <?php include 'inc/nav.php'; ?>
    <table class="w3-table-all">
        <tr class="w3-theme">
            <th>BookID</th>
            <th>Book Name</th>
            <th>Borrowed Date</th>
            <th>Expiry Date</th>
            <th>Status</th>
            <th>Read</th>
        </tr>

        <?php

        session_start();
        $userId = "";
        $userId = $_SESSION('userID');

        function getBorrowedList() {
            global $userId;

            $config = parse_ini_file('var/www/private/db-config.ini');
            //if config is empty
            if (!$config){
                $errormsg = "Failed to read database config file";
                return;
            }
            else {
                $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);

                //check connection
                if ($conn->connect_error){
                    $errormsg = "Connection Failed";
                    return;
                }else {

                    //prepare statement
                    $stmt = $conn->prepare("SELECT * FROM Borrowed where userID=?");
                    $stmt->bind_param("s", $userId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    

                }
            }
        }

        ?>

    </table>

</body>

</html>