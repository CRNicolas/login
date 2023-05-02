<?php
    require_once "config.php"; 
    require_once "session.php";

    $error='';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {

        $email = trim($_POST['email']); 
        $password = trim($_POST['password']);

        $ip = $_SERVER['REMOTE_ADDR'];
        $captcha = $_POST['g-recaptcha-response'];
        $secretkey = '6LfPnNIlAAAAAJ4_RnSjtI5HNq4BPHpHuMYMZ5-U';
        $resCaptcha = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretkey&response=$captcha&remoteip=$ip");
        $atributos = json_decode($resCaptcha, TRUE);

        $errors = array();
        if(empty($email)){
            $errors[]= 'Por favor ingrese su Correo!';
        }
        if(empty($password)){
            $errors[]= 'Por favor ingrese su contraseña!';
        }
        if(!$atributos['success']){
            $errors[] = 'Captcha no Verificado!';
        }

        if(count($errors) == 0){
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bindParam(1, $email); 
            $stmt->execute();
            $row = $stmt->fetch();

            if($row){
                if(password_verify($password, $row['password'])){
                    $_SESSION['userid'] = $row['id'];
                    $_SESSION['user'] = $row;
                    header("Location: index.php");
                    exit;
                }else{
                    $errors[]= 'La contraseña no es valida!';
                }
            }else{
                $errors[] = 'Cuenta de usuario asociado al correo no encontrado!';
            }
            $stmt=null;
        }
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

    <title>Inicio de Sesion</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image">
                                
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Bienvenidos</h1>
                                    </div>
                                    <form class="user"  action="" method="post">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Ingrese su Correo..." name="email" require>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control form-control-user"
                                                id="exampleInputPassword" placeholder="Contraseña" require>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Recordarme</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="g-recaptcha" data-sitekey="6LfPnNIlAAAAAAxaoyaNBH4zazFt74UQp9kce7Zx">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" name="submit" class="btn btn-primary btn-user btn-block" value="Iniciar">
                                        </div>
                                        <hr>
                                    </form>
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
                                        <a class="small" href="register.php">Crear nueva Cuenta!</a>
                                    </div>
                                </div>
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