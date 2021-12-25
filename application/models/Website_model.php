<?php
defined('BASEPATH') or exit('No direct script access allowed');
use Google\Cloud\Storage\StorageClient;

class Website_model extends CI_Model
{
   public function __construct()
   {
      parent::__construct();
      date_default_timezone_set("Asia/Jakarta");
   }
   public function getAllWebsite()
   {
      return $this->db->get('tb_websites')->result_array();
   }

   public function getWebsiteById($id)
   {
      return $this->db->get_where('tb_websites', ['id' => $id])->row_array();
   }


   public function addData()
   {

      $data = [
         "nama_website"     => $this->input->post('nama_website', true),
         "domain"           => $this->input->post('domain', true),
         "protocol"         => $this->input->post('protocol', true),
         "auth"				=> $this->input->post('auth', true),
         "logo"             => $_FILES["logo"]["name"] != "" ? $this->_upload() : "assets/images/logolabura1.png",
         "created_at"       => date("Y-m-d H:i:s"),
      ];
      $this->db->insert('tb_websites', $data);
   }


   public function editData()
   {

		$data = [
			"nama_website"     	=> $this->input->post('nama_website', true),
			"domain"           	=> $this->input->post('domain', true),
			"protocol"         	=> $this->input->post('protocol', true),
			"auth"				=> $this->input->post('auth', true),
			"is_hide_in_portal"	=> isset($_POST['is_hide_in_portal']) && $_POST['is_hide_in_portal'] ? $_POST['is_hide_in_portal'] : null,
		];
		if($_FILES["logo"]["name"] != ""){
			$data["logo"]		= $this->_upload();
		}

        $this->db->where('id', $this->input->post('id'));
		$this->db->update('tb_websites', $data);
		
   }

   public function deleteData($id)
   {
    //   $this->_deleteImage($id);
      $this->db->where('id', $id);
      $this->db->delete('tb_websites');
   }

   private function _deleteImage($id)
   {
      $produk = $this->getWebsiteById($id);
      
       if($produk['logo'] == null ){
            return;
        }

      $filename = $produk['logo'];
      array_map('unlink', glob(FCPATH . "assets/images/logo_website/$filename"));
   }

   private function _upload()
   {



		# Instantiates a client
		$storage = new StorageClient([
			'projectId' => 'layanan-325704'
		]);

		# The name for the new bucket
		$bucketName = 'layanan_resources';

		# Creates the new bucket
		$bucket = $storage->bucket($bucketName);

        $jam = date("Y-m-d H:i:s");
        $fileName = 'websites_logo/'.time().'.png';
		$options = [
			'resumable' => true,
			'name' => $fileName,
			'metadata' => [
				'contentLanguage' => 'en'
			]
		];
		$object = $bucket->upload(
			file_get_contents($_FILES['logo']['tmp_name']),
			$options
		);

		return 'https://storage.googleapis.com/layanan_resources/'.$fileName;

      $this->_createdPHP('./assets/images/logo_website/');

      $config['upload_path']          = './assets/images/logo_website/';
      $config['allowed_types']        = 'jpg|png|jpeg';
      $config['overwrite']            = true;
      $config['max_size']             = 2024; // 2MB
      // $config['encrypt_name']         = true;
      // $config['file_name']            = md5(time());
      // $config['max_width']            = 1024;
      // $config['max_height']           = 768;

    //   $new_name = $this->input->post('judul', true);
    //   $namafile = preg_replace("/[^a-zA-Z0-9]/", "-", $new_name);
    //   $config['file_name'] = $namafile;

      $this->load->library('upload', $config);


      if ($this->upload->do_upload('logo')) {
         return $this->upload->data('file_name');
      } else {
         $error = array('error' => $this->upload->display_errors());
      }

   
   }

   public function _createdPHP($DIR)
   {
      $index = fopen($DIR . "/index.php", "w") or die("Unable to open file!");
      $val = "<script>location.href='" . base_url() . "auth/blocked'</script>";
      fwrite($index, $val);
      fclose($index);
   }
}
