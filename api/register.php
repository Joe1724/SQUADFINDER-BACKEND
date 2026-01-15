<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/db_connect.php';
$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->username) &&
    !empty($data->email) &&
    !empty($data->password)
){
    $query = "INSERT INTO users SET username=:username, email=:email, password=:password";
    $stmt = $conn->prepare($query);

    $username = htmlspecialchars(strip_tags($data->username));
    $email = htmlspecialchars(strip_tags($data->email));
    $password_hash = password_hash($data->password, PASSWORD_BCRYPT);

    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password", $password_hash);

    if($stmt->execute()){
        http_response_code(201);
        echo json_encode(array("message" => "User registered successfully."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to register the user."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data. Please provide username, email, and password."));
}

?>