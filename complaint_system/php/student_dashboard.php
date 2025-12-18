<?php
require_once '../config/database.php';
require_once 'helpers.php';

requireStudent();

$studentId = getUserId();
$studentName = getUserName();
$studentEmail = getUserEmail();
$studentDepartment = $_SESSION['user_department'] ?? '';

$stmt = $pdo->prepare("
    SELECT c.*, s.status_name, s.status_color 
    FROM complaints c 
    JOIN statuses s ON c.status_id = s.status_id 
    WHERE c.student_id = ? 
    ORDER BY c.date_submitted DESC
");
$stmt->execute([$studentId]);
$complaints = $stmt->fetchAll();

$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status_id = 2 THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN status_id = 3 THEN 1 ELSE 0 END) as resolved
    FROM complaints 
    WHERE student_id = ?
");
$stmt->execute([$studentId]);
$stats = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - ComplaintHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="nav-container">
                <h1 class="logo"><i class="fas fa-comments"></i> ComplaintSystem</h1>
                <div class="nav-links">
                    <span style="color: white;">Welcome, <?php echo clean($studentName); ?></span>
                    <a href="logout.php" class="btn-login">Logout</a>
                </div>
            </div>
        </nav>

        <div style="max-width: 1400px; margin: 0 auto; padding: 2rem;">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <h1><i class="fas fa-tachometer-alt"></i> My Dashboard</h1>
                <p>Welcome back, <?php echo clean($studentName); ?>! Department: <?php echo clean($studentDepartment); ?></p>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-nav">
                <div>
                    <a href="#" onclick="openModal('newComplaintModal')"><i class="fas fa-plus"></i> New Complaint</a>
                    <a href="#complaints"><i class="fas fa-list"></i> My Complaints</a>
                </div>
                <a href="logout.php" class="btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="color: #667eea;"><i class="fas fa-clipboard-list"></i></div>
                    <div class="stat-value"><?php echo $stats['total']; ?></div>
                    <div class="stat-label">Total Complaints</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="color: #ffc107;"><i class="fas fa-clock"></i></div>
                    <div class="stat-value"><?php echo $stats['pending']; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="color: #17a2b8;"><i class="fas fa-spinner"></i></div>
                    <div class="stat-value"><?php echo $stats['in_progress']; ?></div>
                    <div class="stat-label">In Progress</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="color: #28a745;"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-value"><?php echo $stats['resolved']; ?></div>
                    <div class="stat-label">Resolved</div>
                </div>
            </div>

            <!-- Complaints List -->
            <div class="table-container" id="complaints">
                <h2><i class="fas fa-list"></i> My Complaints</h2>
                
                <?php if (count($complaints) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($complaints as $complaint): ?>
                            <tr>
                                <td><strong>#<?php echo $complaint['complaint_id']; ?></strong></td>
                                <td>
                                    <strong><?php echo clean($complaint['title']); ?></strong><br>
                                    <small><?php echo substr(clean($complaint['description']), 0, 50) . '...'; ?></small>
                                </td>
                                <td><i class="fas fa-tag"></i> <?php echo clean($complaint['category']); ?></td>
                                <td><?php echo getPriorityBadge($complaint['priority']); ?></td>
                                <td><?php echo getStatusBadge($complaint['status_name'], $complaint['status_color']); ?></td>
                                <td><?php echo formatDate($complaint['date_submitted']); ?></td>
                                <td>
                                    <a href="view_complaint.php?id=<?php echo $complaint['complaint_id']; ?>" class="btn-action">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-inbox"></i>
                        <h3>No Complaints Yet</h3>
                        <p>You haven't submitted any complaints. Click "New Complaint" to get started.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- New Complaint Modal -->
    <div id="newComplaintModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <span class="close" onclick="closeModal('newComplaintModal')">&times;</span>
            <h2><i class="fas fa-plus"></i> Submit New Complaint</h2>
            
            <form action="submit_complaint.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Category *</label>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        <option value="Hostel">Hostel Problems</option>
                        <option value="Cafeteria">Cafeteria Concerns</option>
                        <option value="Academic">Academic Challenges</option>
                        <option value="Service">Service Delays</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Title *</label>
                    <input type="text" name="title" required maxlength="200" placeholder="Brief title of your complaint">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Description *</label>
                    <textarea name="description" required rows="5" placeholder="Describe your complaint in detail..."></textarea>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-exclamation-circle"></i> Priority</label>
                    <select name="priority">
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-paperclip"></i> Attach Media (Optional)</label>
                    <div class="file-upload-wrapper">
                        <input type="file" name="media" id="mediaFile" accept="image/*,application/pdf,.doc,.docx">
                        <label for="mediaFile" class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i> Choose File (Max 5MB)
                        </label>
                    </div>
                    <small>Supported: Images, PDF, DOC, DOCX</small>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-paper-plane"></i> Submit Complaint</button>
            </form>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>
</html>
