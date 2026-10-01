
<?php

session_start();
include 'db.php';
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = $conn->prepare('SELECT * FROM project_managers WHERE email=?');
$sql->bind_param('s',$email);
$sql->execute();
$result = $sql->get_result();

if($result->num_rows > 0){
    $row = $result->fetch_assoc();

    if(password_verify($password,$row['password'])){
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['name'] = $row['full_name'];
        $_SESSION['email'] = $row['email'];

        header('Location:dashboard.php');
        exit();
    }else{
        $error = 'Invalid email or password';
    }
}else{
    $error = 'Invalid email or password';
}
    
}

?>


<!doctype html>
<html lang="en" data-bs-theme="light">
    <head>
        <title>Title</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <!-- Bootstrap CSS v5.3.8 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
            crossorigin="anonymous"
        />
    </head>

    <body>
        <main>

            <h3 class="text-center my-4">Login </h3>
            <div class="container border col-lg-5   rounded py-4 shadow">
                <?php if($error != ''){ ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } ?>
                <form action="" method="post">
                    <div class="form-floating mb-3">
                        <input
                            type="email"
                            class="form-control"
                            name="email"
                            id="email"
                            placeholder=""
                        />
                    
                        <label for="formId1">Email</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input
                            type="password"
                            class="form-control"
                            name="password"
                            id="password"
                            placeholder=""
                        />
                        <label for="formId1">Password</label>
                    </div>
                        

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Login
                        </button>
                        <div class="text-center mt-3">
    <span>Don't have an account?</span>
    <a href="register.php">Register</a>
</div>
                    
                    
                </form>
            </div>

        </main>
        <footer>
            <!-- place footer here -->
        </footer>
        <!-- Bootstrap JavaScript Bundle (includes Popper) -->
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"
        ></script>
    </body>
</html>