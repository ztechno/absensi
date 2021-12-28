<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'assets/vendors/jwt/autoload.php';
use Firebase\JWT\JWT;

class Api extends CI_Controller {
	public function __construct(){
        parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
		$this->load->model(['Api_model', 'Sms_model']);
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");

    }

    public function getSlideShow()
    {
        $slide = $this->db->get('tb_slideshow')->result_array();
        echo json_encode($slide);
        return;
    }
    
    private function tokenData($token, $user_id){
        return $this->db->where('token', $token)
                        ->where('user_id', $user_id)
                        ->where('status', 1)
                        ->get('tb_token')
                        ->row();
    }

    public function getToken(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(false);
            return;
        }

        if(isset($_POST['token'])&&isset($_POST['user_id'])){
            extract($_POST);

            $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();
            
            if(!$api){ echo json_encode(false);return;}

            $token       = $this->tokenData($token, $user_id);

            if(!$token){ echo json_encode(false);return;}

            echo json_encode($token);
            return;
        }
        echo json_encode(false);
        return;

    }

    public function getWebsiteLogo(){
        if(isset($_POST['user_key']) && isset($_POST['pass_key'])){
            extract($_POST);
            $api        = $this->db->select('tb_api.website_id, tb_websites.nama_website, tb_websites.logo')->
                                     where('tb_api.user_key', $user_key)->
                                     where('tb_api.pass_key', $pass_key)->
                                     join('tb_websites', 'tb_websites.id=tb_api.website_id', 'left')->
                                     get('tb_api')->row();
            
            if($api){
                echo json_encode($api->logo);
                return;            
            }
            
        }
        
        echo json_encode(false);
        return;
    }

    // APP Absen    ---------------------------------------------------------------------------------------------------------------------------------
    public function cekToken(){
        $headers = getallheaders();
        if(!isset($headers['Authorization'])){
            echo json_encode(["status"=>"gagal"]);
            return;
            
        }
        
        $SIMPEG      = $this->load->database('otherdb', TRUE);
        $Authorization = $headers['Authorization'];
        $token = $this->db->where('token', $Authorization)
                        ->where('status', 1)
                        ->get('tb_token')
                        ->row();

        if(!$token){
            echo json_encode(["status"=>"gagal"]);
            return;
        }

        if($token->jenis_pegawai=='pegawai'){
            $user       = $SIMPEG->select('pegawai.*, skpd.id_skpd skpd_id, skpd.nama_skpd')
                                 ->where('pegawai.id_pegawai', $token->user_id)
                                 ->join('skpd', 'skpd.id_skpd=pegawai.id_skpd', 'left')
                                 ->get('pegawai')
                                 ->row();

            if(!$user){
                echo json_encode(["status"=>"gagal"]);
                return;
            }

            $user->nama_pegawai = ($user->gelar_depan && $user->gelar_depan!="" ? $user->gelar_depan.". " : null).$user->nama_pegawai.($user->gelar_belakang && $user->gelar_belakang!="" ? ", ".$user->gelar_belakang : null);
    
            $data = [
                'user_id'       => $user->id_pegawai,
                'nama'          => $user->nama_pegawai,
                'opd_id'        => $user->skpd_id,
                'nama_opd'      => $user->nama_skpd,
                'start_token'   => $token->start_token,
                'jenis_pegawai' => 'pegawai',
                'token'         => $token->token
            ];

            echo json_encode([
                                "status"    =>"berhasil",
                                "data"      => $data
            ]);
            return;


        }else{
            $user       = $SIMPEG->select('tb_pegawai_tks.*, skpd.id_skpd skpd_id, skpd.nama_skpd')
                                ->where('tb_pegawai_tks.id', $token->user_id)
                                ->join('skpd', 'skpd.id_skpd=tb_pegawai_tks.skpd_id', 'left')
                                ->get('tb_pegawai_tks')
                                ->row();

            if(!$user){
                echo json_encode(["status"=>"gagal"]);
                return;
            }

            $data = [
                'user_id'       => $user->id,
                'nama'          => $user->nama_tks,
                'opd_id'        => $user->skpd_id,
                'nama_opd'      => $user->nama_skpd,
                'start_token'   => $token->start_token,
                'jenis_pegawai' => 'tks',
                'token'         => $token->token
            ];
            echo json_encode([
                                "status"    =>"berhasil",
                                "data"      => $data
            ]);
            return;
            
        }
        
        if(!$user){
            echo json_encode(["status"=>"gagal"]);
            return;
        }


        
    }

    public function cekabsenupacara(){
        $upacara    = $this->db->where('tanggal', date('Y-m-d'))->where('kategori', 'upacara')->get('tb_upacara_libur')->row();
        if($upacara){
            echo json_encode([
                'status'    => 'berhasil',
                'pesan'     => $upacara->nama_hari,
            ]);
            return;
        }
        echo json_encode([
            'status'    => 'gagal',
            'pesan'     => 'Tidak ada upacara hari ini!'
        ]);
        return;
    }

    public function cekabsensenam(){
        if(date('N')==5){
            echo json_encode([
                'status'    => 'berhasil',
                'pesan'     => 'Absen Senam Tersedia',
            ]);
            return;
        }
        echo json_encode([
            'status'    => 'gagal',
            'pesan'     => 'Tidak ada senam hari ini!'
        ]);
        return;
    }


	public function ubahpassword(){
        error_reporting(0);
        if(!isset($_POST['token'])){
            echo json_encode([
				"status"	=> "gagal",
				"pesan"		=> "Parameter Token tidak ada !"
			]);
            return;
        }

		if(!isset($_POST['new_password']) && !isset($_POST['nomor_wa'])){
            echo json_encode([
				"status"	=> "gagal",
				"pesan"		=> "Tidak ada perubahan yang dilakukan !"
			]);
            return;
        }

        $SIMPEG     	= $this->load->database('otherdb', TRUE);
        $token 			= $this->db->where('token', $_POST['token'])
									->where('status', 1)
									->get('tb_token')
									->row();

        if(!$token){
            echo json_encode([
				"status"	=> "gagal",
				"pesan"		=> "Silahkan login terlebih dahulu!"		
			]);
            return;
        }

        $user_id 			= $token->user_id;
        $jenis_pegawai 		= $token->jenis_pegawai;

        if($jenis_pegawai == 'pegawai'){
            $cek_data               = $SIMPEG->select('pegawai.*, skpd.id_skpd skpd_id, skpd.nama_skpd')->
											   where('pegawai.id_pegawai', $user_id)->
											   join('skpd', 'skpd.id_skpd=pegawai.id_skpd', 'left')->
											   get('pegawai')->row();
            $pegawai                = $cek_data;
            $gelarDepan             = $pegawai->gelar_depan && $pegawai->gelar_depan!="" ? $pegawai->gelar_depan.". " : null;
            $gelarBelakang          = $pegawai->gelar_belakang && $pegawai->gelar_belakang!="" ? ", ".$pegawai->gelar_belakang : null;
            $cek_data->nama 		= $gelarDepan.$pegawai->nama_pegawai.$gelarBelakang;
        }else{
            $cek_data               = $SIMPEG->select('tb_pegawai_tks.*, skpd.id_skpd skpd_id, skpd.nama_skpd')->
											   where('tb_pegawai_tks.id', $user_id)->
											   join('skpd', 'skpd.id_skpd=tb_pegawai_tks.skpd_id', 'left')->
											   get('tb_pegawai_tks')->row();
			$cek_data->nama			= $cek_data->nama_tks;
        }

		if(!$cek_data){
            echo json_encode([
				"status"	=> "gagal",
				"pesan"		=> "Maaf data tidak ditemukan !"		
			]);
            return;
		}
        
       	$datameta =  $this->db->
                    		where('pegawai_id', $user_id)->
                    		where('jenis_pegawai', $jenis_pegawai)->
                        	get('tb_pegawai_meta')->row();
	
		if(
            isset($_POST['new_password']) && 
            $_POST['new_password'] != null && 
            $_POST['new_password'] != "null" && 
            $_POST['new_password'] != "" && 
            !empty($_POST['new_password']) && 
            strlen($_POST['new_password'])<6){
            echo json_encode([
				"status"	=> "gagal",
				"pesan"		=> "Password tidak boleh kurang dari 6 karakter !"		
			]);
            return;
		}

		$data = [
				'password'      => isset($_POST['new_password']) && !empty($_POST['new_password']) ?  password_hash($_POST['new_password'], PASSWORD_DEFAULT) : ($datameta ? $datameta->password : ($cek_data ? $cek_data->password : password_hash('123', PASSWORD_DEFAULT))),
				'no_hp'         => isset($_POST['nomor_wa']) ?  $_POST['nomor_wa'] : ($datameta ? $datameta->no_hp : ($cek_data ? $cek_data->no_hp : null)),
				'nip'           => $jenis_pegawai=='pegawai' ? $cek_data->nip : $cek_data->nik,
				'pegawai_id'    => $user_id,
				'jenis_pegawai' => $jenis_pegawai,
			];
	
		if($datameta){
			$this->db->where('id', $datameta->id)->update('tb_pegawai_meta', $data);
		}else{
			$this->db->insert('tb_pegawai_meta', $data);
		}

        $role           = $this->db->select('tb_role.*')
                                 ->where('tb_user_roled_website.website_id', 1)
                                 ->where('tb_user_roled_website.user_id', $user_id)
                                 ->where('tb_user_roled_website.jenis_pegawai', $jenis_pegawai)
                                 ->join('tb_role', 'tb_role.role_id=tb_user_roled_website.role_id', 'left')
                                 ->get('tb_user_roled_website')->row();

        $ABSENSI		= $this->load->database('dbabsensi', TRUE);
        $absensi_meta	= $jenis_pegawai=='pegawai' ? 
									$ABSENSI->get_where('tb_pegawai_meta', ['pegawai_id' => $user->id_pegawai])->row():
									$ABSENSI->get_where('tb_tks_meta', ['tks_id' => $user_id])->row();

        $absensi_var['kordinat_bebas']  = isset($absensi_pegawai_meta->kordinat_bebas) && $absensi_pegawai_meta->kordinat_bebas ? ($absensi_pegawai_meta->kordinat_bebas=="Ya" ? $absensi_pegawai_meta->kordinat_bebas : null) : null;     
        $absensi_var['kordinat_khusus'] = isset($absensi_pegawai_meta->kordinat_khusus) && $absensi_pegawai_meta->kordinat_khusus ? ($absensi_pegawai_meta->kordinat_khusus == "Ya" ? unserialize($absensi_pegawai_meta->kordinats) : null) : null;     
        
        if($absensi_var['kordinat_khusus']){
            foreach($absensi_var['kordinat_khusus'] as $kordinat_khusus_id){
                $ABSENSI->or_where('id', $kordinat_khusus_id);
            }
            $kordinats = $ABSENSI->select('
                                            tb_kordinat_tambahan.nama_skpd, 
                                            tb_kordinat_tambahan.nama_kordinat, 
                                            tb_kordinat_tambahan.latitude, 
                                            tb_kordinat_tambahan.longitude, 
                                            tb_kordinat_tambahan.radius
                                ')
                                ->get('tb_kordinat_tambahan')->result_array();
            $absensi_var['kordinat_khusus']  = $kordinats ? $kordinats : null;
        }

		$today = date("Y-m-d H:i:s");
        $token = md5(strtotime($today) . "" . $user_id);

        $data = [
            'user_id'       	=> $user_id,
            'nama'          	=> $cek_data->nama,
            'username'      	=> $jenis_pegawai=='pegawai' ? $cek_data->nip : $cek_data->nik,
            'jenis_pegawai' 	=> $jenis_pegawai,
            'role_id'       	=> $role->role_id,
            'roles'         	=> [],
			'nomor_wa'			=> $data['no_hp'],   
            'absensi_variables' => $absensi_var,
            'skpd_id'       	=> $cek_data->skpd_id,
            'nama_opd'      	=> $cek_data->nama_skpd,
            'start_token'   	=> $today,
            'token'         	=> $token
        ];
        $this->session->set_userdata($data);

        $roles_data          = $this->db->select('tb_user_roled_website.role_id, tb_websites.domain')
                                ->where('user_id', $user_id)
                                ->where('jenis_pegawai', $jenis_pegawai)
                                ->join('tb_websites','tb_websites.id=tb_user_roled_website.website_id', 'left')
                                ->get('tb_user_roled_website')->result();
        $roles = array();
        foreach($roles_data as $role){
            $role      = [
                "domain"    => $role->domain,
                "role_id"   => $role->role_id  
            ];
            $roles[] = $role;
        }
        $data['roles']  = $roles;
        unset($data['role_id']);
        $key = "123aaaa321";
        $data = JWT::encode($data, $key);

		echo json_encode([
			"status"	=> "berhasil",
			"pesan"		=> "Profil Anda berhasil diubah !",
			"auth_data"	=> $data

		]);
		return;

    }

	public function lupapassword()
	{
        if(!isset($_POST['username'])){
            echo json_encode([
                "status"    => "gagal",
                "pesan"     => "Masukkan NIP/NIK terlebih dahulu!",
            ]);
            return;
        }

        $username    = $_POST['username'];
        
        $SIMPEG      = $this->load->database('otherdb', TRUE);
        $user        = ['pegawai' => $SIMPEG->get_where('pegawai', ['nip' => $username, 'status_pegawai'=>'pegawai'])->row()];
        $user        = !$user['pegawai'] ? ['tks' => $SIMPEG->get_where('tb_pegawai_tks', ['nik' => $username])->row()] : $user;

        $password        = rand(100000, 999999);
        $password_hash   = password_hash($password, PASSWORD_DEFAULT);

        if(isset($user['pegawai']) && $user['pegawai']){
            $datameta =  $this->db->
                               where('pegawai_id', $user['pegawai']->id_pegawai)->
                               where('jenis_pegawai', 'pegawai')->
                               get('tb_pegawai_meta')->row();
            $data = [
                    'password'      => $password_hash,
                    'no_hp'         => isset($datameta->no_hp) ? $datameta->no_hp : $user['pegawai']->no_hp,
                    'nip'           => isset($datameta->nip) ? $datameta->nip : $user['pegawai']->nip,
                    'pegawai_id'    => $user['pegawai']->id_pegawai,
                    'jenis_pegawai' => 'pegawai',
                ];
        
            if($datameta){
                $this->db->where('id', $datameta->id)->update('tb_pegawai_meta', $data);
            }else{
                $this->db->insert('tb_pegawai_meta', $data);
            }

            $this->db->where('user_id', $user['pegawai']->id_pegawai)->where('jenis_pegawai', 'pegawai')->update('tb_token', ['status'=>0]);

            $pesan       = "*[LAYANAN E-GOVERMENT]*\n\nHai ".$user['pegawai']->nama_pegawai."\nPassword anda adalah *".$password."*";
            $this->Sms_model->send((isset($datameta->no_hp) ? $datameta->no_hp : $user['pegawai']->no_hp), $pesan);
            
            echo json_encode([
                "status"    => "berhasil",
                "pesan"     => "Password baru sudah dikirim ke Whatsapp Anda, jika tidak masuk coba sekali lagi. Jika ada kendala hubungi kami di Telegram t.me/egovlabura.",
            ]);
            return;

        }else if(isset($user['tks']) && $user['tks']){
            // $SIMPEG->where('id', $user['tks']->id)->update('tb_pegawai_tks', ['password'=>$password_hash]);
            $datameta =  $this->db->
                               where('pegawai_id', $user['tks']->id)->
                               where('jenis_pegawai', 'tks')->
                               get('tb_pegawai_meta')->row();
            $data = [
                    'password'      => $password_hash,
                    'no_hp'         => isset($datameta->no_hp) ? $datameta->no_hp : $user['tks']->no_hp,
                    'nip'           => isset($datameta->nip) ? $datameta->nip : $user['tks']->nik,
                    'pegawai_id'    => $user['tks']->id,
                    'jenis_pegawai' => 'tks',
                ];
        
            if($datameta){
                $this->db->where('id', $datameta->id)->update('tb_pegawai_meta', $data);
            }else{
                $this->db->insert('tb_pegawai_meta', $data);
            }

            $this->db->where('user_id', $user['tks']->id)->where('jenis_pegawai', 'tks')->update('tb_token', ['status'=>0]);
            $pesan       = "*[LAYANAN E-GOVERMENT]*\n\nHai ".$user['tks']->nama_tks."\nPassword anda adalah *".$password."*";
            $this->Sms_model->send((isset($datameta->no_hp) ? $datameta->no_hp : $user['tks']->no_hp), $pesan);

            echo json_encode([
                "status"    => "berhasil",
                "pesan"     => "Password baru sudah dikirim ke Whatsapp Anda, jika tidak masuk coba sekali lagi. Jika ada kendala hubungi kami di Telegram t.me/egovlabura.",
            ]);
            return;

        }

        echo json_encode([
            "status"    => "gagal",
            "pesan"     => "NIP/NIK tidak ditemukan!",
        ]);
        return;
    }


    public function loginAPP(){
        if(isset($_POST['username']) && isset($_POST['password'])){
            extract($_POST);
            $this->_login($username, $password);
            return;
        }
    
        echo json_encode(["status"=>"gagal"]);
        return;

    }
    private function _login($username, $password)
    {
        $username    = $this->input->post('username');
        $password    = $this->input->post('password');

        $user        = $this->db->where('username',$username)->get('tb_users')->row();
        if($user)
        {
            if($user->is_active != 'Ya')
            {
                echo json_encode(["status"=>"gagal","pesan"=>'Akun anda sedang di non aktifkan']);
                return;
            }

            if(password_verify($password, $user->password))
            {
                $roles = $this->db->select('tb_roles.nama')
                        ->where('tb_user_roles.user_id',$user->id)
                        ->join('tb_roles','tb_roles.id=tb_user_roles.role_id')
                        ->get('tb_user_roles')->row();
                $all_roles = [];
                foreach($roles as $role)
                    $all_roles[] = $role;
                $user  = (array) $user;
                unset($user['password']);
                $user['roles'] = $all_roles;
                $pegawai = $this->db->where('user_id',$user['id'])->get('tb_pegawai')->row();
                $opd = $this->db->where('id',$pegawai->opd_id)->get('tb_opd')->row();
                $user['pegawai'] = (array) $pegawai;
                $user['opd'] = (array) $opd;

                $key = "123aaaa321";
                $data = JWT::encode($user, $key);
                echo json_encode(["status"=>"berhasil","data"=>$data]);
                return;
            }
            else
            {
                echo json_encode(["status"=>"gagal","pesan"=>'Password tidak valid!']);
                return;
            }
        }
        else
        {
            echo json_encode(["status"=>"gagal","pesan"=>'User tidak ditemukan!']);
            return;
        }
        return;

    }


    private function _loginTKS($username, $password)
    {

        $SIMPEG      = $this->load->database('otherdb', TRUE);
        $user        = $SIMPEG->select('tb_pegawai_tks.*, skpd.id_skpd skpd_id, skpd.nama_skpd')
                            ->where('tb_pegawai_tks.nik', $username)
                            ->join('skpd', 'skpd.id_skpd=tb_pegawai_tks.skpd_id', 'left')
                            ->get('tb_pegawai_tks')
                            ->row();


        if ($user) {
            $user_meta = $this->db->
                                where('pegawai_id', $user->id)->
                                where('jenis_pegawai', 'tks')->
                                get('tb_pegawai_meta')->
                                row();
    
            if (($user_meta && password_verify($password, $user_meta->password)) || (!$user_meta && password_verify($password, $user->password))){
                
            }else{
				echo json_encode([
					"status"	=> "gagal",
					"pesan"		=> "Password Salah",
				]);
                return;
            }
            $website_id     = 1;
            $access         = $this->db->where('website_id', $website_id)->where('user_id', $user->id)->where('jenis_pegawai', 'tks')->get('tb_user_roled_website')->num_rows();
            if($access==0){
				echo json_encode([
					"status"	=> "gagal",
					"pesan"		=> "Tidak ada akses!"
				]);
                return;   
            }

            $today = date("Y-m-d H:i:s");
            $token = md5(strtotime($today) . "" . $user->id);
            $data = [
                "token"                 => $token,
                "user_id"               => $user->id,
                "start_token"           => $today,
                "last_actived"          => $today,
                'jenis_pegawai'         => 'tks',
                "status"                => 1
            ];
            $this->db->insert('tb_token', $data);

            $user_id        = $user->id;
            $role = $this->db->select('tb_role.*')
                                     ->where('tb_user_roled_website.website_id', $website_id)
                                     ->where('tb_user_roled_website.user_id', $user_id)
                                     ->where('tb_user_roled_website.jenis_pegawai', 'tks')
                                     ->join('tb_role', 'tb_role.role_id=tb_user_roled_website.role_id', 'left')
                                     ->get('tb_user_roled_website')->row();

									 
									 
									 // FROM ABSENSI ///////////////////////////////////////////
            $ABSENSI                = $this->load->database('dbabsensi', TRUE);
			$kordinat_default 		= $ABSENSI->select('tb_kordinat.skpd_id, tb_kordinat.latitude, tb_kordinat.longitude, tb_kordinat.radius')->where('tb_kordinat.skpd_id', $user->skpd_id)->get('tb_kordinat')->row_array();
            $absensi_pegawai_meta   = $ABSENSI->get_where('tb_tks_meta', ['tks_id' => $user->id])->row();
        
            $absensi_var['kordinat_bebas']  = isset($absensi_pegawai_meta->kordinat_bebas) && $absensi_pegawai_meta->kordinat_bebas ? ($absensi_pegawai_meta->kordinat_bebas=="Ya" ? $absensi_pegawai_meta->kordinat_bebas : null) : null;     
            $absensi_var['kordinat_khusus'] = isset($absensi_pegawai_meta->kordinat_khusus) && $absensi_pegawai_meta->kordinat_khusus ? ($absensi_pegawai_meta->kordinat_khusus == "Ya" ? unserialize($absensi_pegawai_meta->kordinats) : null) : null;     

            if($absensi_var['kordinat_khusus']){
                foreach($absensi_var['kordinat_khusus'] as $kordinat_khusus_id){
                    $ABSENSI->or_where('id', $kordinat_khusus_id);
                }
                $kordinats = $ABSENSI->select('
                                                tb_kordinat_tambahan.nama_skpd, 
                                                tb_kordinat_tambahan.nama_kordinat, 
                                                tb_kordinat_tambahan.latitude, 
                                                tb_kordinat_tambahan.longitude, 
                                                tb_kordinat_tambahan.radius
                                    ')
                                    ->get('tb_kordinat_tambahan')->result_array();
                $absensi_var['kordinat_khusus']  = $kordinats ? $kordinats : null;
			}else{
				if($kordinat_default){
					$kordinat_default['nama_skpd'] = "Default";
					$kordinat_default['nama_kordinat'] = "Default";
				}
				$absensi_var['kordinat_khusus']  = $kordinat_default ? [$kordinat_default] :null;
			}


            $data = [
                'user_id'       	=> $user->id,
                'nama'          	=> $user->nama_tks,
                'role_id'       	=> $role->role_id,
                'username'      	=> $user->nik,
				'roles'        	 	=> [],
				'nomor_wa'			=> isset($user_meta->no_hp) ? $user_meta->no_hp : null,
                'absensi_variables' => $absensi_var,
                'skpd_id'       	=> $user->skpd_id,
                'nama_opd'      	=> $user->nama_skpd,
                'start_token'   	=> $today,
                'jenis_pegawai' 	=> 'tks',
                'token'         	=> $token
            ];
            $this->session->set_userdata($data);

            $roles_data          = $this->db->select('tb_user_roled_website.role_id, tb_websites.domain')
                                    ->where('user_id', $user->id)
                                    ->where('jenis_pegawai', 'tks')
                                    ->join('tb_websites','tb_websites.id=tb_user_roled_website.website_id', 'left')
                                    ->get('tb_user_roled_website')->result();
            $roles = array();
            foreach($roles_data as $role){
                $role      = [
                    "domain"    => $role->domain,
                    "role_id"   => $role->role_id  
                ];
                $roles[] = $role;
            }
            $data['roles']  = $roles;

            $key = "123aaaa321";
            $data = JWT::encode($data, $key);
            
            $cookie_name = "labura_layanan_app_token";
            $cookie_value = $data;
            setcookie($cookie_name, $cookie_value, time()+(30*24*3600), '/',".labura.go.id"); // 86400 = 1 Month

			echo json_encode([
				"status"	=> "berhasil",
				"data"		=> $data	  					
			]);
            return;

        } else {
			echo json_encode([
				"status"	=> "gagal",
				"pesan"		=> "User tidak ditemukan!"		
			]);            
            return;
        }
    }

    // END APP Login    ---------------------------------------------------------------------------------------------------------------------------------
    // START APP        ---------------------------------------------------------------------------------------------------------------------------------
    
    public function push_absen_wajah(){
        $headers = getallheaders();
        if(!isset($headers['Authorization'])){
            echo json_encode([
                        "status"    => "gagal",
                        "pesan"     => "Authorization Error !"
            ]);
            return;
        }
        
        $SIMPEG      = $this->load->database('otherdb', TRUE);
        $Authorization = $headers['Authorization'];
        $token = $this->db->where('token', $Authorization)
                        ->where('status', 1)
                        ->get('tb_token')
                        ->row();

        if(!$token){
            echo json_encode([
                        "status"    => "gagal",
                        "pesan"     => "Token tidak valid !"
            ]);
            return;
        }

        $userPegawai= $SIMPEG->select('pegawai.id_skpd skpd_id')
                             ->where('pegawai.id_pegawai', $token->user_id)
                             ->get('pegawai')
                             ->row();

        $userTks   = $SIMPEG->select('tb_pegawai_tks.skpd_id')
                            ->where('tb_pegawai_tks.id', $token->user_id)
                            ->get('tb_pegawai_tks')
                            ->row();


        if($token->jenis_pegawai=='pegawai' && $userPegawai){
            $data = '&pegawai_id='.$token->user_id.'&jenis_pegawai='.$token->jenis_pegawai.'&skpd_id='.$userPegawai->skpd_id.'&ip='.$_SERVER['REMOTE_ADDR'];
            if($this->sendFaceToAbsensi($data)){
                echo json_encode([
                            "status"    => "berhasil",
                            "pesan"     => "Absensi wajah berhasil !",
                ]);
                return;
            }
            echo json_encode([
                        "status"    => "gagal",
                        "pesan"     => "Absensi wajah tidak berhasil !"
            ]);
            return;
        }elseif($token->jenis_pegawai=='tks' && $userTks){
            $data = '&pegawai_id='.$token->user_id.'&jenis_pegawai='.$token->jenis_pegawai.'&skpd_id='.$userTks->skpd_id.'&ip='.$_SERVER['REMOTE_ADDR'];
            if($this->sendFaceToAbsensi($data)){
                echo json_encode([
                            "status"    => "berhasil",
                            "pesan"     => "Absensi wajah berhasil !",
                ]);
                return;
            }
            echo json_encode([
                        "status"    => "gagal",
                        "pesan"     => "Absensi wajah tidak berhasil !"
            ]);
            return;
        }else{
            echo json_encode([
                        "status"    => "gagal",
                        "pesan"     => "Data user tidak ditemukan !"
            ]);
            return;
        }

    }

    private function sendFaceToAbsensi($postData){
        $user_key = '64240-d0ede73ccaf823f30d586a5ff9a35fa5';
        $pass_key = 'b546a6dfc4';

        $posts ='user_key='.$user_key.'&pass_key='.$pass_key.$postData;

        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, 'http://allnewabsensi.egov.labura.go.id/api/push_absen_wajah');
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $posts);
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        $results = curl_exec($curlHandle);
        curl_close($curlHandle);
        
        return json_decode($results, true);

    }
    
    // END APP          ---------------------------------------------------------------------------------------------------------------------------------


    public function getLogin(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(false);
            return;
        }

        if(isset($_POST['token'])&&isset($_POST['user_id'])){
            extract($_POST);

            $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();
            
            if(!$api){ echo json_encode(false);return;}

            $datatoken = $this->tokenData($token, $user_id);

            if(!$datatoken){ echo json_encode(false);return;}

            $SIMPEG      = $this->load->database('otherdb', TRUE);
            if($datatoken->jenis_pegawai=='tks'){
                $user        = $SIMPEG->select('
                                                tb_pegawai_tks.id user_id, 
                                                tb_pegawai_tks.nama_tks nama,
                                                tb_pegawai_tks.no_hp,
                                                tb_pegawai_tks.nik username,
                                                skpd.id_skpd skpd_id,
                                                skpd.id_skpd opd_id,
                                                skpd.nama_skpd nama_opd,
                                                ')
                                        ->where('tb_pegawai_tks.id', $user_id)
                                        ->join('skpd','skpd.id_skpd=tb_pegawai_tks.skpd_id','left')
                                        ->get('tb_pegawai_tks')->row();
                $user->jenis_pegawai = 'tks';

            }else{
                $user        = $SIMPEG->select('
                                                pegawai.id_pegawai user_id, 
                                                pegawai.nama_pegawai nama,
                                                pegawai.gelar_depan,
                                                pegawai.gelar_belakang,
                                                pegawai.no_hp,
                                                pegawai.nip username,
                                                skpd.id_skpd skpd_id,
                                                skpd.id_skpd opd_id,
                                                skpd.nama_skpd nama_opd,
                                                ')
                                        ->where('pegawai.id_pegawai', $user_id)
                                        ->join('skpd','skpd.id_skpd=pegawai.id_skpd','left')
                                        ->get('pegawai')->row();
                                        
                $user->nama = ($user->gelar_depan && $user->gelar_depan!="" ? $user->gelar_depan.". " : null).$user->nama.($user->gelar_belakang && $user->gelar_belakang!="" ? ", ".$user->gelar_belakang : null);
                unset($user->gelar_depan);
                unset($user->gelar_belakang);                
                $user->jenis_pegawai = 'pegawai';
            }
            if(!$user){
                echo json_encode(false);
                return;
            }
            
            $opd            = $this->db->select('referensi_opd.egov_opd opd_id')
                                    ->where('simpeg_opd', $user->opd_id)
                                    ->get('referensi_opd')->row();
            
            $user->opd_id   = isset($opd->opd_id) ? $opd->opd_id : null;
            
            
            $role           = $this->db->select('tb_user_roled_website.role_id')
                                    ->where('user_id', $user->user_id)
                                    ->where('website_id', $api->website_id)
                                    ->where('jenis_pegawai', $datatoken->jenis_pegawai)
                                    ->get('tb_user_roled_website')->row();
            $user->role_id  = $role->role_id;

            echo json_encode($user);
            return;
        }
        echo json_encode(false);
        return;
    }
    

    // SKPD & OPD ----------------------------------------------------------------------------------------------
    public function getOpd(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(false);
            return;
        }

        extract($_POST);

        $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();
        
        if(!$api){ echo json_encode(false);return;}
        
        $opd = $this->db->order_by('nama_opd', 'asc')->get('tb_opd')->result();
        $dataOPD = array();
        foreach($opd as $opd){
            $dataOPD[$opd->id] = $opd;
        }
        echo json_encode($dataOPD);
        return;

    }
    public function getOpdById(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(false);
            return;
        }
        $user_key = null;
        $pass_key = null;
        extract($_POST);

        $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();
        
        if(!$api){ echo json_encode(false);return;}
        
        $opd = $this->db->where('id', $id)->get('tb_opd')->row();

        echo json_encode($opd);
        return;

    }


    public function getSkpd(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(false);
            return;
        }

        extract($_POST);

        $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();
        
        if(!$api){ echo json_encode(false);return;}


        $SIMPEG      = $this->load->database('otherdb', TRUE);
        if(isset($_POST['skpd_id'])){
            $SIMPEG->where('id_skpd', $skpd_id);
        }
        $skpds       = $SIMPEG->order_by('nama_skpd', 'asc')->get('skpd')->result();
        $dataSKPDS   = array();
        foreach($skpds as $skpd){
            $dataSKPDS[$skpd->id_skpd] = $skpd;
        }

        echo json_encode($dataSKPDS);
        return;

    }
    
    public function getSkpdById(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(false);
            return;
        }

        if(isset($_POST['skpd_id'])){
            extract($_POST);

            $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();
            
            if(!$api){ echo json_encode(false);return;}


            $SIMPEG      = $this->load->database('otherdb', TRUE);
            $skpd        = $SIMPEG->where('id_skpd', $skpd_id)->get('skpd')->row();

            echo json_encode($skpd);
            return;
        }
        echo json_encode(false);
        return;
    }
    // END of SKPD & OPD ------------------------------------------------------------------------------------------------------
    
    // USER -------------------------------------------------------------------------------------------------------------------
    public function getUserById(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(false);
            return;
        }

        if(isset($_POST['user_id'])){
            extract($_POST);

            $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();
            
            if(!$api){ echo json_encode(false);return;}


            $SIMPEG      = $this->load->database('otherdb', TRUE);

            if(isset($_POST['user_id']) && isset($_POST['jenis_pegawai']) && $_POST['jenis_pegawai']=='tks'){
                $user        = $SIMPEG->select('
                                                tb_pegawai_tks.id user_id, 
                                                tb_pegawai_tks.nama_tks nama,
                                                tb_pegawai_tks.no_hp,
                                                tb_pegawai_tks.nik username,
                                                skpd.id_skpd skpd_id,
                                                skpd.id_skpd opd_id,
                                                skpd.nama_skpd nama_opd,
                                                ')
                                        ->where('tb_pegawai_tks.id', $user_id)
                                        ->join('skpd','skpd.id_skpd=tb_pegawai_tks.skpd_id','left')
                                        ->get('tb_pegawai_tks')->row();
            
            }else{
                $user        = $SIMPEG->select('
                                                pegawai.id_pegawai user_id, 
                                                pegawai.nama_pegawai nama,
                                                pegawai.gelar_depan,
                                                pegawai.gelar_belakang,
                                                pegawai.no_hp,
                                                pegawai.nip username,
                                                skpd.id_skpd skpd_id,
                                                skpd.id_skpd opd_id,
                                                skpd.nama_skpd nama_opd,
                                                ')
                                        ->where('pegawai.id_pegawai', $user_id)
                                        ->join('skpd','skpd.id_skpd=pegawai.id_skpd','left')
                                        ->get('pegawai')->row();
                if($user){
                    $user->nama = ($user->gelar_depan && $user->gelar_depan!="" ? $user->gelar_depan.". " : null).$user->nama.($user->gelar_belakang && $user->gelar_belakang!="" ? ", ".$user->gelar_belakang : null);
                    unset($user->gelar_depan);
                    unset($user->gelar_belakang);
                }
            }
    
            if(!$user){
                echo json_encode(false);
                return;

            }
    
            $opd         = $this->db->select('referensi_opd.egov_opd opd_id')
                                    ->where('simpeg_opd', $user->opd_id)
                                    ->get('referensi_opd')->row();
            
            $role        = $this->db->select('tb_user_roled_website.role_id')
                                    ->where('user_id', $user->user_id)
                                    ->where('website_id', $api->website_id)
                                    ->get('tb_user_roled_website')->row();


            $user->opd_id   = $opd->opd_id;
            $user->role_id  = $role->role_id;

            echo json_encode($user);
            return;
        }
        echo json_encode(false);
        return;
    }
    
    public function getUsersByRole(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(false);
            return;
        }

        if(isset($_POST['role_id'])){
            extract($_POST);

            $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();

            if(!$api){ echo json_encode(false);return;}

            if(isset($_POST['jenis_pegawai']) && $jenis_pegawai=='tks'){
                $user_roled_website = $this->db
                                            ->where('website_id', $api->website_id)
                                            ->where('role_id', $role_id)
                                            ->where('jenis_pegawai', 'tks')
                                            ->get('tb_user_roled_website')
                                            ->result();
            }else{
                $user_roled_website = $this->db
                                            ->where('website_id', $api->website_id)
                                            ->where('role_id', $role_id)
                                            ->where('jenis_pegawai', 'pegawai')
                                            ->get('tb_user_roled_website')
                                            ->result();
            }
            
            $SIMPEG      = $this->load->database('otherdb', TRUE);
            $users       = array();
            foreach($user_roled_website as $roled){
                
                if(isset($_POST['jenis_pegawai']) && $jenis_pegawai=='tks'){
                    $user   = $SIMPEG->select('
                                                tb_pegawai_tks.id tks_id, 
                                                tb_pegawai_tks.id user_id, 
                                                tb_pegawai_tks.nama_tks nama,
                                                tb_pegawai_tks.no_hp,
                                                tb_pegawai_tks.nik username,
                                            ')
                                    ->where('tb_pegawai_tks.id', $roled->user_id)
                                    ->get('tb_pegawai_tks')
                                    ->row();
   
                }else{
 
                    $user   = $SIMPEG->select('
                                                pegawai.id_pegawai pegawai_id, 
                                                pegawai.id_pegawai user_id, 
                                                pegawai.nama_pegawai nama,
                                                pegawai.gelar_depan,
                                                pegawai.gelar_belakang,
                                                pegawai.no_hp,
                                                pegawai.nip username,
                                            ')
                                    ->where('pegawai.id_pegawai', $roled->user_id)
                                    ->get('pegawai')
                                    ->row();
                    $user->nama = ($user->gelar_depan && $user->gelar_depan!="" ? $user->gelar_depan.". " : null).$user->nama.($user->gelar_belakang && $user->gelar_belakang!="" ? ", ".$user->gelar_belakang : null);
                    unset($user->gelar_depan);
                    unset($user->gelar_belakang);
                }
                $users[] = $user;
            }

            echo json_encode($users);
            return;
        }
        echo json_encode(false);
        return;
    }
    public function getUsers(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(false);
            return;
        }

        extract($_POST);

        $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();

        if(!$api){ echo json_encode(false);return;}

        $SIMPEG      = $this->load->database('otherdb', TRUE);

        if(isset($_POST['jenis_pegawai']) && $jenis_pegawai=='tks'){
            $user   = $SIMPEG->select('
                                        tb_pegawai_tks.id user_id, 
                                        tb_pegawai_tks.nama_tks nama,
                                        tb_pegawai_tks.no_hp,
                                        tb_pegawai_tks.nik username,
                                        tb_pegawai_tks.skpd_id,
                                        skpd.nama_skpd,
                                    ')
                            ->join('skpd','skpd.id_skpd=tb_pegawai_tks.skpd_id', 'left')
                            ->get('tb_pegawai_tks')
                            ->result();

            echo json_encode($user);
            return;

        }elseif(isset($_POST['jenis_pegawai']) && $jenis_pegawai=='pegawai'){

            $user   = $SIMPEG->select('
                                        pegawai.id_pegawai user_id, 
                                        pegawai.nama_pegawai nama,
                                        pegawai.gelar_depan,
                                        pegawai.gelar_belakang,
                                        pegawai.no_hp,
                                        pegawai.nip username,
                                        pegawai.id_skpd skpd_id,
                                        skpd.nama_skpd,
                                    ')
                            ->where('pegawai.status_pegawai', 'pegawai')
                            ->where('pegawai.nama_pegawai!=','')
                            ->where('pegawai.nip!=','')
                            ->where('pegawai.nama_pegawai is NOT NULL')
                            ->where('pegawai.nip is NOT NULL')
                            ->join('skpd','skpd.id_skpd=pegawai.id_skpd', 'left')
                            ->get('pegawai')
                            ->result();
                            
            echo json_encode($user);
            return;

        }else{
            echo json_encode(false);
            return;
        }


    }


    public function getKepalaSkpd(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(false);
            return;
        }

        extract($_POST);

        $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();

        if(!$api){ echo json_encode(false);return;}
        
        if(!isset($_POST['skpd_id'])){ echo json_encode(false);return;}

        $SIMPEG      = $this->load->database('otherdb', TRUE);

        $datapegawai    = $SIMPEG->select('
                                    pegawai.id_pegawai user_id, 
                                    pegawai.nama_pegawai nama,
                                    pegawai.gelar_depan,
                                    pegawai.gelar_belakang,
                                    pegawai.no_hp,
                                    pegawai.nip username,
                                    pegawai.tempat_lahir,
                                    pegawai.tanggal_lahir,
                                    pegawai.jenkel,
                                    pegawai.alamat_jalan,
                                    pegawai.alamat_kelurahan,
                                    pegawai.alamat_kecamatan,
                                    pegawai.id_skpd skpd_id,
                                    skpd.nama_skpd,
                                    pangkat.nama_pangkat,
                                    pangkat.kode_pangkat,
                                    jabatan.nama_jabatan,
                                ')
                        ->group_start()
                            ->where('jabatan.nama_jabatan', 'Kepala Dinas')
                            ->or_where('jabatan.nama_jabatan', 'Kepala Badan')
                        ->group_end()
                        ->where('jabatan.id_skpd', $skpd_id)
                        ->join('skpd','skpd.id_skpd=jabatan.id_skpd', 'left')
                        ->join('pegawai','pegawai.id_jabatan=jabatan.id_jabatan', 'left')
                        ->join('pangkat','pangkat.id_pangkat=pegawai.id_pangkat', 'left')
                        ->get('jabatan')
                        ->row();

        echo json_encode($datapegawai);
        return;


    }

    public function getUnitKerja(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(false);
            return;
        }

        extract($_POST);
        $api         = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();
        if(!$api){ echo json_encode(false); return; }

        $SIMPEG      = $this->load->database('otherdb', TRUE);
        if(isset($_POST['opd_id']) && $opd_id) $this->db->where('opd_id', $opd_id);
        $unitkerjas  = $this->db->get('tb_unit_kerja')->result();
        
        echo json_encode($unitkerjas);
        return;
    }
    public function getOpdByUnitKerja(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key']) || !isset($_POST['skpd_id'])){
            echo json_encode(false);
            return;
        }

        extract($_POST);
        $api         = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();
        if(!$api){ echo json_encode(false); return; }

        $unitkerjas  = $this->db->where('skpd_id', $skpd_id)->get('tb_unit_kerja')->row();
        
        echo json_encode($unitkerjas);
        return;
    }
    public function getAllOpd(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(false);
            return;
        }

        extract($_POST);
        $api         = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();
        if(!$api){ echo json_encode(false); return; }

        if(isset($_POST['skpd_id']) && $skpd_id) $this->db->where('skpd_id', $skpd_id);
        $unitkerjas  = $this->db->where('is_opd', 'Ya')->get('tb_unit_kerja')->result();
        
        echo json_encode($unitkerjas);
        return;
    }


    public function getPegawai(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(false);
            return;
        }

        extract($_POST);

        $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();

        if(!$api){ echo json_encode(false);return;}

        $SIMPEG      = $this->load->database('otherdb', TRUE);




        if(isset($_POST['jenis_pegawai']) && $jenis_pegawai=='tks'){
            if(isset($_POST['pegawai_id']) && $pegawai_id){
                $SIMPEG->where('tb_pegawai_tks.id', $pegawai_id);
            }
            if(isset($_POST['skpd_id']) && $skpd_id){
                $SIMPEG->where('tb_pegawai_tks.skpd_id', $skpd_id);
            }

            $datapegawai   = $SIMPEG->select('
                                        tb_pegawai_tks.id tks_id, 
                                        tb_pegawai_tks.id user_id, 
                                        tb_pegawai_tks.nama_tks nama,
                                        tb_pegawai_tks.no_hp,
                                        tb_pegawai_tks.nik username,
                                        tb_pegawai_tks.skpd_id,
                                        tb_pegawai_tks.alamat,
                                        tb_pegawai_tks.tanggal_lahir,
                                        tb_pegawai_tks.tempat_lahir,
                                        tb_pegawai_tks.jenkel,
                                        skpd.nama_skpd,
                                    ')
                            ->join('skpd','skpd.id_skpd=tb_pegawai_tks.skpd_id', 'left')
                            ->get('tb_pegawai_tks')
                            ->result();

            echo json_encode($datapegawai);
            return;

        }elseif(isset($_POST['jenis_pegawai']) && $jenis_pegawai=='pegawai'){
            if(isset($_POST['pegawai_id']) && $pegawai_id){
                $SIMPEG->where('pegawai.id_pegawai', $pegawai_id);
            }
            if(isset($_POST['skpd_id']) && $skpd_id){
                $SIMPEG->where('pegawai.id_skpd', $skpd_id);
            }

            if(isset($_POST['detail']) && $_POST['detail']=="all"){
                $SIMPEG->select('
                                pegawai.id_pegawai user_id, 
                                pegawai.nama_pegawai nama,
                                pegawai.gelar_depan,
                                pegawai.gelar_belakang,
                                pegawai.tempat_lahir,
                                pegawai.tanggal_lahir,
                                pegawai.jenkel,
                                pegawai.alamat_jalan,
                                pegawai.alamat_kelurahan,
                                pegawai.alamat_kecamatan,
                                pegawai.no_hp,
                                pegawai.nip username,
                                pegawai.id_skpd skpd_id,
                                skpd.nama_skpd,
                                pangkat.nama_pangkat,
                                pangkat.kode_pangkat,
                                jabatan.nama_jabatan,
                                eselon.nama_eselon,
                             ');
            }else{
                $SIMPEG->select('
                                pegawai.id_pegawai user_id, 
                                pegawai.nama_pegawai nama,
                                pegawai.gelar_depan,
                                pegawai.gelar_belakang,
                                pegawai.tempat_lahir,
                                pegawai.tanggal_lahir,
                                pegawai.jenkel,
                                pegawai.alamat_jalan,
                                pegawai.alamat_kelurahan,
                                pegawai.alamat_kecamatan,
                                pegawai.no_hp,
                                pegawai.nip username,
                                pegawai.id_skpd skpd_id,
                                skpd.nama_skpd,
                                pangkat.nama_pangkat,
                                pangkat.kode_pangkat,
                                jabatan.nama_jabatan,
                                eselon.nama_eselon,
                            ');
            }

            $datapegawai    = $SIMPEG->where('pegawai.status_pegawai', 'pegawai')
                            ->where('pegawai.nama_pegawai!=','')
                            ->where('pegawai.nip!=','')
                            ->where('pegawai.nama_pegawai is NOT NULL')
                            ->where('pegawai.nip is NOT NULL')
                            ->join('skpd','skpd.id_skpd=pegawai.id_skpd', 'left')
                            ->join('pangkat','pangkat.id_pangkat=pegawai.id_pangkat', 'left')
                            ->join('jabatan','jabatan.id_jabatan=pegawai.id_jabatan', 'left')
                            ->join('eselon','eselon.id_eselon=pegawai.id_eselon', 'left')
                            ->order_by('skpd.nama_skpd', 'asc')
                            ->order_by('pegawai.nama_pegawai', 'asc')
                            ->get('pegawai')
                            ->result();
                            
            echo json_encode($datapegawai);
            return;

        }else{
            echo json_encode(false);
            return;
        }


    }

    public function getPegawaiByPegawaiAtasan(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(array());
            return;
        }
        extract($_POST);

        $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();
        if(!$api){ echo json_encode(array());return;}

        $SIMPEG      = $this->load->database('otherdb', TRUE);
        if(isset($_POST['pegawai_atasan_id']) && isset($_POST['jenis_pegawai_atasan'])){
            $pegawaiMeta = $this->db->
                                  where('pegawai_atasan_id', $pegawai_atasan_id)->
                                  where('jenis_pegawai_atasan', $jenis_pegawai_atasan)->
                                  order_by('nama_pegawai', 'asc')->
                                  get('tb_pegawai_atasan')->result();
            
            echo json_encode($pegawaiMeta);
            return;
        } 
        
        echo json_encode(array());
        return;
    }
    
    public function getPegawaiAtasan(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(array());
            return;
        }
        extract($_POST);

        $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();
        if(!$api){ echo json_encode(array());return;}

        $SIMPEG      = $this->load->database('otherdb', TRUE);
        if(isset($_POST['pegawai_id']) && isset($_POST['jenis_pegawai'])){
            $pegawaiMeta = $this->db->
                                  select('tb_pegawai_atasan.*, tb_pegawai_meta.no_hp no_hp_pegawai_atasan')->
                                  where('tb_pegawai_atasan.pegawai_id', $pegawai_id)->
                                  where('tb_pegawai_atasan.jenis_pegawai', $jenis_pegawai)->
                                  join('tb_pegawai_meta', 'tb_pegawai_meta.pegawai_id=tb_pegawai_atasan.pegawai_atasan_id AND tb_pegawai_meta.jenis_pegawai=tb_pegawai_atasan.jenis_pegawai_atasan', 'left')->
                                  order_by('tb_pegawai_atasan.nama_pegawai', 'asc')->
                                  get('tb_pegawai_atasan')->row();
            
            echo json_encode($pegawaiMeta);
            return;
        } 
        
        echo json_encode(array());
        return;
    }

    public function getPegawaiByOpd(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(false);
            return;
        }

        extract($_POST);

        $api            = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();
        if(!$api){ echo json_encode(false);return;}

        $SIMPEG         = $this->load->database('otherdb', TRUE);
        
        if(!isset($_POST['opd_id'])){ echo json_encode(false);return; }
        $skpds          =  $this->db->where('opd_id', $opd_id)->get('tb_unit_kerja')->result();
        if(count($skpds)==0){ echo json_encode(false);return; }
        
        if(isset($_POST['jenis_pegawai']) && $jenis_pegawai=='tks'){

            foreach($skpds as $skpd){
                $SIMPEG->or_where('tb_pegawai_tks.skpd_id', $skpd->skpd_id);
            }            

            $datapegawai   = $SIMPEG->select('
                                        tb_pegawai_tks.id tks_id, 
                                        tb_pegawai_tks.id user_id, 
                                        tb_pegawai_tks.skpd_id,
                                        tb_pegawai_tks.nama_tks nama,
                                        tb_pegawai_tks.no_hp,
                                        tb_pegawai_tks.nik username,
                                        tb_pegawai_tks.jenkel,
                                        skpd.nama_skpd,
                                    ')
                            ->join('skpd','skpd.id_skpd=tb_pegawai_tks.skpd_id', 'left')
                            ->get('tb_pegawai_tks')
                            ->result();

            echo json_encode($datapegawai);
            return;

        }else if(isset($_POST['jenis_pegawai']) && $jenis_pegawai=='pegawai'){
            foreach($skpds as $skpd){
                $SIMPEG->or_where('pegawai.id_skpd', $skpd->skpd_id);
            }

            $SIMPEG->select('
                            pegawai.id_skpd skpd_id,
                            pegawai.id_pegawai user_id, 
                            pegawai.nama_pegawai nama,
                            pegawai.gelar_depan,
                            pegawai.gelar_belakang,
                            pegawai.jenkel,
                            pegawai.no_hp,
                            pegawai.nip username,
                            skpd.nama_skpd,
                        ');

            $datapegawai    = $SIMPEG->where('pegawai.status_pegawai', 'pegawai')
                            ->where('pegawai.nama_pegawai!=','')
                            ->where('pegawai.nip!=','')
                            ->where('pegawai.nama_pegawai is NOT NULL')
                            ->where('pegawai.nip is NOT NULL')
                            ->join('skpd','skpd.id_skpd=pegawai.id_skpd', 'left')
                            ->order_by('skpd.nama_skpd', 'asc')
                            ->order_by('pegawai.nama_pegawai', 'asc')
                            ->get('pegawai')
                            ->result();
                            
            echo json_encode($datapegawai);
            return;

        }else{
            echo json_encode(false);
            return;
        }

    }

    public function getPegawaiMeta(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(false);
            return;
        }
        extract($_POST);
        $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();
        if(!$api){ echo json_encode(false);return;}

        if(isset($_POST['pegawai_id']) && isset($_POST['jenis_pegawai'])){
            $meta = $this->db->
                    where('pegawai_id', $pegawai_id)->
                    where('jenis_pegawai', $jenis_pegawai)->
                    get('tb_pegawai_meta')->row();
                            
            echo json_encode($meta);
            return;
        }

        echo json_encode(false);
        return;
    }

    public function getPegawaiByRole(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(false);
            return;
        }

        extract($_POST);
        
        $api            = $this->db->
                                 where('user_key', $user_key)->
                                 where('pass_key', $pass_key)->
                                 get('tb_api')->row();
        if(!$api){ echo json_encode(false); return; }

        $SIMPEG         = $this->load->database('otherdb', TRUE);

        if(isset($_POST['role_id']) && $role_id){
            $data = [
                        'website_id'    => $api->website_id,
                        'role_id'       => $role_id,
                        'pegawai'       => array(),
                        'tks'           => array(),
                ];
                
            $roles          = $this->db->
                                     where('website_id', $api->website_id)->
                                     where('role_id', $role_id)->
                                     get('tb_user_roled_website')->result();
            
            $roleTks = 0;
            foreach($roles as $role){
                if($role->jenis_pegawai=="tks"){
                    $SIMPEG->or_where('tb_pegawai_tks.id', $role->user_id);
                    $roleTks++;
                }
            }
            if($roleTks > 0){
                $data['tks']    = $SIMPEG->select('
                                            tb_pegawai_tks.id user_id, 
                                            tb_pegawai_tks.nama_tks nama,
                                            tb_pegawai_tks.no_hp,
                                            tb_pegawai_tks.nik username,
                                            tb_pegawai_tks.skpd_id,
                                            skpd.nama_skpd,
                                        ')
                                ->join('skpd','skpd.id_skpd=tb_pegawai_tks.skpd_id', 'left')
                                ->get('tb_pegawai_tks')
                                ->result();
            }

            $rolePegawai = 0;
            foreach($roles as $role){
                if($role->jenis_pegawai=="pegawai"){
                    $SIMPEG->or_where('pegawai.id_pegawai', $role->user_id);
                    $rolePegawai++;
                }
            }
            
            if($rolePegawai > 0){
                $data['pegawai']    = $SIMPEG->select('
                                    pegawai.id_pegawai user_id, 
                                    pegawai.nama_pegawai nama,
                                    pegawai.gelar_depan,
                                    pegawai.gelar_belakang,
                                    pegawai.no_hp,
                                    pegawai.nip username,
                                    pegawai.id_skpd skpd_id,
                                    skpd.nama_skpd,
                                    pangkat.nama_pangkat,
                                    pangkat.kode_pangkat,
                                    jabatan.nama_jabatan,
                                    eselon.nama_eselon,
                                ')
                                ->join('skpd','skpd.id_skpd=pegawai.id_skpd', 'left')
                                ->join('pangkat','pangkat.id_pangkat=pegawai.id_pangkat', 'left')
                                ->join('jabatan','jabatan.id_jabatan=pegawai.id_jabatan', 'left')
                                ->join('eselon','eselon.id_eselon=pegawai.id_eselon', 'left')
                                ->order_by('skpd.nama_skpd', 'asc')
                                ->order_by('pegawai.nama_pegawai', 'asc')
                                ->get('pegawai')
                                ->result();
            }
            
            echo json_encode($data);
            return;

        }
    }

    // END USER -----------------------------------------------------------------------------------------------------------------------------------------------------------


    // START SIMPERNAS -----------------------------------------------------------------------------------------------------------------------------------------------------------
    
    public function simpernasAPISendSPT(){
        $absensingAPI = $this->db->where('website_id', 9)->get('tb_api')->row();
        $simpernasAPI = $this->db->where('website_id', 10)->get('tb_api')->row();

        extract($_POST);
        if($absensingAPI && $simpernasAPI && $simpernasAPI->user_key==$user_key && $simpernasAPI->pass_key==$pass_key){
            $URL            = 'https://absensi-ng.labura.go.id/api/pushspt';

            $data           ="user_key=".$absensingAPI->user_key;
            $data          .="&pass_key=".$absensingAPI->pass_key;
            $data          .="&spt_id=".$_POST['spt_id'];
            $data          .="&skpd_id=".$_POST['skpd_id'];
            $data          .="&skpd_nama=".$_POST['skpd_nama'];
            $data          .="&pegawai_id=".$_POST['pegawai_id'];
            $data          .="&jenis_pegawai=".$_POST['jenis_pegawai'];
            $data          .="&nama_pegawai=".$_POST['nama_pegawai'];
            $data          .="&no_spt=".$_POST['no_spt'];
            $data          .="&tgl_pergi=".$_POST['tgl_pergi'];
            $data          .="&tgl_kembali=".$_POST['tgl_kembali'];
            $data          .="&tgl_keluar=".$_POST['tgl_keluar'];

            $curlHandle     = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, $URL);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curlHandle, CURLOPT_HEADER, 0);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
            curl_setopt($curlHandle, CURLOPT_POST, 1);
            $results = curl_exec($curlHandle);
            curl_close($curlHandle);

            echo $results;
            return;
        }
        echo json_encode(false);
        return;
    }
    
    // END SIMPERNAS -----------------------------------------------------------------------------------------------------------------------------------------------------------

    // START SIMPEG -----------------------------------------------------------------------------------------------------------------------------------------------------------
    public function setDefaultRole(){
        extract($_POST);
        if(isset($_POST['user_key']) && isset($_POST['pass_key']) && isset($_POST['user_id']) && isset($_POST['jenis_pegawai']) && $user_key=="simpeg_diskominfo" && $pass_key=="10100101"){
            $access = [
                        0 => ["website_id"=>1,"role_id"=>5],
                        1 => ["website_id"=>2,"role_id"=>2],
                        2 => ["website_id"=>8,"role_id"=>4],
                        3 => ["website_id"=>9,"role_id"=>4],
                        4 => ["website_id"=>10,"role_id"=>3]
                ];
            foreach($access as $acc){
                $this->db->insert('tb_user_roled_website', [
                        "user_id"       => $user_id,
                        "role_id"       => $acc['role_id'],
                        "jenis_pegawai" => $jenis_pegawai,
                        "website_id"    => $acc['website_id']
                    ]);
            }
            echo json_encode(true);
            return;

        }
        echo json_encode(false);
        return;
	}

    public function clearRole(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode(false);
            return;
        }
        extract($_POST);
        $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();
        if(!$api){ echo json_encode(false);return;}

        if(isset($_POST['pegawai_id']) && isset($_POST['jenis_pegawai'])){
            $meta = $this->db->
                    where('user_id', $pegawai_id)->
					where('jenis_pegawai', $jenis_pegawai)->
					where('website_id', 1)->
					delete('tb_user_roled_website');
 
            echo json_encode($meta);
            return;
        }

        echo json_encode(false);
        return;
    }

    // END SIMPEG   -----------------------------------------------------------------------------------------------------------------------------------------------------------


    // START SKP -----------------------------------------------------------------------------------------------------------------------------------------------------------
    public function getSKP(){
        $this_user_key  = 'APISKP';
        $this_user_pass = '1001';

        if(!isset($_POST['user_key']) || 
           !isset($_POST['pass_key']) || 
           !isset($_POST['bulan']) ||
           !isset($_POST['pegawai_id']) ||
           !isset($_POST['jenis_pegawai'])
           ){
            echo json_encode(array());
            return;
        }
        
        extract($_POST);
        $api            = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();
        if(!$api){ echo json_encode(array());return;}

        extract($_POST);
        
        $URL         = "https://skp.labura.go.id/api/getSKP";
        $posts       = 'user_key='.$this_user_key.'&pass_key='.$this_user_pass.'&pegawai_id='.$pegawai_id.'&jenis_pegawai='.$jenis_pegawai.'&bulan='.$bulan;

        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $URL);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $posts);
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        $results = curl_exec($curlHandle);
        curl_close($curlHandle);
        
        $return = json_decode($results, true);
 
        echo json_encode($return);
        return;
    }
    // END SKP -----------------------------------------------------------------------------------------------------------------------------------------------------------
    // MASYARAKAT -----------------------------------------------------------------------------------------------------------------------------------------------------------


	public function getMasyarakatByWebsite(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode([
                "status"    => "gagal",
            ]);
            return;
		}
		
        extract($_POST);
        $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();

		if(!$api){ 
			echo json_encode([
            	"status"    => "gagal",
			]);
			return;
		}

		$masyarakats   = $this->db->select('
		                            tb_masyarakat.id masyarakat_id,
									tb_masyarakat.*,
									tb_users.no_hp,
								')
						->where('tb_user_roled_website.website_id', $api->website_id)
						->join('tb_user_roled_website','tb_user_roled_website.user_id=tb_masyarakat.id AND tb_user_roled_website.jenis_pegawai="masyarakat"', 'left')
						->join('tb_users','tb_users.user_id=tb_masyarakat.id AND tb_users.jenis="masyarakat"', 'left')
						->get('tb_masyarakat')
						->result();

		echo json_encode([
			"status"    => "berhasil",
			"data"      => $masyarakats,
		]);
		return;

	}

    public function getMasyarakat(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode([
                "status"    => "gagal",
            ]);
            return;
        }

        extract($_POST);
        $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();

		if(!$api){ 
			echo json_encode([
            	"status"    => "gagal",
			]);
			return;
		}

		$masyarakats   = $this->db->select('
									tb_masyarakat.*,
									tb_users.no_hp,
								')
						->join('tb_users','tb_users.user_id=tb_masyarakat.id AND tb_users.jenis="masyarakat"', 'left')
						->get('tb_masyarakat')
						->result();

		echo json_encode([
			"status"    => "berhasil",
			"data"      => $masyarakats,
		]);
		return;

    }

    public function insertMasyarakat(){
        if(!isset($_POST['user_key']) || !isset($_POST['pass_key'])){
            echo json_encode([
                "status"    => "gagal",
            ]);
            return;
        }

        extract($_POST);
        $api        = $this->db->where('user_key', $user_key)->where('pass_key', $pass_key)->get('tb_api')->row();

		if(!$api){ 
			echo json_encode([
            	"status"    => "gagal",
			]);
			return;
		}
        
        if(
            isset($_POST['nama']) &&
            isset($_POST['nik']) &&
            isset($_POST['jenis_kelamin']) &&
            isset($_POST['tempat_lahir']) &&
            isset($_POST['tanggal_lahir']) &&
            isset($_POST['no_hp']) &&
            isset($_POST['alamat'])
        ){  
            extract($_POST);
            $this->db->insert('tb_masyarakat', [
                'nama'          => $nama,
                'nik'           => $nik,
                'jenis_kelamin' => $jenis_kelamin,
                'tempat_lahir'  => $tempat_lahir,
                'tanggal_lahir' => $tanggal_lahir,
                'no_hp'         => $no_hp,
                'alamat'        => $alamat,              
            ]);
            $user_id = $this->db->insert_id();
            $this->db->insert('tb_users', [
                'user_id'       => $user_id,
                'jenis'         => 'masyarakat',      
                'nama'          => $nama,
                'username'      => $nik,
                'password'      => password_hash('123', PASSWORD_DEFAULT),
            ]);
            
            $access = [
                        0 => ["website_id"=>1,"role_id"=>5],
                        1 => ["website_id"=>23,"role_id"=>2],
                ];
            foreach($access as $acc){
                $this->db->insert('tb_user_roled_website', [
                        "user_id"       => $user_id,
                        "role_id"       => $acc['role_id'],
                        "jenis_pegawai" => 'masyarakat',
                        "website_id"    => $acc['website_id']
                    ]);
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

    // END MASYARAKAT -----------------------------------------------------------------------------------------------------------------------------------------------------------

}
