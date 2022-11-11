<?php
require_once "pdo.php";
session_start();

// Record Deletion 
if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $sqldel = "DELETE FROM Profile WHERE profile_id = :zip";
    $delstmt = $pdo->prepare($sqldel);
    $delstmt->execute(array(
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

$sqlout = "SELECT * FROM Profile WHERE profile_id = :id";
$stmt = $pdo->prepare($sqlout);
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
    <title>Casey Schmid Delete Profile</title>
</head>
<body>
        <p>Confirm Deleting: </p>
        <p>
                <table border = "2">
                    <tr>
                        <th>First</th>
                        <th>Last</th>
                        <th>Email</th>
                        <th>Headline</th>
                        <th>Summary</th>
                    </tr>
                    <tr>
                        <td><?= $row['first_name']?></td>
                        <td><?= $row['last_name']?></td>
                        <td><?= $row['email']?></td>
                        <td><?= $row['headline']?></td>
                        <td><?= $row['summary']?></td>
                    </tr>
                </table>
            </p>
        <form method="post">
            <input type="hidden" name="profile_id" value="<?= $row['profile_id']?>">
            <input type="submit" value="Delete" name="delete">
            <a href="index.php">Cancel</a> 
        </form>
    </body>
</html>