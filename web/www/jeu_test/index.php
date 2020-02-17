<?php
if (isset($idsessadm))
{
    $change_perso = $num_perso;
}
require G_CHE . 'includes/classes.php';
$verif_connexion = new verif_connexion();
$verif_connexion->ident();
$verif_auth = $verif_connexion->verif_auth;
if ($verif_auth)
{
    $test_auth = true;
    //echo "Debug true <br>";
} else
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

/**
 * TODO code inutile, à supprimer ?
 */
/*
if(isset($nom_perso) && $nom_perso == "admin")
{
    $stmt      = $pdo->query("select perso_nom from perso where perso_cod = " . $num_perso);
	$result    = $stmt->fetch();
	$nom_perso =$result['perso_nom'];
}*/

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

if (isset($_POST['changed_frameless']) && $_POST['changed_frameless'] == 1)
{
	$valeur = isset($_POST['frameless']) ? 'O' : 'N';
    $stmt   = $pdo->query("update compte set compt_frameless='$valeur' where compt_cod = $compt_cod");
}
$page="perso2.php";
$req_msg="select count(*) as nombre from messages_dest where dmsg_perso_cod =".$perso_cod." and dmsg_lu = 'N' and dmsg_archive = 'N' ";
$stmt = $pdo->query($req_msg);
$result = $stmt->fetch();
$nb_msg=$result['nombre'];
if($nb_msg!=0)
	$page="messagerie2.php";
header('Location: ' . $page);


