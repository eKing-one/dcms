<?
err();
aut();
if (isset($user) && (!isset($_SESSION['time_c_t_forum']) || $_SESSION['time_c_t_forum']<$time-600 || $user['level']>0))
{
	echo '<div class="foot">';
	echo '<img src="/style/icons/plus.gif" alt="*"> <a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/?act=new" title="创建新主题">新主题</a><br />';
	echo '</div>';
}
$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `forum_t` WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]'"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
echo '<table class="post">';
$q=dbquery("SELECT * FROM `forum_t` WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]' ORDER BY `up` DESC,`time` DESC  LIMIT $start, $set[p_str]");
if (dbrows($q)==0)
{
	echo '<div class="mess">';
	echo '该部分没有主题 "' . text($razdel['name']);
	echo '</div>';
}	
while ($them = dbassoc($q))
{
/*-----------зебра-----------*/	
if ($num == 0)	
{			
	echo '<div class="nav1">';		
	$num = 1;	
}	
elseif ($num == 1)	
{		
	echo '<div class="nav2">';		
	$num = 0;	
}	
/*---------------------------*/
if($them['close']==1){
$closed='<img src="/style/icons/topic_locked.gif">'; }else{ $closed=null; }
if($them['up']==1){
$up='<img src="/style/icons/stick.gif">'; }else{ $up=null; }
echo $up." ";
echo '<a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/' . $them['id'] . '/">' . text($them['name']) . '</a> <font color="#666">(' . dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]' AND `id_them` = '$them[id]'"),0) . ')';
echo ' '.$closed.' ';
echo '<span style="float:right;">'.vremja($them['time_create']).'</span></font><br/>';
echo user::nick($them['id_user']).'';
$post2 = dbassoc(dbquery("SELECT `id_user`,`time` FROM `forum_p` WHERE `id_them` = '$them[id]' AND `id_razdel` = '$razdel[id]' AND `id_forum` = '$forum[id]' ORDER BY `time` DESC LIMIT 1"));
if (!empty($post2['id_user']))echo ' / '.user::nick($post2['id_user']).' (' . vremja($post2['time']) . ')';
echo '</div>';
}
echo '</table>';
if ($k_page>1)str("/forum/$forum[id]/$razdel[id]/?",$k_page,$page); // Вывод страниц
?>