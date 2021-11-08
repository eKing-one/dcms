<?
/*
* Заголовок обсуждения
*/

if ($type == 'obmen' && $post['avtor'] != $user['id']) // обмен
{
	$name = __('Файлы | Файл друга');
}
else if ($type == 'obmen' && $post['avtor'] == $user['id'])
{
	$name = __('Файлы | Ваш файл');
}
 
/*
* Выводим на экран
*/
if ($type == 'obmen')
{
	$file = dbassoc(dbquery("SELECT * FROM `obmennik_files` WHERE `id` = '".$post['id_sim']."' LIMIT 1"));
	
	if ($file['id'])
	{
		?>
		<div class="nav1">
		<img src="/style/icons/disk.png" alt="*"/> 
		<a href="/user/personalfiles/<?= $file['id_user']?>/<?= $file['my_dir']?>/?id_file=<?= $file['id']?>&amp;page=<?= $pageEnd?>"><?= $name?></a> 
		<?
		if ($post['count'] > 0)
		{
			?><b><font color='red'>+<?= $post['count']?></font></b><?
		}
		?>
		<span class="time"><?= $s1 . vremja($post['time']) . $s2?></span>
		</div>
		
		<div class="nav2">
		<b><font color='green'><?= $avtor['nick']?></font></b> 
		<?= ($avtor['id'] != $user['id'] ? '<a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a>' : '')?> 
		<?= $avtor['medal']?> <?= $avtor['online']?> &raquo; <b><?= text($file['name'])?></b><br />
		<span class="text"><?= output_text($file['opis'])?></span>
		</div>
		<?
	}
	else
	{
		?>
		<div class="mess">
		<?= __('Файл уже удален =(')?>
		<span class="time"><?= $s1 . vremja($post['time']) . $s2?></span>
		</div>
		<?
	}

}
?>