<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ob_start();
class Site extends CI_Controller {
	
	var $unreadNotifications = '0';
	var $quickTournamentEnabled = 0;
	var $createTournamentEnabled = 0;
	var $freeTournamentEnabled = 0;
	var $vipTournamentEnabled = 0;
	var $globalLeaderboardEnabled = 0;
	var $practiceBannersEnabled = 0;
	var $redemptionCoinsEnabled = 0;
	var $redemptionDataPackEnabled = 0;
	var $redemptionTalkTimeEnabled = 0;
	var $redemptionGameAccessEnabled = 0;
	var $payAndPlayTournamentEnabled	=	0;
	
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
		$ip=$this->input->ip_address();
		$val=$this->input->valid_ip($ip) ? true : false;
		$this->session->set_userdata('valid_ip' , $val);
		$result=$this->geoplugin->locate($ip);
		$result['geoplugin_timezone'] ? date_default_timezone_set($result['geoplugin_timezone']) : date_default_timezone_set('Asia/Kolkata');
		$this->session->set_userdata('country_code', 'IN');
		$userId = $this->session->userdata('userId');	
		$notifyCount	= $this->SITEDBAPI->getUserUnreadNotificationsCount($userId);
		$this->unreadNotifications	= $notifyCount['rows_count'];		
		//Get The portal enabled/ disabled settings
		$portalSettings	= $this->SITEDBAPI->getPortalSettings();
		if( $this->search_exif($portalSettings, 'quick_tournaments') ){ 
			$this->quickTournamentEnabled	= 1;
		}
		if( $this->search_exif($portalSettings, 'create_tournaments') ){ 
			$this->createTournamentEnabled	= 1;
		}
		if( $this->search_exif($portalSettings, 'vip_tournaments') ){ 
			$this->vipTournamentEnabled	= 1;
		}
		if( $this->search_exif($portalSettings, 'free_tournaments') ){ 
			$this->freeTournamentEnabled	= 1;
		}
		if( $this->search_exif($portalSettings, 'global_leaderboard') ){ 
			$this->globalLeaderboardEnabled	= 1;
		}
		if( $this->search_exif($portalSettings, 'practise_banners') ){ 
			$this->practiceBannersEnabled	= 1;
		}
		if( $this->search_exif($portalSettings, 'redemption_coins') ){ 
			$this->redemptionCoinsEnabled	= 1;
		}
		if( $this->search_exif($portalSettings, 'redemption_data_pack') ){ 
			$this->redemptionDataPackEnabled	= 1;
		}
		if( $this->search_exif($portalSettings, 'redemption_talktime') ){ 
			$this->redemptionTalkTimeEnabled	= 1;
		}
		if( $this->search_exif($portalSettings, 'redemption_games_access') ){ 
			$this->redemptionGameAccessEnabled	= 1;
		}
		if( $this->search_exif($portalSettings, 'pay_and_play_tournaments') ){ 
			$this->payAndPlayTournamentEnabled	= 1;
		}
    }
	
	function search_exif($exif, $field){
		foreach ($exif as $data){
			if ($data['name'] == $field)
				return $data['enabled'];
		}
	}
	
	public function logout()	{
		$data['login_user_logout_at']=date("Y-m-d H:i:s");
		$this->USERDBAPI->manageLogoutHistory($data);
		$this->session->sess_destroy();
		redirect('error');
	}
	
	public function error()	{
		$this->load->view('site/error');
	}
	
	public function privacyPolicy()	{
		$this->load->view('site/privacy_policy');
	}
	
	public function terms()	{
		$this->load->view('site/terms_of_use');
	}
	
	function createShareCode($length){
		// String of all alphanumeric character
		$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

		// Shufle the $str_result and returns substring
		// of specified length
		return substr(str_shuffle($str_result), 0, $length);
	}

	function createOTP($length){
		// String of all alphanumeric character
		$str_result = '0123456789';
		// Shufle the $str_result and returns substring
		// of specified length
		return substr(str_shuffle($str_result), 0, $length);
	}


	public function index()	{
		$userId = '1';
		$user_login_type = '1';
		
		// print_r($this->session->userdata('valid_ip'));
		// die();
		   //1=manual  2=Facebook  3=Google 4=BkashApp
		// Get Test user details
		if(!empty($_GET['token'])) {
			$id = base64_decode($_GET['token']);  
			if(!$this->session->userdata('valid_ip'))
			{
				redirect('error');
			}
			$userInfo = $this->SITEDBAPI->validateUser($id);
			if(!empty($userInfo['user_id'])){
				$userId = $userInfo['user_id'];  
				$user_login_type = $userInfo['user_login_type'];
				
				if($userInfo['user_status'] == 1){
					if(empty($userInfo['skillpod_player_id'])){
						$personEmail = $userInfo['user_email'];
						// Create  Skillpod id on gameboost for playing the games
						// $this->createGameboostId($userId, $personEmail);
					}
				} else {
					redirect('error');
				}
			
			} else {
				//$userId = '1';  // deafult user
				//redirect('Login');
				redirect('error');
			}
		} else if($this->session->userdata('userId')){
			$userId = $this->session->userdata('userId');
			$user_login_type = $this->session->userdata('user_login_type');
		} 	

		if(empty($userId) || $userId==''){
			//$this->session->set_flashdata("error","Error! Please login first to access this portal.");
			//redirect('Login');
			redirect('error');
		} 
		
		
		//echo $userId; die;
		$this->session->set_userdata('userId', $userId);
		$this->session->set_userdata('user_login_type', $user_login_type);

		//echo $userId;
		$data['userInfo'] = $this->SITEDBAPI->getSiteUserDetail($userId);
		if($data['userInfo']['user_subscription_id']!='')
			{
				$s_id = $data['userInfo']['user_subscription_id'];
				$result = $this->SUBSCRIPTIONDBAPI->getSubscriptionDetails($s_id);
				if($result)
				{
					$exp_date=strtotime($result['sub_endDate']);
				// echo $exp_date;
				// echo "<br>";
				$date=strtotime(date("Y-m-d").' +2 days ');
				// echo $date;
				// die();
				if($date>=$exp_date)
					$data['msg']=true;
				else
					$data['msg']=false;
				if($exp_date< strtotime(date("Y-m-d")) )
					$data['msg']=false;
				$data['subscriptionDetails']=$result;	
				$data['parking'] = false;
				}
				else
				{
				$data['msg']=false;
				if($data['userInfo']['user_subscription']==2)
				$data['parking'] = "Your auto payment failed due to some reaseon";
				else
				$data['parking'] = false;
				}
				
			}
		else
		{
			$data['msg']=false;
			$data['parking'] = false;
		}
		$liveInfo = $this->SITEDBAPI->getSiteUserLiveTournament($userId);
		
		//echo $this->db->last_query();
		//echo "<pre>"; print_r($data['liveInfo']);  echo "</pre>"; die;
		
		if(is_array($liveInfo) && count($liveInfo)>0){ 
			$data['liveInfo'] = $liveInfo;
			
			$today = time();
			$startDate = $data['liveInfo']['t_start_date']." ".$data['liveInfo']['t_start_time'].":00";
			$startDate = strtotime($startDate);

			$endDate = $data['liveInfo']['t_end_date']." ".$data['liveInfo']['t_end_time'].":00";
			$endDate = strtotime($endDate);

			$status = 0;     //1=CurrentlyWorking   2=Expired   3=futureTournament
			if($startDate > $today){
				$status = 3;
			} else if($endDate < $today){
				$status = 2;
			} else if($startDate <= $today && $endDate >= $today){
				$status = 1;
			}
			$data['t_current_status'] = $status;
			
		} else {
			$data['liveInfo'] = array();
		}
		
		//If quick tournaments section enabled call the games list
		if($this->quickTournamentEnabled == 1){
			$data['quickTournaments'] = $this->SITEDBAPI->getQuickTournamnetGamesList($limit=8);
		}
		
		if($this->practiceBannersEnabled == 1){
			$data['practiceBanners'] = $this->SITEDBAPI->getPractiseBannersList($limit=8);
		}
		
		if($this->vipTournamentEnabled == 1){
			$data['vipTournaments'] = $this->SITEDBAPI->getVipTournamentList($limit=8);
		}
		
		if($this->freeTournamentEnabled == 1){
			$data['freeTournaments'] = $this->SITEDBAPI->getFreeTournamentList($limit=8);
		}
		if($this->payAndPlayTournamentEnabled==1){
			$data['payAndPlayTournaments'] = $this->SITEDBAPI->getPayAndPlayTournamentList($limit=8);
		}
		
		//echo $this->db->last_query(); die;
		$data['suggestedGames'] = $this->SITEDBAPI->getSuggestedGamesList($limit=8);
		$data['topGames'] = $this->SITEDBAPI->getTopGamesList($limit=8);
		$data['actionGames'] = $this->SITEDBAPI->getHomeGenreGamesList($type='action', $limit=8);
		$data['arcadeGames'] = $this->SITEDBAPI->getHomeGenreGamesList($type='arcade', $limit=8);
		$data['adventureGames'] = $this->SITEDBAPI->getHomeGenreGamesList($type='adventure', $limit=8);
		$data['sportsGames'] = $this->SITEDBAPI->getHomeGenreGamesList($type='sports', $limit=8);
		$data['puzzleGames'] = $this->SITEDBAPI->getHomeGenreGamesList($type='puzzle', $limit=8);
		$data['session_page_type']=1;
		$this->load->view('site/home',$data);
		
	}


	public function tournamentsList()	{
		$userId = '';
		$user_login_type = '1';   //1=manual  2=Facebook  3=Google 4=BkashApp
		// Get Test user details
		if(!empty($_GET['token'])) {
			$id = base64_decode($_GET['token']);  
			$userInfo = $this->SITEDBAPI->validateUser($id);
			if(!empty($userInfo['user_id'])){
				$userId = $userInfo['user_id'];  
				$user_login_type = $userInfo['user_login_type'];
				
				if($userInfo['user_status'] == 1){
					if(empty($userInfo['skillpod_player_id'])){
						$personEmail = $userInfo['user_email'];
						// Create  Skillpod id on gameboost for playing the games
						$this->createGameboostId($userId, $personEmail);
					}
				} else {
					redirect('error');
				}
			
			} else {
				redirect('error');
			}
		} else if($this->session->userdata('userId')){
			$userId = $this->session->userdata('userId');
			$user_login_type = $this->session->userdata('user_login_type');
		} 	

		if(empty($userId) || $userId==''){
			redirect('error');
		} 

		$this->session->set_userdata('userId', $userId);
		$this->session->set_userdata('user_login_type', $user_login_type);

		$data['userInfo'] = $this->SITEDBAPI->getSiteUserDetail($userId);
		if($data['userInfo']['user_subscription_id']!='')
			{
				$s_id = $data['userInfo']['user_subscription_id'];
				$result = $this->SUBSCRIPTIONDBAPI->getSubscriptionDetails($s_id);
				if($result)
				{
					$exp_date=strtotime($result['sub_endDate']);
				$date=strtotime(date("Y-m-d").' +2 days ');
				if($date>=$exp_date)
					$data['msg']=true;
				else
					$data['msg']=false;
				if($exp_date< strtotime(date("Y-m-d")) )
					$data['msg']=false;
				$data['subscriptionDetails']=$result;	
				$data['parking'] = false;
				}
				else
				{
				$data['msg']=false;
				if($data['userInfo']['user_subscription']==2)
				$data['parking'] = "Your auto payment failed due to some reaseon";
				else
				$data['parking'] = false;
				}
				
			}
		else
		{
			$data['msg']=false;
			$data['parking'] = false;
		}
		$liveInfo = $this->SITEDBAPI->getSiteUserLiveTournament($userId);
		
		if(is_array($liveInfo) && count($liveInfo)>0){ 
			$data['liveInfo'] = $liveInfo;
			
			$today = time();
			$startDate = $data['liveInfo']['t_start_date']." ".$data['liveInfo']['t_start_time'].":00";
			$startDate = strtotime($startDate);

			$endDate = $data['liveInfo']['t_end_date']." ".$data['liveInfo']['t_end_time'].":00";
			$endDate = strtotime($endDate);

			$status = 0;     //1=CurrentlyWorking   2=Expired   3=futureTournament
			if($startDate > $today){
				$status = 3;
			} else if($endDate < $today){
				$status = 2;
			} else if($startDate <= $today && $endDate >= $today){
				$status = 1;
			}
			$data['t_current_status'] = $status;
			
		} else {
			$data['liveInfo'] = array();
		}
		
		if($this->freeTournamentEnabled == 1){
			$data['freeTournaments'] = $this->SITEDBAPI->getFreeTournamentListAll();
		}
		if($this->payAndPlayTournamentEnabled==1){
			$data['payAndPlayTournaments'] = $this->SITEDBAPI->getPayAndPlayTournamentListAll();
		}

		$this->load->view('site/tournament_live_list',$data);
		
	}
	public function vipList()	{
		$userId = '';
		$user_login_type = '1';   //1=manual  2=Facebook  3=Google 4=BkashApp
		// Get Test user details
		if(!empty($_GET['token'])) {
			$id = base64_decode($_GET['token']);  
			$userInfo = $this->SITEDBAPI->validateUser($id);
			if(!empty($userInfo['user_id'])){
				$userId = $userInfo['user_id'];  
				$user_login_type = $userInfo['user_login_type'];
				
				if($userInfo['user_status'] == 1){
					if(empty($userInfo['skillpod_player_id'])){
						$personEmail = $userInfo['user_email'];
						// Create  Skillpod id on gameboost for playing the games
						$this->createGameboostId($userId, $personEmail);
					}
				} else {
					redirect('error');
				}
			
			} else {
				redirect('error');
			}
		} else if($this->session->userdata('userId')){
			$userId = $this->session->userdata('userId');
			$user_login_type = $this->session->userdata('user_login_type');
		} 	

		if(empty($userId) || $userId==''){
			redirect('error');
		} 
		
		$this->session->set_userdata('userId', $userId);
		$this->session->set_userdata('user_login_type', $user_login_type);

		$data['userInfo'] = $this->SITEDBAPI->getSiteUserDetail($userId);
		if($data['userInfo']['user_subscription_id']!='')
			{
				$s_id = $data['userInfo']['user_subscription_id'];
				$result = $this->SUBSCRIPTIONDBAPI->getSubscriptionDetails($s_id);
				if($result)
				{
					$exp_date=strtotime($result['sub_endDate']);
				
				$date=strtotime(date("Y-m-d").' +2 days ');
				if($date>=$exp_date)
					$data['msg']=true;
				else
					$data['msg']=false;
				if($exp_date< strtotime(date("Y-m-d")) )
					$data['msg']=false;
				$data['subscriptionDetails']=$result;	
				$data['parking'] = false;
				}
				else
				{
				$data['msg']=false;
				if($data['userInfo']['user_subscription']==2)
				$data['parking'] = "Your auto payment failed due to some reaseon";
				else
				$data['parking'] = false;
				}
				
			}
		else
		{
			$data['msg']=false;
			$data['parking'] = false;
		}
		$liveInfo = $this->SITEDBAPI->getSiteUserLiveTournament($userId);
		
		if(is_array($liveInfo) && count($liveInfo)>0){ 
			$data['liveInfo'] = $liveInfo;
			
			$today = time();
			$startDate = $data['liveInfo']['t_start_date']." ".$data['liveInfo']['t_start_time'].":00";
			$startDate = strtotime($startDate);

			$endDate = $data['liveInfo']['t_end_date']." ".$data['liveInfo']['t_end_time'].":00";
			$endDate = strtotime($endDate);

			$status = 0;     //1=CurrentlyWorking   2=Expired   3=futureTournament
			if($startDate > $today){
				$status = 3;
			} else if($endDate < $today){
				$status = 2;
			} else if($startDate <= $today && $endDate >= $today){
				$status = 1;
			}
			$data['t_current_status'] = $status;
			
		} else {
			$data['liveInfo'] = array();
		}
		if($this->vipTournamentEnabled == 1){
			$data['vipTournaments'] = $this->SITEDBAPI->getVipTournamentListAll();
		}
		
		$this->load->view('site/tournament_live_list_VIP',$data);
		
	}

	public function createGameboostId($userId, $gameboostEmail){
		if(!empty($userId) && !empty($gameboostEmail) ){
			
			$gameboostMSISDN = time();
			$gameboostNickname = "unknown_".$userId;
			$gameboostPassword = $this->createShareCode(12);
			
			$postArray = array(
			'nickname' => $gameboostNickname,
			'email' => $gameboostEmail,
			'msisdn' => $gameboostMSISDN,
			'password' => $gameboostPassword,
			'gender' => 'male',
			'date_of_birth' => '1990-01-01'
			);
			
			
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://games.igpl.pro/xml-api/register_player',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_SSL_VERIFYHOST => 0,
			  CURLOPT_SSL_VERIFYPEER => 0,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => $postArray,
			  CURLOPT_HTTPHEADER => array(
				'Authorization: Bearer eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJwYXJ0bmVyX2NvZGUiOiJ0ZXN0LTAwMSIsInBhcnRuZXJfcGFzc3dvcmQiOiJ0ZXN0LTAwMSJ9.GQp4_XWFc1FkHHGoy6XWVe40_QHCUt4ityt57ahXoEMW2AhNHo27V_wJmypgZshbw1w6345mKffaGtf9XowGOA'
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			//echo $response;
				
			$xmlResponse = simplexml_load_string($response);
			//	echo "<pre>"; print_r($xmlResponse); echo "</pre>";
			
			$status = $xmlResponse->register_player->result;
			
			if(!empty($status) && $status == 'success'){
				$skillpod_player_id = $xmlResponse->register_player->skillpod_player_id;
				$skillpod_player_key = $xmlResponse->register_player->skillpod_player_key;
				
				$dataUser['user_email'] = $gameboostEmail;
				$dataUser['skillpod_nickname'] = $gameboostNickname;
				$dataUser['skillpod_password'] = $gameboostPassword;
				$dataUser['skillpod_object_id'] = $gameboostMSISDN;
				$dataUser['skillpod_player_id'] = $skillpod_player_id;
				$dataUser['skillpod_player_key'] = $skillpod_player_key;
				$this->db->where('user_id', $userId);
				$this->db->update('site_users', $dataUser);
			}
		}
	}
	

	public function userLogin(){
	
		$this->load->view('site/user_login.php');	
		
	}
	
	public function verifyUserLogin(){
		// if(!$this->session->userdata('valid_ip'))
		// {
		// 	print_r("We are unable to get your ip. So you can't access our portal.");
		// 	die();
		// }
		$phone = $_POST['user_phone'];
		$password = $_POST['user_password'];
		if(!empty($phone) && !empty($password)){
			
			$verifyUser =  $this->SITEDBAPI->verifyUserDetails($phone, $password);
			if(!empty($verifyUser['user_id']) && is_array($verifyUser) && count($verifyUser)>0 ){
				$user_id = $verifyUser['user_id'];
				$data['login_user_id']=$user_id;
				$data['login_user_country_code']=$this->session->userdata('country_code');
				$data['login_user_login_at']=date('Y-m-d H:i:s');
				$result=$this->USERDBAPI->manageLoginHistory($data);
				redirect('site/index/?token='.base64_encode($user_id));
				
			} else {
				$this->session->set_flashdata("error","Error! Invalid Mobile No. or Password.");
				redirect('Login');
			}
		} else {
			$this->session->set_flashdata("error","Error! Required parameters are missing.");
			redirect('Login');
		}
	}
	
	
// *****************************   *************************************** ********************************** //
// *****************************   Live  VIP/Pay&Play/Free Tournaments Starts Here ********************************** //
// *****************************   *************************************** ********************************** //


	public function getLiveTournament($id){
			
		
		$userId = $this->session->userdata('userId');
		$userInfo = $this->SITEDBAPI->getSiteUserDetail($userId);
		$id = base64_decode($id);
		$this->session->set_userdata('sess_tournament_id', base64_encode($id));
		// Get tournament info
		$this->session->set_userdata('tournament','VIP');
		$data['tournamentInfo'] = $this->SITEDBAPI->getLiveTournamentInfo($id);
		// echo "<pre>";
		// print_r($data['tournamentInfo']);
		// die();
		if(is_array($data['tournamentInfo']) && count($data['tournamentInfo'])>0){
			
			$data['playersCount'] = $this->SITEDBAPI->getLiveTournamentPlayersCount($id);
			$data['playersInfo'] = $this->SITEDBAPI->getLiveTournamentPlayersListDESC($id);
			$data['checkPlayerEntry'] = $this->SITEDBAPI->checkLiveTournamentPlayer($userId, $id);
			$data['totalPlayersCount'] = count($data['playersInfo']);
			
			$myRank = 0;
			$myScore = 0;
			$joinedStatus = false;
			
			// the user already joined this tournament
			if(!empty($data['checkPlayerEntry']['player_id'])){
			
				// set user joined the tournament
				$joinedStatus = true;
				
				if(is_array($data['playersInfo']) && count($data['playersInfo'])>0){
					
					$highest_score = $data['playersInfo'][0]['player_score'];
					$rank = 1;
					
					foreach($data['playersInfo'] as $rowPlayer){
						if($rowPlayer['user_id'] == $userId){
							$myScore = $rowPlayer['player_score'];
						}
						if($highest_score !=0 && $rowPlayer['player_score'] == $highest_score){
						} else {
							if($rowPlayer['player_score'] > 0 ){
								$rank++;
							} 
							$highest_score = $rowPlayer['player_score'];
						}
						
						if($rowPlayer['user_id'] == $userId){
							$myRank = $rank;
						}
					}
				}
			}	
			
			$data['joinedStatus'] = $joinedStatus;
			$data['myScore'] = $myScore;
			$data['myRank'] = $myRank;
			
			/*
			echo "<pre>";
			print_r($data['tournamentInfo']);
			echo "</pre>";
			die;  */
			$data['userInfo']  = $this->SITEDBAPI->getSiteUserDetail($userId);
			$data['checkPlayerEntry'] = $this->SITEDBAPI->checkLiveTournamentPlayer($userId, $id);
			if($data['checkPlayerEntry'])
				$data['joined']=true;
			else
				$data['joined']=false;
			$data['session_page_type']=2;
			$data['session_tournament_id']=$id;
			$data['session_game_id']=$data['tournamentInfo']['tournament_game_id'];
			$this->load->view('site/live_tournament_info', $data);
		} else {
			redirect();
		}
	}

	public function managePlayCoinsHistory($section, $coin_type, $coins, $tournament_id=''){
		$userId = $this->session->userdata('userId');
		if(!empty($userId)){
			$coin['coin_user_id']           =   	$userId;
			$coin['coin_date']              =   	date("Y-m-d");
			$coin['coin_section']           =   	$section;
			if($coin_type == 'add')
				$coin['coin_play_coins_add']    =   $coins;
			if($coin_type == 'redeem')
				$coin['coin_play_coins_redeem']    =  $coins;
			
			$coin['coin_tournament_id']		=		$tournament_id;
			$coin['coin_type']              =   	1;
			$coin['coin_added_on']          =   	time();
			$this->db->insert('tbl_user_coins_history' , $coin);
		}
	}
	
	public function updateUserPlayCoins($coin_type, $coins){
		$userId = $this->session->userdata('userId');
		if(!empty($userId)){
			$userInfo = $this->SITEDBAPI->getSiteUserDetail($userId);
			$userPlayCoins = $userInfo['user_play_coins'];
			
			if($coin_type == 'add')
				$dataCoins['user_play_coins']    =   ($userPlayCoins+$coins);
			if($coin_type == 'redeem')
				$dataCoins['user_play_coins']    = ($userPlayCoins-$coins);
			
			$this->db->where('user_id' , $userId);
			$this->db->update('tbl_site_users' , $dataCoins);
		}
	}
	
	
	// public function playLiveTournament__0($id){
	// 	$tournament_id = base64_decode($id); 
	// 	$userId = $this->session->userdata('userId');
	// 	$playStatus = false;
	// 	$joinedStatus = false;
	// 	if(!empty($userId) && !empty($tournament_id) ){
	// 		// Get tournament info
	// 		$data['tournamentInfo'] = $this->SITEDBAPI->getLiveTournamentInfo($tournament_id);
	// 		$data['userInfo'] = $this->SITEDBAPI->getSiteUserDetail($userId);
	// 		$data['checkPlayerEntry'] = $this->SITEDBAPI->checkLiveTournamentPlayer($userId, $tournament_id);
	// 		if(!empty($data['checkPlayerEntry']['player_id'])){
	// 			$joinedStatus = true;
	// 		}
	// 		if($data['tournamentInfo']['tournament_section'] == 2){  //1=Free  2=VIP  3=Pay&Play
				
	// 			if(!$joinedStatus){
					
	// 				if($this->session->userdata('subscription')=='failed'){
	// 					$this->session->unset_userdata('subscription');
	// 					$this->session->set_flashdata('subscription_error' , 'Payment Cancelled !');
	// 					redirect('LiveTournament/'.$this->session->userdata('sess_tournament_id'));
	// 				}
					
	// 				if($data['userInfo']['user_subscription'] == 0){  
	// 					$this->session->set_userdata('sess_tournament_id', base64_encode($tournament_id));
	// 					$this->session->set_userdata('sess_tournament_type','VIP');
	// 					$this->session->set_userdata('sess_user_subscribed','0');
	// 					redirect('UserSubscription');
	// 				} else {
						
	// 					$userPlayCoins = $data['userInfo']['user_play_coins'];
	// 					$entryFee = $data['tournamentInfo']['fee_tournament_fee'];
	// 					if($userPlayCoins >= $entryFee){
							
	// 						// Join the user 
	// 						$savePlayer['player_t_id'] = $tournament_id;
	// 						$savePlayer['player_user_id'] = $userId;
	// 						$savePlayer['player_score'] = 0;
	// 						$savePlayer['player_type'] = '2';
	// 						$savePlayer['player_added_on'] = time();
	// 						$this->db->insert('tournaments_players', $savePlayer);
					
	// 						$this->updateUserPlayCoins($coin_type='redeem', $coins=$entryFee);
	// 						$this->managePlayCoinsHistory($section='6', $coin_type='redeem', $coins=$entryFee, $tournament_id);
						
	// 						$gameId = $data['tournamentInfo']['tournament_gameboost_id'];
	// 						$playerProfileId = $data['userInfo']['skillpod_player_id'];
							
	// 						// Update the Report for the user wise tournament game play count
	// 						$this->addReportUserGamePlay($type=2, $gameId, $tournament_id);
			
	// 						$data['game_id'] = $gameId;
	// 						$data['player_profile_id'] = $playerProfileId;
	// 						$data['tournament_id'] = $tournament_id;
	// 						$this->load->view('site/live_tournament_play_game', $data);
						
	// 					} else {
							
	// 						$this->session->set_userdata('sess_tournament_id', base64_encode($tournament_id));
	// 						$this->session->set_userdata('sess_tournament_type','Pay&Play');
							
	// 						if($this->session->userdata('topup')=='failed'){
	// 							$this->session->unset_userdata('topup');
	// 							$this->session->set_flashdata('topup_error' , 'Payment Cancelled !');
	// 							redirect('LiveTournament/'.$this->session->userdata('sess_tournament_id'));
	// 						}
	// 						redirect('UserSubscription/TOPUP');
	// 					}
	// 				}
	// 			} else {
					
	// 				$gameId = $data['tournamentInfo']['tournament_gameboost_id'];
	// 				$playerProfileId = $data['userInfo']['skillpod_player_id'];
					
	// 				// Update the Report for the user wise tournament game play count
	// 				$this->addReportUserGamePlay($type=2, $gameId, $tournament_id);
			
	// 				$data['game_id'] = $gameId;
	// 				$data['player_profile_id'] = $playerProfileId;
	// 				$data['tournament_id'] = $tournament_id;
	// 				$this->load->view('site/live_tournament_play_game', $data);
	// 			}
	// 		} else if($data['tournamentInfo']['tournament_section'] == 3){  //1=Free  2=VIP  3=Pay&Play
				
	// 			if(!$joinedStatus){
	// 				if($this->session->userdata('top_up')=='failed'){
	// 					$this->session->unset_userdata('top_up');
	// 					$this->session->set_flashdata('top_error' , 'Top Up  Failed. Please try Agian');
	// 					redirect('LiveTournament/'.$this->session->userdata('sess_tournament_id'));
	// 				}
					
	// 				$userPlayCoins = $data['userInfo']['user_play_coins'];
	// 				$entryFee = $data['tournamentInfo']['fee_tournament_fee'];
	// 				if($userPlayCoins >= $entryFee){
						
	// 					// Join the user 
	// 					$savePlayer['player_t_id'] = $tournament_id;
	// 					$savePlayer['player_user_id'] = $userId;
	// 					$savePlayer['player_score'] = 0;
	// 					$savePlayer['player_type'] = '2';
	// 					$savePlayer['player_added_on'] = time();
	// 					$this->db->insert('tournaments_players', $savePlayer);
				
	// 					$this->updateUserPlayCoins($coin_type='redeem', $coins=$entryFee);
	// 					$this->managePlayCoinsHistory($section='6', $coin_type='redeem', $coins=$entryFee, $tournament_id);
					
	// 					$gameId = $data['tournamentInfo']['tournament_gameboost_id'];
	// 					$playerProfileId = $data['userInfo']['skillpod_player_id'];
						
	// 					// Update the Report for the user wise tournament game play count
	// 					$this->addReportUserGamePlay($type=2, $gameId, $tournament_id);
		
	// 					$data['game_id'] = $gameId;
	// 					$data['player_profile_id'] = $playerProfileId;
	// 					$data['tournament_id'] = $tournament_id;
	// 					$this->load->view('site/live_tournament_play_game', $data);
					
	// 				} else {
	// 					$this->session->set_userdata('sess_tournament_id', base64_encode($tournament_id));
	// 					$this->session->set_userdata('sess_tournament_type','Pay&Play');

	// 					redirect('UserSubscription/TOPUP');
	// 				}
						
	// 			} else {
	// 				$gameId = $data['tournamentInfo']['tournament_gameboost_id'];
	// 				$playerProfileId = $data['userInfo']['skillpod_player_id'];
					
	// 				// Update the Report for the user wise tournament game play count
	// 				$this->addReportUserGamePlay($type=2, $gameId, $tournament_id);
			
	// 				$data['game_id'] = $gameId;
	// 				$data['player_profile_id'] = $playerProfileId;
	// 				$data['tournament_id'] = $tournament_id;
	// 				$this->load->view('site/live_tournament_play_game', $data);
	// 			}
	// 		}
	// 	} else {
	// 		redirect();
	// 	}
	// }


	public function playLiveTournament($id){
		$tournament_id = base64_decode($id); 
		$userId = $this->session->userdata('userId');
		$joinedStatus = false;
		if(!empty($userId) && !empty($tournament_id)){
			
			// Get tournament info
			$data['tournamentInfo'] = $this->SITEDBAPI->getLiveTournamentInfo($tournament_id);
			$data['userInfo'] = $this->SITEDBAPI->getSiteUserDetail($userId);
			$data['checkPlayerEntry'] = $this->SITEDBAPI->checkLiveTournamentPlayer($userId, $tournament_id);
			if(!empty($data['checkPlayerEntry']['player_id'])){
				$joinedStatus = true;
			}
			
			$userPlayCoins = $data['userInfo']['user_play_coins'];
			$entryFee = $data['tournamentInfo']['fee_tournament_fee'];
			
			if(!$joinedStatus){
				if($data['tournamentInfo']['tournament_section'] == 2){  //1=Free  2=VIP  3=Pay&Play
					if($this->session->userdata('subscription')=='failed'){
						$this->session->unset_userdata('subscription');
						$this->session->set_flashdata('subscription_error' , 'Payment Cancelled !');
						redirect('LiveTournament/'.$this->session->userdata('sess_tournament_id'));
					}
					if($data['userInfo']['user_subscription'] == 2){
						$this->session->set_flashdata('auto_pay',true);
						redirect('LiveTournament/'.$this->session->userdata('sess_tournament_id'));
					}
					if($data['userInfo']['user_subscription'] == 0){  
						$this->session->set_userdata('sess_tournament_id', base64_encode($tournament_id));
						$this->session->set_userdata('sess_tournament_type','VIP');
						$this->session->set_userdata('sess_user_subscribed','0');
						$this->session->set_flashdata('subs',true);
						//redirect('LiveTournament/'.$this->session->userdata('sess_tournament_id'));
						redirect('UserSubscription');
					
					} else if($userPlayCoins < $entryFee){
						$this->session->set_userdata('sess_tournament_id', base64_encode($tournament_id));
						$this->session->set_userdata('sess_tournament_type','VIP');
						$this->session->set_flashdata('topup',true);
						//redirect('LiveTournament/'.$this->session->userdata('sess_tournament_id'));
						redirect('UserSubscription/TOPUP');
					}
				
				} else if($data['tournamentInfo']['tournament_section'] == 3){  //1=Free  2=VIP  3=Pay&Play
					if($this->session->userdata('top_up')=='failed'){
						$this->session->unset_userdata('top_up');
						$this->session->set_flashdata('top_error' , 'Top Up  Failed. Please try Agian');
						redirect('LiveTournament/'.$this->session->userdata('sess_tournament_id'));
					
					} else if($userPlayCoins < $entryFee){
						$this->session->set_userdata('sess_tournament_id', base64_encode($tournament_id));
						// $this->session->set_userdata('sess_tournament_type','VIP');
						// if($this->session->userdata('topup')=='failed')
						// {
							$this->session->set_flashdata('topup',true);
						// 	$this->session->unset_userdata('topup');
						// 	$this->session->set_flashdata('topup_error' , 'Payment Cancelled !');
						// 	redirect(site_url('LiveTournament/'.$this->session->userdata('sess_tournament_id')));
						// }
						//redirect('LiveTournament/'.$this->session->userdata('sess_tournament_id'));
						redirect('UserSubscription/TOPUP');
					}
				}
			}
			
			if(!$joinedStatus){
				
				// Join the user 
				$savePlayer['player_t_id'] = $tournament_id;
				$savePlayer['player_user_id'] = $userId;
				$savePlayer['player_score'] = 0;
				$savePlayer['player_type'] = '2';
				$savePlayer['player_added_on'] = time();
				$this->db->insert('tournaments_players', $savePlayer);
		
				$this->updateUserPlayCoins($coin_type='redeem', $coins=$entryFee);
				$this->managePlayCoinsHistory($section='6', $coin_type='redeem', $coins=$entryFee, $tournament_id);
						
			}
				
			$gameId = $data['tournamentInfo']['tournament_gameboost_id'];
			$playerProfileId = $data['userInfo']['skillpod_player_id'];
			
			// Update the Report for the user wise tournament game play count
			$this->addReportUserGamePlay($type=2, $gameId, $tournament_id);

			$data['game_id'] = $gameId;
			$data['player_profile_id'] = $playerProfileId;
			$data['tournament_id'] = $tournament_id;
			$data['session_page_type']=4;
			$data['session_tournament_id']=$tournament_id;
			$data['session_game_id']=$data['tournamentInfo']['tournament_game_id'];
			$this->load->view('site/live_tournament_play_game', $data);
			
		} else {
			redirect();
		}
	}


	public function updateLiveTournamentPlayerScore_0($tournament_id='', $game_id='', $skillpod_player_id='', $redirect=''){
		
		if(!empty($tournament_id) && !empty($game_id) && !empty($skillpod_player_id) && !empty($redirect)){
			
			$userId = $this->session->userdata('userId');
			$tournament_id =  base64_decode($tournament_id);
			$game_id = $game_id;
			$skillpod_player_id =  $skillpod_player_id;
			$redirect_path =  $redirect;
			
			$tournamentInfo = $this->SITEDBAPI->getLiveTournamentInfo($tournament_id);
			
			// Get current user score starts
			$apiURL = "https://multiplayergameserver.com/xmlapi7/xmlapi.php?site_id=834&password=GiK2Xz9Ty&nocompress=true&action=get_player_scores&order_by_field=time&order_by_direction=DESC&game_id=".$game_id."&skillpod_player_id=".$skillpod_player_id."&show_games=false";
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiURL);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/xml'));
			$response = curl_exec($ch);
			curl_close($ch);

			$responseXML = simplexml_load_string($response);
			$responseJSON = json_encode($responseXML);

			// Convert into associative array
			$responseArray = json_decode($responseJSON, true);
		
			$userScore = @$responseArray['player_scores']['player_score_0']['score'];
			$scoreDate = @$responseArray['player_scores']['player_score_0']['time'];
			$scoreDate = date('Y-m-d', strtotime($scoreDate));
			
			$t_start_date = $tournamentInfo['tournament_start_date'];
			$t_end_date = $tournamentInfo['tournament_end_date'];
			
			/*
			if($t_start_date <= $scoreDate &&  $t_end_date >= $scoreDate ){
				if(!empty($userScore)){
					$currentScore = $userScore;
				} else {
					$currentScore = 0;
				}
			} else {
				$currentScore = 0;
			}
			*/
			$currentScore = $userScore;
			
			//Get User last saved score
			$scoreInfo = $this->SITEDBAPI->getLiveTournamentPlayerScore($tournament_id, $userId);
			$lastScore = @$scoreInfo['player_score'];
			$player_id = @$scoreInfo['player_id'];
			if($currentScore >= $lastScore){
				$saveScore['player_score'] = $currentScore;
				$this->db->where('player_id', $player_id);
				$this->db->update('tournaments_players', $saveScore);
			}
			
			if($redirect_path == 'redirect_leaderboard'){
				redirect('LiveTournamentLeaderboard/'.base64_encode($tournament_id));
			} else {
				redirect('LiveTournament/'.base64_encode($tournament_id));
			}
			
		} else {
			redirect();
		}
	}
	public function updateLiveTournamentPlayerScore($tournament_id='', $game_id='', $skillpod_player_id='', $redirect=''){
	
		if(!empty($tournament_id) && !empty($game_id) && !empty($skillpod_player_id) && !empty($redirect)){
			
			$userId = $this->session->userdata('userId');
			$tournament_id =  base64_decode($tournament_id);
			$game_id = $game_id;
			$skillpod_player_id =  $skillpod_player_id;
			$redirect_path =  $redirect;
			
			$tournamentInfo = $this->SITEDBAPI->getLiveTournamentInfo($tournament_id);
			
			$postArray = array('game_id' => $game_id,'player_id' => $skillpod_player_id);
			
			// Get current user score starts
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://games.igpl.pro/xml-api/get_player_scores',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_SSL_VERIFYHOST => 0,
			  CURLOPT_SSL_VERIFYPEER => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => $postArray,
			  CURLOPT_HTTPHEADER => array(
				'Authorization: Bearer eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJwYXJ0bmVyX2NvZGUiOiJ0ZXN0LTAwMSIsInBhcnRuZXJfcGFzc3dvcmQiOiJ0ZXN0LTAwMSJ9.GQp4_XWFc1FkHHGoy6XWVe40_QHCUt4ityt57ahXoEMW2AhNHo27V_wJmypgZshbw1w6345mKffaGtf9XowGOA'
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			

			$responseXML = simplexml_load_string($response);
			$responseJSON = json_encode($responseXML);

			// Convert into associative array
			$responseArray = json_decode($responseJSON, true);
			
		 	$currentScore = @$responseArray['get_player_scores']['player_scores']['player_score_0']['score'];
			
			//echo "<pre>";  print_r($responseArray); echo "</pre>";  
			//echo $currentScore;
			//die;
			
			//Get User last saved score
			$scoreInfo = $this->SITEDBAPI->getLiveTournamentPlayerScore($tournament_id, $userId);
			$lastScore = @$scoreInfo['player_score'];
			$player_id = @$scoreInfo['player_id'];
			
			//if($currentScore >= $lastScore){
			if($currentScore > $lastScore){
				$saveScore['player_score'] = $currentScore;
				$saveScore['player_score_updated'] = date('Y-m-d H:i:s');
				$this->db->where('player_id', $player_id);
				$this->db->update('tournaments_players', $saveScore);
			}
			
			
			if($redirect_path == 'redirect_leaderboard'){
				redirect('LiveTournamentLeaderboard/'.base64_encode($tournament_id));
			} else {
				
				redirect('LiveTournament/'.base64_encode($tournament_id));
			}
			
		} else {
			redirect('LiveTournament/'.base64_encode($tournament_id));
		}
	}



	public function updateLiveTournamentGameboostPlayerScore(){
		
		$userId = $this->session->userdata('userId');
		$tournament_id = @$_POST['tournament_id'];
		$game_id = @$_POST['game_id'];
		$skillpod_player_id = @$_POST['skillpod_player_id'];
		
		if(!empty($tournament_id) && !empty($game_id) && !empty($skillpod_player_id)){
			
			$tournamentInfo = $this->SITEDBAPI->getLiveTournamentInfo($tournament_id);
		
			$apiURL = "https://multiplayergameserver.com/xmlapi7/xmlapi.php?site_id=834&password=GiK2Xz9Ty&nocompress=true&action=get_player_scores&order_by_field=time&order_by_direction=DESC&game_id=".$game_id."&skillpod_player_id=".$skillpod_player_id."&show_games=false";
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiURL);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/xml'));
			$response = curl_exec($ch);
			curl_close($ch);

			$responseXML = simplexml_load_string($response);
			$responseJSON = json_encode($responseXML);

			// Convert into associative array
			$responseArray = json_decode($responseJSON, true);
			$userScore = @$responseArray['player_scores']['player_score_0']['score'];			
			$scoreDate = @$responseArray['player_scores']['player_score_0']['time'];
			$scoreDate = date('Y-m-d', strtotime($scoreDate));
		
			
			$t_start_date = $tournamentInfo['tournament_start_date'];
			$t_end_date = $tournamentInfo['tournament_end_date'];
			
			/*
			if($t_start_date <= $scoreDate &&  $t_end_date >= $scoreDate ){
			//if($scoreDateTime > $timenow_start &&  $scoreDateTime  < $timenow_end ){
				if(!empty($userScore)){
					$currentScore = $userScore;
				} else {
					$currentScore = 0;
				}
			} else {
				$currentScore = 0;
			}
			*/
			//echo "Current Score: ".$currentScore;
			
			$currentScore = $userScore;
			
			$scoreInfo = $this->SITEDBAPI->getLiveTournamentPlayerScore($tournament_id, $userId);
			$lastScore = @$scoreInfo['player_score'];
			$player_id = @$scoreInfo['player_id'];
			if($currentScore >= $lastScore){
				$saveScore['player_score'] = $currentScore;
				$this->db->where('player_id', $player_id);
				$this->db->update('tournaments_players', $saveScore);
			}
		}
	}
	
	
	public function getLiveTournamentLeaderboard($id){
		 $id = base64_decode($id); 
		//get loggedin  user id
		$userId = $this->session->userdata('userId');
		if(!empty($userId) && !empty($id) ){
		
			// Get tournament info
			$data['tournamentInfo'] = $this->SITEDBAPI->getLiveTournamentInfo($id);
			
			if(is_array($data['tournamentInfo']) && count($data['tournamentInfo'])>0){
			//echo $this->db->last_query(); die;
				$data['userInfo'] = $this->SITEDBAPI->getSiteUserDetail($userId);
			
				$data['playersInfo'] = $this->SITEDBAPI->getLiveTournamentPlayersListDESC($id);
				$data['checkPlayerEntry'] = $this->SITEDBAPI->checkLiveTournamentPlayer($userId, $id);
			
				$data['user_id'] = $userId;
				$data['tournament_id'] = $id;
				
				$myRank = 0;
				$myScore = 0;
				$iCount = 0;
				$totalPlayers =  count($data['playersInfo']);
				if(is_array($data['playersInfo']) && count($data['playersInfo'])>0){
					
					$highest_score = $data['playersInfo'][0]['player_score'];
					$rank = 1;
					
					foreach($data['playersInfo'] as $row){
						if($row['player_score'] >0){
							if($highest_score !=0 && $row['player_score'] == $highest_score){
								// don't change the rank for user
							} else {
								if($row['player_score'] > 0 ){
									$rank++;
								} 
							$highest_score = $row['player_score'];
							}
							
							if($row['user_id'] == $userId){
								$myRank = $rank;
								$myScore = $row['player_score'] ;
								break;
							}
							$iCount++;
						
						}
					}
				}
				
				$data['myRank'] = $myRank;
				$data['myScore'] = $myScore;
				if($totalPlayers > 0 && $myScore>0){ // check the join players number count
					$data['myRank'] = $myRank;
					$data['myScore'] = $myScore;
				} else {
					$data['myRank'] = 0;
					$data['myScore'] = 0;
				}
				$data['session_page_type']=7;
				$data['session_tournament_id']=$id;
				$data['session_game_id']=$data['tournamentInfo']['tournament_game_id'];
				$this->load->view('site/live_tournament_leaderboard', $data);
			
			} else {
				redirect();
			}
		} else {
			redirect();
		}
	}


	public function practiceTournamentGame($gameId, $tournamentId){
		$gameId = base64_decode($gameId); // gameboost game id
		$tournamentId = base64_decode($tournamentId); // gameboost game id
		
		if(!empty($gameId)){
			$data['gameId'] = $gameId;
			$data['gameInfo'] = $this->SITEDBAPI->getGameboostGameInfo($gameId);
			
			// Update the Report for the user wise practice game play count
			$this->addReportUserGamePlay($type=3, $gameId, $tournamentId);
			$data['session_page_type']=3;
			$data['session_tournament_id']=$tournamentId;
			$data['session_game_id']=$data['gameInfo']['gid'];
			$this->load->view('site/play_game', $data);
		} else {
			redirect();
		}
	}

		
	public function addReportUserGamePlay($type, $gameId, $tournamentId='0'){
		
		$userId = $this->session->userdata('userId');
		if(!empty($userId)){
			if($type == '1'){
				// Update the genre practice game count
				$lastPracticeSession =  $this->SITEDBAPI->getUserPracticeGameReport($userId, $gameId);
				if(!empty($lastPracticeSession['report_id'])){
					$reportData['report_practice_counts'] = ($lastPracticeSession['report_practice_counts']+1);
					$this->db->where('report_id', $lastPracticeSession['report_id']);
					$this->db->update('report_game_play', $reportData);
				} else {
					$reportData['report_user_id'] = $userId;
					$reportData['report_date'] = date('Y-m-d');
					$reportData['report_game_id'] = $gameId;
					$reportData['report_practice_counts'] = '1';
					$reportData['report_last_updated'] = time();
					$this->db->insert('report_game_play', $reportData);
				}
				
			} else if($type == '2'){
				
				if(!empty($tournamentId)){
					// Update the Tournament practice game count
					$lastPracticeSession =  $this->SITEDBAPI->getUserTournamentGameReport($userId, $gameId, $tournamentId);
					
					if(!empty($lastPracticeSession['report_id'])){
						$reportData['report_tournament_counts'] = ($lastPracticeSession['report_tournament_counts']+1);
						$this->db->where('report_id', $lastPracticeSession['report_id']);
						$this->db->update('report_game_play', $reportData);
					} else {
						$reportData['report_user_id'] = $userId;
						$reportData['report_date'] = date('Y-m-d');
						$reportData['report_game_id'] = $gameId;
						$reportData['report_tournament_id'] = $tournamentId;
						$reportData['report_tournament_counts'] = '1';
						$reportData['report_last_updated'] = time();
						$this->db->insert('report_game_play', $reportData);
					}
				} 
			} else if($type == '3'){
				
				if(!empty($tournamentId)){
					// Update the Tournament practice game count
					$lastPracticeSession =  $this->SITEDBAPI->getUserTournamentGameReport($userId, $gameId, $tournamentId);
					
					if(!empty($lastPracticeSession['report_id'])){
						$reportData['report_tournament_practice_counts'] = ($lastPracticeSession['report_tournament_practice_counts']+1);
						$this->db->where('report_id', $lastPracticeSession['report_id']);
						$this->db->update('report_game_play', $reportData);
					} else {
						$reportData['report_user_id'] = $userId;
						$reportData['report_date'] = date('Y-m-d');
						$reportData['report_game_id'] = $gameId;
						$reportData['report_tournament_id'] = $tournamentId;
						$reportData['report_tournament_practice_counts'] = '1';
						$reportData['report_last_updated'] = time();
						$this->db->insert('report_game_play', $reportData);
					}
				} 
			} 
			
		}
	}
	
	
	
	public function tournamentHistory()	{
		
		$userId = $this->session->userdata('userId');
		if(!empty($userId)){
			
			$data['userInfo'] = $this->SITEDBAPI->getSiteUserDetail($userId);
			$data['tournamentsList'] = $this->SITEDBAPI->getUserTournamentsList($userId);
			$data['session_page_type']=9;
			$this->load->view('site/tournament_history',$data);
		} else {
			redirect();
		}
	}


	
	public function tournamentLeaderboard($id){
		 $id = base64_decode($id); 
		//get loggedin  user id
		$userId = $this->session->userdata('userId');
		if(!empty($userId) && !empty($id) ){
		
			// Get tournament info
			$data['tournamentInfo'] = $this->SITEDBAPI->getTournamentInfo($id);
			
			if(is_array($data['tournamentInfo']) && count($data['tournamentInfo'])>0){
			//echo $this->db->last_query(); die;
				
				// $today = date('Y-m-d H:i:s');
				$today = time();

				$startDate = $data['tournamentInfo']['t_start_date']." ".$data['tournamentInfo']['t_start_time'].":00";
				$startDate = strtotime($startDate);

				$endDate = $data['tournamentInfo']['t_end_date']." ".$data['tournamentInfo']['t_end_time'].":00";
				$endDate = strtotime($endDate);

				$status = 0;     //1=CurrentlyWorking   2=Expired   3=futureTournament
				if($startDate > $today){
					$status = 3;
				} else if($endDate < $today){
					$status = 2;
				} else if($startDate <= $today && $endDate >= $today){
					$status = 1;
				}
				$data['t_current_status'] = $status;
				$data['user_id'] = $userId;
				$data['tournament_id'] = $id;
				$data['playersInfo'] = $this->SITEDBAPI->getTournamentPlayersListDESC($id);

				$this->load->view('site/tournament_leaderboard', $data);
			
			
			} else {
				redirect();
			}
		} else {
			redirect();
		}
	}


	
	
	
