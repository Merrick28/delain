<?php
include "blocks/_header_page_jeu.php";
include "../includes/binettes.php";
//
// initialisation tableau
//
$mess[0] = 'Boite de réception';
$mess[1] = 'Archives';
$mess[2] = 'Nouveau message';
$mess[3] = 'Boite d’envoi';
$mess[4] = 'Listes de diffusion';
$nb      = count($mess);
//
// Si pas de parametres passés
//


if (!isset($_REQUEST['m']))
{
    $m = 0;
} else
{
    $m = $_REQUEST['m'];
}

if (!isset($_REQUEST['methode']))
{
    $methode = 'debut';
} else
{
    $methode = $_REQUEST['methode'];
}

$contenu_page .= '
	<table cellspacing="0" cellpadding="0" width="100%">
<tr>';
for ($cpt = 0; $cpt < $nb; $cpt++)
{
    $lien   = '<a href="messagerie2.php?m=' . $cpt . '">';
    $f_lien = '</a>';
    if ($cpt == $m)
    {
        $style = 'onglet';
    } else
    {
        $style = 'pas_onglet';

    }
    $contenu_page .= '<td class="' . $style . '"><div style="text-align:center">' . $lien . $mess[$cpt] . $f_lien . '</div></td>';
}
$contenu_page .= '
	</tr>
	<tr>
		<td colspan="' . $nb . '" class="reste_onglet"><center>';
$auth_mes     = 0;
$message      = new messages();
$mid          = $_REQUEST['mid'];
$message->charge($mid);
if ($message->is_auth_msg($perso_cod))
{
    $auth_mes = 1;
}

