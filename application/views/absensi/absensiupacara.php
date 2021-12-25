<div class="content-wrapper">

    <div class="card" style="margin-top:20px; margin-bottom:20px">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <?= $this->session->flashdata('pesan'); ?>
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Nama Upacara</div>
                    <div class="col-md-10">
                        <select id="upacara_id" name="upacara_id" class="form-control select2">
                            <option value="">Pilih Upacara</option>
                            <?php foreach ($upacaras as $key => $upacara): 
                                $tanggal    = strtotime($upacara->tanggal);
                                $tgl        = (integer) date('m', $tanggal);
                                $bln        = $bulan[$tgl];
                            ?>
                                <option value="<?=$upacara->id;?>"><?=$upacara->nama_hari;?> - <?=date("d", $tanggal)." ".$bln." ".date("Y", $tanggal);?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <?php
                    $akses = [1,2];
                    if(in_array($this->session->userdata('role_id'), $akses)):
                ?>
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">OPD</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="opd_id" name="opd_id" class="form-control select2">
                                <option value="">Semua OPD</option>
                                <?php foreach ($skpd as $o) :
                                    $nama_skpd = explode(" ", $o['nama_skpd']);
                                    $o['opd_id'] = isset($o['id_skpd']) ? $o['id_skpd'] : $o['opd_id'];
                                    if(
                                        $o['nama_skpd']=='Satuan Polisi Pamong Praja' || 
                                        $nama_skpd[0]=='Dinas' ||
                                        $nama_skpd[0]=='Badan' ||
                                        $nama_skpd[0]=='Sekretariat' ||
                                        $nama_skpd[0]=='Kecamatan' ||
                                        $nama_skpd[0]=='Inspektorat'
                                    ){}else{continue;}
                                ?>
                                    <option value="<?= $o['id_skpd']; ?>"><?= $o['nama_skpd']; ?></option>
                                <?php endforeach; ?>

                            </select>
                        </div>
                    </div>
                </div>
                <?php endif;?>

                <div class="row">
                    <div class="col-md-12"><button id="btnFilter" class="btn btn-outline-primary btn-sm" disabled>Tampilkan</button></div>
                </div>

            </li>
            <li class="list-group-item">
                <table class="table table-striped table-hover" id="tableOPD" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="haritanggal">Nama Unit Kerja</th>
                            <th width="10">Cetak</th>
                        </tr>
                    </thead>
                </table>
            </li>
        </ul>

    </div>
</div>

<?php $this->view('template/javascript'); ?>

<script type="text/javascript">
    $(document).ready(function() {

        function validateForm(){
            var upacara_id      = $("#upacara_id").val();
            if(upacara_id==""){
                $('#btnFilter').prop('disabled', true);
                return false;
            }

            $('#btnFilter').removeAttr('disabled');
            return true;
        }
        
        $('#upacara_id').change(function(){
            validateForm();
        });
        $('#opd_id').change(function(){
            validateForm();
        });
        $("#btnFilter").click(function() {
            filter();
        });

        function filter() {
            var upacara_id      = $("#upacara_id").val();
            var opd_id          = $("#opd_id").val();

            $('#tableOPD').DataTable().destroy();
            $('#tableOPD').DataTable({
                processing  : true,
                pageLength  : 100,
                ordering    : false,
                paging      : false,
                ajax: {
                    url     : "<?php echo base_url('absensi/getCetakAbsensiUpacara?token=' . $_GET['token']) ?>",
                    type    : "POST",
                    data    : {
                        upacara_id  : upacara_id,
                        opd_id      : opd_id,
                    }

                },
            });
        }

    });
</script>
