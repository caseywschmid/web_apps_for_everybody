<?php
require_once "pdo.php";
require_once "util.php";

session_start();

// Will show all of the positions in an un-numbered list.

if ( isset($_POST['logout'] ) ) {
    header("Location: logout.php");
    return;
}

$profsql = "SELECT * FROM Profile WHERE profile_id = :id";
$stmt = $pdo->prepare($profsql);
$stmt->execute(array(":id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for user_id';
    header('Location: index.php');
    return;
}

$possql = "SELECT * FROM Position WHERE profile_id = :pid ORDER BY rank";
$stmt = $pdo->prepare($possql);
$stmt->execute(array(":pid" => $_GET['profile_id']));
$prof = $stmt->fetchall(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casey Schmid View Detail</title>
    <?php require_once "head.php"; ?>

</head>
<body>
    <div class="container">
        <h1>Detailed Profile View</h1>
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
        // If not logged in you see this... 
        if ( ! isset($_SESSION['name'])){ ?>
        <p><a href="login.php">Please log in</a></p>            
        <p>First Name: <?= $row['first_name']?></p>
        <p>Last Name: <?= $row['last_name']?></p>
        <p>Email: <?= $row['email']?></p>
        <p>Headline:<br/>
        <?= $row['headline']?></p>
        <p>Summary:<br/>
        <?= $row['summary']?></p>
        <p>Position</p>
        <ul>
        <?php foreach ($prof as $item) {
            echo '<li>'.$item['year'].': '.$item['description'].'</li>';
        } ?> 
        </ul>
        <p>
            <a href="index.php" target="_self">Back</a>
        </p>
        <?php } else { ?>
        <p>Logged In: <?= $_SESSION['name']?> </p>
        <p>First Name: <?= $row['first_name']?></p>
        <p>Last Name: <?= $row['last_name']?></p>
        <p>Email: <?= $row['email']?></p>
        <p>Headline:<br/>
        <?= $row['headline']?></p>
        <p>Summary:<br/>
        <?= $row['summary']?></p>
        <p>Position</p>
        <ul>
        <?php foreach ($prof as $item) {
            echo '<li>'.$item['year'].': '.$item['description'].'</li>';
        } ?> 
        </ul>
        <p>
            <a href="index.php" target="_self">Back</a>
        </p>
        <p>
            <a href="logout.php" target="_self">Logout</a>
        </p>
        <?php } ?>
    </div>
</body>
</html>
