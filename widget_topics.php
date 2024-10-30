<?php

class wpcc_chachaWidgetTopic extends WP_Widget {
	// Declares the wpcc_AuthorWidget class.
	function wpcc_chachaWidgetTopic() {
		$widget_ops = array('classname' => 'widget_ChaCha', 'description' => __( "Widget for displaying specific questions for custom searches information from ChaCha on the sidebar") );
		$control_ops = array('width' => 300, 'height' => 200);
		$this->WP_Widget('wpcc_ChaChaTopic', __('ChaCha Topic'), $widget_ops, $control_ops);
	}

	// Displays the Widget
	function widget($args, $instance){
		include_once(ABSPATH . WPINC . '/feed.php');
		extract($args);
		$blogurl = get_bloginfo('url');
		$title = apply_filters('widget_title', empty($instance['titleTopic']) ? '&nbsp;' : $instance['titleTopic']);
		$topic = htmlspecialchars($instance['topicTopic']);
		if(strlen(trim($topic))>0) {
			$wpcc_url = "http://www.chacha.com/category/".$topic;
			$wpcc_rss = "http://www.chacha.com/category/".$topic."/feed.rss2";
			$topic = wpcc_getTopics($topic);
			$poweredby = $topic." Questions Powered by";
			$topic = $topic." Questions and Answers";
		} else {
			$wpcc_url = "http://www.chacha.com/";
			$wpcc_rss = "http://www.chacha.com/answers.rss2";
			$topic ="ChaCha Popular Questions and Answers";
			$poweredby = "Answers by";
		}
		
		$sitename = urlencode(trim(get_bloginfo('name')));
		$campaign = "?utm_source=".$sitename."&utm_medium=Custom%20%Topic%20Widget&utm_campaign=WordPress";

		// Before the widget
		echo $before_widget;

		// The title
		if ($title) { echo $before_title . $title . $after_title; }
		$unique_id = (genRandStr(5, 10, 1, 0, 1, 1));

		// Display the Trends Information 
		echo "<div id='chacha_search_".$unique_id."'>\n";
		$rss = fetch_feed($wpcc_rss);
		
		// Figure out how many total items there are, but limit it to 5. 
		$topicQty = htmlspecialchars($instance['topicQty']);
		$maxitems = $rss->get_item_quantity($topicQty); 
		
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
		echo "<p>";
		echo "<center>".$poweredby." <a href=\"".$wpcc_url.$campaign."\" title=\"".ucwords($topic)."\" target=\"_blank\">\n";
		echo "<img src=\"".$blogurl."/wp-content/plugins/chacha-answers/images/powered.png\" title=\"".ucwords($topic)."\" />\n";
		echo "</a></center></p>\n";

		// After the widget
		echo $after_widget;
 	}

	// Saves the widgets settings.
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['titleTopic'] = strip_tags(stripslashes($new_instance['titleTopic']));
		$instance['topicTopic'] = strip_tags(stripslashes($new_instance['topicTopic']));
		$instance['topicQty'] = strip_tags(stripslashes($new_instance['topicQty']));
		update_option('wpcc_ChaChaTopic', __($instance, 'wpcc_titleTopic'));

	return $instance;
	}

	// Creates the edit form for the widget.
	function form($instance){
		// Defaults
		$instance = wp_parse_args( (array) $instance, array('titleTopic'=>'ChaCha Topic'));
		$title = htmlspecialchars($instance['titleTopic']);
		$topic = htmlspecialchars($instance['topicTopic']);
		$topicQty = htmlspecialchars($instance['topicQty']);

		// Output the options
   		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('titleTopic') . '">' . __('Title:') . ' <input style="width: 250px;" id="' . $this->get_field_id('titleTopic') . '" name="' . $this->get_field_name('titleTopic') . '" type="text" value="' . $title . '" /></label></p>';
		
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('titleTopic') . '">' . __('Topic:');
		echo wpcc_arraytoSelect(wpcc_getTopics(), $this->get_field_name('topicTopic'), $this->get_field_id('topicTopic'), 'topicTopic', $topic);
		echo '</p>';	
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('titleQty') . '">' . __('Quantity:');
		echo wpcc_arraytoSelect(wpcc_getQuantity(), $this->get_field_name('topicQty'), $this->get_field_id('topicQty'), 'topicQty', $topicQty);
		echo '</p>';
		
 	}

}// END class

function wpcc_getchachaWidgetTopic_callback() {
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