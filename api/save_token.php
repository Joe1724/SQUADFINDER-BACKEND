<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->user_id) && !empty($data->token)) {
    
    $query = "UPDATE users SET push_token = :token WHERE id = :id";
    $stmt = $conn->prepare($query);
    
    $stmt->bindParam(":token", $data->token);
    $stmt->bindParam(":id", $data->user_id);
    
    if($stmt->execute()) {
        echo json_encode(["status" => 200, "message" => "Token saved"]);
    } else {
        echo json_encode(["status" => 500, "message" => "Database error"]);
    }
} else {
    echo json_encode(["status" => 400, "message" => "Missing data"]);
}
?>