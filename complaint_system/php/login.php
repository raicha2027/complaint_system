<?php
require_once '../config/database.php';
require_once 'helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

$email = clean($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

// Validate inputs
if (empty($email) || empty($password)) {
    header('Location: ../index.php?error=Email and password are required');
    exit();
}

if (!isValidEmail($email)) {
    header('Location: ../index.php?error=Invalid email format');
    exit();
}

// Try student login first
$stmt = $pdo->prepare("SELECT student_id, name, email, department, password FROM students WHERE email = ?");
$stmt->execute([$email]);
$student = $stmt->fetch();

if ($student && password_verify($password, $student['password'])) {
    // Student login successful
    $_SESSION['user_id'] = $student['student_id'];
    $_SESSION['user_name'] = $student['name'];
    $_SESSION['user_email'] = $student['email'];
    $_SESSION['user_department'] = $student['department'];
    $_SESSION['user_type'] = 'student';
    
    if ($remember) {
        setcookie('user_email', $email, time() + (86400 * 30), '/');
    }
    
    header('Location: ../php/student_dashboard.php');
    exit();
}

// Try admin login
$stmt = $pdo->prepare("SELECT admin_id, name, email, role, password FROM admins WHERE email = ?");
$stmt->execute([$email]);
$admin = $stmt->fetch();

if ($admin && password_verify($password, $admin['password'])) {
    // Admin login successful
    $_SESSION['user_id'] = $admin['admin_id'];
    $_SESSION['user_name'] = $admin['name'];
    $_SESSION['user_email'] = $admin['email'];
    $_SESSION['user_role'] = $admin['role'];
    $_SESSION['user_type'] = 'admin';
    
    if ($remember) {
        setcookie('user_email', $email, time() + (86400 * 30), '/');
    }
    
    header('Location: ../admin/dashboard.php');
    exit();
}

// Login failed
header('Location: ../index.php?error=Invalid email or password');
exit();
