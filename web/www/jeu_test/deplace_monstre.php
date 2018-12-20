<?php
include "blocks/_header_page_jeu.php";
$erreur = 0;
$erreur2 = 0;

$droit_modif = 'dcompt_modif_perso';
include "blocks/_test_droit_modif_generique.php";



if ($erreur != 0)
{
    echo "<p>Erreur ! Vous n’avez pas accès à cette page !</p>";
    $erreur2 = 1;
}
$contenu_page .= '';
if (isset($_REQUEST['etage']))
{
    $monstre_etage = $_REQUEST['etage'];
}

if (!isset($monstre))
{
    $monstre = '';
}
if (!isset($pos_x))
{
    $pos_x = '';
}
if (!isset($pos_y))
{
    $pos_y = '';
}
if (!isset($monstre_etage))
{
    $monstre_etage = '';
}
//
// initialisation tableau
//
$db2 = new base_delain;
if ($erreur2 != 1)
{
    ob_start();
    include "admin_edition_header.php";
    $contenu_page .= ob_get_contents();
    ob_end_clean();

    $contenu_page .= '<table><form name="deplace_monstre" method="post" action="' . $PHP_SELF . '">
		
		<tr>
			<td class="titre" colspan="2"><p class="titre">Déplacement de monstres en bloc</p></td>
		</tr>
		<tr>
			<td class="soustitre2"><p>Monstres concernés<br><em>(Entrez les numéros de monstres séparés par des ";")</em></p></td>
			<td class="soustitre2"><p>Récupération de numéros de monstres</p></td>
		</tr>
		<tr>
			<td><p><textarea name="monstre" cols="70" rows="4">' . $monstre . '</textarea></p></td>
			<td><p><strong>Par position</strong></p>
				<p><strong>Position X : </strong><input type="text" name="recup_x" size="5" />
				<strong>Position Y : </strong><input type="text" name="recup_y" size="5" /><br />
				<strong>Étage : </strong><select name="recup_etage">';

    $contenu_page .= $html->etage_select();

    $contenu_page
        .= '</select></p>
			<hr /><p><strong>Par armée</strong></p>
			<p><strong>Numéro du commandant : </strong><input type="text" name="recup_commandant" size="10" /></p></td>
		</tr><tr>
			<td class="soustitre2"><p>Position de lancement<br></p></td>
			<td><input type="submit" name="recup" value="Récupérer"></td>
		</tr>
		<tr><td><p><strong>Position X : </strong><input type="text" name="pos_x" size="5" value="' . $pos_x . '">
			<strong>Position Y : </strong><input type="text" name="pos_y" size="5" value="' . $pos_y . '">
			<strong>Étage : </strong><select name="etage">';

    $contenu_page .= $html->etage_select($monstre_etage);

    $contenu_page
        .= '</select><br></p></td>
		</tr>
		<tr>
			<td><input type="submit" name="validation" value="Valider"></td>	
		</tr>
		</form></table><hr>';

    $methode2 = 'debut';
    if (isset($_POST['recup']))
    {
        $methode2 = 'recuperation';
    } elseif (isset($_POST['validation']))
    {
        $methode2 = 'validation';
    } elseif (isset($_POST['deplacement']))
    {
        $methode2 = 'deplacement';
    }
    switch ($methode2)
    {
        case 'recuperation':
            if ($recup_commandant != '')
            {
                $monstre2 .= $recup_commandant . ';';
                $req = "select perso_nom from perso where perso_cod = $recup_commandant";
                $db->query($req);
                $db->next_record();
                $perso_nom = $db->f("perso_nom");
                $req = "select perso_subalterne_cod from perso_commandement where perso_superieur_cod = $recup_commandant";
                $db->query($req);
                while ($db->next_record())
                {
                    $monstre2 .= $db->f("perso_subalterne_cod") . ';';
                }
                $texte = "Liste des monstres sous le commandement de <strong>$perso_nom</strong>";
            } else
            {
                $req = "select etage_numero, etage_libelle from etage where etage_numero = $recup_etage";
                $db->query($req);
                $db->next_record();
                $etage_nom = $db->f("etage_libelle");
                $req
                    = "select ppos_perso_cod from perso_position, positions 
					where pos_x = $recup_x and pos_y = $recup_y and pos_etage = $recup_etage and ppos_pos_cod = pos_cod";
                $db->query($req);
                while ($db->next_record())
                {
                    $monstre2 .= $db->f("ppos_perso_cod") . ';';
                }
                $texte = "Liste des monstres en position <strong>X : $recup_x / Y : $recup_y / Étage : $etage_nom</strong>";
            }
            $contenu_page .= '<table><td class="soustitre2"><p>' . $texte . '</p></td>
			<tr><td><p><textarea name="monstre2" cols="70" rows="2">' . $monstre2 . '</textarea></p></td></tr></table>';
            break;

        case 'validation': //Validation des monstres et validation de la position de lancement
            $err_depl = 0;
            if ($pos_x == null or $pos_y == null)
            {
                $contenu_page .= '<br><p><strong>*********** Attention, aucune position sélectionnée. *************</strong><br><br><hr>';
                $err_depl = 1;
            } else
            {
                $req
                    = "select pos_cod from positions 
					where pos_x = $pos_x and pos_y = $pos_y and pos_etage = $monstre_etage ";
                $db->query($req);
                if ($db->nf() == 0)
                {
                    $contenu_page .= '<br><p><strong>*********** Aucune position trouvée à ces coordonnées. *************</strong><br><br><hr>';
                    $err_depl = 1;
                } else
                {
                    $db->next_record();
                    $pos_cod2 = $db->f("pos_cod");
                    $req = "select mur_pos_cod from murs where mur_pos_cod = $pos_cod2 ";
                    $db->query($req);
                    if ($db->nf() != 0)
                    {
                        $contenu_page .= '<br><p><strong> ************* impossible de déplacer le perso : un mur en destination. *************</strong><br><br><fr>';
                        $err_depl = 1;
                    }
                }
            }
            $erreur = 0;
            $tab_monstre = explode(";", $monstre);
            $nb_monstre = count($tab_monstre);
            $cpt = 0;
            if ($nb_monstre == 0)
            {
                $contenu_page .= '<p>Vous devez renseigner au moins un monstre !';
                $erreur = 1;
            }
            if ($erreur != 1)
            {
                if ($err_depl != 1)
                {
                    $contenu_page .= '<br><p><strong>*********** Position trouvée. Vérifiez bien monstres concernés et position avant de valider définitivement !*************</strong><br><br><hr>';
                }
                $contenu_page .= '<table><form name="deplace" method="post" action="' . $PHP_SELF . '">
					<tr><td class="titre">Code</td><td class="titre">Nom</td><td class="titre">Type</td><td class="titre">Position de déplacement</td><td class="titre" colspan="2">Paramètres d’IA</td></tr>';
                for ($cpt = 0; $cpt < $nb_monstre; $cpt++)
                {
                    $code = $tab_monstre[$cpt];
                    if ($code != null)
                    {
                        $req
                            = "select perso_nom, perso_type_perso, perso_cod, perso_sta_combat, perso_sta_hors_combat, perso_dirige_admin
							from perso where perso_cod = $code";
                        $db->query($req);
                        //On teste si le code existe, sinon danger !
                        if ($db->nf() == 0)
                        {
                            $contenu_page .= '<tr><td class="soustitre2">' . $code . '</td><td class="soustitre2"><strong>Ce code n’existe pas !</strong></td></tr>';
                        } else
                        {
                            $db->next_record();
                            $nom = $db->f("perso_nom");
                            $type = $db->f("perso_type_perso");
                            $check_stat = $db->f("perso_sta_combat");
                            $check_stat_hc = $db->f("perso_sta_hors_combat");
                            $dirige_admin = $db->f("perso_dirige_admin");

                            //On indique le type de perso dont il s’agit
                            if ($type == 1)
                            {
                                $type_nom = 'Personnage';
                            } else if ($type == 2)
                            {
                                $type_nom = 'Monstre';
                            } else if ($type == 3)
                            {
                                $type_nom = 'Familier';
                            } else
                            {
                                $type = 'Autre... <strong>Attention, risque d’être inconnu !</strong>';
                            }
                            //On donne la possibilité de changer la position de manière individuelle
                            //en ayant préalablement récupéré la position générale
                            $contenu_page .= '<tr><td class="soustitre2">' . $code . '</td><td class="soustitre2">' . $nom . '</td><td class="soustitre2">' . $type_nom . '</td>
								<td class="soustitre2"><p><strong>X : </strong><input type="text" name="x[' . $code . ']" size="5" value="' . $pos_x . '">
									<strong>Y : </strong><input type="text" name="y[' . $code . ']" size="5" value="' . $pos_y . '">
									<strong>Étage : </strong><select name="etage_monstre[' . $code . ']">';

                            $contenu_page .= $html->etage_select($monstre_etage);

                            $contenu_page .= '</select></td>';
                            //On indique le type de statisme dans le cas d’un monstre, avec possibilité de le changer
                            if ($type == 2)
                            {
                                if ($check_stat == 'O')
                                {
                                    $check_stat_O = "checked";
                                    $check_stat_N = "";
                                } else
                                {
                                    $check_stat_O = "";
                                    $check_stat_N = "checked";
                                }
                                if ($check_stat_hc == 'O')
                                {
                                    $check_stat_hc_O = "checked";
                                    $check_stat_hc_N = "";
                                } else
                                {
                                    $check_stat_hc_O = "";
                                    $check_stat_hc_N = "checked";
                                }
                                $contenu_page .= '<td class="soustitre2">Statique en combat : Oui <input ' . $check_stat_O . ' type="radio" name="perso_sta_combat[' . $code . ']" value="O"> / Non <input ' . $check_stat_N . ' type="radio" name="perso_sta_combat[' . $code . ']" value="N"><br />
									Statique hors combat : Oui <input ' . $check_stat_hc_O . ' type="radio" name="perso_sta_hors_combat[' . $code . ']" value="O"> / Non <input ' . $check_stat_hc_N . ' type="radio" name="perso_sta_hors_combat[' . $code . ']" value="N">';
                                $contenu_page
                                    .= '<br></p></td>
									<td class="soustitre2"><p>Géré par l’IA ?
									<select name="ia[' . $code . ']"><option value="O"';
                                if ($dirige_admin == 'O')
                                {
                                    $contenu_page .= ' selected';
                                }
                                $contenu_page .= '>Non</option><option value="N"';
                                if ($dirige_admin == 'N')
                                {
                                    $contenu_page .= ' selected';
                                }
                                $contenu_page .= '>Oui</option><br />';
                                $req = "select pcompt_compt_cod, pcompt_perso_cod from perso_compte where pcompt_perso_cod = $code";
                                $db2->query($req);
                                if ($db2->nf() == 0)
                                {
                                    $compte_administrateur = '';
                                } else
                                {
                                    $db2->next_record();
                                    $compte_administrateur = $db2->f("pcompt_compt_cod");
                                }
                                $contenu_page .= '<input type="hidden" name="compte_administrateur[' . $code . ']" value="' . $compte_administrateur . '">
									<br />Admin monstre : <select name="compt_admin[' . $code . ']">
										<option value="-1">Aucun (géré par l’IA)</option>';

                                $req = "SELECT compt_nom, compt_cod FROM compte WHERE compt_monstre = 'O' ORDER BY compt_nom ASC ";
                                $html->select_from_query($req, 'compt_cod', 'compt_nom', $compte_administrateur);

                                $contenu_page .= '</select></td></tr>';
                            } else
                            {
                                $contenu_page .= '<td colspan="2" class="soustitre2"></td></tr>';
                            }
                        }
                    }
                }
            }
            if ($erreur != 1 and $err_depl != 1)
            {
                $contenu_page .= '<td><tr><input type="submit" name="deplacement" value="Déplacer le groupe de monstres"></td></tr>';
            }
            $contenu_page .= '</form></table>';
            break;

        case 'deplacement': //déplacement des monstres
            $liste_monstre = '';
            foreach ($x as $key => $val)
            {
                if ($key != null)
                {
                    //$contenu_page .= '<br>debug ' . $key . ' - ' . $val . ' - ' . $pos_x[$key];
                    $liste_monstre .= $key;
                    $pos_x1 = $x[$key];
                    $pos_y1 = $y[$key];
                    $monstre_etage = $etage_monstre[$key];

                    $gestion_ia = isset($perso_sta_combat) && isset($perso_sta_combat[$key]);
                    if ($gestion_ia)
                    {
                        $stat_combat = $perso_sta_combat[$key];
                        $stat_hc = $perso_sta_hors_combat[$key];
                        $hors_ia = $ia[$key];
                        $admin = $compt_admin[$key];
                        $ancien_admin = $compte_administrateur[$key];
                    }
                    $req
                        = "select pos_cod, etage_arene from positions 
						inner join etage on etage_numero = pos_etage
						where pos_x = $pos_x1 
							and pos_y = $pos_y1
							and pos_etage = $monstre_etage ";
                    $db->query($req);
                    if ($db->nf() == 0)
                    {
                        $liste_monstre .= '<strong> ***position inconnue***</strong><br>';
                    } else
                    {
                        $db->next_record();
                        $position = $db->f("pos_cod");
                        $arene_dest = $db->f("etage_arene");
                        $req = "delete from lock_combat where lock_cible = $key ";
                        $db2->query($req);
                        $req = "delete from lock_combat where lock_attaquant = $key ";
                        $db2->query($req);

                        // origine
                        $req
                            = "select pos_cod, etage_arene from perso_position
							inner join positions on pos_cod = ppos_pos_cod
							inner join etage on etage_numero = pos_etage
							where ppos_perso_cod = $key";
                        $db2->query($req);
                        $db2->next_record();
                        $position_depart = $db2->f("pos_cod");
                        $arene_depart = $db2->f("etage_arene");

                        // déplacement
                        $req = "update perso_position set ppos_pos_cod = $position where ppos_perso_cod = $key ";
                        $db2->query($req);
                        $liste_monstre .= ' déplacé';

                        // Gestion arènes
                        switch ($arene_depart . $arene_dest)
                        {
                            case 'NO':    // D’un étage normal vers une arène
                                $req = "delete from perso_arene where parene_perso_cod = $key ";
                                $db2->query($req);
                                $req
                                    = "insert into perso_arene (parene_perso_cod, parene_etage_numero, parene_pos_cod, parene_date_entree)
									values($key, $monstre_etage, $position_depart, now()) ";
                                $db2->query($req);
                                $liste_monstre .= " vers une arène : position d’origine = position de sortie d’arène.";
                                break;

                            case 'OO':    // D’une arène vers une autre
                                $req = "update perso_arene set parene_etage_numero = $monstre_etage where parene_perso_cod = $key";
                                $db2->query($req);
                                $liste_monstre .= " d’une arène vers une arène : première position d’entrée = position de sortie d’arène.";
                                break;

                            case 'ON':    // D’une arène vers un étage normal
                                $req = "delete from perso_arene where parene_perso_cod = $key ";
                                $db2->query($req);
                                $liste_monstre .= " d’une arène vers un étage normal : perte de la position d’entrée dans l’arène !";
                                break;

                            case 'NN':    // D’un étage normal vers un étage normal
                                // Rien à faire
                                break;
                        }
                        $liste_monstre .= '<br>';

                        if ($gestion_ia)
                        {
                            //MAJ des propriétés de statisme
                            $req
                                = "update perso set perso_dirige_admin = '$hors_ia',
								perso_sta_combat = '$stat_combat',
								perso_sta_hors_combat = '$stat_hc'
							where perso_cod = $key";
                            $db2->query($req);

                            if ($admin != -1 and $admin != '' and $admin != null)
                            {
                                if ($compte_administrateur[$key] == '')
                                {
                                    $req = "insert into perso_compte (pcompt_compt_cod,pcompt_perso_cod) values ($admin,$key)";
                                    $db2->query($req);
                                } else
                                {
                                    $req = "update perso_compte set pcompt_compt_cod = $admin where pcompt_perso_cod = $key";
                                    $db2->query($req);
                                }
                            }
                        }
                    }
                }
            }
            $contenu_page .= '<br><strong>Les monstres ont bien été déplacés</strong><br><br>' . $liste_monstre . '<br>';
            break;
    }
}

include "blocks/_footer_page_jeu.php";