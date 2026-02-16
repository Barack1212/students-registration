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

// ADMIN ONLY - Redirect standard users
if ($user['account_type'] !== 'Administrator') {
    header("Location: dashboard.php");
    exit();
}

// Handle Delete Action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Prevent admin from deleting themselves
    if ($id != $_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    }
    header("Location: seeusers.php");
    exit();
}

// Handle Update Action
if (isset($_POST['update_user'])) {
    $id = intval($_POST['user_id']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $account_type = mysqli_real_escape_string($conn, $_POST['account_type']);
    
    // Update password only if provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = "UPDATE users SET full_name = '$full_name', email = '$email', 
                  account_type = '$account_type', password = '$password' WHERE id = $id";
    } else {
        $query = "UPDATE users SET full_name = '$full_name', email = '$email', 
                  account_type = '$account_type' WHERE id = $id";
    }
    
    mysqli_query($conn, $query);
    header("Location: seeusers.php");
    exit();
}

// Get user data for editing
$edit_user = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
    $edit_user = mysqli_fetch_assoc($result);
}

// Check for success message
$success_message = '';
if (isset($_GET['success'])) {
    $success_message = 'User created successfully!';
}

// Get all users
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <style>
        /* Page Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
    background-color: #f8f9fa;
    color: #333;
}

.container {
    display: flex;
    min-height: 100vh;
}

.main-content {
    flex: 1;
    margin-left: 260px;
    padding: 32px 40px;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.sidebar {
    width: 260px;
    background: #fff;
    border-right: 1px solid #e9ecef;
    display: flex;
    flex-direction: column;
    padding: 24px 16px;
    position: fixed;
    height: 100vh;
}

.logo {
    margin-bottom: 32px;
    padding: 0 8px;
}

.logo h1 {
    font-size: 24px;
    font-weight: 700;
    color: #4f46e5;
    margin-bottom: 4px;
}

.username {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 2px;
}

.role {
    font-size: 12px;
    color: #9ca3af;
    text-transform: capitalize;
}

.nav-menu {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    text-decoration: none;
    color: #4b5563;
    border-radius: 8px;
    transition: all 0.2s ease;
    font-size: 14px;
    font-weight: 500;
}

.nav-item:hover {
    background-color: #f3f4f6;
    color: #374151;
}

.nav-item.active {
    background-color: #eef2ff;
    color: #4f46e5;
}

.icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

.logout-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    text-decoration: none;
    color: #dc2626;
    border-radius: 8px;
    transition: all 0.2s ease;
    font-size: 14px;
    font-weight: 500;
    margin-top: auto;
}

.logout-btn:hover {
    background-color: #fef2f2;
}

.btn-primary {
    background-color: #4f46e5;
    color: white;
}

.btn-primary:hover {
    background-color: #4338ca;
}

.btn-secondary {
    background-color: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background-color: #d1d5db;
}

/* Table Container */
.table-container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table thead {
    background-color: #f9fafb;
}

.data-table th {
    text-align: left;
    padding: 16px 24px;
    font-size: 14px;
    font-weight: 600;
    color: #4b5563;
    border-bottom: 1px solid #e5e7eb;
}

.data-table td {
    padding: 16px 24px;
    font-size: 14px;
    color: #111827;
    border-bottom: 1px solid #f3f4f6;
}

.data-table tbody tr:hover {
    background-color: #f9fafb;
}

.data-table tbody tr:last-child td {
    border-bottom: none;
}

/* Badges */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 9999px;
    font-size: 12px;
    font-weight: 500;
    text-transform: lowercase;
}

.badge.administrator {
    background-color: #f3e8ff;
    color: #9333ea;
}

.badge.standard-user {
    background-color: #dbeafe;
    color: #2563eb;
}

/* Action Buttons */
.actions {
    display: flex;
    gap: 8px;
}

.action-btn {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s;
}

.action-btn svg {
    width: 16px;
    height: 16px;
}

.action-btn.edit {
    color: #4f46e5;
    background-color: #eef2ff;
}

.action-btn.edit:hover {
    background-color: #e0e7ff;
}

.action-btn.delete {
    color: #dc2626;
    background-color: #fef2f2;
}

.action-btn.delete:hover {
    background-color: #fee2e2;
}

/* Modal */
.modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
    z-index: 1000;
    width: 90%;
    max-width: 500px;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 999;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

.close-btn {
    text-decoration: none;
    font-size: 24px;
    color: #9ca3af;
    line-height: 1;
}

.close-btn:hover {
    color: #4b5563;
}

.modal form {
    padding: 24px;
}

.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    color: #111827;
    background: white;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
}
    </style>
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
                <a href="dashboard.php" class="nav-item">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span>Home</span>
                </a>
                
                <a href="seeusers.php" class="nav-item active">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>View Users</span>
                </a>
                
                <a href="seestudents.php" class="nav-item">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                    </svg>
                    <span>Add Students</span>
                </a>
            </nav>
            
            <a href="home.php" class="logout-btn">
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
            <div class="page-header">
                <h1 class="page-title">User Management</h1>
                <a href="adduser.php" class="btn btn-primary">Create New User</a>
            </div>

            <?php if ($success_message): ?>
            <div style="background-color:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:12px 16px;border-radius:8px;font-size:14px;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <?php echo htmlspecialchars($success_message); ?>
            </div>
            <?php endif; ?>

            <!-- Edit Modal -->
            <?php if ($edit_user): ?>
            <div class="modal" id="editModal" style="display: block;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Edit User</h3>
                        <a href="seeusers.php" class="close-btn">&times;</a>
                    </div>
                    <form method="POST" action="">
                        <input type="hidden" name="user_id" value="<?php echo $edit_user['id']; ?>">
                        
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" value="<?php echo htmlspecialchars($edit_user['full_name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($edit_user['email']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Role</label>
                            <select name="account_type" required>
                                <option value="Administrator" <?php echo $edit_user['account_type'] == 'Administrator' ? 'selected' : ''; ?>>Administrator</option>
                                <option value="Standard User" <?php echo $edit_user['account_type'] == 'Standard User' ? 'selected' : ''; ?>>Standard User</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>New Password (leave blank to keep current)</label>
                            <input type="password" name="password" placeholder="Enter new password">
                        </div>
                        
                        <div class="modal-actions">
                            <a href="seeusers.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" name="update_user" class="btn btn-primary">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-overlay" style="display: block;"></div>
            <?php endif; ?>

            <!-- Users Table -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)) { 
                            $roleClass = strtolower(str_replace(' ', '-', $row['account_type']));
                            $createdDate = date('n/j/Y', strtotime($row['created_at']));
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <span class="badge <?php echo $roleClass; ?>">
                                    <?php echo strtolower($row['account_type']); ?>
                                </span>
                            </td>
                            <td><?php echo $createdDate; ?></td>
                            <td class="actions">
                                <a href="?edit=<?php echo $row['id']; ?>" class="action-btn edit" title="Edit">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>
                                <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                <a href="?delete=<?php echo $row['id']; ?>" class="action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this user?');">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>