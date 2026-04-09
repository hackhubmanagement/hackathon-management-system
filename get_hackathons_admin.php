<?php

include "db.php";

$result = mysqli_query($conn,"SELECT * FROM hackathons");

$hackathons = [];

while($row = mysqli_fetch_assoc($result)){
$hackathons[] = $row;
}

echo json_encode($hackathons);

?>	