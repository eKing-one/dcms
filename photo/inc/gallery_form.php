<?php
if (isset($user) && $user['id'] == $ank['id'])
{
	if (isset($_GET['act']) && $_GET['act'] == 'create')
	{
		?>
		<div class="foot">
		<img src="/style/icons/str2.gif" alt="*"> <?=user::nick($ank['id'],1,0,0)?> | 
		<a href="/photo/<?=$ank['id']?>/">相册</a> | 
		<b>创建</b>
		</div>
		<form action="?act=create&amp;ok" method="post">
		<div class="nav2">专辑名称：<br />
		<input type="text" name="name" value="" /><br />
		描述:<?=$tPanel?>
		<textarea name="opis"></textarea><br />
		密码:<br />
		<input type="text" name="pass" value="" /></div>
		<div class="nav1">
		可以观看:<br />
		<input name="privat" type="radio" checked="checked" value="0" />全部 
		<input name="privat" type="radio" value="1" />朋友 
		<input name="privat" type="radio" value="2" />只有我</div>
		<div class="nav2">
		可以评论:<br />
		<input name="privat_komm" type="radio" checked="checked" value="0" />全部 
		<input name="privat_komm" type="radio" value="1" />朋友 
		<input name="privat_komm" type="radio" value="2" />只有我</div>
		<input class="submit" type="submit" value="创建" />
		</form>
		<div class="foot">
		<img src="/style/icons/str2.gif" alt="*"> <?=user::nick($ank['id'],1,0,0)?> | 
		<a href="/photo/<?=$ank['id']?>/">相册</a> | 
		<b>创建</b>
		</div>
		<?
		include_once '../sys/inc/tfoot.php';
		exit;
	}
}
?>