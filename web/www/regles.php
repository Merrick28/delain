<?php 
include 'connexion.php';
include 'includes/template.inc';
$t = new template;
$t->set_file('FileRef','template/delain/index.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
//
// identification
//
ob_start();
include G_CHE . "ident.php";
$ident = ob_get_contents();
ob_end_clean();
if($verif_auth)
{
	$ident = '<b>' . $compt_nom . '</b><br />
	<b><a href="validation_login2.php" target="droite">Jouer</a></b><br />
	<a href="logout.php" target="_top">Se déconnecter</a>';
}
$t->set_var("IDENT",$ident);
//
//Contenu de la div de droite
//
$contenu_page = '';
if(!isset($_GET['regle_cod'])    || sscanf($_GET['regle_cod'] , "%u" , $regle_cod) == 0)
    $regle_cod=1;
$contenu_page .= '<div class="titre">Règles</div>';
$contenu_page .= '<center><i>Règles en cours de mise à jour (en gras) pour tenir compte des modifications récentes. En attendait la fin de leur réécriture, la version ancienne reste disponible</i>
<table width="80%">';
$res_regles=pg_exec($dbconnect,"select regle_cod,regle_titre,regle_texte from regles order by regle_cod");
$nb_regles=pg_numrows($res_regles);
for($cpt=0;$cpt<$nb_regles;$cpt++)
{
	if (fmod($cpt,2) == 0)
	{
		$contenu_page .= "<tr>";
	}
	$contenu_page .= "<td class=\"soustitre2\" valign=\"top\" width=\"50%\">";
	$tab_regles=pg_fetch_array($res_regles,$cpt);
	$contenu_page .= '<a href="regles.php?regle_cod='.$tab_regles[0].'">'.$tab_regles[1].'</a>';
	$contenu_page .= "</td>";
	if (fmod(($cpt+1),2) == 0)
	{
		$contenu_page .= "</tr>";
	}
}
$contenu_page .= '</table></center>';
$contenu_page .= '<div class="titre">A savoir</div>';
$res=pg_exec($dbconnect,"select regle_titre,regle_texte from regles where regle_cod='".$regle_cod."'");
$tab=pg_fetch_array($res,0);
$contenu_page .= "<b>".$tab[0]."</b><br>".$tab[1];
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>

