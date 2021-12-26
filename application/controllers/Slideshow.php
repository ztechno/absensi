<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Slideshow extends CI_Controller {
	public function __construct(){
        parent::__construct();
        // $this->load->model(['Pegawai_model', 'Api_model']);
		date_default_timezone_set("Asia/Jakarta");
		is_logged_in('Admin');
    }

    function getAll()
    {
        $slideshow = $this->db->get('tb_slideshow')->result_array();

        $results = [];
        foreach($slideshow as $key => $p)
        {
            $action = "<a href='".base_url('slideshow/edit')."/".$p['id']."' class='btn btn-success btn-sm' style='border-radius:0'>Ubah</a><a href='".base_url('slideshow/hapus')."/".$p['id']."' onclick='if(confirm(\"Apakah anda yakin akan menghapus data ini ?\")){return true}else{return false}' class='btn btn-danger btn-sm' style='border-radius:0'>Hapus</a>";
            unset($p['id']);
            $p['pic'] = "<img src='$p[pic]' style='width:100px;height:auto;border-radius:0;'>";
            $p['url'] = "<a href='$p[url]'>$p[url]</a>";
            $res = array_merge([$key+1],array_values($p));
            $res = array_merge($res,[$action]);
            $results[] = $res;
        }

        echo json_encode(['data'=>$results]);
        return;
    }
    
    function index()
    {
        $slides = $this->db->get('tb_slideshow')->result();
        $data = [
			"page"    => "slideshow/index",
            "title"   => "Data Slideshow",
            "css"     => [
                base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
			],
			"cssCode" => "",
            "slides"    => $slides
		];
		$this->load->view('template/default', $data);
    }

    function tambah()
    {
        if(isset($_POST['slide']))
        {
            $pic  = $_FILES['slide']; //['name']['foto'];
            $ext  = pathinfo($pic['name']['foto'], PATHINFO_EXTENSION);
            $name = strtotime('now').'.'.$ext;
            $file = 'slides/'.$name;
            copy($pic['tmp_name']['foto'],$file);
            $_POST['slide']['pic'] = $file;

            $this->db->insert('tb_slideshow',$_POST['slide']);

            $this->session->set_flashdata('pesan', '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil !</strong> Slideshow Berhasil di Tambah
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ');
            redirect('slideshow');
        }

        $this->load->view('template/default', [
            'title'   => 'Tambah Slideshow',
            'page'    => 'slideshow/form',
        ]);
    }

    function hapus($id)
    {
        $this->db->delete('tb_slideshow',['id'=>$id]);
        $this->session->set_flashdata('pesan', '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil !</strong> Slideshow Berhasil di hapus
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            ');
        redirect('slideshow');
    }
}
