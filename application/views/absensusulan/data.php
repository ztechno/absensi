<div class="content-wrapper">
    <!-- Page Heading -->
    <div class="card">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <a href="<?=base_url('absensusulan/buatbaru?token='.$_GET['token']);?>" class="btn btn-outline-primary"><em class="ti-plus"></em> Buat Baru</a>
            </li>
            <li class="list-group-item">
                <?=$this->session->flashdata('pesan');?>
                <div class="form-group mb-3">
                    <label for="tanggal">Bulan <span class="text-danger">*</span></label></label>
                    <input type="text" id="bulan" name="bulan" type="text" class="form-control" autocomplete="off" />
                </div>

                <div class="form-group mb-3">
                    <label for="skpd_id">Unit Kerja <span class="text-danger">*</span></label></label>
                    <select id="skpd_id" name="skpd_id" class="form-control select2">
                        <option value="">Pilih Unit Kerja</option>
                        <?php foreach ($skpd as $s) { ?>
                            <option value="<?= $s['id_skpd']."_".$s['nama_skpd']; ?>"><?= $s['nama_skpd']; ?></option>
                        <?php } ?>
                    </select>
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
                    <label for="jenis_absen">Jenis Absen <span class="text-danger">*</span></label></label>
                    <select id="jenis_absen" name="jenis_absen" class="form-control select2">
                        <option value="">Pilih Jenis Absen</option>
                        <option value="Absen Masuk">Absen Masuk</option>
                        <option value="Absen Istirahat">Absen Istirahat</option>
                        <option value="Absen Selesai Istirahat">Absen Selesai Istirahat</option>
                        <option value="Absen Pulang">Absen Pulang</option>
                        <option value="Absen Upacara">Absen Upacara</option>
                    </select>
                </div>



                <div class="row">
                    <div class="col-md-12"><button id="btnFilter" class="btn btn-outline-primary btn-sm">Tampilkan</button></div>
                </div>

            </li>
            <li class="list-group-item">
                <div class="accordion mt-3" id="absenSusulan" role="tablist">
                    <table class="table" style="width:100%" id="tableAbsenSusulan" cellpadding="8">
                        <thead>
                            <tr>
                                <th>Nama Pegawai/TKS</th>
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
        $('#bulan').datepicker({
            format: "mm-yyyy",
            viewMode: "months", 
            minViewMode: "months",
            autoclose: true
        });

        $("#skpd_id").change(function() {
            if($("#skpd_id").val()=="" || $("#jenis_pegawai").val()==""){
                return;
            }
            getPegawai();
        });

        $("#jenis_pegawai").change(function() {
            getPegawai();
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

        getFilter();
        $('#btnFilter').click(function(){
            getFilter();
        })
        function getFilter() {
            $('#tableAbsenSusulan').DataTable().destroy();
            $('#tableAbsenSusulan').DataTable({
                "serverSide": true,
                "processing": true,
                "ordering":false,
                "ajax": {
                    "url": "<?php echo site_url('absensusulan/getDataAbsenSusulan?token=' . $_GET['token']) ?>",
                    "type": "POST",
                    "data": {
                        "bulan"         : $('#bulan').val(),
                        "skpd_id"       : $('#skpd_id').val(),
                        "jenis_pegawai" : $('#jenis_pegawai').val(),
                        "jenis_absen"   : $('#jenis_absen').val(),
                    }
                },

            });
        }
    
    });
</script>
