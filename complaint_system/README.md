# Student Complaint Management System

A comprehensive web-based platform for managing student complaints with separate portals for students and administrators.

## Features

### Student Features
- Register and login to personal account
- Submit complaints with categories (Hostel, Cafeteria, Academic, Service, Other)
- Upload media attachments (images, PDFs, documents) with complaints
- View all submitted complaints and their status
- Track complaint progress (Pending, In Progress, Resolved, Rejected)
- Receive and view feedback from administrators

### Admin Features
- Register with admin access code: **AdminM25**
- View all complaints from all students
- Filter complaints by status and category
- Update complaint status
- Assign complaints to specific admins
- Provide feedback and updates to students
- View statistics and category breakdown

## Installation Guide

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Web browser

### Step 1: Extract Files
Extract the complaint_system folder to your web server directory:
- **XAMPP**: `C:\xampp\htdocs\`
- **WAMP**: `C:\wamp\www\`
- **LAMP**: `/var/www/html/`

### Step 2: Create Database
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `student_complaints`
3. Import the SQL schema:
   - Click on the `student_complaints` database
   - Go to the "Import" tab
   - Choose file: `complaint_system/config/database.sql`
   - Click "Go" to execute

### Step 3: Configure Database Connection
The database connection is already configured in `config/database.php`:
```php
$host = 'localhost';
$dbname = 'student_complaints';
$username = 'root';
$password = '';
```
If your MySQL has a different username/password, update these values.

### Step 4: Set Upload Folder Permissions
Make sure the `uploads` folder has write permissions:
- **Windows**: Right-click > Properties > Security > Edit > Allow "Full control"
- **Linux/Mac**: `chmod 777 complaint_system/uploads`

### Step 5: Access the System
Open your web browser and navigate to:
```
http://localhost/complaint_system/
```

## Default Accounts

### Admin Account
- **Email**: admin@university.edu
- **Password**: admin123

### Test Student Account
You can register a new student account directly from the homepage.

### New Admin Registration
- Click "Register as Admin" on the homepage
- Use access code: **AdminM25**

## File Structure

```
complaint_system/
├── config/
│   ├── database.php          # Database connection (PDO)
│   └── database.sql           # SQL schema
├── php/
│   ├── helpers.php            # Helper functions
│   ├── login.php              # Login handler
│   ├── logout.php             # Logout handler
│   ├── register_student.php   # Student registration
│   ├── register_admin.php     # Admin registration (requires access code)
│   ├── student_dashboard.php  # Student dashboard
│   ├── submit_complaint.php   # Submit complaint handler
│   └── view_complaint.php     # View complaint details (student)
├── admin/
│   ├── dashboard.php          # Admin dashboard
│   └── manage_complaint.php   # Manage complaint (admin)
├── css/
│   └── style.css              # Main stylesheet
├── js/
│   └── main.js                # JavaScript functionality
├── images/
│   └── (place images here)
├── uploads/
│   └── (complaint media uploads)
├── index.php                  # Landing page
└── README.md                  # This file
```

## Usage Guide

### For Students
1. **Register**: Click "Get Started" or "Register" on the homepage
2. **Login**: Enter your email and password
3. **Submit Complaint**: Click "New Complaint" button
4. **Select Category**: Choose from Hostel, Cafeteria, Academic, Service, or Other
5. **Add Details**: Enter title and description
6. **Attach Media** (Optional): Upload supporting images or documents
7. **Track Status**: View your complaints on the dashboard
8. **View Feedback**: Click "View" on any complaint to see admin feedback

### For Administrators
1. **Register**: Click "Register as Admin" and use code: **AdminM25**
2. **Login**: Enter your admin credentials
3. **View Complaints**: See all complaints on the dashboard
4. **Filter**: Use status and category filters
5. **Manage**: Click "Manage" on any complaint
6. **Update Status**: Change complaint status (Pending, In Progress, Resolved, Rejected)
7. **Assign**: Assign complaint to yourself
8. **Provide Feedback**: Add messages that students can see
9. **View Statistics**: Monitor complaint trends and resolution rates

## Security Features
- Password hashing using PHP password_hash()
- SQL injection prevention using PDO prepared statements
- XSS protection using htmlspecialchars()
- Session management with secure logout
- File upload validation (type and size)
- Admin access code verification

## Supported File Types for Upload
- **Images**: JPG, JPEG, PNG, GIF
- **Documents**: PDF, DOC, DOCX
- **Maximum Size**: 5MB per file

## Troubleshooting

### Database Connection Error
- Verify database credentials in `config/database.php`
- Ensure MySQL service is running
- Check if database `student_complaints` exists

### File Upload Issues
- Check `uploads/` folder permissions
- Verify PHP `upload_max_filesize` in `php.ini`
- Ensure file size is under 5MB

### Login Issues
- Clear browser cache and cookies
- Check if email is registered
- Verify password is at least 8 characters

### Page Not Found (404)
- Ensure files are in correct web server directory
- Check file paths in code match your setup
- Verify Apache/Nginx is running

## Browser Compatibility
- Google Chrome (recommended)
- Mozilla Firefox
- Microsoft Edge
- Safari

## Technologies Used
- **Backend**: PHP 7.4+
- **Database**: MySQL with PDO
- **Frontend**: HTML5, CSS3, JavaScript
- **Icons**: Font Awesome 6.4.0
- **Fonts**: Google Fonts (Poppins)

## Support
For issues or questions, contact: support@complainthub.edu

## License
This project is for educational purposes.

---
