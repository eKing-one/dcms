<?
// 更改照片信息
if (isset($_GET['act']) && $_GET['act'] == 'rename') {
	?>
	<form class="foot" action="?act=rename&amp;ok" method="post">
	标题:<br />
	<input name="name" type="text" value="<?=text($photo['name'])?>" /><br />
	描述:<?=$tPanel?>
	<textarea name="opis"><?=text($photo['opis'])?></textarea><br />
	<label><input type="checkbox" name="metka" value="1" <?=($photo['metka'] == 1 ? "checked='checked'" : null)?>/> 标签 <font color="red">18+</font></label><br />
	<input class="submit" type="submit" value="更改" /><br />
	<img src="/style/icons/str2.gif" alt="*"> <a href="?">取消</a><br />
	</form>
	<?
}

// 删除照片
if (isset($_GET['act']) && $_GET['act'] == 'delete') {
	?>
	<form class="foot" action="?act=delete&amp;ok" method="post">
	<div class="err">确认删除照片</div>
	<input class="submit" type="submit" value="删除" /><br />
	<img src="/style/icons/str2.gif" alt="*"> <a href="?">取消</a><br />
	</form>
	<?
}

echo '<div class="foot">';
if ($ank['id'] == $user['id'])
echo '<img src="/style/icons/pht2.png" alt="*"> <a href="?act=avatar">设置为头像</a><br />';
?>
<img src="/style/icons/pen2.png" alt="*"> <a href="?act=rename">重命名</a><br />
<img src="/style/icons/crs2.png" alt="*"> <a href="?act=delete">删除</a><br />
</div>
