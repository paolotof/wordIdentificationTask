<?php
$servername = "localhost";
$username = "rootino";
$password = "not2day";
$db_name = "treasureChest";
$conn = new mysqli($servername, $username, $password, $dbname);
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", $conn->error);
    exit();
}

$obj = json_decode($_REQUEST['prikey']);

$sql = "INSERT IGNORE INTO `{$dbname}`.`responses` (respondentId, wordIdx, RT, keyPress) VALUES ('{$obj->respID}','{$obj->wordIdx}','{$obj->RT}','{$obj->keyPress}')";
if ($conn->query($sql) === TRUE) {
	echo "Record added: " . "<br>";
} else { echo "Error: " . $sql . "<br>" . $conn->error; }
$conn->close();

?>
