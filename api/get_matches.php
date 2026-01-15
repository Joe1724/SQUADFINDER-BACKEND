<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db_connect.php';

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : die();

// Logic: Find all matches where I am involved, then JOIN to get the OTHER person's details
// IF I am user_1, show me user_2. IF I am user_2, show me user_1.
$query = "
    SELECT 
        m.id as match_id,
        u.id as user_id,
        u.username,
        u.email,
        g.name as game_name,
        gp.rank_tier,
        gp.role
    FROM matches m
    JOIN users u ON (CASE WHEN m.user_1_id = :my_id THEN m.user_2_id ELSE m.user_1_id END) = u.id
    LEFT JOIN user_game_profiles gp ON u.id = gp.user_id
    LEFT JOIN games g ON gp.game_id = g.id
    WHERE m.user_1_id = :my_id OR m.user_2_id = :my_id
";

$stmt = $conn->prepare($query);
$stmt->bindParam(":my_id", $user_id);
$stmt->execute();

$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "status" => 200,
    "matches" => $matches
]);
?>