<!DOCTYPE html>
<html lang="en">

<head>
  <base href="<?=base_url();?>" />
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?=isset($title) ? $title : "Layanan Absensi";?></title>

  <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="assets/css/style.css">
  
  

<!-- Select 2-->
<link rel="stylesheet" href="<?= base_url('assets/') ?>vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?= base_url('assets/') ?>vendors/select2-bootstrap-theme/select2-bootstrap.min.css">


<!--DAte picker-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css" />

  <!-- Plugin css for this page -->
  <?php 
    if(isset($css)){ 
      for($i=0; $i<count($css); $i++){
  ?>
    <link rel="stylesheet" href="<?=$css[$i];?>">
  <?php }} ?>
  <!-- End plugin css for this page -->

<style>
    <?=isset($cssCode) ? $cssCode : null;?>
	button.dt-button{
		border: 1px solid rgb(135 135 135 / 20%);
		border-radius: 6px;
	}
    .iframe_homepopup{
        width: 100%;
        height: 500px;
    }

    .select2-container--bootstrap .select2-results__option[aria-selected=true] {
        background-color: #f5f5f5;
        color: #262626;
        display: none;
    }
    .table th, .jsgrid .jsgrid-table th, .table td, .jsgrid .jsgrid-table td {
        padding: 0.65rem 0.9375rem;
    }
    .horizontal-menu .bottom-navbar .page-navigation > .nav-item.active > .nav-link .menu-title, .horizontal-menu .bottom-navbar .page-navigation > .nav-item.active > .nav-link .menu-arrow {
        color: #7b7575;
    }
    .horizontal-menu .bottom-navbar .page-navigation > .nav-item .submenu ul li a.active {
        color: #7b7575;
    }
    .labura-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        padding-top: 1%;
        padding-bottom: 1%;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0, 0, 0);
        background-color: rgba(0, 0, 0, 0.4);
    }
    
    .labura-modal-content {
        margin: auto;
        width: 80%;
    }
    
    .labura-modal-close {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    
    .card-modal {
        height: 700px;
    }
	.dataTables_wrapper .dataTable .btn, .dataTables_wrapper .dataTable .fc button, .fc .dataTables_wrapper .dataTable button, .dataTables_wrapper .dataTable .ajax-upload-dragdrop .ajax-file-upload, .ajax-upload-dragdrop .dataTables_wrapper .dataTable .ajax-file-upload, .dataTables_wrapper .dataTable .swal2-modal .swal2-buttonswrapper .swal2-styled, .swal2-modal .swal2-buttonswrapper .dataTables_wrapper .dataTable .swal2-styled, .dataTables_wrapper .dataTable .wizard > .actions a, .wizard > .actions .dataTables_wrapper .dataTable a {
		padding: 0.6rem 0.7rem!important;
		border-radius: 100%;
		vertical-align: top;
	}
    .btn-kotak {
        border-radius: 5px !important;
    }
    .mb-show{
    display: none;
}
.mb-hide{
    display: block;
}
.mb-show-flex{
    display: none;
}
.mb-hide-flex{
    display: flex;
}
.haritanggal{
    width: 250px;
}

@media (max-width: 1320px) {
    .labura-modal-content {
        margin: auto;
        width: 90%;
    }

    .card-modal {
        height: 650px;
    }

}

@media (max-width: 1000px) {
    .labura-modal-content {
        margin: auto;
        width: 90%;
    }

    .card-modal {
        height: 600px;
    }

}

@media (max-width: 780px) {
    .mb-strong{
        font-weight: 700;
    }

    .mb-show{
        display: block;
    }
    .mb-hide{
        display: none;
    }
    .mb-show-flex{
        display: flex;
    }
    .mb-hide-flex{
        display: none;
    }
    .haritanggal{
        width: 125px;
    }
    .main-panel {
        background: #ffffff;
    }
}
@media (max-width: 700px) {
    .labura-modal-content {
        margin: auto;
        width: 98%;
    }

    .card-modal {
        height: 600px;
    }

}

</style>
</head>

<body>
  <div class="container-scroller">
    <div class="horizontal-menu">
      <nav class="navbar top-navbar col-lg-12 col-12 p-0">
        <div class="container">
          <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
            <a class="navbar-brand brand-logo" href="">
              <img src="assets/assets_login/img/favicon.ico" alt="logo"/>
              <span>LAYANAN ABSENSI</span>
            </a>
            <a class="navbar-brand brand-logo-mini" href="">
              <img src="assets/assets_login/img/favicon.ico" alt="logo"/>
              <span>LAYANAN ABSENSI</span>
            </a>
          </div>
          <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
            <ul class="navbar-nav mr-lg-2">
            </ul>
            <ul class="navbar-nav navbar-nav-right">
              <li class="nav-item nav-profile dropdown">
                <a class="nav-link" href="#" data-toggle="dropdown" id="profileDropdown">
                    <h3 class="ti-user" style="margin-top: 5px; font-size: 25px;"></h3>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                  <a class="dropdown-item" href="javascript:;" style="background: #eaeaea;border-top: 5px solid #558B2F;cursor: default">
                    <i class="ti-user text-primary" style="color: #fff; font-weight: 700"></i>
                    <h4><?=auth()->nama;?><br>
                    <small><?=auth()->roles[0];?></small></h4>
                  </a>
                  <a class="dropdown-item" href="<?=base_url('auth/pengaturanakun');?>">
                    <i class="ti-settings text-primary"></i>
                    Pengaturan Akun
                  </a>
                  <a class="dropdown-item" href="<?=base_url('auth/logout');?>">
                    <i class="ti-power-off text-primary"></i>
                    Logout
                  </a>
                </div>
              </li>
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="horizontal-menu-toggle">
              <span class="ti-menu"></span>
            </button>
          </div>
        </div>
      </nav>
      <nav class="bottom-navbar">
        <div class="container">
          <ul class="nav page-navigation" style="justify-content:inherit">
            <li class="nav-item">
                <a class="nav-link" href="<?=base_url()?>">
                <i class="ti-home menu-icon"></i>
                <span class="menu-title">Home</span>
                </a>
            </li>
            <?php if(in_array('Admin',auth()->roles)): ?>
            <li class="nav-item">
                <a class="nav-link" href="#">
                <i class="ti-layers menu-icon"></i>
                <span class="menu-title">OPD dan Unit Kerja &nbsp; <i class="ti-angle-down"></i></span>
                <i class="menu-icon"></i>
                </a>
                <div class="submenu">
                    <ul class="submenu-item">
                        <li class="nav-item"><a class="nav-link" href="<?=base_url('opd')?>">OPD</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?=base_url('unitkerja')?>">Unit Kerja</a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?=base_url('pegawai')?>">
                <i class="ti-user menu-icon"></i>
                <span class="menu-title">Kepegawaian</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                <i class="ti-pie-chart menu-icon"></i>
                <span class="menu-title">Absensi dan Izin Kerja &nbsp; <i class="ti-angle-down"></i></span>
                <i class="menu-icon"></i>
                </a>
                <div class="submenu">
                    <ul class="submenu-item">
                        <li class="nav-item"><a class="nav-link" href="<?=base_url('absensi')?>">Absensi</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?=base_url('izinkerja/dataizinkerja')?>">Izin Kerja</a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                <i class="ti-settings menu-icon"></i>
                <span class="menu-title">Pengaturan Absensi &nbsp; <i class="ti-angle-down"></i></span>
                <i class="menu-icon"></i>
                </a>
                <div class="submenu">
                    <ul class="submenu-item">
                        <li class="nav-item"><a class="nav-link" href="<?=base_url('pengaturan/kordinat')?>">Pengaturan Kordinat Unit Kerja</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?=base_url('pengaturan/kordinattambahan')?>">Pengaturan Kordinat Tambahan</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?=base_url('upacara/upacara')?>">Upacara dan Libur</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?=base_url('pengaturan/jamkerja')?>">Jam Kerja Default</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?=base_url('pengaturan/jamkerjanew')?>">Jam Kerja Lainnya</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?=base_url('pengaturan/aturjamkerjapegawainew')?>">Atur Jam Kerja Pegawai</a></li>
                    </ul>
                </div>
            </li>
            <?php endif ?>
          </ul>
        </div>
      </nav>
    </div>

    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <div class="main-panel">

        <?php $this->load->view($page);?>

        <footer class="container footer">
          <div class="w-100 clearfix">
            <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © <?=date("Y");?> <a href="<?=base_url()?>" target="_blank">LAYANAN ABSENSI</a>. All rights reserved.</span>
            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="ti-heart text-danger ml-1"></i></span>
          </div>
        </footer>

        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->




</body>

</html>
