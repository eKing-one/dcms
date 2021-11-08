<?
err();
aut();

$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `forum_r` WHERE `id_forum` = '$forum[id]'"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];

echo "<table class='post'>\n";

$q=dbquery("SELECT * FROM `forum_r` WHERE `id_forum` = '$forum[id]' ORDER BY `time` DESC LIMIT $start, $set[p_str]");

if (dbrows($q)==0) {
	echo "  <div class='mess'>\n";
	echo "没有任何部分\n";
	echo "  </div>\n";
}	

while ($razdel = dbassoc($q))
{
/*-----------зебра-----------*/	
if ($num==0)	
{		
echo "  <div class='nav1'>\n";
$num=1;	
}	
elseif ($num==1)
{	
echo "  <div class='nav2'>\n";	
$num=0;	
}	
/*---------------------------*/

echo "<a href='/forum/$forum[id]/$razdel[id]/'>" . text($razdel['name']) . "</a> [".dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]'"),0).'/'.dbresult(dbquery("SELECT COUNT(*) FROM `forum_t` WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]'"),0)."]\n";
if(!empty($razdel['opis'])){ echo '<br/><span style="color:#666;">'.output_text($razdel['opis']).'</span>'; }
echo "   </div>\n";
}
echo "</table>\n";
if ($k_page>1)str("/forum/$forum[id]/?",$k_page,$page); // Вывод страниц
?>