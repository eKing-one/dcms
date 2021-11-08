<?
/*
* Заголовок обсуждения
*/

if ($type == 'notes' && $post['avtor'] != $user['id'])
{
	$name = __('Дневник друга');
}
else if ($type == 'notes' && $post['avtor'] == $user['id'])
{
	$name = __('Ваш дневник');
}
 
/*
* Выводим на экран
*/
if ($type == 'notes')
{
	$notes = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` = '".$post['id_sim']."' LIMIT 1"));
	
	if ($notes['id'])
	{
		?>
		<div class="nav1">
		<img src="/style/icons/dnev.png" alt="*"/> <a href="/plugins/notes/list.php?id=<?= $notes['id']?>&amp;page=<?= $pageEnd?>"><?= $name?></a> 
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
		<?= $avtor['medal']?> <?= $avtor['online']?> &raquo; <b><?= text($notes['name'])?></b><br />
		<span class="text"><?= output_text($notes['msg'])?></span>
		</div>
		<?
	}
	else
	{
		?>
		<div class="mess">
		<?= __('Тема форума уже удалена =(')?>
		<span class="time"><?= $s1 . vremja($post['time']) . $s2?></span>
		</div>
		<?
	}

}
?>