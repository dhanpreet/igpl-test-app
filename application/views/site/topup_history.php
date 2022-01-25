<!doctype html>
<html class="no-js" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title>Topup History</title>
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
			
			background:#3fd53f !important;
			color:#fff !important;
			border-radius:10px;
			
			height:inherit;
			min-height:100px;
			
			padding:10px;
			vertical-align: middle;
			
		}
		
		.light-side{
			
			background:#efefef !important;
			color:#202125 !important;
			border-radius:10px;
		
			height:inherit;
			min-height:100px;
			
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
		
		
		.rotate{
			-moz-transition: all 2s linear;
			-webkit-transition: all 2s linear;
			transition: all 2s linear;
		}

		.rotate.down{
			-ms-transform: rotate(180deg);
			-moz-transform: rotate(180deg);
			-webkit-transform: rotate(180deg);
			transform: rotate(180deg);
		}
		
		.circle-div {
			background-color: #3fd53f;
			height: 40px;
			width: 40px;
			border-radius: 100%;
			position: fixed;
			bottom: 21px;
			right: 25px;
			text-align:center;
			vertical-align:middle;
			padding:5px;
			color: #fff;
		}
		
		.float{
			position:fixed;
			width:45px;
			height:45px;
			bottom:20px;
			right:20px;
			background-color:#3fd53f;
			color:#FFF;
			border-radius:50%;
			text-align:center;
			box-shadow: 2px 2px 3px #999;
		}

		.my-float{
			margin-top:16px;
		}
		
	</style>
	
	<style>
	
	</style>
	
</head>
<body>
<div id="load"></div>

	<section>
		<div class="f1lhk7ql">
			<a href="javascript:history.go(-1)"><img class="f1iowekn" src="<?php echo base_url() ?>assets/frontend/img/icons/back.png" height="14"></a>
			<div class="f1py95a7" style="text-transform: capitalize; color: rgb(255, 255, 255);">Topup History</div>
		</div>
		
		<div class="container">
			<div class="row" style="margin-bottom: 80px">
				<div class="col-xs-12 padd auto-margin games_area"> 
					
				</div>
			</div>
			<?php if(is_array($activePlan) && count($activePlan)>0 ){ $iCount= 1; ?>
				<!-- <div class="row text-left "> <h5 class="text-bold">&nbsp;&nbsp; Tournaments History</h5> </div>  -->
				
				<?php foreach($activePlan as $planRow ){ ?>
					<div class="row bg-row module" style="margin:5px auto;">
						<!--<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 text-left <?php if($iCount %2 ==0){ echo "light-side"; } else { echo "dark-side"; } ?>">
						-->
						<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 text-left <?php if($planRow['top_status'] == 'CHARGED'){ echo "dark-side"; } else { echo "light-side"; } ?>">
							<?php if($planRow['top_status']=='CHARGED'){ ?>
								<p class="badge badge-warning">Success</p>
								<?php } else { ?>
								<p class="badge danger-badge">Failed</p>
								<?php } ?>
							<div class="row">
								<div class="col-8 col-xs-8 col-sm-8 col-md-8 col-lg-8">
									<h4 class="text-bold"><b><?php echo $planRow['top_plan'];?></b></h4>
									<p class="text-bold"><i>Topup Plan</i></p>
                                </div>
								
								<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 text-RIGHT">
									<h3 class="text-bold"> <b>&#2547; <?php echo number_format($planRow['top_amount'], 2); ?></b></h3>
								</div>
								
							</div>
						
							<div class="row collapse" id="collapse_<?php echo $iCount; ?>">
								<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<HR>
								</div>
								<div class="col-6 col-xs-6 col-sm-6 col-md-6 col-lg-6">
									<p>Play Coins </p>
									<h4><b>+ <?php echo $planRow['top_no_of_coins']; ?> </b></h4>
								</div>
								
								<div class="col-6 col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
									<p>Purchased On </p>
									<h4><b><?php echo date('Fj, Y' ,strtotime($planRow['top_created_at'])); ?></b></h4>
								</div>
							</div>
							
							<div class="row">
								<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right" style="padding-right:30px;">
									<a class="text-white rotate" data-toggle="collapse" href="#collapse_<?php echo $iCount; ?>"><i class="fa fa-angle-up fa-lg"></i> </a>
								</div>
							</div>
						</div>
					</div>
					<div class="row"> <br> </div>
				
				<?php $iCount++; } ?>
				
			
				<?php } else { ?>
					<div class="row"> <br><br> <br><br> <br><br> <br><br>  </div>
					
					<div class="row">
						<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center"> 
							<h4 class="text-bold text-white" style="margin-top:5px !important;"> No Topup found!</h4>
                           <h4 class="text-bold text-white" style="margin-top:5px !important;"> 
						   Add play coins to your account to play & win exciting tournaments </h4> 
						   <br><a class="btn-filled" href="<?php echo site_url('UserSubscription/TOPUP'); ?> "> Topup Now</a>
						  
						</div>
					</div>
				
				<?php }  ?>
				
				<div class="row"> <br> <br> <br> <br> <br> </div>
			
		   
		</div>
	</section>
	
	<!--
	<a href="<?php echo site_url('UserSubscription/TOPUP'); ?>"> 
	<div class="circle-div"><i class="fa fa-plus fa-2x"></i></div>
	</a>
	-->
	
	<a href="<?php echo site_url('UserSubscription/TOPUP'); ?>" class="float">
	<i class="fa fa-plus my-float"></i>
	</a>
	
	
	<!-- Footer-Content -->
		<?php //include "footer.php"; ?>
	<!-- Footer Content End -->
<script>
    function verifyCancel(id)
    {
       var btn = confirm("Are you sure?")
    }
</script>
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

<script>
jQuery(document).ready(function() {
   jQuery(".rotate").click(function(){
		jQuery(this).toggleClass("down"); 
	});
});
</script>



</body>
</html>