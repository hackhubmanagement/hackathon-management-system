<?php
include "db.php";

$email = $_POST['email'];
$new_password = $_POST['new_password'];

// SAME encryption as login.php
$new_password = md5($new_password);

$sql = "UPDATE students SET password='$new_password' WHERE email='$email'";

if(mysqli_query($conn, $sql)){
    echo "Password updated successfully";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>