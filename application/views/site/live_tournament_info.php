<!doctype html>
<html class="no-js" lang="en">

<head>

	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1, user-scalable=0">

	<title>Live Tournament - iGPL</title> 
	<meta name="description" content="Play your private battles with Friends & Family!">
	<meta property="og:type" content="website">
	<meta property="og:site_name" content="Gaming Social League" />
	<meta property="og:title" content="Gaming Social League">
	<meta property="og:url" content="https://playpt.igpl.pro">
	<meta property="og:description" content="Play your private battles with Friends & Family!">
	<meta property="og:image" content="<?php echo base_url() ?>assets/frontend/img/gpl.png" />
	<meta content="<?php echo base_url() ?>assets/frontend/img/gpl.png" property="thumbnail" />
	
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/frontend/css/bootstrap.min.css">
	<script src="<?php echo base_url() ?>assets/frontend/js/jquery.min.js"></script>
	<script src="<?php echo base_url() ?>assets/frontend/js/bootstrap.min.js"></script>
	
	<!-- For fontawesome icons -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/frontend/fontawesome-5.15.1/css/all.css" rel="stylesheet">
	<script defer src="<?php echo base_url() ?>assets/frontend/fontawesome-5.15.1/js/all.js"></script>
	
	
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/frontend/css/style_theme_2.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/frontend/css/new-style.css">
	<script type="text/javascript" src="<?php echo base_url() ?>assets/frontend/js/main.js"></script>
	
	<style>
		
		#timer{
			font-size:1.0em !important;
		}
		/* @media screen and (min-width :1024) { */
			@media only screen and (min-width: 768px){
			.modal-title {
    			padding: 0;
			}
			.modal {
    			margin-top: 18%;
				width:30%;
			}
		}
		/* } */
		
		.bg-dark{
			color:#fff;
			background: rgb(44,43,77);
			background: linear-gradient(242deg, rgba(44,43,77,1) 0%, rgba(53,56,81,1) 49%, rgba(44,85,134,1) 100%);

		}
		
		.btn-outline-dark{
			border:2px solid #fff;
			color:#fff;
			border-radius:20px;
			font-size:1em;
			font-weight:400;
			background:#5c71db !important;
			border:2px solid #5c71db;
			padding:5px 10px;
			
		}
		
		.btn-outline-dark:hover{
			background:#5c71db !important;
			border:2px solid #fff;
			color:#fff;
			font-weight:400;
		}
		
		.btn-outline-share{
			border:2px solid #fff;
			color:#5c71db;
			border-radius:20px;
			font-size:1em;
			font-weight:400;
			background:none !important;
			border:2px solid #5c71db;
			padding:5px 10px;
			
		}
		
		.btn-outline-share:hover{
			border:2px solid #fff;
			color:#fff;
			border-radius:20px;
			font-size:1em;
			font-weight:400;
			background:#5c71db !important;
			border:2px solid #5c71db;
			padding:5px 10px;
			
		}
		
		
		@media only screen and (min-width: 600px){
			.share-text {
				margin-top:25px;
			}
			.share-text > h4{
				font-size:1.8em;
			}
		}
		
		.thumb-icon-2{
			width:100%;
		}
		
		.modal-body{
			color:#2b2b2b !Important;
		}
		
		
	</style>
	
	
	
</head>
<body>
<div id="load"></div>


<?php
	
		$descQuery = "SELECT description,gamehelp,gametip FROM tbl_games WHERE id =".$tournamentInfo['tournament_gameboost_id'];
		$descInfo = $this->db->query($descQuery)->row();
		$gameDescription = stripslashes(urldecode($descInfo->description));			
		$gameHelp = stripslashes(urldecode($descInfo->gamehelp));			
		$gameTips = stripslashes(urldecode($descInfo->gametip));

	?>

