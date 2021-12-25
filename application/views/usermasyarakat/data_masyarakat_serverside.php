<div class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h3><?= $title ?></h3> 
        </div>

        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <?php if($this->session->userdata('role_id') == 1): ?>
                    <a href="<?= base_url("usermasyarakat/generaterole?token=".$_GET['token']) ?>" class="btn btn-sm btn-warning"><em class="ti-exchange"></em> 
                       Generate Role
                    </a>
                <?php endif ; ?>
				<a href="<?= base_url("usermasyarakat/tambah?token=".$_GET['token']) ?>" class="btn btn-sm btn-primary"><em class="ti-plus"></em> 
					Tambah Masyarakat
				</a>
                
            </li>

            <li class="list-group-item">
                <table id="tableUsers" class="table table-hover table-striped" width="100%">
                    <thead>
                        <tr>
                            <th width="10">#</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Nomor HP</th>
                            <th width="40">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </li>
        </ul>
        
    </div>
</div>

<?php $this->view('template/javascript'); ?>
<script>
	$(document).ready(function() {
        Filter();
        $('#btnFilter').click(function(){
            Filter();
        });
	    function Filter(){
            $('#tableUsers').DataTable().destroy();
            $('#tableUsers').DataTable({
                scrollX: true,
				scrollY: false,
				processing : true,
				serverSide : true,
				responsive: true,
				lengthChange: true,
    	        ajax: {
    	            url:'<?=base_url('usermasyarakat/getAllMasyarakat?token='.$_GET['token']);?>',
    	            type: 'post',
    	            data:{}
    	        },
                initComplete: function(settings, json) {
                    $('.dataTables_scrollBody').css("overflow-y", "hidden");
                },
    		    pageLength: 20,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        exportOptions: {
                            columns: [ ':visible' ]
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    'colvis'
                    // 'copy', 'csv', 'excel', 'pdf', 'print','colvis'
                ]
    		});
	    }
	});
</script>
