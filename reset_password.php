<?php
header('Content-Type: application/json');

try {
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['action'])) {
        throw new Exception('Action not specified');
    }

    if ($data['action'] === 'verify_code') {
        // Check if the reset code matches
        if (!isset($data['code']) || $data['code'] !== '7268') {
            echo json_encode(['success' => false, 'message' => 'Invalid reset code']);
            exit;
        }
        echo json_encode(['success' => true, 'message' => 'Code verified']);
        exit;
    }

    if ($data['action'] === 'reset_password') {
        if (!isset($data['mobile']) || !isset($data['new_password'])) {
            throw new Exception('Mobile number or new password not provided');
        }

        $mobile = $data['mobile'];
        $newPassword = $data['new_password'];
        $dataFile = __DIR__ . '/data/data.txt';

        if (!file_exists($dataFile)) {
            throw new Exception('No users registered');
        }

        // Read the file content
        $content = file_get_contents($dataFile);
        $entries = explode("------------------------\n", $content);
        $newContent = '';
        $passwordUpdated = false;

        foreach ($entries as $entry) {
            if (empty(trim($entry))) continue;
            
            preg_match('/Mobile: (\d+)/', $entry, $mobileMatch);
            
            if (isset($mobileMatch[1]) && $mobileMatch[1] === $mobile) {
                // Update the password in this entry
                $entry = preg_replace(
                    '/Password: .+\n/',
                    "Password: " . $newPassword . "\n",
                    $entry
                );
                $passwordUpdated = true;
            }
            
            $newContent .= $entry . "------------------------\n";
        }

        if (!$passwordUpdated) {
            throw new Exception('User not found');
        }

        // Write the updated content back to file
        if (file_put_contents($dataFile, $newContent) === false) {
            throw new Exception('Failed to update password');
        }

        echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
        exit;
    }

    throw new Exception('Invalid action');

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