<?php 
	function obfuscate_email($email){
	$em   = explode("@",$email);
   //return @$em[0].'@' . 'xxxxxxxxx' ; 
	return @$em[0];
}
?>

	<section>
	
	
	<div class="details-img">
		<?php if(!empty($heroRow['banner_image_path'])){ ?>
			<img src="<?php echo base_url('uploads/tournaments-banners/'.$tournamentInfo['banner_image_path']) ?>" class=" image-responsive">
		<?php } else { ?>
				<img src="<?php echo base_url('uploads/640X360/'.$tournamentInfo['tournament_gameboost_id'].'.jpg') ?>" class=" image-responsive">
		<?php } ?>
		
		<div class="overlay">&nbsp;</div>	
			
		<div class="details-game-strip">
			<span class="pull-left"><a href="<?php echo site_url('') ?>"><img src="<?php echo base_url() ?>assets/frontend/img/icons/back.png" height="14"></a></span>
			<span class="pull-right how-to-play"><a href="#" data-toggle="modal" data-target="#how_to_play" >How to Play?</a></span>
		</div>
		<!-- End Details Strip -->
		<div class="detail-game-content">
			<span class="details-name"><?php echo stripslashes( urldecode( @$tournamentInfo['tournament_name'])); ?></span>
			<span class="details-arcade"><?php echo stripslashes( urldecode( @$tournamentInfo['tournament_category'])); ?></span>
		</div>
	</div>
	
		
	<div class="main-container">
		<div class="col-xs-12">
			<span class="tournament-start-in">Tournament ends in</span>
			<div class="tournament-duaration">
			
			<div class="col-xs-3"><span class="text-dark" data-countdown="<?php echo $tournamentInfo['tournament_end_date'].' '.$tournamentInfo['tournament_end_time'].":00"; ?>" id="timer-days">00</span><span class="text-light">Days</span></div>
			<div class="col-xs-3"><span class="text-dark" data-countdown="<?php echo $tournamentInfo['tournament_end_date'].' '.$tournamentInfo['tournament_end_time'].":00"; ?>" id="timer-hours">00</span><span class="text-light">Hours</span></div>
			<div class="col-xs-3"><span class="text-dark" data-countdown="<?php echo $tournamentInfo['tournament_end_date'].' '.$tournamentInfo['tournament_end_time'].":00"; ?>" id="timer-mins">00</span><span class="text-light">Minutes</span></div>
			<div class="col-xs-3"><span class="text-dark" data-countdown="<?php echo $tournamentInfo['tournament_end_date'].' '.$tournamentInfo['tournament_end_time'].":00"; ?>" id="timer-sec">00</span><span class="text-light">Seconds</span></div>
		
		</div>
		</div>
		
		<div class="col-xs-12 detail-btns">
			<div class="col-xs-6 practive"><span><a class="practice_tournament" href="<?php echo site_url('practiceTournamentGame/'.base64_encode($tournamentInfo['tournament_gameboost_id']).'/'.base64_encode($tournamentInfo['tournament_id'])) ?>"><span class="theme-color">Practice</span></a></span></div>
			
			<div class="col-xs-6 play-tournament"><span><a id="play_tournament" href="<?php echo site_url('PlayLiveTournament/'.base64_encode($tournamentInfo['tournament_id'])) ?>"><span class="white">Play Tournament</span></a></span></div> 
			
		</div>
		
		
		
		<div class="row" style="max-width:100vw !important;">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 ">
				<a href="https://www.netflix.com/" target="_blank">  
					<img src="<?php echo base_url('assets/ads/netflix-ad.jpg') ?>" style="min-width:100%;max-width:100%;" /> 
				</a>
			</div>
		</div>
		
		
		
		<div class="col-xs-12 padd detail-rewards align-middle">
			<div class="col-xs-3 padd text-center"><img src="<?php echo base_url() ?>assets/frontend/img/coin.png" width="50"></div>
			<div class="col-xs-6 padd ">Entry Fee<span class="reward-coins block"><span class="theme-color"> 
			<?php if($tournamentInfo['fee_tournament_fee']>0){ ?>
			<b><?php echo number_format($tournamentInfo['fee_tournament_fee'], 0); ?> </b> </span> play coins</span>
			<?php } else { ?>
			<b>Free</b>
			<?php }  ?>
			</div>
			
		</div>
		
		<div class="col-xs-12 padd detail-rewards align-middle">
			<div class="col-xs-3 padd text-center"><img src="<?php echo base_url() ?>assets/frontend/img/gold-coin.png" width="64"></div>
			<div class="col-xs-6 padd">Win up to<span class="reward-coins block"><span class="theme-color"> <b><?php echo number_format($tournamentInfo['fee_tournament_prize_1'], 0); ?> </b></span> reward coins</span></div>
			
		</div>
		
		
		<!-- Second-section -->
		<div class="col-xs-12 padd detail-rewards align-middle">
			<div class="col-xs-3 padd text-center"><img src="<?php echo base_url() ?>assets/frontend/img/trophy.png" width="64"></div>
			<div class="col-xs-9 padd">
			
				<?php if(!empty($checkPlayerEntry['player_id'])){ ?>
					<span class="bold theme-color">Your current rank #<?php echo @$myRank; ?></span> 
				<?php } else { ?>
					<span class="bold">You havn't played this tournament.</span> 
				<?php }  ?>
				
				<?php if($totalPlayersCount == 0){ ?>
				<span class="light-dark theme-color"> Be the first to score in this tournament!</span>
				<?php } else { ?>
					<?php if($myRank != 1){ ?>
						<span class="light-dark"> Play now and climb up the leaderboard!</span>
					<?php }  ?>
				<?php }  ?>
				
				
				
				<?php if($totalPlayersCount >0){ ?>
					<a  href="<?php echo site_url('LiveTournamentLeaderboard/'.base64_encode($tournamentInfo['tournament_id'])) ?>" class="block white">View Leaderboard <img src="<?php echo base_url() ?>assets/frontend/img/icons/Arrow-Forward.png" width="14"></a>
				<?php } ?>
			</div>
			
		</div>
		<!-- Reward-Ranking -->
		<div class="col-xs-12 padd reward-ranking">
			<table class="table">
				<tr><th colspan="2">Rewards per ranking</th></tr>
				<?php if(!empty($tournamentInfo['fee_tournament_prize_1']) ){ ?>
					<tr><td>Rank 1</td><td><?php echo number_format($tournamentInfo['fee_tournament_prize_1'],0); ?></td></tr>
				<?php } ?>
				<?php if(!empty($tournamentInfo['fee_tournament_prize_2']) ){ ?>
					<tr><td>Rank 2</td><td><?php echo number_format($tournamentInfo['fee_tournament_prize_2'],0); ?></td></tr>
				<?php } ?>
				<?php if(!empty($tournamentInfo['fee_tournament_prize_3']) ){ ?>
					<tr><td>Rank 3</td><td><?php echo number_format($tournamentInfo['fee_tournament_prize_3'],0); ?></td></tr>
				<?php } ?>
				<?php if(!empty($tournamentInfo['fee_tournament_prize_4']) ){ ?>
					<tr><td>Rank 4 - 5</td><td><?php echo number_format($tournamentInfo['fee_tournament_prize_4'],0); ?></td></tr>
				<?php } ?>
				<?php if(!empty($tournamentInfo['fee_tournament_prize_5']) ){ ?>
					<tr><td>Rank 6 - 10</td><td><?php echo number_format($tournamentInfo['fee_tournament_prize_5'],0); ?></td></tr>
				<?php } ?>
				<?php if(!empty($tournamentInfo['fee_tournament_prize_6']) ){ ?>
					<tr><td>Rank 11 - 25</td><td><?php echo number_format($tournamentInfo['fee_tournament_prize_6'],0); ?></td></tr>
				<?php } ?>
				<?php if(!empty($tournamentInfo['fee_tournament_prize_7']) ){ ?>
					<tr><td>Rank 26 - 50</td><td><?php echo number_format($tournamentInfo['fee_tournament_prize_7'],0); ?></td></tr>
				<?php } ?>
				<?php if(!empty($tournamentInfo['fee_tournament_prize_8']) ){ ?>
					<tr><td>Rank 51 - 100</td><td><?php echo number_format($tournamentInfo['fee_tournament_prize_8'],0); ?></td></tr>
				<?php } ?>
				<?php if(!empty($tournamentInfo['fee_tournament_prize_9']) ){ ?>
					<tr><td>Rank 101 - 200</td><td><?php echo number_format($tournamentInfo['fee_tournament_prize_9'],0); ?></td></tr>
				<?php } ?>
				
			</table>

		</div>
		
		<div class="row" style="max-width:100vw !important;">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 ">
				<a href="https://tinder.com/" target="_blank"> 
					<img src="<?php echo base_url('assets/ads/tinder.jpg') ?>" style="min-width:100%;max-width:100%;" /> 
				</a>
				
			</div>
		</div>
		<div class="row" style="max-width:100vw !important;"><br></div>
		
		
	</div>
