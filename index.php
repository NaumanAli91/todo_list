<?php
session_start();
include 'db.php'; // Include your database connection file

// Handle delete task
if (isset($_GET['del_task'])) {
    $query = $con->prepare('DELETE FROM tasks WHERE task_id = ?');
    $query->execute(array($_GET['del_task']));
    header('Location: index.php');
    exit();
}

// Handle mark task as complete
if (isset($_GET['mark_complete'])) {
    $query = $con->prepare('UPDATE tasks SET is_completed = 1 WHERE task_id = ?');
    $query->execute(array($_GET['mark_complete']));
    header('Location: index.php');
    exit();
}

// Handle edit task
if (isset($_GET['edit_task'])) {
    $_SESSION['edit_task'] = $_GET['edit_task'];

    // Fetch the description for the task to be edited
    $query = $con->prepare('SELECT description FROM tasks WHERE task_id = ?');
    $query->execute(array($_SESSION['edit_task']));
    $row = $query->fetch(PDO::FETCH_ASSOC);
    $_SESSION['description'] = $row['description'];
    header('Location: edit_task.php');
    exit();
}

// Handle task filtering
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$result = [];

if (isset($_SESSION['user_id'])) {
    if ($filter == 'completed') {
        $query = $con->prepare('SELECT * FROM tasks WHERE user_id = ? AND is_completed = 1');
    } elseif ($filter == 'pending') {
        $query = $con->prepare('SELECT * FROM tasks WHERE user_id = ? AND is_completed = 0');
    } else {
        $query = $con->prepare('SELECT * FROM tasks WHERE user_id = ?');
    }
    $query->execute(array($_SESSION['user_id']));
    $result = $query->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>To-Do List</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <a id='app_name' href="#">To-do List</a>
        <a href="login.php" id='login_btn'>Log in</a>
        <a href="register.php" id='signup_btn'>Sign up</a>
        <a href="logout.php" id='logout_btn'>Log out</a>
    </header>

    <div>
        <?php
        if (isset($_SESSION['user'])) { // User is logged in
            echo '<script>document.querySelector("#login_btn").style.display = "none";</script>';
            echo '<script>document.querySelector("#signup_btn").style.display = "none";</script>';
            echo '<script>document.querySelector("#logout_btn").style.display = "inline-block";</script>';
            echo '<h3 id="username">' . htmlspecialchars($_SESSION['user'], ENT_QUOTES) . '</h3>';
            ?>

            <form action="add_task.php" method='post' id='add_task_form'>
                <input type="text" name='description' placeholder='Task Description' id='description' required>
                <input type="datetime-local" name='due_date' id='due_date'>
                <input type="submit" value='Add Task' id='add_task_submit'>
            </form>

            <!-- Task Filtering -->
            <form method="GET" id="filter_form">
                <select name="filter" onchange="this.form.submit()">
                    <option value="all" <?php if ($filter == 'all') echo 'selected'; ?>>All Tasks</option>
                    <option value="pending" <?php if ($filter == 'pending') echo 'selected'; ?>>Pending Tasks</option>
                    <option value="completed" <?php if ($filter == 'completed') echo 'selected'; ?>>Completed Tasks</option>
                </select>
            </form>

            <?php
            // Display user's tasks
            foreach ($result as $row) {
                ?>
                <div class='task_container' style="<?php echo $row['is_completed'] ? 'text-decoration: line-through;' : ''; ?>">
                    <p><?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?></p>
                    <p>Due: <?php echo $row['due_date'] ? date('Y-m-d H:i', strtotime($row['due_date'])) : 'N/A'; ?></p>
                    <a href="index.php?del_task=<?php echo $row['task_id']; ?>">Delete</a>
                    <a href="index.php?edit_task=<?php echo $row['task_id']; ?>">Edit</a>
                    <a href="index.php?mark_complete=<?php echo $row['task_id']; ?>">Mark Complete</a>
                </div>
                <?php
            }
        } else { // User is logged out
           
        }
        ?>
    </div>
</body>

</html>
