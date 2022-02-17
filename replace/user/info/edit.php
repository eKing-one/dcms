<?

include_once '../../sys/inc/home.php';
include_once START;
include_once COMPRESS;
include_once SESS;
include_once SETTINGS;
include_once DB_CONNECT;
include_once IPUA;
include_once FNC;
include_once SHIF;
include_once USER;
include_once THEAD;

only_reg();
$set['title']='Редактирование анкеты';
title();
aut();

if (isset($_POST['save'])){
//----------чем занимаюсь------------//
if (isset($_POST['ank_zan']) && strlen2($_POST['ank_zan'])<=215)
{
if (preg_match('#[^A-zА-я0-9 _\-\=\+\(\)\*\!\?\.,]#ui',$_POST['ank_zan']))$err[]='В поле "Чем занимаюсь" используются запрещенные символы';
else {
$user['ank_zan']=$_POST['ank_zan'];
dbquery("UPDATE `user` SET `ank_zan` = '".my_esc($user['ank_zan'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
}
else $err[]='Слишком большой текст';

//----------авто------------//
if (isset($_POST['ank_avto_n']) && $_POST['ank_avto_n']==3)
{
$user['ank_avto_n']=3;
dbquery("UPDATE `user` SET `ank_avto_n` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_avto_n']) && $_POST['ank_avto_n']==2)
{
$user['ank_avto_n']=2;
dbquery("UPDATE `user` SET `ank_avto_n` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_avto_n']) && $_POST['ank_avto_n']==1)
{
$user['ank_avto_n']=1;
dbquery("UPDATE `user` SET `ank_avto_n` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_avto_n']) && $_POST['ank_avto_n']==0)
{
$user['ank_avto_n']=0;
dbquery("UPDATE `user` SET `ank_avto_n` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}

if (isset($_POST['ank_avto']) && strlen2($_POST['ank_avto'])<=215)
{

if (preg_match('#[^A-zА-я0-9 _\-\=\+\(\)\*\!\?\.,]#ui',$_POST['ank_avto']))$err[]='В поле "Название\Марка авто" используются запрещенные символы';
else {
$user['ank_avto']=$_POST['ank_avto'];
dbquery("UPDATE `user` SET `ank_avto` = '".my_esc($user['ank_avto'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
}
else $err[]='О вашем авто нужно писать меньше :)';

//----------проживание------------//
if (isset($_POST['ank_proj']) && $_POST['ank_proj']==1)
{
$user['ank_proj']=1;
dbquery("UPDATE `user` SET `ank_proj` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_proj']) && $_POST['ank_proj']==0)
{
$user['ank_proj']=0;
dbquery("UPDATE `user` SET `ank_proj` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_proj']) && $_POST['ank_proj']==2)
{
$user['ank_proj']=2;
dbquery("UPDATE `user` SET `ank_proj` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_proj']) && $_POST['ank_proj']==3)
{
$user['ank_proj']=3;
dbquery("UPDATE `user` SET `ank_proj` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_proj']) && $_POST['ank_proj']==4)
{
$user['ank_proj']=4;
dbquery("UPDATE `user` SET `ank_proj` = '4' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_proj']) && $_POST['ank_proj']==5)
{
$user['ank_proj']=5;
dbquery("UPDATE `user` SET `ank_proj` = '5' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_proj']) && $_POST['ank_proj']==6)
{
$user['ank_proj']=6;
dbquery("UPDATE `user` SET `ank_proj` = '6' WHERE `id` = '$user[id]' LIMIT 1");
}

//----------материальное положение------------//
if (isset($_POST['ank_mat_pol']) && $_POST['ank_mat_pol']==1)
{
$user['ank_mat_pol']=1;
dbquery("UPDATE `user` SET `ank_mat_pol` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_mat_pol']) && $_POST['ank_mat_pol']==0)
{
$user['ank_mat_pol']=0;
dbquery("UPDATE `user` SET `ank_mat_pol` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_mat_pol']) && $_POST['ank_mat_pol']==2)
{
$user['ank_mat_pol']=2;
dbquery("UPDATE `user` SET `ank_mat_pol` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_mat_pol']) && $_POST['ank_mat_pol']==3)
{
$user['ank_mat_pol']=3;
dbquery("UPDATE `user` SET `ank_mat_pol` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_mat_pol']) && $_POST['ank_mat_pol']==4)
{
$user['ank_mat_pol']=4;
dbquery("UPDATE `user` SET `ank_mat_pol` = '4' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_mat_pol']) && $_POST['ank_mat_pol']==5)
{
$user['ank_mat_pol']=5;
dbquery("UPDATE `user` SET `ank_mat_pol` = '5' WHERE `id` = '$user[id]' LIMIT 1");
}
//----------имя------------//
if (isset($_POST['ank_name']) && preg_match('#^([ЁёA-zА-я \-]*)$#ui', $_POST['ank_name']))
{
$user['ank_name']=$_POST['ank_name'];
mysql_query("UPDATE `user` SET `ank_name` = '".my_esc($user['ank_name'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='Неверный формат имени';



//----------глаза------------//
if (isset($_POST['ank_cvet_glas']) && preg_match('#^([ЁёA-zА-я \-]*)$#ui', $_POST['ank_cvet_glas']))
{
$user['ank_cvet_glas']=$_POST['ank_cvet_glas'];
mysql_query("UPDATE `user` SET `ank_cvet_glas` = '".my_esc($user['ank_cvet_glas'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='Неверный формат цвет глаз';


//----------волосы------------//
if (isset($_POST['ank_volos']) && preg_match('#^([ЁёA-zА-я \-]*)$#ui', $_POST['ank_volos']))
{
$user['ank_volos']=$_POST['ank_volos'];
mysql_query("UPDATE `user` SET `ank_volos` = '".my_esc($user['ank_volos'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='Неверный формат цвет глаз';


//----------дата рождения------------//
if (isset($_POST['ank_d_r']) && (is_numeric($_POST['ank_d_r']) && $_POST['ank_d_r']>0 && $_POST['ank_d_r']<=31 || $_POST['ank_d_r']==NULL))
{
$user['ank_d_r']= (int) $_POST['ank_d_r'];
if ($user['ank_d_r']==null)$user['ank_d_r']='null';
mysql_query("UPDATE `user` SET `ank_d_r` = $user[ank_d_r] WHERE `id` = '$user[id]' LIMIT 1");
if ($user['ank_d_r']=='null')$user['ank_d_r']=NULL;
}
else $err[]='Неверный формат дня рождения';

if (isset($_POST['ank_m_r']) && (is_numeric($_POST['ank_m_r']) && $_POST['ank_m_r']>0 && $_POST['ank_m_r']<=12 || $_POST['ank_m_r']==NULL))
{
$user['ank_m_r']= (int) $_POST['ank_m_r'];
if ($user['ank_m_r']==null)$user['ank_m_r']='null';
mysql_query("UPDATE `user` SET `ank_m_r` = $user[ank_m_r] WHERE `id` = '$user[id]' LIMIT 1");
if ($user['ank_m_r']=='null')$user['ank_m_r']=NULL;
}
else $err[]='Неверный формат месяца рождения';

if (isset($_POST['ank_g_r']) && (is_numeric($_POST['ank_g_r']) && $_POST['ank_g_r']>0 && $_POST['ank_g_r']<=date('Y') || $_POST['ank_g_r']==NULL))
{
$user['ank_g_r']= (int) $_POST['ank_g_r'];
if ($user['ank_g_r']==null)$user['ank_g_r']='null';
mysql_query("UPDATE `user` SET `ank_g_r` = $user[ank_g_r] WHERE `id` = '$user[id]' LIMIT 1");
if ($user['ank_g_r']=='null')$user['ank_g_r']=NULL;
}
else $err[]='Неверный формат года рождения';




//---------------город----------------//
if (isset($_POST['ank_city']) && preg_match('#^([A-zА-я \-]*)$#ui', $_POST['ank_city']))
{
$user['ank_city']=$_POST['ank_city'];
mysql_query("UPDATE `user` SET `ank_city` = '".my_esc($user['ank_city'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='Неверный формат названия города';


//--------------вес----------------//
if (isset($_POST['ank_ves']) && (intval($_POST['ank_ves']) && strlen($_POST['ank_ves'])>=1 && strlen($_POST['ank_ves'])<=4 || $_POST['ank_ves']==NULL))
{
$user['ank_ves']=$_POST['ank_ves'];
if ($user['ank_ves']==null)$user['ank_ves']='null';
mysql_query("UPDATE `user` SET `ank_ves` = $user[ank_ves] WHERE `id` = '$user[id]' LIMIT 1");

if ($user['ank_ves']=='null')$user['ank_ves']=NULL;
}
else $err[]='Неверный формат веса';


//--------------рост----------------//
if (isset($_POST['ank_rost']) && (intval($_POST['ank_rost']) && strlen($_POST['ank_rost'])>=1 && strlen($_POST['ank_rost'])<=4 || $_POST['ank_rost']==NULL))
{
$user['ank_rost']=$_POST['ank_rost'];
if ($user['ank_rost']==null)$user['ank_rost']='null';
mysql_query("UPDATE `user` SET `ank_rost` = $user[ank_rost] WHERE `id` = '$user[id]' LIMIT 1");

if ($user['ank_rost']=='null')$user['ank_rost']=NULL;
}
else $err[]='Неверный формат роста';

//-----------------телосложение-----------------//
if (isset($_POST['ank_telosl']) && $_POST['ank_telosl']==1)
{
$user['ank_telosl']=1;
mysql_query("UPDATE `user` SET `ank_telosl` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_telosl']) && $_POST['ank_telosl']==0)
{
$user['ank_telosl']=0;
mysql_query("UPDATE `user` SET `ank_telosl` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_telosl']) && $_POST['ank_telosl']==2)
{
$user['ank_telosl']=2;
mysql_query("UPDATE `user` SET `ank_telosl` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_telosl']) && $_POST['ank_telosl']==3)
{
$user['ank_telosl']=3;
mysql_query("UPDATE `user` SET `ank_telosl` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_telosl']) && $_POST['ank_telosl']==4)
{
$user['ank_telosl']=4;
mysql_query("UPDATE `user` SET `ank_telosl` = '4' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_telosl']) && $_POST['ank_telosl']==5)
{
$user['ank_telosl']=5;
mysql_query("UPDATE `user` SET `ank_telosl` = '5' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_telosl']) && $_POST['ank_telosl']==6)
{
$user['ank_telosl']=6;
mysql_query("UPDATE `user` SET `ank_telosl` = '6' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_telosl']) && $_POST['ank_telosl']==7)
{
$user['ank_telosl']=7;
mysql_query("UPDATE `user` SET `ank_telosl` = '7' WHERE `id` = '$user[id]' LIMIT 1");
}

//----------------о себе-------------//
if (isset($_POST['ank_o_sebe']) && strlen2($_POST['ank_o_sebe'])<=512)
{

if (preg_match('#[^A-zА-я0-9 _\-\=\+\(\)\*\!\?\.,]#ui',$_POST['ank_o_sebe']))$err[]='В поле "О себе" используются запрещенные символы';
else {
$user['ank_o_sebe']=$_POST['ank_o_sebe'];
mysql_query("UPDATE `user` SET `ank_o_sebe` = '".my_esc($user['ank_o_sebe'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
}
else $err[]='О себе нужно писать меньше :)';

//-----------------пол-----------------//
if (isset($_POST['pol']) && $_POST['pol']==1)
{
$user['pol']=1;
mysql_query("UPDATE `user` SET `pol` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['pol']) && $_POST['pol']==0)
{
$user['pol']=0;
mysql_query("UPDATE `user` SET `pol` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}

if (!isset($err))
{
$_SESSION['message'] = 'Изменения успешно приняты';

	mysql_query("UPDATE `user` SET `rating_tmp` = '".($user['rating_tmp']+1)."' WHERE `id` = '$user[id]' LIMIT 1");
		
		if (isset($_GET['act']) && $_GET['act']=='ank')
			header("Location: /user/info/anketa.php?".SID);			
		else
			header("Location: /user/info/edit.php?".SID);
			
			exit;
}



}
err();
echo "<div id='comments' class='menus'>";

echo "<div class='webmenu'>";
echo "<a href='/user/info/edit.php' class='activ'>Основное</a>";
echo "</div>"; 

echo "<div class='webmenu last'>";
echo "<a href='/user/info/edit.meet.php'>Знакомства</a>";
echo "</div>"; 

echo "<div class='webmenu last'>";
echo "<a href='/user/info/edit.contacts.php'>Контакты</a>";
echo "</div>";
echo "<div class='webmenu last'>";
echo "<a href='/user/info/edit.habits.php'>Привычки</a>";
echo "</div>";
echo "</div>";

	echo "<form method='post' action=''>";	
echo "<div class='nav2'>";
echo "<b>Имя в реале:</b><br /><input type='text' name='ank_name' value='".htmlspecialchars($user['ank_name'],false)."' maxlength='32' /></div>";
echo "<div class='nav1'>";
echo "<b>Пол:</b><br /> <label><input name='pol' type='radio' ".($user['pol']==1?' checked="checked"':null)." value='1' /> Муж.</label>
	<label><input name='pol' type='radio' ".($user['pol']==0?' checked="checked"':null)." value='0' /> Жен.</label></div>";
echo "<div class='nav2'>";	
echo "<b>Город:</b><br /><input type='text' name='ank_city' value='$user[ank_city]' maxlength='32' /></div>";

echo "<div class='nav1'>";
echo '<b>Дата рождения:</b><br /><select name="ank_d_r">';
    if (!empty($user['ank_d_r']))  echo '<option  value=""></option>';
echo	'<option selected="'.$user['ank_d_r'].'" value="'.$user['ank_d_r'].'" >'.$user['ank_d_r'].'</option>
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	<option value="6">6</option>
	<option value="7">7</option>
	<option value="8">8</option>
	<option value="9">9</option>
	<option value="10">10</option>
	<option value="11">11</option>
	<option value="12">12</option>
	<option value="13">13</option>
	<option value="14">14</option>
	<option value="15">15</option>
	<option value="16">16</option>
	<option value="17">17</option>
	<option value="18">18</option>
	<option value="19">19</option>
	<option value="20">20</option>
	<option value="21">21</option>
	<option value="22">22</option>
	<option value="23">23</option>
	<option value="24">24</option>
	<option value="25">25</option>
	<option value="26">26</option>
	<option value="27">27</option>
	<option value="28">28</option>
	<option value="29">29</option>
	<option value="30">30</option>
	<option value="31">31</option>
	</select>';
		
	echo '<select name="ank_m_r">';
    if (!empty($user['ank_m_r']))  echo '<option  value=""></option>';
echo	'<option selected="'.$user['ank_m_r'].'" value="'.$user['ank_m_r'].'" >'.$user['ank_m_r'].'</option>	
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	<option value="6">6</option>
	<option value="7">7</option>
	<option value="8">8</option>
	<option value="9">9</option>
	<option value="10">10</option>
	<option value="11">11</option>
	<option value="12">12</option>
	</select>';
	
	echo '<select name="ank_g_r">';
    if (!empty($user['ank_g_r']))  echo '<option  value=""></option>';
echo '<option selected="'.$user['ank_g_r'].'" value="'.$user['ank_g_r'].'" >'.$user['ank_g_r'].'</option>';



for( $i = date("Y")-16; $i >= 1940; $i--) {

  echo '<option  value="' . $i . '">' . $i . '</option>';
}
	echo '</select><br/>';
	
echo "</div>";

echo "<div class='nav2'>";
echo "<b>Рост:</b><br /><input type='text' name='ank_rost' value='$user[ank_rost]' maxlength='3' /></div>";

echo "<div class='nav1'>";
echo "<b>Вес:</b><br /><input type='text' name='ank_ves' value='$user[ank_ves]' maxlength='3' /></div>";

echo "<div class='nav2'>";
echo "<b>Цвет глаз:</b><br /><input type='text' name='ank_cvet_glas' value='".htmlspecialchars($user['ank_cvet_glas'],false)."' maxlength='32' /></div>";

echo "<div class='nav1'>";
echo "<b>Волосы:</b><br /><input type='text' name='ank_volos' value='".htmlspecialchars($user['ank_volos'],false)."' maxlength='32' /></div>";

echo "<div class='nav2'>";
echo "<b>Телосложение:</b><br /> 
	<label><input name='ank_telosl' type='radio' ".($user['ank_telosl']==1?' checked="checked"':null)." value='1' />Нет ответа</label><br />
	<label><input name='ank_telosl' type='radio' ".($user['ank_telosl']==2?' checked="checked"':null)." value='2' />Худощавое</label><br />
	<label><input name='ank_telosl' type='radio' ".($user['ank_telosl']==3?' checked="checked"':null)." value='3' />Обычное</label><br />
	<label><input name='ank_telosl' type='radio' ".($user['ank_telosl']==4?' checked="checked"':null)." value='4' />Спортивное</label><br />
	<label><input name='ank_telosl' type='radio' ".($user['ank_telosl']==5?' checked="checked"':null)." value='5' />Мускулистое</label><br />
	<label><input name='ank_telosl' type='radio' ".($user['ank_telosl']==6?' checked="checked"':null)." value='6' />Плотное</label><br />
	<label><input name='ank_telosl' type='radio' ".($user['ank_telosl']==7?' checked="checked"':null)." value='7' />Полное</label><br />
	<label><input name='ank_telosl' type='radio' ".($user['ank_telosl']==0?' checked="checked"':null)." value='0' />Не указано</label><br />";
echo "</div>";

echo "<div class='nav1'>";	
	echo "<b>О себе:</b><br /><textarea maxlength='512' name=\"ank_o_sebe\">$user[ank_o_sebe]</textarea></div>";
	
echo "<div class='nav2'>";	
	echo "<b>Чем занимаюсь:</b><br /><input type='text' name='ank_zan' value='$user[ank_zan]' maxlength='215' /></div>";
	
	echo "<div class='nav1'>";
	echo "<b>Проживание:</b><br /> 
	<label><input name='ank_proj' type='radio' ".($user['ank_proj']==0?' checked="checked"':null)." value='0' />Не указано</label><br />
	<label><input name='ank_proj' type='radio' ".($user['ank_proj']==1?' checked="checked"':null)." value='1' />Отдельная квартира (снимаю или своя)</label><br />
	<label><input name='ank_proj' type='radio' ".($user['ank_proj']==2?' checked="checked"':null)." value='2' />Комната в общежитии, коммуналка</label><br />
	<label><input name='ank_proj' type='radio' ".($user['ank_proj']==3?' checked="checked"':null)." value='3' />Живу с родителями</label><br />
	<label><input name='ank_proj' type='radio' ".($user['ank_proj']==4?' checked="checked"':null)." value='4' />Живу с приятелем / с подругой</label><br />
	<label><input name='ank_proj' type='radio' ".($user['ank_proj']==5?' checked="checked"':null)." value='5' />Живу с партнером или супругом (-ой)</label><br />
	<label><input name='ank_proj' type='radio' ".($user['ank_proj']==6?' checked="checked"':null)." value='6' />Нет постоянного жилья</label><br />";
	echo "</div>";
	
	echo "<div class='nav2'>";
	echo "<b>Наличие автомобиля:</b><br /> 
	<label><input name='ank_avto_n' type='radio' ".($user['ank_avto_n']==0?' checked="checked"':null)." value='0' />Не указано</label><br />
	<label><input name='ank_avto_n' type='radio' ".($user['ank_avto_n']==1?' checked="checked"':null)." value='1' />Есть</label><br />
	<label><input name='ank_avto_n' type='radio' ".($user['ank_avto_n']==2?' checked="checked"':null)." value='2' />Нет</label><br />
	<label><input name='ank_avto_n' type='radio' ".($user['ank_avto_n']==3?' checked="checked"':null)." value='3' />Хочу купить</label><br />";
	echo "<br/><b>Название\Марка авто:</b><br /><input type='text' name='ank_avto' value='".htmlspecialchars($user['ank_avto'],false)."' maxlength='215' /></div>";
	
	echo "<div class='nav1'>";
echo "<b>Материальное положение:</b><br /> 
	<label><input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==0?' checked="checked"':null)." value='0' />Не указано</label><br />
	<label><input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==1?' checked="checked"':null)." value='1' />Непостоянные заработки</label><br />
	<label><input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==2?' checked="checked"':null)." value='2' />Постоянный небольшой доход</label><br />
	<label><input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==3?' checked="checked"':null)." value='3' />Стабильный средний доход</label><br />
	<label><input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==4?' checked="checked"':null)." value='4' />Хорошо зарабатываю / обеспечен</label><br />
	<label><input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==5?' checked="checked"':null)." value='5' />Не зарабатываю</label><br />";

echo "</div>";
echo "<div class='nav2'>";
echo "<input type='submit' name='save' value='Сохранить' /></div></form>\n";

echo "<div class='foot'><img src='/style/icons/str.gif' alt='*'> <a href='anketa.php'>Посмотреть анкету</a><br />";

if(isset($_SESSION['refer']) && $_SESSION['refer']!=NULL && otkuda($_SESSION['refer']))
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='$_SESSION[refer]'>".otkuda($_SESSION['refer'])."</a><br />\n";
echo '</div>';
	
include_once TFOOT;
?>