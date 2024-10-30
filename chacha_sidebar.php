<?php 
/*
Author: Douglas Karr
Author URI: http://www.dknewmedia.com
Description: ChaCha Sidebar
*/
?>
<div class="postbox-container" style="width:35%; margin-left: 10px">
        <div class="metabox-holder">	
            <div class="meta-box-sortables">
            	<div id="boxfeedback" class="postbox">
					<h3 class="hndle"><span style="background: url(<?php echo $site_url; ?>/wp-content/plugins/chacha-answers/images/cog.png) no-repeat; padding: 0 0 10px 25px;">Feedback</span></h3>
					<div class="inside" style="padding: 10px;">
                        <div id="twitter2" style="min-height:50px">
						<p>
						Suggestions? Ideas? Feedback? Please tell us: <a href="mailto:developer@chacha.com">developer@chacha.com</a>
						</p>
						<div style="height: 30px; margin-top: -10px; background: #fff url(<?php echo $site_url; ?>/wp-content/plugins/chacha-answers/images/bg_chacha.png) bottom right no-repeat"></div>
						</div>
                	</div>
				</div>
                <div id="boxtweets" class="postbox">
					<h3 class="hndle"><span style="background: url(<?php echo $site_url; ?>/wp-content/plugins/chacha-answers/images/twitter.gif) no-repeat; padding: 0 0 10px 25px;">ChaCha on Twitter</span></h3>
					<div class="inside" style="padding: 10px;">
						<div id="tweets" style="min-height:100px"><img src="<?php echo $site_url; ?>/wp-content/plugins/chacha-answers/images/load.gif" style="margin: 20px 0 0 120px;"></div>
						<script id="page" language="javascript" type="text/javascript">
                        jQuery.post(ajaxurl, { action: "wpcc_gettweets" }, function(response) {
                                jQuery("#tweets").html(response).fadeIn();
                        }, "text");
                        </script>
						<div style="height: 35px; background: #fff url(<?php echo $site_url; ?>/wp-content/plugins/chacha-answers/images/bg_chacha.png) bottom right no-repeat"></div>
                   	</div>
				</div>
                <div id="boxabout" class="postbox">
					<h3 class="hndle"><span style="background: url(<?php echo $site_url; ?>/wp-content/plugins/chacha-answers/images/cog.png) no-repeat; padding: 0 0 10px 25px;">About ChaCha</span></h3>
					<div class="inside" style="padding: 10px;">
                        <div id="twitter2" style="min-height:100px">
						<ul style="list-style:square; margin-left: 20px">
							<li><a href="http://www.chacha.com" target="_blank">ChaCha Home</a></li>
							<li><a href="http://answers.chacha.com/about-chacha/how-it-works/" target="_blank">How It Works</a></li>
							<li><a href="http://partners.chacha.com/" target="_blank">National Advertising</a></li>
							<li><a href="http://partners.chacha.com/sms-advertising/local-advertising/" target="_blank">Regional Advertising</a></li>
							<li><a href="http://answers.chacha.com/footer/contact-us" target="_blank">Contact Us</a></li>
						</ul>
						<div style="height: 30px; margin-top: -10px; background: #fff url(<?php echo $site_url; ?>/wp-content/plugins/chacha-answers/images/bg_chacha.png) bottom right no-repeat"></div>
						</div>
                	</div>
				</div>
                <div id="author" style="text-align: center; font-size: 10px">Developed by <a href="http://www.dknewmedia.com">DK New Media, LLC</a></div>
            </div>
        </div>
    </div>
</div>