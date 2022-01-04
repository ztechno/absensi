<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <div class="row">
                    <div class="col-12">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="">OPD</label>
                                <select name="import[opd_id]" class="form-control" onchange="loadAtasan(this.value,false,false)">
                                    <option value="">- Pilih -</option>
                                    <?php foreach($opds as $opd): ?>
                                    <option value="<?=$opd->id?>" <?=isset($pegawai->opd_id) && $pegawai->opd_id==$opd->id ? "selected" : null;?>><?=$opd->nama_opd?></option>
                                    <?php endforeach ?>
                                </select>
                                <?= form_error('import[opd_id]', '<small class="text-danger pl-2">', '</small>'); ?>
                            </div>
                            <div class="form-group">
                                <label for="">Atasan</label>
                                <select name="import[pegawai_id]" id="atasan" class="form-control">
                                    <option value="">- Pilih -</option>
                                </select>
                                <?= form_error('import[pegawai_id]', '<small class="text-danger pl-2">', '</small>'); ?>
                            </div>
                            <div class="form-group">
                                <label for="">File</label>
                                <div class="d-flex">
                                    <button type="button" class="btn btn-warning btn-dsb" onclick="document.querySelector('#file_import').click()"><i class="ti ti-upload"></i> Upload File</button>
                                </div>
                                <?= form_error('import[file]', '<small class="text-danger pl-2">', '</small>'); ?>
                                <input type="file" class="form-control" id="file_import" name="import[file]" style="opacity:0;height:0px;overflow:hidden;padding:0;margin:0;">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-success btn-sm"><i class="ti ti-save "></i> Submit</button>
                                <a href="<?=base_url('pegawai')?>" class="btn btn-danger btn-sm"><i class="ti ti-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>

<?php $this->view('template/javascript'); ?>
<script>
function loadAtasan(opd_id, selected_id = false, not_id = false)
{
    fetch('<?=base_url('pegawai/getPegawaiByOpd')?>/'+opd_id)
    .then(res => res.json())
    .then(res => {
        var data = res.data
        var opt = '<option value="">- Pilih -</option>'
        for(i=0;i<data.length;i++)
        {
            if(not_id && data[i].id == not_id) continue
            var selected = selected_id && data[i].id == selected_id
            opt += '<option value="'+data[i].id+'" '+(selected?'selected=""':'')+'>'+data[i].nama+'</option>'
        }
        document.querySelector('#atasan').innerHTML = opt
    })
}
</script>
