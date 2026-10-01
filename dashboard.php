
<?php

session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header('Location:login.php');
    exit();
}

$message = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $project_name = trim($_POST['project_name']);
    $project_description = trim($_POST['project_description']);
    $status = $_POST['status'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $manager_id = $_SESSION['user_id'];

    if(empty($project_name) || empty($project_description) || empty($status) || empty($start_date) || empty($end_date)){
        $message = 'All fields are mandatory';
    } elseif(!in_array($status, ['Pending', 'In Progress', 'Completed', 'Cancelled'], true)){
        $message = 'Invalid project status';
    } elseif($end_date < $start_date){
        $message = 'End date cannot be before start date';
    } else {
        $sql = $conn->prepare('INSERT INTO projects(project_name, project_description, status, start_date, end_date, manager_id) VALUES(?,?,?,?,?,?)');
        $sql->bind_param('sssssi', $project_name, $project_description, $status, $start_date, $end_date, $manager_id);

        if($sql->execute()){
            header('Location:dashboard.php?success=1');
            exit();
        } else {
            $message = 'Failed to add project';
        }
    }
}

if(isset($_GET['success'])){
    $message = 'Project added successfully';
}

$result = $conn->query("SELECT projects.*, project_managers.full_name FROM projects JOIN project_managers ON projects.manager_id = project_managers.id ORDER BY projects.id DESC");

?>

<!doctype html>
<html lang="en">
<head>
    <title>Project Management Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
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

<div class="container py-4">

    <h2>Hello, <?php echo htmlspecialchars($_SESSION['name']); ?> 👋</h2>
    <p class="text-muted">Welcome to your project management dashboard.</p>

    <?php if($message != ''){ ?>
        <div class="alert <?php echo isset($_GET['success']) ? 'alert-success' : 'alert-danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>

    <div class="card shadow-sm my-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Add New Project</h5>
        </div>
        <div class="card-body">
            <form action="" method="post">

                <div class="mb-3">
                    <label class="form-label">Project Name</label>
                    <input type="text" name="project_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Project Description</label>
                    <textarea name="project_description" class="form-control" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="">Select Status</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Add Project</button>

            </form>
        </div>
    </div>

    <h4 class="mb-3">All Projects</h4>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover bg-white align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Project Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Project Manager</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result->num_rows > 0){ ?>
                    <?php while($row = $result->fetch_assoc()){ ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['project_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['project_description']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7" class="text-center">No projects found</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
