<?php
require_once "pdo.php";

session_start();

header('Content-Type: application/json; charset=utf-8');

// Looks up schools that match what the user has typed in and returns those to
// JavaScript for the drop down 
$sql = "SELECT name FROM Institution WHERE name LIKE :prefix";
$stmt = $pdo->prepare($sql);
$stmt->execute(array( ':prefix' => $_REQUEST['term']."%"));


$school_list = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $school_list[] = $row['name'];
}

echo(json_encode($school_list, JSON_PRETTY_PRINT));

?>




