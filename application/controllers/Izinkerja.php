<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Izinkerja extends CI_Controller {
    
    public $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
    public $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

	public function __construct(){
        parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
        $this->load->model(['Pegawai_model','Skpd_model','Izin_model','Upacara_model']);
		is_logged_in();
    }

    public function dataizinkerja($bulanTahun=false)
    {
        $data = [
			"page"				=> "izinkerja/data_izinkerja",
			"title"             => "Data Izin Kerja",
            "skpds"             => $this->Skpd_model->get(),
            "pegawais"          => $this->Pegawai_model->getPegawai(),
            "javascript"		=> [
				base_url("assets/vendors/datatables.net/jquery.dataTables.js"),
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js"),
			],
			"css"				=> [
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
			],
		];
		$this->load->view('template/default', $data);   
    }

    private function queryIzinKerja($order="tb_izin_kerja.id"){
        $start_date     = isset($_POST['bulan']) ? date("Y-m-01", strtotime("01-".$_POST['bulan']))   : date("Y-m-01");
        $end_date       = isset($_POST['bulan']) ? date("Y-m-t", strtotime("01-".$_POST['bulan']))    : date("Y-m-t");

        return $this->db->select('
                            tb_izin_kerja.*, 
                            tb_pegawai.nama,
                            tb_atasan.nama nama_atasan,
                            tb_atasan_meta.pegawai_atasan_id,
                            tb_opd.nama_opd
                        ')->
                        where("tb_izin_kerja.pegawai_id", $this->session->userdata('id'))->
                        where("DATE_FORMAT(tb_izin_kerja.created_at,'%Y-%m-%d')>=", $start_date)->
                        where("DATE_FORMAT(tb_izin_kerja.created_at,'%Y-%m-%d')<=", $end_date)->                        
                        order_by($order, 'desc')->
                        join('tb_pegawai','tb_pegawai.user_id=tb_izin_kerja.pegawai_id', 'left')->
                        join('tb_pegawai_atasan as tb_atasan_meta','tb_atasan_meta.pegawai_id=tb_izin_kerja.pegawai_id', 'left')->
                        join('tb_pegawai as tb_atasan','tb_atasan.id=tb_atasan_meta.pegawai_atasan_id', 'left')->
                        join('tb_opd','tb_opd.id=tb_pegawai.opd_id', 'left')->
                        order_by('tb_izin_kerja.id', 'desc')->
                        get('tb_izin_kerja')->
                        result_array();
    }

    public function getDataIzinKerja(){
        $start_date     = isset($_POST['bulan']) ? date("Y-m-01", strtotime("01-".$_POST['bulan']))   : date("Y-m-01");
        $end_date       = isset($_POST['bulan']) ? date("Y-m-t", strtotime("01-".$_POST['bulan']))    : date("Y-m-t");

        $list = $this->queryIzinKerja();

        $data       = array();
        $no         = 1;
        $confirm    = "Anda yakin hapus ?";
        foreach ($list as $field) {
            $row = array();
            $row[] = $this->hari[date("w", strtotime($field['tanggal_awal']))] . ", " . date('d F Y', strtotime($field['tanggal_awal']));
            $row[] = $this->hari[date("w", strtotime($field['tanggal_akhir']))] . ", " . date('d F Y', strtotime($field['tanggal_akhir']));            
            $row[] = '<div title="'.$field['nama'].'">'.(strlen($field['nama'])>25 ? substr($field['nama'],0,25)."..." : $field['nama']).'</div>';
            $row[] = '<div title="'.$field['nama_opd'].'">'.(strlen($field['nama_opd'])>25 ? substr($field['nama_opd'],0,25)."..." : $field['nama_opd']).'</div>';
            $row[] = $field['jenis_izin'];
            $row[] = '<a href="' . $field['file_izin'] . '" class="text-center" target="_BLANK">Berkas</a>';
            $row[] = $field['status']==1 ? 
                            'Disetujui Oleh '.$field['aproved_by_nama'].'</strong><br><small class="text-info"><i>'.date("d/m/Y, H:i", strtotime($field['aproved_by_nama'])).'</i></small>'  : 
                      ($field['status']==null ? 
                            '<span class="btn-warning" style="padding: 2px 7px; border-radius: 6px;">Menunggu</span>' : 
                            '<span class="btn-danger" style="padding: 2px 7px; border-radius: 6px;">Ditolak</span>'); 
            $row[] = '<a href="' . site_url('izinkerja/deleteizin/' . $field['id']).'" onclick="return confirm(\'Yakin hapus data?\')"  class="btn btn-danger btn-sm" title="Hapus"><i style="margin-right:0;" class="fa fa-trash"></i></a>';
            $no++;

            $data[] = $row;
        }

        $output = array(
            "data" => $data
        );
        echo json_encode($output);
        return;
    }
    
    public function addizin()
    {

        $data = [
                    "page"				=> "izinkerja/add_izinkerja",
            		"title"             => "Buat Izin Kerja",
            		"javascript"		=> [
                                    		base_url("assets/vendors/bs-custom-file-input/bs-custom-file-input.min.js"),
                                    		base_url("assets/js/select2.js"),
    		        ],
            		"css"				=> [
            			                    base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
            		],
    	];

	    $this->form_validation->set_rules('jenis_izin', 'Jenis Izin', 'required');
	    $this->form_validation->set_rules('tanggal_awal', 'Tanggal Awal', 'required');

		if ($this->form_validation->run() == false) {
		    $this->load->view('template/default', $data);
		}else{

		    $_POST['tanggal_akhir'] = $_POST['tanggal_akhir'] ? $_POST['tanggal_akhir'] : $_POST['tanggal_awal'];
		    if($this->cekIzin(true)){
                $this->session->set_flashdata('pesan', '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Data izin sudah ada, silahkan pilih tanggal lain!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                  </div>
                ');
                redirect('izinkerja/addizin');
                return;
		    }
            $addIzin = $this->Izin_model->addDataIzin();

            $this->session->set_flashdata('pesan', '
                <div class="alert alert-'.($addIzin[0] ? 'success' : 'danger').' alert-dismissible fade show" role="alert">
                    ' . $addIzin[1] . '
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            ');
            redirect('izinkerja/dataizinkerja');
            return;
        }
    }

    public function cekIzin($no_json=false){
        
        if (isset($_POST['tanggal_awal']) && isset($_POST['tanggal_akhir'])) {
            extract($_POST);
            $tanggal_awal   = date("Y-m-d", strtotime($tanggal_awal));
            $tanggal_akhir  = date("Y-m-d", strtotime($tanggal_akhir));
            $data   = $this->db->
                             where('tb_izin_kerja.pegawai_id', $this->session->userdata('id'))->
                             group_start()->
                                where('tb_izin_kerja.status', null)->
                                or_where('tb_izin_kerja.status', 1)->
                             group_end()->
                             group_start()->
                                or_group_start()->
                                    where('tb_izin_kerja.tanggal_awal>=', $tanggal_awal)->
                                    where('tb_izin_kerja.tanggal_akhir<=', $tanggal_awal)->
                                group_end()->
                                or_group_start()->
                                    where('tb_izin_kerja.tanggal_awal>=', $tanggal_akhir)->
                                    where('tb_izin_kerja.tanggal_akhir<=', $tanggal_akhir)->
                                group_end()->
                             group_end()->
                             order_by('tb_izin_kerja.id', 'desc')->
                             get('tb_izin_kerja')->result();

            if(count($data)>0) {
                $html = "<ol>";
                foreach($data as $dt){
                    $html .= "<li>".$dt->jenis_izin." ".$dt->tanggal_awal." s/d ".$dt->tanggal_awal."</li>";
                }
                $html .= "</ol>";
                if(!$no_json) echo json_encode([true, $html]);
                return true;
            }
        }
        if(!$no_json) echo json_encode([false]);
        return false;
    }

    
     function selectpegawaibyopdeditizin()
    {
        $this->db->order_by('nama', 'asc');
        $this->db->where("opd_id", $_POST['opd_id']);
        $list = $this->Pegawai_model->getPegawaiByOpd();

        $a = count($list);
        if ($a > 0) {
            echo "<option value=''>-- Pilih Pegawai --</option>";
        } else {
            echo "<option value=''>-- Tidak ada data --</option>";
        }
        foreach ($list as $l) {
            $last_pegawai = explode(',', $_POST['last_pegawai']);
            $selected = in_array($l->id, $last_pegawai) ? "selected" : null;
            echo "<option value='" . $l->id . "'" . $selected . ">" . $l->nama . "</option>";
        }
    }
    
    public function deleteizin($id = false){
        $izinkerja = $this->db->
                           where('pegawai_id', $this->session->userdata('id'))->
                           get('tb_izin_kerja')->row();

        if(!$izinkerja){
            $this->session->set_flashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                 Invalid!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
               ');
            redirect('izinkerja/dataizinkerja');
            return;
        }

        $this->db->where('id', $id)->delete('tb_izin_kerja');


        $this->session->set_flashdata('pesan', '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
             Berhasil dihapus!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
           ');
        
        redirect('izinkerja/dataizinkerja');
        return;
    }

}
