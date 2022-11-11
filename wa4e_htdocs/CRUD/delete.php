<?php
require_once "pdo.php";
session_start();

// Record Deletion 
if ( isset($_POST['delete']) && isset($_POST['autos_id']) ) {
    $sqldel = "DELETE FROM autos WHERE autos_id = :zip";
    $delstmt = $pdo->prepare($sqldel);
    $delstmt->execute(array(
        ':zip' => $_POST['autos_id']));
    $_POST['autos_id'] = $_SESSION['autos_id'];
    $_SESSION['delete'] = "Record deleted";
    header('Location: index.php');
    return;
}
if ( ! isset($_GET['autos_id'])) {
    $_SESSION['error'] = "Missing autos_id";
    header('Location: index.php');
    return;
}

$sql = "SELECT make, year, mileage, autos_id, model FROM autos WHERE autos_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(":id" => $_GET['autos_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false) {
    $_SESSION['error'] = 'Bad value for autos_id';
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
    <title>Casey Schmid Delete</title>
</head>
<body>
        <p>Confirm Deleting: </p>
        <p>
            <?= htmlentities($row['year']) ?> 
            <?= htmlentities($row['make']) ?> 
            <?= htmlentities($row['model']) ?> with  
            <?= htmlentities($row['mileage']) ?> miles
        </p>
        <form method="post">
            <input type="hidden" name="autos_id" value="<?= $row['autos_id']?>">
            <input type="submit" value="Delete" name="delete">
            <a href="index.php">Cancel</a> 
        </form>
    </body>
</html>