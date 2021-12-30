<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Google\Cloud\Storage\StorageClient;

class Absensi extends CI_Controller {
    
    public $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
    public $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    
	public function __construct(){
        parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
		is_logged_in();
		$this->load->model([
            'LogAbsen_model',
            'Pegawai_model',
            'Skpd_model',
            'Unitkerja_model',
            'AbsenManual_model'
        ]);
    }
    
    public function index()
    {
        $data = [
		    "title"             => "Absensi Harian Pegawai",
			"page"				=> "absensi/absensiharian",
			"skpd"              => $this->session->userdata('role_id')==1 || $this->session->userdata('role_id')==2 ? $this->Skpd_model->get() : $this->Skpd_model->get($this->session->userdata('skpd_id')),
			"javascript"		=> [
				base_url("assets/vendors/datatables.net/jquery.dataTables.js"),
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js"),
			],
			"css"				=> [
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
			],
		];
		
		$this->load->view('template/default', $data);
    }

    public function rekapitulasi()
    {
        $data = [
		    "title"             => "Rekapitulasi Absensi",
			"page"				=> "absensi/rekapitulasi",
			"javascript"		=> [
				base_url("assets/vendors/datatables.net/jquery.dataTables.js"),
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js"),
			],
			"css"				=> [
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
			],
		];
		
		$this->load->view('template/default', $data);
    }

