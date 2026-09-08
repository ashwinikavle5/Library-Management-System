<?php

session_start();

require_once "connect.php";

$error = "";
$success = "";

$full_name = "";
$username = "";
$email = "";


/* Registration */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";


    /* Validate Full Name */

    if ($full_name === "") {

        $error = "Please enter your full name.";

    } elseif (strlen($full_name) < 2 || strlen($full_name) > 100) {

        $error = "Full name must be between 2 and 100 characters.";
    }


    /* Validate Username */

    elseif ($username === "") {

        $error = "Please enter a username.";

    } elseif (strlen($username) < 3 || strlen($username) > 50) {

        $error = "Username must be 3-50 characters.";

    } elseif (!preg_match('/^[A-Za-z0-9_@.\-]+$/', $username)) {

        $error = "Username can contain letters, numbers, underscore, @, dot and hyphen.";
    }


    /* Validate Email */

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";
    }


    /* Validate Password */

    elseif (strlen($password) < 6) {

        $error = "Password must contain at least 6 characters.";
    }


    /* Confirm Password */

    elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";
    }


    /* Check Duplicate Username and Email */

    if ($error === "") {

        $sql = "SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "ss",
                $username,
                $email
            );

            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {

                $error = "Username or email already exists.";

            }

            mysqli_stmt_close($stmt);

        } else {

            $error = "Something went wrong. Please try again.";
        }
    }


    /* Create Account */

    if ($error === "") {

        $hashed_password = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $sql = "INSERT INTO users
                (full_name, username, email, password)
                VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $full_name,
                $username,
                $email,
                $hashed_password
            );

            if (mysqli_stmt_execute($stmt)) {

                $success = "Account Created Successfully!";

                $full_name = "";
                $username = "";
                $email = "";

            } else {

                $error = "Account could not be created. Please try again.";
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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Create Account - Library Management System</title>


    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        body {

            font-family: Arial, Helvetica, sans-serif;

            background:
                radial-gradient(
                    circle at top left,
                    #eaf3ff 0%,
                    #f7faff 45%,
                    #eef5ff 100%
                );

            min-height: 100vh;

            display: flex;

            justify-content: center;

            align-items: center;

            padding: 30px 15px;

            color: #172033;
        }


        .container {

            width: 100%;

            max-width: 540px;
        }


        .card {

            background: #ffffff;

            border: 1px solid #dbe3ef;

            border-radius: 18px;

            padding: 35px;

            box-shadow:
                0 15px 40px
                rgba(37, 99, 235, 0.10);
        }


        .header {

            text-align: center;

            margin-bottom: 25px;
        }


        .logo {

            width: 58px;

            height: 58px;

            margin: 0 auto 15px;

            border-radius: 14px;

            background: #2563eb;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 28px;
        }


        .header h1 {

            font-size: 27px;

            margin-bottom: 7px;

            color: #172033;
        }


        .header p {

            color: #64748b;

            font-size: 14px;
        }


        .message {

            padding: 13px 15px;

            border-radius: 9px;

            margin-bottom: 20px;

            font-size: 14px;

            line-height: 1.5;
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

            text-align: center;
        }


        .form-group {

            margin-bottom: 18px;
        }


        label {

            display: block;

            font-size: 14px;

            font-weight: 700;

            margin-bottom: 8px;

            color: #172033;
        }


        input {

            width: 100%;

            height: 48px;

            border: 1px solid #cbd5e1;

            border-radius: 9px;

            padding: 0 14px;

            font-size: 15px;

            color: #172033;

            background: #ffffff;

            outline: none;
        }


        input:focus {

            border-color: #2563eb;

            box-shadow:
                0 0 0 3px
                rgba(37, 99, 235, 0.10);
        }


        .password-box {

            position: relative;
        }


        .password-box input {

            padding-right: 48px;
        }


        .eye {

            position: absolute;

            right: 14px;

            top: 50%;

            transform: translateY(-50%);

            cursor: pointer;

            color: #64748b;

            font-size: 18px;
        }


        .button {

            width: 100%;

            height: 48px;

            border: none;

            border-radius: 9px;

            background: #2563eb;

            color: white;

            font-size: 15px;

            font-weight: 700;

            cursor: pointer;

            margin-top: 5px;
        }


        .button:hover {

            background: #1d4ed8;
        }


        .login-link {

            text-align: center;

            margin-top: 20px;

            font-size: 14px;

            color: #64748b;
        }


        .login-link a {

            color: #2563eb;

            text-decoration: none;

            font-weight: 700;
        }


        .login-link a:hover {

            text-decoration: underline;
        }


        .back-link {

            display: block;

            text-align: center;

            margin-top: 12px;

            color: #64748b;

            text-decoration: none;

            font-size: 13px;
        }


        .back-link:hover {

            color: #2563eb;
        }


        @media (max-width: 600px) {

            .card {

                padding: 25px 20px;
            }

            .header h1 {

                font-size: 23px;
            }
        }

    </style>

</head>


<body>


<div class="container">

    <div class="card">


        <div class="header">

            <div class="logo">
                📚
            </div>

            <h1>
                Create Library Account
            </h1>

            <p>
                Fill in the details to create your account
            </p>

        </div>


        <?php if ($error !== ""): ?>

            <div class="message error">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <?php if ($success !== ""): ?>

            <div class="message success">

                <strong>
                    <?= htmlspecialchars($success) ?>
                </strong>

                <br><br>

                Your account has been created successfully.

            </div>

            <a
                href="index.php"
                class="button"
                style="
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    text-decoration:none;
                "
            >
                Go to Login
            </a>


        <?php else: ?>


            <form
                method="POST"
                action="register.php"
                onsubmit="return validateForm();"
            >


                <!-- Full Name -->

                <div class="form-group">

                    <label for="full_name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        placeholder="Enter your full name"
                        value="<?= htmlspecialchars($full_name) ?>"
                        required
                    >

                </div>


                <!-- Username -->

                <div class="form-group">

                    <label for="username">
                        Username
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Enter your username"
                        value="<?= htmlspecialchars($username) ?>"
                        maxlength="50"
                        required
                    >

                </div>


                <!-- Email -->

                <div class="form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email address"
                        value="<?= htmlspecialchars($email) ?>"
                        required
                    >

                </div>


                <!-- Password -->

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
                            minlength="6"
                            required
                        >

                        <span
                            class="eye"
                            onclick="togglePassword('password', this)"
                        >
                            ◉
                        </span>

                    </div>

                </div>


                <!-- Confirm Password -->

                <div class="form-group">

                    <label for="confirm_password">
                        Confirm Password
                    </label>

                    <div class="password-box">

                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            placeholder="Confirm your password"
                            minlength="6"
                            required
                        >

                        <span
                            class="eye"
                            onclick="togglePassword('confirm_password', this)"
                        >
                            ◉
                        </span>

                    </div>

                </div>


                <button
                    type="submit"
                    class="button"
                >
                    Create Account
                </button>


            </form>


            <div class="login-link">

                Already have an account?

                <a href="index.php">
                    Login
                </a>

            </div>


            <a
                href="index.php"
                class="back-link"
            >
                ← Back to Login
            </a>


        <?php endif; ?>


    </div>

</div>


<script>

function togglePassword(fieldId, icon) {

    const field = document.getElementById(fieldId);

    if (field.type === "password") {

        field.type = "text";

        icon.textContent = "◉";

    } else {

        field.type = "password";

        icon.textContent = "◉";
    }
}


function validateForm() {

    const password =
        document.getElementById("password").value;

    const confirmPassword =
        document.getElementById("confirm_password").value;

    if (password !== confirmPassword) {

        alert("Passwords do not match.");

        return false;
    }

    return true;
}

</script>


</body>

</html>