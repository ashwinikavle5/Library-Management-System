<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}
require_once "connect.php";
$message = "";
$error = "";

/* Delete Book */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_book"])) {

    $book_id = filter_var(
        $_POST["book_id"] ?? "",
        FILTER_VALIDATE_INT
    );
    if ($book_id === false || $book_id <= 0) {

        $error = "Invalid book selected.";

    } else {

        $sql = "DELETE FROM books WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param($stmt, "i", $book_id);

            if (mysqli_stmt_execute($stmt)) {
                $message = "Book deleted successfully.";
            } else {
                $error = "Something went wrong. Please try again.";
            }

            mysqli_stmt_close($stmt);

        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}


/* Summary Counts */

$total_books = 0;
$available_books = 0;
$issued_books = 0;

$sql = "SELECT
            COUNT(*) AS total,
            COALESCE(SUM(status = 'Available'), 0) AS available,
            COALESCE(SUM(status = 'Issued'), 0) AS issued
        FROM books";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $summary = mysqli_fetch_assoc($result);

    if ($summary) {
        $total_books = (int)$summary["total"];
        $available_books = (int)$summary["available"];
        $issued_books = (int)$summary["issued"];
    }

    mysqli_stmt_close($stmt);
}


/* Search and Filters */

$search = trim($_GET["search"] ?? "");
$category = trim($_GET["category"] ?? "");
$status = trim($_GET["status"] ?? "");

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

if (!in_array($category, $allowed_categories, true)) {
    $category = "";
}

if (!in_array($status, $allowed_status, true)) {
    $status = "";
}


/* Build safe WHERE conditions */

$where = [];
$types = "";
$params = [];


if ($search !== "") {

    $where[] = "(book_name LIKE ? OR author LIKE ? OR book_code LIKE ?)";

    $search_value = "%" . $search . "%";

    $params[] = $search_value;
    $params[] = $search_value;
    $params[] = $search_value;

    $types .= "sss";
}


if ($category !== "") {

    $where[] = "category = ?";

    $params[] = $category;

    $types .= "s";
}


if ($status !== "") {

    $where[] = "status = ?";

    $params[] = $status;

    $types .= "s";
}


$sql = "SELECT
            id,
            book_name,
            author,
            book_code,
            category,
            status,
            created_at
        FROM books";


if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY id DESC";


$stmt = mysqli_prepare($conn, $sql);

$books = [];

