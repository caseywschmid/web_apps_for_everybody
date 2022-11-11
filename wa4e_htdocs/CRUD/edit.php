<?php
require_once "pdo.php";
session_start();

// Demand a session
if ( ! isset($_SESSION['name'])) {
    die('ACCESS DENIED');
}
if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}



// Record Edit
if (isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage']) && isset($_POST['model']) && isset($_POST['autos_id'])) {
    // Error Checking and Data Validation
    if (strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 || strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?autos_id=".$_REQUEST['autos_id']);
        return;
    }
    if ( ! is_numeric($_POST['year']) || ! is_numeric($_POST['mileage'])){
        $_SESSION['error'] = "Mileage and Year must be numeric";
        header("Location: edit.php?autos_id=".$_REQUEST['autos_id']);
        return;
    } else {
        // Actual Edit
        $sqlup = "UPDATE autos SET make = :make, model = :model, year = :year, mileage = :mile WHERE autos_id = :id";
        $upstmt = $pdo->prepare($sqlup);
        $upstmt->execute(array(
            ':make'=> htmlentities($_POST['make']),
            ':year'=> htmlentities($_POST['year']),
            ':mile'=> htmlentities($_POST['mileage']),
            ':model'=> htmlentities($_POST['model']),
            ':id' => $_POST['autos_id']));
        $_SESSION['success'] = 'Record edited';
    }
    header('Location: index.php');
    return;
}

$sqlout = "SELECT * FROM autos WHERE autos_id = :id";
$stmt = $pdo->prepare($sqlout);
$stmt->execute(array(":id" => $_GET['autos_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for autos_id';
    header('Location: index.php');
    return;
}


$make = htmlentities($row['make']);
$model = htmlentities($row['model']); 
$year = htmlentities($row['year']); 
$mile = htmlentities($row['mileage']);
$id = $row['autos_id']; 
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
            <h1>Edit Auto</h1>
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
            <form method="post">
                <p>
                    Make: 
                    <input type="text" name="make" value="<?= $make ?>"><br/> 
                </p>
                <p>
                    Model: 
                    <input type="text" name="model" value="<?= $model ?>"><br/>
                </p>
                <p>
                    Year:
                    <input type="text" name="year" value="<?= $year ?>"><br/>
                </p>
                <p>
                    Mileage: 
                    <input type="text" name="mileage" value="<?= $mile ?>"><br/>
                    <input type="hidden" name="autos_id" value="<?= $id ?>"><br/>
                </p>
                <input type="submit" name="add" value="Save">
                <input type="submit" name="cancel" value="Cancel">
            </form>
        </div>
    </body>
</html>