    public function cetak($opd_id)
    {
        $opd     = $this->db->where('id',$opd_id)->get('tb_opd')->row();
        $pegawai = $this->db->where('opd_id',$opd_id)->get('tb_pegawai')->result();
        $kepala  = $this->db->where('opd_id',$opd_id)->where('kepala',1)->get('tb_pegawai')->row();
            
        $from = $_GET['from'] . " 00:00:00";
        $from = date_create($from);

        $to   = $_GET['to'] . " 23:59:59";
        $to   = date_create($to);
        
        $diff = (array) date_diff($from,$to);
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($from, $interval ,$to);

        $bukan_hari_kerja = [];
        $pegawai_date = [];
        $pegawai_kerja = [];
        $pegawai_total = [];
        foreach($daterange as $d)
        {
            // check if date is sabtu or minggu
            $tanggal = $d->format('Y-m-d');
            $weekend = isWeekend($tanggal);
            $libur = $this->db->where('tanggal', $tanggal)->get('tb_upacara_libur')->row();
            // $datelists[$d->format('Y-m-d')] = $weekend || $libur ? 0 : 1;
            foreach($pegawai as $p)
            {
                if(!isset($pegawai_kerja[$p->id])) $pegawai_kerja[$p->id] = 0;
                if(!isset($pegawai_total[$p->id])) $pegawai_total[$p->id] = 0;

                if($weekend)
                {
                    $bukan_hari_kerja[$tanggal] = 'Weekend'; 
                    $pegawai_date[$p->id][$tanggal] = ['',0];
                    continue;
                }

                if($libur)
                {
                    if($libur->kategori == 'Libur')
                    {
                        $bukan_hari_kerja[$tanggal] = $libur->nama_hari;
                        $pegawai_date[$p->id][$tanggal] = ['',0];
                        continue;
                    }
                }
                $absensi = $this->db->where('pegawai_id',$p->id)->like('jam',$tanggal)->get('tb_absensi')->result();
                $p_count = 0;
                $percent_count = 0;
                if(empty($absensi))
                {
                    // cek apakah ada izin atau sakit
                    $tanggal_izin = $d->format('Y-m');
                    $izin_kerja = $this->db->where('pegawai_id',$p->id)->like('tanggal_awal',$tanggal_izin)->or_like('tanggal_akhir',$tanggal_izin)->get('tb_izin_kerja')->result();
                    $izin_rate = [
                        'Izin Tugas Dinas' => 0,
                        'Izin Datang Terlambat' => 0,
                        'Izin Tidak Masuk' => 1,
                        // 'Izin Cepat Pulang' => 1,
                        'Sakit' => 0.5,
                        'Cuti Besar' => 2.5,
                        'Cuti Keguguran' => 0,
                        'Cuti Bersalin' => 0,
                        'Cuti Tahunan' => 1.5,
                        'Cuti Alasan Penting' => 0
                    ];
                    $izin_key = '';
                    $izin_multiply = 1;
                    foreach($izin_kerja as $ik)
                    {
                        $tgl_awal  = strtotime($ik->tanggal_awal);
                        $tgl_akhir = strtotime($ik->tanggal_akhir);
                        $sekarang  = strtotime($tanggal);

                        // cek if d is between tgl_awal and tgl_akhir
                        if(!($sekarang >= $tgl_awal && $sekarang <= $tgl_akhir)) continue;

                        if($ik->status == 1){
                            $izin_key = $ik->status;
                            if($ik->status == 'Sakit')
                            {
                                $last_day = strtotime($ik->tanggal_awal. " +3 days");
                                $diff_sakit = (array) date_diff($ik->tanggal_awal,$ik->tanggal_akhir);
                                if(!($diff_sakit['days'] > 3 && $sekarang > $last_day))
                                    $izin_multiply = 0;
                            }

                            if($ik->status == 'Cuti Bersalin')
                            {
                                $last_day = strtotime($ik->tanggal_awal. " +5 days");
                                $diff_sakit = (array) date_diff($ik->tanggal_awal,$ik->tanggal_akhir);
                                if(!($diff_sakit['days'] > 5 && $sekarang > $last_day))
                                    $izin_multiply = 0;
                            }

                            if($ik->status == 'Cuti Alasan Penting')
                            {
                                $diff_sakit = (array) date_diff($ik->tanggal_awal,$ik->tanggal_akhir);
                                if($diff_sakit['days'] < 10)
                                    $izin_rate['Cuti Alasan Penting'] = 1.5;
                                elseif($diff_sakit['days'] < 20)
                                    $izin_rate['Cuti Alasan Penting'] = 2;
                                elseif($diff_sakit['days'] < 30)
                                    $izin_rate['Cuti Alasan Penting'] = 2.5;
                            }
                            break;
                        }
                    }

                    if($izin_key)
                    {
                        $pegawai_date[$p->id][$tanggal] = ['',$izin_rate[$izin_key]*$izin_multiply];
                        continue;
                    }

                    // jika tanpa keterangan
                    $pegawai_date[$p->id][$tanggal] = ['1p',3];
                    $pegawai_total[$p->id] += 3;
                    continue;
                }

                $is_masuk  = false;
                $is_pulang = false;
                $is_upacara = false;
                foreach($absensi as $abs)
                {
                    if($abs->jenis_absen == 'Absen Masuk')
                        $is_masuk = $abs->jam;

                    if($abs->jenis_absen == 'Absen Pulang')
                        $is_pulang = $abs->jam;

                    if($abs->jenis_absen == 'Absen Upacara')
                        $is_upacara = $abs->jam;
                }

                $jamKerjaPegawai = $this->db->where('pegawai_id',$p->id)->where('tanggal',$tanggal)->get('tb_jam_kerja_pegawai')->row();
                if(empty($jamKerjaPegawai))
                {
                    // get from default jam kerja
                    $jamKerjaPegawai = $this->db->where('is_default',1)->get('tb_jam_kerja')->row();
                    $jamKerjaPegawai = $this->db->where('jam_kerja_id',$jamKerjaPegawai->id)->where('hari',$d->format('N'))->get('tb_jam_kerja_meta')->row();
                }


                if($libur->kategori == 'Upacara' && !$is_upacara)
                {
                    $p_count++;
                    $percent_count += 2;
                }

                // if masuk telat
                if($is_masuk)
                {
                    $pegawai_kerja[$p->id]++;
                    if((date('H',strtotime($is_masuk)) >= 9 || date('H',strtotime($is_masuk)) <= 10))
                    {
                        $p_count++;
                        $percent_count += 2;
                    }
                    elseif(date('H',strtotime($is_masuk)) > 10)
                    {
                        $p_count++;
                        $percent_count += 3;
                    }
                }
                
                // if pulang telat
                if($is_pulang)
                {
                    $jam_pulang   = strtotime($is_pulang);
                    $harus_pulang = strtotime($jamKerjaPegawai->jam_awal_pulang);
                    $selisih     = round(($harus_pulang - $jam_pulang) / 60,2);
                    if($selisih > 0)
                    {
                        $izin_kerja = $this->db->where('pegawai_id',$p->id)->where('tanggal_awal',$tanggal)->where('jenis_izin','Izin Cepat Pulang')->where('status',1)->get('tb_izin_kerja')->num_rows();
                        if($izin_kerja)
                        {
                            $p_count++;
                            $percent_count += 1;
                        }
                        else
                        {

                            if($selisih >= 1 || $selisih <= 30)
                            {
                                $p_count++;
                                $percent_count += 0.5;
                            }
                            elseif($selisih >= 31 || $selisih <= 60)
                            {
                                $p_count++;
                                $percent_count += 1;
                            }
                            elseif($selisih >= 61 || $selisih <= 90)
                            {
                                $p_count++;
                                $percent_count += 1.5;
                            }
                            elseif($selisih >= 91 || $selisih <= 120)
                            {
                                $p_count++;
                                $percent_count += 2;
                            }
                            elseif($selisih > 120)
                            {
                                $p_count++;
                                $percent_count += 3;
                            }
                        }
                    }
                }

                $pegawai_date[$p->id][$tanggal] = [$p_count.'p',$percent_count];
                $pegawai_total[$p->id] += $percent_count;
            }
        }

        $jumlah_hari_kerja = ($diff['days']+1) - count($bukan_hari_kerja);

        $data = [
		    "opd"       => $opd,
            "pegawai"   => $pegawai,
            "pegawai_date"   => $pegawai_date,
            "pegawai_kerja"  => $pegawai_kerja,
            "pegawai_total"  => $pegawai_total,
            "kepala"    => $kepala,
            "daterange" => $daterange,
            "datelists" => $datelists,
            "diffdays"  => $diff['days'],
            "jumlah_hari_kerja"  => $jumlah_hari_kerja,
            "bukan_hari_kerja"  => $bukan_hari_kerja,
		];
		
		$this->load->view('absensi/cetak-rekapitulasi', $data);
    }
    
