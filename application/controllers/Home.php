<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	public function __construct(){
        parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
		is_logged_in();
    }

    public function index(){
		$data = [
			"page"				=> "home",
		];
		$this->load->view('template/default', $data);
    }
}
