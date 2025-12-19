<?php
require_once '../config/database.php';
require_once '../php/helpers.php';

requireAdmin();

$adminId = getUserId();
$adminName = getUserName();
$complaintId = $_GET['id'] ?? 0;

// Get complaint details
$stmt = $pdo->prepare("
    SELECT c.*, s.status_name, s.status_color, st.name as student_name, st.email as student_email, 
           st.department, a.name as assigned_admin_name
    FROM complaints c
    JOIN statuses s ON c.status_id = s.status_id
    JOIN students st ON c.student_id = st.student_id
    LEFT JOIN admins a ON c.assigned_to = a.admin_id
    WHERE c.complaint_id = ?
");
$stmt->execute([$complaintId]);
$complaint = $stmt->fetch();

if (!$complaint) {
    header('Location: dashboard.php?error=Complaint not found');
    exit();
}

// Get all statuses
$stmt = $pdo->query("SELECT * FROM statuses ORDER BY status_id");
$statuses = $stmt->fetchAll();

// Get feedback
$stmt = $pdo->prepare("
    SELECT f.*, a.name as admin_name
    FROM complaint_feedback f
    JOIN admins a ON f.admin_id = a.admin_id
    WHERE f.complaint_id = ?
    ORDER BY f.created_at DESC
");
$stmt->execute([$complaintId]);
$feedbacks = $stmt->fetchAll();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $newStatusId = $_POST['status_id'] ?? 0;
        $assignToMe = isset($_POST['assign_to_me']);
        
        $assignedTo = $assignToMe ? $adminId : $complaint['assigned_to'];
        
        $stmt = $pdo->prepare("UPDATE complaints SET status_id = ?, assigned_to = ? WHERE complaint_id = ?");
        $stmt->execute([$newStatusId, $assignedTo, $complaintId]);
        
        header("Location: manage_complaint.php?id=$complaintId&success=Status updated successfully");
        exit();
    }
    
    if (isset($_POST['add_feedback'])) {
        $feedbackMessage = clean($_POST['feedback_message'] ?? '');
        
        if (!empty($feedbackMessage)) {
            $stmt = $pdo->prepare("INSERT INTO complaint_feedback (complaint_id, admin_id, feedback_message) VALUES (?, ?, ?)");
            $stmt->execute([$complaintId, $adminId, $feedbackMessage]);
            
            header("Location: manage_complaint.php?id=$complaintId&success=Feedback added successfully");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Complaint - ComplaintHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="nav-container">
                <h1 class="logo"><i class="fas fa-user-shield"></i> Admin Panel</h1>
                <div class="nav-links">
                    <span style="color: white;">Admin: <?php echo clean($adminName); ?></span>
                    <a href="../php/logout.php" class="btn-login">Logout</a>
                </div>
            </div>
        </nav>

        <div style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
            <!-- Back Button -->
            <div style="margin-bottom: 1rem;">
                <a href="dashboard.php" class="btn-action"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?php echo clean($_GET['success']); ?></div>
            <?php endif; ?>

            <!-- Complaint Details -->
            <div class="complaint-detail">
                <div class="complaint-header">
                    <h1><?php echo clean($complaint['title']); ?></h1>
                    <div style="margin-top: 1rem;">
                        <?php echo getStatusBadge($complaint['status_name'], $complaint['status_color']); ?>
                        <?php echo getPriorityBadge($complaint['priority']); ?>
                    </div>
                </div>

                <div class="complaint-meta">
                    <span><i class="fas fa-hashtag"></i> ID: <?php echo $complaint['complaint_id']; ?></span>
                    <span><i class="fas fa-user"></i> <?php echo clean($complaint['student_name']); ?></span>
                    <span><i class="fas fa-envelope"></i> <?php echo clean($complaint['student_email']); ?></span>
                    <span><i class="fas fa-building"></i> <?php echo clean($complaint['department']); ?></span>
                    <span><i class="fas fa-tag"></i> <?php echo clean($complaint['category']); ?></span>
                    <span><i class="fas fa-calendar"></i> <?php echo formatDateTime($complaint['date_submitted']); ?></span>
                    <?php if ($complaint['assigned_admin_name']): ?>
                        <span><i class="fas fa-user-shield"></i> Assigned to: <?php echo clean($complaint['assigned_admin_name']); ?></span>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 2rem;">
                    <h3><i class="fas fa-align-left"></i> Description</h3>
                    <p style="line-height: 1.8; color: #666; margin-top: 1rem;">
                        <?php echo nl2br(clean($complaint['description'])); ?>
                    </p>
                </div>

                <?php if ($complaint['media_file']): ?>
                    <div style="margin-top: 2rem;">
                        <h3><i class="fas fa-paperclip"></i> Attached Media</h3>
                        <div class="media-preview">
                            <?php 
                            $ext = pathinfo($complaint['media_file'], PATHINFO_EXTENSION);
                            $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                            if (in_array(strtolower($ext), $imageExt)): 
                            ?>
                                <img src="../uploads/<?php echo $complaint['media_file']; ?>" alt="Complaint Media">
                            <?php else: ?>
                                <a href="../uploads/<?php echo $complaint['media_file']; ?>" target="_blank" class="btn-action">
                                    <i class="fas fa-download"></i> Download Attachment
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Update Status Form -->
            <div class="complaint-detail">
                <h2><i class="fas fa-edit"></i> Update Status</h2>
                <form method="POST" style="margin-top: 1rem;">
                    <div class="form-group">
                        <select name="status_id" required>
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?php echo $status['status_id']; ?>" 
                                    <?php echo $status['status_id'] == $complaint['status_id'] ? 'selected' : ''; ?>>
                                    <?php echo clean($status['status_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="assign_to_me" <?php echo $complaint['assigned_to'] == $adminId ? 'checked' : ''; ?>>
                            Assign this complaint to me
                        </label>
                    </div>
                    <button type="submit" name="update_status" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </form>
            </div>

            <!-- Add Feedback Form -->
            <div class="complaint-detail">
                <h2><i class="fas fa-comment-dots"></i> Add Feedback</h2>
                <form method="POST" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label><i class="fas fa-message"></i> Feedback Message</label>
                        <textarea name="feedback_message" required rows="4" 
                            placeholder="Provide feedback or updates to the student..."></textarea>
                    </div>
                    <button type="submit" name="add_feedback" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> Send Feedback
                    </button>
                </form>
            </div>

            <!-- Previous Feedback -->
            <div class="complaint-detail">
                <h2><i class="fas fa-history"></i> Feedback History</h2>
                
                <?php if (count($feedbacks) > 0): ?>
                    <div class="feedback-section">
                        <?php foreach ($feedbacks as $feedback): ?>
                            <div class="feedback-item">
                                <div class="feedback-header">
                                    <strong><i class="fas fa-user-shield"></i> <?php echo clean($feedback['admin_name']); ?></strong>
                                    <span><?php echo formatDateTime($feedback['created_at']); ?></span>
                                </div>
                                <p style="margin-top: 0.5rem; color: #333;">
                                    <?php echo nl2br(clean($feedback['feedback_message'])); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-data" style="padding: 2rem;">
                        <i class="fas fa-inbox"></i>
                        <p>No feedback has been provided yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>
</html>
