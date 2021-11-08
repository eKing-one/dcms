<?投诉有关投诉有关




include_once '../../../sys/inc/start.php';




include_once '../../../sys/inc/compress.php';




include_once '../../../sys/inc/sess.php';




include_once '../../../sys/inc/home.php';




include_once '../../../sys/inc/settings.php';




include_once '../../../sys/inc/db_connect.php';




include_once '../../../sys/inc/ipua.php';




include_once '../../../sys/inc/fnc.php';




include_once '../../../sys/inc/user.php';









if (isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id` = '".intval($_GET['id'])."'"),0)==1)




{




$post=dbassoc(dbquery("SELECT * FROM `spamus` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));









$spamer = get_user($post['id_spam']);




$ank=get_user($post['id_user']);









if ($user['group_access'] == 2)




$adm = 'Модератором чата';




elseif ($user['group_access'] == 3)




$adm = 'Модератором форума';




elseif ($user['group_access'] == 4)




$adm = 'Модератором зоны обмена';




elseif ($user['group_access'] == 5)




$adm = 'Модератором библиотеки';




elseif ($user['group_access'] == 6)




$adm = 'Модератором фотографий';




elseif ($user['group_access'] == 7)




$adm = 'Модератором';




elseif ($user['group_access'] == 8)




$adm = 'Администратором';




elseif ($user['group_access'] == 9)




$adm = 'Главным администратором';




elseif ($user['group_access'] == 11)




$adm = 'Модератором дневников';




elseif ($user['group_access'] == 12)




$adm = 'Модератором гостевой';




elseif ($user['group_access'] == 15)




$adm = 'Создателем';









if ($user['group_access']==2)




{




$types = "chat";




}




elseif ($user['group_access']==3)




{




$types ="forum";




}




elseif ($user['group_access']==4)




{




$types = "obmen_komm";




}




elseif ($user['group_access']==5)




{




$types = "lib_komm";




}




elseif ($user['group_access']==6)




{




$types = "foto_komm";




}




elseif ($user['group_access']==11)




{




$types = "notes_komm' ";




}




elseif ($user['group_access']==12)




{




$types = "guest";




}




elseif (($user['group_access']>6 && $user['group_access']<10) || $user['group_access']==15)




{




$types = true;




}




else




{




$types = false;




}









if ($types == $post['types'] || $types == true)




{




admin_log('Жалобы','Удаление жалобы',"Удаление жалобы от $ank[nick] на $spamer[nick]");




// отправка сообщения




if (isset($_GET['otkl']))




$msg = "Ваша жалоба на пользователя [b]$spamer[nick][/b] отклонена $adm [b]$user[nick][/b] [br][red]Будьте внимательней, в следующий раз это может привести к блокировке вашего аккаунта![/red]";




else




$msg = "Ваша жалоба на пользователя [b]$spamer[nick][/b] рассмотрена $adm [b]$user[nick][/b]. [br][b]$ank[nick][/b] спасибо вам за вашу бдительность! .дружба.";









dbquery("INSERT INTO `mail` (`id_user`, `id_kont`, `msg`, `time`) values('0', '$ank[id]', '".my_esc($msg)."', '$time')");




dbquery("DELETE FROM `spamus` WHERE `id` = '$post[id]'");




}









}









if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=NULL)




header("Location: ".$_SERVER['HTTP_REFERER']);




else




header("Location: index.php?".SID);









?>