    public function foto()
    {
        $data = [
		    "title"             => "Foto Absensi Harian Pegawai",
			"page"				=> "absensi/fotoabsensiharian",
			"skpd"              => $this->Skpd_model->getSkpd(),

			"javascript"		=> [
				base_url("assets/vendors/datatables.net/jquery.dataTables.js"),
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js"),
				base_url('assets/vendors/jquery-toast-plugin/jquery.toast.min.js')

			],
			"css"				    => [
				base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
				base_url('assets/vendors/jquery-toast-plugin/jquery.toast.min.css')
			],
		];
		
		$this->load->view('template/default', $data);
    }
    public function getFotoAbsensiHarianPegawai(){
        if (!isset($_POST['tanggal']) || 
            $_POST['tanggal'] == "") {
            return;
        }
        
        extract($_POST);
        $pegawais   = $this->Pegawai_model->getPegawai(null, $skpd_id);
        $tkss       = $this->Pegawai_model->getPegawaiTks(null, $skpd_id);
        $tanggal    = date("Y-m-d", strtotime($_POST['tanggal']));
        if($skpd_id){
            $this->db->where('tb_absensi.skpd_id', $skpd_id);
        }
        if($jenis_pegawai){
            $this->db->where('tb_absensi.jenis_pegawai', $jenis_pegawai);
        }
        $absensis   = $this->db->where("DATE_FORMAT(tb_absensi.jam,'%Y-%m-%d')", $tanggal)->
                                 get('tb_absensi')->
                                 result();

        $jumlah = 0;
        foreach($absensis as $absensi){
            $indexPegawai   = array_search($absensi->pegawai_id, array_column($pegawais, 'user_id'));
            $indexTks       = array_search($absensi->pegawai_id, array_column($tkss, 'user_id'));
            $pegawai        = $absensi->jenis_pegawai=='pegawai' ? 
                              (isset($pegawais[$indexPegawai]) ? $pegawais[$indexPegawai] : ['username'=>'undefined']) : 
                              (isset($tkss[$indexTks])? $tkss[$indexTks]         : ['username'=>'undefined']);
            $jam            = $absensi->jam;
            $url            = 'https://storage.googleapis.com/file-absensi/file_absensi/'.$pegawai['username'].'/'.$jam.'.png';

            echo "<div class='col-md-3 col-lg-2' style='margin-bottom: 15px;'>";
            echo '<img src="'.$url.'" width="100%" alt="'.$jam.'" class="img-thumbnail" />
                    <div style="margin-bottom: 0px;margin-top: 0px;max-width: 100%;overflow: hidden;white-space: nowrap;">
                        <center>
                            <div style="font-weight: 700;font-size: 12px" title="'.$pegawai['nama'].'">'.$pegawai['nama'].'</div>
                            <div style="font-weight: 300;font-size: 12px" title="'.$pegawai['nama_skpd'].'">'.$pegawai['nama_skpd'].'</div>
                        </center>
                    </div>
                    <div style="margin-bottom: 3px; font-size: 12px; background: #cef5d0;">
                        <center>
                            <small>'.$jam.'</small>
                        </center>
                    </div>';
            echo '<div>
                    <div class="btn-group btn-block">
                      <button type="button" onclick="sendSMS('.$absensi->id.')" class="btn btn-ss btn-outline-danger">Pesan</button>
                        <div class="btn-group">
                          <button type="button" class="btn btn-ss btn-outline-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <em class="ti-more"></em>
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="javascript:;" onclick="sendSMSdanNonaktifkan('.$absensi->id.',)">Hapus, Kirim Pesan dan Nonaktifkan Akun</a>
                          </div>
                        </div>

                    </div>
            </div>';

            echo "</div>";
            $jumlah++;
        }
        return;



        $jenis_pegawai  = $jenis_pegawai=="" ? null : $jenis_pegawai;
        $skpd_id        = $skpd_id=="" ? null : $skpd_id;

        $akses           = [1,2];
        $skpd_id         = in_array($this->session->userdata('role_id'), $akses) ? $_POST['skpd_id'] : $this->session->userdata('skpd_id');
        $tanggal         = date("Y-m-d", strtotime($_POST['tanggal']));
        $skpd            = $this->Skpd_model->getSkpd();
        $datas = array();
        $pegawai     =  $jenis_pegawai==null ? array_merge($this->Pegawai_model->getPegawai(null, $skpd_id), $this->Pegawai_model->getPegawaiTks(null, $skpd_id)) : ($jenis_pegawai == 'pegawai' ? 
                        $this->Pegawai_model->getPegawai(null, $skpd_id) : 
                        $this->Pegawai_model->getPegawaiTks(null, $skpd_id));
                        
        array_multisort(array_column($pegawai, 'nama'), SORT_ASC, $pegawai);
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

        $all_files = $bucket->objects([
            'prefix' => "file_absensi",
            'fields' => 'items/name'
        ]);
        $no=1;
        $jumlah = 0;
        $pegawais = [];
        foreach ($pegawai as $pg) {
            $pegawais[$pg['username']] = [
                'nama' => $pg['nama'],
                'nama_skpd' => $pg['nama_skpd'],
                'skpd_id' => $pg['skpd_id'],
                'status' => isset($pg['tks_id']) ? 'tks' : 'pegawai',
                'user_id' => $pg['user_id']
            ];
        }
			
        // $all_files = glob("file_absensi/".$pg['username']."/*.*");
        // for ($i=0; $i<count($all_files); $i++){
        foreach($all_files as $key => $file){
            $i = $key+1;
            $image_name = $file->name();
            $supported_format = array('gif','jpg','jpeg','png');
            $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            if (in_array($ext, $supported_format)){
                $full_name = explode('/',$image_name);
                $username = $full_name[1];
                if(strpos(basename($image_name), date("Y-m-d", strtotime($tanggal)))!==false && isset($pegawais[$username])){
                    echo "<div class='col-md-3 col-lg-2' style='margin-bottom: 15px;'>";
                    echo '<img src="https://storage.googleapis.com/file-absensi/'.$image_name.'" width="100%" alt="'.$image_name.'" class="img-thumbnail" />
                            <div style="margin-bottom: 0px;margin-top: 0px;max-width: 100%;overflow: hidden;white-space: nowrap;">
                                <center>
                                    <div style="font-weight: 700;font-size: 12px">'.$pegawais[$username]['nama'].'</div>
                                    <div style="font-weight: 300;font-size: 12px">'.$pegawais[$username]['nama_skpd'].'</div>
                                </center>
                            </div>
                            
                            
                            <div style="margin-bottom: 3px; font-size: 12px; background: #cef5d0;">
                                <center>
                                    <small>'.basename($full_name[2]).'</small>
                                </center>
                            </div>';
                    echo '<div style="margin-top: 0px;margin-bottom:10px;background:#F4A460">
                            <center>
                                <small>
                                    <a href="'.base_url('absensi/pesan/'.$pegawais[$username]['status'].'/'.$pegawais[$username]['user_id'].'/'.$i .'?token='.$_GET['token'].'').'" style=" text-decoration: none;color:white;" >Hapus & Kirim Pesan</a>
                                </small>
                            </center>
                        </div>';
                    
                    echo "</div>";
                    $jumlah++;
                }else{
                    continue;
                }
            }
            
        }
        if($jumlah==0){
            echo '<h3 align="center">Tidak ada foto absensi!</h3>';
        }
    }
    
    
    public function pesan($id, $forceShutdown=false)
    {

        $absensi    = $this->db->where('id', $id)->
                                 get('tb_absensi')->
                                 row();
        if(!$absensi){
            echo json_encode([
                "alert"     => "danger",
                "color"     => "red",
                "message"   => "Data tidak ditemukan!"
            ]);
            return;
        }

        $pegawai        = $absensi->jenis_pegawai=="pegawai" ? 
                            $this->Pegawai_model->getPegawai($absensi->pegawai_id, null) :
                            $this->Pegawai_model->getPegawaiTks($absensi->pegawai_id, null);
        
        if(isset($pegawai[0]) && $pegawai[0]){
            $pegawaiMeta    = $this->Pegawai_model->getPegawaiMeta($absensi->pegawai_id, $absensi->jenis_pegawai);
            $pegawai        = $pegawai[0];
            
            $jam            = $absensi->jam;
            $url            = 'https://storage.googleapis.com/file-absensi/file_absensi/'.$pegawai['username'].'/'.str_replace(' ','%20',$jam).'.png';
            $pesan          = "Halo, ".$absensi->nama_pegawai." Anda terdeksi melakukan manipulasi absensi, pada tanggal " .date('d-m-Y', strtotime($jam))." pukul ".date('H:i', strtotime($jam)).". Tindakan tersebut dapat dikenakan sanksi disiplin. Berikut bukti manipulasi : ". $url; 

            if($pegawaiMeta || $pegawai){

				$message	= "Pesan berhasil dikirim!"; 
				if($forceShutdown){
					$clearRole	= $this->clearRole($absensi->pegawai_id, $absensi->jenis_pegawai);
					$message	= $clearRole ? "Pesan berhasil dikirim, dan user berhasil di nonaktifkan!" : "Pesan berhasil dikirim, dan user tidak berhasil di nonaktifkan!";
					$pesan		.= 'Maka dengan ini akun anda dimatikan, silahkan hubungi pihak yang bersangkutan!'; 
				}
                $no_hp      = $pegawaiMeta ? $pegawaiMeta['no_hp'] : $pegawai['no_hp'];
                $this->Sms_model->send($no_hp, $pesan);
                $this->Notifikasi_model->send(array(
                          'user_id'         => $absensi->pegawai_id,
                          'jenis_user'      => $absensi->jenis_pegawai,
                          'user_name'       => $absensi->nama_pegawai,
                          'contents'        => $pesan,
                    ));

                $this->db->where('id', $id)->delete('tb_absensi');
                echo json_encode([
                    "alert"     => "success",
                    "color"     => "green",
                    "message"   => $message
                ]);
                return;
            }else{
                echo json_encode([
                    "alert"     => "danger",
                    "color"     => "red",
                    "message"   => "Pesan gagal dikirim!"
                ]);
                return;
            }    
        }
	}
	
