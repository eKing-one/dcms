<?
$q = dbquery("SELECT * FROM `news` WHERE `main_time` > '".time()."' ORDER BY `id` DESC LIMIT 1");

if (dbrows($q) == 1 && !$set['web'] && $user['news_read'] == 0)
{
	$news = dbassoc($q);
	echo '<div class="mess">';
	echo '<img src="/style/icons/blogi.png" alt="*" /> <a href="/news/news.php?id=' . $news['id'] . '">' . text($news['title']) . '</a><br/> ';
	echo output_text($news['msg']) . '<br />';
	
	if ($news['link']!=NULL)
	echo '<a href="' .htmlentities($news['link'], ENT_QUOTES, 'UTF-8').'">Подробности</a><br />';
	echo 'Опубликовал: '.group($news['id_user']).' ';
 echo user::nick($news['id_user'],1,1,1).' '.vremja($news['time']).' ';
	echo ' <img src="/style/icons/komm.png" alt="*" /> (' . dbresult(dbquery("SELECT COUNT(*) FROM `news_komm` WHERE `id_news` = '$news[id]'"),0) . ')<br />';
	
	if (isset($user))
	echo '<div style="text-align:right;"><a href="?news_read">Скрыть</a></div>';
	echo '</div>';
}
?>