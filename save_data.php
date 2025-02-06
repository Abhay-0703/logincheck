<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('No data received');
    }

    // Validate required fields
    $requiredFields = ['name', 'mobile', 'password'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            throw new Exception("$field is required");
        }
    }

    // Create data directory if it doesn't exist
    $dataDir = __DIR__ . '/data';
    if (!file_exists($dataDir)) {
        mkdir($dataDir, 0777, true);
    }

    // Check if mobile number already exists
    $existingData = file_exists($dataDir . '/data.txt') ? file_get_contents($dataDir . '/data.txt') : '';
    if (strpos($existingData, "Mobile: " . $data['mobile'] . "\n") !== false) {
        throw new Exception('Mobile number already registered');
    }

    // Format the data
    $formattedData = "Name: " . $data['name'] . "\n" .
                     (isset($data['class']) ? "Class: " . $data['class'] . "\n" : "") .
                     "Mobile: " . $data['mobile'] . "\n" .
                     (isset($data['email']) ? "Email: " . $data['email'] . "\n" : "") .
                     "Password: " . $data['password'] . "\n" .
                     "Timestamp: " . date('Y-m-d H:i:s') . "\n" .
                     "------------------------\n";

    // Append to master file
    $masterFile = $dataDir . '/data.txt';
    if (file_put_contents($masterFile, $formattedData, FILE_APPEND) === false) {
        throw new Exception('Failed to write to file');
    }

    // Send success response
    echo json_encode([
        'success' => true, 
        'message' => 'Data saved successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>
