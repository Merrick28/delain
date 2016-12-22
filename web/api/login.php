<?
define('NOGOOGLE',1);
echo $var1;
// on fait le require qui va bien
//require '/home/delain/public_html/www/includes/delain_header.php';
// on a en entrée : $numappli,$numcompte,$id,$cle,$extension
require "classes.php";
// a partir d'ici, on peut commencer à requêter
$ano = false;
$erreur = '';
// on vérifie qu'on a tout ce qu'il faut
$req = "select * from auth.demande_temp
	where dtemp_appli_cod = $numappli
	and dtemp_compt_cod = $numcompte
	and dtemp_id = '" . $id . "'
	and dtemp_cle = '" . $cle . "'
	and dtemp_valide
	and dtemp_cle_delivree";
$db->query($req);
if($db->nf() == 0)
{
	$ano = true;
	$erreur .= "Aucune autorisation pour ce compte/appli - ";
}
else
{
	$db->next_record();
	$dtemp_cod = $db->f('dtemp_cod');
	// on regarde s'il existe déjà une session
	$req = "select * from auth.session
		where sess_dtemp_cod = $dtemp_cod ";
	$db->query($req);
	if($db->nf() == 0)
	{
		// création de session
		$sess_key = uniqid('');
		$req = "insert into auth.session (sess_dtemp_cod,sess_key)
			values (" . $dtemp_cod . ",'" . $sess_key . "')";
		$db->query($req);
	}
	else
	{
		// on repousse la date 
		$db->next_record();
		$sess_key = $db->f('sess_key');
		/*$req = "update auth.session
			set sess_date = now()
			where sess_dtemp_cod = " . $dtemp_cod;
		$db->query($req);*/
	}
}


//
// On passe à l'affichage
//
//echo $sess_key;
require "smarty/Smarty.class.php";
$smarty = new Smarty();
$smarty->template_dir = '/home/delain/public_html/api';
$smarty->compile_dir = '/home/delain/public_html/api/compile';
$smarty->cache_dir = '/home/delain/public_html/api/cache';
$i = 1;
if(!$ano)
{
	$data = array(
			 array('name' => 'id_session', 'valeur' => $sess_key)
	);
	// on va cherche les numéros de perso ?
	$req_perso = "select perso_cod,perso_nom
		from perso,perso_compte
		where pcompt_compt_cod = $numcompte
		and pcompt_perso_cod = perso_cod
		and perso_actif = 'O'
		and perso_type_perso = 1
		order by perso_cod ";
	//echo $req_perso;
	$db->query($req_perso);
	while($db->next_record())
	{
		$persotab = array(array('name' => 'nom_perso_' . $i, 'valeur' => urlencode($db->f('perso_nom'))));
		$data = array_merge($data,$persotab);
		$persotab = array(array('name' => 'num_perso_' . $i, 'valeur' => $db->f('perso_cod')));
		$data = array_merge($data,$persotab);	
		//print_r($persotab);
		//print_r($data);
		$i++;
	}
	
	// familiers
	$req_perso = "select perso_nom,perso_cod
		from perso,perso_compte,perso_familier
		where pcompt_compt_cod = $numcompte
		and pcompt_perso_cod = pfam_perso_cod
		and pfam_familier_cod = perso_cod
		and perso_actif = 'O'
		and perso_type_perso = 3
		order by pfam_perso_cod "; 
	$db->query($req_perso);
	while($db->next_record())
	{
		
		$persotab = array(array('name' => 'nom_perso_' . $i, 'valeur' => urlencode($db->f('perso_nom'))));
		$data = array_merge($data,$persotab);
		$persotab = array(array('name' => 'num_perso_' . $i, 'valeur' => $db->f('perso_cod')));
		$data = array_merge($data,$persotab);	
		$i++;
	}
}	
else
{
	$data = array(
			 array('name' => 'Resultat', 'valeur' => 'KO'),
			 array('name' => 'Détail', 'valeur' => $erreur)
	);
}
$smarty->assign('type',"session");
$smarty->assign('data',$data);
switch($extension)
{
	case 'xml':
		header('Content-Type: text/xml',true);
		$smarty->display('gen_xml.tpl');
		break;
	case 'json':
		$smarty->display('gen_json.tpl');
		break;
}	

