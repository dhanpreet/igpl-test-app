<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_subscription_model extends CI_Model {
	public function getBkashCredentials($mode = 'sandbox'){
        $this->db->select('*', false);
        $this->db->from('bkash_credentials');
        $this->db->where('mode', $mode);
        $this->db->limit('1');
        return $this->db->get()->row_array();
    }
	public function getSubscriptionPlans(){
        $this->db->select('*');
        $this->db->from('subscription_plans');
        $this->db->where('sub_plan_status', '1');
        return $this->db->get()->result_array();
	}
	public function getSubscriptionPlanInfo($plan_sku){
        $this->db->select('*');
        $this->db->from('subscription_plans');
        $this->db->where('sub_plan_sku', $plan_sku);
        $this->db->where('sub_plan_status', '1');
        $this->db->order_by('sub_plan_id', 'ASC');
        $this->db->limit('1');
        return $this->db->get()->row_array();
	}
	
	public function setSubscriptionRequest($requestId ,$plan){
        $requestData = array('user_id'=>$this->session->userdata('userId'), 'request_id'=>$requestId ,'plan_name'=>$plan['sub_plan_name'] , 'plan_sku'=>$plan['sub_plan_sku'] , 'plan_days'=>$plan['sub_plan_days'] ,'plan_coins'=>$plan['sub_plan_coins'] ,'plan_amount'=>$plan['sub_plan_amount']);
		$this->db->insert('subscription_data' , $requestData);
        if($this->db->affected_rows()>0){
            return true;
        } else {
            return false;
		}
    }
	public function getUserActiveSubscription(){
		$userId = $this->session->userdata('userId');
        $this->db->select('*', false);
        $this->db->from('subscription_details');
        $this->db->where('sub_user_id', $userId);
        $this->db->where('sub_sub_payment_id != 0');
        // $this->db->where('sub_user_id', $userId);
        $this->db->order_by("sub_id", "desc");
        // $this->db->where('sub_status', '1');
        return $this->db->get()->result_array();
        // echo "<pre>";
        // print_r($this->db->get()->result_array());
        // die();
    }
	
	public function setSubscriptionData($requestId , $response){
		$userId = $this->session->userdata('userId');
        $data = array('user_id' => $userId, 'invoice_id'=>$response->invoiceID, 'payment_status'=>'FAILED');
        $this->db->where('request_id', $requestId);
        $this->db->update('subscription_data' , $data);
		return true;
    }
	
	public function setSubscriptionResponseData($requestId, $response){
        if($response->subscriptionID != 'NA'){
            $data = array(
                'payment_status' => $response->status,
                'subscription_Id' => $response->subscriptionID,
                'error_code'    =>  $response->errorCode,
                'error_message' => $response->errorMessage,
                'createdAt' =>   $response->createdAt,
                'subscription_status' => '1',
            );
        } else {
            $data = array(
                'payment_status' => $response->status,
                'subscription_Id' => $response->subscriptionID,
                'error_code'    =>  $response->errorCode,
                'error_message' => $response->errorMessage,
                'createdAt' =>   $response->createdAt,
                'subscription_status' => '0',
            );
        }
        $this->db->where('invoice_id', $requestId);
        $this->db->update('subscription_data' , $data);
    }
	
    public function setSubscriptionDetails($subscriptionResponse){
       
        $data = array(
            'sub_invoice_id' => $subscriptionResponse->invoiceID,
            'sub_subscription_id' => $subscriptionResponse->id,
            'sub_user_id' => $this->session->userdata('userId'),
            'sub_amount' => $subscriptionResponse->amount,
            'sub_cycle' => $subscriptionResponse->cycle,
            'sub_enabled' => $subscriptionResponse->enabled,
            'sub_createdDate' => $subscriptionResponse->createdDate,
            'sub_startDate' => $subscriptionResponse->startDate,
            'sub_endDate' => $subscriptionResponse->endDate,
            'sub_requestID' => $subscriptionResponse->requestID,
            'sub_errorCode' => $subscriptionResponse->errorCode,
            'sub_errorMessage' => $subscriptionResponse->errorMessage,
        );

        if($this->db->insert('subscription_details', $data)){
        
			$id = $this->db->insert_id();
			$data = array('subscription_detail_id' => $id);
       
			$this->db->where('invoice_id', $subscriptionResponse->invoiceID);
			$result = $this->db->update('subscription_data', $data);
			
            
			
			// Add Coins to user account after successfull subscription
			$this->db->select('sub_plan_coins', FALSE);
			$this->db->where('sub_plan_sku' , $subscriptionResponse->cycle);
			$planCoins = $this->db->get('subscription_plans')->row();

            $this->db->select('user_play_coins', FALSE);
            $this->db->where('user_id' , $this->session->userdata('userId'));
            $userCoins = $this->db->get('site_users')->row_array();
           
            $userPlayCoins = $userCoins['user_play_coins'];
     
            $updateCoins['user_play_coins'] = strval($userPlayCoins + $planCoins->sub_plan_coins);
			$updateCoins['user_subscription'] = '1';
            $updateCoins['user_subscription_id']=$subscriptionResponse->id;
			$updateCoins['user_type'] = '1';
			$this->db->where('user_id' , $this->session->userdata('userId'));
			$this->db->update('site_users', $updateCoins);
        
			// Manage Coin history 
			$coin['coin_user_id']           =   $this->session->userdata('userId');
			$coin['coin_date']              =   date("Y-m-d");
			$coin['coin_section']           =   7;
			$coin['coin_play_coins_add']    =   $planCoins->sub_plan_coins;
			$coin['coin_type']              =   1;
			$coin['coin_added_on']          =   time();
			$this->db->insert('tbl_user_coins_history' , $coin);
			// END

        
		}
    }

    #########################################################################

    public function setRefundData($payment_id, $subscription_id)
    {
        $data2=array(
            'ref_user_id'=>$this->session->userdata('userId'),
            'ref_subscription_id'=>$subscription_id,
            'ref_payment_id'=>$payment_id,
            // 'ref_transaction_id'=>$data->subscriptionPayments[0]->transactionId,
        );
        $result=$this->db->insert('tbl_refund_details' , $data2);
        $id     =       $this->db->insert_id();
        return $id;
    }

    public function setRefundStatus($data , $refund_id , $subscription_id)
    {
        $data2=array(
            'ref_request_id'=>$data->requestID,
            'ref_amount'=>$data->amount,
            'ref_status'=>$data->status,
            'ref_error_code'=>$data->errorCode,
            'ref_error_message'=>$data->errorMessage,
        );
        $this->db->where('ref_id' , $refund_id);
        $result=$this->db->update('tbl_refund_details' , $data2);
        $data3=array(
            'payment_refund_id'=>$refund_id,
            'subscription_status'=>'2',
        );
        $this->db->where('subscription_Id' , $subscription_id);
        $result=$this->db->update('tbl_subscription_data' , $data3);
        $data4=array(
            'sub_status'=>'3',
        );
        $this->db->where('sub_subscription_Id' , $subscription_id);
        $result=$this->db->update('tbl_subscription_details' , $data4);
        return true;
    }
    #########################################################################


    ############################## GEt SUBSCRIPIOn DETAILS #####################
    public function getSubscriptionDetails($id)
    {
        $this->db->select('*' , FALSE);
        $this->db->where('sub_subscription_id' , $id);
        $this->db->where('sub_status','1');
        $result=    $this->db->get('tbl_subscription_details')->result_array();
        if($result)
            return $result[0];
        else 
        return false;
    }


     //  SET PAYMENT DETAILS AFTER GET SUCCEEDED_PAYMENT IN RESPONSE
     public function setSubscriptionDetailsByNotification($subscriptionResponse , $p_id)
     {
         $update['sub_status']=0;
         $this->db->where('sub_subscription_id', $subscriptionResponse->id);
         $this->db->update('tbl_subscription_details' , $update);
 
         $this->db->select('*');
         $this->db->where('subscription_Id' ,$subscriptionResponse->id);
         $result=$this->db->get('tbl_subscription_data');
         $user_id=$result->row()->user_id;
 
         $this->db->select('user_play_coins');
         $this->db->where('user_id' , $user_id);
         $coin=$this->db->get('tbl_site_users');
 
         $coins=$coin->row()->user_play_coins;
         $rewarded_coins=$result->row()->plan_coins;
         $update_coin['user_play_coins']=$coins+$rewarded_coins;
         $this->db->where('user_id', $user_id);
         $this->db->update('tbl_site_users' , $update_coin);
         
                 $coinHistory['coin_user_id'] = $user_id;
                 $coinHistory['coin_date'] = date('Y-m-d');				
                 $coinHistory['coin_section'] = '7';  //1=AddCoins  2=SpinWin  3=RedeemRewardCoins  4=CreateTournament  5=TournamentReward
                 $coinHistory['coin_play_coins_add'] = $rewarded_coins;
                 $coinHistory['coin_play_coins_redeem'] = 0;
                 // $coinHistory['coin_tournament_id'] = $tournamentId;
                 $coinHistory['coin_type'] = '1';  // 1=PlayCoins  2=RewardCoins  3=Both
                 $coinHistory['coin_added_on'] = time();
                 $this->db->insert('user_coins_history', $coinHistory);
 
         $data = array(
             'sub_invoice_id' => $subscriptionResponse->invoiceID,
             'sub_subscription_id' => $subscriptionResponse->id,
             'sub_user_id' => $user_id,
             'sub_amount' => $subscriptionResponse->amount,
             'sub_cycle' => $subscriptionResponse->cycle,
             'sub_enabled' => $subscriptionResponse->enabled,
             'sub_createdDate' => $subscriptionResponse->createdDate,
             'sub_startDate' => $subscriptionResponse->startDate,
             'sub_endDate' => $subscriptionResponse->endDate,
             'sub_requestID' => $subscriptionResponse->requestID,
             'sub_sub_payment_id'=>$p_id,
             'sub_errorCode' => $subscriptionResponse->errorCode,
             'sub_errorMessage' => $subscriptionResponse->errorMessage,
         );
         $this->db->insert('tbl_subscription_details' , $data);
         $detail_id =$this->db->insert_id();
 
         $data2['subscription_detail_id'] = $detail_id;
         $data2['subscription_status']=1;
         $this->db->where('subscription_Id' , $subscriptionResponse->id);
         $this->db->update('tbl_subscription_data' , $data2);
         return true;
 
    }

    public function setPaymentDetails($res)
    {
        $update['pay_active']=0;
        $this->db->where('pay_subscription_id' , $res->subscriptionId);
        $this->db->update('tbl_subscription_notification_payment_success', $update);
        // echo "<pre>";
        // print_r($res);
        // die();

        $this->db->select('user_id', FALSE);
        $this->db->where('subscription_Id' , $res->subscriptionId);
        $result=$this->db->get('tbl_subscription_data');
        // if(!$result)
        //     return false;
        // print_r($this->db->last_query());
        // die();
        $user_id=$result->result_array()[0]['user_id'];
        $data= array(
            'pay_subscription_id'=>$res->subscriptionId,
            'pay_payment_id'=>$res->paymentId,
            'pay_status'=>$res->status,
            'pay_user_id'=>$user_id,
            'pay_cycle'=>$res->cycle,
            'pay_due_date'=>$res->dueDate,
            'pay_transaction_date'=>$res->transactionDate,
            'pay_transaction_id'=>$res->transactionId,
            'pay_reverse_transaction_amount'=>$res->reverseTransactionAmount,
            'pay_reverse_transaction_date'=>$res->reverseTransactionDate,
            'pay_reverse_transaction_id'=>$res->reverseTransactionId,
            'pay_error_code'=>  $res->errorCode,
            'pay_error_message'=>$res->errorMessage,
            'pay_active'=>1
        );
        $this->db->insert('tbl_subscription_notification_payment_success' , $data);
        // $planInfo = $this->SUBSCRIPTIONDBAPI->getSubscriptionPlanInfo($planSKU);
        $user['user_subscription']=1;
        $user['user_subscription_id']=$res->subscriptionId;
        $this->db->where('user_id' , $user_id);
        $this->db->update('tbl_site_users' , $user);
        return true;
    }
    ########################## END #############################

}