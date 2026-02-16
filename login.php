<?php
session_start();
$conn = new mysqli("localhost", "root", "", "student_registration");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            // Set all required session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = $user['full_name'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['account_type'] = $user['account_type'];

            if ($user['account_type'] == "Administrator") {
                $_SESSION['role'] = 'admin';
            } else {
                $_SESSION['role'] = 'standard';
            }
            
            header("Location: dashboard.php");
            exit();

        } else {
            $error = "Invalid password!";
        }

    } else {
        $error = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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

        input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 14px;
            color: #1f2937;
            background-color: #ffffff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus {
            outline: none;
            border-color: #5b4cdb;
            box-shadow: 0 0 0 3px rgba(91, 76, 219, 0.1);
        }

        input::placeholder {
            color: #9ca3af;
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

        .signup-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #6b7280;
        }

        .signup-link a {
            color: #5b4cdb;
            text-decoration: none;
            font-weight: 500;
        }

        .signup-link a:hover {
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
        <h2>Sign in to your account</h2>
        <p>Administrator | Standard Login</p>
    </div>

    <div class="container">
        <form method="POST">
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <label>Email address</label>
            <input type="email" name="email" placeholder="name@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>

            <button type="submit">Sign in</button>

        </form>

        <div class="signup-link">
            Don't have an account? <a href="signup.php">Sign up</a>
        </div>
    </div>

    <a href="home.php" class="back-link">Back to home</a>

</body>
</html>