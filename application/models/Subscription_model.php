<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscription_model extends CI_Model {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function getSubscriptionPlans()
    {
        $this->db->select('*');
        $this->db->where('status', '1');
        $result=$this->db->get('tbl_subscription_plans')->result_array();
        if($result)
            {
                foreach($result as $row)
                {
                    $data[]=(array)$row;
                }
                $data2['plans']=$data;
                // echo "<pre>";
                // print_r($data2['plans']);
                // die();
                return $data2;
            }
        else
            return false ;
    }
    public function getNoOfDaysOfPlan($cycle)
    {
        $this->db->select('*');
        $this->db->where('plan', $cycle);
        $result=$this->db->get('tbl_subscription_plans')->result_array();
        if($result)
        {
            return $result[0]['no_of_days'];
        }
        else
        return false;
    }
    public function getCredentials()
    {
        $this->db->select('*');
        $result=$this->db->get('tbl_bkash_credentials')->result_array();
        return $result[0];
    }
    public function setSubscriptionData($requestId , $response)
    {
        $data=array(
            'user_id'=>$this->session->userdata('userId'),
            // 'request_id'=>$requestId,
            'invoice_id'=>$response->invoiceID,
            'payment_status'=>'FAILED',
        );
        $this->db->where('request_id' , $requestId);
        $this->db->update('tbl_subscription_data' , $data);
        $result=$this->db->insert_id();
        return true;
    }
    public function setSubscriptionAllData($id, $response)
    {
        if($response->subscriptionID!='NA')
        {
            $data=array(
                'payment_status'=>$response->status,
                'subscription_Id'=>$response->subscriptionID,
                'error_code'    =>  $response->errorCode,
                'error_message'=> $response->errorMessage,
                'createdAt'=>   $response->createdAt,
                'subscription_status'=>'1',
            );
        }
        else
        {
            $data=array(
                'payment_status'=>$response->status,
                'subscription_Id'=>$response->subscriptionID,
                'error_code'    =>  $response->errorCode,
                'error_message'=> $response->errorMessage,
                'createdAt'=>   $response->createdAt,
                'subscription_status'=>'0',
            );
        }
        $this->db->where('invoice_id', $id);
        $result=$this->db->update('tbl_subscription_data' , $data);
    }
    public function setSubscriptionDetails($final_result)
    {
        $data=array(
            'sub_invoice_id'=>$final_result->invoiceID,
            'sub_subscription_id'=>$final_result->id,
            'sub_user_id'=>$this->session->userdata('userId'),
            'sub_amount'=>$final_result->amount,
            'sub_cycle'=>$final_result->cycle,
            'sub_enabled'=>$final_result->enabled,
            'sub_createdDate'=>$final_result->createdDate,
            'sub_startDate'=>$final_result->startDate,
            'sub_endDate'=>$final_result->endDate,
            'sub_requestID'=>$final_result->requestID,
            'sub_errorCode'=>$final_result->errorCode,
            'sub_errorMessage'=>$final_result->errorMessage,
        );
        $result=$this->db->insert('tbl_subscription_details', $data);
        // if($result)
        // {
        $id=$this->db->insert_id();
        $data=array(
            'subscription_detail_id'=>$id,
        );
        $this->db->where('invoice_id', $final_result->invoiceID);
        $result=$this->db->update('tbl_subscription_data', $data);
        // Add Coins to user account after successfull subscription
        $this->db->select('no_of_coins', FALSE);
        $this->db->where('plan' , $final_result->cycle);
        $coins=$this->db->get('tbl_subscription_plans')->row();

        // Add coins into exiting coin of the user
            $this->db->select('user_play_coins', FALSE);
            $this->db->where('user_id' , $this->session->userdata('userId'));
            $result=$this->db->get('tbl_site_users')->result_array()[0];
            // print_r($result['user_play_coins']);
            // die();
            $existed_coins=$result['user_play_coins'];
        // End
            $data2['user_play_coins']=strval($existed_coins+ $coins->no_of_coins);
        // $data2['user_play_coins']=$coins->no_of_coins;
        $data2['user_subscription']='1';
        $this->db->where('user_id' , $this->session->userdata('userId'));
        $this->db->update('tbl_site_users', $data2);
        
        // Manage Coin history //
        $coin['coin_user_id']           =   $this->session->userdata('userId');
        $coin['coin_date']              =   date("Y-m-d");
        $coin['coin_section']           =   7;
        $coin['coin_play_coins_add']    =   $coins->no_of_coins;
        $coin['coin_type']              =   1;
        $coin['coin_added_on']          =   time();
        $this->db->insert('tbl_user_coins_history' , $coin);
        // END

        // }
    }

    // *****************************SET TOPUP DEtails********************************************//

    public function setTopUpSubscriptionDetails($final_result)
    {
        $data=array(
            'sub_invoice_id'=>$final_result->invoiceID,
            'sub_subscription_id'=>$final_result->id,
            'sub_user_id'=>$this->session->userdata('userId'),
            'sub_amount'=>$final_result->amount,
            'sub_cycle'=>$final_result->cycle,
            'sub_enabled'=>$final_result->enabled,
            'sub_createdDate'=>$final_result->createdDate,
            'sub_startDate'=>$final_result->startDate,
            'sub_endDate'=>$final_result->endDate,
            'sub_requestID'=>$final_result->requestID,
            'sub_errorCode'=>$final_result->errorCode,
            'sub_errorMessage'=>$final_result->errorMessage,
        );

        $result=$this->db->insert('tbl_subscription_details', $data);
        $id=$this->db->insert_id();
        $data=array(
            'subscription_detail_id'=>$id,
        );
        $this->db->where('invoice_id', $final_result->invoiceID);
        $result=$this->db->update('tbl_subscription_data', $data);
        // Add Coins to user account after successfull subscription
        

        // Add coins into exiting coin of the user
            $this->db->select('user_play_coins', FALSE);
            $this->db->where('user_id' , $this->session->userdata('userId'));
            $result=$this->db->get('tbl_site_users')->result_array()[0];
            // print_r($result['user_play_coins']);
            // die();
            $existed_coins=$result['user_play_coins'];
        // End
            $data2['user_play_coins']=strval($existed_coins+ $this->session->userdata('top_value'));
        // $data2['user_play_coins']=$coins->no_of_coins;
        // $data2['user_subscription']='1';
        $this->db->where('user_id' , $this->session->userdata('userId'));
        $this->db->update('tbl_site_users', $data2);
        // }
        
        // Manage Coin History
        $coin['coin_user_id']           =   $this->session->userdata('userId');
        $coin['coin_date']              =   date("Y-m-d");
        $coin['coin_section']           =   7;
        $coin['coin_play_coins_add']    =   $this->session->userdata('top_value');
        $coin['coin_type']              =   1;
        $coin['coin_added_on']          =   time();
        $this->db->insert('tbl_user_coins_history' , $coin);
        // END

    }

    // ********************************************************* END *********************************************************//


    public function getActiveSubscriptionID()
    {
        $this->db->select('*');
        $this->db->where('sub_user_id', $this->session->userdata('userId'));
        $this->db->where('sub_status', '1');
        $result=$this->db->get('tbl_subscription_details');
        if($result->num_rows()>0)
        {
            foreach($result->result_array() as $row)
                $data[]=(array)$row;
            $data2['active_plan']=$data;
            return $data2;
        }
        else
            {
                $data2['active_plan']='';
                return $data2;
            }
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
    
    // ##################################################### END #######################################################

    public function setCancelPaymentResult($data)
    {
        print_r($data);
        $data2=array(
            'user_id'                       =>              $this->session->userdata('userId'),
            'subscription_Id'               =>              $data->subscriptionID,
            'payment_cancel_request_id'     =>              $data->requestID,
            'status'                        =>              $data->status,
            'error_code'                    =>              $data->errorCode,
            'error_message'                 =>              $data->errorMessage,
        );
        $result=$this->db->insert('tbl_subscription_cancel', $data2);
        $cancel_id=$this->db->insert_id();
        // *** To change status of active subscription of subscription_details
        $data3=array(
            'sub_status'=>'5',
        );
        $this->db->where('sub_subscription_id', $data->subscriptionID);
        $this->db->where('sub_status' , 1);
        $result=$this->db->update('tbl_subscription_details',$data3);
        // ** End

        // ** To Change the status of tbl_subscription_data
        $data4=array(
            'cancelled_by_user'=>'1',
            'payment_cancel_id'=>$cancel_id,
        );
        $this->db->where('subscription_Id' , $data->subscriptionID);
        $this->db->update('tbl_subscription_data', $data4);
        // ** END
    }
    public function setCancelPaymentDetail($data)
    {
        $data2=array(
            // 'user_id'                       =>              $this->session->userdata('user_id'),
            // 'subscription_Id'               =>              $data->subscriptionID,
            // 'payment_cancel_request_id'     =>              $data->requestID,
            'status'                        =>              $data->status,
            'error_code'                    =>              $data->errorCode,
            'error_message'                 =>              $data->errorMessage,
        );
        $this->db->where('subscription_Id' , $data->subscriptionID);
        $this->db->where('user_id' , $this->session->userdata('userId'));
        $result=$this->db->update('tbl_subscription_cancel', $data2);
    }
    public function setRefundData($data)
    {
        $data2=array(
            'ref_user_id'=>$this->session->userdata('userId'),
            'ref_subscription_id'=>$data->subscriptionPayments[0]->subscriptionId,
            'ref_payment_id'=>$data->subscriptionPayments[0]->paymentId,
            'ref_transaction_id'=>$data->subscriptionPayments[0]->transactionId,
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
    public function setRequest($requestid)
    {
        $data=array(
            'user_id'=>$this->session->userdata('userId'),
            'request_id'=>$requestid
        );
        $this->db->insert('tbl_subscription_data' , $data);
        if($this->db->affected_rows()>0)
        {
            return true;
        }
        else
            return false;
    }
}