<?php

class wpcc_chachaWidgetAsk extends WP_Widget {
	// Declares the wpcc_AuthorWidget class.
	function wpcc_chachaWidgetAsk() {
		$widget_ops = array('classname' => 'widget_ChaCha', 'description' => __( "Widget for displaying a form that asks ChaCha a question and answers it in real-time.") );
		$control_ops = array('width' => 300, 'height' => 200);
		$this->WP_Widget('wpcc_ChaChaAsk', __('Ask ChaCha'), $widget_ops, $control_ops);
	}

	// Displays the Widget
	function widget($args, $instance){
		extract($args);
		$blogurl = get_bloginfo('url');
		
		$title = apply_filters('widget_title', empty($instance['titleAsk']) ? '&nbsp;' : $instance['titleAsk']);
		
		$sitename = urlencode(trim(get_bloginfo('name')));
		$campaign = "?utm_source=".$sitename."&utm_medium=Ask%20Widget&utm_campaign=WordPress";

		// Before the widget
		echo $before_widget;

		// The title
		if ($title) { echo $before_title . $title . $after_title; }

		// Display the Trends Information 
		echo "<div id='chacha_ask' style='margin-bottom: 6px'>\n";
		echo "<center><form name=\"chacha_ask\" onsubmit=\"getChaCha(this.form.text_chacha,'answer_chacha','wpcc_getchachaWidgetAsk');return false;\">";
		echo "<input type=\"text\" id=\"text_chacha\" maxlength=\"140\"/>";
		echo "<script type=\"text/javascript\">\n";
		echo "jQuery(function(){";
    	echo "jQuery('input#text_chacha').keydown(function(e){";
        echo "if (e.keyCode == 13) {";
		echo "var query = this.form.text_chacha;";
        echo "getChaCha(query,'answer_chacha','wpcc_getchachaWidgetAsk');";
        echo "return false;";
        echo "}";
    	echo "});";
		echo "});";
		echo "</script>\n";
		echo "<input type=\"button\" value=\"Ask!\" onclick=\"getChaCha(this.form.text_chacha,'answer_chacha','wpcc_getchachaWidgetAsk');\">"; 
		echo "</form></center>";
		echo "<span id=\"answer_chacha\">&nbsp;</span>\n";
		echo "</div>";
		echo "<p><center>Answers Powered by <a href=\"http://www.chacha.com".$campaign."\" title=\"Ask ChaCha\" target=\"_blank\">\n";
		echo "<img src=\"".$blogurl."/wp-content/plugins/chacha-answers/images/powered.png\" title=\"Ask ChaCha\" />\n";
		echo "</a></center></p>\n";

		// After the widget
		echo $after_widget;
 	}

	// Saves the widgets settings.
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['titleAsk'] = strip_tags(stripslashes($new_instance['titleAsk']));
		update_option('wpcc_ChaChaAsk', __($instance, 'wpcc_titleAsk'));

	return $instance;
	}

	// Creates the edit form for the widget.
	function form($instance){
		// Defaults
		$instance = wp_parse_args( (array) $instance, array('titleAsk'=>'Ask ChaCha'));
		$title = htmlspecialchars($instance['titleAsk']);

		// Output the options
   		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('titleAsk') . '">' . __('Title:') . ' <input style="width: 250px;" id="' . $this->get_field_id('titleAsk') . '" name="' . $this->get_field_name('titleAsk') . '" type="text" value="' . $title . '" /></label></p>';
		
 	}

}// END class

function wpcc_getchachaWidgetAsk_callback() {
	$wpcc_apikey = stripslashes(get_option('wpcc_apikey'));
	
	$query = trim($_POST['query']);
	$results_id = $_POST['results_div_id'];
	
	$query = preg_replace("/[^a-zA-Z0-9\s]/","", $query);
	$query = urlencode($query);
	
	if(strlen($query)>1) {
		$url = 'http://query.chacha.com/answer/search.json?query='.$query.'&apikey='.$wpcc_apikey;
		$response = wpccRequest($wpcc_apikey, $url);
		$arr = json_decode($response); 
		
		$sitename = urlencode(trim(get_bloginfo('name')));
		$campaign = "?utm_source=".$sitename."&utm_medium=Ask%20Widget&utm_campaign=WordPress";
		$answer = $arr->{"qvpResults"}[0]->{"answer"}->{"answer"};
		$category = $arr->{"qvpResults"}[0]->{"answer"}->{"category"};
		$link = "http://www.chacha.com/search/".$query."/";
		
		if(strlen($answer)>1) {
			$html = "<p><strong>Answer:</strong> ";
			$html .= $answer;
			$html .= " <a href=\"".$link.$campaign."\" target=\"_blank\" title=\"Read more answers from this category in ChaCha\">more&raquo</a>";
			$html .= "</p>";
		} else {
			$html = "<p>You stumped us! Text your question to 242242 (spells ChaCha) or call 1.800.2ChaCha (1.800.224.2242).</p>";
		}
	} else {
		$html = "<p>Oops! You forgot to ask a question! Text your question to 242242 (spells ChaCha) or call 1.800.2ChaCha (1.800.224.2242).</p>";
	}
	
	// Remove single quotes - a bug in WordPress
	$html = str_replace("'","\'",$html);
	
	$error = "";
	
	if( $error ) {
	die("document.getElementById('$results_id').innerHTML = '$error'");
	} 
	
	die("document.getElementById('$results_id').innerHTML = '$html'");
} ?>