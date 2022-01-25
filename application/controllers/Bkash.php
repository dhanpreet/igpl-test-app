<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Bkash extends REST_Controller {
    
	  /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function __construct() {
        parent::__construct();
        
        $this->load->database();
        $this->load->library('Geoplugin');
        $this->load->model('bkash_model');
        $this->load->model('User_topup_model' ,'TOPUPDBAPI');
        $this->load->model('user_subscription_model' , 'SUBSCRIPTIONDBAPI');
         $this->load->library('phpseclib/Crypt/RSA');
        //  $this->load->library('phpseclib/Net/SSH2');
        $result=$this->geoplugin->locate('103.37.82.18');
	    $result['geoplugin_timezone'] ? date_default_timezone_set($result['geoplugin_timezone']) : date_default_timezone_set('Asia/Kolkata');
	    $this->session->set_userdata('country_code', 'IN');
	    $this->session->set_userdata('ip_address', '103.37.82.18');
    }
    


	public function index_get(){
       
		$loginTokenShared = $_GET['token'];  
		$loginToken = base64_decode($loginTokenShared);  
		$loginTokenArray = explode('-', $loginToken);
        // print_r($loginToken);
        // die();
		$bkashUserPhone =  $loginTokenArray[1];
		$bkashUserToken =  $loginToken;
		$currentTimestamp = time();
		
		$validateUserToken = $this->bkash_model->validateBkashSiteUserToken($bkashUserPhone, $loginTokenShared);
		if(!empty($validateUserToken['user_id'])){
			$userId = $validateUserToken['user_id'];
			$tokenValidTill = $validateUserToken['user_bkash_token_update'];
			$timestamp =  strtotime($tokenValidTill);
			$timeDiff = round(($timestamp - $currentTimestamp));
			if($timeDiff < 0 || $timeDiff > 60) {
				$this->session->set_flashdata("error","ERROR: EXPIRED OR INVALID REQUEST");
				redirect('error');
			} else {
				$dataLogin['login_user_country_code'] = $this->session->userdata('country_code');
				$dataLogin['login_user_ip_address'] = $this->session->userdata('ip_address');
				$dataLogin['login_user_id'] = $userId;
				$dataLogin['login_user_login_from'] = '2';  // 1=Web  2=App
				$dataLogin['login_user_login_at'] = date("Y-m-d H:i:s", $currentTimestamp);
				$this->db->insert('tbl_user_login_history', $dataLogin);
				
				$this->session->set_userdata('userId', $userId);
				redirect('/?token='.base64_encode($userId));
			}
		} else {

			$status = (string) parent::HTTP_UNAUTHORIZED;
			$response = ['status' => $status, 'message' => 'Unauthorized Access!'];
			$this->response($response, $status);
            
		}
	 
       
	}
      

    public function auth_post(){
		$loginJSON = file_get_contents('php://input');
        $requestJSON = json_decode($loginJSON);
		
		$bkash_username  =   @$requestJSON->username;
        $bkash_password  =   @$requestJSON->password;
        $bkash_phone     =   @$requestJSON->mobile_number;
		
        $loginToken = time().'-'.$bkash_phone;
					$loginToken = base64_encode($loginToken);
					$loginUpdateTime = date('Y-m-d H:i:s',strtotime('+1 minutes')) ;
					$loginAccessURL = base_url().'bkash/?token='.$loginToken;
		if(!empty($bkash_username) && !empty($bkash_password) && !empty($bkash_phone)){
			$validateBkashUser = $this->bkash_model->validateBkashUser($bkash_username, $bkash_password, $bkash_phone);
			if(!empty($validateBkashUser['bkash_user_id'])){
					$siteUser = $this->bkash_model->getBkashSiteUser($validateBkashUser['bkash_user_id']);
					
					
					
					$bkashSiteUser['user_bkash_token'] = $loginToken;
					$bkashSiteUser['user_bkash_token_update'] = $loginUpdateTime;
					$bkashSiteUser['user_last_login'] = $loginUpdateTime;
					$bkashSiteUser['user_updated_on'] = time();
					$this->db->where('user_id', $siteUser['user_id']);
					$this->db->update('tbl_site_users' , $bkashSiteUser);
					
					$status = (string) parent::HTTP_OK;
					$response = ['id_token' => $loginToken, 'update_time' => $loginUpdateTime, 'access_url' => $loginAccessURL];
					$this->response($response, $status);
					
			} else {  
			   
				$bkashUser['bkash_username'] = $bkash_username;
				$bkashUser['bkash_password'] = $bkash_password;
				$bkashUser['bkash_phone'] = $bkash_phone;
				$bkashUser['bkash_status'] = '1';
				$bkashUser['bkash_added_on'] = date('Y-m-d H:i:s');
				$bkashUser['bkash_updated_on'] = date('Y-m-d H:i:s');
				if($this->db->insert('bkash_app_users' , $bkashUser)){
					$bkash_id = $this->db->insert_id();
					
					// $loginToken = time().'-'.$bkash_phone;
					// $loginToken = base64_encode($loginToken);
					// $loginUpdateTime = date('Y-m-d H:i:s');
					// $loginAccessURL = base_url().'bkash/?token='.$loginToken;
					
					$bkashSiteUser['user_full_name'] = $bkash_username;
					$bkashSiteUser['user_email'] = 'b_'.$bkash_phone.'@igpl.pro';
					$bkashSiteUser['user_password'] = $bkash_password;
					$bkashSiteUser['user_phone'] = $bkash_phone;
					$bkashSiteUser['user_image'] = 'default.png';
					$bkashSiteUser['user_subscription'] = '0';
					$bkashSiteUser['user_subscription_id'] = '';
					$bkashSiteUser['user_type'] = '1';
					$bkashSiteUser['user_status'] = '1';
					$bkashSiteUser['user_login_type'] = '4';  //1=manual  2=Facebook  3=Google 4=Bkash
					$bkashSiteUser['user_bkash_id'] = $bkash_id;
					$bkashSiteUser['user_bkash_token'] = $loginToken;
					$bkashSiteUser['user_bkash_token_update'] =  $loginUpdateTime;
					$bkashSiteUser['user_registered_on'] = $loginUpdateTime;
					$bkashSiteUser['user_last_login'] = $loginUpdateTime;
					$bkashSiteUser['user_added_on'] = time();
					$bkashSiteUser['user_updated_on'] = time();
					$this->db->insert('site_users' , $bkashSiteUser);
				}
				$status = (string) parent::HTTP_OK;
				$response = ['id_token' => $loginToken, 'update_time' => $loginUpdateTime, 'access_url' => $loginAccessURL];
				$this->response($response, $status);
			}
		
		} else {
			$status = (string) parent::HTTP_UNAUTHORIZED;
			$response = ['error_code' => "01", 'message' => "Required information missing"];
			$this->response($response, $status);
		}
       
    } 
    ##################################################################################################
	 ######################################### Save Notification ####################################
	##################################################################################################
	
    public function createSignatureForSubscription_post()
    {
        $content    =   file_get_contents('php://input');
        $result     =   $this->SUBSCRIPTIONDBAPI->getBkashCredentials();
        $secret     =   $result['client_secret'];
        $digest     =   hash_hmac('sha256' ,$content, $secret);
        $result     =   $this->bkash_model->loadRsaKey();
        $key        =   $result->rsa_private_key;
        $pkey       =   $result->rsa_public_key;
        $rsa        =   new RSA;
        $rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
        $rsa->loadkey($key);
        $signature = base64_encode($rsa->sign($digest));
        $res['x_signature'] =$signature;
        $res['x_content_digest'] = base64_encode($digest);
        $this->response($res, REST_Controller::HTTP_OK);
    }

	public function notification_post()
    {
        $result=$this->SUBSCRIPTIONDBAPI->getBkashCredentials();
        $content = file_get_contents('php://input');
        $request = json_decode($content);
        $secret =   $result['client_secret'];
        $digest =   hash_hmac('sha256' ,$content, $secret);
        foreach(getallheaders() as $key=>$value)
            $data[$key]=$value;
        $x_content_digest=base64_decode($data['X-Content-Digest']);
        $is_integrity_confirmed=hash_equals($digest , $x_content_digest);
        $result2=$this->bkash_model->loadRsaKey();
        $pkey=$result2->rsa_public_key;
        $x_signature    =   base64_decode($data['X-Signature']);
        
        // $rsa = new RSA();
        $rsa = new RSA;
        $rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
        $rsa->loadkey($pkey); // public key
        
        $is_verified = $rsa->verify($digest, $x_signature);

        if($is_verified && $is_integrity_confirmed)
        {
            $data3['n_notification_id']  =   $request->notificationID;
            $data3['n_type']             =   $request->type;
            $data3['n_status']           =   $request->status;
            $data3['n_subscription_id']  =   $request->subscriptionID;
            $data3['n_payment_id']       =   $request->paymentID;
            $data3['n_bkash_timestamp']  =   $request->timestamp;
            $this->db->insert('tbl_subscription_notification', $data3);
            
            $res['recordID']  =   $this->db->insert_id();
            $res['status']    =   'RECEIVED';
            $res['timestamp'] =    time();
            if($data3['n_status']=='CANCELED')
            {
                // Cancel subscription from tbl_subscription_data table
                // print_r("hello");
                // die();
                $cancel_data['subscription_status']='2';
                $this->db->where('subscription_Id' , $data3['n_subscription_id']);
                $this->db->update('tbl_subscription_data' , $cancel_data);
                // END
                //  Cancel Subscrition from tbl_subscription_details table
                $cancel['sub_enabled']  =   'false';
                $cancel['sub_status']   =   '2';
                // $cancel['sub_updated_at']=date('Y-m-d H:i:s');
                $this->db->where('sub_subscription_id' ,$data3['n_subscription_id']);
                $this->db->update('tbl_subscription_details' , $cancel);
                // END
				################# Update tbl_subscription_cancel statue ################
				$cancel_status['status']	='CANCELED';
				$this->db->where('subscription_Id' , $data3['n_subscription_id']);
				$this->db->update('tbl_subscription_cancel' ,$cancel_status);	
				################################ END ##################################
                // $pay['pay_active']=2;
                // $this->db->where('pay_subscription_id' , $data3['n_subscription_id']);
                // // $this->db->where('pay_payment_id' , $data3['n_payment_id']);
                // $this->db->update('tbl_subscription_notification_payment_success' ,$pay);

                // Cance overall subscription of the user
                $this->db->select('user_id' , FALSE);
                $this->db->where('subscription_Id' , $data3['n_subscription_id']);
                $id=$this->db->get('tbl_subscription_data')->result_array();
                if($id)
                    {
                        $cancel_all['user_subscription']= 0;
                        $cancel_all['user_subscription_id']='';
                        $this->db->where('user_id' , $id[0]['user_id']);
                        $this->db->update('tbl_site_users' , $cancel_all);
                    }
            }
           else if($data3['n_status']=='FAILED_REFUND')
            {
                $this->db->select('payment_refund_id');
                $this->db->where('subscription_Id' , $data3['n_subscription_id']);
                $result = $this->db->get('tbl_subscription_data');
            }
            else if($data3['n_status']=='FAILED_PAYMENT')
            {
                $site_user['user_subscription'] = 2;
                $this->db->where('user_subscription_id' , $data3['n_subscription_id'] );
                $this->db->update('tbl_site_users' , $site_user);

                $sub_data['subscription_status'] = 3;
                $this->db->where('subscription_Id' , $data3['n_subscription_id']);
                $this->db->update('tbl_subscription_data' , $sub_data);

                $sub_details['sub_status'] = 4;
                $sub_details['sub_status'] = 2;
                $this->db->where('sub_subscription_id' , $data3['n_subscription_id'] );
                $this->db->where('sub_enabled' , 'true');
                $this->db->update('tbl_subscription_details' , $sub_details);
            }

            else if($data3['n_status']=='FAILED_INSUFFICIENT_BALANCE')
            {
                $site_user['user_subscription'] = 2;
                $this->db->where('user_subscription_id' , $data3['n_subscription_id'] );
                $this->db->update('tbl_site_users' , $site_user);

                $sub_data['subscription_status'] = 3;
                $this->db->where('subscription_Id' , $data3['n_subscription_id']);
                $this->db->update('tbl_subscription_data' , $sub_data);

                $sub_details['sub_status'] = 4;
                $sub_details['sub_status'] = 2;
                $this->db->where('sub_subscription_id' , $data3['n_subscription_id'] );
                $this->db->where('sub_enabled' , 'true');
                $this->db->update('tbl_subscription_details' , $sub_details);
            }
            else if($data3['n_status']=='SUCCEEDED_REFUND')
            {
                $refund['sub_status']   =   '3';
                $this->db->where('sub_subscription_id' ,$data3['n_subscription_id']);
                $this->db->where('sub_enabled' ,'true');
                $this->db->update('tbl_subscription_details' , $refund);
                
                // $this->db->select('payment_refund_id');
                // $this->db->where('subscription_Id' , $data3['n_subscription_id']);
                // $result = $this->db->get('tbl_subscription_data');
                // if($result)
                // {
                    
                // }
            }
            else if($data3['n_status']=='SUCCEEDED_PAYMENT')
            {
                // Start Generate Token
                // $result=$this->SUBSCRIPTION->getCredentials();
                $url = BKASH_ACCESS_TOKEN_URL;   
                $ch = curl_init($url);
                $data = ['grant_type'=>$result['grant_type'], 'client_id'=>$result['client_id'], 'client_secret'=>$result['client_secret'] ,'username'=>$result['username'] , 'password'=>$result['password'] ];
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
                $data2 = json_decode($result);
                // End
                
                $token=$data2->access_token;
                // CAll Payment Details API
                // $url = 'http://sb-payments.ghoori.com.bd/api/v1.0/subscription/payment/'.$data3['n_payment_id'];
                $url = BKASH_BASE_URL.'subscription/payment/'.$data3['n_payment_id'];
                // print_r($url);
                // die();
                $ch = curl_init($url);
                /* Array Parameter Data */
                $data = array(
                    'id'=>'5'
                );
                /* pass encoded JSON string to the POST fields */
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                /* set the content type json */
                $authorization="Authorization: Bearer $token";
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json' , $authorization));
                /* set return type json */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                /* execute request */
                $value = curl_exec($ch);    
                /* close cURL resource */
                curl_close($ch);
                $response=json_decode($value);
                // print_r($response);
                // die();
                $this->SUBSCRIPTIONDBAPI->setPaymentDetails($response);

                // $url = 'http://sb-payments.ghoori.com.bd/api/v1.0/subscription/'.$data3['n_subscription_id'];   
                $url = BKASH_BASE_URL.'subscription/'.$data3['n_subscription_id'];
                $ch = curl_init($url);
                /* Array Parameter Data */
                $data = array(
                    // 'id'=>'5'
                );
                /* pass encoded JSON string to the POST fields */
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                /* set the content type json */
                $authorization="Authorization: Bearer $token";
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json' , $authorization));
                /* set return type json */
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                /* execute request */
                $result = curl_exec($ch);
                /* close cURL resource */
                curl_close($ch);
                // echo "<pre>";
                $final_result=json_decode($result);
                // print_r($final_result);
                // die();
                $this->SUBSCRIPTIONDBAPI->setSubscriptionDetailsByNotification($final_result , $data3['n_payment_id']);
                // $result=$this->SUBSCRIPTION->setSubscriptionDetails($final_result);
            }
            $this->response($res , REST_Controller::HTTP_OK);
        }
        else
        {
            $data2="False";
            $this->response($data2, REST_Controller::HTTP_OK);
        }
    }


	##################################################################################################
	 ###################################### End Notification Save ###################################
	##################################################################################################



    /* *************************  Notifications From bKash ************************  */


    ################################################################################
    ########################### START TOP UP INTEGRITY #############################
    ################################################################################
    public function createSignature_post()
    {
        $content = file_get_contents('php://input');
        $result=$this->TOPUPDBAPI->getBkashOnDemandCredentials();
        $secret =   $result['client_secret'];
        $digest =   hash_hmac('sha256' ,$content, $secret);
        
        $result=$this->bkash_model->loadRsaKey();
        $key=$result->rsa_private_key;
        $pkey=$result->rsa_public_key;
        $rsa = new RSA;
        $rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
        $rsa->loadkey($key);
        $signature = base64_encode($rsa->sign($digest));
        $res['x_signature'] =$signature;
        $res['x_content_digest'] = base64_encode($digest);
        $this->response($res, REST_Controller::HTTP_OK);
    }
    public function notificationTopup_post()
    {
        $result=$this->TOPUPDBAPI->getBkashOnDemandCredentials();
        $content    =   file_get_contents('php://input');
        $request    =   json_decode($content);
        $secret     =   $result['client_secret'];
        $digest     =   hash_hmac('sha256' ,$content, $secret);
        foreach(getallheaders() as $key=>$value)
            $data[$key]=$value;
        $x_content_digest=base64_decode($data['X-Content-Digest']);
        $is_integrity_confirmed=hash_equals($digest , $x_content_digest);
        $x_signature    =   base64_decode($data['X-Signature']);
        $result=$this->bkash_model->loadRsaKey();
        $key=$result->rsa_private_key;
        $pkey=$result->rsa_public_key;
        $rsa = new RSA;
        $rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
        $rsa->loadkey($pkey);
        $is_verified = $rsa->verify($digest, $x_signature);
        if($is_integrity_confirmed && $is_verified)
            {
                $data2['ack_sp_trans_id']=$request->spTransID;
                $data2['ack_package']=$request->package;
                $data2['ack_mobile']=$request->mobile;
                $data2['ack_bkash_msisdn']=$request->bKashMsisdn;
                $data2['ack_amount']=$request->amount;
                $data2['ack_status']=$request->status;
                $data2['ack_processing_status']=$request->processingStatus;
                $data2['ack_bkash_trans_id']=$request->bKashTransID;
                $data2['ack_payment_date']=$request->paymentDate;
                $data2['ack_action_taken']=$request->actionTaken;
                if($data2['ack_action_taken']=='PRODUCT_SENT')
                {
                    $this->db->insert('tbl_topup_acknowledgement' , $data2);
                    $res['actionTaken']='ACKNOWLEDGED';
                    $this->response($res, REST_Controller::HTTP_OK);
                }
                
            }
        else
            {
                $res=false;
                $this->response($res, REST_Controller::HTTP_OK);
            }
    }

    ################################################################################
    ############################## END TOP UP INTEGRITY ############################
    ################################################################################
}