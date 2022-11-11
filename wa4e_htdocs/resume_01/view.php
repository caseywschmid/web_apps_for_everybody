<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['logout'] ) ) {
    header("Location: logout.php");
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
    <title>Casey Schmid View Detail</title>
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
            <p>
                <a href="index.php" target="_self">Back</a>
            </p>
      <?php } else { ?>
            <p>Logged In: <?= $_SESSION['name']?> </p>
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