<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_topup extends CI_Controller {

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
		$this->load->model('user_topup_model','TOPUPDBAPI');
		$this->load->library('Geoplugin');
		$result=$this->geoplugin->locate('103.37.82.18');
		// $result['geoplugin_countryCode'] ? $this->session->set_userdata('country_code' , $result['geoplugin_countryCode']) : $this->session->set_userdata('country_code' , $result['geoplugin_countryCode']);
		$result['geoplugin_timezone'] ? date_default_timezone_set($result['geoplugin_timezone']) : date_default_timezone_set('Asia/Kolkata');
		$this->session->set_userdata('country_code', 'IN');
		date_default_timezone_set("Asia/Kolkata");
		
	}
    
    public function getTopupPlans(){	
        $userId = $this->session->userdata('userId');
            
        if(!empty($userId)){
            $userActiveSubscription = $this->SUBSCRIPTIONDBAPI->getUserActiveSubscription();
            //echo "<pre>";  print_r($userActiveSubscription); echo "</pre>"; die;
            $tournamentId =  $this->session->userdata('sess_tournament_id');
            $data['sess_tournament_id'] = $tournamentId;
            $data['sess_user_id'] = $userId;
            $data['subscriptionPlans'] = $this->TOPUPDBAPI->getTopUpPlans();
            $this->load->view('site/topup_plans', $data);
        } else {
            redirect();
        }
    }

    public function topupHistory()
	{
		$userId = $this->session->userdata('userId');
		
		if(!empty($userId)){
			$data['activePlan'] = $this->TOPUPDBAPI->getUserTopUpHistory($userId);
			//echo "<pre>";  print_r($userActiveSubscription); echo "</pre>"; die;
			$tournamentId =  $this->session->userdata('sess_tournament_id');
			$data['sess_tournament_id'] = $tournamentId;
			$data['sess_user_id'] = $userId;
			$data['topup_plans'] = $this->TOPUPDBAPI->getTopUpPlans();
			$this->load->view('site/topup_history', $data);
		} else {
			redirect();
		}
	}
    public function generateBkashOnDemandAccessToken(){	
		$result = $this->TOPUPDBAPI->getBkashOnDemandCredentials();
		
       
		$url = TOPUP_ACCESS_TOKEN_URL;   
        $ch = curl_init($url);
        $requestData = ['grant_type'=>$result['grant_type'], 'client_id'=>$result['client_id'], 'client_secret'=>$result['client_secret'] ,'username'=>$result['username'] , 'password'=>$result['password'] ];
        // $data = ['grant_type'=>$result['grant_type'], 'client_id'=>$result['client_id'], 'client_secret'=>$result['client_secret'] ,'username'=>$result['username'] , 'password'=>$result['password'] ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
		
		$response = json_decode($response);
		$access_token = $response->access_token;
		$this->session->set_userdata('bKashOnDemandClientId' , $result['client_id']);
		$this->session->set_userdata('bKashOnDemandAccessToken' , $access_token);
        return $response;
    }

    public function getTopUpRequest($plan)
    {
        $plan=base64_decode($plan);
        $planDetails=$this->TOPUPDBAPI->getPlanDetails($plan);
		$result = $this->generateBkashOnDemandAccessToken();
        $this->session->set_userdata('topup_plans' , $plan);
        $this->session->set_userdata('topup_coins' , $planDetails['p_no_of_coins']);
		$token=$this->session->userdata('bKashOnDemandAccessToken');
		$url = TOPUP_BASE_URL_CHARGE;
        $ch = curl_init($url);
        $data = ['clientID' =>$this->session->userdata('bKashOnDemandClientId') ,'orderID' =>'test01' ,'package'=>'GPL_On_Demand' ,'amount'=>$planDetails['p_fee'] ,'details'=>'On Demand Test' , 'callBackURL'=>base_url().'User_topup/getOnDemandResponse', 'mobile'=>'8801725605108' ];
        $data=json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$authorization="Authorization: Bearer $token";
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json' , $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($result);
        redirect($response->url);
    }

    public function getOnDemandResponse()
    {
        $id=$this->input->get('spTransID');
        $token=$this->session->userdata('bKashOnDemandAccessToken');
        $url = TOP_BASE_URL_STATUS;
        $ch = curl_init($url);
        $data = ['appID' =>$this->session->userdata('bKashOnDemandClientId') ,'spTransID' =>$id];
        $data=json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$authorization="Authorization: Bearer $token";
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json' , $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($result);
        $result     =       $this->TOPUPDBAPI->setTopUpResponse($id ,$response);
        if($response->status=='CHARGED')
        {
            $this->session->set_flashdata('topup_success' , $this->session->userdata('topup_coins')." Coins Credited to your wallet");
        }
        else
            $this->session->set_flashdata('topup_error' , "TopUp Failed. Please try again");
			redirect('LiveTournament/'.$this->session->userdata('sess_tournament_id'));
    }

}