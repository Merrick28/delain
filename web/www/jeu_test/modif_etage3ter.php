<?php 
/* #LAG - +++ 2018-01-25 +++ - Création, modification des lieux */

include_once "verif_connexion.php";
include_once '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include_once('variables_menu.php');

echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>'; 							//Facilité le developpement avec du jquery
echo '<script src="../scripts/admin_etage_modif3.js"></script>';	 // Scripts des traitements des clics dans la map
								
function ecrireResultatEtLoguer($texte, $loguer, $sql = '')
{
	global $db, $compt_cod;

	if ($texte)
	{
		$log_sql = false;	// Mettre à true pour le debug des requêtes

		if (!$log_sql || $sql == '')
			$sql = "\n";
		else
			$sql = "\n\t\tRequête : $sql\n";

		$req = "select compt_nom from compte where compt_cod = $compt_cod";
		$db->query($req);
		$db->next_record();
		$compt_nom = $db->f("compt_nom");

		$en_tete = date("d/m/y - H:i") . "\tCompte $compt_nom ($compt_cod)\t";
		if ($log_sql) echo "<div style='padding:10px;'>$texte<pre>$sql</pre></div><hr />";
		if ($loguer)
			writelog($en_tete . $texte . $sql,'lieux_etages');
	}
}
// Function récupérée/adaptée de modif_etage3bis.php (c'est pas avec du pdo, mais ça va plus vite à DEV car c'est déjà fait)
function creer_lieu($_LIEU)
{ 
		GLOBAL $db;
	
		$lieu_cod = $_LIEU["lieu_cod"];
		$tlieu_cod = $_LIEU["tlieu_cod"];
		$pos_x = $_LIEU["pos_x"];
		$pos_y = $_LIEU["pos_y"];
		$pos_etage = $_LIEU["pos_etage"];
		$nom = $_LIEU["nom"];
		$description = $_LIEU["description"];
		$erreur = 0 ;
	
		$req = "select pos_cod from positions where pos_x = $pos_x and pos_y = $pos_y and pos_etage = $pos_etage";
		$db->query($req);
		if ($db->nf() == 0)
		{
			$resultat = "<p>Aucune position trouvée à ces coordonnées.</p>";
			$erreur = 1;
		}
		else
		{
			$db->next_record();
			$lieu_pos_cod = $db->f("pos_cod");
			$lieu_dest_pos_cod = 'null';
			/* LAG: lieux avec des destinations de sont pas gérés ici
			if($_LIEU['dest_pos_x'] != NULL && $_LIEU['dest_pos_y'] != NULL  && $_LIEU['dest_pos_etage'] != NULL )
			{
				$req = "select pos_cod from positions where pos_x = $dest_pos_x and pos_y = $dest_pos_y and pos_etage = $dest_pos_etage";
				$db->query($req);
				if ($db->nf() != 0)
				{
					$db->next_record();
					$lieu_dest_pos_cod = $db->f("pos_cod");
				}
			}*/
			$req = "select nextval('seq_lieu_cod') as lieu_cod";
			$db->query($req);
			$db->next_record();
			$lieu_cod = $db->f("lieu_cod");

			$nom = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $nom)));
			$description = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $description)));

			// Récupération lieu_url
			$req_url = "select coalesce(tlieu_url, '') as tlieu_url from lieu_type where tlieu_cod = $tlieu_cod";
			$url = $db->get_value($req_url, 'tlieu_url');

			if ($tlieu_cod == 29 || $tlieu_cod == 30)
			{
				$cout_pa = $_LIEU['cout_pa'];
			}
			else
			{
				$cout_pa = 30; /*correspond au prélèvement des magasins*/
			}
			$req = "insert into lieu (lieu_cod, lieu_tlieu_cod, lieu_nom, lieu_description, lieu_refuge, lieu_url,
					lieu_dest, lieu_alignement, lieu_dfin, lieu_compte, lieu_marge, lieu_prelev,
					lieu_mobile, lieu_date_bouge, lieu_date_refill, lieu_port_dfin, lieu_dieu_cod) values "
				."($lieu_cod, $tlieu_cod, e'$nom', e'$description', e'".pg_escape_string($_LIEU['refuge'])."', '".pg_escape_string($url)."', ".
				"$lieu_dest_pos_cod, 0, null, null, 50, $cout_pa, 
				'".$_LIEU['mobile']."', now(), null, null, ".$_LIEU['dieu'].")";
			$db->query($req);

			$req = "insert into lieu_position (lpos_pos_cod,lpos_lieu_cod) values "
				. "($lieu_pos_cod, $lieu_cod)";
			$db->query($req);

			$req = "select init_automap_pos(".$lieu_pos_cod.")";
			$db->query($req);

			$req = "select tlieu_libelle from lieu_type where tlieu_cod = $tlieu_cod";
			$db->query($req);
			$db->next_record();
			$type_nom = $db->f('tlieu_libelle');

			$resultat = "Lieu $nom n°$lieu_cod ($type_nom) créé en $lieu_pos_cod ($pos_x, $pos_y, $pos_etage)";
			if ($lieu_dest_pos_cod != 'null')
				$resultat .= ", et menant vers $lieu_dest_pos_cod ($dest_pos_x, $dest_pos_y, $dest_pos_etage)";
			$resultat .= '.';
			
		}
	
		ecrireResultatEtLoguer($resultat, $erreur == 0);
	
		return $resultat ; 
}

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$contenu = '';
$erreur = 0;
$req = "select dcompt_modif_carte from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);
if ($db->nf() == 0)
{
	$droit['carte'] = 'N';
}
else
{
	$db->next_record();
	$droit['carte'] = $db->f("dcompt_modif_carte");
}
if ($droit['carte'] != 'O')
{
	die("<p>Erreur ! Vous n’avez pas accès à cette page !</p>");
}

