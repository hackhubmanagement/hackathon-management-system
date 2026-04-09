<?php

include "db.php";

$sql = "SELECT * FROM hackathons WHERE status='active' AND registration_deadline > NOW()";

$result = mysqli_query($conn,$sql);

$hackathons = [];

while($row = mysqli_fetch_assoc($result)){
    $hackathons[] = $row;
}

echo json_encode($hackathons);

?>