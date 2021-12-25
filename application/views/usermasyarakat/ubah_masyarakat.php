<div class="content-wrapper">
    <div class="card">
          <div class="card-header">
              <h3><?= $title ?></h3>
            </div>
           
                
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <a href="<?=base_url('usermasyarakat?token='.$_GET['token']);?>" class="btn btn-sm btn-danger"><em class="ti-arrow-left"></em> Kembali</a>
            </li>
            <li class="list-group-item">
                
              <form class="forms-sample" action="" method="post">

                <div class="form-group col-12">
                  <label for="nik">NIK</label>
                  <input type="number" class="form-control" id="nik" placeholder="Masukan NIK" name="nik" value="<?= $masyarakat->nik;?>">
                  <?= form_error('nik', '<small class="text-danger pl-2">', '</small>'); ?>
                </div>
                
                <div class="form-group col-12">
                  <label for="nama">Nama</label>
                  <input type="text" class="form-control" id="nama" placeholder="Masukan Nama" name="nama" value="<?= $masyarakat->nama;?>">
                  <?= form_error('nama', '<small class="text-danger pl-2">', '</small>'); ?>
                </div>
                
                
                <div class="form-group col-12">
                  <label for="no_hp">No Hp</label>
                  <input type="number" class="form-control" id="no_hp" placeholder="Masukan No Hp" name="no_hp" value="<?= $masyarakat->no_hp;?>">
                  <?= form_error('no_hp', '<small class="text-danger pl-2">', '</small>'); ?>
                </div>
                
                <div class="form-group col-12">
                  <label for="nama">Alamat</label>
                  <input type="text" class="form-control" id="alamat" placeholder="Masukan Alamat" name="alamat" value="<?= $masyarakat->alamat;?>">
                  <?= form_error('alamat', '<small class="text-danger pl-2">', '</small>'); ?>
                </div>
                
                <div class="form-group col-12">
                  <label for="nama">Tempat Lahir</label>
                  <input type="text" class="form-control" id="tempat_lahir" placeholder="Masukan Tempat Lahir" value="<?= $masyarakat->tempat_lahir;?>" name="tempat_lahir">
                  <?= form_error('tempat_lahir', '<small class="text-danger pl-2">', '</small>'); ?>
                </div>
                
                <div class="form-group col-12">
                  <label for="nama">Tanggal Lahir</label>
                  <input type="text" class="form-control date" id="tanggal_lahir" placeholder="Masukan Tanggal Lahir" value="<?= date('d-m-Y', strtotime($masyarakat->tanggal_lahir)) ?>" name="tanggal_lahir" autocomplete="off">
                  <?= form_error('tanggal_lahir', '<small class="text-danger pl-2">', '</small>'); ?>
                </div>
                  <div class="form-group col-12">
                   <label for="jenis_kelamin">Jenis Kelamin</label>
                   <select id="jenis_kelamin" name="jenis_kelamin" class="form-control select2 col-12">
                    	<option value="">Pilih Jenis Kelamin</option>
						<option value="Laki-laki" <?= $masyarakat->jenis_kelamin == "Laki-laki" ? "selected" : null ?> >Laki-laki</option>
						<option value="Perempuan" <?= $masyarakat->jenis_kelamin == "Perempuan" ? "selected" : null ?> >Perempuan</option>
                     
                   </select>
                </div>
               
               
                <div class="form-group col-12">
                    <button type="submit" class="btn btn-sm btn-primary mr-2">Simpan</button>
                    
                </div>
              </form>
            </li>
        </ul>
    </div>
</div>
         
<?php $this->view('template/javascript'); ?>   
      
      <script>
 $(document).ready(function() {
  $('.date').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
            todayHighlight: true,
         })
 });
</script>
      