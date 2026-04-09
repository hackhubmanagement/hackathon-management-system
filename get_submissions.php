<?php

include "db.php";

$sql = "SELECT * FROM submissions ORDER BY submitted_at DESC";

$result = mysqli_query($conn,$sql);

$data = [];

while($row = mysqli_fetch_assoc($result)){
$data[] = $row;
}

echo json_encode($data);

?>