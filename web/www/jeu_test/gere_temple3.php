<?php 
/*
FORMULE
FRM_COD
FRM_TYPE
FRM_NOM
FRM_TEMPS_TRAVAIL
FRM_COUT
FRM_RESULTAT
FRM_COMP_COD

FORMULE_COMPOSANT
FRM_COD
FRM_GOBJ_COD
FRM_NUM

FORMULE_PRODUIT
FRM_COD
FRM_GOBJ_COD
FRM_NUM

FORMULE_REALISATION

ECHOPPE_LOG
ELOG_COD
ELOG_LIEU_COD
ELOG_PERSO_COD
ELOG_TYPE_COD
ELOG_GOBJ_COD
ELOG_OBJ_COD
ELOG_DATE
ELOG_DESCRIPTION
ELOG_CREDIT
ELOG_DEBIT


*/


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
$db2 = new base_delain;




function startPane($tab,$index,$active_index){
	if($active_index == $index){
		echo "<div align=\"center\" id=\"pane$index\">";
	} else {
		echo "<div align=\"center\" id=\"pane$index\" style=\"display:none;\">";
	}
	echo "<table class=\"tableauPane\">";
	echo "<tr>";
	foreach ($tab as $i => $vali) {
		if($i == $index){
		 	echo "<td class=\"activePane\"><strong>$vali</strong></td>";
		} else {
			echo "<td class=\"inactivePane\"><a href=\"javascript:switchPane('pane$i');\">$vali</a></td>";
		}
	}

	echo "</tr>";
	echo "<tr><td colspan=\"".count($tab)."\" class=\"centerPane\">";

}

function endPane(){
	echo "</td></tr>";
	echo "</table>";
	echo "</div>";
}
$liste_panels = array("Liste des fidèles connaissant ce temple","Prosélytisme","Puissance du Dieu","à définir","à définir","Mode refuge du temple");

echo '	<link rel="stylesheet" type="text/css" href="../styles/onglets.css" title="essai">
		<script language="javascript" src="../scripts/onglets.js"></script>
';

