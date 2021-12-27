<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'assets/vendors/jwt/autoload.php';
use Firebase\JWT\JWT;

class Auth extends CI_Controller {
	public function __construct(){
        parent::__construct();
        $this->load->model(['Sms_model']);
        date_default_timezone_set("Asia/Jakarta");
    }

	public function index()
	{

        if ($this->session->userdata('token')) { redirect('home'); return false; }

        $data = [
			"title"				=> "Masuk ke Aplikasi",
        ];

        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if ($this->form_validation->run() == false) {
            $this->load->view('auth', $data);
            return;
        }
        $this->_login();
        return;
    }

	public function lupapassword()
	{
        if ($this->session->userdata('token')) { redirect('home'); return false; }

        $data = [
			"title"				=> "Lupa Password",
        ];
    
        $this->form_validation->set_rules('username', 'Username', 'trim|required');

        if ($this->form_validation->run() == false) {
            $this->load->view('lupapassword', $data);
            return;
        }

        $username    = $this->input->post('username');
        
        $SIMPEG      = $this->load->database('otherdb', TRUE);
        $user        = ['pegawai' => $SIMPEG->get_where('pegawai', ['nip' => $username, 'status_pegawai'=>'pegawai'])->row()];
        $user        = !$user['pegawai'] ? ['tks' => $SIMPEG->get_where('tb_pegawai_tks', ['nik' => $username])->row()] : $user;
        
        $password        = rand(100000, 999999);
        $password_hash   = password_hash($password, PASSWORD_DEFAULT);
        // echo "<pre>";
        // print_r($user);
        // return;
        if(isset($user['pegawai']) && $user['pegawai']){
            // $SIMPEG->where('id_pegawai', $user['pegawai']->id_pegawai)->update('pegawai', ['password'=>$password_hash]);
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
            
            $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Password baru sudah dikirim ke Whatsapp Anda, jika tidak masuk coba sekali lagi. Jika ada kendala hubungi kami di Telegram t.me/egovlabura.</div>');
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

            $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Password baru sudah dikirim ke Whatsapp Anda, jika tidak masuk coba sekali lagi. Jika ada kendala hubungi kami di Telegram t.me/egovlabura.</div>');
        }else{
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">NIP/NIK tidak ditemukan!</div>');
        }
        redirect('auth');
        return;

    }
    

	public function pengaturanakunberhasil(){
    	$data = [
    	    "title"             => "Akun Berhasil Diubah",
    		"page"				=> "pengaturanakunberhasil",
    	];
    	
    	$this->load->view('template/default', $data);

	}
	
