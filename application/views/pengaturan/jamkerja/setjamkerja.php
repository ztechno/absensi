<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
          <h3><?=$title?><span id="subtitle"></span></h3>
        </div>
        <ul class="list-group list-group-flush">
            <form method="post" id="formJamKerja">
                <li class="list-group-item">
                    <div class="form-group">
                        <label>Nama Jam Kerja</label>
                        <input name="nama_jam_kerja" type="text" class="form-control" value="<?=isset($jamkerja['nama_jam_kerja']) ? $jamkerja['nama_jam_kerja'] : null;?>" placeholder="Jam Kerja" />
                        <?= form_error('nama_jam_kerja', '<small class="text-danger pl-2">', '</small>'); ?>
                    </div>

                    <div class="form-group mb-0">
                        <label>Khusus OPD ?</label>
                        <select id="opd_id" name="opd_id" class="form-control select2">
                            <option value="">Semua OPD</option>
                            <?php foreach ($skpds as $opd) { 
                                    $nama_skpd      = explode(" ", $opd['nama_opd']);
                                    if(!(
                                        $opd['nama_opd']=='Satuan Polisi Pamong Praja' ||
                                        $opd['nama_opd']=='Rumah Sakit Umum Daerah' ||
                                        $nama_skpd[0]=='Dinas' ||
                                        $nama_skpd[0]=='Badan' ||
                                        $nama_skpd[0]=='Sekretariat' ||
                                        $nama_skpd[0]=='Kecamatan' ||
                                        $nama_skpd[0]=='Inspektorat'
                                    )){continue;}

                            ?>
                            <option value="<?= $opd['id']."_".$opd['nama_opd']; ?>" <?=isset($jamkerja['opd_id']) && $jamkerja['opd_id']==$opd['id'] ? "selected" : null;?>><?= $opd['nama_opd']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                </li>
                <li class="list-group-item">
                    <div id="formJam">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>Jam Awal Masuk</label>
                                <input name="jam_awal_masuk" type="time" class="form-control" autocomplete="off" value="<?=isset($jamkerja['jam_awal_masuk']) ? $jamkerja['jam_awal_masuk'] : null;?>" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Jam Akhir Masuk</label>
                                <input name="jam_akhir_masuk" type="time" class="form-control" autocomplete="off" value="<?=isset($jamkerja['jam_akhir_masuk']) ? $jamkerja['jam_akhir_masuk'] : null;?>" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Jam Awal Pulang</label>
                                <input name="jam_awal_pulang" type="time" class="form-control" autocomplete="off" value="<?=isset($jamkerja['jam_awal_pulang']) ? $jamkerja['jam_awal_pulang'] : null;?>" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Jam Akhir Pulang</label>
                                <input name="jam_akhir_pulang" type="time" class="form-control" autocomplete="off" value="<?=isset($jamkerja['jam_akhir_pulang']) ? $jamkerja['jam_akhir_pulang'] : null;?>" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Jam Awal Istirathat</label>
                                <input name="jam_awal_istirahat" type="time" class="form-control" autocomplete="off" value="<?=isset($jamkerja['jam_awal_istirahat']) ? $jamkerja['jam_awal_istirahat'] : null;?>" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Jam Akhir Istirahat</label>
                                <input name="jam_akhir_istirahat" type="time" class="form-control" autocomplete="off" value="<?=isset($jamkerja['jam_akhir_istirahat']) ? $jamkerja['jam_akhir_istirahat'] : null;?>" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Jam Awal Selesai Istirahat</label>
                                <input name="jam_awal_selesai_istirahat" type="time" class="form-control" autocomplete="off" value="<?=isset($jamkerja['jam_awal_selesai_istirahat']) ? $jamkerja['jam_awal_selesai_istirahat'] : null;?>" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Jam Akhir Selesai Istirahat</label>
                                <input name="jam_akhir_selesai_istirahat" type="time" class="form-control" autocomplete="off" value="<?=isset($jamkerja['jam_akhir_selesai_istirahat']) ? $jamkerja['jam_akhir_selesai_istirahat'] : null;?>" />
                            </div>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <a href="javascript:;" onclick="history.back()" class="btn btn-sm btn-danger"><em class="ti-arrow-left"></em> Kembali</a>
                    <button type="submit" id="btnSubmit" class="btn btn-sm btn-primary"><em class="ti-save"></em> Selesai</button>
                </li>
            </form>
        </ul>
    </div>
</div>
<!-- End of Main Content -->

<?php $this->view('template/javascript'); ?>

<script>
    $(document).ready(function(){
        $('#formJamKerja').submit(function(){
            $('#btnSubmit').html('Tunggu sebentar . .');
            $('#btnSubmit').prop('disabled', true);
        });
    });
</script>
