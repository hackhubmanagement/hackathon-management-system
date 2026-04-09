<?php

include "db.php";

$result = mysqli_query($conn,"SELECT * FROM system_logs ORDER BY created_at DESC LIMIT 5");

$logs = [];

while($row = mysqli_fetch_assoc($result)){

$logs[] = $row['created_at']." - ".$row['log_message'];

}

echo json_encode($logs);

?>