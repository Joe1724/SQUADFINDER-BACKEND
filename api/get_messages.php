<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db_connect.php';

$user1 = isset($_GET['user_1']) ? $_GET['user_1'] : die();
$user2 = isset($_GET['user_2']) ? $_GET['user_2'] : die();

// Fetch messages where (Sender is Me AND Receiver is You) OR (Sender is You AND Receiver is Me)
// Ordered by time so they appear in correct flow
$query = "SELECT * FROM messages 
          WHERE (sender_id = :u1 AND receiver_id = :u2) 
             OR (sender_id = :u2 AND receiver_id = :u1) 
          ORDER BY created_at ASC";

$stmt = $conn->prepare($query);
$stmt->bindParam(":u1", $user1);
$stmt->bindParam(":u2", $user2);
$stmt->execute();

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["status" => 200, "messages" => $messages]);
?>