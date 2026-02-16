<?php
session_start();
$conn = new mysqli("localhost", "root", "", "student_registration");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $account_type = $_POST['account_type'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email already exists first
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $error = "Email already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, account_type) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $account_type);

            if ($stmt->execute()) {
                $new_user_id = $stmt->insert_id;
                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['user'] = $full_name;
                $_SESSION['full_name'] = $full_name;
                $_SESSION['account_type'] = $account_type;

                if ($account_type == "Administrator") {
                    $_SESSION['role'] = 'admin';
                } else {
                    $_SESSION['role'] = 'standard';
                }
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
        $check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #e8ecf8 0%, #f0f4ff 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .logo {
            color: #5b4cdb;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 30px;
            letter-spacing: -0.5px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h2 {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8px;
        }

        .header p {
            color: #6b7280;
            font-size: 15px;
        }

        .container {
            background: #ffffff;
            padding: 35px 40px;
            width: 100%;
            max-width: 420px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .error-message {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .error-message::before {
            content: "⚠";
            font-size: 16px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-top: 20px;
            margin-bottom: 6px;
            color: #374151;
            font-size: 14px;
        }

        input, select {
            width: 100%;
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 14px;
            color: #1f2937;
            background-color: #ffffff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #5b4cdb;
            box-shadow: 0 0 0 3px rgba(91, 76, 219, 0.1);
        }

        input::placeholder {
            color: #9ca3af;
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 20px;
            padding-right: 40px;
        }

        .note {
            font-size: 13px;
            color: #6b7280;
            margin-top: 8px;
            line-height: 1.4;
        }

        button {
            width: 100%;
            padding: 14px;
            margin-top: 25px;
            background: #5b4cdb;
            border: none;
            color: white;
            font-size: 15px;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button:hover {
            background: #4c3fd0;
        }

        .signin-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #6b7280;
        }

        .signin-link a {
            color: #5b4cdb;
            text-decoration: none;
            font-weight: 500;
        }

        .signin-link a:hover {
            text-decoration: underline;
        }

        .back-link {
            margin-top: 25px;
            font-size: 14px;
            color: #5b4cdb;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .back-link::before {
            content: "←";
        }
    </style>
</head>
<body>

    <div class="logo">MyStudentManager</div>
    
    <div class="header">
        <h2>Create your account</h2>
        <p>Sign up to get started</p>
    </div>

    <div class="container">
        <form method="POST">
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <label>Full Name</label>
            <input type="text" name="full_name" placeholder="name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required>

            <label>Email address</label>
            <input type="email" name="email" placeholder="name@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>

            <label>Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="••••••••" required>

            <label>Account Type</label>
            <select name="account_type">
                <option value="Standard User" <?php echo (isset($_POST['account_type']) && $_POST['account_type'] == 'Standard User') ? 'selected' : ''; ?>>Standard User</option>
                <option value="Administrator" <?php echo (!isset($_POST['account_type']) || $_POST['account_type'] == 'Administrator') ? 'selected' : ''; ?>>Administrator</option>
            </select>

            <div class="note">
                Standard users can only add and remove students. Administrators have full access.
            </div>

            <button type="submit">Create Account</button>

        </form>

        <div class="signin-link">
            Already have an account? <a href="login.php">Sign in</a>
        </div>
    </div>

    <a href="home.php" class="back-link">Back to home</a>

</body>
</html>