	private function clearRole($pegawai_id, $jenis_pegawai){
        $user_key = API()->user_key;
        $pass_key = API()->pass_key;
        $URL      = API()->clearRole;
        
        $posts ='user_key='.$user_key.'&pass_key='.$pass_key;
        $posts .= '&pegawai_id='.$pegawai_id;
        $posts .= '&jenis_pegawai='.$jenis_pegawai;
    
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
		
        return json_decode($results, true);
	}

    public function upacara()
    {
        $data = [
            "title"             => "Absensi Upacara",
            "page"              => "absensi/absensiupacara",
            "upacaras"          => $this->db->where('kategori', 'Upacara')->order_by('created_at', 'desc')->get('tb_upacara_libur')->result(),
            "skpd"              => $this->Skpd_model->getSkpd(),
            "bulan"             => $this->bulan,
            "javascript"        => [
                base_url("assets/vendors/datatables.net/jquery.dataTables.js"),
                base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js"),
            ],
            "css"               => [
                base_url("assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css"),
            ],
        ];
        
        $this->load->view('template/default', $data);
    }

    public function getCetakAbsensiUpacara(){
        if(!isset($_POST['upacara_id'])){
            echo json_encode(["data"=> array()]);
            return;
        }
        $akses      = [1, 2];
        if(in_array($this->session->userdata('role_id'), $akses)){
            $skpds = $this->Unitkerja_model->get($_POST['opd_id']);
            $datas  = array();
            foreach ($skpds as $skpd){

                $row    = array();
                $row[]  = $skpd['nama_skpd'];
                $row[]  = "<a target='_blank' href='".base_url('absensi/cetakabsensiupacara/'.$skpd['skpd_id'].'/'.$_POST['upacara_id']."?token=".$_GET['token'])."' class='btn btn-sm btn-primary'><em class='ti-printer'></em> Cetak</a>";
                $datas[] = $row;
            }

            echo json_encode(["data"=>$datas]);
            return;
        }

    }


