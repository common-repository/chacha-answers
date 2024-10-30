<?php 
/*
Author: Douglas Karr
Author URI: http://www.dknewmedia.com
Description: Retrieve Data
*/
                        
$obj_pages = json_decode($response_pages, true); 

$arr=$obj_pages['data'];

foreach ($arr as $key => $value) {
	$daterange = $key;
	foreach ($value as $iKey => $iValue) {
		switch($iKey) {
			case "measures":
				$totalvisits = $iValue[Visits];
			case "SubRows":
				$arrPages = $iValue;
		}
	}	
} ?>
<small><?php echo number_format($totalvisits); ?> visits within the date range <?php echo $daterange ?></small>
<table id="pagedata" border="0" width="565px" style="margin-top: 10px">
	<thead>
		<th></th>
        <th>Page Title</th>
		<th>Visits</th>
	</thead>
	<tbody>
		<?php 
		$i=1;
		foreach ($arrPages as $nkey => $nvalue) {
			echo "<tr onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='normal'\">";
			echo "<td align=\"right\" valign=\"top\">";
			echo $i.".</td><td>";
			if ($nvalue[Attributes][Title]!="") {
			echo "<a href=".$nvalue[Attributes][UrlLink]." target='_blank'>";
			echo $nvalue[Attributes][Title];
			echo "</a>";
			} else {
				echo "No Title";
			}
			echo "</td><td align=\"right\" valign=\"top\">";
			echo number_format($nvalue[measures][Visits]);
			echo "</td></tr>";
			$i = $i + 1;
		}
		?>
	</tbody>
</table>