include "tab_haut.php";
$erreur = 0;
if (!isset($mag))
{
	echo "<p>Erreur sur la transmission du lieu_cod ";
	$erreur = 1;
}
if ($erreur == 0)
{
		$req = "select dper_dieu_cod,dper_niveau from dieu_perso where dper_perso_cod = $perso_cod";
		$db->query($req);
    $db->next_record();
    $niveau_pretre = $db->f("dper_niveau");
    $dieu = $db->f("dper_dieu_cod");
	$req = "select lieu_cod,lieu_tlieu_cod,lieu_nom,pos_cod,pos_x,pos_y,etage_libelle,lieu_dieu_cod,tfid_perso_cod
								from lieu,lieu_position,positions,etage
								left outer join temple_fidele on tfid_lieu_cod = $mag
								where lieu_cod = lpos_lieu_cod
								and lieu_tlieu_cod = 17
								and lpos_pos_cod = pos_cod
								and pos_etage = etage_numero
								and lieu_cod = $mag ";
	$db->query($req);
	if ($db->nf() == 0)
	{
		echo "<p>Erreur, vous n'êtes pas le fidèle en charge de ce temple !";
		$erreur = 1;
	}
	else
	{
    $acces_ok = 0;
    $db->next_record();
		$type_lieu = $db->f("lieu_tlieu_cod");
		$dieu_lieu = $db->f("lieu_dieu_cod");
    if ($type_lieu == 17 && $niveau_pretre > 3 && $dieu_lieu == $dieu)
    {
       $acces_ok = 1;
    }
	  if ($perso_cod == $db->f("tfid_perso_cod"))
	  {
       $acces_ok = 1;
    }
    if($acces_ok == 0)
    {
		  echo "<p>Erreur, vous n'êtes pas le fidèle en charge de ce temple !</p>";
		  $erreur = 1;
	  }

		$pos_actuelle = $db->f("pos_cod");
		$lieu_cod = $db->f("lieu_cod");
		$lieu_nom = $db->f("lieu_nom");
		$pos_x = $db->f("pos_x");
		$pos_y = $db->f("pos_y");
		$etage_libelle = $db->f("etage_libelle");
	}

}
if ($erreur == 0)
{
  $select_pane = 0;
  if(isset($_POST['pane'])){
     $select_pane = $pane;
  }
	if (!isset($methode))
	{
		$methode = "debut";
	}
	if(isset($_POST['methode'])){
		switch ($methode) {
		case "nom";
			echo "<form name=\"echoppe\" method=\"post\" action=\"gere_temple3.php\">";
			echo "<input type=\"hidden\" name=\"methode\" value=\"nom2\">";
			echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";
			$req = "select lieu_nom,lieu_description from lieu ";
			$req = $req . "where lieu_cod = $mag ";
			$db->query($req);
			$db->next_record();

			echo "<table>";
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p>Nom du temple (70 caracs maxi)</td>";
			echo "<td><input type=\"text\" name=\"nom\" size=\"50\" value=\"" . $db->f("lieu_nom") . "\"></td>";
			echo "</tr>";

			echo "<tr>";
			echo "<td class=\"soustitre2\"><p>Description</td>";
			$desc = str_replace(chr(127),";",$db->f("lieu_description"));
			echo "<td><textarea name=\"desc\" rows=\"10\" cols=\"50\">" . $desc . "</textarea></td>";
			echo "</tr>";

			echo "<tr>";
			echo "<td colspan=\"2\"><input type=\"submit\" class=\"test\" value=\"Valider les changements\"></td>";
			echo "</tr>";

			echo "</table>";

			echo "</form>";

			break;
		case "nom2":
			echo "<p><strong>Aperçu : " . $desc;
			$desc = str_replace(";",chr(127),$desc);
			$req = "update lieu set lieu_nom = e'" . pg_escape_string($nom) . "', lieu_description = e'" . pg_escape_string($desc) . "' where lieu_cod = $mag ";
			$db->query($req);
			echo "<p>Les changements sont validés !";
			break;

	}
}



?>
<script>
currentPane = 'pane<?php echo $select_pane?>';
</script>

<p class="titre">Gestion de : <?php  echo $lieu_nom?> - (<?php  echo $pos_x?>, <?php  echo $pos_y ?>, <?php  echo $etage_libelle?>)</p>
<div align="center">
<div id="intro" class="tableau2">
	<p><strong>Information aux responsables de temple	</strong></p>
	<p>Ceci est une page en cours de développement, ce qui veut principalement dire qu'elle ne fonctionne pas. Elle va changer, contenir des essais et des idées qui viendront plus ou moins vite (ou pas du tout). Mais vous pouvez faire des essais et des remarques si vous voulez.</p>
  <p><strong>31/01/2008</strong> On lance le truc, il y a potentiellement beaucoup à faire, et à définir !
  	<br>L'interface est un premier pas, mais ce qu'elle contiendra n'est pas encore bien sec.</p>

<BR />

  <form name="description" method="post" action="gere_temple4.php">
	<input type="hidden" name="mag" value="<?php echo $mag?>">
	<input type="hidden" name="methode" value="nom">
	</form>
    <p style=text-align:center><strong><a href="javascript:document.description.submit();">Changer le nom et la description</a></strong></p>


	</form>
</div>
<br>
<?php 
startPane($liste_panels,0,$select_pane);
?><p>
<strong>Liste des fidèles de votre Dieu qui connaissent ce temple :<br /></strong>
<?php 
$req = "select perso_cod,perso_nom,dper_niveau,dniv_libelle from choix_perso_position_vus($pos_actuelle),perso,dieu_perso,dieu_niveau
						where choix_perso_position_vus = perso_cod
						and perso_type_perso = 1
						and perso_actif != 'N'
						and dper_perso_cod = perso_cod
						and dper_dieu_cod = $dieu
						and dniv_dieu_cod = dper_dieu_cod
						and dniv_niveau = dper_niveau";
			$db2->query($req);
			while($db2->next_record())
			{
			 $liste_clients .= $db2->f("perso_nom").";";
        echo  $db2->f("perso_nom")." <em>(". $db2->f("dniv_libelle").")</em><br /> ";
			}
?>
</p><?php 
endPane();
startPane($liste_panels,1,$select_pane);
?>
	<p style=text-align:left><strong>	Aventuriers présents sur le lieu du temple :</strong><br />
<?php 
      $liste_clients = "";
			$req_vue = "select lower(perso_cod) as minusc,perso_cod,perso_nom,dper_niveau,dieu_nom,dniv_libelle from perso_position
									left outer join perso on perso_position.ppos_perso_cod = perso.perso_cod
									left outer join dieu_perso on dieu_perso.dper_perso_cod = perso.perso_cod
									left outer join dieu on dieu_perso.dper_dieu_cod = dieu.dieu_cod
									left outer join dieu_niveau on dieu_niveau.dniv_niveau = dieu_perso.dper_niveau and dieu_niveau.dniv_dieu_cod = dieu_perso.dper_dieu_cod
									where ppos_pos_cod = $pos_actuelle and perso_type_perso in (1,2,3) and perso_actif = 'O' order by perso_type_perso,minusc";
			$db->query($req_vue);
			while($db->next_record())
			{
			 $liste_clients .= $db->f("perso_nom").";";
			 $religion = $db->f("dniv_libelle");
			 if (is_null($religion))
			 {
			 	$religion = "";
			 }
			 else
			 {
			 	$religion =  " <em>(". $db->f("dniv_libelle")." de ". $db->f("dieu_nom").")</em>";
      	}
        echo  $db->f("perso_nom").$religion ."<br /> ";
			}
?>
  <form name="message" method="post" action="messagerie2.php">
	<input type="hidden" name="m" value="2">
	<input type="hidden" name="n_dest" value="<?php echo $liste_clients?>">
	<input type="hidden" name="dmsg_cod">
	</form>
	<p style=text-align:left>
	<a href="javascript:document.message.submit();">Envoyer un message à tous les aventuriers présents dans le temple !</a><br>
	<p style=text-align:left><strong> Aventuriers présents à 3 lieux du temple</strong><br />
<?php 
      $liste_clients2 = "";
			$req_vue = "select lower(perso_cod) as minusc,perso_cod,perso_nom from perso, perso_position where ppos_pos_cod = $pos_actuelle and ppos_perso_cod = perso_cod  and perso_type_perso = 1 and perso_actif = 'O' order by perso_type_perso,minusc";
			$db->query($req_vue);
			while($db->next_record())
			{
			 $liste_clients2 .= $db->f("perso_nom").";";
        echo  $db->f("perso_nom")."<br /> ";
			}
      $req_perso = "select * from lancer_position($pos_actuelle,3)";
      $db2->query($req_perso);
			while($db2->next_record())
			{
				$pos_calcul = $db2->f("lancer_position");
				$req_vue = "select lower(perso_cod) as minusc,perso_cod,perso_nom from perso, perso_position where ppos_pos_cod = $pos_calcul and ppos_perso_cod = perso_cod  and perso_type_perso = 1 and perso_actif = 'O' order by perso_type_perso,minusc";
				$db->query($req_vue);
				while($db->next_record())
				{
				 $liste_clients2 .= $db->f("perso_nom").";";
	        echo  $db->f("perso_nom")."<br /> ";
				}
			}
?>
  <form name="message2" method="post" action="messagerie2.php">
	<input type="hidden" name="m" value="2">
	<input type="hidden" name="n_dest" value="<?php echo $liste_clients2?>">
	<input type="hidden" name="dmsg_cod">
	</form>
	<p style=text-align:left>
	<a href="javascript:document.message2.submit();">Envoyer un message aux alentours du temple <em>(i.e. à 3 lieues)</em></a><br><em>Attention, il est prévue que cela consomme de la puissance de votre Dieu</em>
<?php 
endPane();
startPane($liste_panels,2,$select_pane);
?><p><?php            /*  Ajout par Maverick, le 17/02/2011.
                Affichage du niveau de pouvoir (%age).
                Pts pouvoir <= 0 : Calice noir.
               %age de 1 à 10 : Calice rouge.
                %age de 11 à 20 : Calice violer.
                %age de 21 à 40 : Calice bleu foncé.
                %age de 41 à 60 : Calice bleu.
                %age de 61 à 80 : Calice bleu clair.
                %age de 81 à 90 : Calice vert.
                %age de 91 à 100 : Calice jaune.
            */
            $req = 'select 100 * dieu_pouvoir / (select cast(max(dieu_pouvoir) as float) from dieu ) as pourc from dieu where dieu_cod = ' . $dieu;
            $db->query($req);
            $db->next_record();
            $pourc = $db->f('pourc');
            $base_calice = G_IMAGES.'calice_'; // URL des images (sous-domaine)
            $calice = '';
            if ($pourc <= 0) {
                $calice .= 'noir.png';
            } elseif ($pourc <= 10) {
                $calice .= 'rouge.png';
            } elseif ($pourc <= 20) {
                $calice .= 'violet.png';
            } elseif ($pourc <= 40) {
                $calice .= 'bleuf.png';
            } elseif ($pourc <= 60) {
                $calice .= 'bleum.png';
            } elseif ($pourc <= 80) {
                $calice .= 'bleuc.png';
            } elseif ($pourc <= 90) {
                $calice .= 'vert.png';
            } else {
                $calice .= 'jaune.png';
            }
            echo '<div style="float:left;"><table><tr><td colspan=8><img title="Niveau de pouvoir (noir, rouge, violet, bleu, vert, jaune du moins au plus élevé.)" src="'.$base_calice.$calice.'" /></td></tr><tr>';
            $calices = Array('noir.png','rouge.png','violet.png',
                            'bleuf.png','bleum.png','bleuc.png',
                            'vert.png','jaune.png');
            for ( $i = 0; $i < sizeof($calices); ++$i)
            {
                echo '<td><img '. ($calices[$i] == $calice?' border=1 ':'') .'width=20 height=25 title=' . ($calices[$i] == $calice?'"Niveau de pouvoir actuel du Dieu"':'"Niveaux de pouvoir, du plus faible au plus fort"') . ' src="'.G_IMAGES.'calice_'.$calices[$i].'" /></td>';
            }
            echo '</tr></table></div>';
?></p><?php 
endPane();
startPane($liste_panels,3,$select_pane);
?><p>B</p><?php 
endPane();
startPane($liste_panels,4,$select_pane);
?><p>B</p><?php 
endPane();
startPane($liste_panels,5,$select_pane);
?>
<form name="refuge" method="post" action="gere_temple4.php">
	<input type="hidden" name="mag" value="<?php echo $mag?>">
	<input type="hidden" name="methode" value="statut2">

<?php 
			$req = "select lieu_refuge from lieu where lieu_cod = $mag ";
			$db->query($req);
			$db->next_record();
			if ($db->f("lieu_refuge") == 'N')
			{
				?>
					<p>Votre temple n'est pas un refuge. Si vous souhaitez le transformer en refuge, ...<br>
					<input type="hidden" name="ref" value="o">
					<p style=text-align:left><strong><a href="javascript:document.refuge.submit();">Passer en mode refuge <em>(Cette fonctionnalité sera dorénavant controlée)</em></a></strong>
					<?php 
			}
			else
			{
				?>
				<input type="hidden" name="ref" value="n">
				<p>Votre temple est un refuge. Si vous souhaitez abandonner cette fonctionnalité, ...<br>
				<p style=text-align:left><strong><a href="javascript:document.refuge.submit();">Abandonner le statut de refuge pour ce temple ? <em>(Cette fonctionnalité sera dorénavant controlée)</em></a></strong>
				<?php 
			}
				?><?php 
endPane();
?>
</div>
<?php 

}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
