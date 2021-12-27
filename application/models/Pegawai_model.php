<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pegawai_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getPegawai($opd_id=false, $pegawai_id=false, $only_one=false){
        if($opd_id) $this->db->where('opd_id', $opd_id);
        if($pegawai_id) $this->db->where('pegawai_id', $pegawai_id);
        $this->db->order_by('nama', 'desc');
        if($only_one) return $this->db->get('tb_pegawai')->row();
        return $this->db->get('tb_pegawai')->result();
    } 

    public function addDataPegawai()
    {
        $jabatan_rangkap_perbub = $this->input->post('plt', true) == 1 ? $this->input->post('jabatan_rangkap_perbub', true) : null;
        if ($this->input->post('kategori_pegawai', true) == 1) {
            $data = [
                "nama"                   => $this->input->post('nama', true),
                "opd_id"                 => $this->input->post('opd_id', true),
                "jabatan_opd"            => "",
                "jabatan_perbub_tpp"     => 0,
                "kepala"                 => 0,
                "st_post"                => $this->input->post('status_upload', true),
                "post_date"              => date("Y-m-d H:i:s"),
                "kategori_pegawai"       => "tks",
                "no_rekening"            => 0,
                "mesin_id"               => $this->input->post('mesin_id', true),
            ];
        } else if ($this->input->post('kategori_pegawai', true) == 2) {
            $data = [
                "nama"                   => $this->input->post('nama', true),
                "nip"                    => $this->input->post('nip', true),
                "golongan"               => $this->input->post('golongan', true),
                "opd_id"                 => $this->input->post('opd_id', true),
                "jabatan_opd"            => $this->input->post('jabatan_opd', true),
                "jabatan_perbub_tpp"     => $this->input->post('jabatan_perbub_tpp', true),
                "st_post"                => $this->input->post('status_upload', true),
                "post_date"              => date("Y-m-d H:i:s"),
                "cpns"                   => $this->input->post('cpns', true),
                "plt"                    => $this->input->post('plt', true),
                "jabatan_rangkap_perbub" => $jabatan_rangkap_perbub,
                "kategori_pegawai"       => "pegawai",
                "no_rekening"            => 0,
                "kepala"                 => $this->input->post('kepala', true),
                "bendahara_opd"          => $this->input->post('operator_opd', true),
                "mesin_id"               => $this->input->post('mesin_id', true),
            ];
        }
        $this->db->insert('tb_pegawai', $data);
    }

    public function editDataPegawai($id)
    {
        $jabatan_rangkap_perbub = $this->input->post('plt', true) == 1 ? $this->input->post('jabatan_rangkap_perbub', true) : null;
        if ($this->input->post('kategori_pegawai', true) == 1) {
            $data = [
                "nama"                   => $this->input->post('nama', true),
                "opd_id"                 => $this->input->post('opd_id', true),
                "jabatan_opd"            => "",
                "jabatan_perbub_tpp"     => 0,
                "kepala"                 => 0,
                "st_post"                => $this->input->post('status_upload', true),
                "post_date"              => date("Y-m-d H:i:s"),
                "kategori_pegawai"       => "tks",
                "no_rekening"                 => 0,
                "mesin_id"               => $this->input->post('mesin_id', true),
            ];
        } else if ($this->input->post('kategori_pegawai', true) == 2) {
            $data = [
                "nama"                   => $this->input->post('nama', true),
                "nip"                    => $this->input->post('nip', true),
                "golongan"               => $this->input->post('golongan', true),
                "opd_id"                 => $this->input->post('opd_id', true),
                "jabatan_opd"            => $this->input->post('jabatan_opd', true),
                "jabatan_perbub_tpp"     => $this->input->post('jabatan_perbub_tpp', true),
                "st_post"                => $this->input->post('status_upload', true),
                "post_date"              => date("Y-m-d H:i:s"),
                "cpns"                   => $this->input->post('cpns', true),
                "plt"                    => $this->input->post('plt', true),
                "jabatan_rangkap_perbub" => $jabatan_rangkap_perbub,
                "kategori_pegawai"       => "pegawai",
                "no_rekening"            => 0,
                "kepala"                 => $this->input->post('kepala_opd', true),
                "bendahara_opd"           => $this->input->post('operator_opd', true),
                "mesin_id"               => $this->input->post('mesin_id', true),
            ];
        }
        $this->db->where('id', $id);
        $this->db->update('tb_pegawai', $data);
    }

    public function deleteDataPegawai($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tb_pegawai');
    }
}