<!-- 
	<div class="modal" tabindex="-1" id="subscription" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Modal body text goes here.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary">Save changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div> -->
	<!-- <div class="modal fade" id="subscription" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p>you are not a subscribed user. To Play this tournament , You have to subscribe us</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary">Subscribe</button>
				</div>
			</div>
		</div>
	</div> -->
	<!-- <div class="modal fade" id="subscription" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Subscribe</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				<hr>
			</div>
			<div class="modal-body">
				<p>You are not a VIP user. Please subscribe us to play this tournament.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<a href="<?php echo site_url('PlayLiveTournament/'.base64_encode($tournamentInfo['tournament_id'])) ?>"> <button type="button" class="btn btn-primary">Subscribe</button></a>
			</div>
			</div>
		</div>
	</div> -->

	<!-- <div class="modal fade" id="top_up" tabindex="-1" role="dialog" aria-labelledby="example" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="example">TOPUP</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				<hr>
			</div>
			<div class="modal-body">
				<p>You have not enough coin to pay the tournament fee.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				 <button type="button" class="btn btn-primary">Top Up</button>
				<a href="<?php echo site_url('PlayLiveTournament/'.base64_encode($tournamentInfo['tournament_id'])) ?>"> <button type="button" class="btn btn-primary">Top Up</button></a>
			</div>
			</div>
		</div>
	</div> -->