$db2 = new base_delain;
$pdo = new bddpdo;

$log = '';
$resultat = '';

//---------------------------------------------------------------------------------------------------------------------------
// Objectif: 
//		1- Saisie des caractéristiques du lieu
//		2- Affichage de l'étages avec possibilité de cliquer pour ajouter
//		3- Sauvegarde et passage à l'etage suivant (retour step2 s'il reste des etages à faire)
//---------------------------------------------------------------------------------------------------------------------------

if ($erreur == 0)
{
	//echo "<pre>"; print_r($_POST); echo "</pre>";
	//---------------------------------------------------------------------------------------------------------------------------
	if ((isset($_POST["save"])) && (isset($_POST["positions"])))
	{
							
		//caraterisqtiques des nouveaux lieux a créer
		$_LIEU = array(
			"pos_etage" =>  $_POST["pos_etage"],
			"tlieu_cod" => $_POST["tlieu_cod"],				
			"nom" => $_POST["nom"],
			"description" => $_POST["description"],
			"dieu" => $_POST['dieu'],
			"mobile" => $_POST['mobile'],
			"refuge" => $_POST['refuge'],
			"cout_pa" => $_POST['cout_pa']
		);

		// boucle sur toutes les positions demandées:
		foreach($_POST["positions"] as $k => $position)
		{
			$arr_pos = explode ( "," , $position) ;
			$_POS = array(
					"pos_x" => $arr_pos[0],
					"pos_y" => $arr_pos[1]
			);
					
			$resultat = creer_lieu(array_merge($_LIEU, $_POS));
			echo $resultat."<br>";		// Afichage des resultats!
		}
		echo "<hr />";		
	}
	
	//---------------------------------------------------------------------------------------------------------------------------
	$action=$_POST["action"];
	if(($action=="") || (isset($_POST["cancel"])))
	{
		
		// Liste des niveaux principaux
    	$req = "select distinct ABS(etage_reference) sort, etage_reference from etage order by ABS(etage_reference); ";
		$stmt = $pdo->query($req);
		$etage_ref = array();
		while ($resultat = $stmt->fetch())
		{
			$etage_ref[] = $resultat["etage_reference"];
		}		

		// Page de selection des options.
		echo '
			<p><strong>Caractéristiques des lieux à créer: </strong></p>
			<div>
				<form method="post" action="modif_etage3ter.php">
					<input type="hidden" name="action" value="creer_lieux">
					<input type="hidden" name="step" value="0">
					<table>
					<tr><td>Type : </td><td><select name="tlieu_cod">';

		$req = "select tlieu_cod, tlieu_libelle from lieu_type order by tlieu_libelle desc ";
		echo $html->select_from_query($req, "tlieu_cod", "tlieu_libelle");
		echo '
					</select><br>
					</td></tr><tr><td>Nom : </td><td><input type="text" name="nom">
					</td></tr><tr><td>Description : </td><td><textarea name="description"></textarea>
					</td></tr><tr><td>Dieu (pour les temples) :</td><td><select name="dieu">
					<option value="null">Pas de dieu</option>';
		$req = "select dieu_cod,dieu_nom from dieu order by dieu_nom desc ";
		echo $html->select_from_query($req, "dieu_cod", "dieu_nom");
		echo '
					</select><br />
					</td></tr><tr><td>Refuge : </td><td><select name="refuge">
					<option value="N">non</option>
					<option value="O">oui</option>
					</select>
					</td></tr><tr><td>Mobile : </td><td><select name="mobile">
					<option value="N">non</option>
					<option value="O">oui</option>
					</select>
					</td></tr><tr><td>Coût en pa : </td><td><input size=3 type="text" name="cout_pa"> (pour les passages ondulants uniquement)	
					</td></tr><tr><td>Densité : </td><td><select name="densite">
					<option value="3">clairsemé </option>
					<option value="2">dense</option>
					<option value="1">très dense</option>
					</select>
					</td></tr><tr><td>Etages à voir : </td><td><select name="etage_type">
					<option value="etage_type_0">Etages principaux seulement</option>
					<option value="etage_type_1">Etages principaux et leurs annexes</option>
					<option value="etage_type_2">Le Proving Ground et ses annexes</option>';
		foreach($etage_ref as $k => $type) 
		{
			echo   '<option value="etage_type_x_'.$type.'">Etage '.$type.' et ses annexes</option>';
		}
		echo '
					</select>
					</td></tr></td></tr></table>
					<br><br>
					<a href=admin_etage.php><input type="button" class="test" name="close" value="Quitter" /></a>	
					&nbsp;&nbsp;&nbsp;<input type="submit" class="test" value="Suite" />
					
				</form>
				<br><u>Nota</u>: &nbsp;&nbsp;&nbsp;&nbsp;<i>Les lieux utilisant des coordonnées de destination ne sont pas traités</i>.
			</div>';
	} 
	else if($action=="creer_lieux") 
	{
		$etage_type = $_POST["etage_type"];
		$step = 1* $_POST["step"];
		if ((isset($_POST["next"]))||(isset($_POST["save"])))
		{
			$step ++ ;
		}
		else if (isset($_POST["previous"]))
		{
			$step -- ;
		 	$step = ($step <0) ? 0 : $step ;
		}
					
		$where="";
		if ($etage_type=="etage_type_0") 
		{
			$where = "WHERE etage_reference = etage_numero and etage_reference>-100";
		}
		else if ($etage_type=="etage_type_1") 
		{
			$where = "WHERE etage_reference>-100";
		} 
		else if ($etage_type=="etage_type_2")
		{
			$where = "WHERE etage_reference<=-100";			
		}
		else if (substr($etage_type,0,13)=="etage_type_x_")
		{
			$where = "WHERE etage_reference=".substr($etage_type,13);	
		}

		//recupérer les données envoyées:
		$def_tlieu_cod = $_POST["tlieu_cod"];
		$def_nom = $_POST["nom"];
		$def_description = $_POST["description"] ;
		$def_dieu = $_POST["dieu"] ;
		$def_refuge = $_POST["refuge"];
		$def_mobile = $_POST["mobile"];
		$def_cout_pa = $_POST["cout_pa"];
		$def_etage_type = $_POST["etage_type"];
		$densite = 1*$_POST["densite"];
		$densite_desc = ($densite==3 ? 'clairsemé' : ($densite==2 ? 'dense' : 'très dense'));
				
		// Récupérer le nom du type de lieu à ajouter
    	$req  = "select tlieu_libelle from lieu_type where tlieu_cod = :tlieu_cod";
		$stmt   = $pdo->prepare($req);
		$stmt   = $pdo->execute(array(":tlieu_cod"=>$def_tlieu_cod), $stmt);
		$lieu_desc = $stmt->fetch();
		$tlieu_libelle = $lieu_desc["tlieu_libelle"];

				// Récupérer le nom du dieu à ajouter
    	$req  = "select dieu_nom from dieu where  dieu_cod= :dieu_cod";
		$stmt   = $pdo->prepare($req);
		$stmt   = $pdo->execute(array(":dieu_cod"=> ($def_dieu =="null") ? 0 : $def_dieu), $stmt);
		$dieu_desc = $stmt->fetch();
		$dieu_nom = $dieu_desc["dieu_nom"];
		
    	$req  = "select etage_cod,case when etage_reference <> etage_numero then ' |- ' else '' end || etage_libelle as etage_libelle, etage_numero from etage {$where} order by etage_reference desc, etage_numero LIMIT 1 OFFSET :step";
		$stmt   = $pdo->prepare($req);
		$stmt   = $pdo->execute(array(":step"=>$step), $stmt);
		if ($stmt->rowCount()>0) 
		{
			$fin_creation = false;
			$etage_desc = $stmt->fetch();
			$pos_etage = $etage_desc["etage_numero"];
			$nom_etage = $etage_desc["etage_libelle"];
		}
		else 
		{
			$fin_creation = true;
			$pos_etage = "";	// On a finit, plus d'étage a passer en revue
			$nom_etage = "Fin de traitement!";
		}
			
		echo '
			<p>Traitement étage: <strong>'.$nom_etage.' </strong></p>
			<div>
				Ajout de lieu du type: <strong>'.$tlieu_libelle.'</strong><br>
				Nom: <strong>'.$def_nom.'</strong> Description: <strong>'.$def_description.'</strong><br>
				Dieu: <strong>'.$dieu_nom.'</strong><br>
				Refuge: <strong>'.$def_refuge.'</strong> Mobile: <strong>'.$def_mobile.'</strong> Coût en pa: <strong>'.$def_cout_pa.'</strong><br>
				Densité: <strong>'.$densite_desc.'</strong><br>
				<form method="post" action="modif_etage3ter.php">
					<input type="hidden" name="action" value="creer_lieux">
					<input type="hidden" name="etage_type" value="'.$etage_type.'">
					<input type="hidden" name="densite" value="'.$densite.'">
					<input type="hidden" name="pos_etage" value="'.$pos_etage.'">
					<input type="hidden" name="step" value="'.$step.'">
					<input type="hidden" name="tlieu_cod" value="'.$def_tlieu_cod.'">
					<input type="hidden" name="nom" value="'.$def_nom.'">
					<input type="hidden" name="description" value="'.$def_description.'">
					<input type="hidden" name="dieu" value="'.$def_dieu.'">
					<input type="hidden" name="refuge" value="'.$def_refuge.'">
					<input type="hidden" name="mobile" value="'.$def_mobile.'">						
					<input type="hidden" name="cout_pa" value="'.$def_cout_pa.'">						
					<br>
					<input type="submit" class="test" name="cancel" value="Annuler" />';
			if (!$fin_creation) echo '	
					<input type="submit" class="test" name="reload" value="Recharger Etage" />';
			echo '		
					<input type="submit" class="test" name="previous" value="Etage précédent" />';
			if (!$fin_creation) echo '			
					<input type="submit" class="test" name="next" value="Etage suivant" />
					<input type="submit" class="test" name="save" value="Sauvegarder et suivant" />';
			if ($fin_creation) echo '	
					<a href=admin_etage.php><input type="button" class="test" name="close" value="Terminer" /></a>';		
			echo '			
					<div id="list-position"></div>
				</form>
			</div>';

		if (!$fin_creation)
		{
				// limites de la carte
				$req  = "SELECT MIN(pos_x) as minx, MIN(pos_y) as miny, MAX(pos_x) as maxx, MAX(pos_y) as maxy,
							COUNT(*) as nb_case,
							SUM(case when dauto_valeur=0 then 1 else 0 end) as cases_free,
							SUM(case when tlieu_cod=:tlieu_cod then 1 else 0 end) as cases_lieu
							from positions 
							inner join donnees_automap on pos_cod = dauto_pos_cod
							left join lieu_position on  lpos_pos_cod = pos_cod
							left join lieu on lieu_cod = lpos_lieu_cod
							left join lieu_type on lieu_tlieu_cod = tlieu_cod
							where pos_etage = :pos_etage";
				$stmt   = $pdo->prepare($req);
				$stmt   = $pdo->execute(array(":pos_etage"=>$pos_etage, ":tlieu_cod"=>$def_tlieu_cod), $stmt);	
				$etage_limites = $stmt->fetch();
				$minx = $etage_limites["minx"];
				$miny = $etage_limites["miny"];
				$maxx = $etage_limites["maxx"];
				$maxy = $etage_limites["maxy"];			
				$nb_case = $etage_limites["nb_case"];			
				$cases_free = $etage_limites["cases_free"];			
				$cases_lieu = $etage_limites["cases_lieu"];			
				$taux = $nb_case >0 ? ROUND(100 * $cases_free / $nb_case,0) : 0 ;	
				$nb_sug = ROUND($nb_case / (30*30*$densite),0);
			
				//echo "<pre>"; print_r($_POST); echo "<pre>";
			
				$req  = "SELECT pos_cod, pos_x, pos_y, dauto_valeur, lieu_tlieu_cod, lieu_nom
							from positions 
							inner join donnees_automap on pos_cod = dauto_pos_cod
							left join lieu_position on  lpos_pos_cod = pos_cod
							left join lieu on lieu_cod = lpos_lieu_cod
							left join lieu_type on lieu_tlieu_cod = tlieu_cod
							where pos_etage = :pos_etage
							order by pos_y desc, pos_x";
				$stmt   = $pdo->prepare($req);
				$stmt   = $pdo->execute(array(":pos_etage"=>$pos_etage), $stmt);	
			
				$map = array();
				while ($data = $stmt->fetch())
				{
					$map[$data["pos_x"]][$data["pos_y"]] = array("pos_cod"=>$data["pos_cod"], "valeur"=>$data["dauto_valeur"], "type_lieu"=>$data["lieu_tlieu_cod"], "lieu_nom"=>$data["lieu_nom"]);			
				}			

				//echo "<pre>"; print_r($map); echo "<pre>";

				// Style pour une table automap compact
				echo '<style>
						.compact-automap { } 
						.compact-automap.table, .compact-automap.tr, .compact-automap.td, .compact-automap.article {
							margin: 0;
							padding: 0;
							border: 0;
							font-size: 0;
							vertical-align: baseline;
						}
						</style>';

				echo '<div  class="soustitre2" style="margin:8px; margin:8px; padding:8px; border-radius:10px; border:solid #800000 3px;">';
				echo '<table><tr><td valign="top" style="min-width:200px;">
							<strong><u>Caracteristiques étage	</u>:</strong><br>
							Surface total: <strong>'.$nb_case.'</strong> case(s)<br>
							Surface habitable: <strong>'.$taux.'%</strong> ('.$cases_free.')<br>
							Nombre de '.$tlieu_libelle.': <strong><span id="count-lieu">'.($cases_lieu*1).'</span></strong>&nbsp;<img src="' . G_IMAGES . 'automap_1_3.gif"><br>	
							Suggestion: <strong>'.$nb_sug.'</strong> lieux max.<br>
							<br>
							<strong><u>Liste des lieux à ajouter</u>:</strong><br>
							<div id="list-lieux">
							</div><span style="font-size:9px;">(cliquez sur la carte pour ajouter)</span>
					  </td><td valign="top" width="100%"><center>';
				echo '<table class="compact-automap" border="0" background="../images/fond5.gif" cellspacing="0" cellpadding="0">';
				for ($y=$maxy; $y>=$miny; $y--)
				{
					echo '<tr>';
					for ($x=$minx; $x<=$maxx; $x++)
					{					
						$pos_cod = $map[$x][$y]["pos_cod"];
						$dessus = $map[$x][$y]["valeur"];
						$type_lieu = $map[$x][$y]["type_lieu"];
						$comment = $map[$x][$y]["lieu_nom"];
						
						//On donne une nouvelle valeur au $dessus pour faire apparaître des couleurs différentes en fonction des bâtiments
						if (($dessus == 2) || ($dessus == 4) || ($dessus == 5) || ($dessus == 6)|| ($dessus == 7))
						{
							if (($type_lieu == 11) || ($type_lieu == 14) || ($type_lieu == 17) || ($type_lieu == 10)|| ($type_lieu == 13) || ($type_lieu == 9))
							{
								$dessus = $type_lieu;
							}
							if ($type_lieu == 6)
							{
								$dessus = 19;
							}
							if ($type_lieu == 1)
							{
								$dessus = 20;
							}
							if ($type_lieu == 4)
							{
								$dessus = 22;
							}
							if ($type_lieu == 33) // Les autels de prière sont identiques aux temples
							{
								$dessus = 17;
							}
							if ($type_lieu == 34) // Les grandes portes sont assimilées à des passages
							{
								$dessus = 10;
							}
						}						
				
						// faire clignoter les lieux du type que l'on veut ajouter.
						if ($def_tlieu_cod==$type_lieu) 
						{
							 $dessus = 3;
						}
						
						// Clicable seulement si vide
						$onClick = "" ;
						if ($dessus==0)
						{
							$onClick = 'id="pos-'.$pos_cod.'" data-posx="'.$x.'" data-posy="'.$y.'" onclick="add_lieu('.$pos_cod.');"' ;
						}
						echo '<td '.$onClick.'><img src="' . G_IMAGES . 'automap_1_' . $dessus . '.gif" title=" '. $comment . ' (X ' . $x . ',Y ' . $y . ')"></td>';
					}
					echo "</tr>";
				}
				echo '</table><center>';
				echo '</td></tr></table></div>';			
		}
		else 
		{
			// tous les étages ont été passés en revue 

		}
	
	}
	
	//---------------------------------------------------------------------------------------------------------------------------	
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
