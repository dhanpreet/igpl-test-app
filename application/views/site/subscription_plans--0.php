<!doctype html>
<html class="no-js" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title> Subscription Plans</title>
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1, user-scalable=0">


	<link rel="stylesheet" href="<?php echo base_url() ?>assets/frontend/css/bootstrap.min.css">
	<script src="<?php echo base_url() ?>assets/frontend/js/jquery.min.js"></script>
	<script src="<?php echo base_url() ?>assets/frontend/js/bootstrap.min.js"></script>
	
	<!-- For fontawesome icons -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/frontend/fontawesome-5.15.1/css/all.css" rel="stylesheet">
	<script defer src="<?php echo base_url() ?>assets/frontend/fontawesome-5.15.1/js/all.js"></script>
	
	
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/frontend/css/style_theme_2.css">
	
	<style>
		.row{
			 max-width:98vw !important;
		}
		
		
		.text-bold{
			font-weight:600;
		}
	
		.btn-filled, .btn-filled:hover, .btn-filled:active, .btn-filled:focus{
			background: #3fd53f !important;
			color: #fff;
			width: inherit;
			min-width: 75%;
			border: 0 !important;
			padding: 8px 15px;
			border-radius: 20px;
			font-weight:600;
		}
		
		.dark-side{
			
			background:#5fc35f !important;
			color:#fff !important;
			border-top-left-radius:10px;
			border-bottom-left-radius:10px;
			height:inherit;
			min-height:175px;
			
			padding:10px;
			vertical-align: middle;
			
		}
		.dark-side-v2{
			
			background:#9c9c9c !important;
			color:#fff !important;
			border-top-left-radius:10px;
			border-bottom-left-radius:10px;
			height:inherit;
			min-height:175px;
			
			padding:10px;
			vertical-align: middle;
			
		}
		
		.light-side{
			
			background:#efefef !important;
			color:#202125 !important;
			border-top-right-radius:10px;
			border-bottom-right-radius:10px;
			height:inherit;
			min-height:175px;
			
			padding:10px;
			vertical-align: middle;
		}


		.come-in {
		  transform: translateY(150px);
		  animation: come-in 0.5s ease forwards;
		}
		.come-in:nth-child(odd) {
		  animation-duration: 0.3s;
		}
		.already-visible {
		  transform: translateY(0);
		  animation: none;
		}

		@keyframes come-in {
		  to { transform: translateY(0); }
		}
	</style>
	
</head>
<body>
<div id="load"></div>

	<section>
		<div class="f1lhk7ql">
			<a href="javascript:history.go(-1)"><img class="f1iowekn" src="<?php echo base_url() ?>assets/frontend/img/icons/back.png" height="14"></a>
			<div class="f1py95a7" style="text-transform: capitalize; color: rgb(255, 255, 255);">Subscription Plans</div>
		</div>
		
		<div class="container">
			<div class="row" style="margin-bottom: 80px">
				<div class="col-xs-12 padd auto-margin games_area"> 
					
				</div>
			</div>
			
			
			<div class="row bg-row module" style="margin:5px auto;">
				<div class="col-8 col-xs-8 col-sm-8 col-md-8 col-lg-8 text-left text-white">
					<i>Become a</i> <h3 style="margin-top:5px !important;">iGPL VIP Member</h3>
				</div>		
				<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 text-center ">
					<img src="<?php echo base_url('assets/frontend/img/vip-access.png') ?>" style="max-width:75%;" />
				</div>	
			</div>
			<div class="row bg-row module" style="margin:5px auto;">
				<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 text-left text-white">
					<br>
					<p><i>To get a VIP access for joining exciting tournaments, you have to buy one of the subscription plan listed below.</i></p>
				</div>		
				
			</div>
			<div class="row"> <br> <br> </div>
			
			
			
			<?php if(is_array($subscriptionPlans) && count($subscriptionPlans)>0 ){ $iCount=1; ?>
				
				<?php foreach($subscriptionPlans as $planRow ){ ?>
				
					<div class="row bg-row module" style="margin:5px auto;">
						
						<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 text-center <?php if($iCount %2 ==0){ echo "dark-side-v2"; } else { echo "dark-side"; } ?>">
						<br>
							<h3 class="text-bold text-white"> 
								<b><?php echo $planRow['sub_plan_amount']; ?></b>
							</h3>
							<h4 class="text-bold text-white">BDT </h4>
							
						
						</div>
						
						
						<div class="col-8 col-xs-8 col-sm-8 col-md-8 col-lg-8 text-left light-side">
							<div class="row">
								<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<h4 class="text-bold"> <?php echo $planRow['sub_plan_name']; ?></h4>
								</div>
							</div>
							
							<div class="row">
								<div class="col-6 col-xs-6 col-sm-6 col-md-6 col-lg-6">
									<p><b><?php echo $planRow['sub_plan_coins']; ?></b> <br>Play Coins</p>
								</div>
								<div class="col-6 col-xs-6 col-sm-6 col-md-6 col-lg-6">
									<p><b><?php echo $planRow['sub_plan_days']; ?> Day(s) </b> <br>Validity</p>
								</div>
							</div>
							<br>
							<p><a href="<?php echo site_url('SubscriptionRequest/'.base64_encode($planRow['sub_plan_sku'])) ?>" class="btn-filled">Subscribe Now</a></p>
						</div>
							
					</div>
				
					<div class="row"> <br> </div>
				
				<?php $iCount++;  } ?>
				<?php } else { ?>
					<div class="row"> <br><br> <br><br> <br><br> <br><br>  </div>
					
					<div class="row">
						<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center"> 
							<h4 class="text-bold" style="margin-top:5px !important;"> No subscription plans found!</h4>
						</div>
					</div>
				
				<?php }  ?>
				
				<div class="row"> <br> <br> <br> <br> <br> </div>
			
		   
		</div>
	</section>
	
	<!-- Footer-Content -->
		<?php // include "footer.php"; ?>
	<!-- Footer Content End -->

<script>
(function($) {

  $.fn.visible = function(partial) {
    
      var $t            = $(this),
          $w            = $(window),
          viewTop       = $w.scrollTop(),
          viewBottom    = viewTop + $w.height(),
          _top          = $t.offset().top,
          _bottom       = _top + $t.height(),
          compareTop    = partial === true ? _bottom : _top,
          compareBottom = partial === true ? _top : _bottom;
    
    return ((compareBottom <= viewBottom) && (compareTop >= viewTop));

  };
    
})(jQuery);

var win = $(window);

var allMods = $(".module");


allMods.each(function(i, el) {
  var el = $(el);
  if (el.visible(true)) {
     el.addClass("already-visible"); 
  } 
});

win.scroll(function(event) {
  
  allMods.each(function(i, el) {
    var el = $(el);
    if (el.visible(true)) {
      el.addClass("come-in"); 
    } 
  });
  
});  
</script>
<script>
jQuery(document).ready(function() {
    jQuery('#load').fadeOut("slow");
});
</script>
</body>
</html>