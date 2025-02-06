<?php
header('Content-Type: application/json');

try {
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['mobile']) || !isset($data['password'])) {
        throw new Exception('Mobile number or password not provided');
    }

    $mobile = $data['mobile'];
    $password = $data['password'];
    $dataFile = __DIR__ . '/data/data.txt';

    if (!file_exists($dataFile)) {
        echo json_encode(['success' => false, 'message' => 'No users registered']);
        exit;
    }

    // Read the file content
    $content = file_get_contents($dataFile);
    $entries = explode("------------------------\n", $content);
    
    foreach ($entries as $entry) {
        if (empty(trim($entry))) continue;
        
        // Extract mobile and password from entry
        preg_match('/Mobile: (\d+)/', $entry, $mobileMatch);
        preg_match('/Password: (.+)/', $entry, $passwordMatch);
        preg_match('/Name: (.+)/', $entry, $nameMatch);
        
        if (isset($mobileMatch[1]) && isset($passwordMatch[1])) {
            if ($mobileMatch[1] === $mobile && $passwordMatch[1] === $password) {
                echo json_encode([
                    'success' => true,
                    'name' => isset($nameMatch[1]) ? $nameMatch[1] : 'User',
                    'message' => 'Login successful'
                ]);
                exit;
            }
        }
    }

    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
