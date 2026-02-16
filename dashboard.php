<?php
session_start();
require_once "config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get current user info
$user_id = $_SESSION['user_id'];
$user_query = "SELECT full_name, account_type FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Update session with latest account type
$_SESSION['account_type'] = $user['account_type'];
$is_admin = ($user['account_type'] == 'Administrator');

// Get statistics
$total_students_query = "SELECT COUNT(*) as count FROM students";
$total_students_result = mysqli_query($conn, $total_students_query);
$total_students = mysqli_fetch_assoc($total_students_result)['count'];

$total_users_query = "SELECT COUNT(*) as count FROM users";
$total_users_result = mysqli_query($conn, $total_users_query);
$total_users = mysqli_fetch_assoc($total_users_result)['count'];

// Get recent students
$recent_students_query = "SELECT * FROM students ORDER BY created_at DESC LIMIT 5";
$recent_students_result = mysqli_query($conn, $recent_students_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Management System</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <h1>Dashboard</h1>
                <p class="username"><?php echo htmlspecialchars($user['full_name']); ?></p>
                <p class="role"><?php echo $user['account_type'] == 'Administrator' ? 'Admin' : 'Standard User'; ?></p>
            </div>
            
            <nav class="nav-menu">
                <a href="dashboard.php" class="nav-item active">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span>Home</span>
                </a>
                
                <?php if ($is_admin): ?>
                <a href="seeusers.php" class="nav-item">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>View Users</span>
                </a>
                <?php endif; ?>
                
                <a href="seestudents.php" class="nav-item">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                    </svg>
                    <span>View Students</span>
                </a>
            </nav>
            
            <a href="logout.php" class="logout-btn">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>Logout</span>
            </a>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <h1 class="page-title">Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>!</h1>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <span class="stat-label">Total Students</span>
                        <span class="stat-value"><?php echo $total_students; ?></span>
                    </div>
                    <div class="stat-icon blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                        </svg>
                    </div>
                </div>

                <?php if ($is_admin): ?>
                <div class="stat-card">
                    <div class="stat-info">
                        <span class="stat-label">Total Users</span>
                        <span class="stat-value"><?php echo $total_users; ?></span>
                    </div>
                    <div class="stat-icon green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                </div>
                <?php endif; ?>

                <div class="stat-card">
                    <div class="stat-info">
                        <span class="stat-label">Account Type</span>
                        <span class="stat-value" style="font-size: 18px;"><?php echo $user['account_type'] == 'Administrator' ? 'Admin' : 'Standard'; ?></span>
                    </div>
                    <div class="stat-icon purple">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Recent Section -->
            <div class="recent-section">
                <h2 class="section-title">Recently Added Students</h2>
                
                <?php if (mysqli_num_rows($recent_students_result) > 0): ?>
                <table class="recent-table" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:1px solid #e5e7eb;">
                            <th style="text-align:left;padding:12px;font-size:12px;font-weight:600;color:#6b7280;">Name</th>
                            <th style="text-align:left;padding:12px;font-size:12px;font-weight:600;color:#6b7280;">Email</th>
                            <th style="text-align:left;padding:12px;font-size:12px;font-weight:600;color:#6b7280;">Course</th>
                            <th style="text-align:left;padding:12px;font-size:12px;font-weight:600;color:#6b7280;">Year</th>
                            <th style="text-align:left;padding:12px;font-size:12px;font-weight:600;color:#6b7280;">Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($student = mysqli_fetch_assoc($recent_students_result)): ?>
                        <tr style="border-bottom:1px solid #f3f4f6;">
                            <td style="padding:16px 12px;font-size:14px;color:#374151;"><?php echo htmlspecialchars($student['fullname']); ?></td>
                            <td style="padding:16px 12px;font-size:14px;color:#374151;"><?php echo htmlspecialchars($student['email']); ?></td>
                            <td style="padding:16px 12px;font-size:14px;color:#374151;"><?php echo htmlspecialchars($student['course']); ?></td>
                            <td style="padding:16px 12px;font-size:14px;color:#374151;">Year <?php echo htmlspecialchars($student['year']); ?></td>
                            <td style="padding:16px 12px;font-size:14px;color:#374151;"><?php echo date('M d, Y', strtotime($student['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <p>No students registered yet</p>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
