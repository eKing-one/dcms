<?
function adm_check()
{
	global $time;
	if (!isset($_SESSION['adm_auth']) || $_SESSION['adm_auth'] < $time)
	{
		header('Location: /adm_panel/?go=' . base64_encode($_SERVER['REQUEST_URI']) . '&' . passgen() . '&' . SID); exit;
	}

	if (isset($_SESSION['adm_auth']) && $_SESSION['adm_auth']>$time)
	{
		$_SESSION['adm_auth'] = $time + 600;
	}
}
?>