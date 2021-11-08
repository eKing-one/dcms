<?
include_once 'sys/inc/start.php';

include_once 'sys/inc/compress.php';

include_once 'sys/inc/sess.php';

include_once 'sys/inc/home.php';

include_once 'sys/inc/settings.php';

include_once 'sys/inc/db_connect.php';

include_once 'sys/inc/ipua.php';

$ban_ip_page=true; // чтобы небыло зацикливания

include_once 'sys/inc/fnc.php';

//include_once 'sys/inc/user.php';

$set['title']='知识产权禁令';

include_once 'sys/inc/thead.php';

title();

$err="<h1>从您的IP ($_SERVER[REMOTE_ADDR]) 封锁</h1>";

err();

//aut();

?>
<h2>可能的原因:</h2>

1）从同一个IP地址频繁访问服务器<br />

2)您的IP地址与入侵者的地址匹配<br />


<h2>解决方法:</h2>

1)重新启动您的互联网连接<br />

2）在静态IP地址的情况下，您可以使用代理服务器

<br />
<?include_once 'sys/inc/tfoot.php';?>