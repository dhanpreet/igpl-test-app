<!doctype html>
<html class="no-js" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title><?php echo @$genreName ?> Game | iGPL</title>
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
	<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.4/jquery.lazy.min.js"></script>
	
</head>
<body>

<div id="load"></div>
	<section>
		<div class="f1lhk7ql"><a href="<?php echo site_url('') ?>"><img class="f1iowekn" src="<?php echo base_url() ?>assets/frontend/img/icons/back.png" height="14"></a>
		
		<div class="f1py95a7" style="text-transform: capitalize; color: rgb(255, 255, 255);"><?php echo @$genreName ?> Games</div></div>
		<div class="step-container header-padding"></div>
      <div class="container">
        <div class="row" style="margin-bottom: 20px">
			<div class="col-xs-12 padd auto-margin games_area"> 
				<?php if(is_array($gamesList) && count($gamesList)>0){ ?>
					<?php foreach($gamesList as $row){ ?>
						<a href="<?php echo site_url('playGame/'.base64_encode($row['id'])) ?>" >
						  <div class="col-xs-4 padd">
							<div class="thumb-container" data-attr-id="<?php echo base64_encode(@$row['gid']); ?>">
							  <!-- <img class="img-responsive" src="<?php echo @$row['GameImage']; ?>"> -->
							   <img class="img-responsive lazy" data-src="<?php echo base_url('uploads/games/'.$row['ImageName']); ?>"  src="<?php echo base_url() ?>assets/frontend/img/placeholder.gif">
							  <p class="game-name"><?php echo @$row['Name']; ?></p>
							</div>
						  </div>
						</a>
					<?php } ?>
				<?php } ?>
			</div>
        </div>
		
		<div class="row" style="max-width:100vw !important;">
			<a href="https://music.youtube.com/" target="_blank"> 
				<img src="<?php echo base_url('assets/ads/youtube-music.jpg') ?>" style="max-width:99%;"/> 
			</a><br>
		</div>

      <!--   <a class="bottom-fixed-btn"><button type="button" class="btn btn-bottom btn-active">NEXT</button></a>  -->
      </div>
	</section>
	

<div class="modal fade" id="ads_modal" style="margin-top: 1%;" role="dialog">
    <div class="modal-dialog box-center">
      <div class="modal-content modal-bg" align="center">
      
        <div class="modal-body text-dark">
          <a href="https://netflix.com/" target="_blank"> <img src="<?php echo base_url('assets/ads/full-banner-ad.jpg') ?>" style="max-width:99%;" />  </a>
         
		   <br><br>
		  <a class="btn modal-btn-dark-ads" href="javascript:(0);" data-dismiss="modal">Close Ad</a>
		  <br><br>
			<b><i>Hides in <span class="text-dark text-bold" data-countdown-ad="<?php echo date("Y/m/d H:i:s", time() + 7); ?>" id="timer-ads"></span> </i></b>
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
	
		
<script>
jQuery(document).ready(function() {
    jQuery('#load').fadeOut("slow");
});
</script>
 
<script>
!function(window){
  var $q = function(q, res){
        if (document.querySelectorAll) {
          res = document.querySelectorAll(q);
        } else {
          var d=document
            , a=d.styleSheets[0] || d.createStyleSheet();
          a.addRule(q,'f:b');
          for(var l=d.all,b=0,c=[],f=l.length;b<f;b++)
            l[b].currentStyle.f && c.push(l[b]);

          a.removeRule(0);
          res = c;
        }
        return res;
      }
    , addEventListener = function(evt, fn){
        window.addEventListener
          ? this.addEventListener(evt, fn, false)
          : (window.attachEvent)
            ? this.attachEvent('on' + evt, fn)
            : this['on' + evt] = fn;
      }
    , _has = function(obj, key) {
        return Object.prototype.hasOwnProperty.call(obj, key);
      }
    ;

  function loadImage (el, fn) {
    var img = new Image()
      , src = el.getAttribute('data-src');
    img.onload = function() {
      if (!! el.parent)
        el.parent.replaceChild(img, el)
      else
        el.src = src;

      fn? fn() : null;
    }
    img.src = src;
  }

  function elementInViewport(el) {
    var rect = el.getBoundingClientRect()

    return (
       rect.top    >= 0
    && rect.left   >= 0
    && rect.top <= (window.innerHeight || document.documentElement.clientHeight)
    )
  }

    var images = new Array()
      , query = $q('img.lazy')
      , processScroll = function(){
          for (var i = 0; i < images.length; i++) {
            if (elementInViewport(images[i])) {
              loadImage(images[i], function () {
                images.splice(i, i);
              });
            }
          };
        }
      ;
    // Array.prototype.slice.call is not callable under our lovely IE8 
    for (var i = 0; i < query.length; i++) {
      images.push(query[i]);
    };

    processScroll();
    addEventListener('scroll',processScroll);

}(this);
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js" integrity="sha512-lteuRD+aUENrZPTXWFRPTBcDDxIGWe5uu0apPEn+3ZKYDwDaEErIK9rvR0QzUGmUQ55KFE2RqGTVoZsKctGMVw==" crossorigin="anonymous"></script>

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

</body>
</html>