</section>

<script>
	<?php if($this->session->flashdata('topup')){ ?>
		$(document).ready(function()
		{
			$('#top_up').modal('show');
		})
	<?php } ?>
</script>

<script>
	<?php if($this->session->flashdata('subs')){ ?>
		$(document).ready(function()
		{
			$('#subscription').modal('show');
		})
	<?php } ?>
</script>

<script>
	<?php if($this->session->flashdata('auto_pay')){ ?>
		$(document).ready(function()
		{
			$('#parking').modal('show');
		})
	<?php } ?>
</script>

<script>
	$('#play_tournament').click(function()
	{
			window.location.href='<?php echo site_url('PlayLiveTournament/'.base64_encode($tournamentInfo['tournament_id'])) ?>';	
	})
</script>


<!-- Modal -->
<div id="how_to_play" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header bg-info">
        <button type="button" class="close" data-dismiss="modal">x</button>
        <h4 class="modal-title">How to play </h4>
      </div>
      <div class="modal-body">
		<?php if(!empty($gameDescription)){ ?>
				<p> <?php echo $gameDescription; ?></p>
		<?php } ?>
		<br>
		<?php if(!empty($gameHelp)){ ?>
				<h4>Help</h4>
				<p> <?php echo $gameHelp; ?></p>
		<?php } ?>
		<br>
		<?php if(!empty($gameTips)){ ?>
				<h4>Tips</h4>
				<p> <?php echo $gameTips; ?></p>
		<?php } ?>
		
		<br>
      </div>
    
    </div>

  </div>
</div>

 <div class="modal fade" id="ads_modal" style="margin-top: 5%;" role="dialog">
    <div class="modal-dialog box-center">
      <div class="modal-content modal-bg" align="center">
      
        <div class="modal-body">
			<a href="https://music.youtube.com/" target="_blank"> 
				<img src="<?php echo base_url('assets/ads/full-banner-ad-2.jpg') ?>" width="99%"/>  
			</a>
			<br><br>
			<button class="btn modal-btn-dark-ads" data-dismiss="modal">Close Ad</button>
			<br><br>
			<p><b>Hides in <span data-countdown-ad="<?php echo date("Y/m/d H:i:s", time() + 7); ?>" id="timer-ads"></span></b> </p>
			<br>	
        </div>
      </div>
    </div>
  </div>	
