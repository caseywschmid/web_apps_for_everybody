<?php
require_once "pdo.php";
require_once "util.php";

session_start();

// Demand a session
if ( ! isset($_SESSION['name'])) {
    die('ACCESS DENIED');
}

// Record Deletion 
if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $sql = "DELETE FROM Profile WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':zip' => $_POST['profile_id']));
    $_POST['profile_id'] = $_SESSION['profile_id'];
    $_SESSION['delete'] = "Record deleted";
    header('Location: index.php');
    return;
}
if ( ! isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}

$sql = "SELECT * FROM Profile WHERE profile_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(":id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for user_id';
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
    <title>Delete Profile</title>
    <?php require_once "head.php"; ?>

</head>
<body>
    <div class="container">
        <h1>Confirm Deleting: </h1>
        <p>
            <label for="first_name">First Name:</label>
            <p><?= $row['first_name']?></p>
        </p>
        <p>
            <label for="last_name">Last Name:</label>
            <p><?= $row['last_name']?></p>
        </p>
        <p>
            <label for="email">Email:</label>
            <p><?= $row['email']?></p>
        </p>
        <p>
            <label for="headline">Headline:</label>
            <p><?= $row['headline']?></p>
        </p>
        <p>
            <label for="summary">Summary:</label>
            <p><?= $row['summary']?></p>
        </p>
        <form method="post">
            <input type="hidden" name="profile_id" value="<?= $row['profile_id']?>">
            <input type="submit" value="Delete" name="delete">
            <a href="index.php">Cancel</a> 
        </form>
    </div>    
</body>
</html>