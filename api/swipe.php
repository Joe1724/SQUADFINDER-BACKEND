<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/db_connect.php';
include_once 'send_push.php'; // <--- NEW: Include the notification helper

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->swiper_id) && 
    !empty($data->target_id) && 
    !empty($data->action)
){
    $swiper_id = $data->swiper_id;
    $target_id = $data->target_id;
    $action    = $data->action; // 'like' or 'pass'

    // 1. Check if swipe already exists (Prevent Duplicates)
    $check_query = "SELECT id FROM swipes WHERE swiper_id = ? AND target_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->execute([$swiper_id, $target_id]);

    if($check_stmt->rowCount() > 0){
        echo json_encode(["status" => 400, "message" => "You already swiped on this user."]);
        exit();
    }

    // 2. Insert the Swipe
    $insert_query = "INSERT INTO swipes (swiper_id, target_id, action) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    
    if($stmt->execute([$swiper_id, $target_id, $action])){
        
        $is_match = false;

        // 3. IF ACTION IS LIKE: Check for Mutual Match
        if($action === 'like'){
            // Check if the OTHER person (target) already swiped 'like' on ME (swiper)
            $match_query = "SELECT id FROM swipes WHERE swiper_id = ? AND target_id = ? AND action = 'like'";
            $match_stmt = $conn->prepare($match_query);
            $match_stmt->execute([$target_id, $swiper_id]);

            if($match_stmt->rowCount() > 0){
                // IT'S A MATCH! 
                $is_match = true;

                // Save to matches table
                $save_match = "INSERT INTO matches (user_1_id, user_2_id) VALUES (?, ?)";
                $save_stmt = $conn->prepare($save_match);
                $save_stmt->execute([$swiper_id, $target_id]);

                // <--- NEW: SEND PUSH NOTIFICATION --->
                // Notify the Target (the person you just liked)
                sendPushNotification(
                    $target_id, 
                    "New Squad Mate! 🎮", 
                    "You have a new match! Check your Squads tab.",
                    ["match_id" => $swiper_id]
                );
            }
        }

        echo json_encode([
            "status" => 200, 
            "message" => "Swipe recorded", 
            "is_match" => $is_match
        ]);

    } else {
        echo json_encode(["status" => 503, "message" => "Unable to record swipe."]);
    }

} else {
    echo json_encode(["status" => 400, "message" => "Incomplete data."]);
}
?>