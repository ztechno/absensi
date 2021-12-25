<div class="content-wrapper">
    <!-- Page Heading -->
    <div class="card">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <?=$this->session->flashdata('pesan');?>
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Bulan</div>
                    <?php
                        $bulan   = date("m-Y");
                    ?>
                    <div class="col-md-10">
                        <div class="input-group">
                            <input id="bulan" name="bulan" type="text" class="form-control" autocomplete="off" value="<?= isset($_GET['bulan']) ? $_GET['bulan'] : $bulan ?>" />
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Jenis Pegawai</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="jenis_pegawai" name="jenis_pegawai" class="form-control select2">
                                <option value="">Semua Jenis Pegawai</option>
                                <option value="pegawai">PNS</option>
                                <option value="tks">TKS</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Unit Kerja</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="skpd_id" name="skpd_id" class="form-control select2">
                                <option value="">Semua Unit Kerja</option>
                                <?php foreach ($skpds as $s) {
									$s['skpd_id'] = isset($s['skpd_id']) ? $s['skpd_id'] : $s['id_skpd'];									
								?>
                                    <option value="<?= $s['skpd_id']; ?>"><?= $s['nama_skpd']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Status</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="status" name="status" class="form-control select2">
                                <option value="">Semua Status</option>
                                <option value="Menunggu">Menunggu</option>
                                <option value="Disetujui">Disetujui</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12"><button id="btnTampilkan" class="btn btn-outline-primary btn-sm">Tampilkan</button></div>
                </div>

            </li>
            <li class="list-group-item">
                <table class="table table-striped table-hover" id="tableAbsensiManual" cellpadding="8">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Unit Kerja</th>
                            <th>Keterangan</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Foto</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </li>

        </ul>
    </div>
</div>

<?php $this->view('template/javascript'); ?>
<script type="text/javascript">
    $('#bulan').datepicker({
        format: "mm-yyyy",
        viewMode: "months", 
        minViewMode: "months",
        autoclose: true
    });
    
    getFilter();

    $('#btnTampilkan').click(function(){
        getFilter();
    })
    
    function getFilter() {
        var bulan            = $("#bulan").val();
        var skpd_id          = $("#skpd_id").val();
        var jenis_pegawai    = $("#jenis_pegawai").val();
        var status           = $("#status").val();
        $('#tableAbsensiManual_wrapper .row .col-sm-12').addClass('table-responsive');
        $('#tableAbsensiManual').DataTable().destroy();
        $('#tableAbsensiManual').DataTable({
            "autoWidth": false,
            "ordering": false,
            "processing": true,
            "serverSide": true,
            "pageLength": 25,

            "ajax": {
                "url": "<?php echo site_url('absenluarlokasiforadmin/getDataAbsenManualNew?token=' . $_GET['token']) ?>",
                "type": "POST",
                "data": {
                    "bulan"         : bulan,
                    "skpd_id"       : skpd_id,
                    "jenis_pegawai" : jenis_pegawai,
                    "status"        : status
                }
            },
            "fnInitComplete": function(oSettings, json) {
                $('#tableAbsensiManual_wrapper .row .col-sm-12').addClass('table-responsive');
            },
            columnDefs: [
                {
                    targets:[7, 8],
                    className: "text-right"
                },
                {orderable: false, targets: 0 }
            ]

        });
    }

    function toast(toast, color){
        $.toast({ 
          text : toast, 
          showHideTransition : 'slide',  // It can be plain, fade or slide
          bgColor : color,              // Background color for toast
          textColor : '#fff',            // text color
          allowToastClose : false,       // Show the close button or not
          hideAfter : 5000,              // `false` to make it sticky or time in miliseconds to hide after
          stack : 5,                     // `fakse` to show one stack at a time count showing the number of toasts that can be shown at once
          textAlign : 'left',            // Alignment of text i.e. left, right, center
          position : 'top-center'       // bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values to position the toast on page
        })
    }

    function hapusabsenluarlokasi(absen_id){
        var searchField = $("[type=search][aria-controls=tableAbsensiManual]");
        var searchVal   = searchField.val();
        if(!confirm('Apakah anda yakin untuk menghapus?')) return false;
        var url     = "<?=base_url('absenluarlokasiforadmin/hapusabsenluarlokasi');?>";
        var token   = "<?=$_GET['token'];?>";
        $.ajax({
            url     : url+"/"+absen_id+"?token="+token,
            type    : "get",
            success : function(res){
                res = $.parseJSON(res);
                toast(res.pesan, res.warna);
                // if(searchVal){
                    $('#tableAbsensiManual').DataTable().search( searchVal ).draw();
                // }else{
                //     $('#tableAbsensiManual').DataTable().draw();
                // }
            }
        });
    }    
</script>