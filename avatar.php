<?php

	// SimbaSocialNetwork

	// http://mydcms.ru

	// Искатель

	

include_once 'sys/inc/start.php';

include_once 'sys/inc/compress.php';

include_once 'sys/inc/sess.php';

include_once 'sys/inc/home.php';

include_once 'sys/inc/settings.php';

include_once 'sys/inc/db_connect.php';

include_once 'sys/inc/ipua.php';

include_once 'sys/inc/fnc.php';

include_once 'sys/inc/user.php';



only_reg();

$set['title']='我的头像';

include_once 'sys/inc/thead.php';

title();



err();

aut();

	

	

	echo "<div class='main'>";

	echo avatar($ank['id'], true, 128, false);

	echo "</div>";

	echo "<div class='main'>";

	echo "为了在你的页面上安装一个头像，上传一张照片到你的相册，然后点击链接 \"让它成为主要的\"";

	echo "</div>";

	

	

	//--------------------------相片册-----------------------------//

	echo "<div class='main'>";echo "<img src='/style/icons/foto.png' alt='*' /> ";

	echo "<a href='/foto/$user[id]/'>照片</a> ";

	echo "(" . dbresult(dbquery("SELECT COUNT(*) FROM `gallery_foto` WHERE `id_user` = '$user[id]'"),0) . ")";

	echo "</div>";

	

	

	//------------------------------------------------------------------// 



include_once 'sys/inc/tfoot.php';

?>