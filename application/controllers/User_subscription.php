<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_subscription extends CI_Controller {

	public  function __construct(){
        parent:: __construct();
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('text');
		$this->load->library('pagination');
		$this->load->library('session');
		$this->load->library('encrypt');
		$this->load->model('site_model','SITEDBAPI');
		$this->load->model('user_subscription_model','SUBSCRIPTIONDBAPI');
		$this->load->library('Geoplugin');
		$result=$this->geoplugin->locate('103.37.82.18');
		// $result['geoplugin_countryCode'] ? $this->session->set_userdata('country_code' , $result['geoplugin_countryCode']) : $this->session->set_userdata('country_code' , $result['geoplugin_countryCode']);
		$result['geoplugin_timezone'] ? date_default_timezone_set($result['geoplugin_timezone']) : date_default_timezone_set('Asia/Kolkata');
		$this->session->set_userdata('country_code', 'IN');
		date_default_timezone_set("Asia/Kolkata");
	}
	public function generateBkashAccessToken(){	
		$result = $this->SUBSCRIPTIONDBAPI->getBkashCredentials($mode='sandbox');
		$requestData = ['grant_type'=>$result['grant_type'], 'client_id'=>$result['client_id'], 'client_secret'=>$result['client_secret'] ,'username'=>$result['username'] , 'password'=>$result['password'] ];
       
		$curl = curl_init(BKASH_ACCESS_TOKEN_URL);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $requestData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
		
		$response = json_decode($response);
		$access_token = $response->access_token;
		$this->session->set_userdata('bKashClientId' , $result['client_id']);
		$this->session->set_userdata('bKashAccessToken' , $access_token);
	}
	public function getSubscriptionPlans(){	
		$userId = $this->session->userdata('userId');
		
		if(!empty($userId)){
			$userActiveSubscription = $this->SUBSCRIPTIONDBAPI->getUserActiveSubscription();
			//echo "<pre>";  print_r($userActiveSubscription); echo "</pre>"; die;
			$tournamentId =  $this->session->userdata('sess_tournament_id');
			$data['sess_tournament_id'] = $tournamentId;
			$data['sess_user_id'] = $userId;
			$data['subscriptionPlans'] = $this->SUBSCRIPTIONDBAPI->getSubscriptionPlans();
			$this->load->view('site/subscription_plans', $data);
		} else {
			redirect();
		}
	}
	public function manageSubscription()
	{
		$userId = $this->session->userdata('userId');
		
		if(!empty($userId)){
			$data['activePlan'] = $this->SUBSCRIPTIONDBAPI->getUserActiveSubscription();
			//echo "<pre>";  print_r($data['activePlan']); echo "</pre>"; die;
			$tournamentId =  $this->session->userdata('sess_tournament_id');
			$data['sess_tournament_id'] = $tournamentId;
			$data['sess_user_id'] = $userId;
			$data['subscriptionPlans'] = $this->SUBSCRIPTIONDBAPI->getSubscriptionPlans();
			$this->load->view('site/manage_subscription', $data);
		} else {
			redirect();
		}
	}
	
	public function getSubscriptionRequest($planSKU){
		$planSKU	= base64_decode($planSKU);
		$planInfo = $this->SUBSCRIPTIONDBAPI->getSubscriptionPlanInfo($planSKU);
		$userActiveSubscription = $this->SUBSCRIPTIONDBAPI->getUserActiveSubscription();
		
		if(is_array($planInfo) && !empty($planInfo['sub_plan_id'])){
			
			$daysCount = $planInfo['sub_plan_days'];
			$startDate = date("Y-m-d");
			$endDate = date('Y-m-d', strtotime($startDate. " + {$daysCount} days"));
			
			$number = rand(1000,1000000);
			$subscriptionRequestId = time().$number;
			$subscriptionRequest = $this->SUBSCRIPTIONDBAPI->setSubscriptionRequest($subscriptionRequestId, $planInfo);
			if($subscriptionRequest){
				//Generate access token
				$this->generateBkashAccessToken();
				
				$this->session->set_userdata('request_id' , $subscriptionRequestId);
				$bKashAccessToken = $this->session->userdata('bKashAccessToken');
				$bKashClientId = $this->session->userdata('bKashClientId');
				
				
				$url = BKASH_BASE_URL.'subscribe';   
				$ch = curl_init($url);
				
				$data = array('clientID'=>$bKashClientId, 'requestID'=>$subscriptionRequestId, 'package'=>'BBC_Janala_Weekly1', 'start'=>$startDate , 'end'=>$endDate ,'userReturnURL'=>site_url('SubscriptionResponse'), 'cycle'=>$planInfo['sub_plan_sku']);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
				$authorization = "Authorization: Bearer $bKashAccessToken";
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json' , $authorization));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch);
				curl_close($ch);
			
				$subscriptionResponse = json_decode($response);
				//echo "<pre>"; print_r($subscriptionResponse); echo "</pre>"; die;
				
				$this->session->set_userdata('bkashInvoiceId', $subscriptionResponse->invoiceID);
				$this->SUBSCRIPTIONDBAPI->setSubscriptionData($subscriptionRequestId, $subscriptionResponse);
				
				redirect($subscriptionResponse->url);
			
			} else {
				redirect('UserSubscription');
			}
			
		} else {
			$this->session->set_flashdata('Error' ,'Error! Invalid Input');
			redirect('UserSubscription');
		}
		
	}
	public function getSubscriptionResponse(){
		$invoiceID = $this->input->get('invoiceID');
		$bKashAccessToken = $this->session->userdata('bKashAccessToken');
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => BKASH_BASE_URL.'subscribe/'.$invoiceID.'/status',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => array(
				'Authorization: Bearer '.$bKashAccessToken.'',
				'Cookie: XSRF-TOKEN=eyJpdiI6IjRVbEdZMmdKVDRVWnR6em9lRzlBbVE9PSIsInZhbHVlIjoib3NHcURKMktzckYzcFFSOFFWd0pPSCt2U3lzTytaQUVnWkdLVXVPck1KRDFZRWFoY01vUDdlSFd4Q0ZvaHJZNmREL1h3cytwV0xYRUE4ZWVsMk1UcTkvZzRubkRZZkxmK0d5L2plYlR1dVU4NEJ4aXRjTkFMR3RMRDlYeHZGa0kiLCJtYWMiOiJhZmExNTUwYmI1NDAyYWEyMTllZTIyYzZiMTg3MTI1MzMwNmMxMjE0ZmVkMDNiYzdhODI3YzM1NGU5OGQxNTY2In0%3D; ghoori_digital_platform_session=eyJpdiI6InVmYVpHcTVDTHIrUFJHbmtGbk40Nmc9PSIsInZhbHVlIjoiMnoydUtmYmxPU3dmNnpHTnZpdDZ5dUN5Umt4UE5vVWIxSDlSaGF1R1VJZEt6K2Z6MlVkMGlaQ1kwbkVXRlhpcUhDYk8xeE9rTW5RdW0wU2sxZklHZnZ0OVNYMWZ0cGpiTGc1Vi9INmN1RkYxcUZSOFJaczBWU3gvS0gybXVjUVAiLCJtYWMiOiJkNjJiNmE4OTkyZDczODQzNTUxYTcxYTM5ZGFiNmZlNmI2MWYyMDFiZmRkMmQwOGFkZjc2NzBhMmI2MWYxNjUyIn0%3D'
			),
		));
		
		$responseInvoice = curl_exec($curl);
		curl_close($curl);
		
		$this->SUBSCRIPTIONDBAPI->setSubscriptionResponseData($invoiceID, json_decode($responseInvoice));
		$responseInvoice = json_decode($responseInvoice);
		
		if($responseInvoice->subscriptionID != 'NA'){
			$subcriptionID = $responseInvoice->subscriptionID;
			$url = BKASH_BASE_URL.'subscription/'.$subcriptionID;   
			$ch = curl_init($url);
			$data = array();
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$authorization = "Authorization: Bearer $bKashAccessToken";
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json' , $authorization));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$subscriptionResponse = curl_exec($ch);
			curl_close($ch);
			
			$subscriptionResponse = json_decode($subscriptionResponse);
			$result = $this->SUBSCRIPTIONDBAPI->setSubscriptionDetails($subscriptionResponse);
			
			if($this->session->userdata('user_tournament'))
			{
				$this->session->set_flashdata('success' , 'You are a Vip User.So you can create tournament now.');
				$this->session->unset_userdata('user_tournament');
				redirect('createTournament/Step-3/'.base64_encode($this->session->userdata('gameId')));
			}
			$this->session->set_flashdata('Success',  'Thank-You for subscribing us');
			$this->session->set_flashdata('InvoiceID', $responseInvoice->invoiceID);
			$this->session->set_userdata('subscription' , '1');
			$this->session->unset_userdata('error');
			$this->session->set_flashdata('subscription_success' , 'Payment Cancelled !');
		} else {
			if($this->session->userdata('user_tournament'))
			{
				$this->session->set_flashdata('error' , 'Payment Failed');
				$this->session->unset_userdata('user_tournament');
				redirect();
			}
			// $this->session->set_userdata('subscription' ,'failed');
			$this->session->set_flashdata('subscription_error' , 'Payment failed');		
		}
		redirect('LiveTournament/'.$this->session->userdata('sess_tournament_id'));
	}
	public function cancelPaymentRequest()
	{
		$subscription_id=$this->input->post('id');
		$result=$this->SUBSCRIPTION->getCredentials();
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
		$this->session->set_userdata('token' , $token);
		$url = BKASH_BASE_URL.'subscription/'.$subscription_id.'/cancel';
        $ch = curl_init($url);
        $data = [];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$authorization="Authorization: Bearer $token";
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json' , $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
		// ***End Cancel Payment Request Api Call ***//
		$data=json_decode($result);
		if($data->requestID)
		{
			$this->SUBSCRIPTION->setCancelPaymentResult($data);
			$url = 'http://sb-payments.ghoori.com.bd/api/v1.0/subscription/cancel/'.$data->requestID.'';  
			$url = BKASH_BASE_URL.'subscription/cancel/'.$data->requestID;
			$ch = curl_init($url);
			$data = [];
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$authorization="Authorization: Bearer $token";
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json' , $authorization));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);
			$data=json_decode($result);
			if($data)
			{
				$this->SUBSCRIPTION->setCancelPaymentDetail($data);
			}
			redirect('ManageSubscription');
			// *** End Cancel Payment Detail Api Call ***//

		}
		else
		{
			redirect('ManageSubscription');
		}
	}
	public function refundPaymentRequest__0()
	{
		$subscription_id=744;
		$result=$this->SUBSCRIPTIONDBAPI->getBkashCredentials();
		$url = BKASH_ACCESS_TOKEN_URL;
        $ch = curl_init($url);
        $data = ['grant_type'=>$result['grant_type'], 'client_id'=>$result['client_id'], 'client_secret'=>$result['client_secret'] ,'username'=>$result['username'] , 'password'=>$result['password'] ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
		$data2 = json_decode($result);
		$token=$data2->access_token;
		$this->session->set_userdata('token' , $token);
		$url = BKASH_BASE_URL.'subscription/'.$subscription_id.'/payments';
        $ch = curl_init($url);
        $data = [];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$authorization="Authorization: Bearer $token";
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json' , $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
		// *** END
		$data=json_decode($result);
		if($data->subscriptionPayments)
		{
			$refund_id=$this->SUBSCRIPTIONDBAPI->setRefundData($data);
			$payment_id=$data->subscriptionPayments[0]->paymentId;
			//  *** Start Payment Refund Api Call **//

				$url = BKASH_BASE_URL.'subscription/payment/'.$payment_id.'/refund';   
				$ch = curl_init($url);
				$data = [
					'amount'=>'22'
				];
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
				$authorization="Authorization: Bearer $token";
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json' , $authorization));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$result = curl_exec($ch);
				$data=json_decode($result);
				if($data)
				{
					$result		=		$this->SUBSCRIPTIONDBAPI->setRefundStatus($data , $refund_id, $subscription_id);
					$this->session->set_flashdata('Success' , 'Refund Successfull');
					// redirect('subscription/profile');
				}
				else
					{
						$this->session->set_flashdata('Error' , "Please Try Again");
					}
		}
		else
			{
				$this->session->set_flashdata('Error' , 'Please Select an Valid Subscription Id');
			}
		print_r($this->session->flashdata('Success'));
		print_r($this->session->flashdata('Error'));
	}

	public function refundPaymentRequest()
	{
		// $payment_id=$this->input->post('payment_id');
		// $subscription_id=$this->input->post('subscription_id');
		// $amount = 	$this->input->post('amount');
		$payment_id			=  	5349;
		$subscription_id	=	749;
		$result=$this->SUBSCRIPTIONDBAPI->getBkashCredentials();
		$url = BKASH_ACCESS_TOKEN_URL;
        $ch = curl_init($url);
        $data = ['grant_type'=>$result['grant_type'], 'client_id'=>$result['client_id'], 'client_secret'=>$result['client_secret'] ,'username'=>$result['username'] , 'password'=>$result['password'] ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
		$data2 = json_decode($result);
		$token=$data2->access_token;
		$this->session->set_userdata('token' , $token);
		$refund_id=$this->SUBSCRIPTIONDBAPI->setRefundData($payment_id , $subscription_id);
		//  *** Start Payment Refund Api Call **//
		$url = BKASH_BASE_URL.'subscription/payment/'.$payment_id.'/refund';   
		$ch = curl_init($url);
		$data = [
			'amount'=>'22'
		];
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		$authorization="Authorization: Bearer $token";
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json' , $authorization));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$data=json_decode($result);
		if($data)
		{
			$result		=		$this->SUBSCRIPTIONDBAPI->setRefundStatus($data , $refund_id, $subscription_id);
			$this->session->set_flashdata('Success' , 'Refund Successfull');
		}
		else
		{
			$this->session->set_flashdata('Error' , "Please Try Again");
		}
		print_r($this->session->flashdata('Success'));
		print_r($this->session->flashdata('Error'));
	}
}