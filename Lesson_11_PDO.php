// Created a misc database with one table (users) 
// 		mysql> CREATE DATABASE misc;
// 		GRANT ALL ON misc.* TO ‘caseywschmid’@‘localhost’ IDENTIFIED BY ‘admin’;
// 		GRANT ALL ON misc.* TO ‘caseywschmid’@‘127.0.0.1’ IDENTIFIED BY ‘admin’;

// mysql> DESCRIBE users;
// +----------+--------------+------+-----+---------+----------------+
// | Field    | Type         | Null | Key | Default | Extra          |
// +----------+--------------+------+-----+---------+----------------+
// | user_id  | int(11)      | NO   | PRI | NULL    | auto_increment |
// | name     | varchar(128) | YES  |     | NULL    |                |
// | email    | varchar(128) | YES  | MUL | NULL    |                |
// | password | varchar(128) | YES  |     | NULL    |                |
// +----------+--------------+------+-----+---------+----------------+
// 4 rows in set (0.00 sec)

// mysql> SELECT * FROM users;
// +---------+-----------+-----------------+----------+
// | user_id | name      | email           | password |
// +---------+-----------+-----------------+----------+
// |       1 | Casey     | casey@gmail.com | 123      |
// |       2 | Dominique | domi@gmail.com  | 456      |
// +---------+-----------+-----------------+----------+
// 2 rows in set (0.00 sec)

// HOW TO LOG INTO THIS DATABASE WITH PHP 

<?php

// This connection will allow you to send SQL commands directly to your database through the variable $pdo.
echo "<pre>\n";
$pdo = new PDO('mysql:host=localhost;port=8889;dbname=misc', 'admin', 'admin');
// PDO([database]:[server name];port[number];dbname=[name of database], [username], [password])

$stmt = $pdo->query("SELECT * FROM users");
// while (True) do stuff
// Each time you do the FETCH it'll give you a new row and remain True
// Once its out of rows, it'll become False and exit the while loop
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}
echo "</pre>\n";

?>

// Instead of print_r() you could print it all out in an HTML table

<?php

$pdo = new PDO('mysql:host=localhost;port=8889;dbname=misc', 'admin', 'admin');
$stmt = $pdo->query("SELECT * FROM users");

echo '<table border="1">'."\n";
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC)){
    echo "<tr><td>";
    echo ($row['name']);
    echo "</td><td>";
    echo ($row['email']);
    echo "</td><td>";
    echo ($row['password']);
    echo "</td></tr>\n";
}
echo "</table>\n";

?>

// Its generally not a good idea to combine your connection statement with your website code. 
// These are separated and recombined with include statements

// pdo.php 
<?php
$pdo = new PDO('mysql:host=localhost;port=8889;dbname=misc', 'admin', 'admin');
// This code tells your program to blow up if you have a syntax error in your SQL
// The default is to ignore these... 
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>

// site.php
<?php
echo "<pre>\n";
require_once "pdo.php";

$stmt = $pdo->query("SELECT * FROM users");
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}
echo "</pre>\n";
?>

// To prevent SQL Injection write your code like this:
<?php
if ( isset($_POST['email']) && isset($_POST['password'])){
    echo ("Handling POST data...\n");
    $sql = "SELECT name FROM users WHERE email = :em AND password=:pw";
    echo "<pre>\n$sql\n</pre>\n";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(   ':em' => $_POST['email'], 
                            ':pw' => $_POST['password']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

// PDO ERROR HANDLING 
// Where do error_log()'s go? 
// File Path:
    // /Applications/MAMP/logs/php_error.log 
// Open the log file and scroll to the bottom
// Or you can actively watch the log. 
    // $ tail -f [filename]
    // $ tail -f /Applications/MAMP/logs/php_error.log

<?php
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 try {
    $stmt = $pdo->query("SELECT * FROM users WHERE user_id = :xyz");
    $stmt->execute(array(':pizza' => $_GET['user_id'])); 
 } catch (Exception $ex) {
    echo ("Exception Message: ".$ex->getMessage());
    return;
 }
 $row = $stmt->fetch(PDO::FETCH_ASSOC);


