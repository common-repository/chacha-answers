<?php 
/*
Author: Douglas Karr
Author URI: http://www.dknewmedia.com
Description: chacha
*/

$site_url = get_settings('siteurl');
$wpcc_pluginurl = $site_url."/wp-admin/options-general.php?page=chacha-answers/chacha.php"; ?>
<div class="wrap">
    <div style="height: 30px; width: 500px; margin: 15px 0 5px 0;"><a href="http://www.chacha.com" target="_blank"><img src="<?php echo $site_url; ?>/wp-content/plugins/chacha-answers/images/logo.png"></a> <strong>What's happening now?</strong></div>
    <div class="postbox-container" style="width:60%;">
        <div class="metabox-holder">	
            <div class="meta-box-sortables">
                <div id="chacha_trends" class="postbox">
                    <h3 class="hndle"><span>ChaCha Trends</span></h3>
                    <div class="inside" style="padding:15px 15px 5px 15px">
					<p>
					<form id="searchForm" action="http://www.chacha.com/search" method="get" target="_blank">
					<input id="query" name="query" type="text" value="" size="40" maxlength="140" />
					<button type="submit" class="button-primary">Search</button>
					</p>
                    <div id="chacha_data" style="min-height:80px"><img src="<?php echo $site_url; ?>/wp-content/plugins/chacha-answers/images/load.gif" style="margin: 20px 0 0 250px;"></div>
					<script id="page" language="javascript" type="text/javascript">
                        jQuery.post(ajaxurl, { action: "wpcc_getchacha" }, function(response) {
                                jQuery("#chacha_data").html(response).fadeIn();
                        }, "text");
                    </script>
					<p><a href="http://www.chacha.com/" title="ChaCha Answers" target="_blank"><img src="<?php echo $siteurl; ?>/wp-content/plugins/chacha-answers/images/powered.png" title="ChaCha Answers" /></a>&nbsp;Text your question to <strong>242242</strong> or Call <strong>1.800.2ChaCha</strong> (1.800.224.2242)</p>
					<div style="height: 30px; background: #fff url(<?php echo $site_url; ?>/wp-content/plugins/chacha-answers/images/bg_chacha.png) bottom right no-repeat"></div>
                    </div>
                </div>
                <div id="twitter_trends" class="postbox">
                    <h3 class="hndle"><span>Twitter Trends</span></h3>
                    <div class="inside" style="padding:15px 15px 5px 15px">
                    <div id="twitter_data" style="min-height:80px"><img src="<?php echo $site_url; ?>/wp-content/plugins/chacha-answers/images/load.gif" style="margin: 20px 0 0 250px;"></div>
					<script id="page" language="javascript" type="text/javascript">
                        jQuery.post(ajaxurl, { action: "wpcc_gettwitter" }, function(response) {
                                jQuery("#twitter_data").html(response).fadeIn();
                        }, "text");
                    </script>
					<div style="height: 35px; background: #fff url(<?php echo $site_url; ?>/wp-content/plugins/chacha-answers/images/bg_twitter.png) bottom right no-repeat"></div>
                    </div>
                </div>
				<div id="google_trends" class="postbox">
                    <h3 class="hndle"><span>Google Trends</span></h3>
                    <div class="inside" style="padding:15px 15px 5px 15px">
                    <div id="google_data" style="min-height:80px"><img src="<?php echo $site_url; ?>/wp-content/plugins/chacha-answers/images/load.gif" style="margin: 20px 0 0 250px;"></div>
					<script id="page" language="javascript" type="text/javascript">
                        jQuery.post(ajaxurl, { action: "wpcc_getgoogle" }, function(response) {
                                jQuery("#google_data").html(response).fadeIn();
                        }, "text");
                    </script>
					<div style="height: 30px; background: #fff url(<?php echo $site_url; ?>/wp-content/plugins/chacha-answers/images/bg_google.png) bottom right no-repeat"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include('chacha_sidebar.php'); ?>