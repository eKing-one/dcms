<?php
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
		$err[] = 'CHMOD ' . $n_chmod1 . $n_chmod2 . $n_chmod3 . ' 在 ' . $show_df;
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
test_chmod(H.'sys/inc/',777);
test_chmod(H.'sys/fnc/',777);
test_chmod(H.'files/down/',777);
test_chmod(H.'files/forum',777);
test_chmod(H.'files/gallery/48/',777);
test_chmod(H.'files/gallery/50/',777);
test_chmod(H.'files/gallery/128/',777);
test_chmod(H.'files/gallery/640/',777);
test_chmod(H.'files/gallery/photo/',777);
test_chmod(H.'files/screens/14/',777);
test_chmod(H.'files/screens/48/',777);
test_chmod(H.'files/screens/128/',777);
test_chmod(H.'files/gift/',777);
test_chmod(H.'sys/update/',777);
test_chmod(H.'sys/tmp/',777);
test_chmod(H.'style/themes/',777);
test_chmod(H.'style/smiles/',777);

?>