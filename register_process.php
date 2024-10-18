<?php
include 'db.php';
session_start();
$_POST = array_map('trim', $_POST);

// Validate input
if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirm_password'])) {
    $_SESSION['error'] = 'Please fill in all the fields.';
    header('Location: register.php');
    exit(); // Added exit to ensure no further code is executed
}

if (strlen($_POST['password']) < 6) {
    $_SESSION['error'] = 'Password must be at least 6 characters.';
    header('Location: register.php');
    exit();
} 

if ($_POST['password'] === $_POST['confirm_password']) {
    // Check if username already exists
    $stmt = $con->prepare('SELECT count(*) FROM users WHERE username = ?');
    $stmt->execute(array($_POST['username']));
    
    if ($stmt->fetchColumn()) {
        $_SESSION['error'] = 'Username already exists.';
        header('Location: register.php');
        exit();
    } else {
        // Insert new user
        $stmt = $con->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
        if ($stmt->execute(array($_POST['username'], $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT)))) {
            $_SESSION['success'] = 'Account created successfully. Please log in.';
            header('Location: login.php'); // Redirect to login page after successful registration
            exit();
        } else {
            $_SESSION['error'] = 'There was a problem creating your account.';
            header('Location: register.php');
            exit();
        }
    }
} else {
    $_SESSION['error'] = 'Passwords must match.';
    header('Location: register.php');
    exit();
}
