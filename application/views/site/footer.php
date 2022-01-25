
<section class="footer-wrapper" align="center">
	<div class="container">
		<div class="row footer-dark <?php if($this->session->userdata('user_login_type') == 4){ ?>footer-dark-bkash<?php } ?>" style="padding:15px">
			<div class="col-2 col-xs-2 col-sm-2 col-md-2 col-lg-2 text-center">
				<a href="<?php echo site_url() ?>" class="<?php if($this->uri->segment(1)=='' || $this->uri->segment(1)=='index'){ echo "theme-color"; }else{ echo "text-white"; } ?>"> 
					<i class="f1h2vptk fa fa-home <?php if($this->uri->segment(1)=='' || $this->uri->segment(1)=='index'){ echo "theme-color"; }else{ echo "text-white"; } ?>"></i><span>
					<br>  Home 
				</a>
			</div>
			
			<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 text-center">
				<a href="#" class="<?php if($this->uri->segment(1)=='TournamentsList'){ echo "theme-color"; }else{ echo "text-white"; } ?>"> 
					<i class="f1h2vptk fa fa-boxes <?php if($this->uri->segment(1)=='TournamentsList'){ echo "theme-color"; }else{ echo "text-white"; } ?>"></i><span>
					<br>  Tournaments
				</a>
			</div>
			
			<div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-4 text-center">
				<a href="#" class="<?php if($this->uri->segment(1)=='vipList'){ echo "theme-color"; }else{ echo "text-white"; } ?>">
					<i class="f1h2vptk fa fa-bookmark <?php if($this->uri->segment(1)=='vipList'){ echo "theme-color"; }else{ echo "text-white"; } ?>"></i><span>
					<br> VIP Room 
				</a>
			</div>
			
			<div class="col-2 col-xs-2 col-sm-2 col-md-2 col-lg-2 text-center">
				<a href="#" class="<?php if($this->uri->segment(1)=='ManageProfile'){ echo "theme-color"; }else{ echo "text-white"; } ?>">
					<i class="f1h2vptk fa fa-user-circle <?php if($this->uri->segment(1)=='ManageProfile'){ echo "theme-color"; }else{ echo "text-white"; } ?>"></i><span>
					<br>Profile 
				 
				 </a>
			</div>
			
	   </div>
	</div>
</section>