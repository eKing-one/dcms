<?php

/**
 * 安装欢迎页面
 */

$set['title']='安装向导';
include_once 'inc/head.php'; // 设计主题的顶部
if(isset($_GET['yes']) && $_GET['step']=='1') {
	$_SESSION['install_step']++;
	header("Location: index.php?$passgen&".SID);
	exit;
}
if (isset($_GET['no'])) {echo '<div class="err">必须接受这一条件</div>';}
?>
<form method="post" action="<?php echo passgen(); ?>">
	<input type='submit' name='refresh' value='下一步' />
</form>
<center>
	<div class="mess">
		<b>欢迎来到 DCMS-Social 安装向导！</b><br />
	</div>
	<div class="nav2">
		<font color='green'>当前版本：v<?echo $set['dcms_version'];?></font>
	</div>
	<div class="mess">
		<font color='red'>由于本分支版本采用“滚动更新”策略，我们建议您在安装后每间隔一段时间从 <a href="https://github.com/zzyh1145/CN_DCMS-Social">GitHub</a> 上同步最新版本。</font>
	</div>
</center>
<div class="nav2">
	-	俄语官方支持网站：<a target="_blank" href="http://dcms-social.ru">Dcms-Social.Ru</a><br />
	-   下载引擎的最安全来源被认为是上述官方网站和 GitHub 仓库<br />
	-   有关本分支版本的错误和缺陷，请向<a target="_blank" href="https://dcms.net.cn/forum/12/20/">论坛</a>反馈<br />
    -   禁止在未拥有<a target="_blank" href="http://dcms-social.ru/plugins/rules/post.php?id=2">俄语 DCMS 付费许可证</a>的情况下移除最底部的链接<br />
</div>
<hr />
要继续使用此版本，您必须同意以下条件:<br />
<b>您是否承诺不在不购买俄语付费许可证的情况下移除最底部的链接？</b><br />
<form method='get' action='index.php'>";
	<input name="step" value="<?php echo $_SESSION['install_step'] + 1; ?>" type="hidden" />
	<input name="gen" value="<?php echo $passgen; ?>" type="hidden" />
	<input value="是的，我同意" name="yes" type="submit" />
	<input value="不，再见" name="no" type="submit" /><br />
</form>

<hr />
<b>步骤: <?php echo $_SESSION['install_step']; ?></b>
<?
include_once 'inc/foot.php'; //设计主题的底部