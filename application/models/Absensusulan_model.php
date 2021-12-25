<?php
defined('BASEPATH') or exit('No direct script access allowed');
# Imports the Google Cloud client library
use Google\Cloud\Storage\StorageClient;

class Absensusulan_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
    }

    public function addAbsenSusulan()
    {
        extract($_POST);
        $pegawai        = explode("_", $pegawai);
        $skpd           = explode("_", $skpd_id);

        $pegawai_id     = $pegawai[0];
        $jenis_pegawai  = $pegawai[1];
        $username       = $pegawai[2];
        $nama_pegawai   = $pegawai[3];
        $skpd_id        = $skpd[0];
        $nama_skpd      = $skpd[1];
        
        $data       = file_get_contents($_FILES['pendukung']['tmp_name']);
        $fileData   = $data;
        $fileName   = 'absensusulan/'.date("F Y").'/'.$username.'/'.time()."_".$_FILES['pendukung']['name'];

        if(!isset($fileData)){
            return array(false, "Periksa kembali file pendukung!");
        }

        $projectId = 'absensi-325704'; # Your Google Cloud Platform project ID
        $storage = new StorageClient([ # Instantiates a client
            'projectId' => $projectId
        ]);
        $bucketName = 'file-absensi'; # The name for the new bucket
        $bucket = $storage->bucket($bucketName); # Creates the new bucket

        $jam = date("Y-m-d H:i:s");
        $options = [
            'resumable' => true,
            'name' => $fileName,
            'metadata' => [
                'contentLanguage' => 'en'
            ]
        ];
        $object = $bucket->upload(
            $fileData,
            $options
        );
        
        $urlFile = str_replace(" ","%20", $fileName);
        $this->db->insert('tb_absensi', [
            "pegawai_id"        => $pegawai_id,
            "jenis_pegawai"     => $jenis_pegawai,
            "nama_pegawai"      => $nama_pegawai,
            "skpd_id"           => $skpd_id,
            "nama_opd"          => $nama_skpd,
            "jam"               => date("Y-m-d", strtotime($tanggal))." 01:01:01",
            "jenis_absen"       => $this->input->post('jenis_absen', true),
            "file_absensi"      => $urlFile,
            "is_susulan"        => "Ya",
            "status"            => 1,
            "approved_by"       => $this->session->userdata("user_id"),
            "approved_by_nama"  => $this->session->userdata("nama"),
            "approved_at"       => date("Y-m-d H:i:s"),
        ]);

        return array(true, "Berhasil ditambahkan!");
    }
    
    private function generatePegawai($pegawai_id, $jenis_pegawai, $pegawais, $tkss){

        if($jenis_pegawai=='pegawai'){
            $indexPegawai   = array_search($pegawai_id, array_column($pegawais, 'user_id'));
            $indexPegawai   = $indexPegawai!==false ? $indexPegawai : "none"; 
            $pegawai        = (isset($pegawais[$indexPegawai])  ? $pegawais[$indexPegawai] : ['nama'=>'undefined']);
        }else{
            $indexTks       = array_search($pegawai_id, array_column($tkss, 'user_id'));
            $indexTks       = $indexTks!==false ? $indexTks : "none"; 
            $pegawai        = (isset($tkss[$indexTks]) ? $tkss[$indexTks] : ['nama'=>'undefined']);
        }
        $gelarDepan         = isset($pegawai['gelar_depan']) && $pegawai['gelar_depan'] && $pegawai['gelar_depan']!=="" ? $pegawai['gelar_depan']."." : null;
        $gelarBelakang      = isset($pegawai['gelar_belakang']) && $pegawai['gelar_belakang'] && $pegawai['gelar_belakang']!="" ? " ".$pegawai['gelar_belakang'] : null;

        $pegawai['nama']    = $gelarDepan.$pegawai['nama'].$gelarBelakang;

        return $pegawai;
    }
    
    private function _upload($skpd_id)
    {
        $ext = 'jpeg'; // strtolower(pathinfo($_FILES["file_izin"]["name"], PATHINFO_EXTENSION));
        $this->load->model('Skpd_model');
        $skpd       = $this->Skpd_model->getSkpdById($skpd_id);
        $sub_dir    = $skpd['nama_skpd'] . "_" . $skpd['id_skpd'] . "/" . date('Y') . "/" . date('m') . "/";
        $directory  = './resources/berkas/izin_kerja/' . $sub_dir;


        if (!file_exists($directory)) {
            if (!mkdir($directory, 0777, true)) {
                die('Failed to create folders...');
            }
        }

        $this->_createdPHP('./resources/berkas/izin_kerja');
        $this->_createdPHP('./resources/berkas/izin_kerja/' . $skpd['nama_skpd'] . "_" . $skpd['id_skpd']);
        $this->_createdPHP('./resources/berkas/izin_kerja/' . $skpd['nama_skpd'] . "_" . $skpd['id_skpd'] . "/" . date('Y'));
        $this->_createdPHP('./resources/berkas/izin_kerja/' . $skpd['nama_skpd'] . "_" . $skpd['id_skpd'] . "/" . date('Y') . "/" . date('m'));
        
        if(isset($_POST['file_izin']) && $_POST['file_izin']){
            $img = $_POST['file_izin'];
            $img = str_replace('data:image/jpeg;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $fileData = base64_decode($img);
            $name = md5(time()).".jpeg";
            $fileName = $directory."/".$name;
            file_put_contents($fileName, $fileData);
            
            return array(true, $sub_dir.$name);
        }

        $config['upload_path']          = $directory;
        $config['allowed_types']        = 'pdf|jpg|jpeg|png';
        $config['overwrite']            = true;
        $config['max_size']             = 20400; // 2MB
        $config['file_name']            = md5(time());
        // $config['file_ext']             = $ext;
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('lampiran')) {
            $directory = $sub_dir . "" . $this->upload->data("file_name");
            return array(true, $directory);
        } else {
            return array(false, $this->upload->display_errors());
        }
    }

    public function _createdPHP($DIR)
    {
        $index = fopen($DIR . "/index.php", "w") or die("Unable to open file!");
        $val = "<script>location.href='" . base_url() . "auth/blocked'</script>";
        fwrite($index, $val);
        fclose($index);
    }


    public function getIzinById($id = false)
    {
        return $this->db->get_where('tb_izin_kerja', ['id' => $id])->row_array();
    }

    public function deleteDataIzin($id)
    {
        $this->_deleteImage($id);
        $this->db->where('id', $id);
        $this->db->delete('tb_izin_kerja');
    }

    private function _deleteImage($id)
    {
        $izin = $this->getIzinById($id);

        $filename = $izin['file_izin'];
        array_map('unlink', glob(FCPATH . "assets/img/berkas/izin_kerja/$filename"));
    }
}
