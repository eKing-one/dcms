<?

if (isset($_GET['act']) && $_GET['act']=='mesto')
{
    echo "<form method=\"post\" action=\"/forum/$forum[id]/$razdel[id]/?act=mesto&amp;ok\">\n";
    echo "子论坛:<br />\n";
    echo "<select name=\"forum\">\n";
    $q2 = dbquery("SELECT * FROM `forum_f` ORDER BY `pos` ASC");
    
    while ($forums = dbassoc($q2))
    {
        if ($forum['id']==$forums['id'])$check=' selected="selected"';
        else 
        $check=NULL;
        
        echo '<option' . $check . ' value="' . $forums['id'] . '">' . text($forums['name']) . '</option>';
    }
    echo "</select><br />\n";
    echo "<input value=\"移动\" type=\"submit\" /><br />\n";
    echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/forum/$forum[id]/$razdel[id]/'>取消</a><br />\n";
    echo "</form>\n";
}

if (isset($_GET['act']) && $_GET['act']=='set')
{
    echo "<form method=\"post\" action=\"/forum/$forum[id]/$razdel[id]/?act=set&amp;ok\">\n";
    echo "部分名称:<br />\n";
    echo '<input name="name" type="text" maxlength="32" value="' . text($razdel['name']) . '" /><br />';
    echo "资料描述<br/>\n";
	echo "<textarea name='opis' placeholder='部分描述'>" . text($razdel['opis']) . "</textarea><br/>";
    echo "<input value=\"更改\" type=\"submit\" /><br />\n";
    echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/forum/$forum[id]/$razdel[id]/'>取消</a><br />\n";
    echo "</form>\n";
}

if (isset($_GET['act']) && $_GET['act']=='del')
{
    echo "<div class=\"err\">\n";
    echo "确认删除分区<br />\n";
    echo "<a href=\"/forum/$forum[id]/$razdel[id]/?act=delete&amp;ok\">是的</a> / <a href=\"/forum/$forum[id]/$razdel[id]/\">取消</a><br />";
    echo "</div>\n";
}

echo "<div class=\"foot\">\n";

echo "<img src='/style/icons/str.gif' alt='*'> <a href='/forum/$forum[id]/$razdel[id]/?act=mesto'>移动一个部分</a><br />\n";

echo "<img src='/style/icons/str.gif' alt='*'> <a href='/forum/$forum[id]/$razdel[id]/?act=del'>删除部分</a><br />\n";

echo "<img src='/style/icons/str.gif' alt='*'> <a href='/forum/$forum[id]/$razdel[id]/?act=set'>节参数</a><br />\n";

echo "</div>\n";
?>