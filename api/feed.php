<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db_connect.php';

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : die();
// <--- NEW: Check for game_id filter (default to 0 for "All") --->
$filter_game_id = isset($_GET['game_id']) ? $_GET['game_id'] : 0;

// 1. Get IDs of users you already swiped on
$swiped_query = "SELECT target_id FROM swipes WHERE swiper_id = :id";
$stmt = $conn->prepare($swiped_query);
$stmt->execute([':id' => $user_id]);
$swiped_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

// 2. Build the Main Query
$query = "SELECT 
            u.id, 
            u.username, 
            u.bio, 
            u.avatar,
            COALESCE(gp.rank_tier, 'Unranked') as rank_tier, 
            COALESCE(gp.role, 'Any') as role,
            COALESCE(g.name, 'No Game') as game_name
          FROM users u
          LEFT JOIN user_game_profiles gp ON u.id = gp.user_id
          LEFT JOIN games g ON gp.game_id = g.id
          WHERE u.id != :user_id";

// Exclude swiped users
if (!empty($swiped_ids)) {
    $ids_string = implode(',', $swiped_ids);
    $query .= " AND u.id NOT IN ($ids_string)";
}

// <--- NEW: Apply Game Filter if selected --->
if ($filter_game_id != 0) {
    $query .= " AND gp.game_id = :game_id";
}

$query .= " LIMIT 10"; // Fetch 10 at a time

$stmt = $conn->prepare($query);
$stmt->bindParam(":user_id", $user_id);

// <--- NEW: Bind Game ID if filtering --->
if ($filter_game_id != 0) {
    $stmt->bindParam(":game_id", $filter_game_id);
}

$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($users)) {
    echo json_encode(["status" => 200, "users" => $users]);
} else {
    echo json_encode(["status" => 200, "users" => [], "message" => "No users found"]);
}
?>