<?php
function validateInput($input)
{
    return htmlspecialchars(trim($input));
}

function validateEmail($email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format.');
    }
    return $email;
}

function hashPassword($password)
{
    return password_hash($password, PASSWORD_BCRYPT);
}

function verifyPassword($password, $hashedPassword)
{
    return password_verify($password, $hashedPassword);
}

function handleFileUpload($file, $targetDir)
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error. Code: ' . $file['error']);
    }

    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($file['type'], $allowedMimeTypes)) {
        throw new Exception('Invalid file type. Only JPEG, PNG, and GIF images are allowed.');
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        throw new Exception('Invalid file extension. Only .jpg, .jpeg, .png, and .gif are allowed.');
    }

    if ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
        throw new Exception('File size exceeds 2MB.');
    }

    $uniqueName = uniqid() . '-' . basename($file['name']);
    if (!is_dir($targetDir)) {
        throw new Exception("Target directory does not exist: $targetDir");
    }

    $targetPath = $targetDir . $uniqueName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Failed to move uploaded file. Check permissions.');
    }

    return $uniqueName;
}

function saveUserData($userData, $filePath)
{
    $users = json_decode(file_get_contents($filePath), true) ?: [];
    $users[] = $userData;
    file_put_contents($filePath, json_encode($users, JSON_PRETTY_PRINT));
}

function getUserData($filePath)
{
    return json_decode(file_get_contents($filePath), true) ?: [];
}

function logActivity($email, $action)
{
    $logMessage = sprintf("[%s] %s: %s\n", date('Y-m-d H:i:s'), $email, $action);
    file_put_contents('../logs/log.txt', $logMessage, FILE_APPEND);
}
