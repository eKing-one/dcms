<?

include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/adm_check.php';
include_once '../../sys/inc/user.php';

/* Бан пользователя */ 
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'notes' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)
{
header('Location: /ban.php?'.SID);exit;
}

$set['title']='新日记';
include_once '../../sys/inc/thead.php';
title();

if (!isset($user))header("location: index.php?");

if (isset($_POST['title']) && isset($_POST['msg']))
{
if (($user['rating'] < 2 || $user['group_access'] < 6 ))
{
if (!isset($_SESSION['captcha']))$err[]='验证号码错误';
if (!isset($_POST['chislo']))$err[]='输入验证号码';
elseif ($_POST['chislo']==null)$err[]='输入验证号码';
elseif ($_POST['chislo']!=$_SESSION['captcha'])$err[]='检查验证号码是否输入正确';
}

if (!isset($err))
{
if(empty($_POST['title'])){
$title=esc(stripslashes(htmlspecialchars(substr($_POST['msg'],0,24)))).' ...';
$title=my_esc($title);
}else{
$title=my_esc($_POST['title']); }
$msg = my_esc($_POST['msg']);
$id_dir = intval($_POST['id_dir']);

if (isset($_POST['private'])){
$privat=intval($_POST['private']);
}else{
$privat=0;
}
if (isset($_POST['private_komm'])){
$privat_komm=intval($_POST['private_komm']);
}else{
$privat_komm=0;
}

$type=0;

if (strlen2($title)>32){$err='名称不能超过32个字符';}
if (strlen2($msg)>30000){$err='内容不能超过30,000个字符';}
if (strlen2($msg)<2 && $type == 0){$err='内容太短';}

if (!isset($err)){
dbquery("INSERT INTO `notes` (`time`, `msg`, `name`, `id_user`, `private`, `private_komm`, `id_dir`, `type`) values('$time', '$msg', '$title', '{$user['id']}', '$privat', '$privat_komm', '$id_dir', '$type')");

$st = mysql_insert_id();
if($privat!=2){
dbquery("insert into `stena`(`id_stena`,`id_user`,`time`,`info`,`info_1`,`type`) values('".$user['id']."','".$user['id']."','".$time."','новый дневник','".$st."','note')");
}
/*
===================================
Лента
===================================
*/

$q = dbquery("SELECT * FROM `frends` WHERE `user` = '".$user['id']."' AND `i` = '1'");
while ($f = dbarray($q))
{
$a=get_user($f['frend']);
$lentaSet = dbarray(dbquery("SELECT * FROM `tape_set` WHERE `id_user` = '".$a['id']."' LIMIT 1")); // Общая настройка ленты
 
if ($f['lenta_notes'] == 1 && $lentaSet['lenta_notes'] == 1 ) // фильтр рассылки
dbquery("INSERT INTO `tape` (`id_user`,`avtor`, `type`, `time`, `id_file`) values('$a[id]', '$user[id]', 'notes', '$time', '$st')"); }
		   
dbquery("OPTIMIZE TABLE `notes`");

$_SESSION['message'] = '日记创建成功';
header("Location: list.php?id=$st");
$_SESSION['captcha']=NULL;
exit;
}
}
}
if (isset($_GET['id_dir']))
$id_dir=intval($_GET['id_dir']);
else
$id_dir=0;
err();
aut();

if (isset($_POST["msg"])) $msg = output_text($_POST["msg"]);
echo "<form method=\"post\" name=\"message\" action=\"add.php\">";
echo "标题:<br /><input name=\"title\" size=\"16\" maxlength=\"32\" value=\"\" type=\"text\" /><br />";
if ($set['web'] && is_file(H.'style/themes/'.$set['set_them'].'/altername_post_form.php'))
include_once H.'style/themes/'.$set['set_them'].'/altername_post_form.php';
else
echo "通信:$tPanel<textarea name=\"msg\"></textarea><br />";

echo "类别：:<br /><select name='id_dir'>";
$q=dbquery("SELECT * FROM `notes_dir` ORDER BY `id` DESC");
echo "<option value='0'".($id_dir==0?" selected='selected'":null)."><b>没有类别</b></option>";

while ($post = dbassoc($q))
{
echo "<option value='$post[id]'".($id_dir == $post['id']?" selected='selected'" : null).">" . text($post['name']) . "</option>";
}

echo "</select><br />";

echo "<div class='main'>他们可以看:<br /><input name='private' type='radio' value='0'  selected='selected'/>所有人 ";
echo "<input name='private' type='radio'  value='1' />朋友 ";
echo "<input name='private' type='radio'  value='2' />只有我</div>";
 
echo "<div class='main'>他们可以发表评论:<br /><input name='private_komm' type='radio' value='0'  selected='selected'/>所有人 ";
echo "<input name='private_komm' type='radio'  value='1' />朋友 ";
echo "<input name='private_komm' type='radio'  value='2' />只有我</div>";

if ($user['rating'] < 6 || $user['group_access'] < 6)
echo "<img src='/captcha.php?SESS=$sess' width='100' height='30' alt='核证号码' /><br /><input name='chislo' size='5' maxlength='5' value='' type='text' /><br/>";
	 
echo "<input value=\"要创建\" type=\"submit\" />";
echo "</form>";

echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='index.php'>日记</a><br />";
echo "</div>";

include_once '../../sys/inc/tfoot.php';
?>