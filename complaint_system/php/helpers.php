<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

// Check if user is student
function isStudent() {
    return isLoggedIn() && $_SESSION['user_type'] === 'student';
}

// Check if user is admin
function isAdmin() {
    return isLoggedIn() && $_SESSION['user_type'] === 'admin';
}

// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /complaint_system/index.php?error=Please login to access this page");
        exit();
    }
}

// Require student access
function requireStudent() {
    requireLogin();
    if (!isStudent()) {
        header("Location: /complaint_system/index.php?error=Student access only");
        exit();
    }
}

// Require admin access
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: /complaint_system/index.php?error=Admin access only");
        exit();
    }
}

// Get current user ID
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Get current user name
function getUserName() {
    return $_SESSION['user_name'] ?? null;
}

// Get current user email
function getUserEmail() {
    return $_SESSION['user_email'] ?? null;
}

// Get user type
function getUserType() {
    return $_SESSION['user_type'] ?? null;
}

// Sanitize input
function clean($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate password strength
function isValidPassword($password) {
    return strlen($password) >= 8;
}

// Handle file upload
function uploadFile($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'No file uploaded or upload error'];
    }
    
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmp = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Check file extension
    if (!in_array($fileExt, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    // Check file size (5MB max)
    if ($fileSize > 5242880) {
        return ['success' => false, 'message' => 'File too large (max 5MB)'];
    }
    
    // Generate unique filename
    $newFileName = uniqid('media_', true) . '.' . $fileExt;
    $uploadPath = __DIR__ . '/../uploads/' . $newFileName;
    
    // Move file
    if (move_uploaded_file($fileTmp, $uploadPath)) {
        return ['success' => true, 'filename' => $newFileName];
    }
    
    return ['success' => false, 'message' => 'Failed to upload file'];
}

// Format date
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Format datetime
function formatDateTime($datetime) {
    return date('M d, Y H:i A', strtotime($datetime));
}

// Get status badge HTML
function getStatusBadge($statusName, $statusColor) {
    return '<span class="status-badge" style="background-color: ' . $statusColor . '">' . clean($statusName) . '</span>';
}

// Get priority badge HTML
function getPriorityBadge($priority) {
    $class = 'priority-' . strtolower($priority);
    return '<span class="priority-badge ' . $class . '">' . clean($priority) . '</span>';
}
