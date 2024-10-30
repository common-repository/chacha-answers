<?php

function wpcc_getchacha() {
	$wpcc_apikey = stripslashes(get_option('wpcc_apikey'));
	$url = 'http://query.chacha.com/trends/getTrends?source=RawQuery&count=25&prettyOutput=true';
	$response = wpccRequest($wpcc_apikey, $url);
	$arr = json_decode($response, true); 
	$string = "<span style='line-height: 1.3'>";
	foreach ($arr as $key => $value) {
		if($key=="trends") {
    		$arrtrends = array_unique($value);
			foreach ($arrtrends as $trend) {
				$string .= "<a href='http://www.chacha.com/search/".urlencode($trend)."' target='_blank' ";
				$string .= "style='font-size: 1.1em'>";
				$string .= $trend;
				$string .= "</a>, ";
			}
		}
	}
	$string = substr($string,0,-6);
	echo $string."</span>";
	die;
}

function wpcc_gettwitter() {
	$wpcc_apikey = stripslashes(get_option('wpcc_apikey'));
	$url = 'http://search.twitter.com/trends/current.json';
	$response = wpccRequest($wpcc_apikey, $url);
	$arr = json_decode($response, true); 
	$string = "<span style='line-height: 1.3'>Including Hashtags: ";
	foreach ($arr['trends'] as $currenttrends) {
		foreach ($currenttrends as $trend) {
			$trendname = $trend['name'];
			$trendquery = $trend['query'];
			$string .= "<a href='http://search.twitter.com/search?q=".urlencode($trendquery)."' target='_blank' ";
			$string .= "style='font-size: 1.1em'>";
			$string .= $trendname;
			$string .= "</a>, ";
		}
	}
	$string = substr($string,0,-2);
	echo $string."</span>";
	$url = 'http://search.twitter.com/trends/current.json?exclude=hashtags';
	$response = wpccRequest($wpcc_apikey, $url);
	$arr = json_decode($response, true); 
	$string = "<br /><br /><span style='line-height: 1.3'>Excluding Hashtags: ";
	foreach ($arr['trends'] as $currenttrends) {
		foreach ($currenttrends as $trend) {
			$trendname = $trend['name'];
			$trendquery = $trend['query'];
			$string .= "<a href='http://search.twitter.com/search?q=".urlencode($trendquery)."' target='_blank' ";
			$string .= "style='font-size: 1.1em'>";
			$string .= $trendname;
			$string .= "</a>, ";
		}
	}
	$string = substr($string,0,-6);
	echo $string."</span>";
	die;
}

function wpcc_getgoogle() {
	$rss = fetch_feed('http://www.google.com/trends/hottrends/atom/hourly');
	$maxitems = $rss->get_item_quantity(5); 
	$rss_items = $rss->get_items(0, $maxitems); 
	$string = "<span style='line-height: 1.3'>";
	if ($maxitems == 0) {
		$string =  "Unable to get trends feed from Google";
	} else {
		foreach ( $rss_items as $item ) :
		$string .= $item->get_description();
		$string = str_replace("<a href=", "<a target='_blank' style='font-size: 1.1em' href=",$string);
		$string = str_replace("<ol>","",$string);
		$string = str_replace("</ol>","",$string);
		$string = str_replace("<li>","",$string);
		$string = str_replace("</li>",", ",$string);
		endforeach;
	}
	$string = substr($string,0,-6);
	echo $string."</span>";
	die;
}

function wpcc_gettweets() {
	$rss = fetch_feed('http://twitter.com/statuses/user_timeline/13768052.rss');
	$maxitems = $rss->get_item_quantity(5); 
	$rss_items = $rss->get_items(0, $maxitems); 
	if ($maxitems == 0) {
		$string =  "Unable to get ChaCha feed from Twitter";
	} else {
		echo "<ul style='list-style:square; margin-left: 20px'>";
		foreach ( $rss_items as $item ) :
			if (substr($item->get_title(),0,5)!="links") {
				echo "<li>";
				$title = substr($item->get_title(),8,140);
				$title = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $title);
				echo $title;
				echo " <a href=\"".$item->get_permalink()."\" target=\"_blank\">&raquo;</a>";
				echo "</li>";
		} 
		endforeach;
		echo "</ul>";
	}
	die;
} ?>