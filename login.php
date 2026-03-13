<?php
session_start();
include_once 'app/classes/Database.class.php';
include_once 'app/classes/Page.class.php';
include_once 'app/classes/models/Extras.php';
include_once Extras::fileExists("app/classes/models/OrderModel.php");
include_once Extras::fileExists("app/classes/controllers/OrderController.class.php"); 

if (isset($_SESSION['user_name'])) {
    Page::route('/admin/index.php');
}

if (isset($_GET['error'])) {
    if ($_GET['error'] == "emptyinput") {
        $message = 'Fill in all fields!';
    } else if ($_GET['error'] == "usernamenotexist") {
        $message = 'Username does not exist!';
    } else if ($_GET['error'] == "incorrectpassword") {
        $message = 'Incorrect password!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory | Log in</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href=<?php echo Page::asset('/public/plugins/fontawesome-free/css/all.min.css'); ?>>
    <link rel="stylesheet" href=<?php echo Page::asset('/public/dist/css/adminlte.min.css'); ?>>
    <link rel="stylesheet" href="<?php echo Page::asset('/public/plugins/toastr/toastr.min.css'); ?>">
    <link href="public/assets/css/style.css" rel="stylesheet">
</head>
<style>
    .container {
        padding-top: 5%;
    }
    
    .imgcontainer {
        text-align: center;
        margin: 24px 0 12px 0;
        position: relative;
    }
    .avatar {
        width: 40%;
        border-radius: 50%;
    }
    .login-box {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    }
    .hero {
        height: 100vh;
        width: 100%;
        background-position: center;
        background-size: cover;
        position: relative;
    }

    .login-card-body {
        position: relative;
        z-index: 2;
    }   


</style>
<body class="hold-transition login-page">
    <section id="hero" style="background: url('app/uploads/4.png'); background-size: 100% 100%; background-repeat: no-repeat;" class="hero d-flex align-items-center">

        <div class="container">
            <div class="row">
                <div class="col-lg-5 m-auto">
                    <div class="login-box">
                        <div class="login-logo">
                            <a href='index.php'><b></b></a>
                        </div>
                        <div class="card">
                            <div class="card-body login-card-body">
                                <div class="imgcontainer">
                                    <img src="<?= Page::asset('/public/dist/img/avatar.png') ?>" alt="Avatar" class="avatar" style="width: 25%">
                                </div>

                                <form action="./app/includes/auth/login.inc.php" method="POST">
                                    <div class="input-group mb-3">
                                        <input type="text" name="username" class="form-control" placeholder="Username">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-envelope"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3">
                                        <input type="password" name="password" class="form-control" placeholder="Password">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-lock"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3">
                                        <button type="submit" name="submit" class="btn btn-primary btn-block">Sign In</button>
                                    </div>
                                    <div class="input-group m-3">
                                        <a href="#" data-toggle="modal" data-target="#forgot-password-modal">Forgot password?</a> &nbsp; | &nbsp; <a href="register.php">Create new Account</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <div class="modal fade" id="forgot-password-modal" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-label">Forgot Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div tag="display">
                        <form id="forgot-form" enctype="multipart/form-data">
                            <div class="row">
                                <div class="input-group mb-3">
                                    <input type="text" name="email" class="form-control" placeholder="Email">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-envelope"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery -->
    <script src=<?php echo Page::asset('/public/plugins/jquery/jquery.min.js'); ?>></script>
    <script src=<?php echo Page::asset('/public/plugins/bootstrap/js/bootstrap.bundle.min.js'); ?>></script>
    <script src=<?php echo Page::asset('/public/dist/js/adminlte.min.js'); ?>></script>
    <script src="<?php echo Page::asset('/public/plugins/toastr/toastr.min.js'); ?>"></script>
    <script src="public/assets/js/main.js"></script>
</body>

</html>

<script>
    $(function() {
        let message = '<?php echo $message ?? '' ?>';
        if (message) {
            toastr.error(message);
        }

        $(document).off('submit', '#forgot-form').on('submit', '#forgot-form', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: '<?=Extras::fileExists("app/classes/controllers/UserController.class.php");?>?action=forgotPassword',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    toastr.info('<i class="fa fa-spinner fa-spin"></i> Loading...', 'Please wait');
                },
                success: function(response) {
                    var res = JSON.parse(response);
                    setTimeout(() => {
                        toastr.remove();
                        if (res.status === 'success') {
                            toastr.success(res.message);
                            $('#forgot-password-modal').modal('hide');
                        } else {
                            toastr.error(res.message);
                        }
                    }, 1000);
                },
                error: function() {
                    toastr.error('An error occurred while processing the request.');
                }
            });
        });
    })
</script>
