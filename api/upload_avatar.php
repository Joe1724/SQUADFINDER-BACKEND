<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/db_connect.php';

// Check if a file was sent
if(isset($_FILES['avatar']) && isset($_POST['user_id'])) {
    
    $user_id = $_POST['user_id'];
    $file = $_FILES['avatar'];
    
    // Create a unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_name = "user_" . $user_id . "_" . time() . "." . $extension;
    
    // <--- THE FIX IS HERE: Create folder if it doesn't exist --->
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . $new_name;

    // Move file from temp storage to our uploads folder
    if(move_uploaded_file($file['tmp_name'], $target_file)) {
        
        // Save the URL to the database
        // NOTE: If using a real phone, replace 'localhost' with your PC's IP address (e.g., 192.168.x.x)
        $full_url = "http://192.168.254.195/squadfinder/uploads/" . $new_name;

        $query = "UPDATE users SET avatar = :avatar WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":avatar", $full_url);
        $stmt->bindParam(":id", $user_id);
        
        if($stmt->execute()) {
            echo json_encode([
                "status" => 200, 
                "message" => "Upload successful",
                "avatar_url" => $full_url
            ]);
        } else {
            echo json_encode(["status" => 500, "message" => "Database error"]);
        }

    } else {
        echo json_encode(["status" => 500, "message" => "Failed to save file"]);
    }
} else {
    echo json_encode(["status" => 400, "message" => "No file received"]);
}
?>