    public function cetakabsensiupacara($opd_id, $upacara_id=0)
    {
        $upacara            = $this->db->where('id', $upacara_id)->get('tb_upacara_libur')->row();
        if(!$upacara){
			$this->load->view('template/custom', [
					'title' => 'Halaman tidak ditemukan !',
					'page'  => '404'
			]);
            return;
        }
        $OPD                = $this->Skpd_model->getSkpdById($opd_id);
        if(!$OPD){
			$this->load->view('template/custom', [
					'title' => 'Halaman tidak ditemukan !',
					'page'  => '404'
			]);
            return;
        }

        if($OPD){
            $namaOPD        = $OPD['nama_skpd'];
        }

        $pegawais           = $this->Pegawai_model->getPegawai(null, $opd_id);
        $tkss               = $this->Pegawai_model->getPegawaiTks(null, $opd_id);
        $pegawais           = array_merge($pegawais, $tkss);
        $numTanggal         = strtotime($upacara->tanggal);
    ?>

        <head>
            <title>Absensi <?=$upacara->nama_hari;?> - <?=$namaOPD;?></title>
            <link rel="icon" href="<?= base_url('assets/') ?>img/logo/logo_labura.jpg">
            <link href="<?= base_url('assets/') ?>datepicker/css/bootstrap.min.css" rel="stylesheet">
            <style type="text/css" media="print">
                @page {
                    size: 33cm 21cm;
                    margin: 5mm 5mm 5mm 5mm;
                    size: portrait;
                }
            </style>
        </head>

        <body>
            <div>
                <h5 style="font-weight: 800" align="center">
                    ABSENSI <?=strtoupper($upacara->nama_hari);?> TANGGAL <?=date("d", $numTanggal); ?> <?= strtoupper($this->bulan[(int) date("m", $numTanggal)]); ?> <?=date("Y", $numTanggal); ?><br>
                    <?=strtoupper($namaOPD);?><br>
                    KABUPATEN LABUHANBATU UTARA
                    
                </h5>
                <br />
                <table class="table-bordered table-striped" cellpadding="5" style="width:100%; font-size:14px" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">Nama</th>
                            <th class="text-center">NIP/NIK</th>
                            <th class="text-center">Unit Kerja</th>
                            <th class="text-center">Keterangan</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php
                        $no  = 1;
                        foreach ($pegawais as $pegawai) {
                            if($pegawai['nama']==""){
                                continue;
                            }
                            $pegawai['nama']    = ($pegawai['gelar_depan'] && $pegawai['gelar_depan']!="" ? $pegawai['gelar_depan'].". " : null).$pegawai['nama'].($pegawai['gelar_belakang'] && $pegawai['gelar_belakang']!="" ? ", ".$pegawai['gelar_belakang'] : null);
							$jenis_pegawai      = isset($pegawai['tks_id']) ? 'tks' : 'pegawai';
							$pegawai_id			= $pegawai['user_id'];
                            $isHadir            = "<span class='text-success'>Hadir</span>";
                            $numHadir           = $this->db->where('pegawai_id', $pegawai_id)->where('jenis_pegawai', $jenis_pegawai)->where('jenis_absen', 'Absen Upacara')->where('status', 1)->get('tb_absensi')->num_rows();
                            if($numHadir==0){
                                $isHadir        = "<span class='text-danger'>Tidak Hadir</span>";
								$manual         = $this->db->where('jenis_absen', 'Absen Masuk')->
															 where('pegawai_id', $pegawai_id)->
															 where('jenis_pegawai', $jenis_pegawai)->
                                                             where('keterangan!=', null)->
                                                             where('status', 1)->get('tb_absensi')->row();
                                if($manual){
                                    $isHadir    = "<span class='text-primary'>".$manual->keterangan."</span>";
                                }
                            }
                    ?>
                        <tr>
                            <td><?=$pegawai['nama'];?></td>
                            <td><?=$pegawai['username'];?></td>
                            <td><?=$pegawai['nama_skpd'];?></td>
                            <td width="40%" class="text-center"><?=$isHadir;?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </body>
<?php
    }

    public function getAbsensiHarianPegawai(){
        if (!isset($_POST['tanggal']) || 
            !isset($_POST['skpd_id']) || 
            $_POST['tanggal'] == "") {

            echo json_encode(["data"=>array()]);
            return;
        }
        
        extract($_POST);
        
        $opd_id     = $_POST['skpd_id'];
        $tanggal    = date("Y-m-d", strtotime($_POST['tanggal']));
        $datas      = array();
        $pegawai    = $this->db->where('opd_id', $opd_id)->order_by('nama', 'desc')->get('tb_pegawai')->result();
        $no         = 1;
        foreach ($pegawai as $pg) {
            $izinKerja = $this->db
                            ->where('tb_izin_kerja.pegawai_id', $pg->id)
                            ->group_start()
                                ->where("tb_izin_kerja.tanggal_awal<=", $tanggal)
                                ->where("tb_izin_kerja.tanggal_akhir>=", $tanggal)
                            ->group_end()
                            ->where("tb_izin_kerja.status", 1)
                            ->get('tb_izin_kerja')->row_array();
            
            $absensi = $this->db
                            ->where('pegawai_id', $pg->id)
                            ->where("DATE_FORMAT(jam,'%Y-%m-%d')", $tanggal)
                            ->where('status', 1)
                            ->order_by('id', 'asc')
                            ->get('tb_absensi')->result();
            
            $jam_masuk              = null;
            $jam_pulang             = null;
            $jam_istirahat_keluar   = null;
            $jam_istirahat_masuk    = null;

            $isAbsenManualMasuk             = null;
            $isAbsenManualPulang            = null;
            $isAbsenManualIstirahat         = null;
            $isAbsenManualSelesiIstirahat   = null;

            $jamKerjaPegawai    = $this->jamKerjaPegawai($pg->id, $tanggal);
            
            foreach($absensi as $abs){
                if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Masuk'){
                    $jam_masuk              = "<span class='mb-show'>Masuk</span><br><a target='_blank' href='".base_url($abs->file_absensi)."'>Susulan</a>";
                    continue;
                    
                }else if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Istirahat'){
                    $jam_istirahat_masuk    = "<span class='mb-show'>Istirahat</span><a target='_blank' href='".base_url($abs->file_absensi)."'>Susulan</a>";
                    continue;

                }else if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Selesai Istirahat'){
                    $jam_istirahat_keluar = "<span class='mb-show'>Selesai Istirahat</span><a target='_blank' href='".base_url($abs->file_absensi)."'>Susulan</a>";
                    continue;

                }else if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Pulang'){
                    $jam_pulang             = "<span class='mb-show'>Pulang</span><a target='_blank' href='".base_url($abs->file_absensi)."'>Susulan</a>";
                    continue;

                }else if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Upacara'){
                    $jam_masuk             = "<span class='mb-show'>Upacara</span><a target='_blank' href='".base_url($abs->file_absensi)."'>Susulan</a>";
                    continue;

                }

                $labels             = array();
                $jam                = $this->getJamAbsen($abs->jam, $pg->id, $abs->jenis_absen, $jamKerjaPegawai);

                if(isset($jam['label'])) $labels[] = $jam['label'];
                if($abs->jenis_absen == 'Absen Upacara' && isset($upacaralibur->kategori)) $labels[] = $upacaralibur->kategori;
                
                if($labels) { 
                    $label = " (".implode(", ", $labels).")";
                }else{
                    $label = null;
                }
                $label = null;

                $isAbsenManualMasuk             = !$jam_masuk && $abs->jenis_absen=="Absen Masuk" && $abs->keterangan ? "<small>AMP (".$abs->keterangan.")" : null;
                $isAbsenManualPulang            = !$jam_pulang && $abs->jenis_absen=="Absen Pulang" && $abs->keterangan ? "<small>AMS (".$abs->keterangan.")" : null;
                $isAbsenManualIstirahat         = !$jam_istirahat_masuk && $abs->jenis_absen=="Absen Istirahat" && $abs->keterangan ? "<small>AMI (".$abs->keterangan.")" : null;
                $isAbsenManualSelesiIstirahat   = !$jam_istirahat_keluar && $abs->jenis_absen=="Absen Selesai" && $abs->keterangan ? "<small>AMSI (".$abs->keterangan.")" : null;

                $jam_masuk              = isset($jam['jam_masuk']) && (!$jam_masuk || $jam['jam_masuk']=="Upacara" ||  $jam['jam_masuk']=="Senam") ? "<span class='mb-show'>Masuk</span><a target='_blank' href='".base_url($abs->file_absensi)."'>".$jam['jam_masuk']."</a>".$label : $jam_masuk;
                $jam_istirahat_masuk    = isset($jam['jam_istirahat']) && !$jam_istirahat_masuk ? "<span class='mb-show'>Istirahat</span><a target='_blank' href='".base_url($abs->file_absensi)."'>".$jam['jam_istirahat']."</a>".$label : $jam_istirahat_masuk;
                $jam_istirahat_keluar   = isset($jam['jam_selesai_istirahat']) && !$jam_istirahat_keluar ? "<span class='mb-show'>Selesai Istirahat</span><a target='_blank' href='".base_url($abs->file_absensi)."'>".$jam['jam_selesai_istirahat']."</a>".$label : $jam_istirahat_keluar;
                $jam_pulang             = isset($jam['jam_pulang']) && !$jam_pulang ? "<span class='mb-show'>Pulang</span><a target='_blank' href='".base_url($abs->file_absensi)."'>".$jam['jam_pulang']."</a>".$label : $jam_pulang;
            }
  
            $nama = "<div class='tb-wrap'>".$pg->nama."</div>"
                            .($jamKerjaPegawai ? "<div style='margin-top: 7px; font-size: 12px' class='tb-wrap text-primary'>".$jamKerjaPegawai['nama_jam_kerja']."</div>" :null);
            $returnJam  = $izinKerja ? 
                            "<div class='col-md-4 tb-wrap text-center'><strong>".$izinKerja['jenis_izin']."</strong></div>".
                            "<div class='col-md-4 tb-wrap text-center'><a target='_blank' href='".$izinKerja['file_izin']."'>Berkas</a></div>".
                            "<div class='col-md-4 tb-wrap text-center'>Disetujui Oleh : <br><strong>".$izinKerja['aproved_by_nama']."</strong></div>"
                            : 
                            "<div class='col-md-3 tb-wrap text-center ".($jam_masuk ? "p-1" : null)."'>".$jam_masuk."<br>".$isAbsenManualMasuk."</div>".
                            "<div class='col-md-3 tb-wrap text-center ".($jam_istirahat_masuk ? "p-1" : null)."'>".$jam_istirahat_masuk."<br>".$isAbsenManualIstirahat."</div>".
                            "<div class='col-md-3 tb-wrap text-center ".($jam_istirahat_keluar ? "p-1" : null)."'>".$jam_istirahat_keluar."<br>".$isAbsenManualSelesiIstirahat."</div>".
                            "<div class='col-md-3 tb-wrap text-center ".($jam_pulang ? "p-1" : null)."'>".$jam_pulang."<br>".$isAbsenManualPulang."</div>"
                            ;
            $data = array();
            $data[] = $nama;
            $data[] = "<div class='row'>".$returnJam."</div>";
            $data[] = date("N", strtotime($tanggal));
            $data[] = isset($upacaralibur->kategori) ? $upacaralibur->kategori : null;
            $data[] = isset($upacaralibur->upacara_hari_libur) ? $upacaralibur->upacara_hari_libur : null;
            $datas[] = $data;
        }
        echo json_encode(array("data" => $datas));
    }

    private function jamKerjaPegawai($pegawai_id, $tanggal){
        return $this->db->
                        select('tb_jam_kerja_pegawai.*, tb_jam_kerja.nama_jam_kerja')->
                        where('pegawai_id', $pegawai_id)->
                        where('tanggal', date("Y-m-d", strtotime($tanggal)))->
                        join('tb_jam_kerja', 'tb_jam_kerja.id=tb_jam_kerja_pegawai.jam_kerja_id', 'left')->
                        get('tb_jam_kerja_pegawai')->row_array();

    }

    private function getJamAbsen($tanggal, $pegawai_id, $jenis_absen, $jamKerjaPegawai){
        
        $now                = strtotime($tanggal);
        $jam_kerja  = $jamKerjaPegawai ? 
                            $this->db
                               ->where('id', $jamKerjaPegawai['jam_kerja_id'])
                               ->get('tb_jam_kerja_new')
                               ->row() : 
                            $this->db
                               ->where('jam_kerja_id', 1)
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
        
        if($jenis_absen=='Absen Masuk' && $now >= $jam_awal_masuk && $now<=($jam_akhir_masuk+7200)){
            return [
                        'jam_masuk'     => date("H:i", $now),
                        'label'         => $this->_hitungTerlambatMasuk($now, $jam_akhir_masuk)
                ];
        }
        
        if($jenis_absen=='Absen Istirahat' && $now >= $jam_awal_istirahat && $now <= $jam_akhir_istirahat){
            return [
                        'jam_istirahat'       => date("H:i", $now),
                ];
        }
        if($jenis_absen=='Absen Selesai Istirahat' && $now >= $jam_awal_selesai_istirahat && $now <= $jam_akhir_selesai_istirahat){
            return [
                        'jam_selesai_istirahat'      => date("H:i", $now),
                ];
        }
        
        
        if($jenis_absen=='Absen Pulang' && $now >= ($jam_awal_pulang-7200) && $now<=$jam_akhir_pulang){
            return [
                        'jam_pulang'    => date("H:i", $now),
                        'label'         => $this->_hitungPulangLebihAwal($now, $jam_awal_pulang)
                ];
        }
        
        return [];        
    }


    private function _hitungTerlambatMasuk($jam, $batasMasukAkhir)
    {
        if ($jam > ($batasMasukAkhir+7200)) return "TDHE1";
        if ($jam > ($batasMasukAkhir+5400)) return "TM4";
        if ($jam > ($batasMasukAkhir+3600)) return "TM3";
        if ($jam > ($batasMasukAkhir+1800)) return "TM2";
        if ($jam > $batasMasukAkhir) return "TM1";
    }
    
    private function _hitungPulangLebihAwal($jam, $batasPulangAwal)
    {
        if ($jam < ($batasPulangAwal-7200)) return "TDHE2";
        if ($jam < ($batasPulangAwal-5400)) return "PLA4";
        if ($jam < ($batasPulangAwal-3600)) return "PLA3";
        if ($jam < ($batasPulangAwal-1800)) return "PLA2";
        if ($jam < $batasPulangAwal) return "PLA1";
    }

    
}
