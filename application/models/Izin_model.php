<?php
defined('BASEPATH') or exit('No direct script access allowed');
# Imports the Google Cloud client library
use Google\Cloud\Storage\StorageClient;

class Izin_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
        $this->load->model(['Pegawai_model','Sms_model', 'Notifikasi_model', 'Shortener_model']);
    }

    public function getAllIzin()
    {
        $this->db->select('tb_izin_kerja.*');
        $this->db->from('tb_izin_kerja');
        return $this->db->get()->result_array();
    }

    public function getIzin()
    {
        return $this->db->get('tb_izin_kerja')->row_array();
    }

    public function addDataIzin()
    {
        $pegawai_id     = $this->session->userdata('user_id');
        $jenis_pegawai  = $this->session->userdata('jenis_pegawai');
    

        if($_FILES['lampiran']['name']){
            $data       = file_get_contents($_FILES['lampiran']['tmp_name']);
            $fileData   = $data;
            $fileName   = 'izin_kerja/'.date("Y/F").'/'.$_SESSION['username'].'/'.time()."_".$_FILES['lampiran']['name'];

        }else if(isset($_POST['file_izin']) && $_POST['file_izin']){
            $img        = $_POST['file_izin'];
            $img        = str_replace('data:image/jpeg;base64,', '', $img);
            $img        = str_replace(' ', '+', $img);
            $fileData   = base64_decode($img);
            $fileName   = 'izin_kerja/'.date("Y/F").'/'.$_SESSION['username'].'/'.time().".jpg";
        }

        if(!isset($fileData)){
            return array(false, "Gagal mengajukan izin kerja!");
        }

        # Your Google Cloud Platform project ID
        $projectId = 'absensi-325704';

        # Instantiates a client
        $storage = new StorageClient([
            'projectId' => $projectId
        ]);

        # The name for the new bucket
        $bucketName = 'file-absensi';

        # Creates the new bucket
        $bucket = $storage->bucket($bucketName);

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

        $urlFile = str_replace(" ","%20", "https://storage.googleapis.com/file-absensi/".$fileName);
        $dataMeta = [
            "tanggal_awal"  => date("Y-m-d", strtotime($this->input->post('tanggal_awal', true))),
            "tanggal_akhir" => $_POST['tanggal_akhir'] == "" ? date("Y-m-d", strtotime($this->input->post('tanggal_awal', true))) : date("Y-m-d", strtotime($this->input->post('tanggal_akhir', true))),
            "jenis_izin"    => $this->input->post('jenis_izin', true),
            "file_izin"     => $urlFile,
            "user_id"       => $this->session->userdata('user_id')
        ];
        $this->db->insert('tb_izin_kerja_meta', $dataMeta);
        $meta_id = $this->db->insert_id();

        $access_key         = rand(90000,99999)."-".substr(md5(time()), 0, 7);

        $data = [
            "meta_id"       => $meta_id,
            "pegawai_id"    => $pegawai_id,
            "jenis_pegawai" => $jenis_pegawai,
            "nama_pegawai"  => $this->session->userdata('nama'),
            "skpd_id"       => $this->session->userdata('skpd_id'),
            "nama_opd"      => $this->session->userdata('nama_skpd'),
            "access_key"    => $access_key, 
        ];
        $this->db->insert('tb_izin_kerja', $data);

        $pegawai            = $this->Pegawai_model->getPegawaiAtasan($pegawai_id, $jenis_pegawai);

        if(isset($pegawai['nama_pegawai'])){
            // $pesan          = "*[ABSENSI-NG]*\n\nAda permohonan izin *".$this->input->post('jenis_izin', true)."* dari *".$pegawai['nama_pegawai']."*.\n\n*Lampiran Izin :*\n".$urlFile."\n\n*Setujui dengan tap link ini :*\n".base_url('byaccesskey/setujuiizinkerja/'.$meta_id."/".$access_key)."\n\n*Tolak dengan tap link ini:*\n".base_url('byaccesskey/tolakizinkerja/'.$meta_id."/".$access_key);
            // $this->Sms_model->send($pegawai['no_hp_pegawai_atasan'], $pesan);
            $url            = $this->Shortener_model->buaturl(base_url('byaccesskey/izinkerja/'.$meta_id."/".$access_key));
            $pesan          = "[ABSENSI-NG] Ada permohonan izin ".$this->input->post('jenis_izin', true)." dari ".$pegawai['nama_pegawai'].". Konfirmasi melalui link ini : ".$url;
            $this->Notifikasi_model->send(array(
                          'user_id'         => $pegawai['pegawai_atasan_id'],
                          'jenis_user'      => $pegawai['jenis_pegawai_atasan'],
                          'user_name'       => $pegawai['nama_pegawai_atasan'],
                          'contents'        => $pesan,
                    ));
        }

        return array(true, "Izin Kerja baru telah ditambahkan, silahkan tunggu verifikasi selanjutnya!");
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
