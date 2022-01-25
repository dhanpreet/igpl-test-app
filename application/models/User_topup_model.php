<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_topup_model extends CI_Model {

    public function getBkashOnDemandCredentials()
    {
        $this->db->select('*');
        $result=$this->db->get('tbl_bkash_ondemand_credentials')->result_array();
        return $result[0];
    }
    public function setTopUpResponse($transId , $response)
    {
        $data=array(
            'top_user_id'=>$this->session->userdata('userId'),
            'top_sp_trans_id'=>$transId,
            'top_status'=>$response->status,
            'top_processing_status'=>$response->processingStatus,
            'top_amount'=>$response->amount,
            'top_bkash_trans_id'=>$response->bKashTransID,
            'top_reference'=>$response->reference,
            'top_error_code'=>$response->errorCode,
            'top_plan'=>$this->session->userdata('topup_plans'),
            'top_no_of_coins'=>$this->session->userdata('topup_coins'),
            'top_error_message'=>$response->errorMessage,
        );
        $this->db->insert('tbl_topup_details' , $data);
        if($response->status=='CHARGED')
        {
            // Add Coins 
            $this->db->select('user_play_coins', FALSE);
            $this->db->where('user_id' , $this->session->userdata('userId'));
            $userCoins = $this->db->get('site_users')->row_array();
           
            $userPlayCoins = $userCoins['user_play_coins'];
     
            $updateCoins['user_play_coins'] = strval($userPlayCoins + $this->session->userdata('topup_coins'));
			$this->db->where('user_id' , $this->session->userdata('userId'));
			$this->db->update('site_users', $updateCoins);
            //  End

            //  Manage Coin History
            $coin['coin_user_id']           =   $this->session->userdata('userId');
			$coin['coin_date']              =   date("Y-m-d");
			$coin['coin_section']           =   8;
			$coin['coin_play_coins_add']    =   $this->session->userdata('topup_coins');
			$coin['coin_type']              =   1;
			$coin['coin_added_on']          =   time();
			$this->db->insert('tbl_user_coins_history' , $coin);
            //  END 
        }
        return true;
    }
    public function getTopUpPlans(){
        $this->db->select('*');
        $this->db->from('tbl_topup_plans');
        $this->db->where('p_status', '1');
        return $this->db->get()->result_array();
	}
    public function getPlanDetails($plan)
    {
        $this->db->select('*');
        $this->db->where('p_plan' , $plan);
        $result =  $this->db->get('tbl_topup_plans')->result_array()[0];
        return $result;
    }

    public function getUserTopUpHistory($userId)
    {
        $this->db->select('*' , FALSE);
        $this->db->where('top_user_id' , $userId);
        $this->db->order_by("top_id", "desc");
        $result = $this->db->get('tbl_topup_details')->result_array();
        return $result;
    }
}