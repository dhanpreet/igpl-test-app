<!doctype html>
<html class="no-js" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title> TopUp Plans</title>
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
		
		.btn-filled-full, .btn-filled-full:hover, .btn-filled-full:active, .btn-filled-full:focus{
			background: #3fd53f !important;
			color: #fff;
			width: 100%;
			
			border: 0 !important;
			padding: 8px 15px;
			border-radius: 20px;
			font-weight:600;
		}
		
		
		
		.dark-bottom{
			background:#5fc35f !important;
			color:#fff !important;
			border-bottom-left-radius:10px;
			border-bottom-right-radius:10px;
			height:inherit;
			padding:10px;
			vertical-align: middle;
			margin-bottom: 30px !important;
			margin-top: -5px !important;
		}
		
		.dark-bottom > a{
			color:#fff !important;
			font-size:1.25em !important;
		}
		
		.light-side-full{
			background:#efefef !important;
			color:#202125 !important;
			border-top-left-radius:10px;
			border-top-right-radius:10px;
			min-height:inherit;
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
			<div class="f1py95a7" style="text-transform: capitalize; color: rgb(255, 255, 255);">Buy Play Coins</div>
		</div>
		
		<div class="container">
			<div class="row" style="margin-bottom: 80px">
				<div class="col-xs-12 padd auto-margin games_area"> 
					
				</div>
			</div>
			
			<div class="row bg-row module" style="margin:5px auto;">
				<div class="col-8 col-xs-8 col-sm-8 col-md-8 col-lg-8 text-left text-white">
					<i>Buy a plan</i> <h3 style="margin-top:5px !important;">Play Coins Topup </h3>
				</div>		
				<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 text-center ">
					<img src="<?php echo base_url('assets/frontend/img/coin.png') ?>" style="max-width:75%;" />
				</div>	
			</div>
			<div class="row bg-row module" style="margin:5px auto;">
				<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 text-left text-white">
					<br>
					<p><i>To join & play exciting tournaments, buy play coins using one of the topup plan listed below.</i></p>
				</div>		
				
			</div>
			<div class="row"> <br> <br> </div>
			
			<?php if(is_array($subscriptionPlans) && count($subscriptionPlans)>0 ){ $iCount=1; ?>
				<!-- <div class="row text-left "> <h5 class="text-bold">&nbsp;&nbsp; Tournaments History</h5> </div>  -->
				
				<?php foreach($subscriptionPlans as $planRow ){ ?>
				
					<div class="row bg-row module light-side-full" style="margin:5px auto;">
						<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center" style="padding:0 !important;">
							
							<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
								<h4 class="text-bold"> <?php echo $planRow['p_plan']; ?> </h4>
								<h4 class="text-bold"><b> <span style="font-size:1.5em;"><sup>&#2547; </sup></span><span style="font-size:2.25em;"><?php echo number_format($planRow['p_fee'], 2); ?></b></span> 
								</h4>
								<br>
								<h4 class="text-bold" style="font-size:1.5em;">Get <b><?php echo number_format($planRow['p_no_of_coins'], 0); ?></b> Play Coins</h4>
							</div>
							
						
						</div>
						
					</div>	
					<div class="row dark-bottom" style="margin:5px auto;">
						<a href="<?php echo site_url('TopUpRequest/'.base64_encode($planRow['p_plan'])) ?>" >
							<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
								BUY NOW
							</div>
						</a>
					</div>
				
				<?php $iCount++; } ?>
				<?php } else { ?>
					<div class="row"> <br><br> <br><br> <br><br> <br><br>  </div>
					
					<div class="row">
						<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center"> 
							<h4 class="text-bold" style="margin-top:5px !important;"> No Top-Up plans found!</h4>
						</div>
					</div>
				
				<?php }  ?>
				
				<div class="row"> <br> <br> <br> <br> <br> </div>
			
		   
		</div>
	</section>
	
	<!-- Footer-Content -->
		<?php // include "footer.php"; ?>
	<!-- Footer Content End -->
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<!-- <script>
		$(document).ready(function()
		{
				Swal.fire({
				icon: 'error',
				title: 'Oops...',
				text: '<?php 
				// echo $this->session->flashdata("topup"); 
				?>',
				// footer: '<a href="">Why do I have this issue?</a>'
				})
			
		})
	</script> -->

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