<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Apiabsen extends CI_Controller {
    public $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
    public $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

	public function __construct(){
        parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
		
		// $this->load->model([
        //     'LogAbsen_model',
        //     'Pegawai_model',
        //     'Notifikasi_model',
        //     'Shortener_model',
        //     'Sms_model',
        //     'Skpd_model',
        //     'Hitung_model',
        //     'AbsenManual_model'
		// ]);
		
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Methods: *");
        header("Access-Control-Allow-Headers: *");
	}
	
	public function role(){
		$this_user_key  = '64240-d0ede73ccaf823f30d586a5ff9a35fa5';
		$this_user_pass = 'b546a6dfc4';
		
	
		if(isset($_POST['user_key']) && isset($_POST['pass_key'])){
			extract($_POST);
			if($user_key!=$this_user_key || $pass_key!=$this_user_pass){
				echo json_encode([
					'alert'     => ['class'    => 'danger', 'capt'     => '<strong>Error</strong> Api key tidak valid, silahkan coba lagi!']
				]);
				exit();
			}

			if($method=='get'){
				$role = $this->db->order_by('role_id', 'desc')->get('tb_role')->result_array();
				echo json_encode([
					'data'      => $role,
				]);
				exit();
			}else if($method=='getone' && isset($_POST['role_id'])){
				$role = $this->db->where('role_id', $role_id)->order_by('role_id', 'desc')->get('tb_role')->row_array();
				echo json_encode([
					'data'      => $role,
				]);
				exit();
				
			}else{
				echo json_encode([
					'alert'     => ['class'    => 'danger', 'capt'     => 'Informasi salah, silahkan coba lagi!']
				]);
				exit();
			}
		}
 		
		echo json_encode([
			'alert'     => ['class'    => 'danger', 'capt'     => 'Api key tidak valid, silahkan coba lagi!']
		]);

	}

    public function hapusSptFromSimpernas(){
        extract($_POST);
        if(
            isset($_POST['user_key']) &&
            isset($_POST['pass_key']) &&
            $user_key == 'absensiAPI' && 
            $pass_key == '12345654321' &&
            isset($_POST['datas'])
        ){

            foreach($_POST['datas'] as $data){
                $izinKerja = $this->db->select('tb_izin_kerja_meta.*, tb_izin_kerja.*')->
                                        where('tb_izin_kerja_meta.spt_id', $data['spt_id'])->
                                        where('tb_izin_kerja.pegawai_id', $data['pegawai_id'])->
                                        where('tb_izin_kerja.jenis_pegawai', $data['jenis_pegawai'])->
                                        join('tb_izin_kerja', 'tb_izin_kerja.meta_id=tb_izin_kerja_meta.id', 'left')->
                                        get('tb_izin_kerja_meta')->row();
                if($izinKerja){
                    $this->db->where('id', $izinKerja->id)->delete('tb_izin_kerja');
                    $this->db->where('id', $izinKerja->meta_id)->delete('tb_izin_kerja_meta');
                }
            }
            echo json_encode([
                "status"    => "berhasil",
            ]);
            return;
        }
        
        echo json_encode([
            "status"    => "gagal",
        ]);
        return;

    }
    
	public function pushSPT()
	{
        if(
            isset($_POST['user_key']) &&
            isset($_POST['pass_key']) &&
            isset($_POST['datas'])
        ){
            extract($_POST);
            if($user_key == 'c17ec-86e36cdf98af8d18ee2a2ed4efe379ac' && $pass_key == '3f81ab816b'){
				foreach($datas as $data){
					$this->db->insert('tb_izin_kerja_meta', [
							'tanggal_awal'      => $data['tgl_pergi'],
							'tanggal_akhir'     => $data['tgl_kembali'],
							'jenis_izin'        => 'Dinas Luar',
							'spt_id'            => $data['spt_id'],
							'file_izin'         => $data['file_izin'],
						]);
					$meta_id = $this->db->insert_id();
					$this->db->insert('tb_izin_kerja', [
							'meta_id'           => $meta_id,
							'skpd_id'           => $data['skpd_id'],
							'pegawai_id'        => $data['pegawai_id'],
							'jenis_pegawai'     => $data['jenis_pegawai'],
							'nama_pegawai'		=> $data['nama_pegawai'],
							'nama_opd'			=> $data['nama_opd'],
							'status'            => 1,
							'aproved_by'		=> $data['aproved_by'],
							'aproved_by_nama'	=> $data['aproved_by_nama'],
							'aproved_at'        => date("Y-m-d H:i:s")
						]);
				}

				echo json_encode(true);
				return;
            }
		}
		
        echo json_encode(false);
        return;
	}
	public function push_absen_wajah()
	{
        if(
            !isset($_POST['user_key']) || 
            !isset($_POST['pass_key']) ||
            !isset($_POST['pegawai_id']) ||
            !isset($_POST['jenis_pegawai']) ||
            !isset($_POST['skpd_id'])
        ){
            echo json_encode(false);
            return;
        }
        
        $user_key = '64240-d0ede73ccaf823f30d586a5ff9a35fa5';
        $pass_key = 'b546a6dfc4';

        if(
            $user_key!=$_POST['user_key'] ||
            $pass_key!=$_POST['pass_key']
        ){
            echo json_encode(false);
            return;
        }
            
        extract($_POST);

        $absen_wajah = $this->db
                            ->where('pegawai_id', $pegawai_id)
                            ->where('jenis_pegawai', $jenis_pegawai)
                            ->where('tahun', date('Y'))
                            ->where('bulan', date('m'))
                            ->where('hari', date('d'))
                            ->get('tb_absen_wajah')
                            ->row();

        $dataAbsen = $this->getJamAbsen2($pegawai_id);

        if(!$dataAbsen){
            echo json_encode(false);
            return;
        }
        

        if($absen_wajah){
            $dataAbsen['updated_at'] = date("Y-m-d H:i:s");
            $this->db->where('id', $absen_wajah->id)->update('tb_absen_wajah', $dataAbsen);
        }else{
            $data = [
                'pegawai_id'        => $pegawai_id,
                'jenis_pegawai'     => $jenis_pegawai,
                'skpd_id'           => $skpd_id,
                'tahun'             => date("Y"),
                'bulan'             => date("m"),
                'hari'              => date("d"),
            ];
            $data = array_merge($data, $dataAbsen);
            $this->db->insert('tb_absen_wajah', $data);
        }
        
        echo json_encode(true);
        return;

    }
    
    public function test(){
        echo "<pre>";
        $data1 = ["a"=>1];
        $data2 = ["b"=>2];
        print_r(array_merge($data1, $data2));
        return;
    }
    
    private function getJamAbsen2($pegawai_id){

        $jam_kerja_pegawai = $this->db->where('pegawai_id', $pegawai_id)->get('tb_jam_kerja_pegawai')->row();
        if($jam_kerja_pegawai){
            $jam_kerja      = $this->db
                                   ->where('jam_kerja_id', $jam_kerja_pegawai->jam_kerja_id)
                                   ->where('hari', date('w'))
                                   ->get('tb_jam_kerja_meta')
                                   ->row();
        }else{
            $jam_kerja      = $this->db
                                   ->where('jam_kerja_id', 1)
                                   ->where('hari', date('w'))
                                   ->get('tb_jam_kerja_meta')
                                   ->row();
        }
        

        $now                    = strtotime(date("H:i:s"));
        $jam_awal_masuk         = strtotime($jam_kerja->jam_awal_masuk);
        $jam_akhir_masuk        = strtotime($jam_kerja->jam_akhir_masuk);
        $jam_awal_pulang        = strtotime($jam_kerja->jam_awal_pulang);
        $jam_akhir_pulang       = strtotime($jam_kerja->jam_akhir_pulang);
        $jam_awal_istirahat     = $jam_kerja->jam_awal_istirahat ? strtotime($jam_kerja->jam_awal_istirahat) : null;
        $jam_akhir_istirahat    = $jam_kerja->jam_akhir_istirahat ? strtotime($jam_kerja->jam_akhir_istirahat) : null;
        

        if($now >= $jam_awal_masuk && $now<=($jam_akhir_masuk+3600)){
            return ['jam_masuk'=>date("H:i:s", $now)];
        }
        if($jam_awal_istirahat && $jam_akhir_istirahat && $now >= $jam_awal_istirahat && $now<=$jam_akhir_istirahat){
            return ['jam_istirahat'=>date("H:i:s", $now)];
        }
        if($now >= ($jam_awal_pulang-3600) && $now<=$jam_akhir_pulang){
            return ['jam_pulang'=>date("H:i:s", $now)];
        }
        
        return false;        
	}

    /*
    Method Name : saveAbsen
    Route : /apiabsen/saveAbsen
    HTTP Method : POST
    Body : 
        pegawai_id : int
        opd_id : int
        jenis_absen : string
        keterangan : string | nullable
        file_absensi : base64
    */
    public function saveAbsen(){
        $jam = date("Y-m-d H:i:s");
        $img = $_POST['file_absensi'];
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
		$fileData = base64_decode($img);
        $file_absensi = 'file_absen/'.strtotime('now').'.png';
        file_put_contents($file_absensi,$fileData);

        $access_key         = rand(90000,99999)."-".substr(md5(time()), 0, 7);
        $this->db->insert("tb_absensi", [
            "pegawai_id"       => $_POST['pegawai_id'],
            "opd_id"           => $_POST['opd_id'],
            "jam"              => $jam,
            "jenis_absen"      => $_POST['jenis_absen'],
            "keterangan"       => isset($_POST['keterangan']) && $_POST['keterangan'] ? $_POST['keterangan'] : null,
            "status"           => isset($_POST['keterangan']) && $_POST['keterangan'] ? null : 1,
            "access_key"       => isset($_POST['keterangan']) && $_POST['keterangan'] ? $access_key : null,
            "file_absensi"     => $file_absensi
        ]);

        $id = $this->db->insert_id();
        $tanggal_berhasil = date('j F Y, H:i');
        echo json_encode([
            'status'            => 'success',
            'id'                => $id,
            'message'           => 'Absensi berhasil',
            'tanggal'           => $tanggal_berhasil
        ]);
        return;
    }
	
	/*
	Method Name : pushAbsen
	Route : /api/pushAbsen
	HTTP Method : POST
	Body :
		username : string
		file_absensi : base64
		user_id : number 
		jenis_pegawai : string
		nama : string
		skpd_id : string
		nama_opd : string
		jenis_absen : string
		keterangan : text
	*/
	public function pushAbsen(){
		// $fr_log = file_get_contents('fr_log.txt');
		$username = $_POST['username'];
        $img = $_POST['file_absensi'];
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
		$fileData = base64_decode($img);
		
		$projectId      = 'absensi-325704';# Your Google Cloud Platform project ID
        $storage        = new StorageClient(['projectId' => $projectId]); # Instantiates a client
        $bucketName     = 'file-absensi'; # The name for the new bucket
        $bucket         = $storage->bucket($bucketName);
        $all_files      = $bucket->objects([
                                'prefix' => 'file_model/'.str_replace(" ","",$username),
                                'fields' => 'items/name'
						]);
		$image_name = "";
		foreach ($all_files as $key => $file)
			$image_name = $file->name();
		
		$image = "https://storage.googleapis.com/file-absensi/".$image_name;
		$image = file_get_contents($image);
		$image = base64_encode($image);
		
		$face_compare_request = ['type'=>'object','srcImage'=>$image,'targetImage'=>$img];
        $face_recognition = $this->faceRecognition(json_encode($face_compare_request));
        $result = json_decode($face_recognition,true);
        if(empty($result['FaceMatches']))
        {
            // file_put_contents('fr_log.txt',$fr_log.$line,FILE_APPEND);
            echo json_encode([
                'status' => 'fail',
				'message' => 'Absensi gagal',
                'face_recognition' => $result 
            ]);
            return;
        }
        
		//saving
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
		$fileName = 'file_absensi/'.$username.'/'.$jam.'.png';
		
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
        $access_key         = rand(90000,99999)."-".substr(md5(time()), 0, 7);
        $this->db->insert("tb_absensi", [
            "pegawai_id"        => $_POST['user_id'],
            "jenis_pegawai"     => $_POST['jenis_pegawai'],
            "nama_pegawai"      => $_POST['nama'],
            "skpd_id"           => $_POST['skpd_id'],
            "nama_opd"          => $_POST['nama_opd'],
            "jam"               => $jam,
            "jenis_absen"       => $_POST['jenis_absen'],
            "keterangan"        => isset($_POST['keterangan']) && $_POST['keterangan'] ? $_POST['keterangan'] : null,
            "status"            => isset($_POST['keterangan']) && $_POST['keterangan'] ? null : 1,
            "access_key"        => isset($_POST['keterangan']) && $_POST['keterangan'] ? $access_key : null,
            "file_absensi"      => $fileName
        ]);
        
        $id = $this->db->insert_id();

        if(isset($_POST['keterangan']) && $_POST['keterangan']) {
                                
            $pegawai_id         = $_POST['user_id'];
            $jenis_pegawai      = $_POST['jenis_pegawai'];
            $pegawai            = $this->Pegawai_model->getPegawaiAtasan($pegawai_id, $jenis_pegawai);

            if(isset($pegawai['nama_pegawai'])){
                // $fileNameEncoded = preg_replace('/ /i', '%20', $fileName);
	            // $url             = 'https://storage.googleapis.com/file-absensi/'.$fileNameEncoded;
	            $url            = $this->Shortener_model->buaturl(base_url('byaccesskey/absenmanual/'.$id."/".$access_key));
                $pesan          = "[ABSENSI-NG] Ada permohonan Absen Luar Lokasi dari ".$pegawai['nama_pegawai'].". Konfirmasi melalui link ini ".$url;
                // $this->Sms_model->send($pegawai['no_hp_pegawai_atasan'], $pesan);
                $this->Notifikasi_model->send(array(
                              'user_id'         => $pegawai['pegawai_atasan_id'],
                              'jenis_user'      => $pegawai['jenis_pegawai_atasan'],
                              'user_name'       => $pegawai['nama_pegawai_atasan'],
                              'contents'        => $pesan,
                        ));

            }
        }

		$tanggal_berhasil = date('j F Y, H:i');
        echo json_encode([
            'status'            => 'success',
            'id'                => $id,
            'message'           => 'Absensi berhasil',
            'tanggal'           => $tanggal_berhasil,
            'face_recognition'  => $result 
        ]);
        return;
        
	}
	
	public function faceRecognition($data)
    {
        $curl = curl_init();
        
		//   CURLOPT_URL => "http://34.101.95.18/face-absen",
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://57x8w7bd20.execute-api.ap-southeast-2.amazonaws.com/egov/recognize/from-object",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $data,
          CURLOPT_HTTPHEADER => array(
            "content-type: application/json",
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
          return "cURL Error #:" . $err;
        } else {
          return $response;
        }
    }

    ////////////////////// LOGABSEN
    public function getLogAbsen(){
        if(
            !isset($_POST['bulan']) || 
            !isset($_POST['pegawai_id']) || 
            !isset($_POST['jenis_pegawai']) || 
            !isset($_POST['user_key']) || 
            !isset($_POST['pass_key']) || 
            !$_POST['bulan'] ||
            !$_POST['pegawai_id'] ||
            !$_POST['jenis_pegawai'] ||
            $_POST['user_key'] != "absensiAPI" ||
            $_POST['pass_key'] != "12345654321"        
        ){
            echo json_encode([
                "status"    => "gagal",
                "data"      => array()
            ]);
            return;
        }

        $pegawai_id      = $_POST['pegawai_id']; 
        $jenis_pegawai   = $_POST['jenis_pegawai']; 

        $begin = new DateTime(date("01-m-Y", strtotime("01-".$_POST['bulan'])));
        $end = new DateTime(date("t-m-Y", strtotime("01-".$_POST['bulan'])));
        $end->modify('+1 day');
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);
        $no = 1;

        $datas = array();
        $ik         = $this ->db
                            ->select('tb_izin_kerja.*, tb_izin_kerja_meta.*')
                            ->where('tb_izin_kerja.pegawai_id', $pegawai_id)
                            ->where('tb_izin_kerja.jenis_pegawai', $jenis_pegawai)
                            ->where("tb_izin_kerja.status", 1)
                            ->join('tb_izin_kerja_meta', 'tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')
                            ->get('tb_izin_kerja');

        $num_ik     = $ik->num_rows();
        $izin       = $ik->result();

        $hari_izin      = [];
        $file_izin      = [];
        $approver_izin  = [];
        
        if ($num_ik > 0) {
            foreach ($izin as $iz) {
                $begin2 = new DateTime($iz->tanggal_awal);
                $end2 = new DateTime($iz->tanggal_akhir);
                $end2->modify('+1 day');
                $interval2 = DateInterval::createFromDateString('1 day');
                $period2 = new DatePeriod($begin2, $interval2, $end2);
                foreach ($period2 as $dt2) {
                    if (isset($hari_izin[$dt2->format("Y-m-d")])) {
                        if ($hari_izin[$dt2->format("Y-m-d")] == "Sakit" || $hari_izin[$dt2->format("Y-m-d")] == "Izin") {
                            continue;
                        }
                    }
                    $hari_izin[$dt2->format("Y-m-d")]       = $iz->jenis_izin;
                    $file_izin[$dt2->format("Y-m-d")]       = $iz->file_izin;
                    $approver_izin[$dt2->format("Y-m-d")]   = $iz->aproved_by_nama;
                }
            }
        }
        
        $pegawais       = $jenis_pegawai == 'pegawai' ? $this->Pegawai_model->getPegawai($pegawai_id) : $this->Pegawai_model->getPegawaiTks($pegawai_id) ;
        $pegawai        = isset($pegawais[0]) ? $pegawais[0] : array();
        $gelarDepan     = isset($pegawai['gelar_depan']) && $pegawai['gelar_depan'] && $pegawai['gelar_depan']!=="" ? $pegawai['gelar_depan']."." : null;
        $gelarBelakang  = isset($pegawai['gelar_belakang']) && $pegawai['gelar_belakang'] && $pegawai['gelar_belakang']!="" ? " ".$pegawai['gelar_belakang'] : null;
        $nama_pegawai   = $gelarDepan.$pegawai['nama'].$gelarBelakang;

        $indexPegawai   = array_search($pegawai_id, array_column($pegawais, 'user_id'));
        $indexPegawai   = $indexPegawai!==false ? $indexPegawai : "none"; 
        $pg             = (isset($pegawais[$indexPegawai])  ? $pegawais[$indexPegawai] : ['username'=>'undefined']);

        $pegawaiMeta    = $jenis_pegawai=='pegawai' ? 
                                $this->db->where('pegawai_id', $pegawai_id)->get('tb_pegawai_meta')->row() :
                                $this->db->where('tks_id', $pegawai_id)->get('tb_tks_meta')->row();
        
        foreach ($period as $dt) {
            $upacaralibur = $this->db
                                 ->where('tanggal', $dt->format('Y-m-d'))
                                 ->get('tb_upacara_libur')->row();

            $izinkerja      = $this ->db
                                    ->select('tb_izin_kerja.*, tb_izin_kerja_meta.*')
                                    ->where('tb_izin_kerja.pegawai_id', $pegawai_id)
                                    ->where('tb_izin_kerja.jenis_pegawai', $jenis_pegawai)
                                    ->group_start()
                                        ->where('tb_izin_kerja_meta.tanggal_awal<=', $dt->format("Y-m-d"))
                                        ->where('tb_izin_kerja_meta.tanggal_akhir>=', $dt->format("Y-m-d"))
                                    ->group_end()
                                    ->where("tb_izin_kerja.status", 1)
                                    ->join('tb_izin_kerja_meta', 'tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')
                                    ->get('tb_izin_kerja')->row();

            $tanggalLog         = $this->hari[$dt->format("w")] . ", ".$dt->format("d") . " " . $this->bulan[(int) $dt->format("m")] . " " . $dt->format("Y");
            $data               = [
                                    "hari"          => $tanggalLog." ".(isset($upacaralibur->nama_hari) ?  $upacaralibur->nama_hari : null),
                                    "absensi"       => null,
                                    "izin_kerja"    => null,
                                ];
            if($izinkerja){
                $data['izin_kerja'] = [
                                        "jenis_izin"        => $izinkerja->jenis_izin,
                                        "lampiran"          => $izinkerja->file_izin,
                                        "disetujui_oleh"    => $izinkerja->aproved_by_nama,
                                    ];
                $datas[]            = $data;           
                continue;
            }

            $absensi = $this->db
                            ->where('pegawai_id', $pegawai_id)
                            ->where('jenis_pegawai', $jenis_pegawai)
                            ->where("DATE_FORMAT(jam,'%Y-%m-%d')", $dt->format("Y-m-d"))
                            ->where('status', 1)
                            ->order_by('id', 'asc')
                            ->get('tb_absensi')->result();
            
            
            $masuk                          = null;
            $istirahat                      = null;
            $selesai_istirahat              = null;
            $pulang                         = null;
            

            
            
            foreach($absensi as $abs){
                $jam        = $this->getJamAbsen($abs->jam, $abs->pegawai_id, $abs->jenis_pegawai, $abs->jenis_absen, isset($pegawaiMeta->guru) && $pegawaiMeta->guru=="Ya" ? true:false);
                if($abs->is_susulan=='Ya' && ($abs->jenis_absen=='Absen Masuk' || $abs->jenis_absen=='Absen Upacara' || $abs->jenis_absen=='Absen Senam')){
                    $masuk      = [
                                "waktu"          => "Susulan",
                                "lampiran"       => "https://storage.googleapis.com/file-absensi/".$abs->file_absensi,
                                "diluar_lokasi"  => null,
                                "ket"            => null,
                                "disetujui_oleh" => null,
                    ];
                    continue;
                    
                }else if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Istirahat'){
                    $istirahat      = [
                                "waktu"          => "Susulan",
                                "lampiran"       => "https://storage.googleapis.com/file-absensi/".$abs->file_absensi,
                                "diluar_lokasi"  => null,
                                "ket"            => null,
                                "disetujui_oleh" => null,
                    ];
                    continue;
    
                }else if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Selesai Istirahat'){
                    $selesai_istirahat  = [
                                "waktu"          => "Susulan",
                                "lampiran"       => "https://storage.googleapis.com/file-absensi/".$abs->file_absensi,
                                "diluar_lokasi"  => null,
                                "ket"            => null,
                                "disetujui_oleh" => null,
                    ];
                    continue;
    
                }else if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Pulang'){
                    $pulang      = [
                                "waktu"          => "Susulan",
                                "lampiran"       => "https://storage.googleapis.com/file-absensi/".$abs->file_absensi,
                                "diluar_lokasi"  => null,
                                "ket"            => null,
                                "disetujui_oleh" => null,
                    ];
                    continue;

                }
                $masuk      = isset($jam['jam_masuk']) && (!$masuk || $abs->jenis_absen=='Absen Upacara' || $abs->jenis_absen=='Absen Senam') ? [
                                "waktu"          => $jam['jam_masuk'],
                                "lampiran"       => "https://storage.googleapis.com/file-absensi/".$abs->file_absensi,
                                "diluar_lokasi"  => $abs->keterangan ? "Ya" : null,
                                "ket"            => $abs->keterangan ? $abs->keterangan : null,
                                "disetujui_oleh" => $abs->keterangan ? $abs->approved_by_nama : null,
                    ] : $masuk;
                $istirahat  = isset($jam['jam_istirahat']) && !$istirahat ? [
                                "waktu"          => $jam['jam_istirahat'],
                                "lampiran"       => "https://storage.googleapis.com/file-absensi/".$abs->file_absensi,
                                "diluar_lokasi"  => $abs->keterangan ? "Ya" : null,
                                "ket"            => $abs->keterangan ? $abs->keterangan : null,
                                "disetujui_oleh" => $abs->keterangan ? $abs->approved_by_nama : null,
                    ] : $istirahat;
                $selesai_istirahat  = isset($jam['jam_selesai_istirahat']) && !$selesai_istirahat ? [
                                "waktu"          => $jam['jam_selesai_istirahat'],
                                "lampiran"       => "https://storage.googleapis.com/file-absensi/".$abs->file_absensi,
                                "diluar_lokasi"  => $abs->keterangan ? "Ya" : null,
                                "ket"            => $abs->keterangan ? $abs->keterangan : null,
                                "disetujui_oleh" => $abs->keterangan ? $abs->approved_by_nama : null,
                    ] : $selesai_istirahat;
                $pulang     = isset($jam['jam_pulang']) && !$pulang ? [
                                "waktu"          => $jam['jam_pulang'],
                                "lampiran"       => "https://storage.googleapis.com/file-absensi/".$abs->file_absensi,
                                "diluar_lokasi"  => $abs->keterangan ? "Ya" : null,
                                "ket"            => $abs->keterangan ? $abs->keterangan : null,
                                "disetujui_oleh" => $abs->keterangan ? $abs->approved_by_nama : null,
                    ] : $pulang;

            }

            $data['absensi']    = [
                                    "masuk"             => $masuk,
                                    "istirahat"         => $istirahat,
                                    "selesai_istirahat" => $selesai_istirahat,
                                    "pulang"            => $pulang,
                                ];

            if((!$data['absensi'] && !$data['jenis_izin']) || (!$masuk && !$istirahat && !$selesai_istirahat && !$pulang)){
                continue;
            }

            $datas[]            = $data;
        }
        echo json_encode([
            "status"        => "berhasil",
            "nama_pegawai"  => $nama_pegawai,
            "data"          => $datas
        ]);
        return;
    }


    private function getJamAbsen($tanggal, $pegawai_id, $jenis_pegawai, $jenis_absen, $isGuru=false){
        $now                = strtotime($tanggal);
        $jamKerjaPegawai = $this->db->
                                select('tb_jam_kerja_pegawai_new.*, tb_jam_kerja_new.nama_jam_kerja')->
                                where('pegawai_id', $pegawai_id)->
                                where('jenis_pegawai', $jenis_pegawai)->
                                where('tanggal', date("Y-m-d", strtotime($tanggal)))->
                                join('tb_jam_kerja_new', 'tb_jam_kerja_new.id=tb_jam_kerja_pegawai_new.jam_kerja_id', 'left')->
                                get('tb_jam_kerja_pegawai_new')->row_array();
                                
        $jam_kerja          = $jamKerjaPegawai ? 
                            $this->db
                               ->where('id', $jamKerjaPegawai['jam_kerja_id'])
                               ->get('tb_jam_kerja_new')
                               ->row() : 
                            $this->db
                               ->where('jam_kerja_id', $isGuru ? 19 :1)
                               ->where('hari', date('N', $now))
                               ->get('tb_jam_kerja_meta')
                               ->row();

        if(!$jam_kerja) return [];
        
        $jam_awal_masuk                 = strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_awal_masuk);
        $jam_akhir_masuk                = strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_akhir_masuk);
        $jam_awal_pulang                = strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_awal_pulang);
        $jam_akhir_pulang               = strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_akhir_pulang);
        $jam_awal_istirahat             = $jam_kerja->jam_awal_istirahat ? strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_awal_istirahat) : null;
        $jam_akhir_istirahat            = $jam_kerja->jam_akhir_istirahat ? strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_akhir_istirahat) : null;
        $jam_awal_selesai_istirahat     = $jam_kerja->jam_awal_selesai_istirahat ? strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_awal_selesai_istirahat) : null;
        $jam_akhir_selesai_istirahat    = $jam_kerja->jam_akhir_selesai_istirahat ? strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_akhir_selesai_istirahat) : null;

        
        if($jenis_absen=='Absen Upacara'){
            return [
                        'jam_masuk'     => "Upacara",
                ];
        }

        if($jenis_absen=='Absen Senam'){
            return [
                        'jam_masuk'     => "Senam",
                ];
        }
        
        if($jenis_absen=='Absen Masuk' && $now >= ($jam_awal_masuk-60) && $now<=($jam_akhir_masuk+7260)){
            return [
                        'jam_masuk'     => date("H:i", $now),
                        // 'label'         => $this->_hitungTerlambatMasuk($now, $jam_akhir_masuk)
                ];
        }
        
        if($jenis_absen=='Absen Istirahat' && $now >= ($jam_awal_istirahat-7260) && $now <= ($jam_akhir_istirahat+60)){
            return [
                        'jam_istirahat'       => date("H:i", $now),
                ];
        }
        if($jenis_absen=='Absen Selesai Istirahat' && $now >= ($jam_awal_selesai_istirahat-60) && $now <= ($jam_akhir_selesai_istirahat+7260)){
            return [
                        'jam_selesai_istirahat'      => date("H:i", $now),
                ];
        }
        
        
        if($jenis_absen=='Absen Pulang' && $now >= ($jam_awal_pulang-7260) && $now<=($jam_akhir_pulang+60)){
            return [
                        'jam_pulang'    => date("H:i", $now),
                        // 'label'         => $this->_hitungPulangLebihAwal($now, $jam_awal_pulang)
                ];
        }
        
        return [];        
    }


    private function _hitungTerlambatMasuk($jam, $batasMasukAkhir)
    {
        if ($jam > ($batasMasukAkhir+7260)) return "TDHE1";
        if ($jam > ($batasMasukAkhir+5460)) return "TM4";
        if ($jam > ($batasMasukAkhir+3660)) return "TM3";
        if ($jam > ($batasMasukAkhir+1860)) return "TM2";
        if ($jam > ($batasMasukAkhir+60)) return "TM1";
    }
    
    private function _hitungPulangLebihAwal($jam, $batasPulangAwal)
    {
        if ($jam < ($batasPulangAwal-7260)) return "TDHE2";
        if ($jam < ($batasPulangAwal-5460)) return "PLA4";
        if ($jam < ($batasPulangAwal-3660)) return "PLA3";
        if ($jam < ($batasPulangAwal-1860)) return "PLA2";
        if ($jam < ($batasPulangAwal+60)) return "PLA1";
    }
    //////////////////////

    ////////////////////// Izin Kerja
    private function queryIzinKerjaNew($for){
        extract($_POST);
        $start_date     = isset($_POST['bulan']) ? date("Y-m-01", strtotime("01-".$_POST['bulan']))   : date("Y-m-01");
        $end_date       = isset($_POST['bulan']) ? date("Y-m-t", strtotime("01-".$_POST['bulan']))    : date("Y-m-t");
        $jenis_pegawai  = $_POST['jenis_pegawai'];
        $pegawai_id     = $_POST['pegawai_id'];

        if(isset($_POST['status']) && $_POST['status']){
            $statuss = [
                    "menunggu"  => null,
                    "ditolak"   => 0,
                    "disetujui" => 1,
                ];
            $this->db->where('tb_izin_kerja.status', $statuss[$status]);
        }

        if($for!='saya' && $for!='bawahan'){
            return array();
        }

        if($for=="saya"){
            $this->db->where('tb_izin_kerja.pegawai_id', $pegawai_id);
            $this->db->where('tb_izin_kerja.jenis_pegawai', $jenis_pegawai);

        }else{
            $pegawai        = $this->Pegawai_model->getPegawaiByPegawaiAtasan($pegawai_id, $jenis_pegawai);
            if(count($pegawai)==0) return array();
            
            $list      = array();
            $this->db->group_start();
                foreach($pegawai as $pegawai){
                    $this->db->or_group_start();
                        $this->db->where('tb_izin_kerja.pegawai_id', $pegawai['pegawai_id']);
                        $this->db->where('tb_izin_kerja.jenis_pegawai', $pegawai['jenis_pegawai']);
                    $this->db->group_end();
                }
            $this->db->group_end();
        }
        
        $this->db->select('tb_izin_kerja.id, tb_izin_kerja_meta.id izinkerja_id, tb_izin_kerja.*, tb_izin_kerja_meta.*, tb_tks_meta.tks_id, tb_pegawai_meta.pegawai_id pegawai_meta_pegawai_id')->
                    where("DATE_FORMAT(tb_izin_kerja.created_at,'%Y-%m-%d')>=", $start_date)->
                    where("DATE_FORMAT(tb_izin_kerja.created_at,'%Y-%m-%d')<=", $end_date)->
                    join('tb_izin_kerja_meta','tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')->
                    join('tb_tks_meta','tb_tks_meta.tks_id=tb_izin_kerja.pegawai_id', 'left')->
                    join('tb_pegawai_meta','tb_pegawai_meta.pegawai_id=tb_izin_kerja.pegawai_id', 'left')->
                    order_by('tb_izin_kerja_meta.id', 'desc');

        
        return $this->db->get('tb_izin_kerja')->result_array();
    }

    function getIzinKerja($is_bawahan=false){
        extract($_POST);

        if(
            !isset($_POST['user_key']) ||
            !isset($_POST['pass_key']) ||
            !isset($_POST['pegawai_id']) ||
            !isset($_POST['jenis_pegawai']) ||
            !$_POST['pegawai_id'] ||
            !$_POST['jenis_pegawai'] ||
            $_POST['user_key'] != "absensiAPI" ||
            $_POST['pass_key'] != "12345654321" ||
            ($is_bawahan && $is_bawahan!='bawahan') ||
            (isset($_POST['status']) && $_POST['status'] && ($_POST['status'] != "menunggu" && $_POST['status'] != "disetujui" && $_POST['status'] != "ditolak"))
        ) { echo json_encode(["status" => "gagal", "data" => null]); return; }
        
        // $lists          = $is_bawahan ? $this->queryIzinKerjaNew('bawahan') : $this->queryIzinKerjaNew('saya');
        $lists = [];
        if($is_bawahan)
        {
            $bawahan = $this->db->where('pegawai_atasan_id',$pegawai_id)->get('tb_pegawai_atasan')->result_array();
            $bawahan_ids = [];
            foreach($bawahan as $b)
                $bawahan_ids[] = $b['pegawai_id'];
            $this->db->select('tb_izin_kerja.*, tb_pegawai.nama as nama_pegawai, tb_opd.id as skpd_id, tb_opd.nama_opd as nama_skpd')->where_in('tb_izin_kerja.pegawai_id',$bawahan_ids);
            if(isset($bulan))
                $this->db->like('tb_izin_kerja.tanggal_awal',$bulan)->or_like('tb_izin_kerja.tanggal_akhir',$bulan);

            $this->db->join('tb_pegawai','tb_pegawai.id=tb_izin_kerja.pegawai_id')->join('tb_opd','tb_opd.id=tb_izin_kerja.opd_id');
            $lists = $this->db->get('tb_izin_kerja')->result_array();
        }
        else
        {
            $this->db->select('tb_izin_kerja.*, tb_pegawai.nama as nama_pegawai, tb_opd.id as skpd_id, tb_opd.nama_opd as nama_skpd')->where('tb_izin_kerja.pegawai_id',$pegawai_id);
            if(isset($bulan))
                $this->db->like('tb_izin_kerja.tanggal_awal',$bulan)->or_like('tb_izin_kerja.tanggal_akhir',$bulan);

            $this->db->join('tb_pegawai','tb_pegawai.id=tb_izin_kerja.pegawai_id')->join('tb_opd','tb_opd.id=tb_izin_kerja.opd_id');
            $lists = $this->db->get('tb_izin_kerja')->result_array();
        }

        // $q = $this->db->last_query();;

        function getTanggal($tanggal, $tanggaldanwaktu=false){
            $totime = strtotime($tanggal);
            $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
            $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            $tanggaldanwaktu = $tanggaldanwaktu ? " " . date('H:i', $totime)." WIB" : null;
            return $hari[date("w", $totime)] . ", " . date('d', $totime)." " . $bulan[date('n', $totime)].date(' Y', $totime).$tanggaldanwaktu;
        }


        $datas      = array();
        $no         = 1;
        foreach ($lists as $ik) {

            $d['izinkerja_id']      = $ik['id'];
            $d['nama_pegawai']      = $ik['nama_pegawai'];
            $d['jenis_izin']        = $ik['jenis_izin'];
            $d['skpd_id']           = $ik['skpd_id'];
            $d['nama_skpd']         = $ik['nama_skpd'];
            $d['tanggal_awal']      = getTanggal($ik['tanggal_awal']);
            $d['tanggal_akhir']     = getTanggal($ik['tanggal_akhir']);
            $d['lampiran']          = base_url().$ik['file_izin'];
            $d['status']            = ($ik['status']==null ? 'Menunggu' : ($ik['status']==1 ? 'Disetujui' : 'Ditolak'));
            if($is_bawahan=="bawahan") { 
                $d['access_key']        = $ik['access_key'];
            }
            if($ik['aproved_by'])
            {
                $approved = $this->db->where('id',$ik['aproved_by'])->get('tb_pegawai')->row();
                $d['aproved_by_nama']   = $approved->nama;
            }
            else
                $d['aproved_by_nama']   = '';
            
            $d['aproved_at']        = '';
            $d['dibuat_pada']       = getTanggal($ik['created_at'], true);
            $datas[]                = $d; 
        }

        echo json_encode([
				"status"            => "berhasil",
                // "query" => $q,
				"data"              => $datas
			]);
        return;
    }

    public function cekIzin(){
        
        if (
            isset($_POST['tanggal_awal']) && 
            isset($_POST['tanggal_akhir']) &&
            isset($_POST['pegawai_id']) &&
            isset($_POST['jenis_pegawai'])
        ) {
            extract($_POST);
            $tanggal_awal   = date("Y-m-d", strtotime($tanggal_awal));
            $tanggal_akhir  = date("Y-m-d", strtotime($tanggal_akhir));
            $data   = $this->db->
                             select('tb_izin_kerja.id izin_kerja_id, tb_izin_kerja.*, tb_izin_kerja_meta.*')->
                             where('tb_izin_kerja.pegawai_id', $pegawai_id)->
                             where('tb_izin_kerja.jenis_pegawai', $jenis_pegawai)->
                             group_start()->
                                where('tb_izin_kerja.status', null)->
                                or_where('tb_izin_kerja.status', 1)->
                             group_end()->
                             group_start()->
                                or_group_start()->
                                    where('tb_izin_kerja_meta.tanggal_awal>=', $tanggal_awal)->
                                    where('tb_izin_kerja_meta.tanggal_akhir<=', $tanggal_awal)->
                                group_end()->
                                or_group_start()->
                                    where('tb_izin_kerja_meta.tanggal_awal>=', $tanggal_akhir)->
                                    where('tb_izin_kerja_meta.tanggal_akhir<=', $tanggal_akhir)->
                                group_end()->
                             group_end()->
                             order_by('tb_izin_kerja.id', 'desc')->
                             join('tb_izin_kerja_meta','tb_izin_kerja_meta.id=tb_izin_kerja.meta_id', 'left')->
                             get('tb_izin_kerja')->result();

            if(count($data)>0) {
                $html[] = array();
                foreach($data as $dt){
                    $html[] = $dt->jenis_izin." ".$dt->tanggal_awal." s/d ".$dt->tanggal_awal;
                }
                $html = implode(" dan ", $html);
                echo json_encode([
                    "status"    => "berhasil",
                    "pesan"     => "Izin kerja tidak tersedia, ada beberapa izin kerja yang sudah ada yaitu ".$html
                ]);
                return;
            }
        }

        echo json_encode([
            "status"    => "berhasil",
            "pesan"     => "Tanggal izin kerja tersedia!"
        ]);
        return;
    }
    
    public function buatIzin()
    {
        if (
            !isset($_POST['user_key']) ||
            !isset($_POST['pass_key']) ||
            !isset($_POST['tanggal_awal']) || 
            !isset($_POST['tanggal_akhir']) ||
            !isset($_POST['pegawai_id']) ||
            !isset($_POST['jenis_pegawai']) ||
            !isset($_POST['jenis_izin']) ||
            $_POST['user_key'] != "absensiAPI" ||
            $_POST['pass_key'] != "12345654321"
        ) {
            echo json_encode([
                "status"    => "gagal",
                "pesan"     => "Periksa kembali form anda!",
            ]);
            return;
        }
        extract($_POST);
        $pegawai = $this->db->where('id',$pegawai_id)->get('tb_pegawai')->row();

        if($_FILES['lampiran']['name']){
            $data       = file_get_contents($_FILES['lampiran']['tmp_name']);
            $fileData   = $data;
            $fileName   = 'izin_kerja/'.$pegawai_id.'-'.time()."_".$_FILES['lampiran']['name'];
        }else if(isset($file_izin) && $file_izin){
            $img        = $file_izin;
            $img        = str_replace('data:image/jpeg;base64,', '', $img);
            $img        = str_replace(' ', '+', $img);
            $fileData   = base64_decode($img);
            $fileName   = 'izin_kerja/'.$pegawai_id.'-'.time().".jpg";
        }
        
        file_put_contents($fileName, $fileData);

        if(!isset($fileData)){
            echo json_encode([
                "status"    => "gagal",
                "pesan"     => "Gagal mengajukan izin kerja, silahkan periksa lampiran izin kerja!",
            ]);
            return;
        }

        $access_key         = rand(90000,99999)."-".substr(md5(time()), 0, 7);
        $data = [
            "pegawai_id"   => $pegawai_id,
            "opd_id"       => $pegawai->opd_id,
            "tanggal_awal" => $tanggal_awal,
            "tanggal_akhir" => $tanggal_akhir,
            "jenis_izin" => $jenis_izin,
            "file_izin" => $fileName,
            "access_key" => $access_key,
        ];
        $this->db->insert('tb_izin_kerja', $data);

        // $pegawai            = $this->Pegawai_model->getPegawaiAtasan($pegawai_id, $jenis_pegawai);

        // if(isset($pegawai['nama_pegawai'])){
        //     // $pesan          = "*[ABSENSI-NG]*\n\nAda permohonan izin *".$this->input->post('jenis_izin', true)."* dari *".$pegawai['nama_pegawai']."*.\n\n*Lampiran Izin :*\n".$urlFile."\n\n*Setujui dengan tap link ini :*\n".base_url('byaccesskey/setujuiizinkerja/'.$meta_id."/".$access_key)."\n\n*Tolak dengan tap link ini:*\n".base_url('byaccesskey/tolakizinkerja/'.$meta_id."/".$access_key);
        //     // $this->Sms_model->send($pegawai['no_hp_pegawai_atasan'], $pesan);
            
        //     $url            = $this->Shortener_model->buaturl(base_url('byaccesskey/izinkerja/'.$meta_id."/".$access_key));
        //     $pesan          = "[ABSENSI-NG] Ada permohonan izin ".$this->input->post('jenis_izin', true)." dari ".$pegawai['nama_pegawai'].". Konfirmasi melalui link ini : ".$url;
        //     $this->Notifikasi_model->send(array(
        //                   'user_id'         => $pegawai['pegawai_atasan_id'],
        //                   'jenis_user'      => $pegawai['jenis_pegawai_atasan'],
        //                   'user_name'       => $pegawai['nama_pegawai_atasan'],
        //                   'contents'        => $pesan,
        //                   'url'             => "https://layanan.labura.go.id",
        //             ));

        // }

        echo json_encode([
            "status"    => "berhasil",
            "pesan"     => "Izin Kerja baru telah ditambahkan, silahkan tunggu verifikasi selanjutnya!",
        ]);
        return;
    }

    public function setujuiizinkerja(){
        if (
            !isset($_POST['user_key']) ||
            !isset($_POST['pass_key']) ||
            $_POST['user_key'] != "absensiAPI" ||
            $_POST['pass_key'] != "12345654321" ||
            !isset($_POST['izinkerja_id']) ||
            !isset($_POST['pegawai_id']) ||
            !isset($_POST['accesskey']) 
        ) {
            echo json_encode([
                "status"    => "gagal",
                "pesan"     => "Gagal menghubungkan!",
            ]);
            return;
        }
        extract($_POST);
        $this->db->where('id',$izinkerja_id)->where('access_key',$accesskey)->update('tb_izin_kerja',[
            'status' => 1,
            'aproved_by' => $pegawai_id
        ]);

        echo json_encode([
            "status"    => "berhasil",
            "pesan"     => "Berhasil disetujui!",
        ]);
        return;
    }
    public function tolakizinkerja(){
        if (
            !isset($_POST['user_key']) ||
            !isset($_POST['pass_key']) ||
            $_POST['user_key'] != "absensiAPI" ||
            $_POST['pass_key'] != "12345654321" ||
            !isset($_POST['izinkerja_id']) ||
            !isset($_POST['pegawai_id']) ||
            !isset($_POST['accesskey']) 
        ) {
            echo json_encode([
                "status"    => "gagal",
                "pesan"     => "Gagal menghubungkan!",
            ]);
            return;
        }

		extract($_POST);
		$this->db->where('id',$izinkerja_id)->where('access_key',$accesskey)->update('tb_izin_kerja',[
            'status' => 0,
            'aproved_by' => $pegawai_id
        ]);

		echo json_encode([
			"status"    => "berhasil",
			"pesan"     => "Berhasil ditolak!",
		]);
		return;
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

    ////////////////////// End Izin Kerja

    ////////////////////// ABSEN LUAR LOKASI
    private function queryAbsenLuarLokasi($for){
        extract($_POST);

        $start_date     = isset($_POST['bulan']) ? date("Y-m-01", strtotime("01-".$_POST['bulan']))   : date("Y-m-01");
        $end_date       = isset($_POST['bulan']) ? date("Y-m-t", strtotime("01-".$_POST['bulan']))    : date("Y-m-t");
        $jenis_pegawai  = $_POST['jenis_pegawai'];
        $pegawai_id     = $_POST['pegawai_id'];

        if(isset($_POST['status']) && $_POST['status']){
            $statuss = [
                    "menunggu"  => null,
                    "ditolak"   => 0,
                    "disetujui" => 1,
                ];
            $this->db->where('tb_absensi.status', $statuss[$status]);
        }

        if($for!='saya' && $for!='bawahan'){
            return array();
        }

        if($for=="saya"){
            $this->db->where('tb_absensi.pegawai_id', $pegawai_id);
            $this->db->where('tb_absensi.jenis_pegawai', $jenis_pegawai);

        }else{
            $pegawai        = $this->Pegawai_model->getPegawaiByPegawaiAtasan($pegawai_id, $jenis_pegawai);
            if(count($pegawai)==0) return array();
            
            $this->db->group_start();
                foreach($pegawai as $pegawai){
                    $this->db->or_group_start();
                        $this->db->where('tb_absensi.pegawai_id', $pegawai['pegawai_id']);
                        $this->db->where('tb_absensi.jenis_pegawai', $pegawai['jenis_pegawai']);
                    $this->db->group_end();
                }
            $this->db->group_end();
        }


        $query  = $this->db->
                        select('
                                tb_absensi.id absensi_id, 
                                tb_absensi.*, 
                                tb_tks_meta.tks_id, 
                                tb_tks_meta.nik, 
                                tb_tks_meta.nama nama_tks, 
                                tb_pegawai_meta.nip, 
                                tb_pegawai_meta.nama pegawai_meta_nama_pegawai, 
                                tb_pegawai_meta.pegawai_id pegawai_meta_pegawai_id
                        ')->
                        group_start()->
                            where('tb_absensi.keterangan!=', null)->
                            where('tb_absensi.keterangan!=', "")->
                        group_end()->
                        where("DATE_FORMAT(tb_absensi.jam,'%Y-%m-%d')>=", $start_date)->
                        where("DATE_FORMAT(tb_absensi.jam,'%Y-%m-%d')<=", $end_date)->
                        join('tb_tks_meta','tb_tks_meta.tks_id=tb_absensi.pegawai_id', 'left')->
                        join('tb_pegawai_meta','tb_pegawai_meta.pegawai_id=tb_absensi.pegawai_id', 'left')->
                        order_by('tb_absensi.jam', 'desc');

            return $this->db->get('tb_absensi')->result_array();
    }

    public function getAbsenLuarLokasi($is_bawahan=false){
        extract($_POST);

        if(
            !isset($_POST['user_key']) ||
            !isset($_POST['pass_key']) ||
            !isset($_POST['pegawai_id']) ||
            !isset($_POST['jenis_pegawai']) ||
            !$_POST['pegawai_id'] ||
            !$_POST['jenis_pegawai'] ||
            $_POST['user_key'] != "absensiAPI" ||
            $_POST['pass_key'] != "12345654321" ||
            ($is_bawahan && $is_bawahan!='bawahan') ||
            (isset($_POST['status']) && $_POST['status'] && ($_POST['status'] != "menunggu" && $_POST['status'] != "disetujui" && $_POST['status'] != "ditolak"))
        ) { echo json_encode(["status" => "gagal", "data" => null]); return; }
        
        $lists          = $is_bawahan ? $this->queryAbsenLuarLokasi('bawahan') : $this->queryAbsenLuarLokasi('saya');

        function getTanggal($tanggal, $tanggaldanwaktu=false){
            $totime = strtotime($tanggal);
            $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
            $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            $tanggaldanwaktu = $tanggaldanwaktu ? " " . date('H:i', $totime)." WIB" : null;
            return $hari[date("w", $totime)] . ", " . date('d', $totime)." " . $bulan[date('n', $totime)].date(' Y', $totime).$tanggaldanwaktu;
        }


        $data           = array();
        foreach ($lists as $field) {

            $row    = array();
            $row['absensi_id']      = $field['absensi_id'];
            $row['nama_pegawai']    = $field['nama_pegawai'] ? $field['nama_pegawai'] : ($field['pegawai_meta_nama_pegawai'] ? $field['pegawai_meta_nama_pegawai'] : $field['nama_tks']);
            $row['nip']             = $field['nip'] ? $field['nip'] : $field['nik'];
            $row['nama_opd']        = $field['nama_opd'];
            $row['jenis_absen']     = $field['jenis_absen'];
            $row['keterangan']      = $field['keterangan'];
            $row['hari_tanggal']    = $this->hari[date("w", strtotime($field['jam']))] . ", " . date('d F Y', strtotime($field['jam']));
            $row['jam']             = date('H:i', strtotime($field['jam']));
            $row['link_absensi']    = 'https://storage.googleapis.com/file-absensi/'.$field['file_absensi'];
            $row['status']          = $status;
            $row['status']            = ($field['status']==null ? 'Menunggu' : ($field['status']==1 ? 'Disetujui' : 'Ditolak'));
            if($is_bawahan=="bawahan") { 
                $row['access_key']        = $field['access_key'];
            }
            $row['aproved_by_nama']   = $field['approved_by_nama'];
            $row['aproved_at']        = getTanggal($field['approved_at'], true);
            $row['dibuat_pada']       = getTanggal($field['created_at'], true);

            $data[] = $row;
            $no++;

        }

        
        echo json_encode([
				"status"            => 'berhasil',
				"data"              => $data
			]);
        return;

    }    
    
    
    public function setujuiabsenluarlokasi(){
        if (
            !isset($_POST['user_key']) ||
            !isset($_POST['pass_key']) ||
            $_POST['user_key'] != "absensiAPI" ||
            $_POST['pass_key'] != "12345654321" ||
            !isset($_POST['absensi_id']) ||
            !isset($_POST['accesskey']) 
        ) {
            echo json_encode([
                "status"    => "gagal",
                "pesan"     => "Gagal menghubungkan!",
            ]);
            return;
        }

		extract($_POST);
        $id = $absensi_id;
        $absenmanual = $this->db->
                            where('access_key', $accesskey)->
                            where('id', $id)->
                            where('status', null)->
                            get('tb_absensi')->row();

        if(!$absenmanual){
            echo json_encode([
    				"status"            => 'gagal',
    				"pesan"             => 'Gagal mengambil informasi, mohon periksa kembali data absen luar lokasi!'
    			]);
            return;
        }

        // $pegawais           = $this->Pegawai_model->getPegawai();
        // $tkss               = $this->Pegawai_model->getPegawaiTks();
        // $pegawai            = $this->generatePegawai($absenmanual->pegawai_id, $absenmanual->jenis_pegawai, $pegawais, $tkss);
        // $this->Sms_model->send(isset($pegawai['no_hp']) ? $pegawai['no_hp'] : null, $pesan);

        $pesan              = "[ABSENSI-NG] Permohonan Absen Luar Lokasi ".$absenmanual->keterangan." Anda telah disetujui.";
        $this->Notifikasi_model->send(array(
                      'user_id'         => $absenmanual->pegawai_id,
                      'jenis_user'      => $absenmanual->jenis_pegawai,
                      'user_name'       => $absenmanual->nama_pegawai,
                      'contents'        => $pesan,
                ));



        $atasan             = $this->Pegawai_model->getPegawaiAtasan($absenmanual->pegawai_id, $absenmanual->jenis_pegawai);

        $this->db->where('id', $id)->update('tb_absensi', [
                "status"            => 1,
                "approved_by"       => isset($atasan['pegawai_atasan_id']) ? $atasan['pegawai_atasan_id'] : null,
                "approved_by_nama"  => isset($atasan['nama_pegawai_atasan']) ? $atasan['nama_pegawai_atasan'] : "Unknown",
                'approved_at'        => date("Y-m-d H:i:s")
            ]);

        echo json_encode([
    			"status"            => 'berhasil',
    			"pesan"             => 'Berhasil disetujui!'
    		]);
        return;

    }
    
    public function tolakabsenluarlokasi(){
        if (
            !isset($_POST['user_key']) ||
            !isset($_POST['pass_key']) ||
            $_POST['user_key'] != "absensiAPI" ||
            $_POST['pass_key'] != "12345654321" ||
            !isset($_POST['absensi_id']) ||
            !isset($_POST['accesskey']) 
        ) {
            echo json_encode([
                "status"    => "gagal",
                "pesan"     => "Gagal menghubungkan!",
            ]);
            return;
        }

		extract($_POST);
        $id = $absensi_id;

        $absenmanual = $this->db->
                            where('access_key', $accesskey)->
                            where('id', $id)->
                            where('status', null)->
                            get('tb_absensi')->row();

        if(!$absenmanual){
            echo json_encode([
    				"status"            => 'gagal',
    				"pesan"             => 'Gagal mengambil informasi, mohon periksa kembali data absen luar lokasi!'
    			]);
            return;
        }

        // $pegawais           = $this->Pegawai_model->getPegawai();
        // $tkss               = $this->Pegawai_model->getPegawaiTks();
        // $pegawai            = $this->generatePegawai($absenmanual->pegawai_id, $absenmanual->jenis_pegawai, $pegawais, $tkss);
        // $this->Sms_model->send(isset($pegawai['no_hp']) ? $pegawai['no_hp'] : null, $pesan);

        $pesan              = "[ABSENSI-NG] Permohonan Absen Luar Lokasi ".$absenmanual->keterangan." Anda telah ditolak.";
        $this->Notifikasi_model->send(array(
                  'user_id'         => $absenmanual->pegawai_id,
                  'jenis_user'      => $absenmanual->jenis_pegawai,
                  'user_name'       => $absenmanual->nama_pegawai,
                  'contents'        => $pesan,
            ));


        $atasan             = $this->Pegawai_model->getPegawaiAtasan($absenmanual->pegawai_id, $absenmanual->jenis_pegawai);
        $this->db->where('id', $id)->update('tb_absensi', [
                "status"            => 0,
                "approved_by"       => isset($atasan['pegawai_atasan_id']) ? $atasan['pegawai_atasan_id'] : null,
                "approved_by_nama"  => isset($atasan['nama_pegawai_atasan']) ? $atasan['nama_pegawai_atasan'] : "Unknown",
                'approved_at'        => date("Y-m-d H:i:s")
            ]);

        echo json_encode([
    			"status"            => 'berhasil',
    			"pesan"             => 'Berhasil ditolak!'
    		]);
        return;
    }

    ////////////////////// END ABSEN LUAR LOKASI
	public function getJabatanPenghasilan(){
        if (
            !isset($_POST['user_key']) ||
            !isset($_POST['pass_key']) ||
            $_POST['user_key'] != "absensiAPI" ||
            $_POST['pass_key'] != "12345654321"
        ) {
            echo json_encode([
                "status"    => "gagal",
                "pesan"     => "Gagal menghubungkan!",
            ]);
            return;
        }
		echo json_encode([
			"status"    => "berhasil",
			"data"     	=> $this->db->order_by('nama_jabatan', 'asc')->get('tb_jabatan_penghasilan_new')->result(),
		]);
		return;

	} 


}
