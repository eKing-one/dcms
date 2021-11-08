<?
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
$temp_set=$set;
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('adm_set_sys',null,'index.php?'.SID);
adm_check();

$set['title']='Настройки BBcode';
include_once '../sys/inc/thead.php';
title();
if (isset($_POST['save']))
{

if (isset($_POST['bb_i']) && $_POST['bb_i']==1)$temp_set['bb_i']=1; else $temp_set['bb_i']=0;
if (isset($_POST['bb_u']) && $_POST['bb_u']==1)$temp_set['bb_u']=1; else $temp_set['bb_u']=0;
if (isset($_POST['bb_b']) && $_POST['bb_b']==1)$temp_set['bb_b']=1; else $temp_set['bb_b']=0;
if (isset($_POST['bb_big']) && $_POST['bb_big']==1)$temp_set['bb_big']=1; else $temp_set['bb_big']=0;
if (isset($_POST['bb_small']) && $_POST['bb_small']==1)$temp_set['bb_small']=1; else $temp_set['bb_small']=0;
if (isset($_POST['bb_code']) && $_POST['bb_code']==1)$temp_set['bb_code']=1; else $temp_set['bb_code']=0;
if (isset($_POST['bb_red']) && $_POST['bb_red']==1)$temp_set['bb_red']=1; else $temp_set['bb_red']=0;
if (isset($_POST['bb_yellow']) && $_POST['bb_yellow']==1)$temp_set['bb_yellow']=1; else $temp_set['bb_yellow']=0;
if (isset($_POST['bb_green']) && $_POST['bb_green']==1)$temp_set['bb_green']=1; else $temp_set['bb_green']=0;
if (isset($_POST['bb_blue']) && $_POST['bb_blue']==1)$temp_set['bb_blue']=1; else $temp_set['bb_blue']=0;
if (isset($_POST['bb_white']) && $_POST['bb_white']==1)$temp_set['bb_white']=1; else $temp_set['bb_white']=0;
if (isset($_POST['bb_size']) && $_POST['bb_size']==1)$temp_set['bb_size']=1; else $temp_set['bb_size']=0;
if (isset($_POST['bb_http']) && $_POST['bb_http']==1)$temp_set['bb_http']=1; else $temp_set['bb_http']=0;
if (isset($_POST['bb_url']) && $_POST['bb_url']==1)$temp_set['bb_url']=1; else $temp_set['bb_url']=0;
if (isset($_POST['bb_img']) && $_POST['bb_img']==1)$temp_set['bb_img']=1; else $temp_set['bb_img']=0;



if (save_settings($temp_set))
{
admin_log('Настройки','Система','Изменение параметров BBcode');
msg('Настройки успешно приняты');
}

else
$err='Нет прав для изменения файла настроек';
}
err();
aut();



echo "<form method='post' action='?$passgen'>\n";


echo "<label><input type='checkbox'".($temp_set['bb_i']?" checked='checked'":null)." name='bb_i' value='1' /> Куксив [i]*</label><br />\n";
echo "<label><input type='checkbox'".($temp_set['bb_u']?" checked='checked'":null)." name='bb_u' value='1' /> Подчеркнутый [u]*</label><br />\n";
echo "<label><input type='checkbox'".($temp_set['bb_b']?" checked='checked'":null)." name='bb_b' value='1' /> Жирный [b]*</label><br />\n";
echo "<label><input type='checkbox'".($temp_set['bb_big']?" checked='checked'":null)." name='bb_big' value='1' /> Большой [big]*</label><br />\n";
echo "<label><input type='checkbox'".($temp_set['bb_small']?" checked='checked'":null)." name='bb_small' value='1' /> Маленький [small]*</label><br />\n";
echo "<label><input type='checkbox'".($temp_set['bb_code']?" checked='checked'":null)." name='bb_code' value='1' /> Подсветка PHP-кода [code]*</label><br />\n";
echo "<label><input type='checkbox'".($temp_set['bb_red']?" checked='checked'":null)." name='bb_red' value='1' /> Красный текст [red]*</label><br />\n";
echo "<label><input type='checkbox'".($temp_set['bb_yellow']?" checked='checked'":null)." name='bb_yellow' value='1' /> Желтый текст [yellow]*</label><br />\n";
echo "<label><input type='checkbox'".($temp_set['bb_green']?" checked='checked'":null)." name='bb_green' value='1' /> Зеленый текст [green]*</label><br />\n";
echo "<label><input type='checkbox'".($temp_set['bb_blue']?" checked='checked'":null)." name='bb_blue' value='1' /> Синий текст [blue]*</label><br />\n";
echo "<label><input type='checkbox'".($temp_set['bb_white']?" checked='checked'":null)." name='bb_white' value='1' /> Белый текст [white]*</label><br />\n";
echo "<label><input type='checkbox'".($temp_set['bb_size']?" checked='checked'":null)." name='bb_size' value='1' /> Размер шрифта</label><br />\n";
echo "[size=размер шрифта]текст[/size]<br />\n";
echo "<label><input type='checkbox'".($temp_set['bb_http']?" checked='checked'":null)." name='bb_http' value='1' /> Выделение ссылок</label><br />\n";
echo "http://...<br />\n";
echo "<label><input type='checkbox'".($temp_set['bb_url']?" checked='checked'":null)." name='bb_url' value='1' /> Вставка ссылок</label><br />\n";
echo "[url=адрес ссылки]Название ссылки[/url]<br />\n";
echo "<label><input type='checkbox'".($temp_set['bb_img']?" checked='checked'":null)." name='bb_img' value='1' /> Вставка изображений</label><br />\n";
echo "[img]URL изображения[/img]<br />\n";

echo "<br />\n";
echo "* Необходим закрывающий тег<br />\n";
echo "<input value='Применить' name='save' type='submit' />\n";
echo "</form>\n";

if (user_access('adm_panel_show')){
echo "<div class='foot'>\n";
echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";
echo "</div>\n";
}
include_once '../sys/inc/tfoot.php';
?>