	public function pengaturanakun()
	{

        if (!$this->session->userdata('token')) { redirect('auth'); return false; }
        if (!isset($_GET['token'])) { redirect('auth/regetToken'); return false; }
        if ($this->session->userdata('jenis_user')=="masyarakat"){ redirect('auth/pengaturanakunmasyarakat?token='.$_GET['token']); }

        $cek_data = (object) array([
                "no_hp"         => null,
                "nama_pegawai"  => null,
                "nama_tks"      => null
            ]);


        $this->form_validation->set_rules('no_hp', 'No WhatsApp', 'required');
        // $this->form_validation->set_rules('password_konfirmasi', 'Password Konfirmasi', 'required|matches[password_baru]');
        // $this->form_validation->set_rules('password_lama', 'Password Lama', 'required');
        // $this->form_validation->set_rules('password_baru', 'Password Baru', 'required');

        $SIMPEG      = $this->load->database('otherdb', TRUE);
        $jenis_pegawai = $this->session->userdata('jenis_pegawai');

        if($jenis_pegawai == 'pegawai'){
            $cek_data               = $SIMPEG->where('id_pegawai', $this->session->userdata('user_id'))->get('pegawai')->row();
            $pegawai                = $cek_data;
            $gelarDepan             = $pegawai->gelar_depan && $pegawai->gelar_depan!="" ? $pegawai->gelar_depan.". " : null;
            $gelarBelakang          = $pegawai->gelar_belakang && $pegawai->gelar_belakang!="" ? ", ".$pegawai->gelar_belakang : null;
            $cek_data->nama_pegawai = $gelarDepan.$pegawai->nama_pegawai.$gelarBelakang;

        }else{
            $cek_data               = $SIMPEG->where('id', $this->session->userdata('user_id'))->get('tb_pegawai_tks')->row();
        }
        
       $datameta =  $this->db->
                           where('pegawai_id', $this->session->userdata('user_id'))->
                           where('jenis_pegawai', $this->session->userdata('jenis_pegawai'))->
                           get('tb_pegawai_meta')->row();
        
        if ($this->form_validation->run() == false) {
          
    		$data = [
    		    "title"             => "Pengaturan Akun / Ubah Akun - ". ($jenis_pegawai=='pegawai' ? $cek_data->nama_pegawai : $cek_data->nama_tks),
    			"page"				=> "pengaturanakun",
    			"no_hp"             => isset($datameta->no_hp) ? $datameta->no_hp : $cek_data->no_hp,
    		];
    		
    		$this->load->view('template/default', $data);
            return;
        }else{
            $datametanohp   =  $this->db->
                                      where('no_hp', $_POST['no_hp'])->
                                      get('tb_pegawai_meta')->row();

            $nomor_whatsapp_saya    = isset($datameta->no_hp) ? $datameta->no_hp : $cek_data->no_hp;
            if($nomor_whatsapp_saya!=$_POST['no_hp'] && $datametanohp){
	            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Nomor Whatsapp/Handphone sudah digunakan!</div>');
	            redirect('auth/pengaturanakun?token='.$_GET['token']);
	            return;
            }
            
            // $cek_password_lama      = password_verify($_POST['password_lama'], (isset($datameta->password) ? $datameta->password : $cek_data->password));				
			$cek_password_lama = true;
			if($cek_data && $cek_password_lama){
                if(strlen($_POST['password_baru'])<6){
    	            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Password baru tidak bolah kurang dari 6 karakter!</div>');
    	            redirect('auth/pengaturanakun?token='.$_GET['token']);
    	            return;
                }
                
                $data = [
                        // 'password'      => $_POST['password_lama'] && $_POST['password_baru'] != null && $cek_password_lama ? password_hash($_POST['password_baru'], PASSWORD_DEFAULT) : $cek_data->password,
                        'password'      => $_POST['password_baru'] ? password_hash($_POST['password_baru'], PASSWORD_DEFAULT) : $cek_data->password,
                        'no_hp'         => $_POST['no_hp'],
                        'nip'           => $this->session->userdata('username'),
                        'pegawai_id'    => $this->session->userdata('user_id'),
                        'jenis_pegawai' => $this->session->userdata('jenis_pegawai'),
                    ];
            
                if($datameta){
                    $this->db->where('id', $datameta->id)->update('tb_pegawai_meta', $data);
                }else{
                    $this->db->insert('tb_pegawai_meta', $data);
                }
            }else if($cek_data && !$_POST['password_baru']){
                $data = [
                        'password'      => $_POST['password_baru'] && $_POST['password_baru'] != null && $cek_password_lama ? password_hash($_POST['password_baru'], PASSWORD_DEFAULT) : $cek_data->password,
                        'no_hp'         => $_POST['no_hp'],
                        'nip'           => $this->session->userdata('username'),
                        'pegawai_id'    => $this->session->userdata('user_id'),
                        'jenis_pegawai' => $this->session->userdata('jenis_pegawai'),
                    ];
            
                if($datameta){
                    unset($data['password']);
                    unset($data['nip']);
                    unset($data['pegawai_id']);
                    unset($data['jenis_pegawai']);
                    $this->db->where('id', $datameta->id)->update('tb_pegawai_meta', $data);
                }else{
                    $this->db->insert('tb_pegawai_meta', $data);
                }
                
            }elseif(!$cek_password_lama){
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Password lama anda salah, silahkan ulangi !</div>');
	            redirect('auth/pengaturanakun?token='.$_GET['token']);
	            return;
            }

            $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Profil anda berhasil diubah.</div>');
            redirect('auth/pengaturanakunberhasil?token='.$_GET['token']);
            return;
        }

    }

