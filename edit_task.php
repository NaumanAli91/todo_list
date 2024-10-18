<?php
session_start();

// Redirect if no task is being edited
if (!isset($_SESSION['edit_task'])) {
    header('Location: index.php');
    exit();
}

// Include database connection
include 'db.php';

// Fetch the task details to be edited
$query = $con->prepare('SELECT description, due_date FROM tasks WHERE task_id = ?');
$query->execute(array($_SESSION['edit_task']));
$row = $query->fetch(PDO::FETCH_ASSOC);

// Store description and due date in session for easy access
$_SESSION['description'] = $row['description'];
$_SESSION['due_date'] = $row['due_date']; // Store due date in session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Task</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <a id='app_name' href="#">To-do List</a>
    <a href="logout.php" id='logout_btn'>Log out</a>
</header>

<form action="edit_task_process.php" method='post'>
    <h3>Edit Task</h3>
    <input type="text" value='<?php echo htmlspecialchars($_SESSION["description"], ENT_QUOTES); ?>' name='description' required>
    <input type="datetime-local" value='<?php echo $_SESSION["due_date"] ? date('Y-m-d\TH:i', strtotime($_SESSION["due_date"])) : ''; ?>' name='due_date'>
    <input type="submit" value='Save'>
    <button id='cancel_btn'><a href="edit_task.php?cancel=1">Cancel</a></button>
</form>

<?php
// Handle the cancel action
if (isset($_GET['cancel']) && $_GET['cancel'] == 1) {
    unset($_SESSION['edit_task']);
    header('Location: index.php');
    exit();
}
?>
</body>
</html>
