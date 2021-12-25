<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usermasyarakat extends CI_Controller {
	
	public function __construct(){
        parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
        $this->load->model(['Api_model','Opd_model','Pegawai_model','Pegawaitks_model']);
		is_logged_in();
    }
    
    public function getAllMasyarakat(){
	    $this->accessable([1]);
		extract($_POST);
		
		$total  = $this->db->select('
									tb_masyarakat.*,
									tb_users.no_hp,
								')
						->join('tb_users','tb_users.user_id=tb_masyarakat.id AND tb_users.jenis="masyarakat"', 'left')
						->get('tb_masyarakat')
						->num_rows();

		if(isset($search['value']) && $search['value']){
			$this->db->group_start()
						->like('tb_masyarakat.nama', $search['value'])
						->or_like('tb_masyarakat.no_hp', $search['value'])
						->or_like('tb_masyarakat.nik', $search['value'])
						->or_like('tb_users.no_hp', $search['value'])
					 ->group_end();
		}

		$this->db->limit($length, $start);
		$masyarakats   = $this->db->select('
									tb_masyarakat.*,
									tb_users.no_hp,
								')
						->join('tb_users','tb_users.user_id=tb_masyarakat.id AND tb_users.jenis="masyarakat"', 'left')
						->get('tb_masyarakat')
						->result();

        $no = 1;
        $data = array();
        foreach ($masyarakats as $dt) {

            $d[0]      	= $no;$no++;
            $d[1]      	= $dt->nama;
            $d[2]      	= $dt->nik;
            $d[3]      	= $dt->no_hp;
            $d[4]      	= ($this->session->userdata('role_id') == 1 ?
                        '
                        <a href="'.base_url('usermasyarakat/pengaturanakun/') . $dt->id . '?token=' . $_GET['token'].'" class="btn btn-info btn-sm" title="Pengaturan Akun" style="padding: 5px 9px"><em class="ti-settings"></em></a>
                        <a href="'.base_url('usermasyarakat/konfigurasirole/') . $dt->id . '?token=' . $_GET['token'].'" class="btn btn-info btn-sm" title="Konfigurasi Role" style="padding: 5px 9px"><em class="ti-exchange-vertical"></em></a>
                        ' : null).
                        ' <a href="'.base_url('usermasyarakat/edit/' . $dt->id . '?token=' . $_GET['token']).'" class="btn btn-warning btn-sm" title="Ubah" style="padding: 5px 9px"><em class="ti-pencil-alt"></em></a>
                        '.($this->session->userdata('role_id')==1 ? '<a href="'.base_url('usermasyarakat/delete/' . $dt->id . '?token=' . $_GET['token']).'" class="btn btn-danger btn-sm" onclick="javascript: return confirm(\'Anda yakin hapus ?\')" title="Hapus" style="padding: 5px 9px"><em class="ti-trash"></em></a>':null);
        
            $data[] = $d;
        }
        
        echo json_encode([
				"draw"=>$draw,
				"recordsTotal"=>$total,
				"recordsFiltered"=>isset($search['value']) && $search['value'] ? count($masyarakats) : $total,
				"data"=> $data
			]);
        return;

    }

    public function index(){
	    $this->accessable([1]);
        
		$data = [
			"page"				=> "usermasyarakat/data_masyarakat_serverside",
			"title"             => "Data Masyarakat",
			"css"				=> [
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
			],
		];
		$this->load->view('template/default', $data);
    }
    
    public function tambah(){
		$this->accessable([1]);

		$this->form_validation->set_rules('nama', 'Nama', 'required');
		$this->form_validation->set_rules('nik', 'NIK', 'required|min_length[16]|max_length[16]|is_unique[tb_masyarakat.nik]');
        $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required');
        $this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'required');
        $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'required');
        $this->form_validation->set_rules('no_hp', 'Nomor HP', 'required');
    	$this->form_validation->set_rules('alamat', 'Alamat', 'required');

		if($this->form_validation->run()) {
		    extract($_POST);
            $this->db->insert('tb_masyarakat', [
                'nama'          => $nama,
                'nik'           => $nik,
                'jenis_kelamin' => $jenis_kelamin,
                'tempat_lahir'  => $tempat_lahir,
                'tanggal_lahir' => date('Y-m-d', strtotime($tanggal_lahir)),
                'no_hp'         => $no_hp,
                'alamat'        => $alamat,              
            ]);
            $user_id = $this->db->insert_id();
            $this->db->insert('tb_users', [
                'user_id'       => $user_id,
                'jenis'         => 'masyarakat',      
                'nama'          => $nama,
                'no_hp'         => $no_hp,
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

            $this->session->set_flashdata('pesan', '
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Data Masyarakat berhasil ditambah!</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            ');
			redirect('usermasyarakat?token=' . $_GET['token']);
			return;
		}

		$this->load->view('template/default', [
			"page"				=> "usermasyarakat/tambah_masyarakat",
			"title"             => "Tambah Data Masyarakat",
		]);

    }
    
    public function edit($id){
		$this->accessable([1]);

		$this->form_validation->set_rules('nama', 'Nama', 'required');
		$this->form_validation->set_rules('nik', 'NIK', 'required|min_length[16]|max_length[16]');
        $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required');
        $this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'required');
        $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'required');
        $this->form_validation->set_rules('no_hp', 'Nomor HP', 'required');
    	$this->form_validation->set_rules('alamat', 'Alamat', 'required');
		
		if($this->form_validation->run()) {
		    extract($_POST);
            $this->db->where('id', $id)->update('tb_masyarakat', [
                'nama'          => $nama,
                'nik'           => $nik,
                'jenis_kelamin' => $jenis_kelamin,
                'tempat_lahir'  => $tempat_lahir,
                'tanggal_lahir' => date('Y-m-d', strtotime($tanggal_lahir)),
                'no_hp'         => $no_hp,
                'alamat'        => $alamat,              
            ]);
            $this->db->where('user_id', $id)->where('jenis', 'masyarakat')->update('tb_users', [
                'jenis'         => 'masyarakat',      
                'nama'          => $nama,
                'no_hp'         => $no_hp,
                'username'      => $nik,
            ]);

			$this->session->set_flashdata('pesan', '
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                Data Masyarakat berhasil diubah
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
               ');
             redirect('usermasyarakat?token=' . $_GET['token']);
		}
		$this->load->view('template/default', [
			"page"				=> "usermasyarakat/ubah_masyarakat",
			"title"             => "Ubah Data Masyarakat",
			"masyarakat"        => $this->db->where('id', $id)->get('tb_masyarakat')->row(),
		]);
		return;
    }
    
    public function delete($id)
    {
		$this->accessable([1]);
		$this->db->where('user_id', $id)->where('jenis', 'masyarakat')->delete('tb_users');
		$this->db->where('id', $id)->delete('tb_masyarakat');
		$this->session->set_flashdata('pesan', '
		<div class="alert alert-success alert-dismissible fade show" role="alert">
		<strong>Data masyarakat berhasil dihapus !</strong>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		');
		redirect('usermasyarakat?token=' . $_GET['token']);
		return;
    }



    public function konfigurasirole($user_id=false){
	    $this->accessable([1]);

		$masyarakat     = $this->db->where('id', $user_id)->get('tb_masyarakat')->row();
        
        if(!$masyarakat){
             $this->session->set_flashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Maaf</strong> User tidak ditemukan !
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
               ');
            redirect('usermasyarakat?token=' . $_GET['token']);
            return;
        }		

		if(!isset($_POST['role_in'])) {
		    $this->load->view('template/default', [
				"title"             => "Konfigurasi Role Pengguna Masyarakat - ".$masyarakat->nama,
				"websites"          => $this->db->select('tb_websites.*, tb_api.id api_id')
												->join('tb_api','tb_api.website_id=tb_websites.id','left')
												->order_by('tb_websites.nama_website', 'asc')
												->get('tb_websites')
												->result(),
				"masyarakat"        => $masyarakat,
				"page"				=> "usermasyarakat/konfigurasirole",
			]);

		}else{
            $this->db
                ->where('user_id', $user_id)
                ->where('jenis_pegawai', 'masyarakat')
                ->delete('tb_user_roled_website');
            foreach($_POST['role_in'] as $website_id=>$role_id):
                if($role_id == 0) continue;
                $this->db->insert('tb_user_roled_website', [
                        "user_id"       => $user_id,
                        "role_id"       => $role_id,
                        "jenis_pegawai" => 'masyarakat',
                        "website_id"    => $website_id
                    ]);
            endforeach;
             $this->session->set_flashdata('pesan', '
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil!</strong> Akses role user masyarakat berhasil di perbaharui !
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
               ');
             redirect('usermasyarakat?token=' . $_GET['token']);
      }
    }
    
      public function generaterole(){
	    $this->accessable([1]);
        $websites    = $this->db->order_by('nama_website', 'asc')->get('tb_websites')->result();

		$this->form_validation->set_rules('website_id', 'Website', 'required');
		$this->form_validation->set_rules('role', 'Role', 'required');
		
		if ($this->form_validation->run()) {
            extract($_POST);            
            if($opd_id==0) {
                $this->generateAll($website_id, $role);
                return;
            }
            
            $values = "INSERT INTO tb_user_roled_website (website_id, user_id, role_id, jenis_pegawai)VALUES";
            $first = true;
            $num = 0;
            foreach($pegawai as $key=>$pegawai){
                if($action == 'skip'){
                    $cek_exists = $this->db
                                        ->where('website_id', $website_id)
                                        ->where('user_id', $pegawai)
										->where('jenis_pegawai', 'masyarakat')
                                        ->get('tb_user_roled_website')
                                        ->num_rows();
                    if($cek_exists>0) continue;
                }
                $this->db
                        ->where('website_id', $website_id)
						->where('user_id', $pegawai)
						->where('jenis_pegawai', 'masyarakat')
                        ->delete('tb_user_roled_website');
                
                if($role==0) continue;

                if(!$first) $values .= ",";
                $values .= "($website_id, $pegawai, $role, 'masyarakat')";
                $first = false;
                $num++;
            }
            
            if($num>0) $this->db->query($values);
            
            $this->session->set_flashdata('pesan', '
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                Data berhasil digenerate!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
               ');
            redirect('usermasyarakat/generaterole?token=' . $_GET['token']);
			return;
		}
		$this->load->view('template/default', [
			"title"             => "Generate Role Masyarakat",
			"page"				=> "usermasyarakat/generaterole",
			"websites"          => $websites,
		]);
		return;

    }

    private function generateAll($website_id, $role){
		$masyarakat = $this->db->get('tb_masyarakat')->result();

		$this->db
				->where('website_id', $website_id)
				->where('jenis_pegawai', 'masyarakat')
				->delete('tb_user_roled_website');

		$values = "INSERT INTO tb_user_roled_website (website_id, user_id, role_id, jenis_pegawai)VALUES";
		$first  = true;
		$num    = 0;

		if($role!=0){
			foreach($masyarakat as $masyarakat){
				if(!$first) $values .= ",";
				$values     .= "($website_id, $pegawai->user_id, $role, 'masyarakat')";
				$first       = false;
				$num++;
			}

			if($num>0) $this->db->query($values);
		}

		$this->session->set_flashdata('pesan', '
			<div class="alert alert-success alert-dismissible fade show" role="alert">
			Data berhasil digenerate!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			</div>');
		redirect('usermasyarakat/generaterole?token=' . $_GET['token']);
		return;
    }

    public function getmasyarakat(){
		$masyarakat      = $this->db->select('tb_masyarakat.id, tb_masyarakat.nama, tb_masyarakat.nik nip')
                                    ->get('tb_masyarakat')
                                    ->result();
		echo json_encode(["masyarakat"=>$masyarakat]);
		return;
    }

    public function getrole_generaterole(){
	    $this->accessable([1]);
        if(isset($_POST['website_id'])){
            $website = $this->db->where('id', $_POST['website_id'])->get('tb_websites')->row();
            if(!$website){echo "Tidak tersambung";return;}
            
            if($website->jenis_auth=="default"):
                $roles = $this->db->where('website_id', $website->id)->get('tb_role')->result();

                if(count($roles)==0){echo "Tidak ada Role";return;}

                $roled = "";
                    $roled.="<label style='margin-right: 5px;'><input type='radio' name='role' value='0' checked> Tidak ada akses</label>";                    
                foreach($roles as $role){
                    $roled.="<label style='margin-right: 5px;'><input type='radio' name='role' value='".$role->role_id."'> ".$role->role_name."</label>";                    
                }
            else:
                $this->load->model('Api_model');
                $roles = $this->Api_model->role_api($website->id, 'get');
    
                if(!$roles){echo "Tidak tersambung";return;}
    
                $roled = "";
                    $roled.="<label style='margin-right: 5px;'><input type='radio' name='role' value='0' checked> Tidak ada akses</label>";                    
                foreach($roles['data'] as $role){
                    $roled.="<label style='margin-right: 5px;'><input type='radio' name='role' value='".$role['role_id']."'> ".$role['role_name']."</label>";                    
                }
            endif;
            
            echo $roled;
            return;
        }
        echo "Tidak tersambung";
        return;
    }

   
	public function pengaturanakun($user_id)
	{
	    $this->accessable([1]);
        $this->form_validation->set_rules('no_hp', 'No WhatsApp', 'required');

		$masyarakat        = $this->db->where('user_id', $user_id)->where('jenis', 'masyarakat')->get('tb_users')->row();
        
        if(!$masyarakat){
            $this->session->set_flashdata('pesan', '
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal!</strong> User tidak ditemukan !
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
               ');
            redirect('usermasyarakat?token=' . $_GET['token']);
        }


        $pegawaiMeta            = $this->db->
                                         where('user_id', $user_id)->
                                         where('jenis', 'masyarakat')->
                                         get('tb_users')->
                                         row();


        if($this->form_validation->run()) {
            $datametanohp   =  $this->db->
									  where('user_id', $user_id)->
									  where('jenis', 'masyarakat')->
                                      where('no_hp', $_POST['no_hp'])->
                                      get('tb_users')->row();

            $nomor_whatsapp_saya    = isset($pegawaiMeta->no_hp) ? $pegawaiMeta->no_hp : $masyarakat->no_hp;
            if($nomor_whatsapp_saya!=$_POST['no_hp'] && $datametanohp){
	            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Nomor Whatsapp/Handphone sudah digunakan!</div>');
	            redirect('usermasyarakat/pengaturanakun/'.$user_id.'?token='.$_GET['token']);
	            return;
            }

            $data = [
                    'password'      => $_POST['password_baru']!= null ? password_hash($_POST['password_baru'], PASSWORD_DEFAULT) : 
                                        (isset($pegawaiMeta->password) ? $pegawaiMeta->password : password_hash('123', PASSWORD_DEFAULT)),
                    'no_hp'         => $_POST['no_hp'],
                    'username'      => $masyarakat->username,
                    'user_id'    	=> $user_id,
                    'jenis' 		=> 'masyarakat',
                ];
        
            if($pegawaiMeta){
                $this->db->where('id', $pegawaiMeta->id)->update('tb_users', $data);
            }else{
                $this->db->insert('tb_users', $data);
            }

            $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Profil berhasil diubah.</div>');
            redirect('usermasyarakat/pengaturanakun/'.$user_id.'?token='.$_GET['token']);
            return;
        }

		$this->load->view('template/default', [
		    "title"             => "Pengaturan Akun / Ubah Akun : ".(isset($pegawaiMeta->nama) ? $pegawaiMeta->nama : $masyarakat->nama),
			"page"				=> "pengaturanakun",
			"no_hp"             => isset($pegawaiMeta->no_hp) ? $pegawaiMeta->no_hp : $pegawai->no_hp,
			"is_admin"          => true
		]);
        return;
    }
    private function accessable($roles){
        if(!in_array($this->session->userdata('role_id'), $roles)){
            redirect('home?token='.$_GET['token']);
            exit();
        }
    }

}
