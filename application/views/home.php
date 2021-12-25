<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3>Layanan Absensi</h3>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
               <div class="row">
                   <div class="col-md-12">
                       Hai <strong><?=$this->session->userdata('nama');?></strong>. Selamat Datang di Layanan Absensi
                   </div>
               </div>
            </li>
        </ul>
    </div>

    <?=$this->session->flashdata('pesan');?>

</div>
<?php $this->view('template/javascript');?>
