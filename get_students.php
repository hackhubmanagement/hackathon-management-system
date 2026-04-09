<?php

include "db.php";

$result = mysqli_query($conn,"SELECT * FROM students");

$students = [];

while($row = mysqli_fetch_assoc($result)){
$students[] = $row;
}

echo json_encode($students);

?>