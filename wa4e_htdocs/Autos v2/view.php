<?php
session_start();
require_once "pdo.php";
// Demand a session
if ( ! isset($_SESSION['name'])) {
    die('Not logged in');
}
if ( isset($_POST['logout'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}
// Record Deletion 
if ( isset($_POST['delete']) && isset($_POST['auto_id']) ) {
    $sqldel = "DELETE FROM autos WHERE auto_id = :zip";
    $delstmt = $pdo->prepare($sqldel);
    $delstmt->execute(array(
        ':zip' => $_POST['auto_id']));
    $_POST['auto_id'] = $_SESSION['auto_id'];
    $_SESSION['delete'] = "Record deleted";
    header('Location: view.php');
    return;
}

$sqlout = "SELECT make, year, mileage, auto_id, url FROM autos ORDER BY make";
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
            <h1>Tracking Autos for <?= htmlentities($_SESSION['name'])?></h1>
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
            ?>
            <p>
                <a href="add.php" target="_blank">Add New</a>
                |
                <a href="logout.php" target="_blank">Logout</a>
            </p>

            <!-- View Table  -->
            <h2>Automobiles</h2>
            <p>
                <table border="2">
                    <?php
                    foreach ( $rows as $row ) {
                        echo "<tr><td>";
                        echo($row['make']);
                        echo("</td><td>");
                        echo($row['year']);
                        echo("</td><td>");
                        echo($row['mileage']);
                        echo("</td><td>");
                        echo($row['url']);
                        echo('<form method="post"><input type="hidden" ');
                        echo('name="auto_id" value="'.$row['auto_id'].'">'."\n");
                        echo('<input type="submit" value="Del" name="delete">');
                        echo("\n</form>\n");
                        echo("</td></tr>\n");
                    }
                    ?>
            </table>
        </p>
        </div>
    </body>
</html>