<?php
//返回管理面板
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('adm_mysql',null,'index.php?'.SID);
adm_check();
$set['title']='导入SQL';
include_once '../sys/inc/thead.php';
title();
if (isset($_FILES['file'])){
$file=esc(stripcslashes(htmlspecialchars($_FILES['file']['name'])));
$ras=strtolower(preg_replace('#^.*\.#i', 'null', $file));
if($ras!='sql')$err='文件格式不正确';
if(!isset($err)){
@chmod(H."sys/update/",0777);
copy($_FILES['file']['tmp_name'], H."sys/update/".$_FILES['file']['name']."");
// выполнение одноразовых запросов
$opdirtables=opendir(H.'sys/update/');
while ($rd=readdir($opdirtables))
{
if (preg_match('#^\.#',$rd))continue;
if (isset($set['update'][$rd]))continue;
if (preg_match('#\.sql$#i',$rd))
{
include_once H.'sys/inc/sql_parser.php';
$sql=SQLParser::getQueriesFromFile(H.'sys/update/'.$rd);
for ($i=0;$i<count($sql);$i++){dbquery($sql[$i]);}
$set['update'][$rd]=true;
$save_settings=true;
}
}
closedir($opdirtables);
@unlink(H."sys/update/".$_FILES['file']['name']."");
msg("表格已成功填写！");
}
}
if(isset($_GET['update'])){
// выполнение одноразовых запросов
$opdirtables=opendir(H.'sys/update/');
while ($rd=readdir($opdirtables))
{
if (preg_match('#^\.#',$rd))continue;
if (isset($set['update'][$rd]))continue;
if (preg_match('#\.sql$#i',$rd))
{
include_once H.'sys/inc/sql_parser.php';
$sql=SQLParser::getQueriesFromFile(H.'sys/update/'.$rd);
for ($i=0;$i<count($sql);$i++){dbquery($sql[$i]);}
$set['update'][$rd]=true;
$save_settings=true;
}
}
closedir($opdirtables);
@unlink(H."sys/update/".$_FILES['file']['name']."");
msg("SQL文件已成功导入！");
}
err();
aut();
	echo "<form method='post' enctype='multipart/form-data' action='?$passgen'>
	上载:<br />
	<input name='file' type='file' accept='sql' /><br /><input value='导入!' type='submit' />
	</form>
	<br /> 注意！ 下载文件并执行请求后，它将被自动删除！";
	echo "<div class='foot'>
	如果包含表格的文件已经在文件夹中，请按照下面的链接操作。<br /> 
	&raquo;<a href='?update'>从文件夹上传</a>
	</div>";
echo "<div class='foot'>";
echo "&laquo;<a href='mysql.php'>MySQL 请求</a><br />";
if (user_access('adm_panel_show'))
echo "&laquo;<a href='/adm_panel/'>返回管理面板</a><br />";
echo "</div>";
include_once '../sys/inc/tfoot.php';

?>