<script>
jQuery(document).ready(function ($) {
	jQuery('#ads_modal').modal('show');
	
	setTimeout(function(){
	  jQuery('#ads_modal').modal('hide')
	}, 6000);  
}); 
</script>	



 
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment-with-locales.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.4.0/moment-timezone-with-data-2010-2020.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js" integrity="sha512-lteuRD+aUENrZPTXWFRPTBcDDxIGWe5uu0apPEn+3ZKYDwDaEErIK9rvR0QzUGmUQ55KFE2RqGTVoZsKctGMVw==" crossorigin="anonymous"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
	$('#timer-days').each(function() {
	  var $this = $(this), finalDate = moment.tz($this.data('countdown'), "Asia/Manila");
	  $this.countdown(finalDate.toDate(), function(event) {
		$this.html(event.strftime('%D'));
	  });
	});
	$('#timer-hours').each(function() {
	  var $this = $(this), finalDate = moment.tz($this.data('countdown'), "Asia/Manila");
	  $this.countdown(finalDate.toDate(), function(event) {
		$this.html(event.strftime('%H'));
	  });
	});
	$('#timer-mins').each(function() {
	  var $this = $(this), finalDate = moment.tz($this.data('countdown'), "Asia/Manila");
	  $this.countdown(finalDate.toDate(), function(event) {
		$this.html(event.strftime('%M'));
	  });
	});
	$('#timer-sec').each(function() {
	  var $this = $(this), finalDate = moment.tz($this.data('countdown'), "Asia/Manila");
	  $this.countdown(finalDate.toDate(), function(event) {
		$this.html(event.strftime('%S'));
	  });
	});
	
	
});
</script>
	
 
 <script>
$(document).ready(function() {
	$('#timer-ads').each(function() {
	  var $this = $(this), finalDate = moment.tz($this.data('countdown-ad'), "Asia/Manila");
	  $this.countdown(finalDate.toDate(), function(event) {
		$this.html(event.strftime('%M:%S'));
	  });
	});
});
</script>
 
<script>
jQuery(document).ready(function() {
    jQuery('#load').fadeOut("slow");
});
</script>

  
 
<div class="modal fade" id="subscriptionErrorModal" role="dialog">
    <div class="modal-dialog box-center">
      <div class="modal-content modal-bg modal-bg-error" align="center">
      
        <div class="modal-body">
           <img src="<?php echo base_url('assets/frontend/img/sad-face.png') ?>" />
          <br>
		  <h4 class="text-white"> <b> OOPS ! </b></h4>
          <h4 class="text-white"><?php echo @$this->session->flashdata('subscription_error') ?></h4>
		   <br><br>
		  <button type="button" class="btn modal-btn-dark" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="top_up" role="dialog">
    <div class="modal-dialog box-center">
      <div class="modal-content modal-bg modal-bg-error" align="center">
      
        <div class="modal-body">
           <img src="<?php echo base_url('assets/frontend/img/sad-face.png') ?>" />
          <br>
		  <h4 class="text-white"> <b> OOPS ! </b></h4>
          <h4 class="text-white">You have not enough coins to pay the tournament fee.</h4>
		   <br><br>
		  <button type="button" class="btn modal-btn-dark" data-dismiss="modal">Close</button>
		  <a href="<?php echo site_url('UserSubscription/TOPUP'); ?>"><button type="button" class="btn modal-btn-dark" >Topup now</button></a>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="subscription" role="dialog">
    <div class="modal-dialog box-center">
      <div class="modal-content modal-bg modal-bg-error" align="center">
      
        <div class="modal-body">
           <img src="<?php echo base_url('assets/frontend/img/sad-face.png') ?>" />
          <br>
		  <h4 class="text-white"> <b> OOPS ! </b></h4>
          <h4 class="text-white">You are not an VIP user.</h4>
		   <br><br>
		  <button type="button" class="btn modal-btn-dark" data-dismiss="modal">Close</button>
		  <a href="<?php echo site_url('UserSubscription'); ?>"><button type="button" class="btn modal-btn-dark" >Subscribe now</button></a>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="parking" role="dialog">
    <div class="modal-dialog box-center">
      <div class="modal-content modal-bg modal-bg-error" align="center">
      
        <div class="modal-body">
           <img src="<?php echo base_url('assets/frontend/img/sad-face.png') ?>" />
          <br>
		  <h4 class="text-white"> <b> OOPS ! </b></h4>
          <h4 class="text-white">Your auto payment failed.</h4>
		   <br><br>
		  <button type="button" class="btn modal-btn-dark" data-dismiss="modal">Close</button>
		  <!-- <a href="<?php echo site_url('PlayLiveTournament/'.base64_encode($tournamentInfo['tournament_id'])) ?>"><button type="button" class="btn modal-btn-dark" >Subscribe now</button></a> -->
        </div>
      </div>
    </div>
  </div>

  <!-- <script>
	  $(document).ready(function(){
		  <?php if($this->session->flashdata('subscription_success')){ ?>
			Swal.fire(
				'Subscribed!',
				'You are a VIP user!',
				'success'
				)
		  <?php } ?>
	  })
  </script> -->

