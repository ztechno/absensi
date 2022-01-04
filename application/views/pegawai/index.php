<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>
        <div class="col-12 mt-3">
            
        <a href="<?= base_url("pegawai/tambah") ?>" class="btn btn-sm btn-primary"><em class="ti-plus"></em> 
           Tambah Pegawai
        </a>

        <a href="<?= base_url("pegawai/import") ?>" class="btn btn-sm btn-success"><em class="ti-upload"></em> 
           Import Pegawai
        </a>

        <a href="<?= base_url("format/f01.xlsx") ?>" class="btn btn-sm btn-success"><em class="ti-download"></em> 
           Download Format Import
        </a>
    
        </div>
        <hr>
        
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <div class="form-group">
                    <label for="">Filter OPD</label>
                    <select name="filter_opd" class="form-control" id="" onchange="getFilter()">
                        <option value="">- Pilih -</option>
                        <?php foreach($opds as $opd): ?>
                        <option value="<?=$opd->id?>"><?=$opd->nama_opd?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </li>
            <li class="list-group-item">
               <div class="row">
                <div class="col-12">
                 <?= $this->session->flashdata('pesan'); ?>
                  <div class="table-responsive">
                    <table id="tablePegawai" class="table">
                        
                      <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Jabatan</th>
                            <th>Unit Kerja</th>
                            <th align="right">Action</th>
                        </tr>
                      </thead>
                      
                    </table>
                  </div>
                </div>
              </div>
            </li>
        </ul>
        
    </div>
</div>

<?php $this->view('template/javascript'); ?>

<script>
getFilter();

function getFilter() {
    var skpd_id = $("select[name=filter_opd]").val();
    console.log(skpd_id)
    $('#tablePegawai').DataTable().destroy();
    $('#tablePegawai').DataTable({
        "autoWidth": false,
        "ajax": {
            "url": "<?=base_url('pegawai/getAll') ?>",
            "type": "POST",
            "data": {
                "skpd_id": skpd_id,
            }
        },
        "pageLength" :25,
    });
}
</script>