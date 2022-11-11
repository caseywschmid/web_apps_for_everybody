<?php
require_once "pdo.php";
// Demand a GET parameter
if ( ! isset($_GET['name']) || strlen($_GET['name']) < 1  ) {
    die('Name parameter missing');
}
if ( isset($_POST['logout'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}
$failure = false;
$success = false;

// URL Data Validation 
if ( isset($_POST['url'] ) ) {
    if (! str_starts_with($_POST['url'],'http') || 
        ! str_starts_with($_POST['url'],'https')) {
        $failure = "Enter a valid URL";
    }
}

// Record Addition
if (isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage'])) {
    // Error Checking and Data Validation
    if ( ! is_numeric($_POST['year']) || ! is_numeric($_POST['mileage'])){
        $failure = "Mileage and Year must be numeric";
    } elseif ( strlen($_POST['make']) < 1 ) {
        $failure = "Make is required";
    } else {
        // Actual Insert
        $sqlin = "INSERT INTO autos (make, year, mileage, url) VALUES ( :mk, :yr, :mi, :url)";
        $instmt = $pdo->prepare($sqlin);
        $instmt->execute(array(
            ':mk'=> htmlentities($_POST['make']),
            ':yr'=> htmlentities($_POST['year']),
            ':mi'=> htmlentities($_POST['mileage']),
            ':url'=> htmlentities($_POST['url'])));
        $success = "Record inserted";
    }
}

// Record Deletion 
$deleted = false;
if ( isset($_POST['delete']) && isset($_POST['auto_id']) ) {
    $sqldel = "DELETE FROM autos WHERE auto_id = :zip";
    $delstmt = $pdo->prepare($sqldel);
    $delstmt->execute(array(
        ':zip' => $_POST['auto_id']));
    $deleted = "Record deleted";
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
        <title>Casey Schmid Autos</title>
    </head>
    <body>
        <div class="container">
            <h1>Tracking Autos for <?= htmlentities($_GET['name'])?></h1>
            <?php
            if ( $failure !== false ) {
                echo('<p style="color: red;">'.htmlentities($failure)."</p>\n");
            }
            if ( $success !== false ) {
                echo('<p style="color: green;">'.htmlentities($success)."</p>\n");
            }
            if ( $deleted !== false ) {
                echo('<p>'.htmlentities($deleted)."</p>\n");
            }
            ?>
            <form method="POST">
                <p>
                    <label for="make">Make:</label>
                    <input type="text" name="make"><br/> 
                </p>
                <p>
                    <label for="year">Year:</label>
                    <input type="text" name="year"><br/>
                </p>
                <p>
                    <label for="mileage">Mileage:</label>
                    <input type="text" name="mileage"><br/>
                </p>
                <p>
                    <label for="url">URL:</label>
                    <input type="text" name="url"><br/>
                </p>
                <input type="submit" name="add" value="Add">
                <input type="submit" name="logout" value="Logout">
            </form>
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
                        echo("</td><td>");
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