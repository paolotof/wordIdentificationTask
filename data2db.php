<?php
session_start(); 
$obj = json_decode($_REQUEST['prikey']);

$servername = "localhost";
$username = "rootino";
$password = "not2day";
$db_name = "treasureChest";
$conn = new mysqli($servername, $username, $password, $db_name);
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
echo "Connected successfully";
echo "<br>";

$sql = "INSERT IGNORE INTO `respondent` (`uniqueID`) VALUES ('{$_SESSION['userid']}')";
if ($conn->query($sql) === TRUE) {
	$sql = "SELECT id FROM respondent WHERE uniqueID = '{$_SESSION['userid']}'";
	$result = $conn->query($sql);
	if ($result->num_rows == 0) {
		echo "0 results";
	} else {
		$row = $result->fetch_assoc();
		$last_id = $row["id"];
	}
} else { echo "Error: " . $sql . "<br>" . $conn->error; }
echo "got last id";

$sql = "INSERT IGNORE INTO `{$db_name}`.`responses` (respondentId, wordIdx, RT, keyPress) VALUES ('{$last_id}','{$obj->wordIdx}','{$obj->RT}','{$obj->keyPress}')";
if ($conn->query($sql) === TRUE) {
	echo "Record added: " . "<br>";
} else { echo "Error: " . $sql . "<br>" . $conn->error; }
$conn->close();
echo "added records";
?>
