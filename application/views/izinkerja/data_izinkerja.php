<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <a href="<?= base_url("izinkerja/addizin") ?>" class="btn btn-sm btn-primary"><em class="ti-plus"></em> 
                   Buat Izin Kerja
                </a>

            </li>
            <li class="list-group-item">
                <?=$this->session->flashdata('pesan');?>

                <form method="get">
                    <div class="row mb-3">
                        <div class="col-md-2 pt-2">Bulan</div>
                        <div class="col-md-10">
                            <div class="input-group">
                                <input id="bulan" name="bulan" type="text" class="form-control" autocomplete="off" value="<?= isset($_GET['bulan']) ? $_GET['bulan'] : date("m-Y") ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12"><button class="btn btn-outline-primary btn-sm">Tampilkan</button></div>
                    </div>
                </form>

            </li>
            <li class="list-group-item mb-hide">
                <div class="table-responsive">
                    <table class="table table-striped" id="tableIzinKerja" width="100%" cellspacing="0">
    
                        <thead>
                            <tr>
                                <th>Dari Tanggal</th>
                                <th>Sampai Tanggal</th>
                                <th>Nama</th>
                                <th>Nama Unit Kerja</th>
                                <th>Jenis Izin</th>
                                <th>Berkas</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </li>
        </ul>
        
    </div>
</div>
<?php $this->view('template/javascript'); ?>

<script type="text/javascript">
    $(document).ready(function() {
        getFilter();

        $('#bulan').datepicker({
            format: "mm-yyyy",
            viewMode: "months", 
            minViewMode: "months",
            autoclose: true
        });

        function getFilter() {
            $('#tableIzinKerja').DataTable().destroy();
            $('#tableIzinKerja').DataTable({
                autoWidth : false,
                ordering : false,
                ajax : {
                    "url": "<?php echo site_url('izinkerja/getDataIzinKerja') ?>",
                    "type": "POST",
                    "data": {
                        "bulan" : "<?=isset($_GET['bulan']) ? $_GET['bulan'] : date("m-Y");?>",

                    }
                },
                "fnInitComplete": function(oSettings, json) {
                    $('#tableIzinKerja_wrapper .row .col-sm-12').addClass('table-responsive');
                },
                columnDefs: [
                    {orderable: false, targets: 0 }
                ]
            });
        }


    });
</script>


