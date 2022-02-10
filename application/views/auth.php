<!DOCTYPE html>
<html lang="en">
<head>
    <!-- End Google Tag Manager -->
    <title>Absensi App</title>
    <!--<meta name="viewport" content="width=device-width, initial-scale=1.0">-->
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <meta charset="UTF-8">
     <meta name="description" content="Absensi App">
    <!-- External CSS libraries -->
    <link type="text/css" rel="stylesheet" href="assets/assets_login/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="assets/assets_login/fonts/font-awesome/css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="assets/assets_login/fonts/flaticon/font/flaticon.css">

    <!-- Favicon icon -->
    <link rel="shortcut icon" href="assets/assets_login/img/favicon.ico" type="image/x-icon" >
    <!--<link rel="shortcut icon" href="assets/images/logolabura.png" />-->
    <!-- <link rel="shortcut icon" href="assets/assets_login/img/logo-layanan.png" /> -->

    <!-- Google fonts -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800%7CPoppins:400,500,700,800,900%7CRoboto:100,300,400,400i,500,700">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Custom Stylesheet -->
    <link type="text/css" rel="stylesheet" href="assets/assets_login/css/style.css">
    <link rel="stylesheet" type="text/css" id="style_sheet" href="assets/assets_login/css/skins/default.css">
    
    <style>
    
    
    .login-18 .login-box-9 {
        margin: 0px !important;
        opacity: 3;
        text-align: center;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        background-size: cover;
        background: rgba(0, 0, 0, 0.04) url('./assets/images/bg-new.jpeg') top left repeat;
        background-size: cover;
        justify-content: center;
        align-items: center;
        position: relative;
        display: flex;
    }
    
    .login-18 .bg-img::before {
        opacity: 0.2;
        content: "";
        display: block;
        left: 0;
        right: 0;
        top: 0;
        background: rgba(0,0,0,9) top left repeat;
        border-radius: 10px;
        bottom: 0;
        height: 100%;
        width: 100%;
        position: absolute;
    }
    
    
    @media (min-width: 525px) {
        .logo-mini{
            display: none;    
        }
    }

    </style>


</head>
<body id="">

<!-- Login 18 start -->
<div class="login-18">
    <div class="container">
        <div class="col-md-12 col-lg-5 mx-auto">
            <div class="row login-box-9">
                <div class="col-sm-10 mx-auto">
                    <div class="login-inner-form pt-4">
                        <div class="logo-mini">
                                <img src="assets/assets_login/img/logo-layanan.png" class="mb-2" alt="logo" width="200px">
                            <h3 class="mb-2">LAYANAN ABSENSI</h3><br/>
                            <!--<h3 class="text-white">Kabupaten Labuhanbatu Utara</h3>-->
                        </div>
                        <div class="none-992">
                                <h3>LAYANAN ABSENSI</h3>
                        </div>
                        <?= $this->session->flashdata('pesan'); ?>
                        <form action="" method="POST">
                            <div class="form-group">
                                    <input type="text" class="form-control form-control-lg" name="username" id="username" value="<?= set_value('username');?>" placeholder="NIP / NIK">
                                    <?= form_error('username', '<small class="text-danger pl-3">', '</small>'); ?>

                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control form-control-lg" name="password" id="password" value="<?= set_value('password') ?>" placeholder="Password">
                                    <?= form_error('password', '<small class="text-danger pl-3">', '</small>'); ?>
                            </div>
                            <center>
                            </center>
                            <div class="form-group">
                                <button type="submit" class="btn-md btn-theme btn-block">Login</button>
                            </div>
                        </form>
                        <div class="mb-3">
                            <a href="<?=base_url('auth/lupapassword');?>" class="text-white">Lupa Password</a>
                        </div class="text-white">
                        <div class="mb-2" style="color: #fefefe">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Login 18 end -->

<!-- External JS libraries -->
<script src="assets/assets_login/js/jquery-2.2.0.min.js"></script>
<script src="assets/assets_login/js/popper.min.js"></script>
<script src="assets/assets_login/js/bootstrap.min.js"></script>
<!-- Custom JS Script -->
</body>
</html>
