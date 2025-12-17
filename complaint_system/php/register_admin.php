<?php
require_once '../config/database.php';
require_once 'helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

$name = clean($_POST['name'] ?? '');
$email = clean($_POST['email'] ?? '');
$accessCode = clean($_POST['access_code'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validate admin access code
define('ADMIN_ACCESS_CODE', 'AdminM25');

if ($accessCode !== ADMIN_ACCESS_CODE) {
    header('Location: ../index.php?error=Invalid admin access code');
    exit();
}

// Validate inputs
if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
    header('Location: ../index.php?error=All fields are required');
    exit();
}

if (!isValidEmail($email)) {
    header('Location: ../index.php?error=Invalid email format');
    exit();
}

if (!isValidPassword($password)) {
    header('Location: ../index.php?error=Password must be at least 8 characters');
    exit();
}

if ($password !== $confirmPassword) {
    header('Location: ../index.php?error=Passwords do not match');
    exit();
}

// Check if email already exists
$stmt = $pdo->prepare("SELECT email FROM admins WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    header('Location: ../index.php?error=Admin email already registered');
    exit();
}

// Hash password and insert admin
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO admins (name, email, password, role) VALUES (?, ?, ?, 'Admin')");
    $stmt->execute([$name, $email, $hashedPassword]);
    
    header('Location: ../index.php?success=Admin registration successful! Please login');
    exit();
} catch (PDOException $e) {
    header('Location: ../index.php?error=Registration failed. Please try again');
    exit();
}
