<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db_connect.php';

$current_user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;

$query = "SELECT 
            u.id, 
            u.username, 
            u.bio, 
            gp.rank_tier, 
            gp.role,
            g.name as game_name
          FROM users u
          LEFT JOIN user_game_profiles gp ON u.id = gp.user_id
          LEFT JOIN games g ON gp.game_id = g.id
          WHERE u.id != :my_id";

$stmt = $conn->prepare($query);
$stmt->bindParam(":my_id", $current_user_id);
$stmt->execute();

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if($users){
    echo json_encode(["status" => 200, "users" => $users]);
} else {
    echo json_encode(["status" => 404, "message" => "No users found"]);
}

?>