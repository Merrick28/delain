<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);

//
//Contenu de la div de droite
//
$contenu_page = '';
$contenu_page .= '<script language="javascript" src="../scripts/messEnvoi.js"></SCRIPT>';
if(!isset($methode))
	$methode = 'debut';
switch($methode)
{
	case 'debut':
		//
		// on commence à regarder si le perso fait partie d'un groupe
		//
		$req = 'select groupe_cod,groupe_nom,pgroupe_statut,pgroupe_chef,is_visible_groupe(groupe_cod,' . $perso_cod . ') as is_visible, pgroupe_champions
								from groupe,groupe_perso
								where pgroupe_perso_cod = ' . $perso_cod . '
								and pgroupe_groupe_cod = groupe_cod
								and pgroupe_statut IN (1, 2) order by pgroupe_statut ';
		$db->query($req);
		if($db->nf() == 0)
			$is_groupe = 'non';
		else
		{
			$is_groupe = 'oui';
			$db->next_record();
			$is_suspendu = $db->f('pgroupe_statut') == 2;
		}
		switch($is_groupe)
		{
			case 'non':
				//
				// pas dans un groupe
				//
				$contenu_page .= 'Vous ne faites partie d\'aucune coterie.<br>
					Voulez vous <a href="' . $PHP_SELF . '?methode=cree">en créer une ? </a>';

				break;
			case 'oui':
				//
				// fait partie d'un groupe de combat
				//

				$num_groupe = $db->f('groupe_cod');
				$contenu_page .= 'Vous faites partie de la coterie <b>' . $db->f('groupe_nom') . '</b><br>
					<a href="' . $PHP_SELF . '?methode=reglage">Gérer les valeurs affichées.</a><br>';
				if ($is_suspendu)
				{
					$contenu_page .= '<a href="' . $PHP_SELF . '?methode=quitte&confirme=N">Quitter cette coterie ?</a><br>';
					$contenu_page .= '<br /> Vous êtes décédé et avez été écarté de votre coterie ! Avec un peu de ferveur, vos compagnons pourront vous y ramener...<br />';
				}
				else
				{
					if($db->f('pgroupe_chef') == 1)
					{
						$contenu_page .= '<hr>Vous êtes chef de groupe.<br>
						<a href="' . $PHP_SELF . '?methode=invite">Inviter des participants ?</a><br>
						<a href="' . $PHP_SELF . '?methode=delegue">Déléguer le role de chef ?</a><br>
						<a href="' . $PHP_SELF . '?methode=lourdage">Se séparer d\'un des participants ?</a><br>
						<a href="' . $PHP_SELF . '?methode=detruire&confirme=N">Détruire cette coterie ?</a><br><hr>';
					}
					else
					{
						$contenu_page .= '<a href="' . $PHP_SELF . '?methode=quitte&confirme=N">Quitter cette coterie ?</a><br>';
					}
					if ($db->f('pgroupe_champions') == 1)
						$contenu_page .= '<a href="groupe_champions.php">Voir les champions !</a><br>';


					$contenu_page .= '<hr>';
					if($db->f('is_visible') == 0)
						$contenu_page .= 'Vous êtes trop éloigné du chef de groupe pour avoir les informations.<br>';
					else
					{
						$req = 'select count(perso_cod) as nbre_perso
												from perso,groupe_perso
												where pgroupe_groupe_cod = ' . $num_groupe . '
												and pgroupe_statut = 1
												and pgroupe_perso_cod = perso_cod';
						$db->query($req);
						$db->next_record();
						$nbre_perso = $db->f('nbre_perso');
						$db2 = new base_delain;
						$req2 = 'select pgroupe_chef,pgroupe_messages,pgroupe_texte,to_char(pgroupe_texte_maj,\'DD/MM/YYYY hh24:mi:ss\') as date_texte_perso,groupe_chef,groupe_texte,to_char(groupe_texte_maj,\'DD/MM/YYYY hh24:mi:ss\') as date_texte_groupe
												from groupe_perso,groupe
												where pgroupe_groupe_cod = ' . $num_groupe . '
												and pgroupe_statut = 1
												and groupe_cod = ' . $num_groupe . '';
						$req = 'select perso_cod,perso_type_perso,perso_nom,lower(perso_nom) as minusc,is_visible_groupe(' . $num_groupe . ',perso_cod) as is_visible,perso_pv/perso_pv_max::numeric as pv_relatif,distance((select ppos_pos_cod from perso_position where ppos_perso_cod = '. $perso_cod .'),(select ppos_pos_cod from perso_position where ppos_perso_cod = perso_cod)) as distance,
												case when pgroupe_montre_pa = 1 then perso_pa else -1 end as perso_pa,
												case when pgroupe_montre_dlt = 1 then to_char(perso_dlt,\'DD/MM/YYYY hh24:mi:ss\') else \'cache\' end as perso_dlt,
												case when pgroupe_montre_dlt = 1 then dlt_passee(perso_cod) else -1 end as perso_dlt_passee,
												case when pgroupe_montre_pv = 1 then etat_perso(perso_cod) else \'cache\' end as perso_pv,
												case when pgroupe_montre_bonus = 1 then perso_bonus(perso_cod) else \'cache\' end as perso_bonus,
												perso_pv_max,pgroupe_chef, pgroupe_messages,pgroupe_texte,to_char(pgroupe_texte_maj,\'DD/MM/YYYY hh24:mi:ss\') as date_texte_perso
												from perso,groupe_perso
												where pgroupe_groupe_cod = ' . $num_groupe . '
												and pgroupe_statut = 1
												and pgroupe_perso_cod = perso_cod
												and is_visible_groupe(' . $num_groupe . ',perso_cod) = 1';

						if (!isset($sort))
						{
							$sort = 'nom';
							$sens = 'asc';
							$nv_sens = 'desc';
						}
						if (!isset($sens))
						{
							$sens = 'asc';
						}
						$autresens = $_POST['autresens'];
						if (!isset($autresens))
						{
							$autresens = 'desc';
						}
						if (($sens != 'desc') && ($sens != 'asc'))
						{
							echo "<p>Anomalie sur sens !";
							exit();
						}
						if (($sort != 'DLT') && ($sort != 'nom') && ($sort != 'PA') && ($sort != 'etat') && ($sort != 'perso_type_perso,minusc')&& ($sort != 'distance'))
						{
							echo "<p>Anomalie sur tri !";
							exit();
						}
						switch ($sort)
						{
							case 'DLT':
								$req = $req . " order by perso_dlt $sens";
							break;
							case 'nom':
								$req = $req . " order by perso_type_perso,minusc $sens";
							break;
							case 'PA':
								$req = $req . " order by perso_pa $sens";
							break;
							case 'etat':
								$req = $req . " order by pv_relatif $sens";
							break;
							case 'distance':
								$req = $req . " order by distance $sens";
							break;
							default: 
							break;
						}
						$sens = ($sens == 'desc') ? 'asc' : 'desc';
						$autresens = ($sens == 'desc') ? 'desc' : 'asc';

						$db->query($req);
						$db2->query($req2);
						$db2->next_record();
						//
						// on affiche les membres et les infos
						//
						$contenu_page .= '<table><td>Liste des membres de ce groupe de combat (<b>'. $nbre_perso .'</b> membres)</td>
																	<form method="post" action="' . $PHP_SELF . '">
																	<input type="hidden" name="methode" value="groupe_texte">
																	<input type="hidden" name="num_groupe" value="' . $num_groupe . '">
																	<td><textarea name="groupe_texte" cols="80" rows =5>'.$db2->f("groupe_texte").'</textarea></td><td><input type="submit" value="Validation" class="test">
																	<br><i>Dernière date mise à jour : </i><br>'.$db2->f("date_texte_groupe").'</td>
																	</form>';
						$contenu_page .= '<form name="fsort" method="post" action="groupe.php">
						<input type="hidden" name="sort">
						<input type="hidden" name="sens" value="$sens">
						<input type="hidden" name="autresens">
						<input type="hidden" name="visu">
						<table>
						<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'nom\';document.fsort.sens.value=\''. $sens .'\';document.fsort.submit();">
						';
						if ($sort == 'nom')
						{
							$contenu_page .= '<b>';
						}
						$contenu_page .= 'Nom';					
						if ($sort == 'nom')
						{
							$contenu_page .= '</b>';
						}
						$contenu_page .= '</a></td>
							<td class="soustitre2" width="15"><p><a href="javascript:document.fsort.sort.value=\'DLT\';document.fsort.sens.value=\''. $sens .'\';document.fsort.submit();">';
						if ($sort == 'DLT')
						{
							$contenu_page .= '<b>';
						}
						$contenu_page .= 'DLT';
						if ($sort == 'DLT')
						{
							$contenu_page .= '</b>';
						}
						$contenu_page .= '</a></td>
							<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'PA\';document.fsort.sens.value=\''. $sens .'\';document.fsort.submit();">
							';
						if ($sort == 'PA')
						{
							$contenu_page .= '<b>';
						}
						$contenu_page .= 'PA';
						if ($sort == 'PA')
						{
							$contenu_page .= '</b>';
						}
						$contenu_page .= '</a></td>
							<td class="soustitre2"><b>Bonus</b></td>
							<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'etat\';document.fsort.sens.value=\''. $sens .'\';document.fsort.submit();">
							';
						if ($sort == 'etat')
						{
							$contenu_page .= '<b>';
						}
						$contenu_page .= 'Santé';
						if ($sort == 'etat')
						{
							$contenu_page .= '</b>';
						}
						$contenu_page .= '</a></td>
							<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'distance\';document.fsort.sens.value=\''. $sens .'\';document.fsort.submit();">
							';
						if ($sort == 'distance')
						{
							$contenu_page .= '<b>';
						}
						$contenu_page .= 'Dist';
					
						if ($sort == 'distance')
						{
							$contenu_page .= '</b>';
						}
						$contenu_page .= '</a></td>
							</tr>
							';
						$liste_dest = "";
						while($db->next_record())
						{
							if ($db->f('perso_cod') != $perso_cod && $db->f('pgroupe_messages') == 1) /*On évite d'inclure le perso dans la liste, et ceux ne voulant pas les messages*/
							{
						  		$liste_dest .=   $db->f('perso_nom').";";
							}
							if($db->f('is_visible') == 0)
							{
								$contenu_page .= "<tr><td colspan=\"5\"><i>" . $db->f('perso_nom') . " trop lointain</i></td></tr>";
							}
							else
							{
								$contenu_page .= "<tr>
									<td class=\"soustitre2\"><a href=\"visu_evt_perso.php?visu=" . $db->f('perso_cod') . "\">" . $db->f('perso_nom') . "</a>";

								if($db->f('pgroupe_chef') == 1)
									$contenu_page .= " <i>(chef de coterie)</i>";
								$contenu_page .= '</td><td>';

								if ($db->f('perso_dlt') == 'cache')
									$contenu_page .= "masqué'";

								else
								{
									if($db->f('perso_dlt_passee') == 1)
										$contenu_page .= "<b>";
									$contenu_page .= $db->f('perso_dlt');

									if($db->f('perso_dlt_passee') == 1)
										$contenu_page .= "</b>";
								}
								$contenu_page .= "</td>
									<td class=\"soustitre2\">";

								if ($db->f('perso_pa') == -1)
									$contenu_page .= "masqué";
								else
									$contenu_page .= $db->f('perso_pa');
								$contenu_page .= "</td><td>";

								if ($db->f('perso_bonus') == 'cache')
									$contenu_page .= "masqué";

								else
									$contenu_page .= $db->f('perso_bonus');

								$contenu_page .= "</td><td>";

								if ($db->f('perso_pv') == 'cache')
									$contenu_page .= "masqué";
								else
									$contenu_page .= $db->f('perso_pv');
								$contenu_page .= "</td><td>";
								$contenu_page .= $db->f('distance');
								$contenu_page .= '</td></form>
																	<form method="post" action="' . $PHP_SELF . '">
																	<input type="hidden" name="methode" value="texte">
																	<input type="hidden" name="num_groupe" value="' . $num_groupe . '">
																	<td><textarea name="pgroupe_texte" rows=1 cols=40>'.$db->f("pgroupe_texte").'</textarea></td>
																	<td>';
								if ($db->f('perso_cod') == $perso_cod) 
								{								
									$contenu_page .= '<input type="submit" value="Validation" class="test">';
								}
								$contenu_page .= '<br>'. $db->f("date_texte_perso").'</td></form></tr>';

							} // fin is visible
						}// fin while
						$req = 'select perso_cod,perso_nom,lower(perso_nom) as minusc,is_visible_groupe(' . $num_groupe . ',perso_cod) as is_visible,pgroupe_messages
												from perso,groupe_perso
												where pgroupe_groupe_cod = ' . $num_groupe . '
												and pgroupe_statut = 1
												and pgroupe_perso_cod = perso_cod
												and is_visible_groupe(' . $num_groupe . ',perso_cod) = 0
												order by minusc';
						$db->query($req);
		      			while($db->next_record())
						{
							if ($db->f('perso_cod') != $perso_cod && $db->f('pgroupe_messages') == 1) /*On évite d'inclure le perso dans la liste, et ceux ne voulant pas les messages*/
							{
						  		$liste_dest .=   $db->f('perso_nom').";";
							}
							if($db->f('is_visible') == 0)
							{
								$contenu_page .= "<tr><td colspan=\"5\"><i><b>" . $db->f('perso_nom') . " trop lointain</b></i></td></tr>";
							}
						}
						$contenu_page .= "</table>
							<form name=\"message\" method=\"post\" action=\"messagerie2.php\">
							<input type=\"hidden\" name=\"m\" value=\"2\">
							<input type=\"hidden\" name=\"n_dest\" value=\"$liste_dest\">
						
							<input type=\"hidden\" name=\"dmsg_cod\">
							</form>
							<p style=text-align:center>
							<a href=\"javascript:document.message.submit();\">Envoyer un message au groupe !</a>
							</p>";
					} // fin else
		                
					//
					// Gestion des morts que l’on peut rappeler
					//
					
					$req = 'select perso_type_perso, perso_tangible
						from perso
						where perso_cod = ' . $perso_cod;
					$db->query($req);
					$db->next_record();
					$afficheBoutons = ($db->f('perso_type_perso') == 1) && ($db->f('perso_tangible') == 'O');
					
					$req = 'select perso_cod, perso_nom, pgroupe_valeur_rappel, perso_niveau
						from groupe_perso
						inner join perso on perso_cod = pgroupe_perso_cod
						where pgroupe_groupe_cod = ' . $num_groupe . '
							and pgroupe_statut = 2
							and perso_type_perso = 1
							and perso_tangible = \'N\'';
					$db->query($req);
					if ($db->nf() > 0)
					{
						$contenu_page .= '<br /><hr /><p><b>Compagnons d’arme morts au combat.</b></p><table><tr><td class="soustitre2">Personnage</td><td class="soustitre2">Jauge de rappel</td><td class="soustitre2">Actions</td></tr>';
						while($db->next_record())
						{
							$barre = min(floor(($db->f('pgroupe_valeur_rappel')/$db->f('perso_niveau'))*10)*10, 100);
							$alt = '';
							if ($barre == 0) $alt = 'Rappel non commencé.';
							else if ($barre < 50) $alt = 'Rappel initié...';
							else if ($barre < 70) $alt = 'Encore un effort...';
							else if ($barre < 90) $alt = 'Rappel bien engagé...';
							else $alt = 'Rappel imminent !';
							$contenu_page .= '<tr><td>' . $db->f('perso_nom') . '</td><td><img src="' . G_IMAGES . 'hp' . $barre . '.gif" alt="' . $alt . '" title="' . $alt . '"></td><td>';
							$contenu_page .= '<form method="post" action="' . $PHP_SELF . '">
								<input type="hidden" name="methode" value="rappel_mort" />
								<input type="hidden" name="mort_perso_cod" value="' . $db->f('perso_cod') . '" />';
							if ($afficheBoutons)
								$contenu_page .= '<input type="submit" class="test" ' . $disabled . ' value="Rappeler ! (6 PA)" />';
							else
								$contenu_page .= 'Rappel impossible';
							$contenu_page .= '</form></td></tr>';
						}
						$contenu_page .= '</table>';
					}
				}
			} // fin switch groupe
			//
			// on regarde maintenant s'il ya des invitations en attente
			//
			$req = 'select groupe_cod,groupe_nom,pgroupe_statut,pgroupe_chef,is_visible_groupe(groupe_cod,' . $perso_cod . ') as is_visible
				from groupe,groupe_perso
				where pgroupe_perso_cod = ' . $perso_cod . '
				and pgroupe_groupe_cod = groupe_cod
				and pgroupe_statut = 0';
			$db->query($req);
			if($db->nf() != 0)
				$contenu_page .= '<hr>Vous avez des invitations en attente. <a href="'. $PHP_SELF . '?methode=vint">Les voir maintenant ?</a>';
			break;	// fin methode debut
	//
	//
		case "cree":
			//
			// se contente d'afficher le formulaire qui va bien
			// le traitement de ce formulaire se fait dans la page action.php
			//
			$contenu_page .= '<form method="post" action="action.php">
				<input type="hidden" name="methode" value="cree_groupe">
					Nom du groupe : <input type="text" name="nom_groupe"><br>
					<input type="submit" value="Envoyer" class="test">
				</form>';
			break; 		// fin méthode cree
	//
	//
	case "reglage":
		//
		// se contente d'afficher le formulaire qui va bien
		// le traitement de ce formulaire se fait dans la page action.php
		//
		$req = 'select pgroupe_montre_pa, pgroupe_montre_dlt, pgroupe_montre_pv, 
				pgroupe_montre_bonus, pgroupe_messages, pgroupe_message_mort, pgroupe_champions
			from groupe_perso
			where pgroupe_perso_cod = ' . $perso_cod . '
			and pgroupe_statut = 1';
		$db->query($req);
		$db->next_record();
		$contenu_page .= '<form method="post" action="action.php">
			<input type="hidden" name="methode" value="regle_groupe">
			<table>
				<tr>
					<td class="soustitre2">Montrer sa dlt ?</td>
					<td><select name="dlt">
							<option value="0"';
		if($db->f('pgroupe_montre_dlt') == 0)
			$contenu_page .= ' selected';
		$contenu_page .= '>Non</option>
						<option value="1"';
		if($db->f('pgroupe_montre_dlt') == 1)
			$contenu_page .= ' selected';
		$contenu_page .= '>Oui</option></select></td>
			</tr>
			<tr>
				<td class="soustitre2">Montrer ses pa ?</td>
				<td><select name="pa">
						<option value="0"';
		if($db->f('pgroupe_montre_pa') == 0)
			$contenu_page .= ' selected';
		$contenu_page .= '>Non</option>
						<option value="1"';
		if($db->f('pgroupe_montre_pa') == 1)
			$contenu_page .= ' selected';
		$contenu_page .= '>Oui</option></select></td>
			</tr>
			<tr>
				<td class="soustitre2">Montrer ses pv ?</td>
				<td><select name="pv">
						<option value="0"';
		if($db->f('pgroupe_montre_pv') == 0)
			$contenu_page .= ' selected';
		$contenu_page .= '>Non</option>
						<option value="1"';
		if($db->f('pgroupe_montre_pv') == 1)
			$contenu_page .= ' selected';
		$contenu_page .= '>Oui</option></select></td>
			</tr>
            <tr>
                <td class="soustitre2">Montrer ses bonus ?</td>
                <td><select name="bonus">
                        <option value="0"';
        if($db->f('pgroupe_montre_bonus') == 0)
            $contenu_page .= ' selected';
        $contenu_page .= '>Non</option>
                        <option value="1"';
        if($db->f('pgroupe_montre_bonus') == 1)
            $contenu_page .= ' selected';
        $contenu_page .= '>Oui</option></select></td>
            </tr>
            <tr>
                <td class="soustitre2">Recevoir les messages de groupe ?</td>
                <td><select name="messages">
                        <option value="0"';
        if($db->f('pgroupe_messages') == 0)
            $contenu_page .= ' selected';
        $contenu_page .= '>Non</option>
                        <option value="1"';
        if($db->f('pgroupe_messages') == 1)
            $contenu_page .= ' selected';
        $contenu_page .= '>Oui</option></select></td>
            </tr>
            <tr>
                <td class="soustitre2">Envoyer un message au groupe en cas de décès ?</td>
                <td><select name="messagemort">
                        <option value="0"';
        if($db->f('pgroupe_message_mort') == 0)
            $contenu_page .= ' selected';
        $contenu_page .= '>Non</option>
                        <option value="1"';
        if($db->f('pgroupe_message_mort') == 1)
            $contenu_page .= ' selected';
        $contenu_page .= '>Oui</option></select></td>
            </tr>
            <tr>
                <td class="soustitre2">Participer au concours interne ?</td>
                <td><select name="champions">
                        <option value="0"';
        if($db->f('pgroupe_champions') == 0)
            $contenu_page .= ' selected';
        $contenu_page .= '>Non</option>
                        <option value="1"';
        if($db->f('pgroupe_champions') == 1)
            $contenu_page .= ' selected';
        $contenu_page .= '>Oui</option></select></td>
            </tr>
			<tr>
				<td colspan="2"><center><input type="submit" value="Valider"></center></td>
			</tr>
			</table>';
		break;		// fin methode reglage
	//
	//
	case "invite":
		//
		// se contente d'afficher le formulaire qui va bien
		// le traitement de ce formulaire se fait dans la page action.php
		//
		$req = 'select groupe_cod,groupe_nom,pgroupe_statut,pgroupe_chef,is_visible_groupe(groupe_cod,' . $perso_cod . ') as is_visible
			from groupe,groupe_perso
			where pgroupe_perso_cod = ' . $perso_cod . '
			and pgroupe_groupe_cod = groupe_cod
			and pgroupe_statut = 1
			and groupe_chef = ' . $perso_cod;
		$db->query($req);
		$db->next_record();

		$contenu_page .= '<form name="nouveau_message" method="post" action="action.php">
			<input type="hidden" name="methode" value="invite_groupe">
			<input type="hidden" name="groupe" value="' . $db->f("groupe_cod") . '">';
		$req_pos = "select ppos_pos_cod,distance_vue($perso_cod) as dist_vue,pos_etage,pos_x,pos_y from perso_position,perso,positions where ppos_perso_cod = $perso_cod and perso_cod = $perso_cod and ppos_pos_cod = pos_cod ";
		$db->query($req_pos);
		$db->next_record();
		$pos_actuelle = $db->f("ppos_pos_cod");
		$v_x = $db->f("pos_x");
		$v_y = $db->f("pos_y");
		$vue =  $db->f("dist_vue");
		$etage = $db->f("pos_etage");
		$req_vue = 'select perso_cod,perso_nom,distance(ppos_pos_cod,' . $pos_actuelle . ') as dist,trajectoire_vue(' . $pos_actuelle . ',pos_cod) as traj from perso, perso_position, positions
			where pos_x >= (' . $v_x . ' - ' . $vue . ') and pos_x <= (' . $v_x . ' + ' . $vue . ')
			and pos_y >= (' . $v_y . ' - ' . $vue . ') and pos_y <= (' . $v_y . ' + ' . $vue . ')
			and ppos_perso_cod = perso_cod
			and perso_cod != ' . $perso_cod . '
			--and perso_type_perso != 2
			and perso_actif = \'O\'
			and ppos_pos_cod = pos_cod
			and pos_etage = ' . $etage  . '
			order by dist,perso_type_perso,perso_nom ';
		$db->query($req_vue);
		$ch = '';
		$optionDefaut = false;
		while($db->next_record())
		{
			if ($db->f('traj') == 1)
			{
				
				if ($db->f('dist') != $dist_init)
				{
					$ch .= '</optgroup><optgroup label="Distance ' . $db->f('dist') . '">';
					$dist_init = $db->f('dist');
				}
				
				if(!$optionDefaut) {
					$ch .= '<option value="">Veuillez saisir un nouveau membre</option>';
					$optionDefaut = true;
				}
				
				$ch .= '<option value="' . $db->f('perso_nom') . ';">' . $db->f('perso_nom') . '</option>';

			}
		}
		$ch = substr($ch,11);
		$contenu_page .= 'Choisissez les aventuriers à inviter : <select name="joueur" onChange="changeDestinataire(0);">' . $ch . '</select>
		<p><input type="text" name="dest" size="80" value=""></p>
		<input type="submit" class="test" value="Inviter"></form>';
		break;		// fin methode invite
	//
	//
	case "vint":
		$req = 'select groupe_cod,groupe_nom,pgroupe_statut,pgroupe_chef,is_visible_groupe(groupe_cod,' . $perso_cod . ') as is_visible
			from groupe,groupe_perso
			where pgroupe_perso_cod = ' . $perso_cod . '
			and pgroupe_groupe_cod = groupe_cod
			and pgroupe_statut = 0';
		$db->query($req);
		if ($db->nf() == 0)
			$contenu_page .= 'Aucune invitation en attente.';
		else
		{
			$contenu_page .= 'Liste des invitations en attente : <br>
				<table>';
			while($db->next_record())
			{
				$contenu_page .= '<tr>
					<td class="soustitre2">' . $db->f("groupe_nom") . '</td>
					<td><a href="action.php?methode=accinv&g=' . $db->f("groupe_cod") . '">Accepter ?</a></td>
				<td><a href="action.php?methode=refinv&g=' . $db->f("groupe_cod") . '">Refuser ?</a></td>
				</tr>';
			}
			$contenu_page .= '</table>
			<i>Attention : un aventurier ne peut faire partie que d\'une coterie à la fois.
			<i>En acceptant l\'entrée dans une coterie, toutes les valeurs d\'information du personnage seront affichées par défaut. Pour changer, il faudra aller les régler.';
		}
		break;		// fin methode vint
	//
	//
	case "quitte":
		if($confirme == 'N')
		{
			$contenu_page .= 'Vous vous apprêtez à quitter cette coterie.<br>
			<a href="' . $PHP_SELF . '?methode=quitte&confirme=O">Cliquez ici pour confirmer !</a>';
		}
		if($confirme == 'O')
		{
			$req = 'select perso_nom,groupe_cod,groupe_chef,groupe_nom,pgroupe_statut,pgroupe_chef,is_visible_groupe(groupe_cod,' . $perso_cod . ') as is_visible
				from groupe,groupe_perso,perso
				where pgroupe_perso_cod = ' . $perso_cod . '
				and pgroupe_groupe_cod = groupe_cod
				and pgroupe_statut = 1
				and perso_cod = pgroupe_perso_cod';
			$db->query($req);
			$db->next_record();
			$groupe = $db->f('groupe_cod');
			$perso_nom = $db->f('perso_nom');
			$corps = $perso_nom . " a quitté la coterie dont vous êtes le chef";
			$db->mess_chef_coterie('Départ',$corps,$groupe,$perso_cod);
			

			$req = 'delete from groupe_perso where pgroupe_perso_cod = ' . $perso_cod . '
				and pgroupe_groupe_cod = ' . $groupe;
			$db->query($req);

			$contenu_page .= 'Vous venez de quitter la coterie.';
		}
		break;		// fin methode quitte
	//
	//
	case "delegue":
		$req = 'select groupe_cod,groupe_chef,groupe_nom,pgroupe_statut,pgroupe_chef,is_visible_groupe(groupe_cod,' . $perso_cod . ') as is_visible
			from groupe,groupe_perso
			where pgroupe_perso_cod = ' . $perso_cod . '
			and pgroupe_groupe_cod = groupe_cod
			and pgroupe_statut = 1';
		$db->query($req);
		$db->next_record();
		$groupe = $db->f('groupe_cod');
		$req = 'select perso_cod,perso_nom,lower(perso_nom) as minusc,is_visible_groupe(' . $groupe . ',perso_cod) as is_visible
			from perso,groupe_perso
			where pgroupe_groupe_cod = ' . $groupe . '
			and pgroupe_statut = 1
			and pgroupe_perso_cod = perso_cod
			and perso_cod != ' . $perso_cod . '
			order by minusc ';
		$db->query($req);
		$contenu_page .= '<form method="post" action="' . $PHP_SELF . '">
			<input type="hidden" name="methode" value="delegue2">
			Choisissez l\'aventurier auquel vous voulez déléguer : <br>
			<select name="deleg">';
		while($db->next_record())
			$contenu_page .= '<option value="' . $db->f('perso_cod') . '">' . $db->f('perso_nom') . '</option>';
		$contenu_page .= '</select> <input type="submit" value="Déléguer"></form>';
		break;		// fin methode delegue
	//
	//
	case "delegue2":
		//
		// on vérifie que l'on est bien le chef
		//
		$req = 'select groupe_cod,groupe_chef,groupe_nom,pgroupe_statut,pgroupe_chef,is_visible_groupe(groupe_cod,' . $perso_cod . ') as is_visible
			from groupe,groupe_perso
			where pgroupe_perso_cod = ' . $perso_cod . '
			and pgroupe_groupe_cod = groupe_cod
			and pgroupe_statut = 1';
		$db->query($req);
		$db->next_record();
		$groupe = $db->f('groupe_cod');
		if($db->f('groupe_chef') != $perso_cod)
			$contenu_page .= 'Anomalie sur gestion chef de coterie !';
		else
		{
			//
			// on modifie les groupe_perso
			//
			$req = 'update groupe set groupe_chef = ' . $deleg . ' where groupe_cod = ' . $groupe;
			$db->query($req);
			$req = 'update groupe_perso set pgroupe_chef = 0 where pgroupe_perso_cod = ' . $perso_cod . ' and pgroupe_groupe_cod = ' . $groupe;
			$db->query($req);
			$req = 'update groupe_perso set pgroupe_chef = 1 where pgroupe_perso_cod = ' . $deleg . ' and pgroupe_groupe_cod = ' . $groupe;
			$db->query($req);
			$db->mess_chef_coterie('Promotion',$perso_nom . ' vous a promu chef de coterie !',$groupe,$perso_cod);
			$contenu_page .= 'Promotion effective. Vous venez de déléguer le rôle de chef de coterie.';
		}
		break;		// fin methode delegue2
	//
	//
	case "detruire":
	//
		// on vérifie que l'on est bien le chef
		//
		$req = 'select groupe_cod,groupe_chef,groupe_nom,pgroupe_statut,pgroupe_chef,is_visible_groupe(groupe_cod,' . $perso_cod . ') as is_visible
			from groupe,groupe_perso
			where pgroupe_perso_cod = ' . $perso_cod . '
			and pgroupe_groupe_cod = groupe_cod
			and pgroupe_statut = 1';
		$db->query($req);
		$db->next_record();
		$groupe = $db->f('groupe_cod');
		if($db->f('groupe_chef') != $perso_cod)
			$contenu_page .= 'Anomalie sur gestion chef de coterie !';
		else
		{
			if($confirme == 'N')
			{
				$contenu_page .= 'Vous vous apprêtez à détruire cette coterie.<br>
				<a href="' . $PHP_SELF . '?methode=detruire&confirme=O">Cliquez ici pour confirmer !</a>';
			}
			if($confirme == 'O')
			{
				//
				// d'abord un envoie un message à tous les participants
				$db->mess_all_coterie('Destruction de la coterie','Le chef de coterie ' . $perso_nom . ' a décidé de détruire la coterie dont vous faisiez partie.',$groupe,$perso_cod);
				//
				// on supprime ensuite les gruope_perso
				$req = 'delete from groupe_perso where pgroupe_groupe_cod = ' . $groupe;
				$db->query($req);
				//
				// on supprime directement la coterie
				$req = 'delete from groupe where groupe_cod = ' . $groupe;
				$db->query($req);
				$contenu_page .= 'La coterie a bien été détruite.<br>
					<a href="' . $PHP_SELF . '">retour au menu coterie</a>';
			}
		}
		break;		// fin methode détruire
	case "lourdage":
		$req = 'select groupe_cod,groupe_chef,groupe_nom,pgroupe_statut,pgroupe_chef,is_visible_groupe(groupe_cod,' . $perso_cod . ') as is_visible
			from groupe,groupe_perso
			where pgroupe_perso_cod = ' . $perso_cod . '
			and pgroupe_groupe_cod = groupe_cod
			and pgroupe_statut = 1';
		$db->query($req);
		$db->next_record();
		$groupe = $db->f('groupe_cod');
		$req = 'select perso_cod,perso_nom,lower(perso_nom) as minusc,is_visible_groupe(' . $groupe . ',perso_cod) as is_visible
			from perso,groupe_perso
			where pgroupe_groupe_cod = ' . $groupe . '
			and pgroupe_statut = 1
			and pgroupe_perso_cod = perso_cod
			and perso_cod != ' . $perso_cod . '
			order by minusc ';
		$db->query($req);
		$contenu_page .= '<form method="post" action="' . $PHP_SELF . '">
			<input type="hidden" name="methode" value="lourde2">
			Choisissez l\'aventurier que vous souhaitez enlever de la coterie : <br>
			<select name="deleg">';
		while($db->next_record())
			$contenu_page .= '<option value="' . $db->f('perso_cod') . '">' . $db->f('perso_nom') . '</option>';
		$contenu_page .= '</select> <input type="submit" value="Virer sans préavis"></form>';
		break;		// fin methode lourdage
	//
	//
	case "lourde2":
		//
		// on vérifie que l'on est bien le chef
		//
		$req = 'select groupe_cod,groupe_chef,groupe_nom,pgroupe_statut,pgroupe_chef,is_visible_groupe(groupe_cod,' . $perso_cod . ') as is_visible
			from groupe,groupe_perso
			where pgroupe_perso_cod = ' . $perso_cod . '
			and pgroupe_groupe_cod = groupe_cod
			and pgroupe_statut = 1';
		$db->query($req);
		$db->next_record();
		$groupe = $db->f('groupe_cod');
		if($db->f('groupe_chef') != $perso_cod)
			$contenu_page .= 'Anomalie sur gestion chef de coterie !';
		else
		{
			$req = 'select perso_nom from perso where perso_cod = ' . $deleg;
			$db->query($req);
			$db->next_record();
			$nom = $db->f('perso_nom');
			$db->mess_all_coterie('Séparation d\'un des membres','Le chef de coterie ' . $perso_nom . ' a décidé d\'éloigner ' .  $nom . ' de la coterie.',$groupe,$perso_cod);
			//
			// on modifie les groupe_perso
			//
			$req = 'delete from groupe_perso where pgroupe_perso_cod = ' . $deleg . ' and pgroupe_groupe_cod = ' . $groupe;
			$db->query($req);
			$contenu_page .= 'L\'aventurier ' . $nom . ' ne fait plus partie de la coterie.';
		}
		break;		// fin methode lourde2
		
		case "texte":
			$texte = $_POST['pgroupe_texte'];
            $texte = htmlspecialchars(str_replace('\'', '’', $texte));
			$texte2 = pg_escape_string($texte);
			$req = 'update groupe_perso set pgroupe_texte = \''. $texte2 .'\',pgroupe_texte_maj = now() where pgroupe_perso_cod = ' . $perso_cod . ' and pgroupe_groupe_cod = ' . $num_groupe;
			$db->query($req);
			$contenu_page .= 'Vous avez mis à jour votre palimpseste avec le texte ci dessous : <br><b>'.$texte.'</b>';
		break;
		case "groupe_texte":
			$texte = $_POST['groupe_texte'];
            $texte = htmlspecialchars(str_replace('\'', '’', $texte));
			$texte2 = pg_escape_string($texte);
			$req = 'update groupe set groupe_texte = \''. $texte2 .'\',groupe_texte_maj = now() where groupe_cod = ' . $num_groupe;
			$db->query($req);
			$contenu_page .= 'Vous avez mis à jour le palimpseste de la coterie avec le texte ci dessous : <br><b>'.$texte.'</b>
			<br><hr>';
			/*include "groupe.php";*/
		break;		
		case "rappel_mort":
			$mort_perso_cod = $_POST['mort_perso_cod'];
			$req = "select rappel_partiel_coterie($mort_perso_cod, $perso_cod) as resultat";
			$db->query($req);
			$db->next_record();
			$contenu_page .= $db->f('resultat') . '<br /><hr>';
		break;	
} // fin switch methode
$contenu_page .= '<p style="text-align:center;"><a href="' . $PHP_SELF . '">Retour à la gestion de la coterie</a></p>';
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
