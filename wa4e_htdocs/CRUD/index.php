<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['logout'] ) ) {
    header("Location: logout.php");
    return;
}
if ( isset($_POST['edit']) && isset($_POST['autos_id']) ) {
    header("Location: edit.php");
    return;
}
if ( isset($_POST['delete']) && isset($_POST['autos_id']) ) {
    $_POST['delete'] = $_SESSION['delete'];
    $_POST['autos_id'] = $_SESSION['autos_id'];
    header("Location: delete.php");
    return;
}

$sqlout = "SELECT make, year, mileage, autos_id, model FROM autos ORDER BY make";
$outstmt = $pdo->query($sqlout);
$rows = $outstmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casey Schmid View</title>
</head>
<body>
        <div class="container">
            <h1>Welcome to the Automobiles Database</h1>
            <?php
            if ( isset($_SESSION['error']) ) {
                echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
                unset($_SESSION['error']);
            }
            if ( isset($_SESSION['success']) ) {
                echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
                unset($_SESSION['success']);
            }
            if ( isset($_SESSION['delete']) ) {
                echo('<p>'.htmlentities($_SESSION['delete'])."</p>\n");
                unset($_SESSION['delete']); 
            }
        
            // Check if we are logged in
            if ( ! isset($_SESSION['name'])){ ?>
                <p><a href="login.php">Please log in</a></p>
            <?php } elseif ($rows === false) { ?>
                <p>No rows found</p>
                <?php } else { ?>
                    <!-- View Table  -->
                    <p>
                        <table border="2">
                            <tr>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Year</th>
                                <th>Mileage</th>
                                <th>Action</th>
                            </tr>
                            <?php
                    foreach ( $rows as $row ) {
                        echo "<tr><td>";
                        echo($row['make']);
                        echo("</td><td>");
                        echo($row['model']);
                        echo("</td><td>");
                        echo($row['year']);
                        echo("</td><td>");
                        echo($row['mileage']);
                        echo("</td><td>");
                        echo('<a href="edit.php?autos_id='.$row['autos_id'].'">Edit</a> / ');
                        echo('<a href="delete.php?autos_id='.$row['autos_id'].'">Delete</a>');
                        echo("</td></tr>\n");
                    } ?>
                    </table>
                </p>
                <p>
                    <a href="add.php" target="_blank">Add New Entry</a>
                </p>
                <p>
                    <a href="logout.php" target="_blank">Logout</a>
                </p>
            <?php } ?>               
        </div>
    </body>
</html>