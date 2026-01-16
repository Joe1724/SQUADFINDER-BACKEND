<?php
// Function to send Expo Push Notification
function sendPushNotification($target_user_id, $title, $body, $data = []) {
    global $conn; // Use the existing database connection

    // 1. Get the Push Token for the target user
    $query = "SELECT push_token FROM users WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->execute([':id' => $target_user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || empty($user['push_token'])) {
        return ["status" => false, "message" => "User has no push token"];
    }

    $token = $user['push_token'];

    // 2. Validate Token format (Must start with ExponentPushToken)
    if (strpos($token, 'ExponentPushToken') !== 0) {
         return ["status" => false, "message" => "Invalid token format"];
    }

    // 3. Prepare the payload for Expo
    $postData = [
        'to' => $token,
        'title' => $title,
        'body' => $body,
        'data' => $data,
        'sound' => 'default',
        'badge' => 1
    ];

    // 4. Send Request to Expo API
    $ch = curl_init('https://exp.host/--/api/v2/push/send');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Accept-Encoding: gzip, deflate'
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ["status" => true, "expo_response" => json_decode($response)];
}
?>