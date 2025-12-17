<?php
require_once '../config/database.php';
require_once 'helpers.php';

requireStudent();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: student_dashboard.php');
    exit();
}

$studentId = getUserId();
$category = clean($_POST['category'] ?? '');
$title = clean($_POST['title'] ?? '');
$description = clean($_POST['description'] ?? '');
$priority = clean($_POST['priority'] ?? 'Medium');

// Validate inputs
if (empty($category) || empty($title) || empty($description)) {
    header('Location: student_dashboard.php?error=All required fields must be filled');
    exit();
}

// Validate category
$validCategories = ['Hostel', 'Cafeteria', 'Academic', 'Service', 'Other'];
if (!in_array($category, $validCategories)) {
    header('Location: student_dashboard.php?error=Invalid category');
    exit();
}

// Validate priority
$validPriorities = ['Low', 'Medium', 'High'];
if (!in_array($priority, $validPriorities)) {
    $priority = 'Medium';
}

// Handle file upload
$mediaFile = null;
if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
    $uploadResult = uploadFile($_FILES['media']);
    if ($uploadResult['success']) {
        $mediaFile = $uploadResult['filename'];
    }
}

// Insert complaint
try {
    $stmt = $pdo->prepare("
        INSERT INTO complaints (student_id, category, title, description, priority, media_file, status_id) 
        VALUES (?, ?, ?, ?, ?, ?, 1)
    ");
    $stmt->execute([$studentId, $category, $title, $description, $priority, $mediaFile]);
    
    header('Location: student_dashboard.php?success=Complaint submitted successfully');
    exit();
} catch (PDOException $e) {
    // Delete uploaded file if database insert fails
    if ($mediaFile && file_exists('../uploads/' . $mediaFile)) {
        unlink('../uploads/' . $mediaFile);
    }
    
    header('Location: student_dashboard.php?error=Failed to submit complaint');
    exit();
}
