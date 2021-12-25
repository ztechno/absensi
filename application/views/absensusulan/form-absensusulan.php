<div class="content-wrapper">

    <div class="card" style="margin-top:20px; margin-bottom:20px">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <?= $this->session->flashdata('pesan'); ?>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group mb-3">
                        <label for="skpd_id">Unit Kerja <span class="text-danger">*</span></label></label>
                        <select id="skpd_id" name="skpd_id" class="form-control select2">
                            <option value="">Pilih Unit Kerja</option>
                            <?php foreach ($skpd as $s) { ?>
                                <option value="<?= $s['id_skpd']."_".$s['nama_skpd']; ?>"><?= $s['nama_skpd']; ?></option>
                            <?php } ?>
                        </select>
                        <?= form_error('skpd_id', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
            
                    <div class="form-group mb-3">
                        <label for="jenis_pegawai">Jenis Pegawai <span class="text-danger">*</span></label></label>
                        <select id="jenis_pegawai" name="jenis_pegawai" class="form-control select2">
                            <option value="">Pilih Jenis Pegawai</option>
                            <option value="pegawai">PNS</option>
                            <option value="tks">TKS</option>
                        </select>
                        <?= form_error('jenis_pegawai', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                    <div class="form-group mb-3">
                        <label for="pegawai_id">Pegawai <span class="text-danger">*</span></label></label>
                        <select id="pegawai_id" name="pegawai" class="form-control select2">
                            <option value="0">Pilih Pegawai</option>
                        </select>
                        <?= form_error('pegawai', '<small class="text-danger pl-2">', '</small>'); ?>
    
                    </div>

                    <div class="form-group mb-3">
                        <label for="jenis_absen">Jenis Absen <span class="text-danger">*</span></label></label>
                        <select id="jenis_absen" name="jenis_absen" class="form-control select2">
                            <option value="">Pilih Jenis Absen</option>
                            <option value="Absen Masuk">Absen Masuk</option>
                            <option value="Absen Istirahat">Absen Istirahat</option>
                            <option value="Absen Selesai Istirahat">Absen Selesai Istirahat</option>
                            <option value="Absen Pulang">Absen Pulang</option>
                            <option value="Absen Upacara">Absen Upacara</option>
                        </select>
                        <?= form_error('jenis_absen', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>


                    <div class="form-group mb-3">
                        <label for="tanggal">Tanggal <span class="text-danger">*</span></label></label>
                        <input type="text" id="tanggal" name="tanggal" type="text" class="form-control" autocomplete="off" />
                        <?= form_error('tanggal', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="pendukung">Pendukung <span class="text-danger">*</span></label></label><br>
                        <input type="file" id="pendukung" name="pendukung" type="text"  /><br>
                        <?= form_error('pendukung', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>
                    <br>
                    <div class="form-group  mb-3">
                        <button id="btnSubmit" class="btn btn-outline-primary btn-sm" disabled>Selesai</button>
                    </div>
                    
                </form>

            </li>
        </ul>

    </div>
</div>

<?php $this->view('template/javascript'); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#tanggal').datepicker({
            format: "dd-mm-yyyy",
            todayHighlight: true,
            autoclose: true
        });

        $("#skpd_id").change(function() {
            if($("#skpd_id").val()=="" || $("#jenis_pegawai").val()==""){
                return;
            }
            validForm();
            getPegawai();
        });

        $("#jenis_pegawai").change(function() {
            if($("#skpd_id").val()=="" || $("#jenis_pegawai").val()==""){
                alert('Pastikan field SKPD dan Jenis Pegawai sudah terpilih!');
                return;
            }
            validForm();
            getPegawai();
        });
        
        $('#pegawai_id').change(function(){
            validForm();
        });
        
        function getPegawai(){

            $('#pegawai_id').find('option').remove().end();
            var skpd_id          = $("#skpd_id").val();
            var jenis_pegawai    = $("#jenis_pegawai").val();
            $.ajax({
                type: "post",
                url: jenis_pegawai=="pegawai" ? "<?= base_url() . 'absensusulan/selectOptionPegawaiBySkpd?is_single=true&token=' . $_GET['token']; ?>" : "<?= base_url() . 'absensusulan/selectOptionTksBySkpd?token=' . $_GET['token']; ?>",
                data: {
                    "skpd_id"       : skpd_id,
                },
                    
                success: function(data) {
                    $("#pegawai_id").html(data).change();
                }
            });

        }

        function validForm(){
            var skpd_id          = $("#skpd_id").val();
            var pegawai_id       = $("#pegawai_id").val();
            var jenis_pegawai    = $("#jenis_pegawai").val();

            if(skpd_id=="" || pegawai_id=="" || jenis_pegawai==""){
                $('#btnSubmit').prop("disabled", true);
            }else{
                $('#btnSubmit').removeAttr("disabled");
            }
            
        }

    });
</script>
