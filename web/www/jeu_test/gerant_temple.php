
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

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
?>
<script language="javascript"> 
ns4 = document.layers; 
ie = document.all; 
ns6 = document.getElementById && !document.all; 

function changeStyles (id, mouse) { 
   if (ns4) { 
      alert ("Sorry, but NS4 does not allow font changes."); 
      return false; 
   } 
   else if (ie) { 
      obj = document.all[id]; 
   } 
   else if (ns6) { 
      obj = document.getElementById(id); 
   } 
   if (!obj) { 
      alert("unrecognized ID"); 
      return false; 
   } 
    
   if (mouse == 1) { 
      obj.className = "navon"; 
   } 
    
   if (mouse == 0) { 
      obj.className = "navoff";  
   } 
   return true; 
} 

</script> 
<?php 
$erreur = 0;
$req = "select dper_dieu_cod,dper_niveau from dieu_perso where dper_perso_cod = $perso_cod";
$db->query($req);
if ($db->nf() == 0)
{
	echo "<p>Erreur1 ! Vous n'avez pas accès à cette page !1";
	$erreur = 1;
}
else
{
	$db->next_record();
}
if ($db->f("dper_niveau") < 4)
{
	echo "<p>Erreur ! Vous n'avez pas accès à cette page !2";
	$erreur = 1;
}
if ($erreur == 0)
{
	$dieu_perso = $db->f("dper_dieu_cod");
	//
	// en premier on liste les temples et leur fidèle associé éventuel
	//	
	// on commence par les temples avec fidèle
	echo "<p class=\"titre\">Temples avec fidèles</p>";
	$req = "select lieu_cod,lieu_nom,pos_x,pos_y,etage_libelle,perso_nom 
								from lieu,lieu_position,positions,etage,perso,temple_fidele
								where lieu_cod = lpos_lieu_cod
								and lieu_tlieu_cod = 17
								and lpos_pos_cod = pos_cod
								and pos_etage = etage_numero
								and tfid_lieu_cod = lieu_cod
								and tfid_perso_cod = perso_cod 
								and lieu_dieu_cod = $dieu_perso
								order by pos_etage desc ";
	$db->query($req);
	if ($db->nf() == 0)
	{
		echo "<p>Aucun temple n'est administré par des fidèles.";
	}
	else
	{
		echo "<table cellspacing=\"2\" cellpadding=\"2\">";
		echo "<tr>";
		echo "<td class=\"soustitre2\"><p>Emplacement</td>";
		echo "<td class=\"soustitre2\"><p>Nom du temple</td>";
		echo "<td class=\"soustitre2\"><p>Fidèle</td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "</tr>";
		
		while ($db->next_record())
		{
			echo "<tr>";
			echo "<td id=\"cell" . $db->f("lieu_cod") . "\" class=\"soustitre2\"><p>" . $db->f("pos_x") . ", " . $db->f("pos_y") . ", " . $db->f("etage_libelle") . "</td>";
      echo "<td class=\"soustitre2\"><p><a href=\"gere_temple3.php?mag=".$db->f("lieu_cod")."\"><strong>" . $db->f("lieu_nom") . "</strong></a></td>";
			echo "<td class=\"soustitre2\"><p><strong>" . $db->f("perso_nom") . "</strong></td>";
			echo "<td><p><a onMouseOver=\"changeStyles('cell" . $db->f("lieu_cod") . "',1)\" onMouseOut=\"changeStyles('cell" . $db->f("lieu_cod") . "',0)\" href=\"modif_fidele.php?methode=modif&lieu=" . $db->f("lieu_cod") . "\">Modifier</a></td>";
			echo "<td><p><a onMouseOver=\"changeStyles('cell" . $db->f("lieu_cod") . "',1)\" onMouseOut=\"changeStyles('cell" . $db->f("lieu_cod") . "',0)\" href=\"modif_fidele.php?methode=supprime&lieu=" . $db->f("lieu_cod") . "\">Supprimer</a></td>";
			echo "</tr>";
		}	
		echo "</table>";
	}
	// on fait les temples sans gérance
	echo "<p class=\"titre\">Temples sans affectation</p>";
	$req = "select lieu_cod,lieu_nom,pos_x,pos_y,etage_libelle
					from lieu,lieu_position,positions,etage
					where lieu_cod = lpos_lieu_cod
					and lieu_dieu_cod = $dieu_perso
					and lieu_tlieu_cod = 17
					and lpos_pos_cod = pos_cod
					and pos_etage = etage_numero
					and not exists
					(select 1 from temple_fidele where tfid_lieu_cod = lieu_cod)
					order by pos_etage desc ";
	$db->query($req);
	if ($db->nf() == 0)
	{
		echo "<p>Aucun temple n'est administré par des fidèles.";
	}
	else
	{
		echo "<table cellspacing=\"2\" cellpadding=\"2\">";
		while ($db->next_record())
		{
			echo "<tr>";
			echo "<td id=\"cell" . $db->f("lieu_cod") . "\" class=\"soustitre2\"><p><a href=\"gere_temple3.php?mag=".$db->f("lieu_cod")."\"><strong>". $db->f("lieu_nom")."</strong></a></td><td class=\"soustitre2\"> ". $db->f("pos_x") . ", " . $db->f("pos_y") . ", " . $db->f("etage_libelle") . "</td>";
			echo "<td><p><a onMouseOver=\"changeStyles('cell" . $db->f("lieu_cod") . "',1)\" onMouseOut=\"changeStyles('cell" . $db->f("lieu_cod") . "',0)\" href=\"modif_fidele.php?methode=ajout&lieu=" . $db->f("lieu_cod") . "\">Ajouter un fidèle pour gérer ce temple</a></td>";
			echo "</tr>";
		}	
		echo "</table>";
	}
}
include "tab_bas.php";

$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
