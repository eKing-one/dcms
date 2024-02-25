<?
if ($user['level'] > $ank['level'] || $user['id'] == $ank['id'])
{
	if (isset($_GET['edit']) && $_GET['edit'] == 'rename')
	{
		?>
		<div class="foot">
		<img src="/style/icons/str2.gif" alt="*"> <?=user::nick($ank['id'],1,0,0)?> | 
		<a href="/photo/<?=$ank['id']?>/">相册</a> | 
		<a href="/photo/<?=$ank['id']?>/<?=$gallery['id']?>/"><?=text($gallery['name'])?></a> | 
		<b>编辑</b>
		</div>
		<form action="?edit=rename&amp;ok" method="post">
		<div class="nav2">相册名称：<br />
		<input type="text" name="name" value="<?=text($gallery['name'])?>" /><br />
		描述：<?=$tPanel?>
		<textarea name="opis"></textarea><br />
		密码：<br />
		<input type="text" name="pass" value="<?=text($gallery['pass'])?>" /></div>
		<div class="nav1">
		谁可以观看？<br />
		<input name="privat" type="radio" <?=($gallery['privat'] == 0 ? ' checked="checked"' : null)?> value="0" />所有人 
		<input name="privat" type="radio" <?=($gallery['privat'] == 1 ? ' checked="checked"' : null)?>value="1" />朋友 
		<input name="privat" type="radio" <?=($gallery['privat'] == 2 ? ' checked="checked"' : null)?>value="2" />只有我</div>
		<div class="nav2">
		谁可以评论？<br />
		<input name="privat_komm" type="radio" <?=($gallery['privat_komm'] == 0 ? ' checked="checked"' : null)?> value="0" />所有人 
		<input name="privat_komm" type="radio" <?=($gallery['privat_komm'] == 1 ? ' checked="checked"' : null)?> value="1" />朋友 
		<input name="privat_komm" type="radio" <?=($gallery['privat_komm'] == 2 ? ' checked="checked"' : null)?> value="2" />只有我</div>
		<input class="submit" type="submit" value="保存" />
		</form>
		<div class="foot">
		<img src="/style/icons/str2.gif" alt="*"> <?=user::nick($ank['id'],1,0,0)?> | 
		<a href="/photo/<?=$ank['id']?>/">相册</a> | 
		<a href="/photo/<?=$ank['id']?>/<?=$gallery['id']?>/"><?=text($gallery['name'])?></a> | 
		<b>编辑</b>
		</div>
		<?
		include_once '../sys/inc/tfoot.php';
		exit;
	}
}
if ((user_access('photo_alb_del') || isset($user) && $user['id'] == $ank['id']) && isset($_GET['act']) && $_GET['act'] == 'delete')
{
	?>
	<div class='mess'>
	您确实要删除相册 <b><?=text($gallery['name'])?></b>, 和里面的所有照片？<br />
	<center>
	<a href="?act=delete&amp;ok"><img src="/style/icons/ok.gif" alt="*"> 删除</a> 
	<a href="?act=delete&amp;ok"><img src="/style/icons/delete.gif" alt="*"> 取消</a> 
	</center>
	</div>
	<?
}
if (isset($user) && $user['id'] == $ank['id'] && isset($_GET['act']) && $_GET['act'] == 'upload')
{
	?>
	<div class="foot">
	<img src="/style/icons/str2.gif" alt="*"> <?=user::nick($ank['id'],1,0,0)?> | 
	<a href="/photo/<?=$ank['id']?>/">相册</a> | 
	<a href="/photo/<?=$ank['id']?>/<?=$gallery['id']?>/"><?=text($gallery['name'])?></a> | 
	<b>上传照片</b>
	</div>
	<form class="nav2" id="photo_form" enctype="multipart/form-data" action="?act=upload&amp;ok" method="post">
	标题：<br />
	<input name="name" type="text" /><br />
	文件：<br />
	<input name="file" type="file" accept="image/*,image/jpeg" /><br />
	描述：<?=$tPanel?>
	<textarea name="opis"></textarea><br />
	<label><input type="checkbox" name="metka" value="1" /> 标签 <font color="red">18+</font></label><br />
	<input class="submit" type="submit" value="上传" /> 
	</form>
	<div class="nav1">
	<b>在网站上发布的照片不得：</b><br />
	* 违反现行法律、荣誉和尊严、权利和受法律保护的第三方利益，助长宗教、民族或种族不和，包含暴力或不人道对待动物的场面等;<br />
	* 淫秽或冒犯性;<br />
	* 包含麻醉药品广告;<br />
	* 侵犯未成年人的权利;<br />
	* 侵犯第三方的版权和相关权利;<br />
	* 色情性质;<br />
	* 包含任何形式的商业广告。<br />
	</div>
	<div class="foot">
	<img src="/style/icons/str2.gif" alt="*"> <?=user::nick($ank['id'],1,0,0)?> | 
	<a href="/photo/<?=$ank['id']?>/">相册</a> | 
	<a href="/photo/<?=$ank['id']?>/<?=$gallery['id']?>/"><?=text($gallery['name'])?></a> | 
	<b>上传照片</b>
	</div>
	<?
	include_once '../sys/inc/tfoot.php';
	exit;
}
?>