// *****************************   *************************************** ********************************** //
// *****************************   Live  VIP/Pay&Play/Free Tournaments Ends Here ********************************** //
// *****************************   *************************************** ********************************** //


	
// *****************************   **************************** ********************************** //
// *****************************   Free Genre Games Starts Here ********************************** //
// *****************************   **************************** ********************************** //

	public function getGenreGames($type){
		$data['gamesList'] = $this->SITEDBAPI->getGenreGamesList($type);
		$data['type'] = $type;		
		if($type == 'Action')
			$data['genreName'] = 'Action';
		else if($type == 'Arcade')
			$data['genreName'] = 'Arcade';
		else if($type == 'Adventure')
					$data['genreName'] = 'Adventure';
		else if($type == 'Sports')
					$data['genreName'] = 'Sports & Racing';
		else if($type == 'Puzzle')
			$data['genreName'] = 'Puzzle & Logic';
	
		$this->load->view('site/genre_games_list', $data);	
		
	}
	
	public function playGame($id){
		$id = base64_decode($id); // gameboost game id
		
		if(!empty($id)){
			$data['gameId'] = $id;
			$data['gameInfo'] = $this->SITEDBAPI->getGameboostGameInfo($id);
			
			// Update the Report for the user wise practice game play count
			$this->addReportUserGamePlay($type=1, $id);
			
			$data['session_page_type']=5;
			$data['session_game_id']=$data['gameInfo']['gid'];
			$this->load->view('site/play_game', $data);
		} else {
			redirect();
		}
	}

	
