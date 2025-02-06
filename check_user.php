<?php
header('Content-Type: application/json');

try {
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['mobile'])) {
        throw new Exception('Mobile number not provided');
    }

    $mobile = $data['mobile'];
    $dataFile = __DIR__ . '/data/data.txt';

    // Check if the mobile number exists in the file
    $exists = false;
    if (file_exists($dataFile)) {
        $content = file_get_contents($dataFile);
        $exists = strpos($content, "Mobile: " . $mobile . "\n") !== false;
    }

    echo json_encode(['exists' => $exists]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
