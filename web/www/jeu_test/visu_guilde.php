<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');
ob_start();
$db2 = new base_delain;
// on vérifie qu'on soit dans la guilde visitée 
$req_guilde = "select pguilde_meta_caravane, pguilde_meta_noir, guilde_cod, guilde_nom, rguilde_libelle_rang, pguilde_rang_cod, rguilde_admin, pguilde_message
    from guilde
    inner join guilde_perso on pguilde_guilde_cod = guilde_cod
    inner join guilde_rang on rguilde_guilde_cod = guilde_cod and rguilde_rang_cod = pguilde_rang_cod
    where pguilde_perso_cod = $perso_cod 
        and pguilde_valide = 'O' 
        and guilde_cod = $num_guilde";
        
$db->query($req_guilde);

$is_guilde = ($db->nf() != 0);

if ($is_guilde)
{
    $db->next_record();
    
    $perso_meta_noir = $db->f("pguilde_meta_noir");
    $perso_meta_caravane = $db->f("pguilde_meta_caravane");
    if ($db->f("rguilde_admin") == 'O')
    {
        $is_admin = true;
    }
    else
    {
    	$is_admin = false;
    }
}
else
{
    $perso_meta_noir = 'N';
    $perso_meta_caravane = 'N';
    $is_admin = false;
}

$req_guilde = "select guilde_meta_caravane,guilde_meta_noir,guilde_cod,guilde_nom,guilde_description from guilde where guilde_cod = $num_guilde ";
$db->query($req_guilde);
$db->next_record();
$description = $db->f("guilde_description");
$meta_noir = $db->f("guilde_meta_noir");
$meta_caravane = $db->f("guilde_meta_caravane");
$guilde_nom = $db->f("guilde_nom");
echo "<p class=\"titre\">" . $guilde_nom;
if ($db->getparm_n(74) == 1)
{
	if ($is_guilde)
	{
		if ($meta_noir == 'O')
		{
			echo "<hr>";
			echo "<p>Votre guilde est rattachée en meta guildage à la guilde <b>envoyés de Salm'o'rv</b>.<br>";
			if ($perso_meta_noir == 'O')
			{
				echo "<p>Vous êtes rattaché à ce meta guildage.<br>";
				echo "<a href=\"meta_guilde.php?g=n&r=N\">Annuler ce meta-guildage ?</a>";
			}
			else
			{
				echo "<p>Vous n'êtes pas rattaché à ce meta guildage.<br>";
				echo "<a href=\"meta_guilde.php?g=n&r=O\">Se rattacher à ce meta guildage ?</a>";
			}
			echo "<hr>";
		}
		if ($meta_caravane == 'O')
		{
			echo "<hr>";
			echo "<p>Votre guilde est rattachée en meta guildage à la guilde <b>Corporation marchande du R.A.D.I.S</b>.<br>";
			if ($perso_meta_caravane == 'O')
			{
				echo "<p>Vous êtes rattaché à ce meta guildage.<br>";
				echo "<a href=\"meta_guilde.php?g=c&r=N\">Annuler ce meta-guildage ?</a>";
			}
			else
			{
				echo "<p>Vous n'êtes pas rattaché à ce meta guildage.<br>";
				echo "<a href=\"meta_guilde.php?g=c&r=O\">Se rattacher à ce meta guildage ?</a>";
			}
			echo "<hr>";
		}
	}
}
// on regarde s'il y a des révolutions en cours
if ($is_guilde)
{
	$req = "select revguilde_cod from guilde_revolution where revguilde_guilde_cod = $num_guilde ";
	$db->query($req);
	if ($db->nf() != 0)
	{
		echo "<hr>";
		echo "<p style=\"text-align:center;\"><b>Révolution en cours !</b><br>";
		echo "<p style=\"text-align:center;\">Pour en savoir plus, <a href=\"guilde_revolution.php\">cliquez ici !</a>";
		echo "<hr>";
	}	
}
echo '<table width="100%"><tr><td class="titre"><p class="titre">' ,
     $guilde_nom , '</td></tr></table>';
echo  "<p>" . str_replace(chr(127),";",$description);
$stat_guilde = $db->get_stats_guilde($num_guilde);
$req_membre = "select perso_nom,rguilde_libelle_rang,perso_cod,rguilde_admin ";
$req_membre = $req_membre . "from guilde_perso,perso,guilde_rang ";
$req_membre = $req_membre . "where pguilde_guilde_cod = $num_guilde ";
$req_membre = $req_membre . "and pguilde_perso_cod = perso_cod ";
$req_membre = $req_membre . "and pguilde_valide = 'O' ";
$req_membre = $req_membre . "and perso_actif != 'N' ";
//$req_membre = $req_membre . "and perso_type_perso = 1 ";
$req_membre = $req_membre . "and pguilde_rang_cod = rguilde_rang_cod ";
$req_membre = $req_membre . "and rguilde_guilde_cod = $num_guilde ";
$req_membre = $req_membre . "order by rguilde_admin desc,perso_nom ";

