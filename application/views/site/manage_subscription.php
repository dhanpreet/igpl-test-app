<!doctype html>
<html class="no-js" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title>My Subscriptions</title>
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
			min-height:175px;
			
			padding:10px;
			vertical-align: middle;
			
		}
		
		.light-side{
			
			background:#efefef !important;
			color:#202125 !important;
			border-radius:10px;
			
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
			<div class="f1py95a7" style="text-transform: capitalize; color: rgb(255, 255, 255);">My Active Subscriptions </div>
		</div>
		
		<div class="container">
			<div class="row" style="margin-bottom: 80px">
				<div class="col-xs-12 padd auto-margin games_area"> 
					
				</div>
			</div>
			<?php if(is_array($activePlan) && count($activePlan)>0 ){ ?>
				
				<?php foreach($activePlan as $planRow ){ 
					
					?>
				
					<div class="row bg-row module" style="margin:5px auto;">
						
						<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 text-left light-side">
							<div class="row">
								<div class="col-8 col-xs-8 col-sm-8 col-md-8 col-lg-8">
									<h4 class="text-bold"><b><?php echo $planRow['sub_cycle']; ?></b></h4>
									<p class="text-bold"><i>Subscription</i></p>
                                </div>
								
								<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 text-center">
									<h3 class="text-bold"> <b>&#2547; <?php echo number_format($planRow['sub_amount'], 2); ?></b></h3>
									
                                </div>
							</div>
							
							<div class="row">
								
								<div class="col-8 col-xs-8 col-sm-8 col-md-8 col-lg-8">
									<p>Valid Upto <br><b><?php echo date('M j', strtotime($planRow['sub_startDate'])); ?> - <?php echo date('M j, Y', strtotime($planRow['sub_endDate'])); ?> </b></p>
								</div>
								
								<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 text-left">
									<p>Next Renewal <br><b><?php echo date('M j, Y', strtotime($planRow['sub_endDate'] . ' +1 day')); ?> </b></p>
								</div>
								
								<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<br>
									<?php if($planRow['sub_status']==1){ ?>
										<p><a onclick="verifyCancel(<?php echo $planRow['sub_subscription_id'] ?>)" class="btn-filled">Cancel Subscription</a></p>
										<?php } else { ?>
											<?php if($planRow['sub_status']==5) {?>
												<p>Cancellation is in progress.</p>
											<?php } else { ?>
										<p><a class="btn-filled">Inactive</a></p>
											<?php } ?>
										<?php } ?>
								</div>
								
								
							</div>
							<br>
							
							
							
							
							
						</div>
							
					</div>
				
					<div class="row"> <br> </div>
				<?php } ?>
				
				<?php } else { ?>
					<div class="row"> <br><br> <br><br> <br><br> <br><br>  </div>
					
					<div class="row">
						<div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center"> 
							<h4 class="text-bold text-white" style="margin-top:5px !important;"> No active subscription found!</h4>
                          
							<h4 class="text-bold text-white" style="margin-top:5px !important;">
								To become a VIP member  
							</h4>
							<br><a  class="btn-filled" href="<?php echo site_url('UserSubscription'); ?> ">Subscribe Now</a> 
							
						</div>
					</div>
				
				<?php }  ?>
				
				<div class="row"> <br> <br> <br> <br> <br> </div>
			
		   
		</div>
	</section>
					
	<div class="modal fade" id="verifyCancel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content modal-bg modal-bg-error">
			
				<div class="modal-body text-center">
				 <br>
				 
				  <h4 class="text-white">Are you sure you want to cancel this subscription?</h4>
				  <p>By cancelling the subscription you are no longer able to access the VIP tournamnets.</p>
				   <br><br>
					<form action="<?php echo site_url('CancelSubscription');?>" method="post" id="cancel">
						<input type="hidden" name="id" value=''  id="cancel_id">
					</form>
					 <br>
					<button onclick="submitForm();" class="btn btn-filled">Cancel</button>
					 <br> <br>
					<a data-dismiss="modal">Close</a>
					
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

	

	<!-- Footer-Content -->
		<?php //include "footer.php"; ?>
	<!-- Footer Content End -->
	<script>
		function submitForm()
		{
			$('#cancel').submit();
		}
	</script>
<script>
    function verifyCancel(id)
    {
		$('#cancel_id').val(id);
       $('#verifyCancel').modal('show');
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
</body>
</html>