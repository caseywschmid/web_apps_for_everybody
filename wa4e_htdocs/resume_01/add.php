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
if (isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['headline'], $_POST['summary'], $_POST['user_id'] )) {
    // Error Checking and Data Validation
    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php?user_id=".$_REQUEST['user_id']);
        return;
    } elseif ((str_contains($_POST['email'],'@')) == False){
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: add.php?user_id=".$_REQUEST['user_id']);
        return;     
    } else {
        // Actual Insert
        $sqladd = "INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES ( :uid, :fn, :ln, :em, :he, :su)";
        $addstmt = $pdo->prepare($sqladd);
        $addstmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':fn'=> htmlentities($_POST['first_name']),
            ':ln'=> htmlentities($_POST['last_name']),
            ':em'=> htmlentities($_POST['email']),
            ':he'=> htmlentities($_POST['headline']),
            ':su'=> htmlentities($_POST['summary'])));
        $_SESSION['success'] = 'Profile added';
        header('Location: index.php');
        return;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Casey Schmid Profile Add</title>
    </head>
    <body>
        <div class="container">
            <h1>Add New Profile</h1>
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
                    <label for="first_name">First Name:</label>
                    <input type="text" name="first_name"><br/> 
                </p>
                <p>
                    <label for="last_name">Last Name:</label>
                    <input type="text" name="last_name"><br/>
                </p>
                <p>
                    <label for="email">Email:</label>
                    <input type="text" name="email"><br/>
                </p>
                <p>
                    <label for="headline">Headline:</label>
                    <input type="text" name="headline"><br/>
                </p>
                <p>
                    <label for="summary">Summary:</label>
                    <textarea name="summary"></textarea>
                </p>
                <input type="hidden" name="user_id" value="<?= $_SESSION['user_id']?>">
                <input type="submit" name="add" value="Add">
                <input type="submit" name="logout" value="Logout">
            </form>
        </div>
    </body>
</html>