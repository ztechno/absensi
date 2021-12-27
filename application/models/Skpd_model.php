<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Skpd_model extends CI_Model
{

    public function get($skpd_id=false){ 
        if($skpd_id){
            $this->db->where('id', $skpd_id);
        }
        return $this->db->get('tb_opd')->result();

    }


}
