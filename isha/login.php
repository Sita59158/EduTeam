<?php
// login.php - Login Page
// Developer: Isha | Module: User Authentication
// Project: Edu Team - Student Record System

session_start(); // Start session for user login

// If already logged in redirect to course list
if(isset($_SESSION['teacher_id'])) {
    header('Location: course_list.php');
    exit();
}

include '../db.php'; // Include database connection

$error = ''; // Store error message

// Check if form was submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get form values
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate fields are not empty
    if($email == '' || $password == '') {
        $error = 'Email and password are required!';
    } else {
        // Check email and password against Teacher table
        // MD5() encrypts password to match stored hash
        // is_active = 1 ensures only active teachers login
        $query = "SELECT * FROM Teacher 
                  WHERE email = '$email' 
                  AND password = MD5('$password')
                  AND is_active = 1";

        $result = mysqli_query($conn, $query);

        // If teacher found login successful
        if(mysqli_num_rows($result) > 0) {
            $teacher = mysqli_fetch_assoc($result);

            // Store teacher details in session
            $_SESSION['teacher_id'] = $teacher['teacher_id'];
            $_SESSION['teacher_name'] = $teacher['first_name'] . ' ' . $teacher['last_name'];
            $_SESSION['teacher_email'] = $teacher['email'];

            // Redirect to course list after login
            header('Location: course_list.php');
            exit();
        } else {
            $error = 'Invalid email or password!';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edu Team - Login</title>
    <style>
        /* Reset browser default styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* Full height centered layout */
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* White login card */
        .card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            width: 420px;
            border: 1px solid #eee;
        }

        /* Purple title - Edu Team brand colour */
        h1 { text-align: center; color: #4a235a; font-size: 22px; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #999; font-size: 13px; margin-bottom: 25px; }
        label { display: block; font-weight: 500; margin-bottom: 5px; color: #333; font-size: 14px; }

        /* Full width inputs */
        input {
            width: 100%; padding: 11px 14px;
            border: 1px solid #ddd; border-radius: 8px;
            font-size: 14px; margin-bottom: 15px; outline: none;
        }
        input:focus { border-color: #4a235a; }
        input::placeholder { color: #bbb; }

        /* Purple login button */
        .login-btn {
            width: 100%; padding: 13px;
            background: #4a235a; color: white;
            border: none; border-radius: 8px;
            font-size: 15px; cursor: pointer;
        }
        .login-btn:hover { background: #6b3580; }

        /* Red error message box */
        .error-box {
            background: #fce4ec; color: #c62828;
            padding: 10px 16px; border-radius: 8px;
            margin-bottom: 16px; font-size: 13px;
            text-align: center;
        }
        .developer { text-align: center; margin-top: 20px; font-size: 12px; color: #bbb; }

        /* Test credentials hint box */
        .hint {
            text-align: center; margin-top: 15px;
            font-size: 12px; color: #999;
            background: #f9f9f9; padding: 10px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Edu Team - Student Record System</h1>
        <p class="subtitle">Sign in to your account</p>

        <?php
        // Show error message if login failed
        if($error != '') {
            echo '<div class="error-box">' . $error . '</div>';
        }
        ?>

        <!-- POST form sends data securely -->
        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email"
                placeholder="Enter your email"
                value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>"
                required>

            <label>Password:</label>
            <input type="password" name="password"
                placeholder="Enter your password"
                required>

            <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="hint">Test login: sita@email.com / 123456</div>
        <p class="developer">Developer: Isha | SRS-86</p>
    </div>
</body>
</html>