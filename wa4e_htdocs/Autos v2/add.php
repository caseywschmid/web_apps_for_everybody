<?php
session_start();
require_once "pdo.php";
// Demand a session
if ( ! isset($_SESSION['name'])) {
    die('Not logged in');
}
if ( isset($_POST['logout'] ) ) {
    header("Location: index.php");
    return;
}

// Record Addition
if (isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage'])) {
    // Error Checking and Data Validation
    if ( ! is_numeric($_POST['year']) || ! is_numeric($_POST['mileage'])){
        $_SESSION['error'] = "Mileage and Year must be numeric";
        header('Location: add.php');
        return;
    } elseif ( strlen($_POST['make']) < 1 ) {
        $_SESSION['error'] = "Make is required";
        header('Location: add.php');
        return;
    } else {
        // Actual Insert
        $sqlin = "INSERT INTO autos (make, year, mileage, url) VALUES ( :mk, :yr, :mi, :url)";
        $instmt = $pdo->prepare($sqlin);
        $instmt->execute(array(
            ':mk'=> htmlentities($_POST['make']),
            ':yr'=> htmlentities($_POST['year']),
            ':mi'=> htmlentities($_POST['mileage']),
            ':url'=> htmlentities($_POST['url'])));
        $_SESSION['success'] = 'Record inserted';
    }
    header('Location: view.php');
    return;
}
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
                <input type="submit" name="add" value="Add">
                <input type="submit" name="logout" value="Logout">
            </form>
        </div>
    </body>
</html>