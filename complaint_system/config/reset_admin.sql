-- Reset Admin Password
-- This file resets the default admin password to: admin123

USE student_complaints;

-- Update admin password
UPDATE admins 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE email = 'admin@university.edu';

-- Verify the update
SELECT admin_id, name, email, role, created_at 
FROM admins 
WHERE email = 'admin@university.edu';