// *****************************   **************************** ********************************** //
// *****************************   Free Genre Games Ends Here ********************************** //
// *****************************   **************************** ********************************** //



	
// *****************************   **************************** ********************************** //
// *****************************   Manage Profile Avatars Starts Here ********************************** //
// *****************************   **************************** ********************************** //

		
		public function manageProfile(){
		$userId = $this->session->userdata('userId');
		$userInfo = $this->SITEDBAPI->validateUser($userId);
		if( !empty($userInfo['user_id'])){	
			$data['userInfo'] = $userInfo;
			$data['session_page_type']=6;
			$this->load->view('site/manage_profile', $data);	
			
		} else {
			redirect('Login');
		}
	}
	
	public function updateUserProfile(){
		$userId = $this->session->userdata('userId');
		$userInfo = $this->SITEDBAPI->validateUser($userId);
		if( !empty($userInfo['user_id'])){	
			
			$update['user_full_name'] = $_POST['user_full_name'];
			$update['user_email'] = $_POST['user_email'];
			if($_POST['user_email'] != $userInfo['user_email']){
				$otp = $this->createOTP(6);
				$update['user_email_verified'] = 0;
				$update['user_email_otp'] = $otp;
			} else {
				$otp = $this->createOTP(6);
				$update['user_email_verified'] = $userInfo['user_email_verified'];
				$update['user_email_otp'] = $otp;
			}
			
			$this->db->where('user_id', $userId);
			if($this->db->update('site_users', $update)){
				
				if($update['user_email_verified'] == 0){
					$this->sendEmailVerificationOTP($update['user_email'], $update['user_email_otp']);
				} else {
					
					//Save User Notification
					$notifyDesc = "Profile information updated successfully.";
					$this->saveUserNotification($type='5', $notifyDesc);
				
				
					$this->session->set_flashdata("success","Profile information updated successfully.");
					redirect('ManageProfile');
				}
				
				
			} else {
				$this->session->set_flashdata("error","Sorry! Unable to update profile information. Please try after sometime.");
				redirect('ManageProfile');
				
			}
		} else {
			redirect('Login');
		}
	}
	
	public function sendEmailVerificationOTP($email, $emailOTP){
		
		if(!empty($email)){

			// Sanitize E-mail Address
			$email =filter_var($email, FILTER_SANITIZE_EMAIL);
			// Validate E-mail Address
			$email= filter_var($email, FILTER_VALIDATE_EMAIL);
			if($email){

					$otp = $emailOTP;

					$row['content']='';
					$row['content'] .= "<p> <br> <b>{$otp}</b> is the One Time Password (OTP) to verify your email address. Do not share the OTP with anyone. </p>";
					$row['content'] .= "<p><br><br> <b>IMPORTANT</b>: Please do not reply to this message or mail address.</p>";
					$row['content'] .= "<p><b>DISCLAIMER</b>: This communication is confidential and privileged and is directed to and for the use of the addressee only. The recipient if not the addressee should not use this message if erroneously received, and access and use of this e-mail in any manner by anyone other than the addressee is unauthorized.</p>";
				
					$row['subject'] = "Your GSL Email Verification OTP";
					
					
				// Enable this when shift to live server
					
					$this->load->library("PhpMailerLib");
					$mail = $this->phpmailerlib->load();
					
					try {
						//Server settings
						$mail->SMTPDebug = 0;                                 // Enable verbose debug output
						$mail->isSMTP();                                      // Set mailer to use SMTP
						$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
						$mail->SMTPAuth = true;                               // Enable SMTP authentication
					//	$mail->Username = 'gpl.gamenow@gmail.com';                 // SMTP username
					//	$mail->Password = 'gpl@123*';                           // SMTP password
						$mail->Username = 'adxdigitalsg@gmail.com';                 // SMTP username
						$mail->Password = 'adxd@123';                           // SMTP password
						$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
						$mail->Port = 465;                                    // TCP port to connect to
						//Recipients
						$mail->setFrom('adxdigitalsg@gmail.com', 'GSL');
						$mail->addAddress($email);     // Add a recipient
						$mail->addReplyTo('adxdigitalsg@gmail.com', 'GSL');
						$mail->addBCC('vaish.nisha55@gmail.com');

						//Content
						$mail->isHTML(true);                                  // Set email format to HTML
						$mail->Subject = $row['subject'];
						$mail->Body    = $row['content'];
					
						$mail->send();
						$this->session->set_flashdata('otp_success','<strong>Success! </strong> OTP request sent on the specified email address.');

						$this->session->set_userdata('person_verify_email', $email);
						$this->session->set_userdata('person_verify_otp', $otp);
						redirect('ManageProfile');
						
					} catch (Exception $e) {
						$this->session->set_flashdata('error','<strong>Error! </strong> Unable to send OTP request to your specified email address. Please try again.');
					
						redirect("ManageProfile");
					}
					
					
			}	else {
				redirect();
			}
		} else {
			redirect();
		}
	}
	
	public function processEmailVerification(){
		$userId = $this->session->userdata('userId');
		$userInfo = $this->SITEDBAPI->validateUser($userId);
		if( !empty($userInfo['user_id'])){	
			
			$email_otp = $_POST['email_otp'];
			
			if($userInfo['user_email_otp'] ==  $email_otp){
				
				$update['user_email_verified'] = 1;
				$this->db->where('user_id', $userId);
				if($this->db->update('site_users', $update)){
					
					//Save User Notification
					$notifyDesc = "Email address verified successfully.";
					$this->saveUserNotification($type='5', $notifyDesc);
				
				
					$this->session->set_flashdata("success","Email address verified successfully.");
					redirect('ManageProfile');
				
				} else {
					$this->session->set_flashdata("error","Sorry! Unable to verify OTP. Please try again.");
					redirect('ManageProfile');
				}
			} else {
				$this->session->set_flashdata("error","Sorry! Unable to verify OTP. Please try again.");
				redirect('ManageProfile');
			}
		} else {
			redirect('Login');
		}
	}
	
	public function updateProfileImage(){
		$data['maleList'] = $this->SITEDBAPI->getMaleProfileImages();
		$data['femaleList'] = $this->SITEDBAPI->getFemaleProfileImages();
		$this->load->view('site/user_images_list', $data);	
		
	}
	
	public function setProfileImage($imgId){
		$userId = $this->session->userdata('userId');		
		$imgId = base64_decode($imgId);
		$dataUser['user_image'] = $imgId.".png";
		$this->db->where('user_id', $userId);
		if($this->db->update('site_users', $dataUser)){
			
			//Save User Notification
			$notifyDesc = "Profile image updated successfully.";
			$this->saveUserNotification($type='5', $notifyDesc);
				
			
			$this->session->set_flashdata("success","Profile image updated successfully.");
			redirect();
		} else {
			$this->session->set_flashdata("error","Unable to update profile image. Please try again.");
			redirect();
		}
		
	}
	
	
