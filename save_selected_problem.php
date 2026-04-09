<?php
include "db.php";

header('Content-Type: application/json'); // 🔥 VERY IMPORTANT

if (!isset($_POST['team_id']) || !isset($_POST['problem_title'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing data"
    ]);
    exit;
}

$team_id = $_POST['team_id'];
$problem_title = $_POST['problem_title'];

// 🔥 SAFE QUERY
$sql = "UPDATE teams SET selected_problem=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $problem_title, $team_id);

if ($stmt->execute()) {

    // ✅ OPTIONAL: INSERT NOTIFICATION
    $title = "Problem Selected";
    $message = "Your team selected a problem";

    $conn->query("INSERT INTO notifications (title, message) VALUES ('$title', '$message')");

    echo json_encode([
        "status" => "success"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => $stmt->error
    ]);
}
?>