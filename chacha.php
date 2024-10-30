<?php
/*
Plugin Name: ChaCha Answers
Plugin URI: http://marketingtechblog.com/projects/chacha/
Description: A plugin for integrating <a href="http://www.chacha.com/?&utm_medium=Plugin&utm_campaign=WordPress" target="_blank">ChaCha</a> to your WordPress Blog and to get the latest trends from ChaCha, Twitter and Google to your Dashboard.
Version: 2.2.7
Author: Douglas Karr
Author URI: http://www.dknewmedia.com/
*/

load_plugin_textdomain('wpcc', $path = 'wp-content/plugins/chacha-answers');

$wpcc_apikey = stripslashes(get_option('wpcc_apikey'));

if ( $wpcc_apikey =="" ) : ?>
<div id="message" class="error fade"><p><strong><?php _e('Attention Required.') ?></strong> You must register for an API Key from the <a href="http://developer.chacha.com">ChaCha Developer Network</a> to enable the widgets for the ChaCha plugin.  Update <a href="<?php echo $site_url; ?>/wp-admin/options-general.php?page=chacha-answers/chacha.php">ChaCha settings</a> with your API Key.</p></div>
<?php endif;

// Check to ensure that the user has PHP 5 or greater
define('CHACHA_COMPATIBLE', version_compare(phpversion(), '5', '>='));
if (!CHACHA_COMPATIBLE) {
	trigger_error('ChaCha requires PHP 5 or greater.', E_USER_ERROR);
}

// Include WordPress RSS capabilities
include_once(ABSPATH . WPINC . '/feed.php');
// Include the widget to ask a question to ChaCha
include_once(dirname(__FILE__).'/widget_ask.php');
// Include the widget to get the latest questions in a ChaCha topic of your choice
include_once(dirname(__FILE__).'/widget_topics.php');
// Include the widget to get the latest ChaCha trends
include_once(dirname(__FILE__).'/widget_trends.php');
// Include the widget to get the latest ChaCha questions with a keyword of your choice
include_once(dirname(__FILE__).'/widget_search.php');
// Include the functions to retrieve trending data from ChaCha, Twitter and Google
include_once(dirname(__FILE__).'/admin_trends.php');

// Add the menus to WordPress
function wpcc_addchacha() {
	// Adds the Trends Menu > ChaCha Trends Option
	if (function_exists('add_menu_page')) {
		$page = add_submenu_page('index.php', __('ChaCha Trends'), __('ChaCha Trends'), '0', 'ChaCha', 'wpcc_addchachatrends_page');
		add_action('admin_print_scripts-'.$page, 'wpcc_javascript');
	}
	// Adds the Settings Menu > ChaCha Option
	if (function_exists('add_options_page')) {
		$settings = add_options_page('ChaCha', 'ChaCha', 'administrator', __FILE__, 'wpcc_addchachaadmin_page');
		add_action('admin_print_scripts-'.$settings, 'wpcc_javascript');
    }
}

// Specify the location of the admin page
function wpcc_addchachaadmin_page() {
    include(dirname(__FILE__).'/admin.php');
}
// Specify the location of the trends page
function wpcc_addchachatrends_page() {
    include(dirname(__FILE__).'/trends.php');
}

// Add tracking code to observe usage in the footer
function wpcc_code() {
	$wpcc_track = get_option('wpcc_track');
	if($wpcc_track == "TRUE") {
		echo "\n".'<!-- Begin ChaCha WordPress Plugin Usage Stats-->';
		echo "<iframe src='http://www.chacha.com/wordpressAnalytics.html' height='1px' width='1px'></iframe>";
		echo '<!-- End ChaCha -->'."\n";
	}
}

// General request function with cURL
function wpccRequest($apikey, $url) {
	// create a new cURL resource
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('apikey:'.$apikey));
	$referrer = get_option('siteurl');
	curl_setopt($ch, CURLOPT_REFERER, $referrer);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