	public function pengaturanakunmasyarakat()
	{

        if (!$this->session->userdata('token')) { redirect('auth'); return false; }
        if (!isset($_GET['token'])) { redirect('auth/regetToken'); return false; }
        if ($this->session->userdata('jenis_user')!="masyarakat"){ redirect('auth/pengaturanakun?token='.$_GET['token']); }

        $user           = $this->db->
                                 where('user_id', $this->session->userdata('user_id'))->
                                 where('jenis', $this->session->userdata('jenis_user'))->
                                 get('tb_users')->row();

        $user_data      = $user ? $user : array();

        $masyarakat     = $this->db->where('id', $this->session->userdata('user_id'))->get('tb_masyarakat')->row();
        $user           = $user ? $user : $masyarakat;
        $user->user_id  = isset($user->user_id) ? $user->user_id : $user->id;
        $user->password = isset($user->password) ? $user->password : password_hash("123", PASSWORD_DEFAULT);

        if((isset($_POST['password_baru']) && $_POST['password_baru']) || (isset($_POST['password_baru']) && $_POST['password_baru'])){
            $this->form_validation->set_rules('password_baru', 'Password Baru', 'required');
        }
        $this->form_validation->set_rules('no_hp', 'No WhatsApp', 'required');
        if ($this->form_validation->run()) {
          
            $ceknomorhp =  $this->db->
                                  where('no_hp', $_POST['no_hp'])->
                                  get('tb_users')->
                                  row();
                                      
            if($user->no_hp!=$_POST['no_hp'] && $ceknomorhp){
	            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Nomor Whatsapp/Handphone sudah digunakan!</div>');
	            redirect('auth/pengaturanakunmasyarakat?token='.$_GET['token']);
	            return;
            }
            
            
            

			// if(isset($_POST['password_lama']) && $_POST['password_lama'] && password_verify($_POST['password_lama'], $user->password)){
				if(isset($_POST['password_baru']) && $_POST['password_baru']){
					if(strlen($_POST['password_baru'])<6){
    	            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Password baru tidak bolah kurang dari 6 karakter!</div>');
    	            redirect('auth/pengaturanakunmasyarakat?token='.$_GET['token']);
    	            return;
                }
                
                $data = [
                        'password'      => password_hash($_POST['password_baru'], PASSWORD_DEFAULT),
                        'no_hp'         => $_POST['no_hp'],
                        'username'      => $this->session->userdata('username'),
                        'user_id'       => $this->session->userdata('user_id'),
                        'jenis'         => $this->session->userdata('jenis_user'),
                    ];
            
                if($user_data){
                    $this->db->where('user_id', $user_data->id)->update('tb_users', $data);
                }else{
                    $this->db->insert('tb_users', $data);
                }
                $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Profil anda berhasil diubah.</div>');
                redirect('auth/pengaturanakunberhasil?token='.$_GET['token']);
                return;

            // }elseif(isset($_POST['password_lama']) && $_POST['password_lama'] && !password_verify($_POST['password_lama'], $user->password)){
            //     $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Password lama anda salah, silahkan ulangi !</div>');
	        //     redirect('auth/pengaturanakunmasyarakat?token='.$_GET['token']);
	        //     return;

            }else if(!$_POST['password_baru'] || $_POST['password_baru']==""){

                $data = [
                        'no_hp'         => $_POST['no_hp'],
                        'username'      => $this->session->userdata('username'),
                        'user_id'       => $this->session->userdata('user_id'),
                        'jenis'         => $this->session->userdata('jenis_pegawai'),
                    ];
            
                if($user_data){
                    $this->db->where('user_id', $user_data->id)->update('tb_users', $data);
                }else{
                    $data['password']   = $user_data->password;
                    $this->db->insert('tb_users', $data);
                }
                $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Profil anda berhasil diubah.</div>');
                redirect('auth/pengaturanakunberhasil?token='.$_GET['token']);
                return;
                
            }


        }

		$data = [
		    "title"             => "Pengaturan Akun / Ubah Akun - ". $user->nama,
			"page"				=> "pengaturanakun",
			"no_hp"             => $user->no_hp,
		];
		
		$this->load->view('template/default', $data);
        return;


    }


