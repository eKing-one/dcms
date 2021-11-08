<?
if (isset($_GET['act']) && $_GET['act'] == 'rename')
{
	?>
	<form class="foot" action="?act=rename&amp;ok" method="post">
	Название:<br />
	<input name="name" type="text" value="<?=text($foto['name'])?>" /><br />
	Описание:<?=$tPanel?>
	<textarea name="opis"><?=text($foto['opis'])?></textarea><br />

	<label><input type="checkbox" name="metka" value="1" <?=($foto['metka'] == 1 ? "checked='checked'" : null)?>/> Метка <font color="red">18+</font></label><br />

	<input class="submit" type="submit" value="Применить" /><br />
	<img src="/style/icons/str2.gif" alt="*"> <a href="?">Отмена</a><br />
	</form>
	<?
}


if (isset($_GET['act']) && $_GET['act'] == 'delete')
{
	?>
	<form class="foot" action="?act=delete&amp;ok" method="post">
	<div class="err">Подтвердите удаление фотографии</div>
	<input class="submit" type="submit" value="Удалить" /><br />
	<img src="/style/icons/str2.gif" alt="*"> <a href="?">Отмена</a><br />
	</form>
	<?
}

echo '<div class="foot">';
if ($ank['id'] == $user['id'])
echo '<img src="/style/icons/pht2.png" alt="*"> <a href="?act=avatar">Сделать главной</a><br />';
?>
<img src="/style/icons/pen2.png" alt="*"> <a href="?act=rename">Переименовать</a><br />
<img src="/style/icons/crs2.png" alt="*"> <a href="?act=delete">移走</a><br />
</div>