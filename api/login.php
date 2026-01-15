<?php
// 1. CORS Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// 2. Include Database
include_once '../config/db_connect.php';

// 3. Get JSON Input
$data = json_decode(file_get_contents("php://input"));

// Check if email and password are provided
if(!empty($data->email) && !empty($data->password)){
    
    // 4. Query: Find the user by email
    $query = "SELECT id, username, password FROM users WHERE email = :email LIMIT 1";
    $stmt = $conn->prepare($query);
    
    $stmt->bindParam(":email", $data->email);
    $stmt->execute();
    
    // 5. Check if row exists
    if($stmt->rowCount() > 0){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id = $row['id'];
        $username = $row['username'];
        $hashed_password = $row['password'];

        // 6. Verify Password (compare plain text with hash)
        if(password_verify($data->password, $hashed_password)){
            // PASSWORD CORRECT
            echo json_encode([
                "status" => 200,
                "message" => "Login successful",
                "user" => [
                    "id" => $id,
                    "username" => $username
                ]
            ]);
        } else {
            // PASSWORD WRONG
            echo json_encode(["status" => 401, "message" => "Invalid password"]);
        }
    } else {
        // EMAIL NOT FOUND
        echo json_encode(["status" => 404, "message" => "User not found"]);
    }
} else {
    echo json_encode(["status" => 400, "message" => "Incomplete data"]);
}
?>