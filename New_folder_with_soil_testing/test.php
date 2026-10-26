<?php

require_once "db.php";

echo "<h2>Connected Database</h2>";

$result = $conn->query("SELECT DATABASE() AS db");

$row = $result->fetch_assoc();

echo "Database: " . $row["db"] . "<br><br>";

echo "<h2>Tables available to PHP:</h2>";

$result = $conn->query("SHOW TABLES");

while ($row = $result->fetch_array()) {
    echo $row[0] . "<br>";
}
?>
