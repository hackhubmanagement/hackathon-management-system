<?php

include "db.php";
include "log_system.php";

$name = $_POST['name'];
$email = $_POST['email'];
$expertise = $_POST['expertise'];

$sql = "INSERT INTO judges(name,email,expertise)
VALUES('$name','$email','$expertise')";

if(mysqli_query($conn,$sql)){
addLog("Judge Added: ".$name);
echo "success";
}
else{
echo "error";
}

?>