<?php 


include "sjoueur.php";

	$contenu_page .= '

	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<script type="text/javascript" src="javascripts/prototype.js"> </script>
	<script type="text/javascript" src="javascripts/effects.js"> </script>
	<script type="text/javascript" src="javascripts/window.js"> </script>
	<script type="text/javascript" src="javascripts/window_effects.js"> </script>
	<script type="text/javascript" src="javascripts/debug.js"> </script>
	<!-- 
	    Marlyza - 21/06/2018 - un des scripts ci-dessus redéfini la fonction $ de jquery
	    Comme je ne sais pas ce qui est vraiment utilisé pour cette page, je préfère recharger un jquery.js complet
	-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<link href="themes/default.css" rel="stylesheet" type="text/css"/>
	<link href="themes/nuncio.css" rel="stylesheet" type="text/css"/>


<script type="text/javascript"> 

var windowList = [];

function newProtoWindow(numero, nom, html, url, largeur, hauteur, posX, posY)
{
		//if (windowList[numero] == null) {
		if (true) {

			var effect = new PopupEffect(html, {className: "popup_effect1"});
		  var win = new Window({className: "nuncio", title: nom, width:largeur, height:hauteur, showEffect:effect.show.bind(effect), hideEffect:effect.hide.bind(effect)}); 
		  win.setContent(url);
		  win.setLocation(posX, posY);
		  win.setDestroyOnClose();
		  win.show();		  
		  windowList[numero] = win;
		}
		else {
			// Close?
			windowList[numero].close();
		}

}
function newProtoWindowUrl(numero, nom, html, vraieUrl, largeur,hauteur, posX, posY)
{
	if (windowList[numero] == null) {
        var effect = new PopupEffect(html, {className: "popup_effect1"});
          var win = new Window({className: "nuncio", title: nom,
				width:largeur, height:hauteur, showEffect:effect.show.bind(effect),
				hideEffect:effect.hide.bind(effect), url: vraieUrl});
          win.setLocation(posX, posY);
          win.setDestroyOnClose();
          win.show();
        }
        else {
            // Close window
            win = windowList[numero];
            win.close();
            windowList[numero]=null;
        }
}
</script>
<style>
body {
  background-image:url("img/overlay3.png");
}

#hiddenDiv {
  width:1px;
  height:1px;
  border: 1px solid #000;
  overflow:hidden;
  
}

#divVue {
  position:absolute;
  top:150px;
  left:50px;
  width:900px;
  height:700px;
  border: 0px solid #000;
  overflow:hidden;
  
}

#innerVue {
  position:absolute;
  top:0px;
  left:0px;
  width:700px;
  height:600px;
  overflow:hidden;
  background:#CCCCCC;
  z-index:0;
}  

#divHautDroit {
  position:absolute;
  top:500px;
  left:180px;
  width:200px;
  height:200px;
  border: 3px solid #000;
  overflow:hidden;
  
}  

#divHautGauche {
  position:absolute;
  top:25px;
  left:20px;
  width:800px;
  height:100px;
  border: 5px ridge #333333;
  overflow:hidden;
}

#divHautGauche2 {
  position:absolute;
  top:60px;
  left:255px;
  width:40px;
  height:40px;
  overflow:hidden;
}

#divBasDroit {
  position:absolute;
  top:700px;
  left:110px;
  width:80px;
  height:80px;
  border: 0px solid #000;
  overflow:hidden;
}  

#divBasGauche {
  position:absolute;
  top:700px;
  left:30px;
  width:80px;
  height:80px;
  border: 0px solid #000;
  overflow:hidden;
}
  
#menuBoutons1 {
  background-image:url("img/fond5.gif");
  position:absolute;
  top:700px;
  left:18px;
  width:210px;
  height:53px;
  border: 5px ridge #666666;
  padding: 0px;

}


#menuBoutons2 {
  position:absolute;
  top:700px;
  left:615px;
  width:140px;
  height:60px;
  
}

