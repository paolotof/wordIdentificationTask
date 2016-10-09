<?php session_start();
$servername = "localhost";
$username = "rootino";
$password = "not2day";
$db_name = "treasureChest";

$conn = new mysqli($servername, $username, $password, $dbname);
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
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
$conn->close();
echo $last_id;
?>
