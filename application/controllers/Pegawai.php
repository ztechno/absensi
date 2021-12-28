<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pegawai extends CI_Controller {
	public function __construct(){
        parent::__construct();
        // $this->load->model(['Pegawai_model', 'Api_model']);
		date_default_timezone_set("Asia/Jakarta");
		is_logged_in('Admin');
    }

    function getAll()
    {
        if(isset($_POST['skpd_id']) && !empty($_POST['skpd_id']))
            $this->db->where('opd_id',$_POST['skpd_id']);
        $pegawai = $this->db->select('tb_pegawai.id, tb_pegawai.nama,tb_pegawai.nip,tb_pegawai.jabatan,tb_opd.nama_opd')
                    ->join('tb_opd','tb_opd.id=tb_pegawai.opd_id')
                    ->get('tb_pegawai')
                    ->result_array();

        $results = [];
        foreach($pegawai as $key => $p)
        {
            $action = "<a href='".base_url('pegawai/edit')."/".$p['id']."' class='btn btn-success btn-sm' style='border-radius:0'>Ubah</a><a href='".base_url('pegawai/hapus')."/".$p['id']."' onclick='if(confirm(\"Apakah anda yakin akan menghapus data ini ?\")){return true}else{return false}' class='btn btn-danger btn-sm' style='border-radius:0'>Hapus</a>";
            unset($p['id']);
            $res = array_merge([$key+1],array_values($p));
            $res = array_merge($res,[$action]);
            $results[] = $res;
        }

        echo json_encode(['data'=>$results]);
        return;
    }

    function getPegawaiByOpd($id)
    {
        $this->db->where('opd_id',$id);
        $pegawai = $this->db->select('tb_pegawai.id, tb_pegawai.nama,tb_pegawai.nip,tb_pegawai.jabatan,tb_opd.nama_opd')
                    ->join('tb_opd','tb_opd.id=tb_pegawai.opd_id')
                    ->get('tb_pegawai')
                    ->result_array();
        echo json_encode(['data'=>$pegawai]);
        return;
    }
    
    function index()
    {
        $opds = $this->db->get('tb_opd')->result();
        $data = [
			"page"    => "pegawai/index",
            "title"   => "Data Pegawai",
            "css"     => [
                base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
			],
			"cssCode" => "",
            "opds"    => $opds
		];
		$this->load->view('template/default', $data);
    }

    function tambah()
    {
        if(isset($_POST['pegawai']))
        {
            $pic  = $_FILES['pegawai']; //['name']['foto'];
            $ext  = pathinfo($pic['name']['foto'], PATHINFO_EXTENSION);
            $name = strtotime('now').'.'.$ext;
            $file = 'foto/'.$name;
            copy($pic['tmp_name']['foto'],$file);
            $_POST['pegawai']['foto'] = $file;

            $_POST['user']['nama'] = $_POST['pegawai']['nama'];
	    $_POST['user']['username'] = $_POST['pegawai']['nip'];
            $_POST['user']['password'] = password_hash($_POST['user']['password'],PASSWORD_DEFAULT);
            $_POST['user']['is_active'] = 'Ya';

            $this->db->insert('tb_users',$_POST['user']);
            $user_id = $this->db->insert_id();

            $_POST['pegawai']['user_id'] = $user_id;
            $this->db->insert('tb_pegawai',$_POST['pegawai']);
            $pegawai_id = $this->db->insert_id();
            $this->db->insert('tb_pegawai_atasan',[
                'pegawai_id'=>$pegawai_id,
                'pegawai_atasan_id'=>$_POST['atasan']['pegawai_id']
            ]);
            $this->db->insert('tb_user_roles',[
                'user_id' => $user_id,
                'role_id' => 3, // pegawai (get from tb_roles)
            ]);

            if(!empty($_POST['detection']))
            {
                $detection = 'var employee_sample='.$_POST['detection'];
                file_put_contents('facesamples/sample-'.$pegawai_id.'.js',$detection);
            }

            $this->session->set_flashdata('pesan', '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil !</strong> Data Pegawai Berhasil di Tambah
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ');
            redirect('pegawai');
        }

        $opds = $this->db->get('tb_opd')->result();
        $this->load->view('template/default', [
            'title'   => 'Tambah Pegawai',
            'page'    => 'pegawai/form',
            'pegawai' => [],
            'opds'    => $opds
        ]);
    }

    function edit($id)
    {

        $pegawai = $this->db->select('tb_pegawai.*, tb_users.*, tb_pegawai.id, tb_pegawai_atasan.pegawai_atasan_id')->where('tb_pegawai.id', $id)->join('tb_users', 'tb_users.id=tb_pegawai.user_id', 'left')->join('tb_pegawai_atasan', 'tb_pegawai_atasan.pegawai_id=tb_pegawai.id', 'left')->get('tb_pegawai')->row();
        if(!$pegawai) redirect('pegawai');
        if(isset($_POST['pegawai']))
        {
            $_POST['user']['nama'] = $_POST['pegawai']['nama'];
            if(isset($_POST['user']['password']) && $_POST['user']['password']) 
            {
                $_POST['user']['password'] = password_hash($_POST['user']['password'],PASSWORD_DEFAULT);
            }if(isset($_POST['user']['password'])){
                unset($_POST['user']['password']);
            }
            $this->db->where('id', $pegawai->user_id)->update('tb_users',$_POST['user']);

            if(!empty($_FILES['pegawai']['name']['foto']))
            {
                $pic  = $_FILES['pegawai']; //['name']['foto'];
                $ext  = pathinfo($pic['name']['foto'], PATHINFO_EXTENSION);
                $name = strtotime('now').'.'.$ext;
                $file = 'foto/'.$name;
                copy($pic['tmp_name']['foto'],$file);
                $_POST['pegawai']['foto'] = $file;
            }
            $this->db->where('id', $id)->update('tb_pegawai',$_POST['pegawai']);
            $this->db->where('pegawai_id', $id)->update('tb_pegawai_atasan',[
                'pegawai_atasan_id'=>$_POST['atasan']['pegawai_id']
            ]);

            if(!empty($_POST['detection']))
            {
                $detection = 'var employee_sample='.$_POST['detection'];
                file_put_contents('facesamples/sample-'.$id.'.js',$detection);
            }

            $this->session->set_flashdata('pesan', '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil !</strong> Data Pegawai berhasil diubah
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ');
            redirect('pegawai');
        }

        $opds = $this->db->get('tb_opd')->result();
        $this->load->view('template/default', [
            'title'   => 'Ubah Pegawai',
            'page'    => 'pegawai/form',
            'pegawai' => $pegawai,
            'opds'    => $opds
        ]);
    }


    function hapus($id)
    {
        $this->db->delete('tb_pegawai',['id'=>$id]);
        $this->session->set_flashdata('pesan', '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil !</strong> Data Pegawai Berhasil di hapus
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ');
        redirect('pegawai');
    }

    public function optionPegawai(){
        echo '<option value="">Pilih Pegawai</option>';
        foreach($this->db->where('opd_id', $_POST['opd_id'])->get('tb_pegawai')->result() as $pegawai){
            echo '<option value="'.$pegawai->id.'">'.$pegawai->nama.'</option>';
        }
    }
}
