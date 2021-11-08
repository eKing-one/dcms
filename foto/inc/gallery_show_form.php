<?
if ($user['level'] > $ank['level'] || $user['id'] == $ank['id'])
{
	if (isset($_GET['edit']) && $_GET['edit'] == 'rename')
	{
		?>
		<div class="foot">
		<img src="/style/icons/str2.gif" alt="*"> <?=user::nick($ank['id'])?> | 
		<a href="/foto/<?=$ank['id']?>/">Альбомы</a> | 
		<a href="/foto/<?=$ank['id']?>/<?=$gallery['id']?>/"><?=text($gallery['name'])?></a> | 
		<b>Редактирование</b>
		</div>
		
		<form action="?edit=rename&amp;ok" method="post">

		<div class="nav2">Название альбома:<br />
		
		<input type="text" name="name" value="<?=text($gallery['name'])?>" /><br />
		Описание:<?=$tPanel?>
		<textarea name="opis"></textarea><br />
		
		Пароль:<br />
		<input type="text" name="pass" value="<?=text($gallery['pass'])?>" /></div>

		<div class="nav1">
		Могут смотреть:<br />
		<input name="privat" type="radio" <?=($gallery['privat'] == 0 ? ' checked="checked"' : null)?> value="0" />Все 
		<input name="privat" type="radio" <?=($gallery['privat'] == 1 ? ' checked="checked"' : null)?>value="1" />Друзья 
		<input name="privat" type="radio" <?=($gallery['privat'] == 2 ? ' checked="checked"' : null)?>value="2" />Только я</div>

		<div class="nav2">
		Могут комментировать:<br />
		<input name="privat_komm" type="radio" <?=($gallery['privat_komm'] == 0 ? ' checked="checked"' : null)?> value="0" />Все 
		<input name="privat_komm" type="radio" <?=($gallery['privat_komm'] == 1 ? ' checked="checked"' : null)?> value="1" />Друзья 
		<input name="privat_komm" type="radio" <?=($gallery['privat_komm'] == 2 ? ' checked="checked"' : null)?> value="2" />Только я</div>
		
		<input class="submit" type="submit" value="Сохранить" />
		</form>

		<div class="foot">
		<img src="/style/icons/str2.gif" alt="*"> <?=user::nick($ank['id'])?> | 
		<a href="/foto/<?=$ank['id']?>/">Альбомы</a> | 
		<a href="/foto/<?=$ank['id']?>/<?=$gallery['id']?>/"><?=text($gallery['name'])?></a> | 
		<b>Редактирование</b>
		</div>
		<?
	
		include_once '../sys/inc/tfoot.php';
		exit;
	}
}


if ((user_access('foto_alb_del') || isset($user) && $user['id'] == $ank['id']) && isset($_GET['act']) && $_GET['act'] == 'delete')
{
	
	?>
	<div class='mess'>
	Вы действительно хотите удалить фотоальбом <b><?=text($gallery['name'])?></b>, и все фотографии в нем?<br />
	<center>
	<a href="?act=delete&amp;ok"><img src="/style/icons/ok.gif" alt="*"> 移走</a> 
	<a href="?act=delete&amp;ok"><img src="/style/icons/delete.gif" alt="*"> Отмена</a> 
	</center>
	</div>
	<?
}


if (isset($user) && $user['id'] == $ank['id'] && isset($_GET['act']) && $_GET['act'] == 'upload')
{
	?>
	<div class="foot">
	<img src="/style/icons/str2.gif" alt="*"> <?=user::nick($ank['id'])?> | 
	<a href="/foto/<?=$ank['id']?>/">Альбомы</a> | 
	<a href="/foto/<?=$ank['id']?>/<?=$gallery['id']?>/"><?=text($gallery['name'])?></a> | 
	<b>Загрузка фотографии</b>
	</div>
	
	<form class="nav2" id="photo_form" enctype="multipart/form-data" action="?act=upload&amp;ok" method="post">
	Название:<br />
	<input name="name" type="text" /><br />
	
	Файл:<br />
	<input name="file" type="file" accept="image/*,image/jpeg" /><br />
	
	Описание:<?=$tPanel?>
	<textarea name="opis"></textarea><br />
	
	<label><input type="checkbox" name="metka" value="1" /> Метка <font color="red">18+</font></label><br />
	
	<input class="submit" type="submit" value="Выгрузить" /> 
	</form>
	
	<div class="nav1">
	<b>Размещаемые на Сайте Фото не должны:</b><br />
	* нарушать действующее законодательство, честь и достоинство, права и охраняемые законом интересы третьих лиц, способствовать разжиганию религиозной, расовой или межнациональной розни, содержать сцены насилия, либо бесчеловечного обращения с животными, и т.д.;<br />
	* носить непристойный или оскорбительный характер;<br />
	* содержать рекламу наркотических средств;<br />
	* нарушать права несовершеннолетних лиц;<br />
	* нарушать авторские и смежные права третьих лиц;<br />
	* носить порнографический характер;<br />
	* содержать коммерческую рекламу в любом виде.<br />
	</div>
	
	<div class="foot">
	<img src="/style/icons/str2.gif" alt="*"> <?=user::nick($ank['id'])?> | 
	<a href="/foto/<?=$ank['id']?>/">Альбомы</a> | 
	<a href="/foto/<?=$ank['id']?>/<?=$gallery['id']?>/"><?=text($gallery['name'])?></a> | 
	<b>Загрузка фотографии</b>
	</div>
	<?

	include_once '../sys/inc/tfoot.php';
	exit;
}
?>