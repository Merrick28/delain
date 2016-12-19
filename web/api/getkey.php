<?
define('NOGOOGLE', 1);
echo $var1;
// on fait le require qui va bien
//require '/home/delain/public_html/www/includes/delain_header.php';
// on a en entrée : $numappli,$nomcompte,$extension
require "classes.php";
// a partir d'ici, on peut commencer à requêter
$ano = false;
$erreur = '';
// on regarde si l'appli est déclarée
$req = "select * from auth.appli where appli_cod = " . $numappli;
$db->query($req);
if ($db->nf() != 1) {
    $erreur .= "Anomalie, appli non trouvée ou compteur incorrect - ";
    $ano = true;
}
// on regarde si le compte existe
$req = "select * from compte where compt_cod = " . $numcompte;
$db->query($req);
if ($db->nf() != 1) {
    $erreur .= "Anomalie, compte non trouvé ou compteur incorrect - ";
    $ano = true;
}
// on regarde s'il existe déjà une demande pour ce compte là
$req = "select * from auth.demande_temp
	where dtemp_appli_cod = " . $numappli . "
	and dtemp_compt_cod = " . $numcompte . "
	and dtemp_valide
	and not dtemp_cle_delivree";
$db->query($req);
if ($db->nf() == 0) {
    $ano = true;
    $erreur .= "Aucune demande validée pour cette appli/ce compte - ";
} else {
    // on repousse le timer
    $db->next_record();
    $dtemp_cod = $db->f('dtemp_cod');
    $dtemp_cle = $db->f('dtemp_cle');
    $dtemp_id = $db->f('dtemp_id');
    $req = "update auth.demande_temp	
		set dtemp_cle_delivree = true
		where dtemp_cod = " . $dtemp_cod;
    $db->query($req);
}
//
// On passe à l'affichage
//
require "smarty/Smarty.class.php";
$smarty = new Smarty();
$smarty->template_dir = '/home/delain/public_html/api';
$smarty->compile_dir = '/home/delain/public_html/api/compile';
$smarty->cache_dir = '/home/delain/public_html/api/cache';

$data = !$ano ? array(
    array('name' => 'id', 'valeur' => $dtemp_id),
    array('name' => 'cle', 'valeur' => $dtemp_cle)
) : array(
    array('name' => 'Resultat', 'valeur' => 'KO'),
    array('name' => 'Détail', 'valeur' => $erreur)
);
$smarty->assign('type', "getkey");
$smarty->assign('data', $data);
switch ($extension) {
    case 'xml':
        header('Content-Type: text/xml', true);
        $smarty->display('gen_xml.tpl');
        break;
    case 'json':
        $smarty->display('gen_json.tpl');
        break;
}