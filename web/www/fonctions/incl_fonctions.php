<?php 
function is_bernardo($perso_cod)
{
	$dbconnect = pg_connect("host=127.0.0.1 dbname=sdewitte user=sdewitte password=") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_pos = "select count(*) from bonus where bonus_perso_cod = $perso_cod and bonus_bernardo = 'O' ";
	$res_pos = pg_exec($dbconnect,$req_pos);
	$tab_pos = pg_fetch_array($res_pos,0);
	if ($tab_pos[0] != 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
?>
