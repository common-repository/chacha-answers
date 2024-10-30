<?php

class wpcc_chachaWidgetTrends extends WP_Widget {
	// Declares the wpcc_AuthorWidget class.
	function wpcc_chachaWidgetTrends() {
		$widget_ops = array('classname' => 'widget_ChaCha', 'description' => __( "Widget for displaying the latest trending topics from ChaCha on the sidebar") );
		$control_ops = array('width' => 300, 'height' => 200);
		$this->WP_Widget('wpcc_ChaChaTrends', __('ChaCha Trends'), $widget_ops, $control_ops);
	}

	// Displays the Widget
	function widget($args, $instance){
		extract($args);
		$blogurl = get_bloginfo('url');
		$title = apply_filters('widget_title', empty($instance['titleTrends']) ? '&nbsp;' : $instance['titleTrends']);

		// Before the widget
		echo $before_widget;

		// The title
		if ($title) { echo $before_title . $title . $after_title; }

		// Display the Trends Information 
		echo "<div id='chacha_trends' style='min-height:100px'>\n";
		echo "<img src='".$site_url."/wp-content/plugins/chacha-answers/images/load.gif' style='margin: 20px 50px;'>\n";
		echo "</div>";
		echo "<script type='text/javascript'>\n";
		echo "jQuery(document).ready(function() {\n";
		echo "getChaCha('trends','chacha_trends','wpcc_getchachaWidgetTrends');\n";
		echo "});\n";
		echo "</script>\n";
		echo "<center>Trends Powered by<a href=\"http://www.chacha.com\" title=\"ChaCha Answers\" target=\"_blank\">\n";
		echo "<img src=\"".$blogurl."/wp-content/plugins/chacha-answers/images/powered.png\" title=\"ChaCha Answers\" />\n";
		echo "</a></center>\n";

		// After the widget
		echo $after_widget;
 	}

	// Saves the widgets settings.
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['titleTrends'] = strip_tags(stripslashes($new_instance['titleTrends']));
		$instance['trendQty'] = strip_tags(stripslashes($new_instance['trendQty']));
		update_option('wpcc_ChaChaTrends', __($instance, 'wpcc_titleTrends'));

	return $instance;
	}

	// Creates the edit form for the widget.
	function form($instance){
		// Defaults
		$instance = wp_parse_args( (array) $instance, array('titleTrends'=>'ChaCha Trends'));
		$title = htmlspecialchars($instance['titleTrends']);

		// Output the options
   		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('titleTrends') . '">' . __('Title:') . ' <input style="width: 250px;" id="' . $this->get_field_id('titleTrends') . '" name="' . $this->get_field_name('titleTrends') . '" type="text" value="' . $title . '" /></label></p>';
   		
 	}

}// END class

function wpcc_getchachaWidgetTrends_callback() {
	$wpcc_apikey = stripslashes(get_option('wpcc_apikey'));
	
	$url = 'http://query.chacha.com/trends/getTrends?source=RawQuery&count=30&prettyOutput=true';
	$response = wpccRequest($wpcc_apikey, $url);
	$arr = json_decode($response, true);
	
	$results_id = $_POST['results_div_id'];
	
	$sitename = urlencode(trim(get_bloginfo('name')));
	$campaign = "?utm_source=".$sitename."&utm_medium=Trends%20Widget&utm_campaign=WordPress";
	
	$results .= "<ul>";
	foreach ($arr as $key => $value) {
		if($key=="trends") {
    		$arrtrends = array_unique($value);
			$trends = array_slice($arrtrends, 0, 10);
			foreach ($trends as $trend) {
				$results .= "<li><a href=\"http://www.chacha.com/search/".urlencode($trend).$campaign."\" target=\"_blank\">";
				$results .= $trend;
				$results .= "</a></li>";
			}
		}
	}
	$results .= "</ul>";
	
	$results = str_replace("'","",$results);
	
	$error = "";
	
	if( $error ) {
	die("document.getElementById('$results_id').innerHTML = '$error'");
	} 
	
	die("document.getElementById('$results_id').innerHTML = '$results'");
} ?>