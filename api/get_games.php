<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db_connect.php';

// Fetch all supported games
$query = "SELECT * FROM games ORDER BY name ASC";
$stmt = $conn->prepare($query);
$stmt->execute();

$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add an "All Games" option manually at the top
array_unshift($games, ["id" => "0", "name" => "All Games"]);

echo json_encode(["status" => 200, "games" => $games]);
?>