<?php
session_start();
require_once "config.php";

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user info
$user_id = $_SESSION['user_id'];
$user_query = "SELECT full_name, account_type FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// ADMIN ONLY - Redirect standard users
if ($user['account_type'] !== 'Administrator') {
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $account_type = mysqli_real_escape_string($conn, $_POST['account_type'] ?? 'Standard User');
    
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        // Check if email already exists
        $check_query = "SELECT id FROM users WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'A user with this email already exists';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO users (full_name, email, password, account_type, created_at) 
                            VALUES ('$full_name', '$email', '$hashed_password', '$account_type', NOW())";
            
            if (mysqli_query($conn, $insert_query)) {
                header("Location: seeusers.php?success=1");
                exit();
            } else {
                $error = 'Error creating user: ' . mysqli_error($conn);
            }
        }
    }
}

// Check for success message from redirect
if (isset($_GET['success'])) {
    $message = 'User created successfully';
}

// Fetch all users for the table
$users_query = "SELECT id, full_name, email, account_type, created_at FROM users ORDER BY created_at DESC";
$users_result = mysqli_query($conn, $users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New User</title>
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            color: #374151;
            font-weight: 500;
            font-size: 14px;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            color: #111827;
            background: white;
            transition: all 0.2s;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-input.error {
            border-color: #dc2626;
        }

        .form-input.error:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .info-text {
            margin-top: 6px;
            font-size: 13px;
            color: #9ca3af;
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
                
                <a href="seeusers.php" class="nav-item">
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
                <h1 class="page-title">User Management</h1>
                <a href="seeusers.php" class="btn btn-secondary">Cancel</a>
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
            
            <!-- Create User Form -->
            <div class="form-container">
                <h2 class="form-title">Create New User</h2>
                
                <form method="POST" action="" id="createUserForm">
                    <div class="form-group">
                        <label class="form-label" for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" class="form-input" 
                               placeholder="John Doe" required 
                               value="<?php echo isset($_POST['full_name']) && $error ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-input <?php echo $error && strpos($error, 'email') !== false ? 'error' : ''; ?>" 
                               placeholder="user@example.com" required 
                               value="<?php echo isset($_POST['email']) && $error ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-input <?php echo $error && strpos($error, 'Password') !== false ? 'error' : ''; ?>" 
                               placeholder="••••••••" required minlength="6">
                        <div class="info-text">Must be at least 6 characters long</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input <?php echo $error && strpos($error, 'match') !== false ? 'error' : ''; ?>" 
                               placeholder="••••••••" required minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="account_type">User Type</label>
                        <select id="account_type" name="account_type" class="form-select">
                            <option value="Standard User" <?php echo isset($_POST['account_type']) && $_POST['account_type'] == 'Standard User' ? 'selected' : ''; ?>>Standard User</option>
                            <option value="Administrator" <?php echo isset($_POST['account_type']) && $_POST['account_type'] == 'Administrator' ? 'selected' : ''; ?>>Administrator</option>
                        </select>
                        <div class="info-text">Standard users can only add students. Administrators have full access.</div>
                    </div>
                    
                    <button type="submit" class="submit-btn">Create User</button>
                </form>
            </div>
            
            <!-- Users Table -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($users_result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($users_result)): 
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
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 40px; color: #9ca3af; font-size: 14px;">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        // Client-side password validation
        document.getElementById('createUserForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                document.getElementById('password').classList.add('error');
                document.getElementById('confirm_password').classList.add('error');
            }
        });
        
        // Remove error class on input
        document.querySelectorAll('.form-input').forEach(input => {
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