// *****************************   **************************** ********************************** //
// *****************************   Manage Profile  Ends Here ********************************** //
// *****************************   **************************** ********************************** //


	
// *****************************   **************************** ********************************** //
// *****************************   Manage Spin & Win  Starts Here ********************************** //
// *****************************   **************************** ********************************** //

	public function spinWheel(){
		$userId = $this->session->userdata('userId');
		$userInfo = $this->SITEDBAPI->validateUser($userId);
		if( !empty($userInfo['user_id'])){	
			
			$userSpinInfo = $this->SITEDBAPI->getUserLastSpin($userId);
			if($userSpinInfo)
			{
				$spinDate = $userSpinInfo['spin_date'];
				$spinDate = strtotime($spinDate);
				$today = strtotime(date('Y-m-d'));
				if($today !== @$spinDate){
					$data['session_page_type']=8;
					$this->load->view('site/spin.php', $data);	
				} else {
					$this->session->set_flashdata("error","Sorry, You already used Spin & Win today.  Come back tomorrow to spin again.");
					redirect();
				}
			}
			else
			{
				$data['session_page_type']=8;
				$this->load->view('site/spin.php', $data);	
			}
				
			// print_r(empty($userSpinInfo));
			// die();
			// if(!empty($userSpinInfo))
			// {
			// 	$this->load->view('site/spin.php');	
			// }
			
		} else {
			redirect('Login');
		}
	}
	
	
	public function getSpinJSON(){
		
		header('Content-type: application/json');
		
		$list  =  $this->SITEDBAPI->getSpinWheelSections();
		$spinArray = array();
		
		foreach($list as $row){
			$aa = array();
			$aa['id'] = $row['wheel_id'];
			$aa['type'] = "string";
			$aa['win'] = true;
			
			if( $row['wheel_type'] == 1){
				 $str = " COINS";
			}  else if( $row['wheel_type'] == 2){
				 $str = " MB DATA";
			}  else if( $row['wheel_type'] == 3){
				 $str = " GB DATA";
			}  else if( $row['wheel_type'] == 4){
				 $str = " RS. TALKTIME ";
			}
			
			$aa['value'] = $row['wheel_value'].$str;
			$aa['resultText'] = "YOU WON ".$row['wheel_value'].$str;
			array_push($spinArray, $aa);
		}
		
		$data = array(
		"colorArray" => array("#364C62", "#F1C40F", "#E67E22", "#E74C3C", "#98985A", "#95A5A6", "#16A085", "#27AE60", "#2980B9", "#8E44AD", "#2C3E50", "#F39C12", "#D35400", "#C0392B", "#BDC3C7","#1ABC9C", "#2ECC71", "#E87AC2", "#3498DB", "#9B59B6", "#7F8C8D"),

		"segmentValuesArray" => $spinArray,
		"svgWidth" => 1024,
		"svgHeight" => 768,
		"wheelStrokeColor" => "#3fd53f",
		"wheelStrokeWidth" => 18,
		"wheelSize" => 700,
		"wheelTextOffsetY" => 80,
		"wheelTextColor" => "#EDEDED",
		"wheelTextSize" => "2.2em",
		"wheelImageOffsetY" => 40,
		"wheelImageSize" => 50,
		"centerCircleSize" => 250,
		"centerCircleStrokeColor" => "#ffffff",
		"centerCircleStrokeWidth" => 12,
		"centerCircleFillColor" => "#efefef",
		"segmentStrokeColor" => "#E2E2E2",
		"segmentStrokeWidth" => 5,
		"centerX" => 512,
		"centerY" => 384,  
		"hasShadows" => false,
		"numSpins" => 1 ,
		"spinDestinationArray" => array(),
		"minSpinDuration" => 6,
		"gameOverText" => "COME BACK TOMORROW TO PLAY AGAIN!",
		"invalidSpinText" =>"INVALID SPIN. PLEASE SPIN AGAIN.",
		"introText" => "CLICK TO SPIN IT! ",
		"hasSound" => true,
		"gameId" => "9a0232ec06bc431114e2a7f3aea03bbe2164f1aa",
		"clickToSpin" => true

		);

		echo json_encode( $data);
	}
	
	
	public function processSpinWin($id){
		$userId = $this->session->userdata('userId');
		$userInfo = $this->SITEDBAPI->validateUser($userId);
		
		if( !empty($userInfo['user_id'])){	
			
			// Get Spin Wheel section info 
			$spinInfo = $this->SITEDBAPI->getSpinWheelInfo($id);
			
			$data['spin_user_id'] = $userId;
			$data['spin_date'] = date('Y-m-d');
			if($spinInfo['wheel_type'] == 1){
				$data['spin_reward'] = '1';  // 1=WinCoin   0=NoCoin
				$data['spin_coins'] = $spinInfo['wheel_value'];
			} else {
				$data['spin_reward'] = '0';
				$data['spin_coins'] = '0';
			}
				
			$data['spin_reward_type'] = $spinInfo['wheel_type'];  // 1=PlayCoins   2=Data-MB  3=Data-GB  4=TalkTime
			$data['spin_reward_value'] = $spinInfo['wheel_value'];  
			
			
			$data['spin_added_on'] = time();
			if($this->db->insert('user_spinwin', $data)){
				
				if($spinInfo['wheel_type'] == 1){
					// If User Wins Play Coins,   then Update the Play Coins in main Users table
					$playCoins = $userInfo['user_play_coins'];
					$updatedPlayCoins = $playCoins+$spinInfo['wheel_value'];
					$dataCoins['user_play_coins'] = $updatedPlayCoins;
					$this->db->where('user_id', $userId);
					$this->db->update('site_users', $dataCoins);
				}
				
				// Update a row for managing coins history				
				$coinHistory['coin_user_id'] = $userId;
				$coinHistory['coin_date'] = date('Y-m-d');				
				$coinHistory['coin_section'] = '2';  //1=AddCoins  2=SpinWin  3=RedeemRewardCoins  4=CreateTournament  5=TournamentReward
				
				// Create User Notificatio desc parameter
				$notifyDesc = '';
						
				if($spinInfo['wheel_type'] == 1){
					$coinHistory['coin_play_coins_add'] = $spinInfo['wheel_value'];
					$coinHistory['coin_type'] = '1';  // 1=PlayCoins  2=RewardCoins  3=Both
					
					$notifyDesc = "<b>{$spinInfo['wheel_value']} Coins</b> added to your account.";
					
				} else if($spinInfo['wheel_type'] == 2){
					$coinHistory['coin_data_pack_value'] = $spinInfo['wheel_value'];
					$coinHistory['coin_data_pack_unit'] = 'MB';
					$coinHistory['coin_type'] = '0';  
					
					$notifyDesc = "<b>{$spinInfo['wheel_value']} MB </b> data added to your account.";
				
				} else if($spinInfo['wheel_type'] == 3){
					$coinHistory['coin_data_pack_value'] = $spinInfo['wheel_value'];
					$coinHistory['coin_data_pack_unit'] = 'GB';
					$coinHistory['coin_type'] = '0'; 
					
					$notifyDesc = "<b>{$spinInfo['wheel_value']} GB </b> data added to your account.";
				
				} else if($spinInfo['wheel_type'] == 4){
					$coinHistory['coin_talk_time_value'] = $spinInfo['wheel_value'];
					$coinHistory['coin_type'] = '0'; 
					
					$notifyDesc = "<b> {$spinInfo['wheel_value']} Rs. </b> recharge done on your account.";
				}
				$coinHistory['coin_added_on'] = time();
				$this->db->insert('user_coins_history', $coinHistory);
				
				//Save User Notification
				$this->saveUserNotification($type='2', $notifyDesc);
				
				
				//$this->session->set_flashdata("success","$coins coins added to your account successfully.");
				redirect();
			} else {
				$this->session->set_flashdata("error","Something went wrong. Please try again after sometime.");
				redirect();
			}
			
		} else {
			redirect('Login');
		}
	}
	
	public function processSpinWinAjax($id){
		$userId = $this->session->userdata('userId');
		$userInfo = $this->SITEDBAPI->validateUser($userId);
		
		if( !empty($userInfo['user_id'])){	
			
			// Get Spin Wheel section info 
			$spinInfo = $this->SITEDBAPI->getSpinWheelInfo($id);
			
			$data['spin_user_id'] = $userId;
			$data['spin_date'] = date('Y-m-d');
			if($spinInfo['wheel_type'] == 1){
				$data['spin_reward'] = '1';  // 1=WinCoin   0=NoCoin
				$data['spin_coins'] = $spinInfo['wheel_value'];
			} else {
				$data['spin_reward'] = '0';
				$data['spin_coins'] = '0';
			}
				
			$data['spin_reward_type'] = $spinInfo['wheel_type'];  // 1=PlayCoins   2=Data-MB  3=Data-GB  4=TalkTime
			$data['spin_reward_value'] = $spinInfo['wheel_value'];  
			
			
			$data['spin_added_on'] = time();
			if($this->db->insert('user_spinwin', $data)){
				
				if($spinInfo['wheel_type'] == 1){
					// If User Wins Play Coins,   then Update the Play Coins in main Users table
					$playCoins = $userInfo['user_play_coins'];
					$updatedPlayCoins = $playCoins+$spinInfo['wheel_value'];
					$dataCoins['user_play_coins'] = $updatedPlayCoins;
					$this->db->where('user_id', $userId);
					$this->db->update('site_users', $dataCoins);
				}
				
				// Update a row for managing coins history				
				$coinHistory['coin_user_id'] = $userId;
				$coinHistory['coin_date'] = date('Y-m-d');				
				$coinHistory['coin_section'] = '2';  //1=AddCoins  2=SpinWin  3=RedeemRewardCoins  4=CreateTournament  5=TournamentReward
				
				// Create User Notificatio desc parameter
				$notifyDesc = '';
				
				
				if($spinInfo['wheel_type'] == 1){
					$coinHistory['coin_play_coins_add'] = $spinInfo['wheel_value'];
					$coinHistory['coin_type'] = '1';  // 1=PlayCoins  2=RewardCoins  3=Both
					
					$notifyDesc = "<b>{$spinInfo['wheel_value']} Coins</b> added to your account.";
					
				} else if($spinInfo['wheel_type'] == 2){
					$coinHistory['coin_data_pack_value'] = $spinInfo['wheel_value'];
					$coinHistory['coin_data_pack_unit'] = 'MB';
					$coinHistory['coin_type'] = '0';  
					
					$notifyDesc = "<b>{$spinInfo['wheel_value']} MB </b> data added to your account.";
					
				} else if($spinInfo['wheel_type'] == 3){
					$coinHistory['coin_data_pack_value'] = $spinInfo['wheel_value'];
					$coinHistory['coin_data_pack_unit'] = 'GB';
					$coinHistory['coin_type'] = '0';
	
					$notifyDesc = "<b>{$spinInfo['wheel_value']} GB </b> data added to your account.";
					
				} else if($spinInfo['wheel_type'] == 4){
					$coinHistory['coin_talk_time_value'] = $spinInfo['wheel_value'];
					$coinHistory['coin_type'] = '0'; 
					
					$notifyDesc = "<b> {$spinInfo['wheel_value']} Rs. </b> recharge done on your account.";
					
				}
				$coinHistory['coin_added_on'] = time();
				$this->db->insert('user_coins_history', $coinHistory);
				
				
				//Save User Notification
				$this->saveUserNotification($type='2', $notifyDesc);
				
				
				echo "success";
				
			} else {
				$this->session->set_flashdata("error","Something went wrong. Please try again after sometime.");
				redirect();
			}
			
		} else {
			redirect('Login');
		}
	}
	
