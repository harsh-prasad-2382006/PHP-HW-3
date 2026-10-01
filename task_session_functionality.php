
<?php
session_start();

$_SESSION['task_id'] = 101;

echo "Active Task ID: " . $_SESSION['task_id'];
?>
