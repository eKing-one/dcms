<script language="JavaScript" type="text/javascript">
	function tag(text1, text2) {
		if ((document.selection)) {
			document.message.msg.focus();
			document.message.document.selection.createRange().text = text1 + document.message.document.selection.createRange().text + text2;
		} else if (document.forms['message'].elements['msg'].selectionStart != undefined) {
			var element = document.forms['message'].elements['msg'];
			var str = element.value;
			var start = element.selectionStart;
			var length = element.selectionEnd - element.selectionStart;
			element.value = str.substr(0, start) + text1 + str.substr(start, length) + text2 + str.substr(start + length);
			document.forms['message'].elements['msg'].focus();
		} else {
			document.message.msg.value += text1 + text2;
		}
		document.forms['message'].elements['msg'].focus();
	}
</script>

<?php if (!isset($msg2)) $msg2 = NULL; ?>

<table width='100%'><tr>

<div id='comments' class='tpanel'>
	<div class='tmenu'>
		<a href='#' id='opener'>表情符号</a>
	</div>
	<div class='tmenu'>
		<a href='<?php echo $set['siteurl']; ?>/plugins/rules/bb-code.php'>标签</a>
	</div>
</div>
<div style="margin:4px;">
	<a class="invert-image" href="javascript:tag('[b]', '[/b]')"><img src="<?php echo $set['siteurl']; ?>/style/value/b.png" alt="b" title="加粗"/></a>
	<a class="invert-image" href="javascript:tag('[i]', '[/i]')"><img src="<?php echo $set['siteurl']; ?>/style/value/i.png" alt="i" title="斜体"/></a>
	<a class="invert-image" href="javascript:tag('[u]', '[/u]')"><img src="<?php echo $set['siteurl']; ?>/style/value/u.png" alt="u" title="下划线"/></a>
	<a class="invert-image" href="javascript:tag('&#60;?php', '?&#62;')"><img src="<?php echo $set['siteurl']; ?>/style/value/cod.png" alt="cod" title="代码"/></a>
	<a href="javascript:tag('[img]', '[/img]')"><img src="<?php echo $set['siteurl']; ?>/style/value/images.png" alt="img" title="图片" /></a>
	<a href="javascript:tag('[url=]', '[/url]')"><img src="<?php echo $set['siteurl']; ?>/style/value/l.png" alt="url" title="链接" /></a>
	<a href="javascript:tag('[size=]', '[/size]')"><img src="<?php echo $set['siteurl']; ?>/style/value/size.png" alt="size" title="字号" /></a>
	<a href="javascript:tag('[big]', '[/big]')"><img src="<?php echo $set['siteurl']; ?>/style/value/big.png" alt="bigsize" title="大字号" /></a>
	<a href="javascript:tag('[small]', '[/small]')"><img src="<?php echo $set['siteurl']; ?>/style/value/small.png" alt="smallsize" title="小字号" /></a>
	<a href="javascript:tag('[red]', '[/red]')"><img src="<?php echo $set['siteurl']; ?>/style/value/re.png" alt="red" title="红色"/></a>
	<a href="javascript:tag('[green]', '[/green]')"><img src="<?php echo $set['siteurl']; ?>/style/value/gr.png" alt="green" title="绿色"/></a>
	<a href="javascript:tag('[blue]', '[/blue]')"><img src="<?php echo $set['siteurl']; ?>/style/value/bl.png" alt="blue" title="蓝色"/></a>
	<a href="javascript:tag('[yellow]', '[/yellow]')"><img src="<?php echo $set['siteurl']; ?>/style/value/ye.png" alt="yellow" title="黄色"/></a>
</div>

<textarea name="msg" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"><?php echo $otvet . $msg2; ?></textarea>
<br />
<script>
	$.fx.speeds._default = 1000;
	$("#dialog").dialog({autoOpen: false, show: "blind", hide: "explode"});
	$("#opener").click(function() {
		$("#dialog").dialog("open");
		showContent2('<?php echo $set['siteurl']; ?>/style/post-form/smiles.php');
		return false;
	});
</script>
