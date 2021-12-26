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
                                <label for="">URL</label>
                                <input type="url" class="form-control" placeholder="Masukan URL" name="slide[url]">
                                <?= form_error('slide[url]', '<small class="text-danger pl-2">', '</small>'); ?>
                            </div>
                            <div class="form-group">
                                <label for="">Foto</label>
                                <div class="d-flex">
                                    <button type="button" class="btn btn-warning btn-dsb" onclick="document.querySelector('#foto_slide').click()"><i class="ti ti-upload"></i> Upload Foto</button>
                                </div>
                                <img src="" alt="" width="150px" id="slide_img">
                                <?= form_error('slide[foto]', '<small class="text-danger pl-2">', '</small>'); ?>
                                <input type="file" class="form-control" id="foto_slide" name="slide[foto]" style="opacity:0;height:0px;overflow:hidden;padding:0;margin:0;" onchange="loadFoto(this)">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-success btn-sm"><i class="ti ti-save "></i> Submit</button>
                                <a href="<?=base_url('slideshow')?>" class="btn btn-danger btn-sm"><i class="ti ti-arrow-left"></i> Kembali</a>
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
function loadFoto(f)
{
    var file = f.files[0]
    var emp_img = document.querySelector('#slide_img')
    var reader = new FileReader();
    reader.onload = function (e) {
        emp_img.src = e.target.result
    }
    reader.readAsDataURL(file);
}
</script>