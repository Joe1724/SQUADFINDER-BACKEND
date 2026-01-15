<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db_connect.php';

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : die();

$query = "SELECT 
            u.id, 
            u.username, 
            u.email, 
            u.bio, 
            u.avatar,
            COALESCE(gp.rank_tier, 'Unranked') as rank_tier, 
            COALESCE(gp.role, 'Any') as role,
            COALESCE(g.name, 'No Game') as game_name
          FROM users u
          LEFT JOIN user_game_profiles gp ON u.id = gp.user_id
          LEFT JOIN games g ON gp.game_id = g.id
          WHERE u.id = :id";

$stmt = $conn->prepare($query);
$stmt->bindParam(":id", $user_id);
$stmt->execute();

if($stmt->rowCount() > 0){
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(["status" => 200, "data" => $row]);
} else {
    echo json_encode(["status" => 404, "message" => "User not found"]);
}
?>