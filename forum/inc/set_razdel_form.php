<?
if (isset($_GET['act']) && $_GET['act']=='mesto')
{
    echo "<form method=\"post\" action=\"/forum/$forum[id]/$razdel[id]/?act=mesto&amp;ok\">";
    echo "子论坛:<br />";
    echo "<select name=\"forum\">";
    $q2 = dbquery("SELECT * FROM `forum_f` ORDER BY `pos` ASC");
    while ($forums = dbassoc($q2))
    {
        if ($forum['id']==$forums['id'])$check=' selected="selected"';
        else 
        $check=NULL;
        echo '<option' . $check . ' value="' . $forums['id'] . '">' . text($forums['name']) . '</option>';
    }
    echo "</select><br />";
    echo "<input value=\"移动\" type=\"submit\" /><br />";
    echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/forum/$forum[id]/$razdel[id]/'>取消</a><br />";
    echo "</form>";
}
if (isset($_GET['act']) && $_GET['act']=='set')
{
    echo "<form method=\"post\" action=\"/forum/$forum[id]/$razdel[id]/?act=set&amp;ok\">";
    echo "部分名称:<br />";
    echo '<input name="name" type="text" maxlength="32" value="' . text($razdel['name']) . '" /><br />';
    echo "资料描述<br/>";
	echo "<textarea name='opis' placeholder='部分描述'>" . text($razdel['opis']) . "</textarea><br/>";
    echo "<input value=\"更改\" type=\"submit\" /><br />";
    echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/forum/$forum[id]/$razdel[id]/'>取消</a><br />";
    echo "</form>";
}
if (isset($_GET['act']) && $_GET['act']=='del')
{
    echo "<div class=\"err\">";
    echo "确认删除分区<br />";
    echo "<a href=\"/forum/$forum[id]/$razdel[id]/?act=delete&amp;ok\">是的</a> / <a href=\"/forum/$forum[id]/$razdel[id]/\">取消</a><br />";
    echo "</div>";
}
echo "<div class=\"foot\">";
echo "<img src='/style/icons/str.gif' alt='*'> <a href='/forum/$forum[id]/$razdel[id]/?act=mesto'>移动一个部分</a><br />";
echo "<img src='/style/icons/str.gif' alt='*'> <a href='/forum/$forum[id]/$razdel[id]/?act=del'>删除部分</a><br />";
echo "<img src='/style/icons/str.gif' alt='*'> <a href='/forum/$forum[id]/$razdel[id]/?act=set'>节参数</a><br />";
echo "</div>";
?>