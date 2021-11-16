<?php
$set['title']='安装向导';
include_once 'inc/head.php'; // 设计主题的顶部

	if(isset($_GET['yes']) && $_GET['step']=='1')
	{
		$_SESSION['install_step']++;
		header("Location: index.php?$passgen&".SID);
		exit;
	}

	if (isset($_GET['no']))
		echo '<div class="err">必须接受这一条件</div>';
		
echo "<form method='post' action='?".passgen()."'>";
echo "<input type='submit' name='refresh' value='下一步' />";
echo "</form>";

?>
		<center>				<div class="mess">
		<b>欢迎来到安装向导 DCMS-Social!</b><br />
		</div>				<div class="nav2">
		<font color='green'>当前版本: DCMS-Social v<?echo $set['dcms_version'];?> beta</font>		</div>		<div class="mess">
		<font color='red'>在开始安装之前，我们建议您检查是否有较新版本。网站<a href="http://dcms-social.ru">Dcms-Social.Ru</a></font>
		</div>				</center>		<div class="nav2">
	-	官方支持网站 DCMS-Social - <a target="_blank" href="http://dcms-social.ru">Dcms-Social.Ru</a><br />
	-  下载引擎的最安全来源被认为是上述官方网站<br />
	-   有关错误和缺陷，请报告 <a target="_blank" href="http://dcms-social.ru/forum/">论坛</a><br />
     - 禁止在不具备的情况下拍摄引擎的文案（底部链接到 Dcms-Social.ru） <a target="_blank" href="http://dcms-social.ru/plugins/rules/post.php?id=2">许可证</a><br />

	</div>		<hr />	要继续使用此版本，您必须同意以下条件:<br /> <b>您是否承诺不在不购买许可证的情况下拍摄文案？</b><br />
<?

echo "<form method='get' action='index.php'>";
echo "<input name='step' value='".($_SESSION['install_step']+1)."' type='hidden' />";
echo "<input name='gen' value='$passgen' type='hidden' />";
echo "<input value='是的，我同意' name='yes' type='submit' />";
echo "<input value='不，再见' name='no' type=\"submit\" /><br />";
echo "</form>";

echo "<hr />";
echo "<b>步骤: $_SESSION[install_step]</b>";
include_once 'inc/foot.php'; //设计主题的底部
?>