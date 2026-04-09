<?php

include "db.php";

$team_id = $_GET['team_id'];

$result = mysqli_query($conn,"SELECT selected_problem FROM teams WHERE id='$team_id'");

$row = mysqli_fetch_assoc($result);

echo json_encode($row);

?>