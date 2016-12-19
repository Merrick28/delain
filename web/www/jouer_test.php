<?php 
if(isset($idsessadm))
{
	$change_perso = $num_perso;
}
require_once G_CHE . 'includes/classes.php';
require_once G_CHE . "ident.php";
if($verif_auth)
{
	$test_auth = true;	
	//echo "Debug true <br>";
}
else
{
	echo "Debug false";
	echo "etape 1";
	//header('Location:' . G_URL . 'inter.php');
	die();
}

if(!$verif_auth)
{
	echo "etape 2";
	//header('Location:' . G_URL . 'inter.php');
	die();
}

$db=new base_delain;

if($nom_perso=="admin")
{
	$db->query("select perso_nom from perso where perso_cod =".$num_perso);
	$db->next_record();
	$nom_perso=$db->f("perso_nom");
}

if (!isset($perso_cod))
{
	$auth->logout();
	//$auth->auth_loginform();
	echo "etape 3";
	//header('Location:' . G_URL . 'inter.php');
	die();
}

if ($perso_cod == "")
{
		$auth->logout();
	//$auth->auth_loginform();
	echo "etape 4";
	//header('Location:' . G_URL . 'inter.php');
	die();
}

if ($perso_cod == 0)
{
	$auth->logout();
	echo "etape 5";
	//$auth->auth_loginform();
	//header('Location:' . G_URL . 'inter.php');
	die();
}

$page="jeu_test/perso2.php";
$req_msg="select count(*) as nombre from messages_dest where dmsg_perso_cod =".$perso_cod." and dmsg_lu = 'N' and dmsg_archive = 'N' ";
$db->query($req_msg);
$db->next_record();
$nb_msg=$db->f("nombre");
if($nb_msg!=0)
	$page="jeu_test/messagerie2.php";
?>
<style>
div#colonne1 {
	float: left;
	width: 175px;
	height : 100%;
	background-color :  #00FF00;
	
}
div#colonne2 {
	background-color :  #0000FF;
	margin-right:20px;
}
</style>

<head>
		<title>Les souterrains de Delain</title>
		<link rel="shortcut icon" href="drake_head_red.ico" type="image/gif">
</head>
<div id="colonne1">
<?php  
include "jeu_test/menu.php";
?>
</div>
<div id="colonne2">
<?php 
include $page; 

?>
</div>
</html>

