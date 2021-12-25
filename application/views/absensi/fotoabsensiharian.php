<div class="content-wrapper">

    <div class="card" style="margin-top:20px; margin-bottom:20px">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <div class="alert alert-danger alert-dismissible fade hide" id="alertFoto" role="alert">
                    <span id="textAlert"></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="row mb-3">
                    <?php $tanggal   = date("d-m-Y");?>
                    <div class="col-md-2 pt-2">Tanggal</div>
                    <div class="col-md-10">
                        <div class="input-group">
                            <input id="tanggal" name="tanggal" type="text" class="form-control" autocomplete="off" value="<?= $tanggal ?>" />
                        </div>
                    </div>
                </div>
                <?php
                $akses = [1,2,7];
                if(in_array($this->session->userdata('role_id'), $akses)):
                ?>
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Unit Kerja</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="skpd_id" name="skpd_id" class="form-control select2">
                                <option value="">Semua Unit Kerja</option>
                                <?php foreach ($skpd as $s) { ?>
                                    <option value="<?= $s['id_skpd']; ?>"><?= $s['nama_skpd']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endif;?>
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
    
                <div class="row">
                    <div class="col-md-12"><button id="btnFilter" class="btn btn-outline-primary btn-sm">Tampilkan</button></div>
                </div>

            </li>
            <li class="list-group-item">
                <div id="loading-animation" class="text-center p-3">
                    <img src="<?= base_url('assets/img/icon/loading.gif') ?>" width="100" />
                    <br>
                    <br>
                    <h3>Sedang memuat, tunggu sebentar!</h3>
                </div>
                <div class="row" id="body-foto">

                </div>
            </li>
        </ul>

    </div>
</div>

<?php $this->view('template/javascript'); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
        $('#loading-animation').hide();
        function validateForm(){
            var tanggal          = $("#tanggal").val();
            if(tanggal==""){
                $('#btnFilter').prop('disabled', true);
                return false;
            }

            $('#btnFilter').removeAttr('disabled');
            return true;
        }
        
        $('#jenis_pegawai').change(function(){
            validateForm();
        });
        $('#skpd_id').change(function(){
            validateForm();
        });

        $("#btnFilter").click(function() {
            filter();
        });

        function filter() {
            var tanggal          = $("#tanggal").val();
            var skpd_id          = $("#skpd_id").val();
            var jenis_pegawai    = $("#jenis_pegawai").val();
            $('#loading-animation').show();
            $.ajax({
                url     : "<?php echo base_url('absensi/getFotoAbsensiHarianPegawai?token=' . $_GET['token']) ?>",
                type    : "POST",
                data    : {
                    tanggal         : tanggal,
                    skpd_id         : skpd_id,
                    jenis_pegawai   : jenis_pegawai
                },
                success: function(res){
                    $('#body-foto').html(res);
                    $('#loading-animation').hide();
                }
            });
        }

    });

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

    function sendSMS(id){
        if(!confirm('Hapus absen ini dan kirim pesan peringatan ?')) return false;

        $.ajax({
            url: "<?=base_url('absensi/pesan/');?>"+id+"<?='?token='.$_GET['token'];?>",
            type: "get",
            success:function(data){
                data = $.parseJSON(data);
                $("#alertFoto").removeClass('alert-danger');
                $("#alertFoto").removeClass('alert-success');
                $("#alertFoto").addClass('alert-'+data.alert);
                $("#alertFoto").fadeTo(30000, 500).slideUp(300);
                $('#textAlert').html("<strong>"+data.message+"</strong>");
                toast(data.message, data.color);
            }

        });
    }
    function sendSMSdanNonaktifkan(id){
        if(!confirm('Hapus absen ini dan kirim pesan peringatan, apakah anda yakin untuk non-aktifkan akun ini?')) return false;

        $.ajax({
            url: "<?=base_url('absensi/pesan/');?>"+id+"/nonaktifkan<?='?token='.$_GET['token'];?>",
            type: "get",
            success:function(data){
                data = $.parseJSON(data);
                $("#alertFoto").removeClass('alert-danger');
                $("#alertFoto").removeClass('alert-success');
                $("#alertFoto").addClass('alert-'+data.alert);
                $("#alertFoto").fadeTo(30000, 500).slideUp(300);
                $('#textAlert').html("<strong>"+data.message+"</strong>");
                toast(data.message, data.color);
            }

        });
    }

</script>
