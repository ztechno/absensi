<?php
defined('BASEPATH') or exit('No direct script access allowed');
use Google\Cloud\Storage\StorageClient;

class Pengaturan_model extends CI_Model
{
   public function __construct()
   {
      parent::__construct();
      date_default_timezone_set("Asia/Jakarta");
   }
   public function _uploadGoogleStorage(){

	   if(!isset($_FILES['image']['tmp_name']) || !$_FILES['image']['tmp_name']){
		   return false;
	   }
		# Instantiates a client
		$storage = new StorageClient([
			'projectId' => 'layanan-325704'
		]);

		# The name for the new bucket
		$bucketName = 'layanan_resources';

		# Creates the new bucket
		$bucket = $storage->bucket($bucketName);

		$jam = date("Y-m-d H:i:s");
		$fileName = 'home_popup/'.time().'.png';
		$options = [
			'resumable' => true,
			'name' => $fileName,
			'metadata' => [
				'contentLanguage' => 'en'
			]
		];
		$object = $bucket->upload(
			file_get_contents($_FILES['image']['tmp_name']),
			$options
		);

		return 'https://storage.googleapis.com/layanan_resources/'.$fileName;
   
   }
}
