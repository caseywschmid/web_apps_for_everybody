<?php
// You need this for any page that will ping the db
// You'll get the following error if you forget it:
// Uncaught Error: Call to a member function prepare() on null 
require_once "pdo.php";
require_once "util.php";

session_start();
if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}
if ( ! isset($_POST['email']) && isset($_POST['pass'])) {
    echo '<a href="login.php">Please log in</a>';
    header("Location: login.php");
    return;
}

$salt = 'XyZzy12*_';
// Email is     admin@admin.com
// Password is  admin
// Email is     casey@gmail.com
// Password is  php123

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    unset ($_SESSION['name']); // Logout anyone logged in
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION['error'] = "User name and Password are required";
        header("Location: login.php");
        return;
    } elseif ((str_contains($_POST['email'],'@')) == False){
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: login.php");
        return;     
    } else {
        $check = hash('md5', $salt.$_POST['pass']);
        $sqllog = 'SELECT user_id, name FROM users WHERE email = :em AND password = :pw';
        $stmt = $pdo->prepare($sqllog);
        $stmt->execute(array(   ':em' => $_POST['email'],
                                ':pw' => $check));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

// Since we are checking if the stored hashed password matches the hash
// computation of the user-provided password, if we get a row, then the password
// matches. If we don't get a row (i.e. $row is false) then the password did not
// match. If the password matches, put the user_id value for the user's row into
// session as well as the user's name
        if ( $row !== false) {
            $_SESSION['name'] = $row['name'];
            // The user_id is put into the session in the login.php and read elsewhere.
            $_SESSION['user_id'] = $row['user_id'];
            header("Location: index.php");
            return;
        } else {
            $_SESSION['error'] = "Incorrect Password";
            error_log("Login fail ".$_POST['email']." $check");
            header("Location: login.php");
            return; 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profile Login</title>
        <?php require_once "head.php"; ?>
        <!-- The validation was done with JS as an exercise  
             EM and PW can't be blank and EM must have an '@' somewhere after the 
             first character -->
        <script>
            function validate() {
                console.log('Validating...');
                try {
                    pw = document.getElementById('pass').value;
                    em = document.getElementById('email').value;
                    console.log('Validating em= ' + em);
                    console.log('Validating pw= ' + pw);
                    if (pw == null || pw == '' || em == null || em == '') {
                        alert('Both fields must be filled out');
                        return false;
                    }
                    console.log('em and pw are filled out')
                    if (! em.includes("@", 1)){
                        alert('Invalid email address');
                    }
                    console.log('em has @ sign')
                    return true;
                } catch(e) {
                    return false;
                }
                return false;
            }
        </script>
    </head>
    <body>
        <div class="container">
            <h1>Please Log In</h1>
            <?php
            if ( isset($_SESSION['error']) ) {
                echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
                unset($_SESSION['error']);
            }
            ?>
            <form method="POST">
                <p>
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email"><br/>
                </p>
                <p>
                    <label for="pass">Password</label>
                    <input type="password" name="pass" id="pass"><br/>
                </p>
                <input type="submit" onclick="return validate();" value="Log In">
                <input type="submit" name="cancel" value="Cancel">
            </form>
            <p></p>
            <p>
                Email: admin@admin.com <br/>
                Password: admin
            </p>
            <p>
                Email: casey@gmail.com <br/>
                Password: php123
            </p>
        </div>
    </body>
</html>