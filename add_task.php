<?php
include 'db.php';
session_start();
$_POST = array_map('trim', $_POST);
if(!empty($_POST['description'])) {
    $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $query = $con->prepare('insert into tasks (user_id, description, due_date) values (?, ?, ?)');
    $query->execute(array($_SESSION['user_id'], $_POST['description'], $due_date));
    header('Location: index.php');
}


else header('Location: index.php');