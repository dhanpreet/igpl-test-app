<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bkash_model extends CI_Model {

	 function __construct(){
		parent::__construct();
	}
	
	
	function validateBkashUser($bkashUsername, $bkashPassword, $bkashPhone){
		$this->db->select("*", FALSE);
        $this->db->from('bkash_app_users');        
        $this->db->where('bkash_username', $bkashUsername);
		$this->db->where('bkash_password', $bkashPassword);
		$this->db->where('bkash_phone', $bkashPhone);		
		$this->db->where('bkash_status', '1');
		$this->db->order_by('bkash_user_id', 'desc');
        $this->db->limit('1');		
        return $this->db->get()->row_array();
	}

	
	function getBkashSiteUser($user_bkash_id){
		$this->db->select("*", FALSE);
        $this->db->from('site_users');        
        $this->db->where('user_bkash_id', $user_bkash_id);
        $this->db->order_by('user_id', 'desc');
        $this->db->limit('1');
        return $this->db->get()->row_array();
	}

	
	
	function validateBkashSiteUserToken($user_phone, $token){
		$this->db->select("*", FALSE);
        $this->db->from('site_users');        
        $this->db->where('user_phone', $user_phone);
        $this->db->where('user_bkash_token', $token);
        $this->db->order_by('user_id', 'desc');
        $this->db->limit('1');
        return $this->db->get()->row_array();
	}	
	public function getBkashCredentials($mode = 'sandbox'){
        $this->db->select('*', false);
        $this->db->from('bkash_credentials');
        $this->db->where('mode', $mode);
        $this->db->limit('1');
        return $this->db->get()->row_array();
    }
    public function loadRsaKey()
    {
        $this->db->select('*');
        $result =    $this->db->get('tbl_rsa_key')->row();
        return $result;
    }
	
}  

?>
