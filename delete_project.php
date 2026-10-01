
<?php

include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $manager_id = $_SESSION['user_id'];

    $sql = $conn->prepare('DELETE FROM projects WHERE id=? AND manager_id=?');
    $sql->bind_param('ii', $id, $manager_id);

    if ($sql->execute()) {
        header('Location: my_projects.php');
        exit();
    }
}

?>
