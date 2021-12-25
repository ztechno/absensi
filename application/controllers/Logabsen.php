<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Logabsen extends CI_Controller
{

    public $hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
    public $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model([
            'Hitung_model',
            'LogAbsen_model',
            'Pegawai_model',
            'Unitkerja_model',
            'Skpd_model'
        ]);
    }

    public function index()
    {
		$data = [
		    "title"             => "Log Absen",
			"page"				=> "logabsen/logabsen",
			"skpd"              => $this->session->userdata('role_id')==1 || $this->session->userdata('role_id')==2 ? $this->Skpd_model->getSkpd() : $this->Unitkerja_model->get($this->session->userdata('skpd_id')),
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

    public function getLogAbsen(){
        if (
               isset($_POST['tanggalAwal']) 
            && isset($_POST['tanggalAkhir']) 
            && $_POST['tanggalAwal'] != "" 
            && $_POST['tanggalAkhir'] != "") {
            $akses = [1,2,3];
            $pegawai_id =  in_array($this->session->userdata('role_id'), $akses) ? $_POST['pegawai_id'] : $this->session->userdata('user_id');
            $jenis_pegawai =  in_array($this->session->userdata('role_id'), $akses) ? $_POST['jenis_pegawai'] : $this->session->userdata('jenis_pegawai');


            $begin = new DateTime($_POST['tanggalAwal']);
            $end = new DateTime($_POST['tanggalAkhir']);
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

            $hari_izin = [];
            $file_izin = [];

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
                        $hari_izin[$dt2->format("Y-m-d")] = $iz->jenis_izin;
                        $file_izin[$dt2->format("Y-m-d")] = $iz->file_izin;
                    }
                }
            }


            foreach ($period as $dt) {
                $this->db->select('tb_absen_wajah.*');
                $this->db->from('tb_absen_wajah');
                $this->db->where("tb_absen_wajah.jenis_pegawai", $jenis_pegawai);
                $this->db->where("tb_absen_wajah.pegawai_id", $pegawai_id);
                $this->db->where("tb_absen_wajah.tanggal", $dt->format("Y-m-d"));
                $abs = $this->db->get();
                $num_absen = $abs->num_rows();
                $absen = $abs->row();
                $num_bulan = (int) $dt->format("m");
                if ($num_absen > 0) {
                    $pegawaiID = $absen->pegawai_id;
                    $tanggalLog = $this->hari[$dt->format("w")] . ", " . $dt->format("d") . " " . $this->bulan[$num_bulan] . " " . $dt->format("Y") . "<br />";
                    $jam_masuk = $absen->jam_masuk != null ? date("H:i", strtotime($absen->jam_masuk)) : "-";
                    $jam_pulang = $absen->jam_pulang != null ? date("H:i", strtotime($absen->jam_pulang)) : "-";
                    $jam_masuk = $jam_masuk;
                    $jam_pulang = $jam_pulang;
                } else {
                    $pegawaiID = $pegawai_id;
                    $tanggalLog = $this->hari[$dt->format("w")] . ", " . $dt->format("d") . " " . $this->bulan[$num_bulan] . " " . $dt->format("Y") . "<br />";
                    $jam_masuk = "-";
                    $jam_pulang = "-";
                }

                $this->db->select('tb_upacara_libur.*');
                $this->db->from('tb_upacara_libur');
                $this->db->where("tb_upacara_libur.tanggal", $dt->format("Y-m-d"));
                $hl = $this->db->get();
                $num_hl = $hl->num_rows();
                $hari_libur = $hl->row();

                $data = array();

                $data[] = $no;
                $data[] = "<center>" . $pegawaiID . "</center>";
                $data[] = $tanggalLog;


                // $this->db->select('tb_absen_upacara.*, tb_absen_upacara_meta.status');
                // $this->db->from('tb_absen_upacara');
                // $this->db->where('tb_absen_upacara.opd_id', $_POST['opd_id']);
                // $this->db->where('tb_absen_upacara.tanggal', $dt->format("y-m-d"));
                // $this->db->join('tb_absen_upacara_meta', 'tb_absen_upacara_meta.pegawai_id=' . $pegawai_id . ' AND tb_absen_upacara_meta.absen_upacara_id=tb_absen_upacara.id', 'left');
                // $upc = $this->db->get();
                // $jml_upc = $upc->num_rows();
                // $upacara = $upc->row();

                // if ($jml_upc > 0) {
                //     if ($upacara->status == 1) {
                //         $jam_masuk = "AU";
                //     }
                // }

                // $this->db->select('tb_absen_senam.*, tb_absen_senam_meta.status');
                // $this->db->from('tb_absen_senam');
                // $this->db->where('tb_absen_senam.opd_id', $_POST['opd_id']);
                // $this->db->where('tb_absen_senam.tanggal', $dt->format("y-m-d"));
                // $this->db->join('tb_absen_senam_meta', 'tb_absen_senam_meta.pegawai_id=' . $pegawai_id . ' AND tb_absen_senam_meta.absen_senam_id=tb_absen_senam.id', 'left');
                // $sn = $this->db->get();
                // $jml_sn = $sn->num_rows();
                // $senam = $sn->row();

                // if ($jml_sn > 0) {
                //     if ($senam->status == 1) {
                //         $jam_masuk = "Senam";
                //     }
                // }

                // $this->db->select('tb_absen_finger.*, tb_absen_finger_meta.status');
                // $this->db->from('tb_absen_finger');
                // $this->db->where('tb_absen_finger.opd_id', $_POST['opd_id']);
                // $this->db->where('tb_absen_finger.tanggal', $dt->format("y-m-d"));
                // $this->db->join('tb_absen_finger_meta', 'tb_absen_finger_meta.pegawai_id=' . $pegawai_id . ' AND tb_absen_finger_meta.absen_finger_id=tb_absen_finger.id', 'left');
                // $sn = $this->db->get();
                // $jml_finger = $sn->num_rows();
                // $finger = $sn->row();

                // if ($jml_finger > 0) {
                //     if ($finger->status == 1) {
                //         $jam_masuk = "Finger";
                //     }
                // }


                $this->db->select('tb_absen_manual.*');
                $this->db->from('tb_absen_manual');
                $this->db->where("tb_absen_manual.pegawai_id", $pegawai_id);
                $this->db->where("tb_absen_manual.tanggal", $dt->format("Y-m-d"));
                $this->db->where("tb_absen_manual.status", 1);
                $mnl = $this->db->get();
                $jml_mnl = $mnl->num_rows();
                $manual = $mnl->result();
                foreach ($manual as $manual) {
                    if ($jml_mnl > 0) {
                        if ($manual->jenis_absen == "AMP dan AMS") {
                            $jam_masuk = "<a href='" . base_url() . "resources/berkas/absen_manual/" . $manual->lampiran_amp . "' target='_blank' class='btn-link'>AMP</a>";
                            $jam_pulang = "<a href='" . base_url() . "resources/berkas/absen_manual/" . $manual->lampiran_ams . "' target='_blank' class='btn-link'>AMS</a>";
                        } else if ($manual->jenis_absen == "AMP") {
                            $jam_masuk = "<a href='" . base_url() . "resources/berkas/absen_manual/" . $manual->lampiran_amp . "' target='_blank' class='btn-link'>AMP</a>";
                        } else if ($manual->jenis_absen == "AMS") {
                            $jam_pulang = "<a href='" . base_url() . "resources/berkas/absen_manual/" . $manual->lampiran_ams . "' target='_blank' class='btn-link'>AMS</a>";
                        }
                    }
                }

                if (isset($hari_izin[$dt->format("Y-m-d")]) && $dt->format("w") != 6 && $dt->format("w") != 0) {
                    $jam_masuk = $hari_izin[$dt->format("Y-m-d")];
                    $jam_pulang = "<a href='" . base_url() . "resources/berkas/izin_kerja/" . $file_izin[$dt->format("Y-m-d")] . "' target='_blank' class='btn-link'>Berkas Izin</a>";
                }

                // $jam_masuk .=  "<br />".$this->_hitungTerlambatMasuk($jam_masuk, $dt->format("w"));
                // $jam_pulang = "<br />".$this->_hitungPulangLebihAwal($jam_pulang, $dt->format("w"));

                $data[] = "<center>" . $jam_masuk . "</center>";
                $data[] = "<center>" . $jam_pulang . "</center>";
                $data[] = $dt->format("w");
                $data[] = $num_hl;
                $data[] = $num_hl > 0 ? $hari_libur->nama_hari : null;
                $data[] = $num_hl > 0 ? $hari_libur->upacara_hari_libur : null;
                $datas[] = $data;
                $no++;
            }
            echo json_encode(array("data" => $datas));
        } else {
            echo json_encode(array("data" => false));
        }
    }

    public function getLogAbsen2(){
        if (isset($_POST['bulan']) && $_POST['bulan'] !="") {
            $akses           = [1,3,7];
            $skpd_id         = isset($_POST['skpd_id']) ? $_POST['skpd_id'] : $this->session->userdata('skpd_id'); 
            $pegawai_id      = in_array($this->session->userdata('role_id'), $akses) ? $_POST['pegawai_id'] : $this->session->userdata('user_id');
            $jenis_pegawai   = in_array($this->session->userdata('role_id'), $akses) ? $_POST['jenis_pegawai'] : $this->session->userdata('jenis_pegawai');

            $begin           = new DateTime(date("01-m-Y", strtotime("01-".$_POST['bulan'])));
            $end             = new DateTime(date("t-m-Y", strtotime("01-".$_POST['bulan'])));
            $end->modify('+1 day');
            $interval       = DateInterval::createFromDateString('1 day');
            $period         = new DatePeriod($begin, $interval, $end);
            $no             = 1;

            $pegawaiMeta    = $jenis_pegawai=='pegawai' ? 
                                    $this->db->where('pegawai_id', $pegawai_id)->get('tb_pegawai_meta')->row() :
                                    $this->db->where('tks_id', $pegawai_id)->get('tb_tks_meta')->row();

            $datas          = array();
            foreach ($period as $dt) {
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

                $absensi = $this->db
                                ->where('pegawai_id', $pegawai_id)
                                ->where('jenis_pegawai', $jenis_pegawai)
                                ->where("DATE_FORMAT(jam,'%Y-%m-%d')", $dt->format("Y-m-d"))
                                ->where('status', 1)
                                ->order_by('id', 'asc')
                                ->get('tb_absensi')->result();
                
                $upacaralibur = $this->db
                                     ->where('tanggal', $dt->format('Y-m-d'))
                                     ->get('tb_upacara_libur')->row();
                
                $jam_masuk                      = null;
                $jam_pulang                     = null;
                $jam_istirahat_keluar           = null;
                $jam_istirahat_masuk            = null;
                
                $isAbsenManualMasuk             = null;
                $isAbsenManualPulang            = null;
                $isAbsenManualIstirahat         = null;
                $isAbsenManualSelesiIstirahat   = null;

                $jamKerjaPegawai    = $this->jamKerjaPegawai($pegawai_id, $jenis_pegawai, $dt->format("Y-m-d"));

                
                
                foreach($absensi as $abs){
                    if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Masuk'){
                        $jam_masuk              = "<span class='mb-show'>Masuk</span><br><a target='_blank' href='https://storage.googleapis.com/file-absensi/".$abs->file_absensi."'>Susulan</a>";
                        continue;
                        
                    }else if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Istirahat'){
                        $jam_istirahat_masuk    = "<span class='mb-show'>Istirahat</span><a target='_blank' href='https://storage.googleapis.com/file-absensi/".$abs->file_absensi."'>Susulan</a>";
                        continue;

                    }else if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Selesai Istirahat'){
                        $jam_istirahat_keluar = "<span class='mb-show'>Selesai Istirahat</span><a target='_blank' href='https://storage.googleapis.com/file-absensi/".$abs->file_absensi."'>Susulan</a>";
                        continue;

                    }else if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Pulang'){
                        $jam_pulang             = "<span class='mb-show'>Pulang</span><a target='_blank' href='https://storage.googleapis.com/file-absensi/".$abs->file_absensi."'>Susulan</a>";
                        continue;

                    }else if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Upacara'){
                        $jam_masuk             = "<span class='mb-show'>Upacara</span><a target='_blank' href='https://storage.googleapis.com/file-absensi/".$abs->file_absensi."'>Susulan</a>";
                        continue;

                    }
                    $labels             = array();
                    $jam                = $this->getJamAbsen($abs->jam, $abs->pegawai_id, $abs->jenis_pegawai, $abs->jenis_absen, $jamKerjaPegawai, isset($pegawaiMeta->guru) && $pegawaiMeta->guru=="Ya" ? true:false);

                    if(isset($jam['label'])) $labels[] = $jam['label'];
                    if($abs->jenis_absen == 'Absen Upacara' && isset($upacaralibur->kategori)) $labels[] = $upacaralibur->kategori;
                    
                    if($labels) { 
                        $label = " (".implode(", ", $labels).")";
                    }else{
                        $label = null;
                    }
                    $label = null;

                    $isAbsenManualMasuk             = !$jam_masuk && $abs->jenis_absen=="Absen Masuk" && $abs->keterangan ? "<small>AMP (".$abs->keterangan.")<div style='margin-top: 2px; padding-top: 3px;'>Disetujui oleh :<br><strong>".$abs->approved_by_nama."</strong></small>" : null;
                    $isAbsenManualPulang            = !$jam_pulang && $abs->jenis_absen=="Absen Pulang" && $abs->keterangan ? "<small>AMS (".$abs->keterangan.")<div style='margin-top: 2px; padding-top: 3px;'>Disetujui oleh :<br><strong>".$abs->approved_by_nama."</strong></small>" : null;
                    $isAbsenManualIstirahat         = !$jam_istirahat_masuk && $abs->jenis_absen=="Absen Istirahat" && $abs->keterangan ? "<small>AMI (".$abs->keterangan.")<div style='margin-top: 2px; padding-top: 3px;'>Disetujui oleh :<br><strong>".$abs->approved_by_nama."</strong></small>" : null;
                    $isAbsenManualSelesiIstirahat   = !$jam_istirahat_keluar && $abs->jenis_absen=="Absen Selesai Istirahat" && $abs->keterangan ? "<small>AMSI (".$abs->keterangan.")<div style='margin-top: 2px; padding-top: 3px;'>Disetujui oleh :<br><strong>".$abs->approved_by_nama."</strong></small>" : null;
                        
                    $jam_masuk              = isset($jam['jam_masuk']) && (!$jam_masuk || $jam['jam_masuk']=="Upacara" ||  $jam['jam_masuk']=="Senam") ? "<span class='mb-show'>Masuk</span><a target='_blank' href='https://storage.googleapis.com/file-absensi/".$abs->file_absensi."'>".$jam['jam_masuk']."</a>".$label : $jam_masuk;
                    $jam_istirahat_masuk    = isset($jam['jam_istirahat']) && !$jam_istirahat_masuk ? "<span class='mb-show'>Istirahat</span><a target='_blank' href='https://storage.googleapis.com/file-absensi/". $abs->file_absensi."'>".$jam['jam_istirahat']."</a>".$label : $jam_istirahat_masuk;
                    $jam_istirahat_keluar   = isset($jam['jam_selesai_istirahat']) && !$jam_istirahat_keluar ? "<span class='mb-show'>Selesai Istirahat</span><a target='_blank' href='https://storage.googleapis.com/file-absensi/" . $abs->file_absensi."'>".$jam['jam_selesai_istirahat']."</a>".$label : $jam_istirahat_keluar;
                    $jam_pulang             = isset($jam['jam_pulang']) && !$jam_pulang ? "<span class='mb-show'>Pulang</span><a target='_blank' href='https://storage.googleapis.com/file-absensi/". $abs->file_absensi."'>".$jam['jam_pulang']."</a>".$label : $jam_pulang;
    
                }
                                
                $tanggalLog = "<div>".$this->hari[$dt->format("w")] . "</div>
                                <div style='margin-top: 7px'>" . $dt->format("d") . " " . $this->bulan[(int) $dt->format("m")] . " " . $dt->format("Y")."
                               </div>"
								.($jamKerjaPegawai ? "<div style='margin-top: 7px;' class='tb-wrap text-primary'>".$jamKerjaPegawai['nama_jam_kerja']."</div>" : (isset($pegawaiMeta->guru) && $pegawaiMeta->guru=="Ya" ? "<div style='margin-top: 7px;' class='tb-wrap text-primary'>Jam Kerja Default Guru</div>" : null));
                $returnJam  = $izinkerja ? 
                                "<div class='col-md-4 tb-wrap text-center'><strong>".$izinkerja->jenis_izin."</strong></div>".
                                "<div class='col-md-4 tb-wrap text-center'><a target='_blank' href='".$izinkerja->file_izin."'>Berkas</a></div>".
                                "<div class='col-md-4 tb-wrap text-center'>".($izinkerja->aproved_by_nama ? "Disetujui Oleh: <br>".$izinkerja->aproved_by_nama : null). "</div>"
                                : 
                                "<div class='col-md-3 tb-wrap text-center p-1'>".($jam_masuk ? $jam_masuk."<br>".$isAbsenManualMasuk : null)."</div>".
                                "<div class='col-md-3 tb-wrap text-center p-1'>".($jam_istirahat_masuk ? $jam_istirahat_masuk."<br>".$isAbsenManualIstirahat : null)."</div>".
                                "<div class='col-md-3 tb-wrap text-center p-1'>".($jam_istirahat_keluar ? $jam_istirahat_keluar."<br>".$isAbsenManualSelesiIstirahat : null)."</div>".
                                "<div class='col-md-3 tb-wrap text-center p-1'>".($jam_pulang ? $jam_pulang."<br>".$isAbsenManualPulang : null)."</div>";
                $data = array();
                $data[] = $tanggalLog.(isset($upacaralibur->nama_hari) ?  "<div class='tb-wrap' style='margin-top: 7px; width:100%;font-weight: 700;'>".$upacaralibur->nama_hari."</div>" : null);
                $data[] = "<div class='row'>".$returnJam."</div>";
                
                $cekJamKerjaGuru = $this->db->where('jam_kerja_id', 19)->where('hari', $dt->format('N'))->get('tb_jam_kerja_meta')->num_rows();
                $cekJamKerja     = $jamKerjaPegawai ? $this->db->where('id', $jamKerjaPegawai['jam_kerja_id'])->get('tb_jam_kerja_new')->num_rows() : 0;
                $data[] = $jamKerjaPegawai ? 
                            ($cekJamKerja>0 ? true : false ) :
                            (isset($pegawaiMeta->guru) && $pegawaiMeta->guru=="Ya" && $cekJamKerjaGuru ?  true : $dt->format('N'));
                $data[] = isset($upacaralibur->kategori) ? $upacaralibur->kategori : null;
                $data[] = isset($upacaralibur->upacara_hari_libur) ? $upacaralibur->upacara_hari_libur : null;
                $datas[] = $data;
            }
            echo json_encode(array("data" => $datas));
        } else {
            echo json_encode(array("data" => false));
        }
    }

    public function getLogAbsen3(){
        if (isset($_POST['bulan']) && $_POST['bulan'] !="") {
            $akses           = [1,3,7];
            $skpd_id         = isset($_POST['skpd_id']) ? $_POST['skpd_id'] : $this->session->userdata('skpd_id'); 
            $pegawai_id      = in_array($this->session->userdata('role_id'), $akses) ? $_POST['pegawai_id'] : $this->session->userdata('user_id');
            $jenis_pegawai   = in_array($this->session->userdata('role_id'), $akses) ? $_POST['jenis_pegawai'] : $this->session->userdata('jenis_pegawai');

            $begin           = new DateTime(date("01-m-Y", strtotime("01-".$_POST['bulan'])));
            $end             = new DateTime(date("t-m-Y", strtotime("01-".$_POST['bulan'])));
            $end->modify('+1 day');
            $interval       = DateInterval::createFromDateString('1 day');
            $period         = new DatePeriod($begin, $interval, $end);
            $no             = 1;

            $pegawaiMeta    = $jenis_pegawai=='pegawai' ? 
                                    $this->db->where('pegawai_id', $pegawai_id)->get('tb_pegawai_meta')->row() :
                                    $this->db->where('tks_id', $pegawai_id)->get('tb_tks_meta')->row();

            $datas          = array();
            foreach ($period as $dt) {
                $data           = array();
                $jamAbsen       = $this->Hitung_model->getJamAbsen($pegawai_id, $jenis_pegawai, $dt->format("Y-m-d"), isset($pegawaiMeta->guru) && $pegawaiMeta->guru=="Ya" ? true:false);
                $upacaralibur   = $this->db
                                        ->where('tanggal', $dt->format('Y-m-d'))
                                        ->get('tb_upacara_libur')->row();

                $tanggalLog     = "<div>".$this->hari[$dt->format("w")] . "</div>
                                   <div style='margin-top: 7px'>" . $dt->format("d") . " " . $this->bulan[(int) $dt->format("m")] . " " . $dt->format("Y")."</div>".
                                  ($jamAbsen ? "<div style='margin-top: 7px;' class='tb-wrap'>".$jamAbsen->nama_jam_kerja."</div>" : null);
                $isUpacara      = (isset($upacaralibur->nama_hari) ?  "<div class='tb-wrap' style='margin-top: 7px; width:100%;font-weight: 700;'>".$upacaralibur->nama_hari."</div>" : null);
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

                $data[0]        = $tanggalLog.$isUpacara;
                $data[1]        = null;
                $data[2]        = $jamAbsen && $upacaralibur && $upacaralibur->kategori=="Upacara" && $upacaralibur->upacara_hari_libur=="no" ? "upacara" : 
                                    ($jamAbsen && $upacaralibur && $upacaralibur->kategori=="Upacara" && $upacaralibur->upacara_hari_libur=="yes" ? "upacaralibur" : 
                                        (!$jamAbsen && $upacaralibur && $upacaralibur->kategori=="Libur" ? "Libur" : 
                                            ($jamAbsen ? "jamkerja" : null)));
                $data[3]        = $upacaralibur;

                if($izinkerja){
                    $data[1]    = "<div class='tb-wrap text-center'><strong>".$izinkerja->jenis_izin."</strong></div>".
                                  "<div class='tb-wrap text-center'><a target='_blank' href='".$izinkerja->file_izin."'>Berkas</a></div>".
                                  "<div class='tb-wrap text-center'>".($izinkerja->aproved_by_nama ? "Disetujui Oleh: <br>".$izinkerja->aproved_by_nama : null). "</div>";
                    $datas[]    = $data;
                    continue;
                }

                $hitungJam          = [];
    
                $this->db->group_start();
                    if(isset($jamAbsen->jam_awal_masuk) && isset($jamAbsen->jam_akhir_masuk)){
                        $hitungJam['masuk']     = 5;
                        $batas_awal     = $dt->format("Y-m-d")." ".$jamAbsen->jam_awal_masuk;
                        $batas_awal     = date("Y-m-d H:i", (strtotime($batas_awal)-60));
                        $batas_akhir    = $dt->format("Y-m-d")." ".$jamAbsen->jam_akhir_masuk;
                        $batas_akhir    = date("Y-m-d H:i", (strtotime($batas_akhir)+7260));
                        $this->db->or_group_start()
                                        ->where("DATE_FORMAT(jam,'%Y-%m-%d %H:%i')>=", $batas_awal)
                                        ->where("DATE_FORMAT(jam,'%Y-%m-%d %H:%i')<=", $batas_akhir)
                                        ->where('jenis_absen', "Absen Masuk")
                                ->group_end();
                    }
                    
                    if(isset($jamAbsen->jam_awal_istirahat) && isset($jamAbsen->jam_akhir_istirahat)){
                        $hitungJam['istirahat']     = 5;
    
                        $batas_awal     = $dt->format("Y-m-d")." ".$jamAbsen->jam_awal_istirahat;
                        $batas_awal     = date("Y-m-d H:i", (strtotime($batas_awal)-7260));
                        $batas_akhir    = $dt->format("Y-m-d")." ".$jamAbsen->jam_akhir_istirahat;
                        $batas_akhir    = date("Y-m-d H:i", (strtotime($batas_akhir)+60));

                        $this->db->or_group_start()
                                    ->where("DATE_FORMAT(jam,'%Y-%m-%d %H:%i')>=", $batas_awal)
                                    ->where("DATE_FORMAT(jam,'%Y-%m-%d %H:%i')<=", $batas_akhir)
                                    ->where('jenis_absen', "Absen Istirahat")
                                ->group_end();
                    }
                    
                    if(isset($jamAbsen->jam_awal_selesai_istirahat) && isset($jamAbsen->jam_akhir_selesai_istirahat)){
                        $hitungJam['selesai_istirahat']     = 5;
    
                        $batas_awal     = $dt->format("Y-m-d")." ".$jamAbsen->jam_awal_selesai_istirahat;
                        $batas_awal     = date("Y-m-d H:i", (strtotime($batas_awal)-60));
                        $batas_akhir    = $dt->format("Y-m-d")." ".$jamAbsen->jam_akhir_selesai_istirahat;
                        $batas_akhir    = date("Y-m-d H:i", (strtotime($batas_akhir)+7260));
                        $this->db->or_group_start()
                                    ->where("DATE_FORMAT(jam,'%Y-%m-%d %H:%i')>=", $batas_awal)
                                    ->where("DATE_FORMAT(jam,'%Y-%m-%d %H:%i')<=", $batas_akhir)
                                    ->where('jenis_absen', "Absen Selesai Istirahat")
                                ->group_end();
                    }
                    
                    if(isset($jamAbsen->jam_awal_pulang) && isset($jamAbsen->jam_akhir_pulang)){
                        $hitungJam['pulang']     = 5;
    
                        $batas_awal     = $dt->format("Y-m-d")." ".$jamAbsen->jam_awal_pulang;
                        $batas_awal     = date("Y-m-d H:i", (strtotime($batas_awal)-7260));
                        $batas_akhir    = $dt->format("Y-m-d")." ".$jamAbsen->jam_akhir_pulang;
                        $batas_akhir    = date("Y-m-d H:i", (strtotime($batas_akhir)+60));
                        $this->db->or_group_start()
                                    ->where("DATE_FORMAT(jam,'%Y-%m-%d %H:%i')>=", $batas_awal)
                                    ->where("DATE_FORMAT(jam,'%Y-%m-%d %H:%i')<=", $batas_akhir)
                                    ->where('jenis_absen', "Absen Pulang")
                                ->group_end();
                    }
    
                    $this->db->or_group_start()
                                ->where('is_susulan', "Ya")
                                ->group_start()
                                    ->where('jenis_absen', "Absen Masuk")
                                    ->or_where('jenis_absen', "Absen Istirahat")
                                    ->or_where('jenis_absen', "Absen Selesai Istirahat")
                                    ->or_where('jenis_absen', "Absen Pulang")
                                ->group_end()
                            ->group_end();
                            
                    $this->db->or_group_start()
                        ->where('jenis_absen', "Absen Senam")
                        ->or_where('jenis_absen', "Absen Upacara")
                    ->group_end();
    
                $this->db->group_end();
    
    
                $absensi            = $this->db
                                            ->where("DATE_FORMAT(jam,'%Y-%m-%d')", $dt->format("Y-m-d"))
                                            ->where('pegawai_id', $pegawai_id)
                                            ->where('jenis_pegawai', $jenis_pegawai)
                                            ->where('status', 1)
                                            ->order_by('id', 'asc')
                                            ->get('tb_absensi')->result();

                $jam_masuk                      = null;
                $jam_pulang                     = null;
                $jam_istirahat_keluar           = null;
                $jam_istirahat_masuk            = null;
                
                foreach($absensi as $abs){
                    if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Masuk'){
                        $jam_masuk              = "<span class='mb-show'>Masuk</span><br><a target='_blank' href='https://storage.googleapis.com/file-absensi/".$abs->file_absensi."'>Susulan</a>";
                        continue;
                        
                    }else if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Istirahat'){
                        $jam_istirahat_masuk    = "<span class='mb-show'>Istirahat</span><a target='_blank' href='https://storage.googleapis.com/file-absensi/".$abs->file_absensi."'>Susulan</a>";
                        continue;

                    }else if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Selesai Istirahat'){
                        $jam_istirahat_keluar = "<span class='mb-show'>Selesai Istirahat</span><a target='_blank' href='https://storage.googleapis.com/file-absensi/".$abs->file_absensi."'>Susulan</a>";
                        continue;

                    }else if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Pulang'){
                        $jam_pulang             = "<span class='mb-show'>Pulang</span><a target='_blank' href='https://storage.googleapis.com/file-absensi/".$abs->file_absensi."'>Susulan</a>";
                        continue;

                    }else if($abs->is_susulan=='Ya' && $abs->jenis_absen=='Absen Upacara'){
                        $jam_masuk             = "<span class='mb-show'>Upacara</span><a target='_blank' href='https://storage.googleapis.com/file-absensi/".$abs->file_absensi."'>Susulan</a>";
                        continue;

                    }
                    $labels             = array();
                    $jam                = $this->getJamAbsen($abs->jam, $abs->jenis_absen, $jamAbsen);

                    if(isset($jam['label'])) $labels[] = $jam['label'];
                    if($abs->jenis_absen == 'Absen Upacara' && isset($upacaralibur->kategori)) $labels[] = $upacaralibur->kategori;
                    
                    if($labels) { 
                        $label = " (".implode(", ", $labels).")";
                    }else{
                        $label = null;
                    }
                    $label = null;

                    $isALLMasuk             = $abs->keterangan ? "<br><small>".(isset($jam['jam_masuk']) && ($jam['jam_masuk']=="Upacara" ||  $jam['jam_masuk']=="Senam") ? null : "ALLM (".$abs->keterangan.")")."<div style='margin-top: 2px; padding-top: 3px;'>Disetujui oleh :<br><strong>".$abs->approved_by_nama."</strong></div></small>" : null;
                    $isALLPulang            = $abs->keterangan ? "<br><small>ALLP (".$abs->keterangan.")<div style='margin-top: 2px; padding-top: 3px;'>Disetujui oleh :<br><strong>".$abs->approved_by_nama."</strong></div></small>" : null;
                    $isALLIstirahat         = $abs->keterangan ? "<br><small>ALLI (".$abs->keterangan.")<div style='margin-top: 2px; padding-top: 3px;'>Disetujui oleh :<br><strong>".$abs->approved_by_nama."</strong></div></small>" : null;
                    $isALLSelesaiIstirahat  = $abs->keterangan ? "<br><small>ALLSI (".$abs->keterangan.")<div style='margin-top: 2px; padding-top: 3px;'>Disetujui oleh :<br><strong>".$abs->approved_by_nama."</strong></div></small>" : null;
                        
                    $jam_masuk              = isset($jam['jam_masuk']) && (!$jam_masuk || $jam['jam_masuk']=="Upacara" ||  $jam['jam_masuk']=="Senam") ? 
                                                "<span class='mb-show'>Masuk</span><a target='_blank' href='https://storage.googleapis.com/file-absensi/".$abs->file_absensi."'>".$jam['jam_masuk']."</a>".$label.$isALLMasuk : $jam_masuk;
                    $jam_istirahat_masuk    = isset($jam['jam_istirahat']) && !$jam_istirahat_masuk ? 
                                                "<span class='mb-show'>Istirahat</span><a target='_blank' href='https://storage.googleapis.com/file-absensi/". $abs->file_absensi."'>".$jam['jam_istirahat']."</a>".$label.$isALLIstirahat : $jam_istirahat_masuk;
                    $jam_istirahat_keluar   = isset($jam['jam_selesai_istirahat']) && !$jam_istirahat_keluar ? 
                                                "<span class='mb-show'>Selesai Istirahat</span><a target='_blank' href='https://storage.googleapis.com/file-absensi/" . $abs->file_absensi."'>".$jam['jam_selesai_istirahat']."</a>".$label.$isALLSelesaiIstirahat : $jam_istirahat_keluar;
                    $jam_pulang             = isset($jam['jam_pulang']) && !$jam_pulang ? 
                                                "<span class='mb-show'>Pulang</span><a target='_blank' href='https://storage.googleapis.com/file-absensi/". $abs->file_absensi."'>".$jam['jam_pulang']."</a>".$label.$isALLPulang : $jam_pulang;
    
                }

                $returnJam  =   "<div class='col-md-3 tb-wrap text-center p-1'>".($jam_masuk ? $jam_masuk : null)."</div>".
                                "<div class='col-md-3 tb-wrap text-center p-1'>".($jam_istirahat_masuk ? $jam_istirahat_masuk : null)."</div>".
                                "<div class='col-md-3 tb-wrap text-center p-1'>".($jam_istirahat_keluar ? $jam_istirahat_keluar : null)."</div>".
                                "<div class='col-md-3 tb-wrap text-center p-1'>".($jam_pulang ? $jam_pulang : null)."</div>";
                $data[1]    =   "<div class='row'>".$returnJam."</div>";
                $datas[]    = $data;
            }
            echo json_encode(array("data" => $datas));
            return;
        } else {
            echo json_encode(array("data" => false));
            return;
        }
    }


    private function jamKerjaPegawai($pegawai_id, $jenis_pegawai, $tanggal){
        return $this->db->
                        select('tb_jam_kerja_pegawai_new.*, tb_jam_kerja_new.nama_jam_kerja')->
                        where('pegawai_id', $pegawai_id)->
                        where('jenis_pegawai', $jenis_pegawai)->
                        where('tanggal', date("Y-m-d", strtotime($tanggal)))->
                        join('tb_jam_kerja_new', 'tb_jam_kerja_new.id=tb_jam_kerja_pegawai_new.jam_kerja_id', 'left')->
                        get('tb_jam_kerja_pegawai_new')->row_array();

    }



    private function getJamAbsen($tanggal, $jenis_absen, $jam_kerja){
        $now                = strtotime($tanggal);

        $jam_awal_masuk                 = isset($jam_kerja->jam_awal_masuk) && $jam_kerja->jam_awal_masuk ? strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_awal_masuk) : null;
        $jam_akhir_masuk                = isset($jam_kerja->jam_akhir_masuk) && $jam_kerja->jam_akhir_masuk ? strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_akhir_masuk) : null;
        $jam_awal_pulang                = isset($jam_kerja->jam_awal_pulang) && $jam_kerja->jam_awal_pulang ? strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_awal_pulang) : null;
        $jam_akhir_pulang               = isset($jam_kerja->jam_akhir_pulang) && $jam_kerja->jam_akhir_pulang ? strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_akhir_pulang) : null;
        $jam_awal_istirahat             = isset($jam_kerja->jam_awal_istirahat) && $jam_kerja->jam_awal_istirahat ? strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_awal_istirahat) : null;
        $jam_akhir_istirahat            = isset($jam_kerja->jam_akhir_istirahat) && $jam_kerja->jam_akhir_istirahat ? strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_akhir_istirahat) : null;
        $jam_awal_selesai_istirahat     = isset($jam_kerja->jam_awal_selesai_istirahat) && $jam_kerja->jam_awal_selesai_istirahat ? strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_awal_selesai_istirahat) : null;
        $jam_akhir_selesai_istirahat    = isset($jam_kerja->jam_akhir_selesai_istirahat) && $jam_kerja->jam_akhir_selesai_istirahat ? strtotime(date("Y-m-d", $now)." ".$jam_kerja->jam_akhir_selesai_istirahat) : null;

        
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
        
        if($jenis_absen=='Absen Masuk' && $now >= $jam_awal_masuk && $now<=($jam_akhir_masuk+7260)){
            return [
                        'jam_masuk'     => date("H:i", $now),
                        // 'label'         => $this->_hitungTerlambatMasuk($now, $jam_akhir_masuk)
                ];
        }
        
        if($jenis_absen=='Absen Istirahat' && $now >= ($jam_awal_istirahat-7260) && $now <= $jam_akhir_istirahat){
            return [
                        'jam_istirahat'       => date("H:i", $now),
                ];
        }
        if($jenis_absen=='Absen Selesai Istirahat' && $now >= $jam_awal_selesai_istirahat && $now <= ($jam_akhir_selesai_istirahat+7260)){
            return [
                        'jam_selesai_istirahat'      => date("H:i", $now),
                ];
        }
        
        
        if($jenis_absen=='Absen Pulang' && $now >= ($jam_awal_pulang-7260) && $now<=$jam_akhir_pulang){
            return [
                        'jam_pulang'    => date("H:i", $now),
                        // 'label'         => $this->_hitungPulangLebihAwal($now, $jam_awal_pulang)
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