$db->query($req_membre);
$nb_membre = $db->nf();
echo("<p>Il y a <b>$nb_membre</b> inscrits à cette guilde<br />");

printf(" - Renommee moyenne de la guilde : <b>%s</b><br />",$stat_guilde['renommee']);
printf(" - Karma moyen de la guilde : <b>%s</b><br />",$stat_guilde['karma']);
$moy_perso_tue = round(($stat_guilde['joueur_tue'] / $nb_membre),2);
$moy_monstre_tue = round(($stat_guilde['monstre_tue'] / $nb_membre),2);
$moy_mort = round(($stat_guilde['nb_mort'] / $nb_membre),2);
printf(" - Nombre d'aventuriers tués : <b>%s</b> (moyenne par membre: %s)<br />",$stat_guilde['joueur_tue'],$moy_perso_tue);
printf(" - Nombre de monstres tués : <b>%s</b> (moyenne par membre: %s)<br />",$stat_guilde['monstre_tue'],$moy_monstre_tue);
printf(" - Nombre de morts : <b>%s</b> (moyenne par membre: %s)<br /><br />",$stat_guilde['nb_mort'],$moy_mort);
?>
<table><form name="visu_perso" method="post" action="visu_desc_perso.php"><input type="hidden" name="visu">
<?php 
$tab_admin['O'] = ' - Administrateur';
$tab_admin['N'] = '';
while($db->next_record())
{
	$adm = $db->f("rguilde_admin");
	echo "<tr>";
	echo "<td class=\"soustitre2\"><p><a href=\"javascript:document.visu_perso.visu.value=" . $db->f("perso_cod") . ";document.visu_perso.submit();\">" . $db->f("perso_nom") . "</a></p>";
	echo "</td><td><p>" . $db->f("rguilde_libelle_rang");
    echo $tab_admin[$adm];
    
	echo "</td>";
	if (($is_guilde) && !$is_admin && ($db->f("rguilde_admin") == 'O'))
	{
		$req2 = "select revguilde_cod from guilde_revolution where revguilde_cible = " . $db->f("perso_cod") . " ";
		$db2->query($req2);
		if ($db2->nf() == 0)
		{
			echo "<td class=\"soustitre2\"><p><a href=\"javascript:document.visu_perso.visu.value=" . $db->f("perso_cod") . ";document.visu_perso.action='revolution.php';document.visu_perso.submit();\">Révolution !!</a></td>";
		}
		else
		{
		echo "<td class=\"soustitre2\"><p>Révolution en cours !</td>";
		}
	}
	else
	{
		echo "<td></td>";
	}
	if($is_guilde){
		$req = "select dieu_nom,dniv_libelle from dieu,dieu_perso,dieu_niveau ";
		$req = $req . "where dper_perso_cod = ".$db->f("perso_cod")." ";
		$req = $req . "and dper_dieu_cod = dieu_cod ";
		$req = $req . "and dper_niveau = dniv_niveau ";
		$req = $req . "and dniv_dieu_cod = dieu_cod ";
		$req = $req . "and dniv_niveau >= 1 ";
		$db2->query($req);
		if ($db2->nf() != 0)
		{
			$db2->next_record();
			$religion = " </b>(". $db2->f("dniv_libelle") . " de " . $db2->f("dieu_nom") . ")<b> ";
			echo "<td>$religion</td>";
		} else {
			echo "<td></td>";
		}
		$requete = "select etage_cod,etage_libelle  ";
		$requete = $requete . "from perso_position,positions,etage ";
		$requete = $requete . "where ppos_perso_cod = ".$db->f("perso_cod")." ";
		$requete = $requete . "and ppos_pos_cod = pos_cod ";
		$requete = $requete . "and etage_numero = pos_etage ";
		$db2->query($requete);
		$db2->next_record();
		$lib_etage = $db2->f("etage_libelle");
		$etage_cod = $db2->f("etage_cod");
		if($etage_cod == 10 or $etage_cod == 14){
			$lib_etage = "Localisation indéterminée";
		}
		echo "<td>",$lib_etage,"</td>";
	}	
	else
	{
		echo "<td></td><td></td>";
	}
	echo "</tr>";	
}
echo "</table>";

$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
?>
