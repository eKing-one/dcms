<?
function permissions($filez)
{
	return decoct(@fileperms("$filez")) % 1000;
}

function test_chmod($df,$chmod)
{
	global $err,$user;
	
	if (isset($user) && $user['level'] == 10)
	$show_df = preg_replace('#^'.preg_quote(H).'#', '/', $df);
	else $show_df = $df;


	@list($f_chmod1, $f_chmod2, $f_chmod3) = str_split(permissions($df));
	list($n_chmod1,$n_chmod2,$n_chmod3) = str_split($chmod);
	//list($m_chmod1,$m_chmod2,$m_chmod3)=str_split($max_chmod);

	if ($f_chmod1<$n_chmod1 || $f_chmod2<$n_chmod2 || $f_chmod3<$n_chmod3)
	{
		$err[] = 'Установите CHMOD ' . $n_chmod1 . $n_chmod2 . $n_chmod3 . ' на ' . $show_df;
		echo '<span class="off">' . $show_df . ' : [' . $f_chmod1 . $f_chmod2 . $f_chmod3 . '] - > ' . $n_chmod1 . $n_chmod2 . $n_chmod3 . '</span><br />';
	}
	else
	{
		echo '<span class="on">' . $show_df . ' (' . $n_chmod1 . $n_chmod2 . $n_chmod3 . ') : ' . 
		$f_chmod1 . $f_chmod2 . $f_chmod3 . ' (ok)</span><br />';
	}
}

if (file_exists(H.'install/'))
test_chmod(H.'install/', 777);

test_chmod(H.'sys/dat/',777);
test_chmod(H.'sys/forum/files',777);
test_chmod(H.'sys/gallery/48/',777);
test_chmod(H.'sys/gallery/50/',777);
test_chmod(H.'sys/gallery/128/',777);
test_chmod(H.'sys/gallery/640/',777);
test_chmod(H.'sys/gallery/foto/',777);
test_chmod(H.'sys/inc/',777);
test_chmod(H.'sys/fnc/',777);
test_chmod(H.'sys/obmen/files/',777);
test_chmod(H.'sys/obmen/screens/14/',777);
test_chmod(H.'sys/obmen/screens/48/',777);
test_chmod(H.'sys/obmen/screens/128/',777);
test_chmod(H.'sys/update/',777);
test_chmod(H.'sys/tmp/',777);
test_chmod(H.'style/themes/',777);
test_chmod(H.'style/smiles/',777);
test_chmod(H.'sys/gift/',777);

if (file_exists(H.'sys/dat/settings_6.2.dat'))
test_chmod(H.'sys/dat/settings_6.2.dat',666);
?>