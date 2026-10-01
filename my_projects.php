
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$manager_id = $_SESSION['user_id'];

$sql = $conn->prepare("SELECT * FROM projects WHERE manager_id=? ORDER BY id DESC");
$sql->bind_param('i', $manager_id);
$sql->execute();
$result = $sql->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Projects</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css" integrity="sha512-QeR2VH+lsBE5LSAe1Q5EnTBbe7XTBubt8dG93Y7gidSgdMCr8nVqKcfKAMyN96SV8KDbZVTDXChatu5G2KQGzg==" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Project Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="my_projects.php">My Projects</a></li>
                <li class="nav-item ms-lg-3"><a class="btn btn-danger" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
<div class="d-flex justify-content-between align-items-center mb-4">
<div>
<h2 class="fw-bold">My Projects</h2>
<p class="text-muted mb-0">Manage, update and delete your projects.</p>
</div>
<a href="dashboard.php" class="btn btn-primary">+ Add Project</a>
</div>

<div class="card border-0 shadow-sm">
<div class="card-body">
<div class="table-responsive">
<table class="table table-hover align-middle">
<thead class="table-dark">
<tr>
<th>ID</th>
<th>Project Name</th>
<th>Description</th>
<th>Status</th>
<th>Start Date</th>
<th>End Date</th>
<th>Actions</th>
</tr>
</thead>
<tbody>

<?php if ($result->num_rows > 0) { ?>
<?php while ($row = $result->fetch_assoc()) { ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td class="fw-semibold"><?php echo htmlspecialchars($row['project_name']); ?></td>
<td><?php echo htmlspecialchars($row['project_description']); ?></td>
<td>
<?php
$badge = 'secondary';
if ($row['status'] === 'Pending') {
    $badge = 'warning';
} elseif ($row['status'] === 'In Progress') {
    $badge = 'primary';
} elseif ($row['status'] === 'Completed') {
    $badge = 'success';
} elseif ($row['status'] === 'Cancelled') {
    $badge = 'danger';
}
?>
<span class="badge text-bg-<?php echo $badge; ?>"><?php echo htmlspecialchars($row['status']); ?></span>
</td>
<td><?php echo htmlspecialchars($row['start_date']); ?></td>
<td><?php echo htmlspecialchars($row['end_date']); ?></td>
<td class="d-flex">
<a href="edit_project.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm mb-1"><i class="fa-regular fa-pen-to-square"></i></a>
<a href="delete_project.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Are you sure you want to delete this project? This action cannot be undone.');"> <i class="fa-solid fa-trash"></i></a>
</td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
<td colspan="7" class="text-center py-5">
<h5>No Projects Found</h5>
<p class="text-muted mb-3">You haven't added any projects yet.</p>
<a href="dashboard.php" class="btn btn-primary btn-sm">Add Your First Project</a>
</td>
</tr>
<?php } ?>

</tbody>
</table>
</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
