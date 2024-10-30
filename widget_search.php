<?php
include_once(ABSPATH . WPINC . '/feed.php');

class wpcc_chachaWidgetSearch extends WP_Widget {
	// Declares the wpcc_AuthorWidget class.
	function wpcc_chachaWidgetSearch() {
		$widget_ops = array('classname' => 'widget_ChaCha', 'description' => __( "Widget for displaying specific questions for custom searches information from ChaCha on the sidebar") );
		$control_ops = array('width' => 300, 'height' => 200);
		$this->WP_Widget('wpcc_ChaChaSearch', __('ChaCha Search'), $widget_ops, $control_ops);
	}

	// Displays the Widget
	function widget($args, $instance){
		include_once(ABSPATH . WPINC . '/feed.php');
		extract($args);
		$blogurl = get_bloginfo('url');
		$title = apply_filters('widget_title', empty($instance['titleSearch']) ? '&nbsp;' : $instance['titleSearch']);
		$topic = htmlspecialchars($instance['topicSearch']);
		if(strlen(trim($topic))>0) {
			$topic_url = preg_replace("/[^a-zA-Z0-9\s]/","", $topic);
			$wpcc_url = "http://www.chacha.com/search/".trim(urlencode(strtolower($topic_url)));
			$wpcc_rss = "http://www.chacha.com/search/feed.rss2?query=".trim(urlencode(strtolower($topic_url)));
			$poweredby = ucwords($topic)." Questions Powered by";
			$topic = ucwords($topic)." Questions and Answers";
		} else {
			$wpcc_url = "http://www.chacha.com/";
			$wpcc_rss = "http://www.chacha.com/answers.rss2";
			$topic ="Popular Questions and Answers from ChaCha";
			$poweredby = "Questions by";
		}
		
		$sitename = urlencode(trim(get_bloginfo('name')));
		$campaign = "?utm_source=".$sitename."&utm_medium=Custom%20%Topic%20Widget&utm_campaign=WordPress";

		// Before the widget
		echo $before_widget;

		// The title
		if ($title) { echo $before_title . $title . $after_title; }
		$unique_id = (genRandStr(5, 10, 1, 0, 1, 1));

		if(function_exists('fetch_feed')){
		// Display the Trends Information 
		echo "<div id='chacha_search_".$unique_id."'>\n";
		$rss = fetch_feed($wpcc_rss);
		
		// Figure out how many total items there are, but limit it to 5. 
		$searchQty = htmlspecialchars($instance['searchQty']);
		$maxitems = $rss->get_item_quantity($searchQty);
		
		// Build an array of all the items, starting with element 0 (first element).
		$rss_items = $rss->get_items(0, $maxitems); 
		echo "<ul class=\"ctrotator\" id=\"".$unique_id."\">";
		if ($maxitems == 0) {
			echo "<li>No items.</li>";
		} else {
			// Loop through each feed item and display each item as a hyperlink.
			foreach ( $rss_items as $item ) :
				echo "<li>";
				echo "<a href=\"".$item->get_permalink().$campaign."\" target=\"_blank\" title=\"".$item->get_description()."\">";
				$title = $item->get_title();
				$title = strip_tags($title);
				$title = preg_replace("/[^a-zA-Z0-9\s]/","", $title);
				echo trim($title);
				echo "?</a>";
				echo "</li>\n";
			endforeach;
		}
		echo "</ul>";
		echo "</div>\n";
		echo "<script type=\"text/javascript\">\n";
		echo "jQuery(document).ready(function() {\n";
		echo "var dataSource = new ctRotatorBridgeLi(jQuery('#".$unique_id."')).getDataSource();\n";;
  		echo "jQuery('#".$unique_id."').ctRotator(dataSource, {\n";
    	echo "showCount:5, speed: 10000, useTooltip: true";
  		echo "});\n";
		echo "});\n";
 		echo "</script>\n";
		echo "<p><center>".$poweredby." <a href=\"".$wpcc_url.$campaign."\" title=\"".ucwords($topic)."\" target=\"_blank\">\n";
		echo "<img src=\"".$blogurl."/wp-content/plugins/chacha-answers/images/powered.png\" title=\"".ucwords($topic)."\" />\n";
		echo "</a></center></p>\n";
		}

		// After the widget
		echo $after_widget;
 	}

	// Saves the widgets settings.
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['titleSearch'] = strip_tags(stripslashes($new_instance['titleSearch']));
		$instance['topicSearch'] = strip_tags(stripslashes($new_instance['topicSearch']));
		$instance['searchQty'] = strip_tags(stripslashes($new_instance['searchQty']));
		update_option('wpcc_ChaChaSearch', __($instance, 'wpcc_titleSearch'));

	return $instance;
	}

	// Creates the edit form for the widget.
	function form($instance){
		// Defaults
		$instance = wp_parse_args( (array) $instance, array('titleSearch'=>'ChaCha Search'));
		$title = htmlspecialchars($instance['titleSearch']);
		$topic = htmlspecialchars($instance['topicSearch']);
		$searchQty = htmlspecialchars($instance['searchQty']);


		// Output the options
   		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('titleSearch') . '">' . __('Title:') . ' <input style="width: 250px;" id="' . $this->get_field_id('titleSearch') . '" name="' . $this->get_field_name('titleSearch') . '" type="text" value="' . $title . '" /></label></p>';
		
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('topicSearch') . '">' . __('Search:') . ' <input style="width: 250px;" id="' . $this->get_field_id('topicSearch') . '" name="' . $this->get_field_name('topicSearch') . '" type="text" value="' . $topic . '" /></label></p>';	
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('searchQty') . '">' . __('Quantity:');
		echo wpcc_arraytoSelect(wpcc_getQuantity(), $this->get_field_name('searchQty'), $this->get_field_id('searchQty'), 'searchQty', $searchQty);
		echo '</p>';
 	}

}// END class

function wpcc_getchachaWidgetSearch_callback() {
	$wpcc_username = stripslashes(get_option('wpcc_username'));
	$wpcc_apikey = stripslashes(get_option('wpcc_apikey'));
	
	$query = trim($_POST['query']);
	$results_id = $_POST['results_div_id'];

	$rss = fetch_feed($query);
	$sitename = urlencode(trim(get_bloginfo('name')));
	$campaign = "?utm_source=".$sitename."&utm_medium=Ask%20Widget&utm_campaign=WordPress";
	
	$maxitems = $rss->get_item_quantity(10); 
		
	// Build an array of all the items, starting with element 0 (first element).
	$rss_items = $rss->get_items(0, $maxitems); 
	
	if ($maxitems == 0) {
		$html = "{title: 'Check ChaCha for the latest Questions', url: 'http://www.chacha.com'}";
	} else {
		// Loop through each feed item and display each item JSON record.
		$html = "";
		foreach ( $rss_items as $item ) :
			$html .= "{title: '".$item->get_title()."', url: '".$item->get_permalink().$campaign."'}";
		endforeach;
	}
	
	$html = substr($html, 0, -1);
	
	// Remove single quotes - a bug in WordPress
	$html = str_replace("'","\'",$html);
	
	$error = "";
	
	if( $error ) {
	die("document.getElementById('$results_id').innerHTML = '$error'");
	} 
	
	die('$html');
} ?>