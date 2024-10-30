<?php
/*
Author: Douglas Karr
Author URI: http://www.dknewmedia.com
Description: ChaCha Admin
*/

$url_admin = get_option('siteurl') . '/wp-admin/admin.php?page=chacha-answers/chacha.php'; 
$url_trends = get_option('siteurl') . '/wp-admin/plugins/chacha-answers/trends.php'; 
$url_plugin = dirname(__FILE__);

add_option('wpcc_apikey', __('', 'wpcc'));
add_option('wpcc_track', __('TRUE', 'wpcc'));

if ($_POST['stage']=='process') {
	update_option('wpcc_apikey', trim($_POST['wpcc_apikey']));
	if($_POST['wpcc_track']) {
		update_option('wpcc_track', trim($_POST['wpcc_track']));
	} else { 
		update_option('wpcc_track', 'FALSE');
	}
}

$wpcc_apikey = stripslashes(get_option('wpcc_apikey'));
$wpcc_track = stripslashes(get_option('wpcc_track'));

?>
<?php if ( !empty($_POST['submit'] ) ) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php endif; ?>
<div class="wrap">
    <h2><a href="http://www.chacha.com" target="_blank"><img src="<?php echo $site_url; ?>/wp-content/plugins/chacha-answers/images/logo.png"></a> <?php _e("Configuration",'chacha'); ?></h2>
    <div class="postbox-container" style="width: 600px;">
        <div class="metabox-holder">	
            <div class="meta-box-sortables">
                <form action="" method="post" id="chacha">
                    <div id="chacha_credentials" class="postbox">
						<h3 class="hndle"><span>ChaCha Credentials</span></h3>
						<div class="inside" style="padding:15px">
                            <p><label style="width:100px;text-align:right; float:left; display:block">API Key:</label>&nbsp;<input id="wpcc_apikey" name="wpcc_apikey" type="text" value="<?php echo $wpcc_apikey; ?>" size="40" /></p>
							<p><label style="width:100px;text-align:right; float:left; display:block">Track:</label>&nbsp;<input id="wpcc_track" name="wpcc_track" type="checkbox" value="TRUE" <?php if($wpcc_track=="TRUE") echo "checked=\"checked\"" ?> /> <small>Sends usage statistics to ChaCha.</small></p>
						</div>
					</div>
                    <div class="submit" style="text-align:right">
                    <input type="hidden" name="stage" id="stage" value="process" /> 
                    <input type="submit" class="button-primary" name="submit" value="<?php _e("Update ChaCha Settings", 'wpcc'); ?> &raquo;" />
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include('chacha_sidebar.php'); ?>