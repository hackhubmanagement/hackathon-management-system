<?php

include "db.php";

$team_id = $_POST['team_id'];
$hackathon_id = $_POST['hackathon_id'];

// 0. CHECK REGISTRATION DEADLINE
$deadline_check = "SELECT registration_deadline FROM hackathons WHERE id='$hackathon_id'";
$deadline_result = mysqli_query($conn, $deadline_check);

if(mysqli_num_rows($deadline_result) > 0){
    $row = mysqli_fetch_assoc($deadline_result);
    if(strtotime($row['registration_deadline']) < time()){
        echo "deadline_passed";
        exit();
    }
}

// 1. CHECK ALREADY REGISTERED
$check = "SELECT * FROM registrations 
WHERE team_id='$team_id' AND hackathon_id='$hackathon_id'";

$result = mysqli_query($conn, $check);

if(mysqli_num_rows($result) > 0){
    echo "already_registered";
    exit();
}

// 2. INSERT REGISTRATION
$sql = "INSERT INTO registrations(team_id, hackathon_id) 
VALUES('$team_id','$hackathon_id')";

if(mysqli_query($conn,$sql)){

    echo "success";

    // 🔔 3. SEND NOTIFICATION TO ALL TEAM MEMBERS
    $title = "Hackathon Registered";
    $message = "Your team registered successfully";

    // get all team members
    $members = mysqli_query($conn, 
    "SELECT email FROM team_members WHERE team_id='$team_id'");

    while($m = mysqli_fetch_assoc($members)){

        $email = $m['email'];

        // get user id
        $userRes = mysqli_query($conn, 
        "SELECT id FROM students WHERE email='$email'");

        $user = mysqli_fetch_assoc($userRes);

        if($user){
            $user_id = $user['id'];

            mysqli_query($conn, "INSERT INTO notifications 
            (user_id, title, message, is_read, created_at)
            VALUES ('$user_id', '$title', '$message', 0, NOW())");
        }
    }

}
else{
    echo "error";
}
?>