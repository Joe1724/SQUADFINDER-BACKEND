<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->user_id)) {
    
    $user_id = $data->user_id;
    $bio = $data->bio;
    $rank = $data->rank_tier;
    $role = $data->role;

    try {
        // 1. Update Bio in 'users' table
        $query1 = "UPDATE users SET bio = :bio WHERE id = :id";
        $stmt1 = $conn->prepare($query1);
        $stmt1->execute([':bio' => $bio, ':id' => $user_id]);

        // 2. Update Rank/Role in 'user_game_profiles' table
        // We use INSERT ... ON DUPLICATE KEY UPDATE just in case the profile didn't exist yet
        $query2 = "INSERT INTO user_game_profiles (user_id, game_id, rank_tier, role) 
                   VALUES (:id, 1, :rank, :role) 
                   ON DUPLICATE KEY UPDATE rank_tier = :rank, role = :role";
        
        $stmt2 = $conn->prepare($query2);
        $stmt2->execute([':id' => $user_id, ':rank' => $rank, ':role' => $role]);

        echo json_encode(["status" => 200, "message" => "Profile updated successfully"]);

    } catch(PDOException $e) {
        echo json_encode(["status" => 500, "message" => "Error: " . $e->getMessage()]);
    }

} else {
    echo json_encode(["status" => 400, "message" => "Incomplete data"]);
}
?>