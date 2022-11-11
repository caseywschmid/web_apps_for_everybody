<?php
require_once "pdo.php";
session_start();

// Demand a session
if ( ! isset($_SESSION['name'])) {
    die('ACCESS DENIED');
}
if ( isset($_POST['logout'] ) ) {
    header("Location: logout.php");
    return;
}

// Record Addition
if (isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage']) && isset($_POST['model'])) {
    // Error Checking and Data Validation
    if (strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 || strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header('Location: add.php');
        return;
    } elseif ( ! is_numeric($_POST['year']) || ! is_numeric($_POST['mileage'])){
        $_SESSION['error'] = "Mileage and Year must be numeric";
        header('Location: add.php');
        return;
    }  else {
        // Actual Insert
        $sqlin = "INSERT INTO autos (make, model, year, mileage) VALUES ( :make, :model, :year, :mile)";
        $instmt = $pdo->prepare($sqlin);
        $instmt->execute(array(
            ':make'=> htmlentities($_POST['make']),
            ':year'=> htmlentities($_POST['year']),
            ':mile'=> htmlentities($_POST['mileage']),
            ':model'=> htmlentities($_POST['model'])));
        $_SESSION['success'] = 'Record added';
    }
    header('Location: index.php');
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
            <h1>Add New Auto</h1>
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
                    <label for="model">Model:</label>
                    <input type="text" name="model"><br/>
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