#menuBoutons3 {
  position:absolute;
  top:40px;
  left:680px;
  width:50px;
  height:50px;

}

</style>

<style type="text/css">
    .popup_effect1 {
      background:#11455A;
      opacity: 0.2;
    }
    .popup_effect2 {
      background:#FF0041;
      border: 3px dashed #000;
    }
    
    table.menu td {
    	width:100;
    	text-align:center;    	
    }
    
  </style>	
';

$methode          = get_request_var('methode', 'debut');
switch($methode)
{
	case 'debut':
		//On regarde les missions de cet étage
		$req_mission = "select mission_cod,mission_statut,mission_date_deb,mission_date_fin,mission_desc,mission_param,mission_param_text,mission_nom,mission_taille,mission_nombre_groupe
								from quetes.mission,positions,perso_position
								where ppos_perso_cod = " . $perso_cod . "
								and ppos_pos_cod = pos_cod
								and mission_etage_numero = pos_etage
								and mission_statut = 'O'
								order by mission_cod asc
								";
		$stmt2 = $pdo->query($req_mission);
		if($stmt2->rowCount() == 0)
			$contenu_page .= '<br>aucune mission à cet étage<br>';		
		else
		{
			while($result2 = $stmt2->fetch())
			{
				$is_chef = '';
				$mission = $result2['mission_cod'];
				$nombre_groupe_max = $result2['mission_nombre_groupe'];
				if ($nombre_groupe_max == null)
				{
					$nombre_groupe_texte = 'Elle peut être réalisée par un nombre indéfini de groupe d\'aventuriers.';
				}
				else if ($nombre_groupe_max == 1)
				{
					$nombre_groupe_texte = 'Elle ne peut être réalisée que par un seul groupe d\'aventuriers.';
				}
				else 
				{
					$nombre_groupe_texte = 'Elle peut être réalisée par '.$nombre_groupe_max.' groupes d\'aventuriers en même temps..';
				}
				//On compte les groupe pour la suite
				$req = "select count(mgroupedef_cod) as nombre
										from quetes.mission_groupe_def
										where mgroupedef_mission_cod = ".$mission."
										and mgroupedef_statut = 'O'";
				$stmt = $pdo->query($req);
				$result = $stmt->fetch();
				$nombre_groupe = $result['nombre'];
				$contenu_page .= '<strong>'.$result2['mission_nom'].'</strong><br><br>'.$result2['mission_desc'].'<br><br><em>Cette mission est prévue pour un groupe de '.$result2['mission_taille'].' aventuriers<br>'.$nombre_groupe_texte;
				//
				// on commence par regarder si le perso fait partie d'une mission à cet étage
				//
				$req = "select mgroupe_statut,mgroupe_groupe_cod,mgroupedef_nom,mgroupe_perso_cod,mgroupedef_statut,mgroupe_statut,mgroupedef_mission_cod,mgroupe_date_int
										from quetes.mission,quetes.mission_groupe,quetes.mission_groupe_def
										where mgroupe_perso_cod = " . $perso_cod . "
										and mgroupedef_mission_cod = ".$mission."
										and mgroupedef_cod = mgroupe_groupe_cod
										and mgroupedef_statut = 'O'";
				$stmt = $pdo->query($req);
				$result = $stmt->fetch();
				if($stmt->rowCount() == 0)
					$is_groupe = 'non';
				else if ($result['mgroupe_statut'] == 'E')
				{
					$is_groupe = 'en_cours';
				}
				else
				{
					$is_groupe = 'oui';
				}
				if($result['mgroupe_statut'] == 'O')
				{
					$is_chef = 'Oui';
				}
				switch($is_groupe)
				{
					case 'non':
						//
						// pas dans un groupe
						//
							//Faire une liste avec le noms des groupes existants pour cette mission, et avec mise à cour en auto Ajax du nom des persos
					   	$contenu_page .= '<br>Vous ne faites partie d\'aucun groupe pour cette mission.<br>
																Voulez vous <strong><a onclick="newProtoWindowUrl(1, \'Groupes de mission\', this, \'' . $_SERVER['PHP_SELF'] . '?methode=rejoindre&mission=' . $mission . '\', 600, 250 , 50 ,500)">Rejoindre un groupe de mission existante ? </a></strong><br>';
							if ($nombre_groupe_max == null or $nombre_groupe < $nombre_groupe_max)
							{
                                $contenu_page .= '<br>nombre de groupe : ' . $nombre_groupe . '<br>
								Souhaitez vous <strong><a onclick="newProtoWindowUrl(1, \'Groupes de mission\', this, \'' . $_SERVER['PHP_SELF'] . '?methode=cree&mission=' . $mission . '\', 600, 250 , 50 ,500)">créer un autre groupe ?</a></strong><br>';
							}
							else
							{
								$contenu_page .= '<br>nombre de groupe : '.$nombre_groupe.'<br>
								Le nombre de groupes constitués pour cette mission est largement suffisant. Vous ne pouvez pas créer le votre<br>';
							}
							
					break;
					
					case 'en_cours':
						//
						// pas dans un groupe, mais invit?						//
							//Faire une liste avec le noms des groupes existants pour cette mission, et avec mise ?our en auto Ajax du nom des persos
                        $contenu_page .= '<br>Vous ne faites partie d\'aucun groupe pour cette mission,<strong> mais vous avez reçu une invitation de la part du chef de groupe.<br>
																Voulez vous <a onclick="newProtoWindowUrl(1, \'Groupes de mission\', this, \'' . $_SERVER['PHP_SELF'] . '?methode=rejoindre&mission=' . $mission . '\', 600, 250 , 50 ,500)">Rejoindre un groupe de mission existante ? </a><br></strong>';
                        if ($nombre_groupe_max == null or $nombre_groupe < $nombre_groupe_max)
                        {
                            $contenu_page .= 'Souhaitez vous néanmoins <strong><a onclick="newProtoWindowUrl(1, \'Groupes de mission\', this, \'' . $_SERVER['PHP_SELF'] . '?methode=cree&mission=' . $mission . '\', 600, 250 , 50 ,500)">créer un autre groupe ?</a></strong><br>';
                        } else
                        {
                            $contenu_page .= '<br>Le nombre de groupes constitués pour cette mission est largement suffisant. Vous ne pouvez pas créer le votre<br>';
                        }
                        break;
								
					case 'oui':
                        $contenu_page .= '<br>Vous appartenez au groupe de mission <strong>"' . $result['mgroupedef_nom'] . '</strong>"';
                        if ($is_chef == 'Oui')
                        {
                            $contenu_page .= '<br>En tant que chef de ce groupe de mission, vous pouvez inviter d\'autres membres.
							<br><strong><a onclick="newProtoWindowUrl(1, \'Invitation à un groupe de mission\', this, \'' . $_SERVER['PHP_SELF'] . '?methode=cree&mission=' . $mission . '&invite_chef=oui\', 600, 250 , 50 ,500)">Oui, je souhaite le faire</a></strong>
							<br><strong><a onclick="newProtoWindowUrl(1, \'Déléguer le role de chef\', this, \'' . $_SERVER['PHP_SELF'] . '?methode=delegue&groupe=' . $result['mgroupe_groupe_cod'] . '&mission=' . $mission . '\', 600, 250 , 50 ,500)">Déléguer le role de chef</a></strong>';
                        }
                        $contenu_page .= '<br><strong><a onclick="newProtoWindowUrl(1, \'Membres du groupe de mission\', this, \'' . $_SERVER['PHP_SELF'] . '?methode=details&mission=' . $mission . '\', 600, 250 , 50 ,500)">voir les détails</a></strong> de ce groupe de mission (aventuriers engagés dedans) ?<br>';
                        $contenu_page .= '<br><strong><a onclick="newProtoWindowUrl(1, \'Quitter ce groupe de mission\', this, \'' . $_SERVER['PHP_SELF'] . '?methode=quitter&mission=' . $mission . '\', 600, 250 , 50 ,500)">Quitter ce groupe ?</a></strong><br>';

                        //On donne les indications de statut de la mission
                        $req_object = "select pos_x,pos_y,t1.perso_nom,t1.mgroupedef_nom,mobject_object_cod,mobject_param_text,mobject_date_deb,mobject_date_prise,mobject_perso_cod,mobject_desc,mobject_pos_cod,mobject_type_object,mobject_statut,mobject_temps,(mobject_date_prise + (mobject_temps::text||' hours')::interval) as date_confirm,now() as maintenant
															from quetes.mission_objectif
															left outer join (select perso_nom,mgroupedef_nom,mgroupedef_mission_cod,mgroupe_perso_cod from quetes.mission_groupe,perso,quetes.mission_groupe_def 
															where perso_cod = mgroupe_perso_cod
															and mgroupedef_cod = mgroupe_groupe_cod and mgroupedef_statut = 'O') as t1
															on (t1.mgroupedef_mission_cod = mobject_mission_cod and mgroupe_perso_cod = mobject_perso_cod )
															left outer join positions on (pos_cod = mobject_pos_cod)
															where mobject_mission_cod = " . $mission . "
															and mobject_statut in ('O','N')
															order by mobject_object_cod";
						$stmt = $pdo->query($req_object);
						$s = 's';
						if ($stmt->rowCount() <2)
						{
							$s = '';
						}
						
						$contenu_page .= '<br><br>Cette mission comporte '.$stmt->rowCount().' objectif'.$s.'.
															<br><br><table border="1" width="1000px" height="200px" style="border: medium solid #FFFF00">
															<tr>
															<td><strong>Description de l\'objectif</strong></td>
															<td width="300px" ><strong>Information</strong></td>
															<td><strong>Statut</strong></td>
															<td><strong>Aventurier / groupe de mission ayant réalisé cet objectif</strong></td>
															</tr>';
						while($result = $stmt->fetch())
						{
						  $camp = '';
							//On détermine les informations en fonction du type d'objectif à crendre
							if ($result['mobject_type_object'] == '1')
							{
								$perso = '';
								if ($result['mgroupedef_nom'] == null)
								{
									$camp = 'Camp des monstres';
								}
								else
								{
  								if ($result['mobject_perso_cod'] == null)
  								{
  									$camp = 'Aucune prise pour l\'instant';
  								}
  								else
  								{
  								  $camp = $result['perso_nom'].' / '.$result['mgroupedef_nom'];
                  }
                }
								if ($result['mobject_statut'] == 'O')
								{
									$objectif = 'Prise confirmée !';
								}
								else
								{
									$objectif = 'La prise n\'est pas encore confirmée';
								}
								$information = 'Coordonnées <br> X : '.$result['pos_x'].' / Y : '.$result['pos_y'];
							}
							else if ($result['mobject_type_object'] == '2')
							{
								$objectif = '';
								$information = 'Vous devez nettoyer toutes ces positions des monstres présents<br>Coordonnées :';
								$texte = str_replace(";",",",$result['mobject_param_text']);
								$texte = trim ($texte, ",");
								if ($texte != '')
								{
									$req_monstre = "select pos_cod,pos_x,pos_y,sum(nombre_perso) as nombre  from positions 
									left outer join (select count(perso_cod) as nombre_perso,ppos_pos_cod from perso_position,perso where ppos_perso_cod = perso_cod and 									perso_actif = 'O' and perso_type_perso = 2 group by perso_cod,ppos_pos_cod) as t1 on (t1.ppos_pos_cod = pos_cod) 
									where pos_cod in (".$texte.") group by pos_cod,pos_x,pos_y,nombre_perso ";
									$stmt3 = $pdo->query($req_monstre);
									while($result3 = $stmt3->fetch())
									{
										$nombre_perso = '';
										if ($result3['nombre'] > 0)
										{
											$nombre_perso = '<em> - ('.$result3['nombre'].' monstres présents)</em>';
										}
										$information = $information.'<br> X : '.$result3['pos_x'].' / Y : '.$result3['pos_y'].''. $nombre_perso;
									}
								}
								else
								{
									$information = 'Objectif réalisé ';
								}
								$information = $information.'<br><br>';
							}
							else if ($result['mobject_type_object'] == '3')
							{
								if ($result['mobject_param_text'] == null or $result['mobject_param_text'] == '')
								{
									$information = 'Monstres éradiqués';
									$objectif = 'Objectif réalisé ';
									$camp = '';
								}
								else
								{
									$objectif = '';
									$information = 'Ces monstres sont le danger à éradiquer !<ul>';
									$texte = str_replace(";",",",$result['mobject_param_text']);
									$texte = trim ($texte, ",");
									$req_monstre = "select perso_nom from perso where perso_cod in (".$texte.")";
									$stmt3 = $pdo->query($req_monstre);
									while($result3 = $stmt3->fetch())
									{
										$information = $information.'<li>'.$result3['perso_nom'].'</li>';
									}
									$information = $information.'</ul><br>';
								}
							}					
							else if ($result['mobject_type_object'] == '4')
							{
								if ($result['mobject_param_text'] == null or $result['mobject_param_text'] == '')
								{
									$information = 'Vous avez vaincu un Commandant';
									$objectif = 'Objectif réalisé ';
									$perso = '';
								}
								else
								{
									$information = 'Ces monstres sont les commandants d\'une troupe !<ul>';
                  $objectif = '';
									$texte = str_replace(";",",",$result['mobject_param_text']);
									$texte = trim ($texte, ",");
									$req_monstre = "select perso_nom from perso where perso_cod in (".$texte.")";
									$stmt3 = $pdo->query($req_monstre);
									if ($stmt3->rowCount() == 1)
									{
									 $information = 'Ce monstre est le commandant d\'une troupe !<ul>';
                  }
                  while($result3 = $stmt3->fetch())
									{
										$information = $information.'<li>'.$result3['perso_nom'].'</li>';
									}
									if ($stmt3->rowCount() == 1)
									{
									 $information = $information.'</ul><br>Il doit être impérativement exécuté !';
                  }
                  else
                  {
                    $information = $information.'</ul><br>Ils doivent être impérativement exécutés !';
                  }
                  
								}
							}
							else if ($result['mobject_type_object'] == '5')
							{
								$information = '<strong>Ces monstres ne doivent pas être tués !</strong><ul>';
									$texte = str_replace(";",",",$result['mobject_param_text']);
									$texte = trim ($texte, ",");
									$req_monstre = "select perso_nom from perso where perso_cod in (".$texte.")";
									$stmt3 = $pdo->query($req_monstre);
									while($result3 = $stmt3->fetch())
									{
										$information = $information.'<li>'.$result3['perso_nom'].'</li>';
									}
									$information = $information.'</ul><br>Si un seul meurt, la mission aura échoué ';
									$objectif = "";
									if ($result['mobject_statut'] == 'R')
									{
										$objectif = 'Vous avez raté cet objectif. La mission est un échec.';
									}
									else if ($result['mobject_statut'] == 'O')
									{
										$objectif = 'Objectif réalisé ';
									}
							}
							else if ($result['mobject_type_object'] == '6')
							{
							  $information = '<strong>Empêchez les informateurs d\'atteindre cette position :</strong>
                                  <br> X : '.$result['pos_x'].' / Y : '.$result['pos_y'];
								if ($result['mobject_param_text'] == null or $result['mobject_param_text'] == '')
								{
									$objectif = 'Objectif réalisé ';
									$perso = '';
								}
								else
								{
									$objectif = '';
									$texte = str_replace(";",",",$result['mobject_param_text']);
									$texte = trim ($texte, ",");
									$req_monstre = "select ppos_pos_cod from perso_position where ppos_perso_cod in (".$texte.")";
									$stmt3 = $pdo->query($req_monstre);
									if($stmt3->rowCount() != 0)
									{
										$objectif = 'Objectif raté !';
									}
								}
              }
							$contenu_page .= '
							<tr>
							<td>'.$result['mobject_desc'].'</td>
							<td>'.$information.'</td>
							<td>'.$objectif.'</td>
							<td>'.$camp.'</td>
							</tr>';
						}
						$contenu_page .= '</table><hr>';
					break;
				}
			}
		}
	break;
	
	case 'cree':
		$contenu_page .= '<strong>Attention, la création d\'un nouveau groupe supprimera toutes les invitations précédentes</strong><br><br>
			<table><form method="post" action="' . $_SERVER['PHP_SELF'] . '">
			<input type="hidden" name="methode" value="cree_groupe">				
			<input type="hidden" name="mission" value="' . $mission . '">';
		if($invite_chef !='oui')
		{
			$contenu_page .= '<strong>Attention, la création d\'un nouveau groupe supprimera toutes les invitations précédentes</strong><br><br>
			<tr><input type="hidden" name="invite_chef" value="non">				
				<td><strong>Nom du groupe : </strong><input type="text" name="nom_groupe"></td></tr>';
		}
				$contenu_page .= '<tr><td>Inviter des membres : <input type="text" name="membre_groupe">
			<br><em>(Séparer les noms des persos par des points virgules</em></td>
			<td>Message envoyé aux membres : <textarea name="message_groupe"></textarea>
			<td><input type="submit" value="Envoyer" class="test"></td>
			</tr>
			</form></table>';
	break;
	
	case 'cree_groupe';
		$erreur = 0;
		if($invite_chef == 'non')
		{
			$req = "select mgroupedef_cod,mgroupedef_nom,mgroupe_perso_cod,mgroupedef_statut,mgroupedef_mission_cod,mgroupe_date_int
											from quetes.mission,quetes.mission_groupe,quetes.mission_groupe_def
											where mgroupe_perso_cod = " . $perso_cod . "
											and mgroupedef_mission_cod = ".$mission."
											and mgroupe_groupe_cod = mgroupedef_cod
											and mgroupedef_statut = 'O'";
			$stmt = $pdo->query($req);
			//On check si le perso n'est pas déjà  dans un groupe
			if($stmt->rowCount() != 0)
			{
				$contenu_page .= 'Vous faites déjà  partie d\'un groupe pour cette mission';
				$erreur = 1;
			}
			//on checke si le perso n'est pas un familier (pas de familier chef)
			$req = 'select pfam_familier_cod from perso_familier where pfam_familier_cod = ' .$perso_cod;
			$stmt = $pdo->query($req);
			if($stmt->rowCount() != 0)
			{
				$contenu_page .= 'Un familier ne peut pas créer de groupe de mission';
				$erreur = 1;
			}
		}
		if($erreur == 0 and $invite_chef == 'non')
		{
			//on crée le groupe avec ce perso
			$nom_groupe = htmlspecialchars($nom_groupe);
      $nom_groupe = str_replace(";",chr(127),$nom_groupe);
      $nom_groupe = nl2br($nom_groupe);
			$nom_groupe = pg_escape_string($nom_groupe);
			$req = 'select quetes.cree_groupe_mission(' .$perso_cod. ',' . $mission . ',e\'' . $nom_groupe  . '\') as resultat ';
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
			$contenu_page .= '<br><br>'.$result['resultat'];			
		}
		//on invite les membres
		$tab_dest = explode(";",$membre_groupe);
		$nb_dest = count($tab_dest);
		$nb_vrai_dest = 0;
		$message_groupe = htmlspecialchars($message_groupe);
	  $message_groupe = str_replace(";",chr(127),$message_groupe);
	  $message_groupe = nl2br($message_groupe);
		$message_groupe = pg_escape_string($message_groupe);
		for($cpt=0;$cpt<$nb_dest;$cpt++)
		{
			if ($tab_dest[$cpt] != "")
			{	
				$nb_vrai_dest = $nb_vrai_dest + 1;
			}
			if ($erreur == 0 and $tab_dest[$cpt] != "")
			{
				// on cherche le destinataire
				if ($tab_dest[$cpt] != "")
				{
					$nom_dest     = ltrim(rtrim($tab_dest[$cpt]));
					$nom_dest     = pg_escape_string($nom_dest);
					$req_dest     = "select f_cherche_perso('$nom_dest') as num_perso";
					$stmt         = $pdo->query($req_dest);
                    $result       = $stmt->fetch();
                    $tab_res_dest = $result['num_perso'];
                }
                //on va envoyer une demande aux persos, qui devront valider
                $req          =
                    'select quetes.invite_mission(' . $perso_cod . ',' . $mission . ',' . $tab_res_dest . ',e\'' . $message_groupe . '\') as resultat ';
                $stmt         = $pdo->query($req);
                $result       = $stmt->fetch();
                $contenu_page .= '<br><br>' . $result['resultat'];
            }
        }
        $contenu_page .= '<p style="text-align:center;"><a href="' . $_SERVER['PHP_SELF'] . '">Retour à ca gestion des groupes de mission</a></p>';
        break;
	case 'delegue':
		if($cas == 'promote')
		{
			$req = "update quetes.mission_groupe set mgroupe_statut = 'O' where mgroupe_perso_cod = $perso and mgroupe_groupe_cod = $groupe and exists (select 1 from quetes.mission_groupe where mgroupe_groupe_cod = $groupe and mgroupe_perso_cod = $perso_cod and mgroupe_statut = 'O')";
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
		}
		$req = "select mgroupe_groupe_cod,mgroupe_perso_cod,perso_nom,mgroupe_statut from quetes.mission_groupe,perso 
						     		where mgroupe_groupe_cod =".$groupe."
						     		and mgroupe_perso_cod = perso_cod 
						     		";
	   $stmt = $pdo->query($req);
	   $contenu_page .= '<br><ul>';
	   while($result = $stmt->fetch())
       {
           $chef    = '';
           $promote =
               '<a href="' . $_SERVER['PHP_SELF'] . '?methode=delegue&cas=promote&perso=' . $result['mgroupe_perso_cod'] . '&groupe=' . $result['mgroupe_groupe_cod'] . '&mission=' . $mission . '">Promotion !</a>';
           if ($result['mgroupe_statut'] == 'O')
           {
               $chef    = '- (chef)';
               $promote = '';
           }
           if ($result['mgroupe_statut'] == 'E')
           {
               $chef    = '- (invitation non acceptée)';
               $promote = '';
           }
	    $contenu_page .= '<li>'.$result['perso_nom'].''.$chef.' -------------> <strong>'.$promote.'</strong></li>';
	   }
	   $contenu_page .= '</ul></br>';
	break;
	     
	case 'rejoindre':
		$req = "select mgroupedef_cod,mgroupedef_nom
										from quetes.mission_groupe_def
										where mgroupedef_mission_cod = ".$mission."
										and mgroupedef_statut = 'O'";
		$stmt = $pdo->query($req);
		$groupe = -1;
		if ($stmt->rowCount() == 0)
		{
			$contenu_page .= 'Aucun groupe existant pour cette mission. Vous devriez essayer de créer le votre et relever le challenge !';
			break;
		}
		else
		{
		$contenu_page .= 'Pour intégrer un groupe de mission, il est nécessaire que ce soit le chef de ce groupe qui vous invite.
		<br>Si il l\'a déjà fait, alors le faire de rejoindre le groupe vous intégrera automatiquement
    <br>Si il n\'a pas encore cherché à vous intégrer, le fait de rejoindre le groupe de votre côté lui enverra un message avec votre demande. Il vous faudra à nouveau faire une demande pour rejoindre le groupe ensuite.
    <br><br>
        <form method="post" action="' . $_SERVER['PHP_SELF'] . '">
				<input type="hidden" name="methode" value="rejoindre_groupe">
				<input type="hidden" name="mission" value="' . $mission . '">
				<select name="groupe" id="groupe" onchange="loadData2();">
				<option name="-1"><--Sélectionnner un groupe de mission pour voir le détail--></option>';
			while($result = $stmt->fetch())
			{
				if ($groupe != $result['mgroupedef_cod'])
				{
					$groupe = $result['mgroupedef_cod'];
					$contenu_page .= "<option value=\"".$result['mgroupedef_cod']."\">".$result['mgroupedef_nom']."
					</option>";
				}
			} 
			$contenu_page .= '</select>
				<div id="zoneResultats" style="display:none;">Test div</div>
				<input type="submit" value="Rejoindre" class="test">
				</form>';
		}
	break;
	
	case 'rejoindre_groupe':
		if($groupe == '-1')
		{
			$contenu_page .= 'Vous n\'avez sélectionné aucun groupe de mission.';
		}
		else
		{
				//on va envoyer une demande aux chefs, qui devront valider
				$req = 'select quetes.rejoindre_mission(' .$perso_cod. ',' . $mission . ',' . $groupe  . ') as resultat ';
				$stmt = $pdo->query($req);
				$result = $stmt->fetch();
				$contenu_page .= '<br><br>'.$result['resultat'];
		}
	break;
	
	case 'details':
		$req = "select mgroupedef_cod,mgroupedef_nom,mgroupe_perso_cod,to_char(mgroupe_date_int,'DD-MM-YYYY / hh24:mi') as date_int,perso_nom,mgroupe_statut
											from quetes.mission,quetes.mission_groupe,quetes.mission_groupe_def,perso
											where mgroupe_groupe_cod = (
											select mgroupe_groupe_cod from quetes.mission_groupe,quetes.mission_groupe_def where mgroupe_perso_cod = " . $perso_cod . " and mgroupe_groupe_cod = mgroupedef_cod and mgroupedef_mission_cod = ".$mission.")
											and mgroupedef_mission_cod = ".$mission."
											and mgroupe_groupe_cod = mgroupedef_cod
											and mgroupe_perso_cod = perso_cod
											and mission_cod = mgroupedef_mission_cod
											and mgroupedef_statut = 'O'
											and mgroupe_statut != 'E'";
			$stmt = $pdo->query($req);
			$contenu_page .= '<strong>Groupe de mission composé des membres suivants : </strong><br><ul>';
			while($result = $stmt->fetch())
			{
				$chef = '';
				if($result['mgroupe_statut'] =='O')
				{
					$chef = '<em> - chef de mission </em>';
				}
				$contenu_page .= '<li>'.$result['perso_nom'].''.$chef.' / Date d\'intégration : '.$result['date_int'].'</li>';
			}
			$contenu_page .= '</ul>';
	break;

	case 'quitter':
		$contenu_page .= '<strong>Attention, quitter un groupe est définitif, et seule une nouvelle invitation d\'un chef du groupe vous permettra d\'y revenir.</strong><br><br>
			<form method="post" action="' . $_SERVER['PHP_SELF'] . '">
			<input type="hidden" name="methode" value="quitter_confirme">				
			<input type="hidden" name="mission" value="' . $mission . '">';
		$contenu_page .= '<input type="submit" value="Je confirme !" class="test"></form>';
	break;

	case 'quitter_confirme':
			$req = "delete from quetes.mission_groupe
											where mgroupe_perso_cod = ". $perso_cod . "
											and mgroupe_groupe_cod = (select mgroupe_groupe_cod from quetes.mission_groupe,quetes.mission_groupe_def 
											where mgroupedef_mission_cod = ".$mission."	
											and mgroupe_groupe_cod = mgroupedef_cod
											and mgroupedef_statut = 'O'
											and mgroupe_perso_cod = ". $perso_cod . ")";
			$stmt = $pdo->query($req);
			$contenu_page .= 'Vous venez de quitter ce groupe<br>';
	break;
}
