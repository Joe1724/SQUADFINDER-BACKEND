<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->sender_id) && !empty($data->receiver_id) && !empty($data->message)) {
    
    $query = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (:sender, :receiver, :msg)";
    $stmt = $conn->prepare($query);

    $stmt->bindParam(":sender", $data->sender_id);
    $stmt->bindParam(":receiver", $data->receiver_id);
    $stmt->bindParam(":msg", $data->message);

    if($stmt->execute()){
        echo json_encode(["status" => 200, "message" => "Sent"]);
    } else {
        echo json_encode(["status" => 500, "message" => "Failed to send"]);
    }
} else {
    echo json_encode(["status" => 400, "message" => "Incomplete data"]);
}
?>