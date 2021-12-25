<?php

function is_logged_in($role = false)
{
    $ci = &get_instance();

    if(!$ci->session->userdata('id'))
    {
        redirect('auth');
        exit();
    }

    if($role)
    if(!in_array($role, auth()->roles))
    {
        redirect('home');
        exit();
    }
}



function loginToken(){
    $ci =&get_instance();

    if(!isset($_GET['token'])) return false;

    $token = $ci->db->where('token', $_GET['token'])->get('tb_token')->row();

    if(!$token) return false;
    if($token->status!=1) return false;
    
    return $token;
}

function auth()
{
    $ci =&get_instance();
    return json_decode(json_encode($ci->session->all_userdata())); 
}


