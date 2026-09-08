# Library Management System

## 1. Student Details
**Student Name:** Ashwini Bhimrao Kawale
**Roll No.:** MLU24F074
**Division:** B
**Project Title:** Library Management System

---
## 2. Problem Statement

The Library Management System is a database-driven web application developed using PHP and MySQL. It allows users to securely add book records, store them in a MySQL database, view saved books, search/filter records, and delete book records.

The application demonstrates the complete flow:

**HTML Form → PHP → MySQL Database → SELECT → Browser Output**
---

## 3. Objective
The main objectives of this project are:

* To develop a simple database-driven web application.
* To store and manage library book records.
* To perform INSERT and SELECT operations using MySQL.
* To provide search and filtering functionality.
* To implement secure database operations using prepared statements.
* To host the application on InfinityFree.

---

## 4. Main Features
* User Registration
* Secure Login and Logout
* Add Book
* View Book Records
* Search Books
* Filter by Category and Status
* Delete Book Records
* MySQL database storage
* Prepared statements for SQL security
* Responsive and clean user interface

---

## 5. Technology Stack

| Technology         | Purpose                                |
| ------------------ | -------------------------------------- |
| HTML5              | Web page structure and forms           |
| CSS3               | User interface and styling             |
| Vanilla JavaScript | Client-side validation and interaction |
| PHP                | Server-side programming                |
| MySQL              | Database management                    |
| MySQLi             | PHP-MySQL connectivity                 |
| XAMPP              | Local development                      |
| InfinityFree       | Web hosting                            |

---

## 6. Project Structure
Library-Management-System/
│
├── index.php
├── register.php
├── logout.php
├── connect.php
├── add_book.php
├── save_book.php
├── view.php
├── database.sql
├── README.md
└── screenshots/
    ├── 01-login.png
    ├── 02-add-book.png
    ├── 03-success.png
    ├── 04-view-books.png
    ├── 05-search-filter-delete.png
    ├── 06-security-test.png
    └── 07-hosted-website.png

### File Description

* **index.php** – Login page.
* **register.php** – New user registration.
* **logout.php** – Destroys the user session.
* **connect.php** – Connects PHP with MySQL.
* **add_book.php** – Provides the book entry form.
* **save_book.php** – Validates and securely inserts book data.
* **view.php** – Displays, searches, filters, and manages book records.
* **database.sql** – Creates database tables and contains sample records.

---

## 7. Database Design

### Users Table

| Column     | Data Type    | Constraint                  |
| ---------- | ------------ | --------------------------- |
| id         | INT          | Primary Key, Auto Increment |
| full_name  | VARCHAR(100) | NOT NULL                    |
| username   | VARCHAR(50)  | UNIQUE, NOT NULL            |
| email      | VARCHAR(100) | UNIQUE, NOT NULL            |
| password   | VARCHAR(255) | NOT NULL                    |
| created_at | TIMESTAMP    | Default Current Timestamp   |

### Books Table

| Column     | Data Type    | Constraint                  |
| ---------- | ------------ | --------------------------- |
| id         | INT          | Primary Key, Auto Increment |
| book_name  | VARCHAR(150) | NOT NULL                    |
| author     | VARCHAR(100) | NOT NULL                    |
| book_code  | VARCHAR(50)  | UNIQUE, NOT NULL            |
| category   | VARCHAR(50)  | NOT NULL                    |
| status     | ENUM         | NOT NULL                    |
| created_at | TIMESTAMP    | Default Current Timestamp   |

### Simple Schema

```text
USERS
----------------
id (PK)
full_name
username (UNIQUE)
email (UNIQUE)
password
created_at

BOOKS
----------------
id (PK)
book_name
author
book_code (UNIQUE)
category
status
created_at
```
---

## 8. Application Flow

```text
User
  ↓
HTML Form
  ↓
PHP Validation
  ↓
Prepared SQL Statement
  ↓
MySQL Database
  ↓
SELECT / Search / Delete
  ↓
Browser Output
```

The user enters book information through the HTML form. PHP validates the submitted data and uses prepared statements to securely communicate with MySQL. The stored records are retrieved and displayed through the browser.

---

## 9. SQL Operations Used

### INSERT
Used to store new user and book records in the database.

### SELECT
Used to retrieve and display stored books.

### SEARCH/FILTER
Used to find books according to book name, author, book code, category, or status.

### DELETE
Used to remove a selected book record from the database.

---

## 10. Security Implementation

The project follows the security requirements specified for the assignment.

* **Prepared Statements:** User input is passed using `?` placeholders and parameter binding to help prevent SQL injection.
* **Server-side Validation:** PHP validates important input before database operations.
* **HTML Output Protection:** `htmlspecialchars()` is used when displaying database values.
* **Password Hashing:** User passwords are stored using `password_hash()`.
* **Session Authentication:** Protected pages require a valid user session.
* **Separate Connection File:** Database connection details are kept in `connect.php`.
* **Error Protection:** Detailed database errors are not exposed to normal users.
* **Credentials Protection:** Database credentials should not be published in screenshots or public GitHub repositories.

---

## 12. Test Cases and Output Screenshots
### 12.1 Login Page
The login page allows registered users to securely access the Library Management System.
![Login Page](screenshots/01-login.png)

### 12.2 Add Book Form
The Add Book form allows the user to enter book name, author, book code, category, and status.
![Add Book Form](screenshots/02-add-book.png)

### 12.3 Successful Book Insertion
After submitting valid book details, the system successfully stores the book record in the MySQL database.
![Successful Book Insertion](screenshots/03-success.png)


### 12.4 View Books
The View Books page displays the book records stored in the MySQL database.
![View Books](screenshots/04-view-books.png)


### 12.5 Search, Filter and Delete
The system provides options to search/filter book records and delete selected records.
![Search Filter Delete](screenshots/05-search-filter-delete.png)

### 12.6 Security Test
A harmless input containing a special character such as `O'Reilly Python` was tested. The record was successfully processed without an SQL error, demonstrating safe handling of user input.
![Security Test](screenshots/06-security-test.png)

### 12.7 Hosted Application
The completed application was deployed and tested on InfinityFree hosting.
![Hosted Website](screenshots/07-hosted-website.png)


## 13. Declaration

I hereby declare that the **Library Management System** project is my original work developed for the DBMS Unit 4 assignment. The project has been implemented using HTML/CSS, PHP, and MySQL and follows the required database and security practices.
