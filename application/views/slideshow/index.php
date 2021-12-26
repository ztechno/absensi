<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?=$title;?></h3> 
        </div>
        <div class="col-3 mt-3">
            
        <a href="<?= base_url("slideshow/tambah") ?>" class="btn btn-sm btn-primary"><em class="ti-plus"></em> 
           Tambah Slideshow
        </a>
    
        </div>
        <hr>
        
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
               <div class="row">
                <div class="col-12">
                 <?= $this->session->flashdata('pesan'); ?>
                  <div class="table-responsive">
                    <table id="tablePegawai" class="table">
                        
                      <thead>
                        <tr>
                            <th>#</th>
                            <th>Pic</th>
                            <th>URL</th>
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
    $('#tablePegawai').DataTable().destroy();
    $('#tablePegawai').DataTable({
        "autoWidth": false,
        "ajax": {
            "url": "<?=base_url('slideshow/getAll') ?>",
            "type": "POST",
        },
        "pageLength" :25,
    });
}
</script>