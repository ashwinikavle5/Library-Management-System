<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: add_book.php");
    exit;
}

require_once "connect.php";

$error = "";
$success = "";

if (isset($_GET['logout']) && $_GET['logout'] === "success") {
    $success = "Logged out successfully.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = trim($_POST["login"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($login === "" || $password === "") {
        $error = "Please enter your username/email and password.";
    } else {

        $sql = "SELECT id, full_name, username, password
                FROM users
                WHERE username = ? OR email = ?
                LIMIT 1";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param($stmt, "ss", $login, $login);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);

            if ($user && password_verify($password, $user["password"])) {

                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["full_name"] = $user["full_name"];
                $_SESSION["username"] = $user["username"];

                mysqli_stmt_close($stmt);

                header("Location: add_book.php");
                exit;

            } else {
                $error = "Invalid username or password.";
            }

            mysqli_stmt_close($stmt);

        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Login</title>
    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;

            background:
                radial-gradient(circle at top left,
                #eaf3ff 0%,
                #f7faff 45%,
                #eef5ff 100%);

            color: #1e293b;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 30px 20px;
        }

        .container {
            width: 100%;
            max-width: 480px;
        }

        .card {
            background: #ffffff;

            border: 1px solid #dbe3ef;

            border-radius: 18px;

            padding: 38px;

            box-shadow:
                0 20px 50px rgba(37, 99, 235, 0.12);

            position: relative;
        }

        .logo {
            width: 70px;
            height: 70px;

            margin: 0 auto 18px;

            border-radius: 50%;

            background: #2563eb;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 34px;

            box-shadow:
                0 10px 25px rgba(37, 99, 235, 0.25);
        }

        h1 {
            text-align: center;

            font-size: 28px;

            color: #172033;

            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;

            color: #64748b;

            font-size: 15px;

            margin-bottom: 28px;
        }

        .message {
            padding: 13px 15px;

            border-radius: 9px;

            margin-bottom: 20px;

            font-size: 14px;
        }

        .error {
            background: #fff1f2;

            border: 1px solid #fecdd3;

            color: #be123c;
        }

        .success {
            background: #f0fdf4;

            border: 1px solid #bbf7d0;

            color: #15803d;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;

            color: #1e293b;

            font-size: 14px;

            font-weight: 600;

            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;

            height: 48px;

            padding: 0 45px 0 15px;

            background: #ffffff;

            border: 1px solid #cbd5e1;

            border-radius: 9px;

            color: #1e293b;

            font-size: 15px;

            outline: none;

            transition: 0.2s;
        }

        input::placeholder {
            color: #94a3b8;
        }

        input:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px rgba(37, 99, 235, 0.10);
        }

        .password-box {
            position: relative;
        }

        .eye {
            position: absolute;

            right: 14px;

            top: 50%;

            transform: translateY(-50%);

            cursor: pointer;

            font-size: 18px;

            color: #64748b;

            user-select: none;
        }

        .eye:hover {
            color: #2563eb;
        }

        .options {
            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 22px;

            font-size: 13px;
        }

        .remember {
            display: flex;

            align-items: center;

            gap: 7px;

            color: #64748b;

            margin: 0;
        }

        .remember input {
            width: 16px;
            height: 16px;

            accent-color: #2563eb;
        }
        .forgot {
            color: #2563eb;

            cursor: pointer;

            font-weight: 500;
        }
        .forgot:hover {
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;

            height: 48px;

            border: none;

            border-radius: 9px;

            background: #2563eb;

            color: #ffffff;

            font-size: 15px;

            font-weight: 600;

            cursor: pointer;

            transition: 0.2s;
        }

        .login-btn:hover {
            background: #1d4ed8;

            transform: translateY(-1px);
        }

        .divider {
            display: flex;

            align-items: center;

            gap: 12px;

            margin: 25px 0;

            color: #94a3b8;

            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: "";

            height: 1px;

            background: #e2e8f0;

            flex: 1;
        }

        .register-btn {
            display: block;

            width: 100%;

            height: 48px;

            line-height: 46px;

            text-align: center;

            border: 1px solid #2563eb;

            border-radius: 9px;

            color: #2563eb;

            background: #ffffff;

            text-decoration: none;

            font-size: 15px;

            font-weight: 600;

            transition: 0.2s;
        }

        .register-btn:hover {
            background: #eff6ff;

            transform: translateY(-1px);
        }

        .footer {
            text-align: center;

            color: #94a3b8;

            font-size: 12px;

            margin-top: 22px;
        }

        @media (max-width: 500px) {

            body {
                padding: 20px 15px;
            }

            .card {
                padding: 28px 20px;
            }

            h1 {
                font-size: 24px;
            }

            .options {
                font-size: 12px;
            }
        }

    </style>

</head>

<body>

<div class="container">

    <div class="card">

        <div class="logo">
            📚
        </div>

        <h1>
            Library Management System
        </h1>

        <p class="subtitle">
            Login to manage your library books
        </p>


        <?php if ($error !== ""): ?>

            <div class="message error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <?php if ($success !== ""): ?>

            <div class="message success">
                <?= htmlspecialchars($success) ?>
            </div>

        <?php endif; ?>


        <form method="POST" action="">

            <div class="form-group">

                <label for="login">
                    Username or Email
                </label>

                <input
                    type="text"
                    id="login"
                    name="login"
                    placeholder="Enter username or email"
                    value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
                    autocomplete="username"
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <div class="password-box">

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                    >
                    <span
                        class="eye"
                        id="eyeButton"
                        onclick="togglePassword()"
                    >
                        👁
                    </span>

                </div>
            </div>
            <div class="options">

                <label class="remember">

                    <input
                        type="checkbox"
                        name="remember"
                    >

                    Remember Me

                </label>
                <span
                    class="forgot"
                    onclick="forgotPassword()"
                >
                    Forgot Password?
                </span>

            </div>
            <button
                type="submit"
                class="login-btn"
            >
                Login
            </button>
        </form>
        <div class="divider">
            OR
        </div>
        <a
            href="register.php"
            class="register-btn"
        >
            Create Account
        </a>
    </div>
    <div class="footer">
        Library Management System
    </div>
</div>
<script>
function togglePassword() {
    const password =
        document.getElementById("password");
    const eye =
        document.getElementById("eyeButton");
    if (password.type === "password") {
        password.type = "text";
        eye.textContent = "🙈";
    } else {
        password.type = "password";
        eye.textContent = "👁";
    }
}
function forgotPassword() {
    alert(
        "Please contact the system administrator to reset your password."
    );
}
</script>
</body>
</html>