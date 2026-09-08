<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once "connect.php";

/* Only allow POST request */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: add_book.php");
    exit;
}

$book_name = trim($_POST["book_name"] ?? "");
$author = trim($_POST["author"] ?? "");
$book_code = trim($_POST["book_code"] ?? "");
$category = trim($_POST["category"] ?? "");
$status = trim($_POST["status"] ?? "");

$allowed_categories = [
    "Programming",
    "Database",
    "Networking",
    "Operating Systems",
    "Mathematics",
    "Other"
];

$allowed_status = [
    "Available",
    "Issued"
];

$error = "";

/* Validation */

if (
    $book_name === "" ||
    $author === "" ||
    $book_code === "" ||
    $category === "" ||
    $status === ""
) {
    $error = "Please fill in all fields.";

} elseif (strlen($book_name) > 150) {
    $error = "Book name is too long.";

} elseif (strlen($author) > 100) {
    $error = "Author name is too long.";

} elseif (strlen($book_code) > 50) {
    $error = "Book code is too long.";

} elseif (!in_array($category, $allowed_categories, true)) {
    $error = "Invalid category selected.";

} elseif (!in_array($status, $allowed_status, true)) {
    $error = "Invalid status selected.";
}


/* Check duplicate book code */

if ($error === "") {

    $sql = "SELECT id
            FROM books
            WHERE book_code = ?
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $book_code
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error = "Book code already exists. Please use a different code.";
        }

        mysqli_stmt_close($stmt);

    } else {
        $error = "Something went wrong. Please try again.";
    }
}


/* Insert book */

