<?php
require_once '../config/database.php';
require_once 'helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

$name = clean($_POST['name'] ?? '');
$email = clean($_POST['email'] ?? '');
$department = clean($_POST['department'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validate inputs
if (empty($name) || empty($email) || empty($department) || empty($password) || empty($confirmPassword)) {
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
$stmt = $pdo->prepare("SELECT email FROM students WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    header('Location: ../index.php?error=Email already registered');
    exit();
}

// Hash password and insert student
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO students (name, email, department, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $department, $hashedPassword]);
    
    header('Location: ../index.php?success=Registration successful! Please login');
    exit();
} catch (PDOException $e) {
    header('Location: ../index.php?error=Registration failed. Please try again');
    exit();
}
