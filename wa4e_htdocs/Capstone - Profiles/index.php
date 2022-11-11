<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if ( isset($_POST['logout'] ) ) {
    header("Location: logout.php");
    return;
}
if ( isset($_POST['edit']) && isset($_POST['user_id']) ) {
    header("Location: edit.php");
    return;
}
if ( isset($_POST['delete']) && isset($_POST['user_id']) ) {
    $_POST['delete'] = $_SESSION['delete'];
    header("Location: delete.php");
    return;
}

$sqlout = "SELECT first_name, last_name, profile_id, user_id FROM Profile";
$outstmt = $pdo->query($sqlout);
$rows = $outstmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casey Schmid View Profile</title>
    <?php require_once "head.php"; ?>
</head>
<body>
    <div class="container">
        <h1>Welcome to the Profile Database</h1>
        <?php
        flashMessages();
        if ( isset($_SESSION['delete']) ) {
            echo('<p>'.htmlentities($_SESSION['delete'])."</p>\n");
            unset($_SESSION['delete']); 
        }
    
        // If not logged in you see this... 
        if ( ! isset($_SESSION['name'])){ ?>
            <p><a href="login.php">Please log in</a></p>
            <p>
                <table border = "2">
                    <tr>
                        <th>First</th>
                        <th>Last</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    foreach ( $rows as $row ) {
                    echo "<tr><td>";
                    echo($row['first_name']);
                    echo("</td><td>");
                    echo($row['last_name']);
                    echo("</td><td>");
                    echo('<a target="_self" href="view.php?profile_id='.$row['profile_id'].'">View</a>');
                    echo("</td></tr>\n");
                    } ?>
                </table>
            </p>
            
  <?php } elseif ($rows === false) { ?>
            <p>No users found</p>
  <?php } else { ?>
            <p>Logged In: <?= $_SESSION['name']?> </p>
            <p>
                <table border = "2">
                    <tr>
                        <th>First</th>
                        <th>Last</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    foreach ( $rows as $row ) {
                    echo "<tr><td>";
                    echo($row['first_name']);
                    echo("</td><td>");
                    echo($row['last_name']);
                    echo("</td><td>");
                    echo('<a href="view.php?profile_id='.$row['profile_id'].'">View</a>');
                    if ($row['user_id'] == $_SESSION['user_id']){
                        echo(' | ');
                        echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a>');
                        echo(' | ');
                        echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
                    }
                    echo("</td></tr>\n");
                    } ?>
                </table>
            </p>
            <p>
                <a href="add.php" target="_self">Add New Entry</a>
            </p>
            <p>
                <a href="logout.php" target="_self">Logout</a>
            </p>
            <p>You should only be able to edit and delete profiles that the currently logged in user created. </p>                         
        <?php } ?>
    </div>
</body>
</html>