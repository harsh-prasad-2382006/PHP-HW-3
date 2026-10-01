
<?php
session_start();

if (isset($_SESSION['task_id'])) {
    echo "Currently Active Task ID: " . $_SESSION['task_id'];
} else {
    echo "No active task found.";
}
?>