// *****************************   **************************** ********************************** //
// *****************************   Manage Spin & Win  Ends Here ********************************** //
// *****************************   **************************** ********************************** //



	
// *****************************   **************************** ********************************** //
// *****************************   Manage Notifications Starts Here ********************************** //
// *****************************   **************************** ********************************** //
	public function manageNotifications(){
		$userId = $this->session->userdata('userId');
		if(!empty($userId) ){
			$updateRead['notify_status'] = '1';
			$this->db->where('notify_user_id', $userId);
			$this->db->update('user_notifications', $updateRead);
			$data['list'] = $this->SITEDBAPI->getUserNotifications($userId);
			$data['session_page_type']=14;
			$this->load->view('site/notifications', $data);
		} else {
			redirect('Login');
		}
	}
		
	
	public function deleteNotification($id){
		$userId = $this->session->userdata('userId');
		if(!empty($userId) ){
			$notify_id =base64_decode($id);
			
			$this->db->where('notify_id', $notify_id);
			$this->db->where('notify_user_id', $userId);
			if($this->db->delete('user_notifications')){
				redirect('Notifications');
			} else {
				redirect('Notifications');
			}
			
		} else {
			redirect('Login');
		}
	}
		
	
	public function clearNotifications(){
		$userId = $this->session->userdata('userId');
		if(!empty($userId) ){
			
			$this->db->where('notify_user_id', $userId);
			if($this->db->delete('user_notifications')){
				
				redirect('Notifications');
			} else {
				redirect('Notifications');
			}
			
		} else {
			redirect('Login');
		}
	}
		
	
	public function saveUserNotification($type, $notifyDesc='', $winner_user_id =''){
		$userId = $this->session->userdata('userId');
		if(!empty($userId) &&  !empty($type)){
			
			if($type == '1'){
				// Tournament Created
				$notifyData['notify_user_id'] =  $userId;
				$notifyData['notify_type'] =  '1';
				$notifyData['notify_title'] =  "Tournament Created";
				$notifyData['notify_desc'] =  "<b>100 Coins</b> deducte from your account for creating new tournament.";
				$notifyData['notify_status'] =  '0';
				$notifyData['notify_date'] =  date('Y-m-d');
				$notifyData['notify_added_on'] =  time();
				
				$this->db->insert('user_notifications', $notifyData);
				
			} else if($type == '2'){
				
				// Spin & Win Reward 
				$notifyData['notify_user_id'] =  $userId;
				$notifyData['notify_type'] =  '2';
				$notifyData['notify_title'] =  "Spin & Win Reward";
				$notifyData['notify_desc'] =  $notifyDesc;
				$notifyData['notify_status'] =  '0';
				$notifyData['notify_date'] =  date('Y-m-d');
				$notifyData['notify_added_on'] =  time();
				
				$this->db->insert('user_notifications', $notifyData);
				
			} else if($type == '3'){
				
				// Spin & Win Reward 
				$notifyData['notify_user_id'] =  $userId;
				$notifyData['notify_type'] =  '3';
				$notifyData['notify_title'] =  "Redeem Coins";
				$notifyData['notify_desc'] =  $notifyDesc;
				$notifyData['notify_status'] =  '0';
				$notifyData['notify_date'] =  date('Y-m-d');
				$notifyData['notify_added_on'] =  time();
				
				$this->db->insert('user_notifications', $notifyData);
			
			} else if($type == '4'){
				if(!empty($winner_user_id)){
					// Tournament Reward 
					$notifyData['notify_user_id'] =  $winner_user_id;
					$notifyData['notify_type'] =  '4';
					$notifyData['notify_title'] =  "Tournament Reward";
					$notifyData['notify_desc'] =  $notifyDesc;
					$notifyData['notify_status'] =  '0';
					$notifyData['notify_date'] =  date('Y-m-d');
					$notifyData['notify_added_on'] =  time();
					
					$this->db->insert('user_notifications', $notifyData);
				}
				
			} else if($type == '5'){
				
				// Update profile name or email
				$notifyData['notify_user_id'] =  $userId;
				$notifyData['notify_type'] =  '5';
				$notifyData['notify_title'] =  "Profile Settings";
				$notifyData['notify_desc'] =  $notifyDesc;
				$notifyData['notify_status'] =  '0';
				$notifyData['notify_date'] =  date('Y-m-d');
				$notifyData['notify_added_on'] =  time();
				
				$this->db->insert('user_notifications', $notifyData);
				
			} else if($type == '6'){
				
				// Update profile verify email
				$notifyData['notify_user_id'] =  $userId;
				$notifyData['notify_type'] =  '6';
				$notifyData['notify_title'] =  "Profile Settings";
				$notifyData['notify_desc'] =  $notifyDesc;
				$notifyData['notify_status'] =  '0';
				$notifyData['notify_date'] =  date('Y-m-d');
				$notifyData['notify_added_on'] =  time();
				
				$this->db->insert('user_notifications', $notifyData);
				
			}
			
		} 
		
	}
	
