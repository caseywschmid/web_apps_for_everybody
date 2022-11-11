<?php
require_once "pdo.php";
require_once "util.php";

session_start();

if ( isset($_POST['logout'] ) ) {
    header("Location: logout.php");
    return;
}
// Retrieve Profile Information
$sql = "SELECT * FROM Profile WHERE profile_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(":id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for user_id';
    header('Location: index.php');
    return;
}

// Retrieve Education Information
$sql = "SELECT Education.year, Institution.name 
        FROM Education JOIN Institution
        ON Education.institution_id = Institution.institution_id
        WHERE profile_id = :pid ORDER BY rank";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(":pid" => $_GET['profile_id']));
$education = $stmt->fetchall(PDO::FETCH_ASSOC);

// Retrieve Position Information
$sql = "SELECT * FROM Position WHERE profile_id = :pid ORDER BY rank";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(":pid" => $_GET['profile_id']));
$position = $stmt->fetchall(PDO::FETCH_ASSOC);
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
        <?php flashMessages();

        // If not logged in you see this... 
        if ( ! isset($_SESSION['name'])){ ?>
        <p><a href="login.php">Please log in</a></p>            
        <p>First Name:  <?= $row['first_name']?></p>
        <p>Last Name:   <?= $row['last_name']?></p>
        <p>Email:       <?= $row['email']?></p>
        <p>Headline:<br/>
        <?= $row['headline']?></p>
        <p>Summary:<br/>
        <?= $row['summary']?></p>
        <p>Education</p>
        <ul>
        <?php foreach ($education as $item) {
            echo '<li>'.$item['year'].': '.$item['name'].'</li>';
        } ?> 
        </ul>
        <p>Position</p>
        <ul>
        <?php foreach ($position as $item) {
            echo '<li>'.$item['year'].': '.$item['description'].'</li>';
        } ?> 
        </ul>
        <p>
            <a href="index.php" target="_self">Back</a>
        </p>
        <?php

        // If logged in you see this. 
         } else { ?>
        <p>Logged In:   <?= $_SESSION['name']?> 
        <a href="logout.php" target="_self">Logout</a>
        </p>
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
        <p>
            <label for="education">Education:</label><br/>
            <p>
                <ul>
                <?php foreach ($education as $item) {
                echo '<li>'.$item['year'].': '.$item['name'].'</li>';
                } ?> 
                </ul>   
            </p>
        </p>
        <p>
            <label for="position">Position:</label><br/>
            <ul>
            <?php foreach ($position as $item) {
            echo '<li>'.$item['year'].': '.$item['description'].'</li>';
            } ?> 
            </ul> 
        
        </p>
            <a href="index.php" target="_self">Back</a>
        </p>
        <?php } ?>
    </div>
</body>
</html>
