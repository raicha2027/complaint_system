# Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Step 1: Extract Files (30 seconds)
Extract the `complaint_system` folder to your web server:
- **XAMPP**: `C:\xampp\htdocs\`
- **WAMP**: `C:\wamp\www\`
- **MAMP**: `/Applications/MAMP/htdocs/`

### Step 2: Start Your Server (30 seconds)
- Open XAMPP/WAMP/MAMP Control Panel
- Start **Apache** and **MySQL** services

### Step 3: Create Database (1 minute)
1. Open browser: `http://localhost/phpmyadmin`
2. Click "New" to create database
3. Name it: `student_complaints`
4. Go to "Import" tab
5. Choose file: `complaint_system/config/database.sql`
6. Click "Go"

### Step 4: Access the System (30 seconds)
Open browser: `http://localhost/complaint_system/`

### Step 5: Login (30 seconds)
**Option A - Use Default Admin:**
- Email: `admin@university.edu`
- Password: `admin123`

**Option B - Register New Student:**
- Click "Get Started"
- Fill in the registration form
- Login with your credentials

**Option C - Register New Admin:**
- Click "Register as Admin"
- Use access code: `AdminM25`

## 🎯 What to Do Next?

### As a Student:
1. Click "New Complaint" button
2. Select category (Hostel, Cafeteria, Academic, Service, Other)
3. Fill in title and description
4. Upload media if needed (optional)
5. Click "Submit Complaint"
6. View your complaints on dashboard
7. Check for admin feedback

### As an Admin:
1. View all complaints on dashboard
2. Use filters to sort by status/category
3. Click "Manage" on any complaint
4. Update status (Pending → In Progress → Resolved)
5. Assign to yourself if needed
6. Add feedback messages to students
7. Monitor statistics

## 💡 Pro Tips

### For Better Experience:
- Use **Google Chrome** for best compatibility
- Clear cache if you see old content
- Make sure `uploads/` folder has write permissions
- Keep admin access code secure: `AdminM25`

### File Uploads:
- Maximum size: **5MB**
- Supported types: JPG, PNG, GIF, PDF, DOC, DOCX
- Files are stored in `uploads/` folder
- Images show preview, documents show download link

### Categories:
- **Hostel**: Room issues, maintenance, facilities
- **Cafeteria**: Food quality, hygiene, service
- **Academic**: Course issues, exams, faculty
- **Service**: Library, IT, administration delays
- **Other**: Anything else

## ⚠️ Troubleshooting

### Problem: "Connection failed"
**Solution**: Check database credentials in `config/database.php`

### Problem: "Cannot upload file"
**Solution**: Set write permissions on `uploads/` folder
- Windows: Right-click → Properties → Security → Allow "Write"
- Mac/Linux: `chmod 755 uploads/`

### Problem: "Page not found"
**Solution**: Make sure files are in web server directory and Apache is running

### Problem: "Login not working"
**Solution**: 
- Clear browser cache
- Check if database has records (student/admin)
- Password must be at least 8 characters

## 📞 Need Help?

### Check These Files:
- `README.md` - Full documentation
- `CHANGELOG.md` - Feature list
- `config/database.sql` - Database structure

### System Requirements:
- PHP 7.4 or higher ✅
- MySQL 5.7 or higher ✅
- Apache with mod_rewrite ✅
- Modern web browser ✅

## 🎉 You're All Set!

The system is now ready to use. Start by:
1. ✅ Registering as a student
2. ✅ Submitting a test complaint
3. ✅ Logging in as admin
4. ✅ Managing the complaint

---

**Questions?** Check the full `README.md` file for detailed documentation.

**Enjoy using ComplaintHub!** 🚀