    private function _login()
    {
        $username    = $this->input->post('username');
        $password    = $this->input->post('password');

        $user        = $this->db->where('username',$username)->get('tb_users')->row();
        if($user)
        {
            if($user->is_active != 'Ya')
            {
                $this->session->set_flashdata('pesan', '
                <div class="alert alert-danger" role="alert">
                <button type="button" class="close" data-dismiss="alert">x</button>
                Maaf! Akun anda sedang di nonaktifkan</div>
                ');
                redirect('auth');
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
                $user['roles'] = $all_roles;
                $this->session->set_userdata($user);
                redirect('home');
            }
            else
            {
                $this->session->set_flashdata('pesan', '
                <div class="alert alert-danger" role="alert">
                <button type="button" class="close" data-dismiss="alert">x</button>
                Password Salah!</div>
                ');
                redirect('auth');
            }
        }
        else
        {
            $this->session->set_flashdata('pesan', '
            <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert">x</button>
            Username/NIK/NIP tidak ditemukan!</div>
            ');
            redirect('auth');
        }
        return;
    }


	public function regetSession(){
		if(!isset($_COOKIE['labura_layanan_app_token'])){
			redirect('auth');
			return;
		};

		$key        = "123aaaa321";
		$jwt        = $_COOKIE['labura_layanan_app_token'];
		$data       = JWT::decode($jwt, $key, array('HS256'));

		$roles      = json_encode($data->roles);
		$roles      = json_decode($roles,1);

		$domain     = $_SERVER['HTTP_HOST'];
		$key        = array_search($domain, array_column($roles, 'domain'));
		$key        = $key ? $key : 'none';
		$role_id    = isset($roles[$key]) ? $roles[$key] : false;
		if(!$role_id){ 
			redirect('auth/logout');
			return;
		};
		
        $data = [
            'user_id'       => $data->user_id,
            'jenis_pegawai' => $data->jenis_pegawai,
            'nama'          => $data->nama,
            'username'      => $data->username,
            'role_id'       => $role_id['role_id'],
            'roles'         => $roles,
            'skpd_id'       => $data->skpd_id,
            'nama_opd'      => $data->nama_opd,
            'start_token'   => $data->start_token,
            'token'         => $data->token
        ];
        $this->session->set_userdata($data);
		redirect('home?token='.$data['token']);
		return;
	} 

    public function regetToken(){
        if(isset($_GET['token'])){
        
            $token = $this->db->where('token', $this->session->userdata('token'))->get('tb_token')->row();

            if(!$token){ redirect('auth/logout/invalidToken'); return;}
            if($token->status==0){redirect('auth/logout/invalidToken'); return;}

            redirect('home?token='.$this->session->userdata('token'));
            return;
        }
        redirect('auth/regetToken?token='.md5(rand()." - ".time()));
    }

    public function pushLogin()
    {
        if (isset($_POST['token']) && isset($_POST['user_id'])) {
            extract($_POST);
            $today = date("Y-m-d H:i:s");
            $data = [
                "last_actived"          => $today,
                "active"                => 1,
            ];
            $this->db->where('user_id', $user_id)
                     ->where('token', $token)
                     ->update('tb_token', $data);
             

            echo json_encode(true);
        } else {
            echo json_encode(false);
        }
    }

    public function logout($customCapt=false)
    {
        session_destroy();
        redirect('auth');
        return;
    }
    
    public function forcelogout()
    {
        $today = date("Y-m-d H:i:s");
        $data = [
            "last_actived"          => $today,
            "status"                => 0,
        ];

        $this->db->where('user_id', $this->session->userdata('user_id'));
        $this->db->where('token', $this->session->userdata('token'));
        $this->db->update('tb_token', $data);

        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('start_token');
        $this->session->unset_userdata('token');

        setcookie("labura_layanan_app_token", "", time(), "/", ".labura.go.id");

        $this->session->set_flashdata('pesan', '
        <div class="alert alert-danger" role="alert">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <strong>Force Logout !</strong> User tidak dapat mengakses aplikasi ini!
        </div>
        ');

        redirect('auth');
        return;
    }


    //////////////////////////////////////////////////////////////////////// 



    public function parseData(){
        return;
        $page = 0;
        $no = $page;
        $username_no = $page;
        echo "<table cellspacing='0' border='1'>";
        foreach($this->db->select('tb_pegawai.*, tb_opd.nama_opd')->
                           join('tb_opd', 'tb_opd.id=tb_pegawai.opd_id', 'left')->
                        //    limit(100,$page)->
                           get('tb_pegawai')->
                           result() as $pegawai){

            $username = $pegawai->nip ? $pegawai->nip : "user".$username_no;
            echo "<tr>";
            echo "<td>".$no."</td>";
            echo "<td>".$pegawai->nama."</td>";
            echo "<td>".$username."</td>";
            echo "<td>".$pegawai->nama_opd."</td>";
            echo "<td>".$pegawai->username."</td>";
            echo "<td>".$pegawai->password."</td>";
            echo "</tr>";
            // $this->db->where('id', $pegawai->id)->update('tb_pegawai', [
            //     'username'      => $username,
            //     'password'      => password_hash(123, PASSWORD_BCRYPT),
            // ]);
            $username_no++;
            $no++;
        }
        echo "</table>";

    }
    
    public function blocked404(){
        $data = [
            'page'  => 'blocked'
        ];
        $this->load->view('template/custom', $data);
    }
    public function notfound404(){
        $data = [
            'page'  => '404'
        ];
        $this->load->view('template/custom', $data);
    }
}
