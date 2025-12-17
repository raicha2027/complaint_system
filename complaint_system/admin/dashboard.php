<?php
require_once '../config/database.php';
require_once '../php/helpers.php';

requireAdmin();

$adminId = getUserId();
$adminName = getUserName();

// Get filters
$statusFilter = $_GET['status'] ?? '';
$categoryFilter = $_GET['category'] ?? '';

// Build query
$query = "
    SELECT c.*, s.status_name, s.status_color, st.name as student_name, st.email as student_email, st.department,
           a.name as admin_name
    FROM complaints c
    JOIN statuses s ON c.status_id = s.status_id
    JOIN students st ON c.student_id = st.student_id
    LEFT JOIN admins a ON c.assigned_to = a.admin_id
    WHERE 1=1
";

$params = [];

if ($statusFilter) {
    $query .= " AND s.status_name = ?";
    $params[] = $statusFilter;
}

if ($categoryFilter) {
    $query .= " AND c.category = ?";
    $params[] = $categoryFilter;
}

$query .= " ORDER BY c.date_submitted DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$complaints = $stmt->fetchAll();

// Get statistics
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status_id = 2 THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN status_id = 3 THEN 1 ELSE 0 END) as resolved,
        SUM(CASE WHEN status_id = 4 THEN 1 ELSE 0 END) as rejected
    FROM complaints
");
$stats = $stmt->fetch();

// Get category breakdown
$stmt = $pdo->query("SELECT category, COUNT(*) as count FROM complaints GROUP BY category ORDER BY count DESC");
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ComplaintHub</title>
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

        <div style="max-width: 1400px; margin: 0 auto; padding: 2rem;">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
                <p>Manage and review all student complaints</p>
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

            <!-- Filters -->
            <div class="table-container" style="margin-bottom: 1rem;">
                <h3><i class="fas fa-filter"></i> Filter Complaints</h3>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1rem;">
                    <div>
                        <label style="font-weight: 600; display: block; margin-bottom: 0.5rem;">Status:</label>
                        <select onchange="window.location.href='dashboard.php?status=' + this.value + '<?php echo $categoryFilter ? '&category=' . $categoryFilter : ''; ?>'" style="padding: 0.5rem; border-radius: 5px; border: 2px solid #e1e8ed;">
                            <option value="">All Statuses</option>
                            <option value="Pending" <?php echo $statusFilter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="In Progress" <?php echo $statusFilter === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="Resolved" <?php echo $statusFilter === 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                            <option value="Rejected" <?php echo $statusFilter === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-weight: 600; display: block; margin-bottom: 0.5rem;">Category:</label>
                        <select onchange="window.location.href='dashboard.php?category=' + this.value + '<?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?>'" style="padding: 0.5rem; border-radius: 5px; border: 2px solid #e1e8ed;">
                            <option value="">All Categories</option>
                            <option value="Hostel" <?php echo $categoryFilter === 'Hostel' ? 'selected' : ''; ?>>Hostel</option>
                            <option value="Cafeteria" <?php echo $categoryFilter === 'Cafeteria' ? 'selected' : ''; ?>>Cafeteria</option>
                            <option value="Academic" <?php echo $categoryFilter === 'Academic' ? 'selected' : ''; ?>>Academic</option>
                            <option value="Service" <?php echo $categoryFilter === 'Service' ? 'selected' : ''; ?>>Service</option>
                            <option value="Other" <?php echo $categoryFilter === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <?php if ($statusFilter || $categoryFilter): ?>
                        <div style="display: flex; align-items: flex-end;">
                            <a href="dashboard.php" class="btn-danger" style="padding: 0.6rem 1rem;">
                                <i class="fas fa-times"></i> Clear Filters
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Category Breakdown -->
            <?php if (count($categories) > 0): ?>
                <div class="table-container" style="margin-bottom: 2rem;">
                    <h3><i class="fas fa-chart-pie"></i> Complaints by Category</h3>
                    <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
                        <?php foreach ($categories as $cat): ?>
                            <div style="background: #f8f9fa; padding: 1rem 1.5rem; border-radius: 10px; display: flex; align-items: center; gap: 0.5rem;">
                                <strong><?php echo clean($cat['category']); ?>:</strong>
                                <span style="background: #667eea; color: white; padding: 0.3rem 0.8rem; border-radius: 15px; font-weight: 600;">
                                    <?php echo $cat['count']; ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Complaints Table -->
            <div class="table-container">
                <h2><i class="fas fa-list"></i> All Complaints</h2>
                
                <?php if (count($complaints) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Student</th>
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
                                <td>
                                    <?php echo clean($complaint['student_name']); ?><br>
                                    <small><?php echo clean($complaint['department']); ?></small>
                                </td>
                                <td><i class="fas fa-tag"></i> <?php echo clean($complaint['category']); ?></td>
                                <td><?php echo getPriorityBadge($complaint['priority']); ?></td>
                                <td><?php echo getStatusBadge($complaint['status_name'], $complaint['status_color']); ?></td>
                                <td><?php echo formatDate($complaint['date_submitted']); ?></td>
                                <td>
                                    <a href="manage_complaint.php?id=<?php echo $complaint['complaint_id']; ?>" class="btn-action">
                                        <i class="fas fa-edit"></i> Manage
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-inbox"></i>
                        <h3>No Complaints Found</h3>
                        <p>No complaints match your filter criteria.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>
</html>
