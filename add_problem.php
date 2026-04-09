<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db.php";

$title = $_POST['title'];
$description = $_POST['description'];
$domain = $_POST['domain'];

$sql = "INSERT INTO problems(title,description,domain)
VALUES('$title','$description','$domain')";

if(mysqli_query($conn,$sql)){
    echo "success";
}else{
    echo mysqli_error($conn); // 🔥 shows real error
}

?>