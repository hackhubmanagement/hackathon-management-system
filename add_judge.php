<?php

include "db.php";
include "log_system.php";

$name = $_POST['name'];
$email = $_POST['email'];
$expertise = $_POST['expertise'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO judges(name,email,expertise,password)
VALUES('$name','$email','$expertise','$password')";

if(mysqli_query($conn,$sql)){
addLog("Judge Added: ".$name);
echo "success";
}
else{
echo "error";
}

?>