// Load jQuery in the User Interface for Ajax Calls
function wpcc_javascript() {
	wp_enqueue_script('wpcc_jqry');
}
function wpcc_getchachaWidget_jquery() {
	wp_enqueue_script('jQuery'); 
}

// Register each of the Widgets
function wpcc_chachaInit() {
	register_widget('wpcc_chachaWidgetSearch');
	register_widget('wpcc_chachaWidgetTrends');
	register_widget('wpcc_chachaWidgetTopic');
	register_widget('wpcc_chachaWidgetAsk');
}

// Load the javascript includes in the User Interface for the streaming RSS
function wpcc_ctrotator_scripts ( ) {
  wp_enqueue_script( "ctrotator", path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/js/jquery.ctrotator.js"), array( 'jquery' ) );
  wp_enqueue_script( "ctrotator-li", path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/js/jquery.ctrotator.bridge.li.js"), array( 'jquery' ) );
  wp_enqueue_script( "tooltip", path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/js/jquery.tooltip.min.js"), array( 'jquery' ) );
}
	
function wpcc_getchachaWidget_js() {
	// Loads the SACK transport for making AJAX calls
	wp_print_scripts( array( 'sack' ));
	?>
	<script type="text/javascript">
	//<![CDATA[
	function getChaCha( query, results_div, action) {
		var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );    
		mysack.execute = 1;
		mysack.method = 'POST';
		mysack.setVar( "action", action );
  		mysack.setVar( "query", query.value );
  		mysack.setVar( "results_div_id", results_div );
		mysack.onError = function() { var resp = 'There was a problem with your request';};
		mysack.runAJAX();
		return true;
	}
	//]]>
	</script>
	<?php
}

function genRandStr($minLen, $maxLen, $alphaLower = 1, $alphaUpper = 1, $num = 1, $batch = 1) {
    
    $alphaLowerArray = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
    $alphaUpperArray = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $numArray = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
    
    if (isset($minLen) && isset($maxLen)) {
        if ($minLen == $maxLen) {
            $strLen = $minLen;
        } else {
            $strLen = rand($minLen, $maxLen);
        }
        $merged = array_merge($alphaLowerArray, $alphaUpperArray, $numArray);
        
        if ($alphaLower == 1 && $alphaUpper == 1 && $num == 1) {
            $finalArray = array_merge($alphaLowerArray, $alphaUpperArray, $numArray);
        } elseif ($alphaLower == 1 && $alphaUpper == 1 && $num == 0) {
            $finalArray = array_merge($alphaLowerArray, $alphaUpperArray);
        } elseif ($alphaLower == 1 && $alphaUpper == 0 && $num == 1) {
            $finalArray = array_merge($alphaLowerArray, $numArray);
        } elseif ($alphaLower == 0 && $alphaUpper == 1 && $num == 1) {
            $finalArray = array_merge($alphaUpperArray, $numArray);
        } elseif ($alphaLower == 1 && $alphaUpper == 0 && $num == 0) {
            $finalArray = $alphaLowerArray;
        } elseif ($alphaLower == 0 && $alphaUpper == 1 && $num == 0) {
            $finalArray = $alphaUpperArray;                        
        } elseif ($alphaLower == 0 && $alphaUpper == 0 && $num == 1) {
            $finalArray = $numArray;
        } else {
            return FALSE;
        }
        
        $count = count($finalArray);
        
        if ($batch == 1) {
            $str = '';
            $i = 1;
            while ($i <= $strLen) {
                $rand = rand(0, $count);
                $newChar = $finalArray[$rand];
                $str .= $newChar;
                $i++;
            }
            $result = $str;
        } else {
            $j = 1;
            $result = array();
            while ($j <= $batch) { 
                $str = '';
                $i = 1;
                while ($i <= $strLen) {
                    $rand = rand(0, $count);
                    $newChar = $finalArray[$rand];
                    $str .= $newChar;
                    $i++;
                }
                $result[] = $str;
                $j++;
            }
        }
        
        return $result;
    }
}

function wpcc_arraytoSelect($array, $name="", $id="", $class="", $selected="") {
	if(is_array($array)) {
		$html = "<select name=\"".$name."\" id=\"".$id."\" class=\"".$class."\">";
		foreach ($array as $key => $value) { 
			$html .= "<option value=\"".$key."\"";
			if($selected==$key) {
				$html .= " selected";
			}
			$html .=">";
			$html .= $value;
			$html .= "</option>";
		} 
		$html .= "</select>";
	} else {
		$html = "Oops!";	
	}
	return $html;
}

function wpcc_getTopics($topic="") {
	$arrTopics = array(
			'addiction-drug-abuse'=>'Addiction &amp; Drug Abuse',
			'advertising-marketing'=>'Advertising &amp; Marketing',
			'air-travel'=>'Air Travel',
			'animals-plants'=>'Animals &amp; Plants',
			'art-design'=>'Art &amp; Design',
			'astronomy'=>'Astronomy',
			'attractions-destinations'=>'Attractions &amp; Destinations',
			'automotive'=>'Automotive',
			'bars-nightlife'=>'Bars &amp; Nightlife',
			'baseball'=>'Baseball',
			'basketball'=>'Basketball',
			'biology'=>'Biology',
			'business'=>'Business',
			'campaigns-elections'=>'Campaigns &amp; Elections',
			'careers-employment'=>'Careers &amp; Employment',
			'celebrities'=>'Celebrities',
			'chacha-info'=>'ChaCha Info',
			'chemistry'=>'Chemistry',
			'christianity'=>'Christianity',
			'computers'=>'Computers',
			'conditions-illness'=>'Conditions &amp; Illness',
			'consumer-electronics'=>'Consumer Electronics',
			'conversational'=>'Conversational',
			'corporate-info'=>'Corporate Info',
			'coupons'=>'Coupons',
			'cultures-groups'=>'Cultures &amp; Groups',
			'definitions'=>'Definitions',
			'demographics'=>'Demographics',
			'diet-nutrition'=>'Diet &amp; Nutrition',
			'dining-out'=>'Dining Out',
			'directions-traffic'=>'Directions &amp; Traffic',
			'earth-sciences'=>'Earth Sciences',
			'education'=>'Education',
			'entertainment-arts'=>'Entertainment &amp; Arts',
			'events'=>'Events',
			'family'=>'Family',
			'finance-economy'=>'Finance &amp; Economy',
			'fitness'=>'Fitness',
			'food-drink'=>'Food &amp; Drink',
			'football'=>'Football',
			'games'=>'Games',
			'geography'=>'Geography',
			'glbt'=>'GLBT',
			'golf'=>'Golf',
			'green-living'=>'Green Living',
			'health'=>'Health',
			'historic-figures'=>'Historic Figures',
			'history'=>'History',
			'hockey'=>'Hockey',
			'home-garden'=>'Home &amp; Garden',
			'humor'=>'Humor',
			'illicit-drugs'=>'Illicit Drugs',
			'internet'=>'Internet',
			'islam'=>'Islam',
			'judaism'=>'Judaism',
			'language-lookup'=>'Language &amp; Lookup',
			'law'=>'Law',
			'lifestyle'=>'Lifestyle',
			'literature'=>'Literature',
			'mail-shipping'=>'Mail &amp; Shipping',
			'mass-transit'=>'Mass Transit',
			'math'=>'Math',
			'medicine-treatment'=>'Medicine &amp; Treatment',
			'military'=>'Military',
			'mormonism'=>'Mormonism',
			'motorsports'=>'Motorsports',
			'movie-showings'=>'Movie Showings',
			'movies'=>'Movies',
			'music'=>'Music',
			'mythology-folklore'=>'Mythology &amp; Folklore',
			'name-etymology'=>'Name Etymology',
			'olympics'=>'Olympics',
			'pets'=>'Pets',
			'physics'=>'Physics',
			'political-figures'=>'Political Figures',
			'politics-gov'=>'Politics &amp; Gov',
			'psychology'=>'Psychology',
			'puzzles'=>'Puzzles',
			'real-estate'=>'Real Estate',
			'recreation'=>'Recreation',
			'relationships-dating'=>'Relationships &amp; Dating',
			'religion-spirituality'=>'Religion &amp; Spirituality',
			'restaurant-coupons'=>'Restaurant Coupons',
			'science'=>'Science',
			'scitech'=>'SciTech',
			'scores'=>'Scores',
			'sex-industry'=>'Sex Industry',
			'sexual-orientation'=>'Sexual Orientation',
			'shopping'=>'Shopping',
			'small-business'=>'Small Business',
			'soccer'=>'Soccer',
			'society-culture'=>'Society &amp; Culture',
			'song-lyrics'=>'Song Lyrics',
			'sports'=>'Sports',
			'state-local-government'=>'State &amp; Local Government',
			'stocks-stock-market'=>'Stocks &amp; Stock Market',
			'style-beauty'=>'Style &amp; Beauty',
			'tennis'=>'Tennis',
			'theater'=>'Theater',
			'translations'=>'Translations',
			'travel'=>'Travel',
			'tv-radio'=>'TV &amp; Radio',
			'u.s.-government'=>'U.S. Government',
			'vacations'=>'Vacations',
			'wars-regional-conflicts'=>'Wars &amp; Regional Conflicts',
			'weather-time'=>'Weather &amp; Time',
			'womens-health'=>'Women\'s Health',
			'world-governments'=>'World Governments',
			'yellow-pages'=>'Yellow Pages');
	if(strlen($topic)<1) {
		return $arrTopics;
	} else {
		return $arrTopics[$topic];
	}
}

function wpcc_getQuantity($qty="") {
	$arrQuantity = array(
			'100'=>'All',
			'75'=>'75',
			'50'=>'50',
			'25'=>'25',
			'10'=>'10',
			'5'=>'5'
			);
	if(strlen($topic)<1) {
		return $arrQuantity;
	} else {
		return $arrQuantity[$qty];
	}
}

add_action('init', 'wpcc_getchachaWidget_jquery');
add_action('wp_head', 'wpcc_getchachaWidget_js' );
add_action('admin_menu', 'wpcc_addchacha');
add_action('wp_footer','wpcc_code',90);
add_action('wp_ajax_wpcc_getchacha', 'wpcc_getchacha' );
add_action('wp_ajax_wpcc_getgoogle', 'wpcc_getgoogle' );
add_action('wp_ajax_wpcc_gettwitter', 'wpcc_gettwitter' );
add_action('wp_ajax_wpcc_gettweets', 'wpcc_gettweets' );
add_action('wp_ajax_wpcc_getchachaWidgetTrends', 'wpcc_getchachaWidgetTrends_callback');
add_action('wp_ajax_nopriv_wpcc_getchachaWidgetTrends', 'wpcc_getchachaWidgetTrends_callback');
add_action('wp_ajax_wpcc_getchachaWidgetAsk', 'wpcc_getchachaWidgetAsk_callback');
add_action('wp_ajax_nopriv_wpcc_getchachaWidgetAsk', 'wpcc_getchachaWidgetAsk_callback');
add_action('wp_ajax_wpcc_getchachaWidgetTopic', 'wpcc_getchachaWidgetTopic_callback');
add_action('wp_ajax_nopriv_wpcc_getchachaWidgetTopic', 'wpcc_getchachaWidgetTopic_callback');
add_action('wp_ajax_wpcc_getchachaWidgetSearch', 'wpcc_getchachaWidgetSearch_callback');
add_action('wp_ajax_nopriv_wpcc_getchachaWidgetSearch', 'wpcc_getchachaWidgetSearch_callback');
add_action('admin_header', 'wpcc_javascript');
add_action('wp_print_scripts', 'wpcc_ctrotator_scripts');
add_action('widgets_init', 'wpcc_chachaInit');
?>