if ($error === "") {

    $sql = "INSERT INTO books
            (book_name, author, book_code, category, status)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {

        mysqli_stmt_bind_param(
            $stmt,
            "sssss",
            $book_name,
            $author,
            $book_code,
            $category,
            $status
        );

        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_close($stmt);
            ?>

            <!DOCTYPE html>
            <html lang="en">

            <head>

                <meta charset="UTF-8">

                <meta
                    name="viewport"
                    content="width=device-width, initial-scale=1.0"
                >

                <title>Book Added Successfully</title>

                <style>

                    * {
                        box-sizing: border-box;
                        margin: 0;
                        padding: 0;
                    }

                    body {
                        min-height: 100vh;

                        font-family:
                            Arial,
                            Helvetica,
                            sans-serif;

                        background:
                            radial-gradient(
                                circle at top left,
                                #eaf3ff 0%,
                                #f7faff 45%,
                                #eef5ff 100%
                            );

                        color: #1e293b;

                        display: flex;

                        align-items: center;

                        justify-content: center;

                        padding: 25px;
                    }

                    .container {
                        width: 100%;
                        max-width: 620px;
                    }

                    .card {
                        background: #ffffff;

                        border: 1px solid #dbe3ef;

                        border-radius: 18px;

                        padding: 38px;

                        box-shadow:
                            0 20px 50px
                            rgba(37, 99, 235, 0.12);

                        text-align: center;
                    }

                    .success-icon {
                        width: 75px;
                        height: 75px;

                        margin: 0 auto 20px;

                        border-radius: 50%;

                        background: #dcfce7;

                        color: #16a34a;

                        display: flex;

                        align-items: center;

                        justify-content: center;

                        font-size: 38px;

                        border: 1px solid #bbf7d0;
                    }

                    h1 {
                        color: #172033;

                        font-size: 28px;

                        margin-bottom: 8px;
                    }

                    .subtitle {
                        color: #64748b;

                        font-size: 14px;

                        margin-bottom: 25px;
                    }

                    .details {
                        text-align: left;

                        background: #f8fafc;

                        border: 1px solid #e2e8f0;

                        border-radius: 12px;

                        padding: 20px;

                        margin-bottom: 25px;
                    }

                    .detail-row {
                        display: flex;

                        justify-content: space-between;

                        gap: 20px;

                        padding: 11px 0;

                        border-bottom: 1px solid #e2e8f0;

                        font-size: 14px;
                    }

                    .detail-row:last-child {
                        border-bottom: none;
                    }

                    .detail-label {
                        color: #64748b;

                        font-weight: 600;
                    }

                    .detail-value {
                        color: #172033;

                        font-weight: 600;

                        text-align: right;

                        word-break: break-word;
                    }

                    .status {
                        display: inline-block;

                        padding: 5px 10px;

                        border-radius: 20px;

                        font-size: 12px;

                        background: #dcfce7;

                        color: #15803d;
                    }

                    .buttons {
                        display: flex;

                        gap: 10px;

                        flex-wrap: wrap;
                    }

                    .btn {
                        flex: 1;

                        min-width: 150px;

                        height: 45px;

                        border-radius: 9px;

                        display: flex;

                        align-items: center;

                        justify-content: center;

                        text-decoration: none;

                        font-size: 14px;

                        font-weight: 600;
                    }

                    .primary {
                        background: #2563eb;

                        color: #ffffff;
                    }

                    .primary:hover {
                        background: #1d4ed8;
                    }

                    .secondary {
                        background: #ffffff;

                        border: 1px solid #cbd5e1;

                        color: #475569;
                    }

                    .secondary:hover {
                        background: #f8fafc;
                    }

                    .logout {
                        color: #dc2626;
                    }

                    .logout:hover {
                        background: #fff1f2;
                    }

                    @media (max-width: 600px) {

                        .card {
                            padding: 28px 20px;
                        }

                        h1 {
                            font-size: 24px;
                        }

                        .detail-row {
                            flex-direction: column;

                            gap: 4px;
                        }

                        .detail-value {
                            text-align: left;
                        }

                        .buttons {
                            flex-direction: column;
                        }

                        .btn {
                            width: 100%;
                        }
                    }

                </style>

            </head>

            <body>

            <div class="container">

                <div class="card">

                    <div class="success-icon">
                        ✓
                    </div>

                    <h1>
                        Book Added Successfully!
                    </h1>

                    <p class="subtitle">
                        The book has been added to your library.
                    </p>


                    <div class="details">

                        <div class="detail-row">

                            <span class="detail-label">
                                Book Name
                            </span>

                            <span class="detail-value">
                                <?= htmlspecialchars($book_name) ?>
                            </span>

                        </div>


                        <div class="detail-row">

                            <span class="detail-label">
                                Author
                            </span>

                            <span class="detail-value">
                                <?= htmlspecialchars($author) ?>
                            </span>

                        </div>


                        <div class="detail-row">

                            <span class="detail-label">
                                Book Code
                            </span>

                            <span class="detail-value">
                                <?= htmlspecialchars($book_code) ?>
                            </span>

                        </div>


                        <div class="detail-row">

                            <span class="detail-label">
                                Category
                            </span>

                            <span class="detail-value">
                                <?= htmlspecialchars($category) ?>
                            </span>

                        </div>


                        <div class="detail-row">

                            <span class="detail-label">
                                Status
                            </span>

                            <span class="detail-value">

                                <span class="status">
                                    <?= htmlspecialchars($status) ?>
                                </span>

                            </span>

                        </div>

                    </div>


                    <div class="buttons">

                        <a
                            href="view.php"
                            class="btn primary"
                        >
                            View All Books
                        </a>

                        <a
                            href="add_book.php"
                            class="btn secondary"
                        >
                            Add Another Book
                        </a>

                        <a
                            href="logout.php"
                            class="btn secondary logout"
                        >
                            Logout
                        </a>

                    </div>

                </div>

            </div>

            </body>

            </html>

            <?php

            exit;

        } else {

            mysqli_stmt_close($stmt);

            $error = "Something went wrong. Please try again.";
        }

    } else {

        $error = "Something went wrong. Please try again.";
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

    <title>Unable to Add Book</title>

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
                radial-gradient(
                    circle at top left,
                    #eaf3ff,
                    #f7faff 50%,
                    #eef5ff
                );

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 20px;
        }

        .card {
            width: 100%;

            max-width: 500px;

            background: #ffffff;

            border: 1px solid #dbe3ef;

            border-radius: 16px;

            padding: 35px;

            text-align: center;

            box-shadow:
                0 20px 50px
                rgba(37, 99, 235, 0.10);
        }

        .icon {
            width: 65px;

            height: 65px;

            margin: 0 auto 18px;

            border-radius: 50%;

            background: #fff1f2;

            border: 1px solid #fecdd3;

            color: #dc2626;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 30px;
        }

        h1 {
            color: #172033;

            font-size: 25px;

            margin-bottom: 10px;
        }

        .error {
            color: #be123c;

            background: #fff1f2;

            border: 1px solid #fecdd3;

            padding: 13px;

            border-radius: 9px;

            font-size: 14px;

            margin: 20px 0;
        }

        a {
            display: inline-block;

            padding: 12px 20px;

            border-radius: 8px;

            background: #2563eb;

            color: #ffffff;

            text-decoration: none;

            font-weight: 600;

            font-size: 14px;
        }

        a:hover {
            background: #1d4ed8;
        }

    </style>

</head>

<body>

<div class="card">

    <div class="icon">
        !
    </div>

    <h1>
        Unable to Add Book
    </h1>

    <div class="error">
        <?= htmlspecialchars($error) ?>
    </div>

    <a href="add_book.php">
        Back to Add Book
    </a>

</div>

</body>

</html>