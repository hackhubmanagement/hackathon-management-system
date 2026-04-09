<?php

include "db.php";

$sql = "SELECT * FROM events ORDER BY event_date ASC";
$result = mysqli_query($conn,$sql);

$events = [];

while($row = mysqli_fetch_assoc($result)){
$events[] = $row;
}

echo json_encode($events);

?>