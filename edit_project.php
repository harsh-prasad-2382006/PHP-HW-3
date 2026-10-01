
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$manager_id = $_SESSION['user_id'];
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if (!$id || $id < 1) {
    header('Location: my_projects.php');
    exit();
}

$error = '';

$sql = $conn->prepare("SELECT * FROM projects WHERE id = ? AND manager_id = ?");
$sql->bind_param('ii', $id, $manager_id);
$sql->execute();
$result = $sql->get_result();
$project = $result->fetch_assoc();

if (!$project) {
    header('Location: my_projects.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['project_name'] ?? '');
    $description = trim($_POST['project_description'] ?? '');
    $status = $_POST['status'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $statuses = ['Pending', 'In Progress', 'Completed', 'Cancelled'];

    if ($name === '' || $description === '' || $start_date === '' || $end_date === '') {
        $error = 'Please fill in all fields.';
    } elseif (!in_array($status, $statuses, true)) {
        $error = 'Please select a valid status.';
    } elseif ($end_date < $start_date) {
        $error = 'End date cannot be before start date.';
    } else {
        $sql = $conn->prepare("UPDATE projects SET project_name = ?, project_description = ?, status = ?, start_date = ?, end_date = ? WHERE id = ? AND manager_id = ?");
        $sql->bind_param('sssssii', $name, $description, $status, $start_date, $end_date, $id, $manager_id);

        if ($sql->execute()) {
            header('Location: my_projects.php');
            exit();
        } else {
            $error = 'Unable to update project.';
        }
    }

    $project['project_name'] = $name;
    $project['project_description'] = $description;
    $project['status'] = $status;
    $project['start_date'] = $start_date;
    $project['end_date'] = $end_date;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Project</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
<div class="card shadow-sm mx-auto" style="max-width: 700px;">
<div class="card-header bg-dark text-white">
<h3 class="mb-0">Edit Project</h3>
</div>
<div class="card-body">
<?php if ($error !== '') { ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php } ?>
<form method="POST">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<div class="mb-3">
<label class="form-label">Project Name</label>
<input type="text" name="project_name" class="form-control" value="<?php echo htmlspecialchars($project['project_name']); ?>" required>
</div>
<div class="mb-3">
<label class="form-label">Description</label>
<textarea name="project_description" class="form-control" rows="4" required><?php echo htmlspecialchars($project['project_description']); ?></textarea>
</div>
<div class="mb-3">
<label class="form-label">Status</label>
<select name="status" class="form-select" required>
<?php foreach (['Pending', 'In Progress', 'Completed', 'Cancelled'] as $option) { ?>
<option value="<?php echo $option; ?>" <?php echo $project['status'] === $option ? 'selected' : ''; ?>><?php echo $option; ?></option>
<?php } ?>
</select>
</div>
<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">Start Date</label>
<input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($project['start_date']); ?>" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">End Date</label>
<input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($project['end_date']); ?>" required>
</div>
</div>
<button type="submit" class="btn btn-primary">Update Project</button>
<a href="my_projects.php" class="btn btn-secondary">Cancel</a>
</form>
</div>
</div>
</div>
</body>
</html>