// *****************************   **************************** ********************************** //
// *****************************   Manage Notifications Ends Here ********************************** //
// *****************************   **************************** ********************************** //


	public function captureTimeToLeave(){
		$userId = $this->session->userdata('userId');
		$paymayaProfileId = $this->session->userdata('paymayaProfileId');
		// echo $session_page = $_POST['session_page'];
		$session_page=$_POST['session_page'];
		// die();
		$time = $_POST['time'];
		$endTime =$_POST['endTime'];
		$timeSpend = $this->time2sec($time);
		
		// Used in case of practice game play
		$game_id = @$_POST['game_id'];  // This is gameboost game id
		
		// Used in case of tournament game play
		$tournament_id = @$_POST['tournament_id'];
		
		$lastLoginSession =  $this->SITEDBAPI->getUserLastLoginReport($userId, $session_page, $game_id , $tournament_id);
		// echo "<pre>";
		// print_r($lastLoginSession);
		// die();
		if($lastLoginSession)
		{
			if($lastLoginSession['report_page'] == $session_page){
				// echo "mani";
				if($timeSpend > $lastLoginSession['report_avg_time']){
					$reportUser['report_avg_time'] = $timeSpend;
				}
				$reportUser['report_logout_time'] = date('Y-m-d H:i:s', strtotime($endTime));
				$reportUser['report_page'] = $session_page;
				
				echo  $lastLoginSession['report_id'];
				
				$this->db->where('report_id', $lastLoginSession['report_id']);
				$this->db->update('report_users', $reportUser);
			
			}
	 	} else {
			// echo "anc";
			$reportUser['report_user_id'] = $userId;
			// $reportUser['report_paymaya_id'] = 1;
			$reportUser['report_date'] = date('Y-m-d');
			$reportUser['report_login_time'] = date('H:i:s A');
			$reportUser['report_logout_time'] = date('Y-m-d H:i:s', strtotime($endTime));
			$reportUser['report_tournament_id'] = $tournament_id;
			$reportUser['report_game_id'] = $game_id;
			$reportUser['report_avg_time'] = $timeSpend;
			$reportUser['report_page'] = $session_page;  //1=Home-Page  2=Live Tournament Info  3=Practice-Game-Play  4=Tournament-Detail  5=Tournament-Game-Play  6=Tournament-Practice-Game-Play  7=User-Profile  8=Tournaments-History
			$reportUser['report_last_updated'] = time();
			$this->db->insert('report_users', $reportUser);
		}
		
		echo "Saved: ".$timeSpend;
		
	}

	public function time2sec($time)
	{
		$durations = array_reverse(explode(':', $time));
		$second = array_shift($durations);
		foreach ($durations as $duration) {
			$second += (60 * $duration);
		}
		return $second;
	}

	
// *****************************   **************************** ********************************** //
// *****************************   Custom Tournaments Starts Here ********************************** //
// *****************************   **************************** ********************************** //


	public function createTournament()	{
		$this->load->view('site/create_tournament');
	}
	public function tournamentStep1()	{
		$userId = $this->session->userdata('userId');
		$userInfo = $this->SITEDBAPI->validateUser($userId);
		$data['gamesList'] = $this->SITEDBAPI->getPublishedPTGames();
		if(!empty($userInfo['user_id']) && $userInfo['user_subscription']==0)
		{
			if($userInfo['user_free_custom_tournaments']>0)
			{
				
				$this->load->view('site/tournament_step_1', $data);	
			}
			
			else
			{
				$this->session->set_flashdata('less_custom_tournament' , "Sorry ,You are not a VIP user. So,  you can not create more tournament. ");
				redirect();
			}
		}
		else if( !empty($userInfo['user_id']) && $userInfo['user_play_coins'] > 100){
		
			$this->load->view('site/tournament_step_1', $data);
		} else {
			$this->session->set_flashdata("less_play_coins","Sorry, You don't have enough play coins to create a new tournament.");
			redirect();
		}
	}

	public function searchGameByName()	{
		$txt = $_POST['txt'];
		$data['gamesList'] = $this->SITEDBAPI->searchPTGameByName($txt);
		$this->load->view('site/tournament_search_result', $data);
	}

	public function tournamentStep2($gameId){
		$gameId =  base64_decode($gameId);
		$this->session->set_userdata('gameId', $gameId);
		$data['gameId'] = $gameId;
		$data['gameInfo'] = $this->SITEDBAPI->getGameInfo($gameId);
		$data['session_game_id']=$data['gameInfo']['gid'];
		$this->load->view('site/tournament_step_2', $data);
	}

	public function saveTournamentTimings(){
		$start_day =  $_POST['start_day'];
		$start_date = date('Y-m-d');
		if($start_day == 'tomorrow'){
			$start_date = date("Y-m-d", strtotime("+ 1 day"));
		}

		$this->session->set_userdata('startDay', $_POST['start_day']);
		$this->session->set_userdata('startDate', $start_date);
		$this->session->set_userdata('duration', $_POST['duration']);
		$this->session->set_userdata('exactHour', $_POST['exact_hour']);
		$this->session->set_userdata('exactMinutes', $_POST['exact_minutes']);
		$this->session->set_userdata('exactAmpm', $_POST['exact_ampm']);
		echo 'success';
	}

	public function tournamentStep3($gameId){
		$gameId =  base64_decode($gameId);
		$data['gameId'] = $gameId;
		$data['start_day'] = $this->session->userdata('startDay');
		$data['start_date'] = $this->session->userdata('startDate');
		$data['duration'] = $this->session->userdata('duration');
		$data['exact_hour'] = $this->session->userdata('exactHour');
		$data['exact_minutes'] = $this->session->userdata('exactMinutes');
		$data['exact_ampm'] = $this->session->userdata('exactAmpm');
		$data['gameInfo'] = $this->SITEDBAPI->getGameInfo($gameId);

		/* Get tournament timings */
		 	$startDate = $this->session->userdata('startDate');

			$duration = $this->session->userdata('duration');
			$exactHour = $this->session->userdata('exactHour');
			$exactMinutes = $this->session->userdata('exactMinutes');
		 	$exactAmpm = $this->session->userdata('exactAmpm');

			$timings = $this->calculateTournamentTimings($startDate, $duration, $exactHour, $exactMinutes, $exactAmpm);
			$data['t_end_date'] = $timings['end_date'];
			$data['t_end_time'] = $timings['end_time'];

		/* Get tournament timings ends */
		$this->load->view('site/tournament_step_3', $data);
	}

	

	public function saveTournamentDetails(){


		$userId = $this->session->userdata('userId');
		if(!empty($userId))
		{
		// $result=$this->SITEDBAPI->getSiteUserDetail($userId);
		$gameId = $this->session->userdata('gameId');
		$startDay = $this->session->userdata('startDay');
		$startDate = $this->session->userdata('startDate');
		$duration = $this->session->userdata('duration');
		$exactHour = $this->session->userdata('exactHour');
		$exactMinutes = $this->session->userdata('exactMinutes');
		$exactAmpm = $this->session->userdata('exactAmpm');
		$userInfo = $this->SITEDBAPI->validateUser($userId);
		$playersCount = $_POST['players_count'];
		$entryFee = $_POST['entry_fee'];
		$prizeType = $_POST['prize_type'];
		$this->session->set_userdata('playersCount' ,$playersCount);
		$this->session->set_userdata('entryFee' ,$entryFee);
		$this->session->set_userdata('prizeType' ,$prizeType);
		if($userInfo['user_subscription']!=1)
			{
				if($userInfo['user_free_custom_tournaments']>0)
				{
					$free_tournament = $userInfo['user_free_custom_tournaments'];
					$tournament['user_free_custom_tournaments'] = ($free_tournament - 1);
					$this->db->where('user_id', $userId);
					$this->db->update('site_users', $tournament);
				}
				else{
					$this->session->set_userdata('user_tournament' , true);
					$redirectLink = site_url()."UserSubscription";
					return print_r($redirectLink);
				}
			}
		// Get the game info from master table
		$gameInfo = $this->SITEDBAPI->getGameInfo($gameId);
		$gplGameId = $gameInfo['id'];
		$gplGameName = $gameInfo['Name'];
		$gplGameImage = $gameInfo['GameImage'];
		$gplGameCategory = $gameInfo['Category'];
		$gplGameScreen = $gameInfo['screen'];

		// Calculate the timings of the tournament
		$timings = $this->calculateTournamentTimings($startDate, $duration, $exactHour, $exactMinutes, $exactAmpm);


		//Set start time in 24 hour format for tournament start time
		if($exactAmpm == 'PM' && $exactHour == 12)
			$exactHour= $exactHour;
		else if($exactAmpm == 'PM')
			$exactHour= $exactHour+12;


		$save['t_user_id'] = $userId;
		$save['t_game_gid'] = $gameId;
		$save['t_game_id'] = $gplGameId;
		$save['t_game_name'] = $gplGameName;
		$save['t_game_image'] = $gplGameImage;
		$save['t_game_category'] = $gplGameCategory;
		$save['t_game_screen'] = $gplGameScreen;
		$save['t_start_day'] = $startDay;

		$save['t_duration'] = $duration;
		$save['t_exact_hour'] = $exactHour;
		$save['t_exact_minutes'] = $exactMinutes;
		$save['t_exact_ampm'] = $exactAmpm;
		$save['t_start_date'] = $startDate;
		$save['t_start_time'] = $exactHour.":".$exactMinutes;
		$save['t_end_date'] = $timings['end_date'];
		$save['t_end_time'] = $timings['end_time'];

		$save['t_players_count'] = $playersCount;
		$save['t_entry_fee'] = $entryFee;
		$save['t_prize_type'] = $prizeType;

		$share_code = $this->createShareCode(10);   // 10 specifies string lenth here
		$share_code = strtoupper($share_code);
		$sharelink = site_url()."SHARE/".base64_encode($share_code);
		$save['t_share_code'] = $share_code;
		$save['t_share_link'] = $sharelink;

		$save['t_added_on'] = time();

		if($this->db->insert('user_tournaments', $save)){
			$tournamentId = $this->db->insert_id();

			$savePlayer['player_t_id'] = $tournamentId;
			$savePlayer['player_user_id'] = $userId;
			$savePlayer['player_score'] = 0;
			$savePlayer['player_type'] = '1';
			$savePlayer['player_added_on'] = time();
			$this->db->insert('user_tournament_players', $savePlayer);
				// Deduct the 100 Play Coins from user Total coins
			if($userInfo['user_subscription']==1)
			{
				$userInfo = $this->SITEDBAPI->validateUser($userId);
				$playCoins = $userInfo['user_play_coins'];
				$updatedPlayCoins = ($playCoins-100);
				$dataCoins['user_play_coins'] = $updatedPlayCoins;
				$this->db->where('user_id', $userId);
				$this->db->update('site_users', $dataCoins);
				// Update a row for managing coins history				
				$coinHistory['coin_user_id'] = $userId;
				$coinHistory['coin_date'] = date('Y-m-d');				
				$coinHistory['coin_section'] = '4';  //1=AddCoins  2=SpinWin  3=RedeemRewardCoins  4=CreateTournament  5=TournamentReward
				$coinHistory['coin_play_coins_add'] = '-'.$entryFee;
				$coinHistory['coin_play_coins_redeem'] = $entryFee;
				$coinHistory['coin_tournament_id'] = $tournamentId;
				$coinHistory['coin_type'] = '1';  // 1=PlayCoins  2=RewardCoins  3=Both
				$coinHistory['coin_added_on'] = time();
				$this->db->insert('user_coins_history', $coinHistory);
			}
				//Save User Notification
				$this->saveUserNotification($type='1');
				
			$redirectLink = site_url()."Tournaments/".base64_encode($tournamentId);

			echo $redirectLink;
		} else {
			echo '0';
		}
	} else
		redirect();

	}

	public function saveTournament(){
		$userId = $this->session->userdata('userId');
		// $result=$this->SITEDBAPI->getSiteUserDetail($userId);
		$gameId = $this->session->userdata('gameId');
		$startDay = $this->session->userdata('startDay');
		$startDate = $this->session->userdata('startDate');
		$duration = $this->session->userdata('duration');
		$exactHour = $this->session->userdata('exactHour');
		$exactMinutes = $this->session->userdata('exactMinutes');
		$exactAmpm = $this->session->userdata('exactAmpm');
		$userInfo = $this->SITEDBAPI->validateUser($userId);
		$playersCount = $this->session->userdata('playersCount');
		$entryFee = $this->session->userdata('entryFee');
		$prizeType =$this->session->userdata('prizeType');
		// print_r($entryFee);
		// die();
		$this->session->set_userdata('playersCount' ,$playersCount);
		$this->session->set_userdata('entryFee' ,$entryFee);
		$this->session->set_userdata('prizeType' ,$prizeType);
		if($userInfo['user_subscription']!=1)
			{
				if($userInfo['user_free_custom_tournaments']>0)
				{
					$free_tournament = $userInfo['user_free_custom_tournaments'];
					$tournament['user_free_custom_tournaments'] = ($free_tournament - 1);
					$this->db->where('user_id', $userId);
					$this->db->update('site_users', $tournament);
				}
				else{
					$this->session->set_userdata('user_tournament' , true);
					$redirectLink = site_url()."UserSubscription";
					return print_r($redirectLink);
				}
			}
		// Get the game info from master table
		$gameInfo = $this->SITEDBAPI->getGameInfo($gameId);
		$gplGameId = $gameInfo['id'];
		$gplGameName = $gameInfo['Name'];
		$gplGameImage = $gameInfo['GameImage'];
		$gplGameCategory = $gameInfo['Category'];
		$gplGameScreen = $gameInfo['screen'];

		// Calculate the timings of the tournament
		$timings = $this->calculateTournamentTimings($startDate, $duration, $exactHour, $exactMinutes, $exactAmpm);


		//Set start time in 24 hour format for tournament start time
		if($exactAmpm == 'PM' && $exactHour == 12)
			$exactHour= $exactHour;
		else if($exactAmpm == 'PM')
			$exactHour= $exactHour+12;


		$save['t_user_id'] = $userId;
		$save['t_game_gid'] = $gameId;
		$save['t_game_id'] = $gplGameId;
		$save['t_game_name'] = $gplGameName;
		$save['t_game_image'] = $gplGameImage;
		$save['t_game_category'] = $gplGameCategory;
		$save['t_game_screen'] = $gplGameScreen;
		$save['t_start_day'] = $startDay;

		$save['t_duration'] = $duration;
		$save['t_exact_hour'] = $exactHour;
		$save['t_exact_minutes'] = $exactMinutes;
		$save['t_exact_ampm'] = $exactAmpm;
		$save['t_start_date'] = $startDate;
		$save['t_start_time'] = $exactHour.":".$exactMinutes;
		$save['t_end_date'] = $timings['end_date'];
		$save['t_end_time'] = $timings['end_time'];

		$save['t_players_count'] = $playersCount;
		$save['t_entry_fee'] = $entryFee;
		$save['t_prize_type'] = $prizeType;

		$share_code = $this->createShareCode(10);   // 10 specifies string lenth here
		$share_code = strtoupper($share_code);
		$sharelink = site_url()."SHARE/".base64_encode($share_code);
		$save['t_share_code'] = $share_code;
		$save['t_share_link'] = $sharelink;

		$save['t_added_on'] = time();

		if($this->db->insert('user_tournaments', $save)){
			$tournamentId = $this->db->insert_id();

			$savePlayer['player_t_id'] = $tournamentId;
			$savePlayer['player_user_id'] = $userId;
			$savePlayer['player_score'] = 0;
			$savePlayer['player_type'] = '1';
			$savePlayer['player_added_on'] = time();
			$this->db->insert('user_tournament_players', $savePlayer);
			
			
				// Deduct the 100 Play Coins from user Total coins
				
				// $userInfo = $this->SITEDBAPI->validateUser($userId);
				// $playCoins = $userInfo['user_play_coins'];
				// $updatedPlayCoins = ($playCoins-100);
				// $dataCoins['user_play_coins'] = $updatedPlayCoins;
				// $this->db->where('user_id', $userId);
				// $this->db->update('site_users', $dataCoins);
				
				// Update a row for managing coins history				
				// $coinHistory['coin_user_id'] = $userId;
				// $coinHistory['coin_date'] = date('Y-m-d');				
				// $coinHistory['coin_section'] = '4';  //1=AddCoins  2=SpinWin  3=RedeemRewardCoins  4=CreateTournament  5=TournamentReward
			//	$coinHistory['coin_play_coins_add'] = '-'.$entryFee;
				// $coinHistory['coin_play_coins_redeem'] = $entryFee;
				// $coinHistory['coin_tournament_id'] = $tournamentId;
				// $coinHistory['coin_type'] = '1';  // 1=PlayCoins  2=RewardCoins  3=Both
				// $coinHistory['coin_added_on'] = time();
				// $this->db->insert('user_coins_history', $coinHistory);
				
				
				//Save User Notification
				$this->saveUserNotification($type='1');
				
			$redirectLink = site_url()."Tournaments/".base64_encode($tournamentId);

			echo $redirectLink;
		} else {
			echo '0';
		}

	}



	function calculateTournamentTimings($startDate, $duration, $exactHour, $exactMinutes, $exactAmpm){

	/*	if($exactAmpm == 'PM' )
			$exactHour= $exactHour+12;
		else if($exactHour == 12)
			$exactHour= 0;

		*/

		if(empty($duration)){
			$duration = '24';
		}

		//echo "<br>"; echo  $exactHour;

		//if($exactHour !=0 && $exactMinutes !=0){
		//echo	$start = "{$startDate} {$exactHour}:{$exactMinutes}";
			$start = "{$startDate} {$exactHour}:{$exactMinutes} {$exactAmpm}";

			$end_date = date('Y-m-d',strtotime("+{$duration} hours ",strtotime($start)));
			$end_time = date('H:i',strtotime("+{$duration} hours ",strtotime($start)));
		/*} else {
			$start = "{$startDate} 00:00";

			$end_date = date('Y-m-d',strtotime("+{$duration} hours ",strtotime($start)));
			$end_time = date('H:i',strtotime("+{$duration} hours ",strtotime($start)));
		}*/

		$timings['end_date'] = $end_date;
		$timings['end_time'] = $end_time;


		return $timings;
	}



	public function tournamentInfo($id){
		$id = base64_decode($id);

		// Get tournament info
		$data['tournamentInfo'] = $this->SITEDBAPI->getTournamentInfo($id);
		$data['playersInfo'] = $this->SITEDBAPI->getTournamentPlayersListDESC($id);


		// Compare dates of tournaments for the status
		// $today = date('Y-m-d H:i:s');
		$today = time();

		$startDate = $data['tournamentInfo']['t_start_date']." ".$data['tournamentInfo']['t_start_time'].":00";
		$startDate = strtotime($startDate);

		$endDate = $data['tournamentInfo']['t_end_date']." ".$data['tournamentInfo']['t_end_time'].":00";
	 	$endDate = strtotime($endDate);

		$status = 0;     //1=CurrentlyWorking   2=Expired   3=futureTournament
		if($startDate > $today){
			$status = 3;
		} else if($endDate < $today){
			$status = 2;
		} else if($startDate <= $today && $endDate >= $today){
			$status = 1;
		}
		$data['t_current_status'] = $status;
		// Compare dates of tournaments for the status  ends


		if(is_array($data['tournamentInfo']) && count($data['tournamentInfo'])>0)
		{
			$data['session_page_type']=13;
			$data['session_game_id']=$data['tournamentInfo']['t_game_gid'];
			$data['session_tournament_id']=$data['tournamentInfo']['t_id'];
			$this->load->view('site/tournament_info', $data);
		}
		
		else
			redirect();

	}


