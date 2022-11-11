<?php
require_once "pdo.php";
session_start();

// Demand a session
if ( ! isset($_SESSION['name'])) {
    die('ACCESS DENIED');
}
// If the User presses the 'cancel' button
if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

// Record Edit
if (isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['headline'], $_POST['summary'], $_POST['profile_id'] )) {
    // Error Checking and Data Validation
    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    } elseif ((str_contains($_POST['email'],'@')) == False){
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;     
    } else {
        // Actual Edit
        $sqlup = "UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :id";
        $upstmt = $pdo->prepare($sqlup);
        $upstmt->execute(array(
            ':fn'=> htmlentities($_POST['first_name']),
            ':ln'=> htmlentities($_POST['last_name']),
            ':em'=> htmlentities($_POST['email']),
            ':he'=> htmlentities($_POST['headline']),
            ':su'=> htmlentities($_POST['summary']),
            ':id' => $_POST['profile_id']));
        $_SESSION['success'] = 'Record edited';
        header('Location: index.php');
        return;
    }
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

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']); 
$em = htmlentities($row['email']); 
$he = htmlentities($row['headline']);
$su = htmlentities($row['summary']);
$id = $row['profile_id']; 
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Casey Schmid Profile Edit</title>
    </head>
    <body>
        <div class="container">
            <h1>Edit Profile</h1>
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
                    First Name: 
                    <input type="text" name="first_name" value="<?= $fn ?>"><br/> 
                </p>
                <p>
                    Last Name: 
                    <input type="text" name="last_name" value="<?= $ln ?>"><br/>
                </p>
                <p>
                    Email:
                    <input type="text" name="email" value="<?= $em ?>"><br/>
                </p>
                <p>
                    Headline:
                    <input type="text" name="headline" value="<?= $he ?>"><br/>
                </p>
                <p>
                    Summary: 
                    <textarea name="summary"><?= $su ?></textarea><br/>
                    <input type="hidden" name="profile_id" value="<?= $id ?>"><br/>
                </p>
                <input type="submit" name="add" value="Save">
                <input type="submit" name="cancel" value="Cancel">
            </form>
        </div>
    </body>
</html>