<?php

include "db.php";

$result = mysqli_query($conn,"SELECT * FROM teams");

$teams = [];

while($row = mysqli_fetch_assoc($result)){
$teams[] = $row;
}

echo json_encode($teams);

?>