<script>
	  $(document).ready(function(){
		  <?php if($this->session->flashdata('subscription_success')){ ?>
			$('#successSubscriptionModal').modal('show');
		  <?php } ?>
	  })
  </script>
<?php include "page_session_timeout.php"; ?>
<div class="modal fade" id="successSubscriptionModal" role="dialog">
    <div class="modal-dialog box-center">
      <div class="modal-content modal-bg  modal-bg-success" align="center">
        <!-- <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>   -->
        <div class="modal-body">
         <!--  <i class="fa fa-check-circle text-success" style="font-size:32px; "></i>  -->
          <img src="<?php echo base_url('assets/frontend/img/happy-face.png') ?>" />
          <br>
		  <h4 class="text-white"> <b>Success! </b></h4>
          <p class="text-white">You are a VIP user!</p>
		  <br><br>
		  <button type="button" class="btn modal-btn-dark" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>

  <div class="modal fade" id="topUpErrorModal" role="dialog">
    <div class="modal-dialog box-center">
      <div class="modal-content modal-bg modal-bg-error" align="center">
      
        <div class="modal-body">
           <img src="<?php echo base_url('assets/frontend/img/sad-face.png') ?>" />
          <br>
		  <h4 class="text-white"> <b> OOPS ! </b></h4>
          <h4 class="text-white"><?php echo @$this->session->flashdata('topup_error') ?></h4>
		   <br><br>
		  <button type="button" class="btn modal-btn-dark" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- <script>
	  $(document).ready(function(){
		  <?php if($this->session->flashdata('topup_success')){ ?>
			Swal.fire(
				'Successfull!',
				'<?php echo $this->session->flashdata('topup_success'); ?>',
				'success'
				)
		  <?php } ?>
	  })
  </script> -->

<script>
	  $(document).ready(function(){
		  <?php if($this->session->flashdata('topup_success')){ ?>
			$('#successTopUpModal').modal('show');
		  <?php } ?>
	  })
  </script>
 
 <div class="modal fade" id="successTopUpModal" role="dialog">
    <div class="modal-dialog box-center">
      <div class="modal-content modal-bg  modal-bg-success" align="center">
        <!-- <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>   -->
        <div class="modal-body">
         <!--  <i class="fa fa-check-circle text-success" style="font-size:32px; "></i>  -->
          <img src="<?php echo base_url('assets/frontend/img/happy-face.png') ?>" />
          <br>
		  <h4 class="text-white"> <b> Success! </b></h4>
          <p class="text-white"><?php echo @$this->session->flashdata('topup_success') ?></p>
		  <br><br>
		  <button type="button" class="btn modal-btn-dark" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>




	
<script>
$(document).ready(function() {
	$('#timer-ads').each(function() {
		var $this = $(this), finalDate = $(this).data('countdown-ad');
		$this.countdown(finalDate, function(event) {
			$this.html(event.strftime('%M:%S'));
		});
	});
});
</script>	
	

	<?php if($this->session->flashdata('subscription_error')){ ?>
		<script>
		jQuery(document).ready(function ($) {
			jQuery('#subscriptionErrorModal').modal('show');
		}); 
		</script>
	<?php } ?>

	<?php if($this->session->flashdata('topup_error')){ ?>
		<script>
		jQuery(document).ready(function ($) {
			jQuery('#topUpErrorModal').modal('show');
		}); 
		</script>
	<?php } ?>

		<?php 
			// if($this->session->userdata('user_login_type') == 4){
			// 	echo "<br><br><br>";
			// 	include "bkash_footer.php";
			// } 
		?>
</body>
</html>