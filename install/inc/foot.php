<?

list($msec, $sec) = explode(chr(32), microtime());
echo "<div class='foot'>";
//echo "<a href='/' accesskey='0' title='На главную'>".(isset ($set['copy']) && $set['copy']!=null?$set['copy']:'На главную')."</a><br />";

$page_size = ob_get_length(); 
ob_end_flush(); 
if(!isset($_SESSION['traf'])) 
	$_SESSION['traf'] = 0; 
	$_SESSION['traf'] += $page_size; 

echo '<center>
页面重量: '.round($page_size / 1024, 2).' KB<br />
您的流量: '.round($_SESSION['traf'] / 1024, 2).' KB <br />
生成页面: '.round(($sec + $msec) - $conf['headtime'], 3).'秒
	</center>'; 
echo "</div>";
echo "</div></body></html>";
exit;

?>