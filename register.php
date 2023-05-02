<?php
    require_once "config.php"; 
    require_once "session.php"; 
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {

        $fullname = trim($_POST['name']);
        $lastname = trim($_POST['lastname']); 
        $email = trim($_POST['email']); 
        $password = trim($_POST['password']); 
        $confirm_password = trim($_POST["confirm_password"]); 
        $password_hash = password_hash( $password, PASSWORD_BCRYPT);

        $ip = $_SERVER['REMOTE_ADDR'];
        $captcha = $_POST['g-recaptcha-response'];
        $secretkey = '6LfPnNIlAAAAAJ4_RnSjtI5HNq4BPHpHuMYMZ5-U';
        $resCaptcha = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretkey&response=$captcha&remoteip=$ip");
        $atributos = json_decode($resCaptcha, TRUE);

        $errors = array();
        if(empty($fullname)){
            $errors[] = 'El campo de nombre es obligatorio';
        }
        if(empty($lastname)){
            $errors[] = 'El campo de apellidos es obligatorio';
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors[] = 'la direccion de correo no es valida';
        }
        if(empty($password)){
            $errors[] = 'La contraseña es Obligatoria!';
        }
        if (strlen($password) < 4) {
            $errors[]= 'La contraseña debe ser mayor a 4 caracteres'; 
        }
        if (empty($confirm_password)) { 
            $errors[]= 'Por favor confirme la contraseña'; 
        } else { 
            if (empty($error) && ($password != $confirm_password)) { 
                $errors[]= 'Error: Las contraaseñas no coiciden'; 
            } 
        }
        if(!$atributos['success']){
            $errors[] = 'Captcha no Verificado!';
        }

        $query = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $query->execute([$email]);

        if ($query->rowCount() > 0) { 
            $errors[]= 'El correo Electronico ya esta registrado!';
        } else { 
            if (count($errors) == 0) { 
                $insertQuery = $pdo->prepare("INSERT INTO users (name, lastname, email, password) VALUES (?, ?, ?, ?);");
                $result = $insertQuery->execute([$fullname, $lastname, $email, $password_hash]); 
                
                if ($result) { 
                    $succes = 'Registro Exitoso!'; 
                } else { 
                    $errors[]= 'Error de Registro de Usuario!'; 
                }
            }
        }
        $query = null;
        $insertQuery = null;
        $pdo = null;
    }
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>..: Registro :..</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image">

                    </div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Crear una Cuenta Nueva!</h1>
                            </div>
                            <hr>
                            <?php
                            if(isset($errors)){
                                if(count($errors) > 0){
                            ?>
                                <div claas="text-center">
                                    <div class="alert alert-danger" role="alert">
                                        <?php
                                            foreach($errors as $error){
                                                echo $error.'<br>';
                                            }
                                        ?>
                                    </div>
                                </div>
                            <?php
                                }
                            }
                            ?>
                            <hr>
                            <form class="user" action="" method="post">
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" name="name" class="form-control form-control-user" id="exampleFirstName"
                                            placeholder="Nombre" require>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" name="lastname" class="form-control form-control-user" id="exampleLastName"
                                            placeholder="Apellidos" require>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control form-control-user" id="exampleInputEmail"
                                        placeholder="Correo Electronico" require>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" name="password" class="form-control form-control-user"
                                            id="exampleInputPassword" placeholder="Contraseña" require>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" name="confirm_password" class="form-control form-control-user"
                                            id="exampleRepeatPassword" placeholder="Confirmar Contraseña" require>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="g-recaptcha" data-sitekey="6LfPnNIlAAAAAAxaoyaNBH4zazFt74UQp9kce7Zx">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="submit" class="btn btn-primary btn-user btn-block" value="Registrarme">
                                </div>
                                <hr>
                            </form>
                            <?php 
                                if(isset($succes)){ 
                            ?>
                                <div class="text-center">
                                    <div class="alert alert-success" role="alert">
                                        <?php echo $succes ?>
                                    </div>
                                </div>
                            <?php 
                                }
                            ?>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="forgot-password.php">¿Olvidaste tu Contraseña?</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="login.php">¿Ya tienes una Cuenta? Iniciar Secion!!!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

</body>

</html>