switch ($methode)
{
    /************************************/
    /* V O I R    U N    M E S S A G E  */
    /************************************/
    case "visu_msg":
        if ($auth_mes == 1)
        {
            $disparu = '<em>-- Personnage disparu --</em>';

            //
            // On recherche la guilde
            //
            $req  =
                "select pguilde_guilde_cod from guilde_perso where pguilde_perso_cod = :perso and pguilde_valide = 'O' ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":perso" => $perso_cod), $stmt);

            if ($result = $stmt->fetch())
            {
                $num_guilde = $resut['pguilde_guilde_cod'];
            } else
            {
                $num_guilde = 0;
            }
            //
            // On marque le message comme lu
            //
            $compte = new compte;
            $compte->charge($compt_cod);
            if (!$compte->is_admin())
            {
                $dmsg   = new messages_dest();
                $allmsg = $dmsg->getByPersoMessage($mid);
                foreach ($allmsg as $detail)
                {
                    $detail->dmsg_lu = 'O';
                    $detail->stocke();
                }

            }
            //
            // On prend les infos du message
            //
            $req_msg = "select to_char(msg_date2,'DD/MM/YYYY hh24:mi:ss') as date_mes,
					msg_init, msg_titre, msg_cod, msg_corps,
					msg_guilde, msg_guilde_cod
				from messages
				where msg_cod = $mid";
            $db->query($req_msg);
            $db->next_record();
            $corps          = str_replace(chr(127), ';', $db->f('msg_corps'));
            $date           = $db->f('date_mes');
            $titre          = str_replace(chr(127), ';', $db->f('msg_titre'));
            $msg_init       = $db->f('msg_init');
            $msg_guilde_cod = $db->f('msg_guilde_cod');
            $msg_guilde     = $db->f('msg_guilde');
            $n_titre        = str_replace(chr(39), " ", $titre);

            //
            // Puis les infos de l’expéditeur
            //
            $req_exp = "select emsg_perso_cod, coalesce(perso_nom, '$disparu') as perso_nom
				from messages_exp
				left outer join perso on perso_cod = emsg_perso_cod
				where emsg_msg_cod = $mid";
            $db->query($req_exp);
            $is_expediteur = ($db->nf() > 0);
            if ($db->next_record())
            {
                $exp            = str_replace("'", "\'", $db->f('perso_nom')) . ";";
                $emsg_perso_cod = $db->f('emsg_perso_cod');
                $emsg_perso_nom = $db->f('perso_nom');
            } else
            {
                $exp            = '';
                $emsg_perso_cod = -1;
                $emsg_perso_nom = $disparu;
            }

            //
            // on regarde sur quel type de message on est
            // et on construit le tableau <--précédent suivant -->
            //
            $precedent_suivant = '<table width="100%"><tr><td>';

            // reçu non archivé
            $pref[0]   = 'dmsg_';
            $suff[0]   = '_dest';
            $restr[0]  = ' and dmsg_archive = \'N\' ';
            $restr2[0] = ' and dmsg_efface = 0';
            // reçu archive
            $pref[1]   = 'dmsg_';
            $suff[1]   = '_dest';
            $restr[1]  = ' and dmsg_archive = \'O\' ';
            $restr2[1] = ' and dmsg_efface = 0';
            // envoyé
            $pref[3]   = 'emsg_';
            $suff[3]   = '_exp';
            $restr[3]  = ' ';
            $restr2[3] = ' ';
            $req_ordre = 'select ' . $pref[$m] . 'msg_cod,' . $pref[$m] . 'lu
				from messages' . $suff[$m] . '
				where ' . $pref[$m] . 'perso_cod = ' . $perso_cod . '
				and ' . $pref[$m] . 'msg_cod < ' . $mid . $restr[$m] . $restr2[$m] . '
				order by ' . $pref[$m] . 'msg_cod desc limit 1';
            $db3->query($req_ordre);
            if ($db3->nf() != 0)
            {
                $db3->next_record();
                $t_var             = $pref[$m] . 'msg_cod';
                $precedent_suivant .= '<a href="' . $PHP_SELF . '?m=' . $m . '&mid=' . $db3->f($t_var) . '&methode=' . $methode . '">';
                if ($db3->f($pref[$m] . 'lu') == 'N')
                {
                    $precedent_suivant .= '<strong>';
                }
                $precedent_suivant .= '<== Message plus ancien ';
                if ($db3->f($pref[$m] . 'lu') == 'N')
                {
                    $precedent_suivant .= '</strong>';
                }
                $precedent_suivant .= '</a>';
            }
            $precedent_suivant .= '</td><td>';
            $req_ordre2        = 'select ' . $pref[$m] . 'msg_cod,' . $pref[$m] . 'lu
				from messages' . $suff[$m] . '
				where ' . $pref[$m] . 'perso_cod = ' . $perso_cod . '
				and ' . $pref[$m] . 'msg_cod > ' . $mid . $restr[$m] . $restr2[$m] . '
				order by ' . $pref[$m] . 'msg_cod asc limit 1';
            $db3->query($req_ordre2);
            if ($db3->nf() != 0)
            {
                $db3->next_record();
                $t_var             = $pref[$m] . 'msg_cod';
                $precedent_suivant .= '<div style="text-align:right;"><a href="' . $PHP_SELF . '?m=' . $m . '&mid=' . $db3->f($t_var) . '&methode=' . $methode . '">';
                if ($db3->f($pref[$m] . 'lu') == 'N')
                {
                    $precedent_suivant .= '<strong>';
                }
                $precedent_suivant .= 'Message plus récent ==> ';
                if ($db3->f($pref[$m] . 'lu') == 'N')
                {
                    $precedent_suivant .= '</strong>';
                }
                $precedent_suivant .= '</a>';
            }
            $precedent_suivant .= '</td></div></td></tr></table>';

            //
            // On construit la page
            //
            $contenu_page .= $precedent_suivant;
            $contenu_page .= '<hr>
				<table cellspacing="2" width="100%">
					<tr>
						<td class="soustitre2" width="200">Date : </td>
						<td>' . $date . '</td>
					</tr>
					<tr>
						<td class="soustitre2" width="200">Titre : </td>
						<td><strong>' . $titre . '</strong></td>
					</tr>
					<tr>
						<td class="soustitre2" width="200">Expéditeur : </td>
						<td><a href="visu_desc_perso.php?visu=' . $emsg_perso_cod . '">' . $emsg_perso_nom . '</a></td>
					</tr>';

            // On construit la liste des destinataires du 'Répondre à tous'
            // en commençant par ajouter l’expéditeur s’il n’est pas soi-même
            // ni dans la même guilde pour un message de guilde.
            if ($msg_guilde == 'O' && $msg_guilde_cod == $num_guilde
                || $emsg_perso_cod == $perso_cod
            )
            {
                $liste_dest = '';
            } else
            {
                $liste_dest = ($is_expediteur) ? $exp . ';' : '';
            }

            //
            // Puis les infos des destinataires
            //
            $req_dest = "select dmsg_perso_cod, coalesce(perso_nom, '$disparu') as perso_nom, dmsg_lu, 
					coalesce(pguilde_guilde_cod, -1) as pguilde_guilde_cod
				from messages_dest
				left outer join perso on perso_cod = dmsg_perso_cod
				left outer join guilde_perso on pguilde_perso_cod = perso_cod and pguilde_valide = 'O'
				where dmsg_msg_cod = $mid";
            $db->query($req_dest);
            $is_destinataires = ($db->nf() > 0);

            $contenu_page .= '
				<tr><td class="soustitre2" width="200">Destinataire(s) : </td>
					<td>';
            if ($msg_guilde == 'O' && $msg_guilde_cod == $num_guilde)
            {
                $liste_dest       = $liste_dest . 'guilde;';
                $contenu_page     .= "Guilde, ";
                $is_destinataires = true;
            }
            if (!$is_destinataires)
            {
                $contenu_page .= $disparu;
            }
            while ($db->next_record())
            {
                $nom_dest        = $db->f('perso_nom');
                $num_dest        = $db->f('dmsg_perso_cod');
                $guilde_cod_dest = $db->f('pguilde_guilde_cod');

                // Construction de la liste des destinataires du Répondre à tous
                if ($num_dest != $perso_cod)    // On ne s’inclut pas
                {
                    if ($msg_guilde != 'O' || $msg_guilde_cod != $num_guilde || $guilde_cod_dest != $msg_guilde_cod)
                        // On inclut tous ceux qui ne sont pas dans la guilde du message,
                        // ou tout le monde si la personne qui répond à tous n’est pas dans la guilde du message
                        // ou tout le monde si le message n’a pas de guilde
                    {
                        $liste_dest = $liste_dest . str_replace("'", "\'", $nom_dest) . ";";
                    }
                }
                if ($db->f('dmsg_lu') == 'O')
                {
                    $contenu_page .= '<a href="visu_desc_perso.php?visu=' . $num_dest . '">' . $nom_dest . '</a>, ';
                } else
                {
                    $contenu_page .= '<a href="visu_desc_perso.php?visu=' . $num_dest . '"><strong>' . $nom_dest . '</strong></a>, ';
                }
            }
            $contenu_page .= '</td></tr>';
            $req          = 'select valeur_bonus(' . $perso_cod . ' , \'ULT\') as bonus_valeur';
            $db->query($req);
            if ($db->nf() != 0)
            {
                $db->next_record();
                $chance   = $db->f('bonus_valeur');
                $longueur = strlen($corps);
                for ($cpt = 0; $cpt < $longueur; $cpt++)
                {
                    if (rand(1, 100) < $chance)
                    {
                        $char  = rand(1, 255);
                        $char2 = chr($char);
                        $corps = substr_replace($corps, $char2, $cpt, 1);
                    }
                }
            }
            $corps = binettes($corps);

            $contenu_page .= '<td colspan="2" class="soustitre2">' . $corps . '</td>
				</tr>
				</table>
				<hr>';
            $contenu_page .= $precedent_suivant;

            $contenu_page .= '<table cellspacing="2" width="100%">
				<tr>
				<form name="message" method="post" action="messagerie2.php">
				<input type="hidden" name="m" value="2">
				<input type="hidden" name="n_dest">
				<input type="hidden" name="n_titre">
				<input type="hidden" name="num_reponse">
				<input type="hidden" name="msg_init">
				<input type="hidden" name="num_message" value="' . $mid . '">
				<input type="hidden" name="dmsg_cod">
					<td class="soustitre2"><div style=text-align:center><a href="javascript:document.message.n_dest.value=\'' . $exp . '\';document.message.n_titre.value=\'' . $n_titre . '\';document.message.msg_init.value=' . $msg_init . ';document.message.submit();">Répondre</a></div></td>
					<td class="soustitre2"><div style=text-align:center><a href="javascript:document.message.n_dest.value=\'' . $liste_dest . '\';document.message.n_titre.value=\'' . $n_titre . '\';document.message.msg_init.value=' . $msg_init . ';document.message.submit();">Répondre à tous</div></td>
					<td class="soustitre2"><div style=text-align:center><a href="action_message.php?m=' . $m . '&methode=archive_msg&mid=' . $mid . '">Archiver</a></div></td>
					<td class="soustitre2"><div style=text-align:center><a href="action_message.php?m=' . $m . '&methode=efface_msg&mid=' . $mid . '">Effacer</a></div></td>
					<td class="soustitre2"><div style=text-align:center><a href="action_message.php?m=' . $m . '&methode=non_lu_msg&mid=' . $mid . '">Marquer comme non lu</a></div></td>
				</tr></table>';
        } else
        {
            $contenu_page .= '<div class="titre">Vous n’avez pas accès à ce message !';
        }
        break;
    case "tout_lu":
        $requete = 'update messages_dest set dmsg_lu = \'O\' where dmsg_perso_cod = ' . $perso_cod;
        $db->query($requete);
        $contenu_page .= 'Tous les messages de votre boite de réception sont marqués comme lus.';
        break;
    case "select_efface":
        $nb = 0;
        for ($cpt = 0; $cpt < 20; $cpt++)
        {
            if (isset($msg[$cpt]) && $msg[$cpt] != '')
            {
                $nb      = $nb + 1;
                $requete =
                    'update messages_dest set dmsg_efface = 1,dmsg_lu = \'O\' where dmsg_cod = ' . $msg[$cpt] . ' and dmsg_perso_cod = ' . $perso_cod;
                $db->query($requete);
            }
        }
        $contenu_page .= $nb . ' messages ont été supprimés de votre boite de réception.';
        break;
    case "select_archive":
        $nb = 0;
        for ($cpt = 0; $cpt < 20; $cpt++)
        {
            if (isset($msg[$cpt]))
            {
                $nb      = $nb + 1;
                $requete =
                    'update messages_dest set dmsg_archive = \'O\',dmsg_lu = \'O\' where dmsg_cod = ' . $msg[$cpt] . ' and dmsg_perso_cod = ' . $perso_cod;
                $db->query($requete);
            }
        }
        $contenu_page .= $nb . ' messages ont été archivés.';
        break;
    case "select_non_lu":
        $nb = 0;
        for ($cpt = 0; $cpt < 20; $cpt++)
        {
            if (isset($msg[$cpt]))
            {
                $nb      = $nb + 1;
                $requete =
                    'update messages_dest set dmsg_lu = \'N\' where dmsg_cod = ' . $msg[$cpt] . ' and dmsg_perso_cod = ' . $perso_cod;
                $db->query($requete);
            }
        }
        $contenu_page .= $nb . ' messages ont été marqués comme non lus.';
        break;
    case "efface_msg":
        $requete =
            'update messages_dest set dmsg_efface = 1,dmsg_lu = \'O\' where dmsg_msg_cod = ' . $mid . ' and dmsg_perso_cod = ' . $perso_cod;
        $db->query($requete);
        $contenu_page .= 'Le message a bien été effacé.';
        break;
    case "archive__vue generale_msg":
        $requete =
            'update messages_dest set dmsg_archive = \'O\' where dmsg_cod = ' . $mid . ' and dmsg_perso_cod = ' . $perso_cod;
        $db->query($requete);
        $contenu_page .= 'Le message a été archivé.';
        break;
    case "efface_vue generale_msg":
        $requete =
            'update messages_dest set dmsg_efface = 1,dmsg_lu = \'O\' where dmsg_cod = ' . $mid . ' and dmsg_perso_cod = ' . $perso_cod;
        $db->query($requete);
        $contenu_page .= 'Le message a bien été effacé. Message : ' . $mid . ' perso : ' . $perso_cod;
        break;
    case "archive_msg":
        $requete =
            'update messages_dest set dmsg_archive = \'O\' where dmsg_msg_cod = ' . $mid . ' and dmsg_perso_cod = ' . $perso_cod;
        $db->query($requete);
        $requete =
            'update messages_exp set emsg_archive = \'O\' where emsg_msg_cod = ' . $mid . ' and emsg_perso_cod = ' . $perso_cod;
        $db->query($requete);
        $contenu_page .= 'Le message a été archivé.';
        break;
    case "non_lu_msg":
        $requete =
            'update messages_dest set dmsg_lu = \'N\' where dmsg_msg_cod = ' . $mid . ' and dmsg_perso_cod = ' . $perso_cod;
        $db->query($requete);
        $contenu_page .= 'Le message a été marqué comme non lu.';
        break;
    case "nouveau_message":
        $db2    = new base_delain;
        $guilde = 'N';
        $erreur = 0;

        // Modif Morgenese
        // permet de matcher les codes html et de supprimer les ; au bout du caractère
        // pour ne par le confondre avec un autre perso
        //$dest = preg_replace("(&#[0-9]+);","\\1", $dest);
        //fin modif

        $tab_dest     = explode(";", $dest);
        $nb_dest      = count($tab_dest);
        $nb_vrai_dest = 0;
        $req          = "select valeur_bonus($perso_cod, 'BER')";
        $db->query($req);
        $db->next_record();
        if ($db->f('valeur_bonus') > 0)
        {
            $contenu_page .= '<br><br><strong>********* Vous êtes sous l’effet d’un Bernardo, vous ne pouvez pas envoyer de message ! *********</strong><br><br>';
            $erreur       = 1;
        }
        for ($cpt = 0; $cpt < $nb_dest; $cpt++)
        {
            if ($tab_dest[$cpt] != "")
            {
                $nb_vrai_dest = $nb_vrai_dest + 1;
            }
        }
        if ($nb_dest > 100)
        {
            $contenu_page .= '<br><br><strong>********* Vous ne pouvez pas envoyer un message à plus de 100 destinataires ! *********</strong><br><br>';
            $erreur       = 1;
        }
        if ($nb_vrai_dest == 0)
        {
            $contenu_page .= '<br><br><strong>********* Vous devez renseigner au moins un destinataire ! *********</strong><br><br>';
            $erreur       = 1;
        }
        if ($titre == '')
        {
            $contenu_page .= '<br><br><strong>********* Vous devez mettre un titre au message !*********</strong><br><br>';
            $erreur       = 1;
        }
        if (strlen($titre) >= 50)
        {
            $contenu_page .= '<br><br><strong>********* Votre titre est trop long, merci de le raccourcir ! *********</strong><br><br>';
            $erreur       = 1;
        }
        if ($erreur == 1)
        {
            $contenu_page .= '<!-- Titre original: [' . $titre . '] -->';
            $titre        = htmlspecialchars($titre);
            $titre        = str_replace(";", chr(127), $titre);
            $titre        = pg_escape_string($titre);
            $contenu_page .= '<!-- Titre final: [' . $titre . '] -->';
            $corps        = htmlspecialchars($corps);
            $corps        = str_replace(";", chr(127), $corps);
            $corps        = pg_escape_string($corps);
            $contenu_page .= '<form name="nouveau_message" method="post" action="action_message.php">
				<input type="hidden" name="msg_init" value="' . $msg_init . '">
				<input type="hidden" name="methode" value="nouveau_message">
				<table cellpadding="2" cellspacing="2">
					<tr>
						<td class="soustitre2">Destinataires : <br><em>(Entrez les noms des destinataires séparés par des ";")</em></td>
						<td><input type="text" name="dest" size="40" value="' . $dest . '"></td>
						<td>
					<tr>
						<td class="soustitre2">Titre du message : </td>
						<td colspan="2"><input type="text" name="titre" size="50" MAXLENGTH="50" value="' . $titre . '"></td>
					</tr>
					<tr>
						<td colspan="2" class="soustitre2"><em>Rappel : </em>Merci de bien vouloir éviter les insultes, et de rester dans le cadre de la courtoisie dans vos messages. Tout abus pourra amener à une cloture du compte sans préavis.</td>
					</tr>
					<tr>
						<td class="soustitre2">Corps du message : </td>
						<td colspan="2">
						<table>
							<tr>
								<td><textarea name="corps" cols="40" rows="10">' . $corps . '</textarea>
								</td>
								<td><input type="button" class="test" onClick="javascript:window.open(\'http://www.jdr-delain.net/includes/codes.php\',\'Smilies\',\'scrollbars=yes,width=300,height=500\')" value="Voir les smilies">
							</td></tr>
						</table></td></tr>
					<tr>
						<td colspan="3" class="soustitre2"><center><input type="submit" accesskey="s" class="test" value="Envoyer le message !"></center></td>
					</tr>
				</table>
				</form>';
        }
        if ($erreur == 0)
        {
            /************************************/
            /* d’abord on enregistre le message */
            /************************************/

            $msg = new message();

            if (!isset($msg_init))
            {
                $msg_init = 0;
            }
            $msg->enReponseA = $msg_init;

            // Crapaud ?
            $req = "select perso_crapaud from perso where perso_cod = $perso_cod ";
            $db->query($req);
            $db->next_record();
            if ($db->f("perso_crapaud") == 1)
            {
                $titre = "Crôôa ?!!??? ";
                $corps = "Croa, crôoâa, crôâ, CROOOAAAAA !
				" . $corps;
            }

            $msg->corps      = $corps;
            $msg->sujet      = $titre;
            $msg->expediteur = $perso_cod;

            /**********************************/
            /* On boucle sur les destintaires */
            /**********************************/
            $nb_expedie        = 0;
            $nb_non_expedie    = 0;
            $liste_non_expedie = "";
            $liste_expedie     = "";
            for ($cpt = 0; $cpt < $nb_dest; $cpt++)
            {
                $special = 0;
                // on cherche le destinataire
                if ($tab_dest[$cpt] != "")
                {
                    if ($tab_dest[$cpt] == 'tous_joueurs_admin')
                    {
                        $special = 1;
                        $req_pos =
                            "select ppos_pos_cod, distance_vue($perso_cod) as dist, pos_etage, pos_x, pos_y from perso_position, perso, positions where ppos_perso_cod = $perso_cod and perso_cod = $perso_cod and ppos_pos_cod = pos_cod ";
                        $db->query($req_pos);
                        $db->next_record();
                        $pos_actuelle = $db->f("ppos_pos_cod");
                        $v_x          = $db->f("pos_x");
                        $v_y          = $db->f("pos_y");
                        $etage        = $db->f("pos_etage");
                        $vue          = $db->f("dist");
                        $req_vue      = 'select perso_cod, perso_nom, distance(ppos_pos_cod,' . $pos_actuelle . ') from perso, perso_position, positions
							where pos_x >= (' . $v_x . ' - ' . $vue . ') and pos_x <= (' . $v_x . ' + ' . $vue . ')
    							and pos_y >= (' . $v_y . ' - ' . $vue . ') and pos_y <= (' . $v_y . ' + ' . $vue . ')
    							and ppos_perso_cod = perso_cod
    							and perso_cod != ' . $perso_cod . '
    							and perso_type_perso = 1
    							and perso_actif = \'O\'
    							and ppos_pos_cod = pos_cod
    							and pos_etage = ' . $etage;
                        $db->query($req_vue);
                        while ($db->next_record())
                        {
                            $msg->ajouteDestinataire($db->f("perso_cod"));
                            $liste_expedie = $liste_expedie . $db->f("perso_nom") . ", ";
                            $nb_expedie++;
                        }
                    }
                    if (!strcasecmp($tab_dest[$cpt], 'guilde'))
                    {
                        $special    = 1;
                        $guilde     = 'O';
                        $dest       = '';
                        $req_guilde = "select pguilde_guilde_cod from guilde_perso
							where pguilde_perso_cod = $perso_cod
							and pguilde_valide = 'O' ";
                        $db->query($req_guilde);
                        $db->next_record();
                        $num_guilde = $db->f("pguilde_guilde_cod");

                        $msg->guilde = $num_guilde;

                        $req_membre = 'select perso_cod,perso_nom from perso,guilde_perso
							where pguilde_guilde_cod = ' . $num_guilde . '
							and pguilde_perso_cod != ' . $perso_cod . '
							and pguilde_perso_cod = perso_cod
							and pguilde_valide = \'O\' and pguilde_message = \'O\' ';
                        $db->query($req_membre);

                        while ($db->next_record())
                        {
                            $msg->ajouteDestinataire($db->f("perso_cod"));
                            $liste_expedie = $liste_expedie . $db->f("perso_nom") . ", ";
                            $nb_expedie++;
                        }
                    }
                    if (substr($tab_dest[$cpt], 0, 10) == 'liste_dif_')
                    {
                        $special = 1;
                        $liste   = substr($tab_dest[$cpt], 10);
                        // on vérfie que cette liste soit bien au bon perso
                        $req = "select cliste_cod from contact_liste
							where (cliste_cod = $liste and cliste_perso_cod = $perso_cod)
								or exists (select 1 from contact,perso where contact_cliste_cod = $liste and contact_perso_cod = $perso_cod) ";
                        $db->query($req);
                        if ($db->nf() == 0)
                        {
                            $contenu_page .= "Vous ne pouvez pas écrire à cette liste !";
                        } else
                        {
                            $req =
                                "select contact_perso_cod,perso_nom from contact,perso where contact_cliste_cod = $liste and contact_perso_cod = perso_cod ";
                            $db->query($req);
                            while ($db->next_record())
                            {
                                $msg->ajouteDestinataire($db->f("contact_perso_cod"));
                                $liste_expedie = $liste_expedie . $db->f("perso_nom") . ", ";
                                $nb_expedie++;
                            }
                        }
                    }
                    if ($special == 0)
                    {
                        $nom_dest = ltrim(rtrim($tab_dest[$cpt]));
                        $nom_dest = pg_escape_string($nom_dest);
                        $req_dest = "select f_cherche_perso('$nom_dest') as num_perso";
                        $db->query($req_dest);
                        $db->next_record();
                        $tab_res_dest = $db->f("num_perso");
                        if ($tab_res_dest == -1)
                        {
                            $nb_non_expedie++;
                            $liste_non_expedie = $liste_non_expedie . $tab_dest[$cpt] . ",";
                        } else
                        {
                            $msg->ajouteDestinataire($db->f("num_perso"));
                            $nb_expedie++;
                            $liste_expedie = $liste_expedie . $tab_dest[$cpt] . ", ";
                        }
                    }
                }
            }
            if ($nb_non_expedie != 0)
            {
                $contenu_page .= "Le message n’a pas été expédié à $liste_non_expedie : aventurier(s) inexistant(s).";
            }

            $envoi = $msg->envoieMessage();
            if ($envoi)
            {
                $contenu_page .= "Le message a été envoyé correctement à $liste_expedie. Il arrivera sous peu.";
            } else
            {
                $contenu_page .= 'Le message n’a pas été envoyé : pas de destinataires valides trouvés.';
            }
        }
        break;
}

$contenu_page .= '</center></td>
	</tr>
	</table>';

include "blocks/_footer_page_jeu.php";
