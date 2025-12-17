<?php
require_once '../config/database.php';
require_once 'helpers.php';

requireStudent();

$studentId = getUserId();
$complaintId = $_GET['id'] ?? 0;

// Get complaint details
$stmt = $pdo->prepare("
    SELECT c.*, s.status_name, s.status_color, a.name as admin_name
    FROM complaints c 
    JOIN statuses s ON c.status_id = s.status_id 
    LEFT JOIN admins a ON c.assigned_to = a.admin_id
    WHERE c.complaint_id = ? AND c.student_id = ?
");
$stmt->execute([$complaintId, $studentId]);
$complaint = $stmt->fetch();

if (!$complaint) {
    header('Location: student_dashboard.php?error=Complaint not found');
    exit();
}

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Details - ComplaintHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="nav-container">
                <h1 class="logo"><i class="fas fa-comments"></i> ComplaintHub</h1>
                <div class="nav-links">
                    <span style="color: white;">Welcome, <?php echo clean(getUserName()); ?></span>
                    <a href="logout.php" class="btn-login">Logout</a>
                </div>
            </div>
        </nav>

        <div style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
            <!-- Back Button -->
            <div style="margin-bottom: 1rem;">
                <a href="student_dashboard.php" class="btn-action"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            </div>

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
                    <span><i class="fas fa-tag"></i> <?php echo clean($complaint['category']); ?></span>
                    <span><i class="fas fa-calendar"></i> <?php echo formatDateTime($complaint['date_submitted']); ?></span>
                    <?php if ($complaint['admin_name']): ?>
                        <span><i class="fas fa-user-shield"></i> Assigned to: <?php echo clean($complaint['admin_name']); ?></span>
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
                            $imagExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                            if (in_array(strtolower($ext), $imagExt)): 
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

            <!-- Feedback Section -->
            <div class="complaint-detail">
                <h2><i class="fas fa-comments"></i> Feedback & Updates</h2>
                
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
                        <p>No feedback yet. The admin will provide updates soon.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>
</html>
