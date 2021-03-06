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
                            <input type="hidden" name="detection">
                            <div class="form-group">
                                <label for="">NIP</label>
                                <input type="tel" class="form-control" placeholder="Masukan NIP" value="<?=isset($pegawai->nip) ? $pegawai->nip : null;?>" name="pegawai[nip]">
                                <?= form_error('pegawai[nip]', '<small class="text-danger pl-2">', '</small>'); ?>
                            </div>
                            <div class="form-group">
                                <label for="">Nama</label>
                                <input type="text" class="form-control" placeholder="Masukan Nama" value="<?=isset($pegawai->nama) ? $pegawai->nama : null;?>" name="pegawai[nama]">
                                <?= form_error('pegawai[nama]', '<small class="text-danger pl-2">', '</small>'); ?>
                            </div>
                            <div class="form-group">
                                <label for="">Jabatan</label>
                                <input type="text" class="form-control" placeholder="Masukan Jabatan" value="<?=isset($pegawai->jabatan) ? $pegawai->jabatan : null;?>" name="pegawai[jabatan]">
                                <?= form_error('pegawai[jabatan]', '<small class="text-danger pl-2">', '</small>'); ?>
                            </div>
                            <div class="form-group">
                                <label for="">OPD</label>
                                <select name="pegawai[opd_id]" class="form-control" onchange="loadAtasan(this.value,false,false)">
                                    <option value="">- Pilih -</option>
                                    <?php foreach($opds as $opd): ?>
                                    <option value="<?=$opd->id?>" <?=isset($pegawai->opd_id) && $pegawai->opd_id==$opd->id ? "selected" : null;?>><?=$opd->nama_opd?></option>
                                    <?php endforeach ?>
                                </select>
                                <?= form_error('pegawai[jenis_pegawai]', '<small class="text-danger pl-2">', '</small>'); ?>
                            </div>
                            <div class="form-group">
                                <label for="">Atasan</label>
                                <select name="atasan[pegawai_id]" id="atasan" class="form-control">
                                    <option value="">- Pilih -</option>
                                </select>
                                <?= form_error('atasan[pegawai_id]', '<small class="text-danger pl-2">', '</small>'); ?>
                            </div>
                            <div class="form-group">
                                <label for="">Jenis Pegawai</label>
                                <select name="pegawai[kategori_pegawai]" class="form-control">
                                    <option value="pegawai" selected>Pegawai/PNS</option>
                                    <option value="honorer">Honorer/Non PNS</option>
                                </select>
                                <?= form_error('pegawai[kategori_pegawai]', '<small class="text-danger pl-2">', '</small>'); ?>
                            </div>
                            <div class="form-group">
                                <label for="">Apakah Pegawai Seorang Kepala OPD ?</label>
                                <br>
                                <input type="radio" name="pegawai[kepala]" value="1" id="" <?=isset($pegawai->kepala) && $pegawai->kepala==1 ? "checked" : null;?>> Ya &nbsp;
                                <input type="radio" name="pegawai[kepala]" value="0" id="" <?=(isset($pegawai->kepala) && $pegawai->kepala==0) || !isset($pegawai->kepala) ? "checked" : null;?>> Tidak
                            </div>
                            <div class="form-group">
                                <label for="">Apakah Pegawai Seorang CPNS ?</label>
                                <br>
                                <input type="radio" name="pegawai[cpns]" value="1" id="" <?=isset($pegawai->cpns) && $pegawai->cpns==1 ? "checked" : null;?>> Ya &nbsp;
                                <input type="radio" name="pegawai[cpns]" value="0" id="" <?=(isset($pegawai->cpns) && $pegawai->cpns==0) || !isset($pegawai->cpns) ? "checked" : null;?>> Tidak
                            </div>
                            <div class="form-group">
                                <label for="">Apakah Pegawai Seorang PLT ?</label>
                                <br>
                                <input type="radio" name="pegawai[plt]" value="1" id="" <?=isset($pegawai->plt) && $pegawai->plt==1 ? "checked" : null;?>> Ya &nbsp;
                                <input type="radio" name="pegawai[plt]" value="0" id="" <?=(isset($pegawai->plt) && $pegawai->plt==0) || !isset($pegawai->plt) ? "checked" : null;?>> Tidak
                            </div>
                            <div class="form-group">
                                <label for="">Apakah Pegawai Seorang Bendahara ?</label>
                                <br>
                                <input type="radio" name="pegawai[bendahara]" value="1" id="" <?=isset($pegawai->bendahara) && $pegawai->bendahara==1 ? "checked" : null;?>> Ya &nbsp;
                                <input type="radio" name="pegawai[bendahara]" value="0" id="" <?=(isset($pegawai->bendahara) && $pegawai->bendahara==0) || !isset($pegawai->bendahara) ? "checked" : null;?>> Tidak
                            </div>
                            <div class="form-group">
                                <label for="">Foto</label>
                                <div class="d-flex">
                                    <button type="button" disabled class="btn btn-warning btn-dsb" onclick="document.querySelector('#foto_pegawai').click()"><i class="ti ti-upload"></i> Upload Foto</button>
                                </div>
                                <img src="<?=isset($pegawai->foto)?$pegawai->foto:''?>" alt="" width="150px" id="pegawai_img">
                                <?= form_error('pegawai[foto]', '<small class="text-danger pl-2">', '</small>'); ?>
                                <input type="file" class="form-control" id="foto_pegawai" name="pegawai[foto]" style="opacity:0;height:0px;overflow:hidden;padding:0;margin:0;" onchange="loadFoto(this)">
                            </div>
                            <div class="form-group">
                                <label for="">Password</label>
                                <input type="password" class="form-control" placeholder="Masukan Password" name="user[password]">
                                <?= form_error('user[password]', '<small class="text-danger pl-2">', '</small>'); ?>
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
<script src="<?=base_url('js/face-api/face-api.js')?>"></script>
<script>
Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri('/js/models'),
    faceapi.nets.faceRecognitionNet.loadFromUri('/js/models'),
    faceapi.nets.faceLandmark68Net.loadFromUri('/js/models'),
    faceapi.nets.ssdMobilenetv1.loadFromUri('/js/models')
]).then(e => {
    document.querySelectorAll('.btn-dsb').forEach(el => el.disabled = false)
})
async function loadFoto(f)
{
    var file = f.files[0]
    var emp_img = document.querySelector('#pegawai_img')
    var reader = new FileReader();
    reader.onload = function (e) {
        emp_img.src = e.target.result
    }
    reader.readAsDataURL(file);

    var image = await faceapi.bufferToImage(file)
    const detection = await faceapi.detectSingleFace(image,new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor()
    console.log(detection)
    if(detection == undefined)
        alert('Wajah tidak terdeteksi pada foto')
    else
    {
        alert('Wajah terdeteksi pada foto')
        document.querySelector('input[name=detection]').value = JSON.stringify(detection)
    }
}
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
<?php if(isset($pegawai->username)): ?>
loadAtasan(<?=$pegawai->opd_id?>,<?=$pegawai->pegawai_atasan_id??0?>,<?=$pegawai->id?>);
<?php endif ?>
</script>