if ($stmt) {

    if ($types !== "") {

        mysqli_stmt_bind_param(
            $stmt,
            $types,
            ...$params
        );
    }

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }

    mysqli_stmt_close($stmt);
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

    <title>View Books - Library Management System</title>

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

            padding: 14px 6%;

            display: flex;

            align-items: center;

            justify-content: space-between;

            box-shadow:
                0 3px 15px
                rgba(15, 23, 42, 0.06);
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

            gap: 8px;
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

            max-width: 1250px;

            margin: 35px auto;

            padding: 0 20px;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h1 {
            color: #172033;

            font-size: 29px;

            margin-bottom: 7px;
        }

        .page-header p {
            color: #64748b;

            font-size: 14px;
        }


        /* MESSAGES */

        .message {
            padding: 13px 15px;

            border-radius: 9px;

            margin-bottom: 20px;

            font-size: 14px;
        }

        .success {
            background: #f0fdf4;

            border: 1px solid #bbf7d0;

            color: #15803d;
        }

        .error {
            background: #fff1f2;

            border: 1px solid #fecdd3;

            color: #be123c;
        }


        /* SUMMARY CARDS */

        .summary-grid {
            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 18px;

            margin-bottom: 25px;
        }

        .summary-card {
            background: #ffffff;

            border: 1px solid #dbe3ef;

            border-radius: 14px;

            padding: 22px;

            display: flex;

            align-items: center;

            gap: 15px;

            box-shadow:
                0 8px 25px
                rgba(37, 99, 235, 0.06);
        }

        .summary-icon {
            width: 50px;

            height: 50px;

            border-radius: 11px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 23px;
        }

        .blue-icon {
            background: #eff6ff;
        }

        .green-icon {
            background: #f0fdf4;
        }

        .orange-icon {
            background: #fffbeb;
        }

        .summary-info span {
            display: block;

            color: #64748b;

            font-size: 13px;

            margin-bottom: 5px;
        }

        .summary-info strong {
            display: block;

            color: #172033;

            font-size: 25px;
        }


        /* SEARCH CARD */

        .search-card {
            background: #ffffff;

            border: 1px solid #dbe3ef;

            border-radius: 14px;

            padding: 20px;

            margin-bottom: 25px;

            box-shadow:
                0 8px 25px
                rgba(37, 99, 235, 0.05);
        }

        .search-title {
            color: #172033;

            font-size: 16px;

            font-weight: 700;

            margin-bottom: 15px;
        }

        .search-form {
            display: grid;

            grid-template-columns:
                2fr 1fr 1fr auto;

            gap: 10px;
        }

        .search-form input,
        .search-form select {
            width: 100%;

            height: 44px;

            border: 1px solid #cbd5e1;

            border-radius: 8px;

            padding: 0 12px;

            color: #1e293b;

            background: #ffffff;

            outline: none;

            font-size: 14px;
        }

        .search-form input:focus,
        .search-form select:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px
                rgba(37, 99, 235, 0.10);
        }

        .search-btn {
            height: 44px;

            padding: 0 20px;

            border: none;

            border-radius: 8px;

            background: #2563eb;

            color: #ffffff;

            font-size: 14px;

            font-weight: 600;

            cursor: pointer;
        }

        .search-btn:hover {
            background: #1d4ed8;
        }

        .clear-btn {
            display: inline-flex;

            align-items: center;

            justify-content: center;

            height: 44px;

            padding: 0 15px;

            border: 1px solid #cbd5e1;

            border-radius: 8px;

            color: #475569;

            text-decoration: none;

            font-size: 14px;
        }

        .clear-btn:hover {
            background: #f8fafc;
        }


        /* TABLE CARD */

        .table-card {
            background: #ffffff;

            border: 1px solid #dbe3ef;

            border-radius: 14px;

            overflow: hidden;

            box-shadow:
                0 8px 25px
                rgba(37, 99, 235, 0.05);
        }

        .table-header {
            padding: 20px;

            border-bottom: 1px solid #e2e8f0;

            display: flex;

            justify-content: space-between;

            align-items: center;
        }

        .table-header h2 {
            color: #172033;

            font-size: 18px;
        }

        .book-count {
            color: #64748b;

            font-size: 13px;
        }

        .table-wrapper {
            width: 100%;

            overflow-x: auto;
        }

        table {
            width: 100%;

            border-collapse: collapse;

            min-width: 900px;
        }

        th {
            background: #f8fafc;

            color: #475569;

            font-size: 12px;

            text-transform: uppercase;

            letter-spacing: 0.4px;

            padding: 14px;

            text-align: left;

            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 15px 14px;

            color: #334155;

            font-size: 13px;

            border-bottom: 1px solid #eef2f7;

            vertical-align: middle;
        }

        tbody tr:hover {
            background: #f8fbff;
        }

        .book-name {
            color: #172033;

            font-weight: 600;
        }

        .book-code {
            font-family: monospace;

            color: #2563eb;

            font-weight: 600;
        }


        /* BADGES */

        .badge {
            display: inline-block;

            padding: 5px 10px;

            border-radius: 20px;

            font-size: 11px;

            font-weight: 600;
        }

        .available {
            background: #dcfce7;

            color: #15803d;
        }

        .issued {
            background: #fef3c7;

            color: #b45309;
        }

        .category {
            background: #eff6ff;

            color: #1d4ed8;
        }


        /* DELETE */

        .delete-btn {
            border: none;

            background: #fff1f2;

            border: 1px solid #fecdd3;

            color: #dc2626;

            padding: 7px 12px;

            border-radius: 7px;

            font-size: 12px;

            font-weight: 600;

            cursor: pointer;
        }

        .delete-btn:hover {
            background: #fee2e2;
        }


        /* EMPTY */

        .empty {
            text-align: center;

            padding: 50px 20px;

            color: #64748b;
        }

        .empty-icon {
            font-size: 40px;

            margin-bottom: 12px;
        }

        .empty h3 {
            color: #334155;

            margin-bottom: 7px;
        }

        .empty p {
            font-size: 13px;

            margin-bottom: 18px;
        }

        .add-btn {
            display: inline-flex;

            align-items: center;

            justify-content: center;

            background: #2563eb;

            color: #ffffff;

            text-decoration: none;

            padding: 11px 18px;

            border-radius: 8px;

            font-size: 13px;

            font-weight: 600;
        }

        .add-btn:hover {
            background: #1d4ed8;
        }


        /* FOOTER */

        .footer {
            text-align: center;

            color: #94a3b8;

            font-size: 12px;

            margin: 25px 0;
        }


        /* RESPONSIVE */

        @media (max-width: 850px) {

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .search-form {
                grid-template-columns: 1fr;
            }

            .search-btn,
            .clear-btn {
                width: 100%;
            }
        }

        @media (max-width: 650px) {

            .navbar {
                flex-direction: column;

                gap: 12px;

                padding: 12px 18px;
            }

            .nav-links {
                justify-content: center;

                flex-wrap: wrap;
            }

            .main {
                margin-top: 25px;
            }

            .page-header h1 {
                font-size: 24px;
            }

            .summary-card {
                padding: 18px;
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

        <a href="add_book.php">
            Add Book
        </a>

        <a href="view.php" class="active">
            View Books
        </a>

        <a href="logout.php" class="logout">
            Logout
        </a>

    </div>

</nav>


<main class="main">


    <div class="page-header">

        <h1>
            Library Books
        </h1>

        <p>
            View, search, filter and manage all books in the library.
        </p>

    </div>


    <?php if ($message !== ""): ?>

        <div class="message success">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>


    <?php if ($error !== ""): ?>

        <div class="message error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <!-- SUMMARY -->

    <div class="summary-grid">


        <div class="summary-card">

            <div class="summary-icon blue-icon">
                📚
            </div>

            <div class="summary-info">

                <span>
                    Total Books
                </span>

                <strong>
                    <?= $total_books ?>
                </strong>

            </div>

        </div>


        <div class="summary-card">

            <div class="summary-icon green-icon">
                ✓
            </div>

            <div class="summary-info">

                <span>
                    Available
                </span>

                <strong>
                    <?= $available_books ?>
                </strong>

            </div>

        </div>


        <div class="summary-card">

            <div class="summary-icon orange-icon">
                ↗
            </div>

            <div class="summary-info">

                <span>
                    Issued
                </span>

                <strong>
                    <?= $issued_books ?>
                </strong>

            </div>

        </div>

    </div>


    <!-- SEARCH -->

    <div class="search-card">

        <div class="search-title">
            Search & Filter Books
        </div>


        <form
            method="GET"
            action="view.php"
            class="search-form"
        >

            <input
                type="text"
                name="search"
                placeholder="Search by book name, author or code..."
                value="<?= htmlspecialchars($search) ?>"
            >


            <select name="category">

                <option value="">
                    All Categories
                </option>

                <?php foreach ($allowed_categories as $item): ?>

                    <option
                        value="<?= htmlspecialchars($item) ?>"
                        <?= $category === $item ? "selected" : "" ?>
                    >
                        <?= htmlspecialchars($item) ?>
                    </option>

                <?php endforeach; ?>

            </select>


            <select name="status">

                <option value="">
                    All Status
                </option>

                <?php foreach ($allowed_status as $item): ?>

                    <option
                        value="<?= htmlspecialchars($item) ?>"
                        <?= $status === $item ? "selected" : "" ?>
                    >
                        <?= htmlspecialchars($item) ?>
                    </option>

                <?php endforeach; ?>

            </select>


            <button
                type="submit"
                class="search-btn"
            >
                Search
            </button>

        </form>


        <?php if ($search !== "" || $category !== "" || $status !== ""): ?>

            <div style="margin-top:10px;">

                <a
                    href="view.php"
                    class="clear-btn"
                >
                    Clear Filters
                </a>

            </div>

        <?php endif; ?>

    </div>


    <!-- TABLE -->

    <div class="table-card">

        <div class="table-header">

            <h2>
                Book List
            </h2>

            <span class="book-count">
                <?= count($books) ?> result(s)
            </span>

        </div>


        <?php if (empty($books)): ?>

            <div class="empty">

                <div class="empty-icon">
                    📚
                </div>

                <h3>
                    No Books Found
                </h3>

                <p>
                    Try changing your search or filter options.
                </p>

                <a
                    href="add_book.php"
                    class="add-btn"
                >
                    Add New Book
                </a>

            </div>

        <?php else: ?>


            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>#</th>

                            <th>Book Name</th>

                            <th>Author</th>

                            <th>Book Code</th>

                            <th>Category</th>

                            <th>Status</th>

                            <th>Added Date</th>

                            <th>Action</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($books as $index => $book): ?>

                        <tr>

                            <td>
                                <?= $index + 1 ?>
                            </td>


                            <td class="book-name">
                                <?= htmlspecialchars($book["book_name"]) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars($book["author"]) ?>
                            </td>


                            <td class="book-code">
                                <?= htmlspecialchars($book["book_code"]) ?>
                            </td>


                            <td>

                                <span class="badge category">
                                    <?= htmlspecialchars($book["category"]) ?>
                                </span>

                            </td>


                            <td>

                                <?php if ($book["status"] === "Available"): ?>

                                    <span class="badge available">
                                        Available
                                    </span>

                                <?php else: ?>

                                    <span class="badge issued">
                                        Issued
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    date(
                                        "d M Y",
                                        strtotime($book["created_at"])
                                    )
                                ) ?>
                            </td>


                            <td>

                                <form
                                    method="POST"
                                    action="view.php"
                                    onsubmit="return confirmDelete();"
                                >

                                    <input
                                        type="hidden"
                                        name="book_id"
                                        value="<?= (int)$book["id"] ?>"
                                    >

                                    <button
                                        type="submit"
                                        name="delete_book"
                                        class="delete-btn"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>
    <div class="footer">
        Library Management System
    </div>


</main>
<script>

function confirmDelete() {

    return confirm(
        "Are you sure you want to delete this book?"
    );

}

</script>
</body>

</html>
