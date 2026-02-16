<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user info
$user_id = $_SESSION['user_id'];
$user_query = "SELECT full_name, account_type FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Handle form submission
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $course = mysqli_real_escape_string($conn, $_POST['course'] ?? '');
    $year = mysqli_real_escape_string($conn, $_POST['year'] ?? '');
    $address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
    
    if (empty($fullname) || empty($email) || empty($phone) || empty($course) || empty($year) || empty($address)) {
        $error = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        // Check if email already exists
        $check_query = "SELECT id FROM students WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'A student with this email already exists';
        } else {
            $insert_query = "INSERT INTO students (fullname, email, phone, course, year, address, created_at) 
                            VALUES ('$fullname', '$email', '$phone', '$course', '$year', '$address', NOW())";
            
            if (mysqli_query($conn, $insert_query)) {
                header("Location: seestudents.php?success=1");
                exit();
            } else {
                $error = 'Error adding student: ' . mysqli_error($conn);
            }
        }
    }
}

// Check for success message from redirect
if (isset($_GET['success'])) {
    $message = 'Student added successfully';
}

// Get all students for the table
$students_query = "SELECT * FROM students ORDER BY created_at DESC";
$students_result = mysqli_query($conn, $students_query);
$total_students = mysqli_num_rows($students_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student</title>
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

        /* Messages */
        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .success-message {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .error-message {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            font-weight: 500;
        }

        /* Form Container */
        .form-container {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e5e7eb;
            margin-bottom: 30px;
        }

        .form-title {
            font-size: 20px;
            color: #111827;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            color: #374151;
            font-weight: 500;
            font-size: 14px;
        }

        .form-label .required {
            color: #dc2626;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            color: #111827;
            background: white;
            transition: all 0.2s;
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-input.error, .form-select.error, .form-textarea.error {
            border-color: #dc2626;
        }

        .form-input.error:focus, .form-select.error:focus, .form-textarea.error:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 10px;
        }

        .submit-btn:hover {
            background: #4338ca;
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
                
                <?php if ($user['account_type'] == 'Administrator'): ?>
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
            <div class="page-header">
                <h1 class="page-title">Student Management</h1>
                <a href="seestudents.php" class="btn btn-secondary">Cancel</a>
            </div>
            
            <?php if ($message): ?>
                <div class="message success-message">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="message error-message">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <!-- Add Student Form -->
            <div class="form-container">
                <h2 class="form-title">Add New Student</h2>
                
                <form method="POST" action="" id="addStudentForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="fullname">Full Name <span class="required">*</span></label>
                            <input type="text" id="fullname" name="fullname" class="form-input <?php echo $error && strpos($error, 'required') !== false ? 'error' : ''; ?>" 
                                   placeholder="John Doe" required 
                                   value="<?php echo isset($_POST['fullname']) && $error ? htmlspecialchars($_POST['fullname']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="email">Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" class="form-input <?php echo $error && strpos($error, 'email') !== false ? 'error' : ''; ?>" 
                                   placeholder="student@example.com" required 
                                   value="<?php echo isset($_POST['email']) && $error ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="phone">Phone <span class="required">*</span></label>
                            <input type="text" id="phone" name="phone" class="form-input <?php echo $error && strpos($error, 'required') !== false ? 'error' : ''; ?>" 
                                   placeholder="+07XXXXXXXX" pattern="[0-9]{10}" required 
                                   value="<?php echo isset($_POST['phone']) && $error ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="course">Course <span class="required">*</span></label>
                            <input type="text" id="course" name="course" class="form-input <?php echo $error && strpos($error, 'required') !== false ? 'error' : ''; ?>" 
                                   placeholder="SOD A" required 
                                   value="<?php echo isset($_POST['course']) && $error ? htmlspecialchars($_POST['course']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="year">Year <span class="required">*</span></label>
                        <select id="year" name="year" class="form-select <?php echo $error && strpos($error, 'required') !== false ? 'error' : ''; ?>" required>
                            <option value="">Select Year</option>
                            <option value="1" <?php echo isset($_POST['year']) && $_POST['year'] == 'L3' ? 'selected' : ''; ?>>Level 3</option>
                            <option value="2" <?php echo isset($_POST['year']) && $_POST['year'] == 'L4' ? 'selected' : ''; ?>>Level 4</option>
                            <option value="3" <?php echo isset($_POST['year']) && $_POST['year'] == 'L5' ? 'selected' : ''; ?>>Level 5</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="address">Address <span class="required">*</span></label>
                        <textarea id="address" name="address" class="form-textarea <?php echo $error && strpos($error, 'required') !== false ? 'error' : ''; ?>" 
                                  placeholder="123 Main St, City, Country" required><?php echo isset($_POST['address']) && $error ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">Add Student</button>
                </form>
            </div>
            
            <!-- Search Box -->
            <div class="search-container">
                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" class="search-input" 
                           placeholder="Search students by name, email, or course..." 
                           readonly>
                </div>
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
                            <td></td>
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

    <script>
        // Remove error class on input
        document.querySelectorAll('.form-input, .form-select, .form-textarea').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('error');
            });
        });
    </script>
</body>
</html>

<?php
mysqli_close($conn);
?>