// Custom Tournaments created with banner click for users

	public function customTournament($gameId)	{
		$userId = $this->session->userdata('userId');
		$userInfo = $this->SITEDBAPI->validateUser($userId);
		if(!empty($userInfo['user_id']) && $userInfo['user_subscription']==0)
		{
			if($userInfo['user_free_custom_tournaments']>0)
			{
				$startDay =  'today';
				$startDate = date('Y-m-d');
				$duration = '24';
				$exactHour = date('h');
				if($exactHour >= 12)
					$exactHour = 0;
				$exactHour = $exactHour+1;
				$exactMinutes = "00";
				$exactAmpm = date('A');
	
				// Save the details in session to go back  7 further
				$this->session->set_userdata('gameId', $gameId);
				$this->session->set_userdata('startDay', $startDay);
				$this->session->set_userdata('startDate', $startDate);
				$this->session->set_userdata('duration', $duration);
				$this->session->set_userdata('exactHour', $exactHour);
				$this->session->set_userdata('exactMinutes', $exactMinutes);
				$this->session->set_userdata('exactAmpm', $exactAmpm);
	
				// Get tournament end time
				$timings = $this->calculateTournamentTimings($startDate, $duration, $exactHour, $exactMinutes, $exactAmpm);
	
	
				$data['gameId'] = $gameId;
				$data['start_day'] = $startDay;
				$data['start_date'] = $startDate;	
				
				$data['duration'] = $duration;
				$data['exact_hour'] = $exactHour;
				$data['exact_minutes'] = $exactMinutes;
				$data['exact_ampm'] = $exactAmpm;
				$data['t_end_date'] = $timings['end_date'];
				$data['t_end_time'] = $timings['end_time'];
			
				$data['gameInfo'] = $this->SITEDBAPI->getGameInfo($gameId);
				$data['session_page_type']=12;
				$data['session_game_id']=$data['gameInfo']['gid'];
				$this->load->view('site/tournament_step_3', $data);	
			}
			else
			{
				$this->session->set_flashdata('less_custom_tournament' , "Sorry ,You are not a VIP user. So,  you can not create more tournament. ");
				redirect();
			}
		}
		else if( !empty($userInfo['user_id']) && $userInfo['user_play_coins'] > 100){

			$startDay =  'today';
			$startDate = date('Y-m-d');
			$duration = '24';
			$exactHour = date('h');
			if($exactHour >= 12)
				$exactHour = 0;
			$exactHour = $exactHour+1;
			$exactMinutes = "00";
			$exactAmpm = date('A');

			// Save the details in session to go back  7 further
			$this->session->set_userdata('gameId', $gameId);
			$this->session->set_userdata('startDay', $startDay);
			$this->session->set_userdata('startDate', $startDate);
			$this->session->set_userdata('duration', $duration);
			$this->session->set_userdata('exactHour', $exactHour);
			$this->session->set_userdata('exactMinutes', $exactMinutes);
			$this->session->set_userdata('exactAmpm', $exactAmpm);

			// Get tournament end time
			$timings = $this->calculateTournamentTimings($startDate, $duration, $exactHour, $exactMinutes, $exactAmpm);


			$data['gameId'] = $gameId;
			$data['start_day'] = $startDay;
			$data['start_date'] = $startDate;	
			
			$data['duration'] = $duration;
			$data['exact_hour'] = $exactHour;
			$data['exact_minutes'] = $exactMinutes;
			$data['exact_ampm'] = $exactAmpm;
			$data['t_end_date'] = $timings['end_date'];
			$data['t_end_time'] = $timings['end_time'];
		
			$data['gameInfo'] = $this->SITEDBAPI->getGameInfo($gameId);
			$data['session_page_type']=12;
			$data['session_game_id']=$data['gameInfo']['gid'];
			$this->load->view('site/tournament_step_3', $data);
		} else {
			$this->session->set_flashdata("redemption_error","Sorry, You don't have enough play coins to create a new tournament.");
			redirect();
		}

	}



	public function sharedTournamentInfo($share_code){
		if(!empty($share_code)){
			$share_code = base64_decode($share_code);
			$data['tournamentInfo'] = $this->SITEDBAPI->getSharedTournamentInfo($share_code);
			if(is_array($data['tournamentInfo']) && count($data['tournamentInfo'])>0){
				$this->session->set_userdata('tournament_share_code', $share_code);
				$this->load->view('site/share_login');
			} else {
				redirect();
			}
		} else {
			redirect();
		}
	}
	
