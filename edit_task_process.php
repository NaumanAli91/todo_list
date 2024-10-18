<?php
include 'db.php';
session_start();
if(isset($_SESSION['edit_task'])) {
    $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $query = $con->prepare('update tasks set description = ?, due_date = ? where task_id = ?');
    $query->execute(array($_POST['description'], $due_date, $_SESSION['edit_task']));
    unset($_SESSION['edit_task']);
    header('Location: index.php');
}


else header('Location: index.php');
