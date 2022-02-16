<?php
$set['title']='MySQL 选项';
include_once 'inc/head.php'; // 设计主题的顶部

echo "<form method='post' action='?".passgen()."'>";
echo "<input type='submit' name='refresh' value='下一步' />";
echo "</form>";

if (isset($_SESSION['mysql_ok']) && $_SESSION['mysql_ok']==true)
{
if(isset($_GET['step']) && $_GET['step']=='4')
{
$_SESSION['install_step']++;
header("Location: index.php?$passgen&".SID);
exit;
}
}
elseif (isset($_POST['host']) && isset($_POST['user']) && isset($_POST['pass']) && isset($_POST['db']))
{
if(!($db=@mysql_connect($_POST['host'], $_POST['user'],$_POST['pass'])))
{
$err[]='无法连接到服务器 '.$_POST['host'];
}
elseif(!@mysql_select_db($_POST['db'],$db))
{
$err[]='检查数据库名称';
}
else
{
$set['mysql_db_name']=$_SESSION['db']=$_POST['db'];
$set['mysql_host']=$_SESSION['host']=$_POST['host'];
$set['mysql_user']=$_SESSION['user']=$_POST['user'];
$set['mysql_pass']=$_SESSION['pass']=$_POST['pass'];

mysql_query('set charset utf8');
  mysql_query('SET names utf8');
  mysql_query('set character_set_client="utf8"');
  mysql_query('set character_set_connection="utf8"');
  mysql_query('set character_set_result="utf8"');

$db_tables=array();
$tab=mysql_query('SHOW TABLES FROM '.$_SESSION['db']);
for($i=0;$i<mysql_num_rows($tab);$i++)
{
$db_tables[]=mysql_tablename($tab,$i);
}
$opdirtables=opendir(H.'install/db_tables');
while ($filetables=readdir($opdirtables))
{
if (preg_match('#\.sql$#i',$filetables))
{
$table_name=preg_replace('#\.sql$#i',null,$filetables);
if (in_array($table_name, $db_tables))
{
if (isset($_POST['rename']) && $_POST['rename']==1)
{
mysql_query("ALTER TABLE `$table_name` RENAME `~".$time."_$table_name`");
}
else $db_not_null=true;
}
}
}

if (isset($db_not_null))
{


$err[]='在所选数据库中 ('.$_SESSION['db'].') 包含具有相同名称的表。清除或选择其他数据库。';
}
else {

include_once H.'install/inc/ver_tables.php';
$msg[]="成功 $ok_sql 从 $k_sql 查询";

$_SESSION['mysql_ok']=true;
}
}



}

if (isset($_SESSION['mysql_ok']) && $_SESSION['mysql_ok']==true)
{
echo "<div class='msg'>数据库连接成功</div>";

if (isset($msg))
{
foreach ($msg as $key=>$value) {
echo "<div class='msg'>$value</div>";
}
}
if (isset($err))
{
foreach ($err as $key=>$value) {
echo "<div class='err'>$value</div>";
}
}
echo "<hr />";
echo "<form method=\"get\" action=\"index.php\">";
echo "<input name=\"step\" value=\"".($_SESSION['install_step']+1)."\" type=\"hidden\" />";
echo "<input value=\"".(isset($err)?'尚未准备好安装':'继续')."\" type=\"submit\"".(isset($err)?' disabled="disabled"':null)." />";
echo "</form>";
}
else
{
if (isset($err))
{
foreach ($err as $key=>$value) {
echo "<div class='err'>$value</div>";
}
}
echo "<form method=\"post\" action=\"index.php?$passgen\">";
echo "主机:<br />";
echo "<input name=\"host\" value=\"$set[mysql_host]\" type=\"text\" /><br />";
echo "用户:<br />";
echo "<input name=\"user\" value=\"$set[mysql_user]\" type=\"text\" /><br />";
echo "密码:<br />";
echo "<input name=\"pass\" value=\"$set[mysql_pass]\" type=\"text\" /><br />";
echo "网站名称:<br />";
echo "<input name=\"db\" value=\"$set[mysql_db_name]\" type=\"text\" /><br />";
if (isset($db_not_null))
echo "<label><input type='checkbox' checked='checked' name='rename' value='1' /> 重命名现有表<br /></label>";
echo "<br /><input value=\"保存\" type=\"submit\" />";
echo "</form>";
}

echo "<hr />";
echo "<b>步骤: $_SESSION[install_step]</b>";

include_once 'inc/foot.php'; //设计主题的底部
?>