// *******************   Custom Tournaments ends  *********************************  //



	public function sendEmailOTP(){
		$email = $_POST['email'];
		if(!empty($email)){

			// Sanitize E-mail Address
			$email =filter_var($email, FILTER_SANITIZE_EMAIL);
			// Validate E-mail Address
			$email= filter_var($email, FILTER_VALIDATE_EMAIL);
			if($email){

					$otp = $this->createOTP(6);

				/*	$row['content']='';
					$row['content'] .= "<p>Dear User, <br> Your one time password(OTP) to verify your email address is <b>{$otp}</b>. </p>";
					$row['content'] .= "<br><b> Thank you for joining GPL. </b>";
					$row['subject'] = "OTP Requested";
					*/
					
					$row['content']='';
					$row['content'] .= "<p> <br> <b>{$otp}</b> is the One Time Password (OTP) to login and play your Private Tournament. Do not share the OTP with anyone. </p>";
					$row['content'] .= "<p><br><br> <b>IMPORTANT</b>: Please do not reply to this message or mail address.</p>";
					$row['content'] .= "<p><b>DISCLAIMER</b>: This communication is confidential and privileged and is directed to and for the use of the addressee only. The recipient if not the addressee should not use this message if erroneously received, and access and use of this e-mail in any manner by anyone other than the addressee is unauthorized.</p>";
				//	$row['content'] .= "<br><br><b> Thank you for joining PT. </b>";
					
					$row['subject'] = "Your GSL Login OTP";
					
					
					//echo $row['content'];  die;
				
				/*	$this->load->library('email');
					$config['mailtype'] = 'html';
					$this->email->initialize($config);
					$this->email->from('vaish.nisha55@gmail.com', 'GPL');
					$this->email->to($email);
					$this->email->subject($row['subject']);
					$this->email->message($row['content']);
					if($this->email->send()){
						$this->email->clear(TRUE);
						//return true;
						$this->session->set_flashdata('success','<strong>Success! </strong> OTP request sent on the specified email address.');

						$this->session->set_userdata('person_email', $email);
						$this->session->set_userdata('person_otp', $otp);
						redirect('verifyOTP');

					} else {
						$this->email->clear(TRUE);
						// return false;
						$this->session->set_flashdata('error','<strong>Error! </strong> Unable to send OTP request to your specified email address. Please try again.');
						$shareCode = $this->session->userdata('tournament_share_code');
						$shareCode = base64_encode($shareCode);
						redirect("SHARE/".$shareCode);
					}
					*/
					
				// Enable this when shift to live server
					
					$this->load->library("PhpMailerLib");
					$mail = $this->phpmailerlib->load();
					
					try {
						//Server settings
						$mail->SMTPDebug = 0;                                 // Enable verbose debug output
						$mail->isSMTP();                                      // Set mailer to use SMTP
						$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
						$mail->SMTPAuth = true;                               // Enable SMTP authentication
					//	$mail->Username = 'gpl.gamenow@gmail.com';                 // SMTP username
					//	$mail->Password = 'gpl@123*';                           // SMTP password
						$mail->Username = 'adxdigitalsg@gmail.com';                 // SMTP username
						$mail->Password = 'adxd@123';                           // SMTP password
						$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
						$mail->Port = 465;                                    // TCP port to connect to
						//Recipients
						$mail->setFrom('adxdigitalsg@gmail.com', 'GSL');
						$mail->addAddress($email);     // Add a recipient
						$mail->addReplyTo('adxdigitalsg@gmail.com', 'GSL');
						$mail->addBCC('vaish.nisha55@gmail.com');

						//Content
						$mail->isHTML(true);                                  // Set email format to HTML
						$mail->Subject = $row['subject'];
						$mail->Body    = $row['content'];
					
						$mail->send();
						$this->session->set_flashdata('success','<strong>Success! </strong> OTP request sent on the specified email address.');

						$this->session->set_userdata('person_email', $email);
						$this->session->set_userdata('person_otp', $otp);
						redirect('verifyOTP/'.$otp);
						
					} catch (Exception $e) {
						$this->session->set_flashdata('error','<strong>Error! </strong> Unable to send OTP request to your specified email address. Please try again.');
						$shareCode = $this->session->userdata('tournament_share_code');
						$shareCode = base64_encode($shareCode);
						redirect("SHARE/".$shareCode);
					}
					
			}	else {
				redirect();
			}
		} else {
			redirect();
		}
	}
	
	
	public function resendEmailOTP($email =''){
		$email = base64_decode($email);
		if(!empty($email)){

			// Sanitize E-mail Address
			$email =filter_var($email, FILTER_SANITIZE_EMAIL);
			// Validate E-mail Address
			$email= filter_var($email, FILTER_VALIDATE_EMAIL);
			if($email){

					$otp = $this->createOTP(6);

				
					$row['content']='';
					$row['content'] .= "<p> <br> <b>{$otp}</b> is the One Time Password (OTP) to login and play your Private Tournament. Do not share the OTP with anyone. </p>";
					$row['content'] .= "<p><br><br> <b>IMPORTANT</b>: Please do not reply to this message or mail address.</p>";
					$row['content'] .= "<p><b>DISCLAIMER</b>: This communication is confidential and privileged and is directed to and for the use of the addressee only. The recipient if not the addressee should not use this message if erroneously received, and access and use of this e-mail in any manner by anyone other than the addressee is unauthorized.</p>";
					
					$row['subject'] = "Your GSL Login OTP";
					
					
					
				// Enable this when shift to live server
					
					$this->load->library("PhpMailerLib");
					$mail = $this->phpmailerlib->load();
					
					try {
						//Server settings
						$mail->SMTPDebug = 0;                                 // Enable verbose debug output
						$mail->isSMTP();                                      // Set mailer to use SMTP
						$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
						$mail->SMTPAuth = true;                               // Enable SMTP authentication
						
						$mail->Username = 'adxdigitalsg@gmail.com';                 // SMTP username
						$mail->Password = 'adxd@123';                           // SMTP password
						$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
						$mail->Port = 465;                                    // TCP port to connect to
						//Recipients
						$mail->setFrom('adxdigitalsg@gmail.com', 'GSL');
						$mail->addAddress($email);     // Add a recipient
						$mail->addReplyTo('adxdigitalsg@gmail.com', 'GSL');
						$mail->addBCC('vaish.nisha55@gmail.com');

						//Content
						$mail->isHTML(true);                                  // Set email format to HTML
						$mail->Subject = $row['subject'];
						$mail->Body    = $row['content'];
					
						$mail->send();
						$this->session->set_flashdata('success','<strong>Success! </strong> OTP request sent on the specified email address.');

						$this->session->set_userdata('person_email', $email);
						$this->session->set_userdata('person_otp', $otp);
						redirect('verifyOTP');
						
					} catch (Exception $e) {
						$this->session->set_flashdata('error','<strong>Error! </strong> Unable to send OTP request to your specified email address. Please try again.');
						$shareCode = $this->session->userdata('tournament_share_code');
						$shareCode = base64_encode($shareCode);
						redirect("SHARE/".$shareCode);
					}
					
			}	else {
				redirect();
			}
		} else {
			redirect();
		}
	}
	
	
	public function verifyOTP(){
		$shareCode = $this->session->userdata('tournament_share_code');
		$personEmail = $this->session->userdata('person_email');
		$personOTP = $this->session->userdata('person_otp');
		$this->load->view('site/verify_login_otp');
	}

	public function confirmEmailOTP(){
		$shareCode = $this->session->userdata('tournament_share_code');
		$personEmail = $this->session->userdata('person_email');
		$personOTP = $this->session->userdata('person_otp');
		$otp = $_POST['otp'];

		if(!empty($otp) && $otp == $personOTP){

			$checkEmail = $this->SITEDBAPI->checkUserByEmail($personEmail);
			if(is_array($checkEmail) && count($checkEmail)>0){
				// already registered email address
				$userId = $checkEmail['user_id'];
				$this->session->set_userdata('userId', $userId);

				if(empty($checkEmail['skillpod_player_id'])){
					// Create  Skillpod id on gameboost for playing the games
					$this->createGameboostId($userId, $personEmail);
				}
				
				$this->session->unset_userdata('person_email');
				$this->session->unset_userdata('person_otp');


				// user verified successflly now resend the user to the tournament page
				redirect('TournamentInfo/'.base64_encode($shareCode));

			} else {
				$profileImage = rand(1,32);
				if(!empty($profileImage)){
					$profileImage = $profileImage.".png";
				} else {
					$profileImage = 'default-user.png';
				}
				// a non registered email address
				$dataUser['user_email'] = $personEmail;
				$dataUser['user_login_otp'] = $personOTP;
				//$dataUser['user_image'] = 'default-user.png';
				$dataUser['user_image'] = $profileImage;
				$dataUser['user_registered_on'] = date('Y-m-d H:i:s');
				if($this->db->insert('site_users', $dataUser)){

					$userId = $this->db->insert_id();
					$this->session->set_userdata('userId', $userId);

					$this->session->unset_userdata('person_email');
					$this->session->unset_userdata('person_otp');
					
					// Create  Skillpod id on gameboost for playing the games
					$this->createGameboostId($userId, $personEmail);
					
					// user verified successflly now resend the user to the tournament page
					redirect('TournamentInfo/'.base64_encode($shareCode));

				} else{
					$this->session->set_flashdata('error','<strong>Error! </strong> Unable to verify your email address. Please try again after sometime.');
					redirect('verifyOTP');
				}

			}
		} else {
			$this->session->set_flashdata('error','<strong>Error! </strong> Invalid OTP entered. Please enter a valid OTP sent on your email address.');
			redirect('verifyOTP');
		}
	}

	public function friendTournamentInfo($shareCode){
		$shareCode = base64_decode($shareCode); 
		$userId = $this->session->userdata('userId');
		if(!empty($shareCode) && !empty($userId) ){
		
			// Get tournament info
			$data['tournamentInfo'] = $this->SITEDBAPI->getSharedTournamentInfo($shareCode);
			
			
			if(is_array($data['tournamentInfo']) && count($data['tournamentInfo'])>0){
				
				$t_id = $data['tournamentInfo']['t_id'];
				$data['playersInfo'] = $this->SITEDBAPI->getTournamentPlayersListDESC($t_id);

				// $today = date('Y-m-d H:i:s');
					$today = time();

					$startDate = $data['tournamentInfo']['t_start_date']." ".$data['tournamentInfo']['t_start_time'].":00";
					$startDate = strtotime($startDate);

					$endDate = $data['tournamentInfo']['t_end_date']." ".$data['tournamentInfo']['t_end_time'].":00";
					$endDate = strtotime($endDate);

					$status = 0;     //1=CurrentlyWorking   2=Expired   3=futureTournament
					if($startDate > $today){
						$status = 3;
					} else if($endDate < $today){
						$status = 2;
					} else if($startDate <= $today && $endDate >= $today){
						$status = 1;
					}
				$data['t_current_status'] = $status;
				// Compare dates of tournaments for the status  ends
				
				
				$total_players_count = $data['tournamentInfo']['t_players_count']+1;
				$joined_players_count = $data['tournamentInfo']['no_players'];
				if($joined_players_count < $total_players_count) {				
					$data['player_availability'] = 'yes';
				} else {
					$data['player_availability'] = 'no';
				}

				if($data['t_current_status'] == 2){
					$this->load->view('site/tournament_expired', $data);
				} else {
					$this->load->view('site/friend_tournament_info', $data);
				}
			
			} else{
				redirect();
			}		
		} else {
			redirect();
		}

	}


	public function joinTournament($id){
		 $id = base64_decode($id); 
		//get loggedin  user id
		$userId = $this->session->userdata('userId');
		if(!empty($userId) && !empty($id) ){
		
			// Get tournament info
			$data['tournamentInfo'] = $this->SITEDBAPI->getTournamentInfo($id);
			
			if(is_array($data['tournamentInfo']) && count($data['tournamentInfo'])>0){
			//echo $this->db->last_query(); die;
				
				$checkTournamentPlayer = $this->SITEDBAPI->checkTournamentPlayer($userId, $id);
				
				if(count($checkTournamentPlayer)==0  ){				
					$savePlayer['player_t_id'] = $id;
					$savePlayer['player_user_id'] = $userId;
					$savePlayer['player_score'] = 0;
					$savePlayer['player_type'] = '2';
					$savePlayer['player_added_on'] = time();
					$this->db->insert('user_tournament_players', $savePlayer);
				}

				// $today = date('Y-m-d H:i:s');
				$today = time();

				$startDate = $data['tournamentInfo']['t_start_date']." ".$data['tournamentInfo']['t_start_time'].":00";
				$startDate = strtotime($startDate);

				$endDate = $data['tournamentInfo']['t_end_date']." ".$data['tournamentInfo']['t_end_time'].":00";
				$endDate = strtotime($endDate);

				$status = 0;     //1=CurrentlyWorking   2=Expired   3=futureTournament
				if($startDate > $today){
					$status = 3;
				} else if($endDate < $today){
					$status = 2;
				} else if($startDate <= $today && $endDate >= $today){
					$status = 1;
				}
				$data['t_current_status'] = $status;
				$data['user_id'] = $userId;
				$data['tournament_id'] = $id;
				$data['playersInfo'] = $this->SITEDBAPI->getTournamentPlayersListDESC($id);

			//	$this->load->view('site/tournament_players', $data);
			redirect('PlayTournament/'.base64_encode($id));
			
			} else {
				redirect();
			}
		} else {
			redirect();
		}
	}


	public function playTournament($id){
		$id = base64_decode($id);
		$userId = $this->session->userdata('userId');
		if(!empty($userId) && !empty($id) ){
			// Get tournament info
			
			$data['tournamentInfo'] = $this->SITEDBAPI->getTournamentInfo($id);
			
			//echo "<pre>"; print_r($data['tournamentInfo']);  echo "</pre>"; die;
			
			$data['userInfo'] = $this->SITEDBAPI->getSiteUserDetail($userId);
			if(is_array($data['tournamentInfo']) && count($data['tournamentInfo'])>0){
				
				// $today = date('Y-m-d H:i:s');
				$today = time();

				$startDate = $data['tournamentInfo']['t_start_date']." ".$data['tournamentInfo']['t_start_time'].":00";
				$startDate = strtotime($startDate);

				$endDate = $data['tournamentInfo']['t_end_date']." ".$data['tournamentInfo']['t_end_time'].":00";
				$endDate = strtotime($endDate);

				$status = 0;     //1=CurrentlyWorking   2=Expired   3=futureTournament
				if($startDate > $today){
					$status = 3;
				} else if($endDate < $today){
					$status = 2;
				} else if($startDate <= $today && $endDate >= $today){
					$status = 1;
				}
				
				if($status == 1){
					$gameId = $data['tournamentInfo']['t_game_id'];
					$playerProfileId = $data['userInfo']['skillpod_player_id'];
					
					$data['game_id'] = $gameId;
					$data['player_profile_id'] = $playerProfileId;
					$data['tournament_id'] = $id;
					
					$this->load->view('site/tournament_play_game', $data);
				} else {
					redirect('TournamentInfo/'.base64_encode($data['tournamentInfo']['t_share_code']));
				}
			} else {
				redirect();
			}
		} else {
			redirect();
		}

	}

	
	
	public function updateTournamentPlayerScore($tournament_id='', $game_id='', $skillpod_player_id='', $redirect=''){
		
		if(!empty($tournament_id) && !empty($game_id) && !empty($skillpod_player_id) && !empty($redirect)){
			
			$userId = $this->session->userdata('userId');
			$tournament_id =  base64_decode($tournament_id);
			$game_id = $game_id;
			$skillpod_player_id =  $skillpod_player_id;
			$redirect_path =  $redirect;
			
			$tournamentInfo = $this->SITEDBAPI->getTournamentInfo($tournament_id);
			
			$postArray = array('game_id' => $game_id,'player_id' => $skillpod_player_id);
			
			// Get current user score starts
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://games.igpl.pro/xml-api/get_player_scores',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_SSL_VERIFYHOST => 0,
			  CURLOPT_SSL_VERIFYPEER => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => $postArray,
			  CURLOPT_HTTPHEADER => array(
				'Authorization: Bearer eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJwYXJ0bmVyX2NvZGUiOiJ0ZXN0LTAwMSIsInBhcnRuZXJfcGFzc3dvcmQiOiJ0ZXN0LTAwMSJ9.GQp4_XWFc1FkHHGoy6XWVe40_QHCUt4ityt57ahXoEMW2AhNHo27V_wJmypgZshbw1w6345mKffaGtf9XowGOA'
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			

			$responseXML = simplexml_load_string($response);
			$responseJSON = json_encode($responseXML);

			// Convert into associative array
			$responseArray = json_decode($responseJSON, true);
			
		 	$currentScore = @$responseArray['get_player_scores']['player_scores']['player_score_0']['score'];
			
			
			//Get User last saved score
			$scoreInfo = $this->SITEDBAPI->getTournamentPlayerScore($tournament_id, $userId);
			$lastScore = @$scoreInfo['player_score'];
			$player_id = @$scoreInfo['player_id'];
			if($currentScore >= $lastScore){
				$saveScore['player_score'] = $currentScore;
				$this->db->where('player_id', $player_id);
				$this->db->update('user_tournament_players', $saveScore);
			}
			
			
			if($redirect_path == 'redirect_leaderboard'){
				redirect('TournamentLeaderboard/'.base64_encode($tournament_id));
			} else {
				$logged_user_id = $this->session->userdata('userId'); 
				if($tournamentInfo['t_user_id'] == $logged_user_id){
					redirect('Tournaments/'.base64_encode($tournamentInfo['t_id']));
				} else {
					redirect('TournamentInfo/'.base64_encode($tournamentInfo['t_share_code']));
				}
			}
			
		} else {
			redirect();
		}
	}


	public function updateGameboostPlayerScore(){
		
		$userId = $this->session->userdata('userId');
		$tournament_id = @$_POST['tournament_id'];
		$game_id = @$_POST['game_id'];
		$skillpod_player_id = @$_POST['skillpod_player_id'];
		
		if(!empty($tournament_id) && !empty($game_id) && !empty($skillpod_player_id)){
			
			$tournamentInfo = $this->SITEDBAPI->getTournamentInfo($tournament_id);
		
			$apiURL = "https://multiplayergameserver.com/xmlapi7/xmlapi.php?site_id=834&password=GiK2Xz9Ty&nocompress=true&action=get_player_scores&order_by_field=time&order_by_direction=DESC&game_id=".$game_id."&skillpod_player_id=".$skillpod_player_id."&show_games=false";
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiURL);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/xml'));
			$response = curl_exec($ch);
			curl_close($ch);

			$responseXML = simplexml_load_string($response);
			$responseJSON = json_encode($responseXML);

			// Convert into associative array
			$responseArray = json_decode($responseJSON, true);
			$userScore = @$responseArray['player_scores']['player_score_0']['score'];			
		 	$scoreDate = @$responseArray['player_scores']['player_score_0']['time'];
			$scoreDate = date('Y-m-d', strtotime($scoreDate));
		//	$scoreDateTime = strtotime($scoreDate);
			
		//	$t_start_date = $tournamentInfo['t_start_date']." ".$tournamentInfo['t_start_time'].":00";
		//	$t_start_date = strtotime($t_start_date);
			
		//	$t_end_date = $tournamentInfo['t_end_date']." ".$tournamentInfo['t_end_time'].":00";
		//	$t_end_date = strtotime($t_end_date);
			
		//	$timenow_start = strtotime("+15 minutes", time());
		//	$timenow_end = strtotime("+45 minutes", time());
			
			$t_start_date = $tournamentInfo['t_start_date'];
			$t_end_date = $tournamentInfo['t_end_date'];
			
			/*
			if($t_start_date <= $scoreDate &&  $t_end_date >= $scoreDate ){
			//if($scoreDateTime > $timenow_start &&  $scoreDateTime  < $timenow_end ){
				if(!empty($userScore)){
					$currentScore = $userScore;
				} else {
					$currentScore = 0;
				}
			} else {
				$currentScore = 0;
			}
			*/
			
			$currentScore = $userScore;
			
			
			
			//echo "Current Score: ".$currentScore;
			
			$scoreInfo = $this->SITEDBAPI->getTournamentPlayerScore($tournament_id, $userId);
			$lastScore = @$scoreInfo['player_score'];
			$player_id = @$scoreInfo['player_id'];
			if($currentScore >= $lastScore){
				$saveScore['player_score'] = $currentScore;
				$this->db->where('player_id', $player_id);
				$this->db->update('user_tournament_players', $saveScore);
			}
		}
	}
	
	
// *****************************   **************************** ********************************** //
// *****************************   Custom Tournaments Ends Here ********************************** //
// *****************************   **************************** ********************************** //




}
