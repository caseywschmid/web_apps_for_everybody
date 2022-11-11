<?php
require_once "pdo.php";

session_start();

header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT name FROM Institution WHERE name LIKE :prefix";
$stmt = $pdo->prepare($sql);
$stmt->execute(array( ':prefix' => $_REQUEST['term']."%"));

$retval = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $retval[] = $row['name'];
}

echo(json_encode($retval, JSON_PRETTY_PRINT));

?>




