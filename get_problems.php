<?php

include "db.php";

$result = mysqli_query($conn,"SELECT * FROM problems");

$problems = [];

while($row = mysqli_fetch_assoc($result)){
$problems[] = $row;
}

echo json_encode($problems);

?>