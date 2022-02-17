<?
list($msec, $sec) = explode(chr(32), microtime());

if ($_SERVER['PHP_SELF'] != '/index.php') 
{
?>
	<div class="foot">
	<img src="/style/icons/icon_glavnaya.gif" alt="*" /> <a href="/index.php">返回首页</a>
	</div>
<?
}
?>
<div class="copy">
<center>
&copy; <a href="http://<?=text($_SERVER['HTTP_HOST'])?>" style="text-transform: capitalize;"><?=text($_SERVER['HTTP_HOST'])?></a> - <?=date('Y');?> г.
</center>
</div>

<div class="foot">
在网站上: 
<a href="/online.php"><?=dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `date_last` > ".(time()-600).""), 0)?></a> &amp;
<a href="/online_g.php"><?=dbresult(dbquery("SELECT COUNT(*) FROM `guests` WHERE `date_last` > ".(time()-600)." AND `pereh` > '0'"), 0)?></a>
<?
if (!$set['web'])
echo ' | <a href="/?t=web">电脑版</a>';
?>
</div>

<div class="rekl">
<?
$page_size = ob_get_length(); 
ob_end_flush(); 

rekl(3);
?>
<center>
页面执行时间: <?=round(($sec + $msec) - $conf['headtime'], 3)?>秒
</center>
</div>

</div>
</body>
</html>
<?
exit;
?>