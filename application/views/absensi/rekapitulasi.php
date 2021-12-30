<div class="content-wrapper">

    <div class="card" style="margin-top:20px; margin-bottom:20px">
        <div class="card-header">
            <span class="h5 mb-4 text-gray-800"><?= $title ?></span>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <?= $this->session->flashdata('pesan'); ?>
                <div class="row mb-3">
                    <?php $tanggal   = date("Y-m-d");?>
                    <div class="col-md-2 pt-2">Dari</div>
                    <div class="col-md-10">
                        <div class="input-group">
                            <input id="dari" name="dari" type="date" class="form-control" autocomplete="off" value="<?= $tanggal ?>" />
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Sampai</div>
                    <div class="col-md-10">
                        <div class="input-group">
                            <input id="sampai" name="sampai" type="date" class="form-control" autocomplete="off" value="<?= $tanggal ?>" />
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2 pt-2">Unit Kerja</div>
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <select id="skpd_id" name="skpd_id" class="form-control select2">
                                <option value="">Pilih Unit Kerja</option>
                                <?php 
                                    $opds = $this->db->order_by('nama_opd', 'asc')->get('tb_opd')->result();
                                    foreach ($opds as $s) {
								?>
                                    <option value="<?= $s->id; ?>"><?= $s->nama_opd; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
    
                <div class="row">
                    <div class="col-md-12"><button id="btnFilter" class="btn btn-outline-primary btn-sm" onclick="cetak()">Cetak</button></div>
                </div>

            </li>
        </ul>

    </div>
</div>

<?php $this->view('template/javascript'); ?>
<script>
function cetak()
{
    var from = document.querySelector('#dari')
    var to = document.querySelector('#sampai')
    var opd = document.querySelector('#skpd_id')
    location.href = '<?=base_url('absensi/cetak')?>/'+opd.value+'?from='+from.value+'&to='+to.value
}
</script>