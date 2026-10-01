<?php

include 'db.php';

$error = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if(empty($name) || empty($email) || empty($password) || empty($confirm_password)){
        $error = 'All fields are mandatory';
    } elseif($password !== $confirm_password){
        $error = 'Passwords do not match';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = 'Email format does not match';
    } else {
        $check = $conn->prepare('SELECT * FROM project_managers WHERE email=?');
        $check->bind_param('s',$email);
        $check->execute();
        $res = $check->get_result();

        if($res->num_rows > 0){
            $error = 'User with this email already exists';
        } else {
            $hashedPassword = password_hash($password,PASSWORD_BCRYPT);

            $sql = $conn->prepare('INSERT INTO project_managers(full_name,email,password) VALUES(?,?,?)');
            $sql->bind_param('sss',$name,$email,$hashedPassword);

            if($sql->execute()){
                header('Location:login.php');
                exit;
            }
        }
    }
}

?>

<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <title>Project Manager Registration</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous"
    />
</head>

<body>

<main>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="text-center mb-4">
                        Project Manager Registration
                    </h2>

                    <?php if($error != ''){ ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } ?>

                    <form action="" method="post">

                        <div class="form-floating mb-3">
                            <input
                                type="text"
                                class="form-control"
                                name="name"
                                id="name"
                                placeholder=""
                            />
                            <label for="name">Enter full name</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input
                                type="email"
                                class="form-control"
                                name="email"
                                id="email"
                                placeholder=""
                            />
                            <label for="email">Email</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input
                                type="password"
                                class="form-control"
                                name="password"
                                id="password"
                                placeholder=""
                            />
                            <label for="password">Password</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input
                                type="password"
                                class="form-control"
                                name="confirm_password"
                                id="confirm_password"
                                placeholder=""
                            />
                            <label for="confirm_password">Confirm Password</label>
                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Register
                        </button>
                        <div class="text-center mt-3">
                            <span>Already have an account?</span>
                            <a href="login.php">Login</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous">
</script>

</body>
</html>