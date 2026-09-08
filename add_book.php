<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'] ?? "User";
$full_name = $_SESSION['full_name'] ?? $username;
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Book - Library Management System</title>

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
            color: #1e293b;
            min-height: 100vh;
        }

        /* NAVBAR */

        .navbar {
            background: #ffffff;
            border-bottom: 1px solid #dbe3ef;
            padding: 15px 6%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 3px 15px rgba(15, 23, 42, 0.06);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #172033;
            font-size: 20px;
            font-weight: 700;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links a {
            text-decoration: none;
            padding: 9px 14px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #475569;
        }

        .nav-links a:hover {
            background: #eff6ff;
            color: #2563eb;
        }

        .nav-links .active {
            background: #eff6ff;
            color: #2563eb;
        }

        .logout {
            color: #dc2626 !important;
        }

        .logout:hover {
            background: #fff1f2 !important;
            color: #dc2626 !important;
        }

        /* MAIN */

        .main {
            width: 100%;
            max-width: 850px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .welcome {
            margin-bottom: 25px;
        }

        .welcome h1 {
            font-size: 29px;
            color: #172033;
            margin-bottom: 7px;
        }

        .welcome p {
            color: #64748b;
            font-size: 14px;
        }

        /* FORM CARD */

        .card {
            background: #ffffff;
            border: 1px solid #dbe3ef;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 15px 40px rgba(37, 99, 235, 0.09);
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .card-title-icon {
            width: 42px;
            height: 42px;
            border-radius: 9px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .card-title h2 {
            font-size: 20px;
            color: #172033;
        }

        .card-title p {
            color: #94a3b8;
            font-size: 12px;
            margin-top: 3px;
        }

        /* FORM */

        .form-group {
            margin-bottom: 19px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }

        .required {
            color: #ef4444;
        }

        input,
        select {
            width: 100%;
            height: 47px;
            padding: 0 14px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background: #ffffff;
            color: #1e293b;
            font-size: 14px;
            outline: none;
            transition: 0.2s;
        }

        input::placeholder {
            color: #94a3b8;
        }

        input:focus,
        select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
        }

        select {
            cursor: pointer;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .save-btn,
        .reset-btn {
            height: 48px;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            padding: 0 22px;
        }

        .save-btn {
            flex: 1;
            border: none;
            background: #2563eb;
            color: #ffffff;
        }

        .save-btn:hover {
            background: #1d4ed8;
        }

        .reset-btn {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
        }

        .reset-btn:hover {
            background: #f8fafc;
        }

        .note {
            margin-top: 20px;
            padding: 12px 14px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            color: #1e40af;
            font-size: 12px;
            line-height: 1.5;
        }

        .footer {
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            margin: 25px 0;
        }

        @media (max-width: 650px) {

            .navbar {
                padding: 12px 18px;
                flex-direction: column;
                gap: 12px;
            }

            .nav-links {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }

            .main {
                margin-top: 25px;
            }

            .card {
                padding: 23px 18px;
            }

            .welcome h1 {
                font-size: 24px;
            }

            .form-actions {
                flex-direction: column;
            }

            .save-btn,
            .reset-btn {
                width: 100%;
            }
        }

    </style>

</head>

<body>

<nav class="navbar">

    <div class="brand">

        <div class="brand-icon">
            📚
        </div>

        Library Management

    </div>

    <div class="nav-links">

        <a href="add_book.php" class="active">
            Add Book
        </a>

        <a href="view.php">
            View Books
        </a>

        <a href="logout.php" class="logout">
            Logout
        </a>

    </div>

</nav>


<main class="main">

    <div class="welcome">

        <h1>
            Welcome, <?= htmlspecialchars($full_name) ?>!
        </h1>

        <p>
            Add a new book to your library collection.
        </p>

    </div>


    <div class="card">

        <div class="card-title">

            <div class="card-title-icon">
                📖
            </div>

            <div>

                <h2>
                    Add New Book
                </h2>

                <p>
                    Enter the book details below
                </p>

            </div>

        </div>


        <form
            method="POST"
            action="save_book.php"
            onsubmit="return validateForm()"
        >

            <div class="form-group">

                <label for="book_name">
                    Book Name <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="book_name"
                    name="book_name"
                    placeholder="Enter book name"
                    maxlength="150"
                    required
                >

            </div>


            <div class="form-group">

                <label for="author">
                    Author <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="author"
                    name="author"
                    placeholder="Enter author name"
                    maxlength="100"
                    required
                >

            </div>


            <div class="form-group">

                <label for="book_code">
                    Book Code <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="book_code"
                    name="book_code"
                    placeholder="Example: LIB-101"
                    maxlength="50"
                    required
                >

            </div>


            <div class="form-group">

                <label for="category">
                    Category <span class="required">*</span>
                </label>

                <select
                    id="category"
                    name="category"
                    required
                >

                    <option value="">
                        Select Category
                    </option>

                    <option value="Programming">
                        Programming
                    </option>

                    <option value="Database">
                        Database
                    </option>

                    <option value="Networking">
                        Networking
                    </option>

                    <option value="Operating Systems">
                        Operating Systems
                    </option>

                    <option value="Mathematics">
                        Mathematics
                    </option>

                    <option value="Other">
                        Other
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label for="status">
                    Status <span class="required">*</span>
                </label>

                <select
                    id="status"
                    name="status"
                    required
                >

                    <option value="">
                        Select Status
                    </option>

                    <option value="Available">
                        Available
                    </option>

                    <option value="Issued">
                        Issued
                    </option>

                </select>

            </div>


            <div class="form-actions">

                <button
                    type="submit"
                    class="save-btn"
                >
                    Save Book
                </button>

                <button
                    type="reset"
                    class="reset-btn"
                >
                    Clear Form
                </button>

            </div>


            <div class="note">

                <strong>Note:</strong>
                All fields are required. Book code must be unique.

            </div>

        </form>

    </div>

    <div class="footer">
        Library Management System
    </div>

</main>


<script>

function validateForm() {

    const bookName =
        document.getElementById("book_name").value.trim();

    const author =
        document.getElementById("author").value.trim();

    const bookCode =
        document.getElementById("book_code").value.trim();

    const category =
        document.getElementById("category").value;

    const status =
        document.getElementById("status").value;


    if (
        bookName === "" ||
        author === "" ||
        bookCode === "" ||
        category === "" ||
        status === ""
    ) {

        alert("Please fill in all fields.");

        return false;
    }


    return true;
}

</script>

</body>

</html>
```
