<?
function smiles($msg)
{
	global $user;

	$q = dbquery("SELECT `id`, `smile` FROM `smile`");

	while($post = dbarray($q))
	{
		$sm = explode("|", $post['smile']);

		for ($i = 0; $i < count($sm); $i++)
		{
			$msg = str_replace($sm[$i], '<img src="/style/smiles/' . $post['id'] . '.gif" alt="smile" />', $msg);
		}
	}

	return $msg;
}
?>