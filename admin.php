<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle Delete Action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM students WHERE id = $id");
    header("Location: seestudents.php");
    exit();
}

// Handle Update Action
if (isset($_POST['update_student'])) {
    $id = intval($_POST['student_id']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    $query = "UPDATE students SET fullname = '$fullname', email = '$email', 
              phone = '$phone', course = '$course', year = '$year', address = '$address' 
              WHERE id = $id";
    
    mysqli_query($conn, $query);
    header("Location: seestudents.php");
    exit();
}

// Get student data for editing
$edit_student = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM students WHERE id = $id");
    $edit_student = mysqli_fetch_assoc($result);
}

// Get current user info
$user_id = $_SESSION['user_id'];
$user_query = "SELECT full_name, account_type FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Handle search
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$search_condition = '';
if (!empty($search)) {
    $search_condition = " WHERE fullname LIKE '%$search%' OR email LIKE '%$search%' OR course LIKE '%$search%'";
}

// Get all students
$students_query = "SELECT * FROM students" . $search_condition . " ORDER BY created_at DESC";
$students_result = mysqli_query($conn, $students_query);
$total_students = mysqli_num_rows($students_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <style>
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

        /* Sidebar */
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

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 32px;
            color: #111827;
            font-weight: 700;
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

        /* Search Box */
        .search-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
        }

        .search-box {
            position: relative;
            width: 100%;
        }

        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: #9ca3af;
        }

        .search-input {
            width: 100%;
            padding: 12px 16px 12px 48px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            color: #111827;
            background: #f9fafb;
        }

        .search-input:focus {
            outline: none;
            border-color: #4f46e5;
            background: white;
        }

        .search-input::placeholder {
            color: #9ca3af;
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

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .empty-state-text {
            font-size: 16px;
            color: #6b7280;
        }

        .student-count {
            padding: 16px 24px;
            font-size: 14px;
            color: #6b7280;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
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
            border: none;
            cursor: pointer;
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
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
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
            cursor: pointer;
        }

        .close-btn:hover {
            color: #4b5563;
        }

        .modal form {
            padding: 24px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            color: #111827;
            background: white;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
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
                <a href="home.php" class="nav-item">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span>Home</span>
                </a>
                
                <a href="adduser.php" class="nav-item">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>Create User</span>
                </a>
                
                <a href="seestudents.php" class="nav-item active">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                    </svg>
                    <span>Add Students</span>
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
            <div class="page-header">
                <h1 class="page-title">Student Management</h1>
                <a href="addstudent.php" class="btn btn-primary">Add New Student</a>
            </div>

            <!-- Edit Modal -->
            <?php if ($edit_student): ?>
            <div class="modal" id="editModal" style="display: block;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Edit Student</h3>
                        <a href="seestudents.php" class="close-btn">&times;</a>
                    </div>
                    <form method="POST" action="">
                        <input type="hidden" name="student_id" value="<?php echo $edit_student['id']; ?>">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Full Name *</label>
                                <input type="text" name="fullname" value="<?php echo htmlspecialchars($edit_student['fullname']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($edit_student['email']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Phone *</label>
                                <input type="text" name="phone" value="<?php echo htmlspecialchars($edit_student['phone']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Course *</label>
                                <input type="text" name="course" value="<?php echo htmlspecialchars($edit_student['course']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Year *</label>
                            <select name="year" required>
                                <option value="">Select Year</option>
                                <option value="1" <?php echo $edit_student['year'] == '1' ? 'selected' : ''; ?>>Year 1</option>
                                <option value="2" <?php echo $edit_student['year'] == '2' ? 'selected' : ''; ?>>Year 2</option>
                                <option value="3" <?php echo $edit_student['year'] == '3' ? 'selected' : ''; ?>>Year 3</option>
                                <option value="4" <?php echo $edit_student['year'] == '4' ? 'selected' : ''; ?>>Year 4</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Address *</label>
                            <textarea name="address" required><?php echo htmlspecialchars($edit_student['address']); ?></textarea>
                        </div>
                        
                        <div class="modal-actions">
                            <a href="seestudents.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" name="update_student" class="btn btn-primary">Update Student</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-overlay" style="display: block;"></div>
            <?php endif; ?>

            <!-- Search Box -->
            <div class="search-container">
                <form method="GET" action="" class="search-box">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" name="search" class="search-input" 
                           placeholder="Search students by name, email, or course..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </form>
            </div>

            <!-- Students Table -->
            <div class="table-container">
                <?php if ($total_students > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Address</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($students_result)): 
                            $createdDate = date('n/j/Y', strtotime($row['created_at']));
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['course']); ?></td>
                            <td><?php echo htmlspecialchars($row['year']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><?php echo $createdDate; ?></td>
                            <td class="actions">
                                <a href="?edit=<?php echo $row['id']; ?>" class="action-btn edit" title="Edit">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>
                                <a href="?delete=<?php echo $row['id']; ?>" class="action-btn delete" title="Delete" 
                                   onclick="return confirm('Are you sure you want to delete this student?');">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="student-count">
                    Showing <?php echo $total_students; ?> of <?php echo $total_students; ?> students
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📚</div>
                    <p class="empty-state-text">No students registered yet</p>
                </div>
                <div class="student-count">
                    Showing 0 of 0 students
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>