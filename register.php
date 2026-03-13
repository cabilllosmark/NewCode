<?php
session_start();
include_once 'app/classes/Database.class.php';
include_once 'app/classes/Page.class.php';
include_once 'app/classes/models/Extras.php';
if (isset($_SESSION['user_name'])) {
    Page::route('/admin/index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory | Register</title>

    <!-- <link href="public/assets/img/logo.png" rel="icon"> -->

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href=<?php echo Page::asset('/public/plugins/fontawesome-free/css/all.min.css'); ?>>
    <link rel="stylesheet" href=<?php echo Page::asset('/public/dist/css/adminlte.min.css'); ?>>
    <link rel="stylesheet" href="<?php echo Page::asset('/public/plugins/toastr/toastr.min.css'); ?>">
    <link href="public/assets/css/style.css" rel="stylesheet">
</head>

<body class="hold-transition login-page">
    <section id="hero" style="background: url('public/dist/img/3.jpg'); background-size: 100% 100%; background-repeat: no-repeat;" class="hero d-flex align-items-center">

        <div class="container">
            <div class="row">
                <div class="col-lg-12 m-auto">
                    <div class="login-box" style="width:100%">
                        <div class="login-logo">
                            <a href='index.php'><b></b></a>
                        </div>
                        <!-- /.login-logo -->
                        <div class="card" style="width:100%">
                            <div class="card-body login-card-body">
                                <div class="mb-3">
                                    <h3 class="text-center">Register</h3> <br>
                                    <p>Please fill in this form to create an account. </p>
                                </div>

                                <form id="register-form" method="POST">
                                    <input type="hidden" name="submit" value="submit">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="first_name">First Name</label>
                                            <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="middle_name">Middle Name</label>
                                            <input type="text" name="middle_name" class="form-control" placeholder="Middle Name">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="last_name">Last Name</label>
                                            <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="username">ID Number</label>
                                            <input type="text" name="username" class="form-control" placeholder="Student Number" required>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="email">Email</label>
                                            <div class="input-group">
                                                <input type="text" name="email" class="form-control" placeholder="Email" required>
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="password">Password</label>
                                            <div class="input-group">
                                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><span class="fas fa-lock"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-3">
                                        Note: Your student number will serve as your username when logging in.
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-4">
                                            <button type="submit" name="submit" class="btn btn-primary btn-block">Sign Up</button>
                                        </div>
                                        <div class="col-md-4">
                                            <a href="login.php" name="submit" class="btn btn-danger btn-block">Cancel</a>
                                        </div>
                                    </div>
                                </form>
                                <p class="mb-0">
                                </p>
                            </div>
                            <!-- /.login-card-body -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section><!-- End Hero -->

    <!-- /.login-box -->

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
        $(document).on('submit', '#register-form', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: '<?=Extras::fileExists("app/includes/auth/register.inc.php");?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    var res = JSON.parse(response);
                    if (res.status === 'success') {
                        toastr.success(res.message);
                        $("#register-form")[0].reset();
                    } else {
                        toastr.error(res.message);
                    }
                },
                error: function() {
                    toastr.error('An error occurred while processing the request.');
                }
            });
        });
    });
</script>
