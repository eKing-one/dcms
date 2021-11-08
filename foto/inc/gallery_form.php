<?
if (isset($user) && $user['id'] == $ank['id'])
{
	if (isset($_GET['act']) && $_GET['act'] == 'create')
	{
		?>
		<div class="foot">
		<img src="/style/icons/str2.gif" alt="*"> <?=user::nick($ank['id'])?> | 
		<a href="/foto/<?=$ank['id']?>/">Альбомы</a> | 
		<b>Создать</b>
		</div>
		
		<form action="?act=create&amp;ok" method="post">
		<div class="nav2">Название альбома:<br />
		
		<input type="text" name="name" value="" /><br />
		Описание:<?=$tPanel?>
		<textarea name="opis"></textarea><br />
		
		Пароль:<br />
		<input type="text" name="pass" value="" /></div>

		<div class="nav1">
		Могут смотреть:<br />
		<input name="privat" type="radio" checked="checked" value="0" />Все 
		<input name="privat" type="radio" value="1" />Друзья 
		<input name="privat" type="radio" value="2" />Только я</div>

		<div class="nav2">
		Могут комментировать:<br />
		<input name="privat_komm" type="radio" checked="checked" value="0" />Все 
		<input name="privat_komm" type="radio" value="1" />Друзья 
		<input name="privat_komm" type="radio" value="2" />Только я</div>
		
		<input class="submit" type="submit" value="Создать" />
		</form>

		<div class="foot">
		<img src="/style/icons/str2.gif" alt="*"> <?=user::nick($ank['id'])?> | 
		<a href="/foto/<?=$ank['id']?>/">Альбомы</a> | 
		<b>Создать</b>
		</div>
		<?

		include_once '../sys/inc/tfoot.php';
		exit;
	}
}
?>