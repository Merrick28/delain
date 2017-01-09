<?php
if (!isset($is_log))
{
    $is_log = 'N';
}
$db = new base_delain;
/***************************************************************/
/* Fonctions pour l'affichage des barres de santé et XP		*/
/***************************************************************/
// barre XP
function barre_xp($perso_px, $limite_niveau_actuel, $limite_niveau)
{
    $barre_xp = '0';
    if (($perso_px - $limite_niveau_actuel) < 0)
    {
        $barre_xp = 'negative';
        return $barre_xp;
    }
    $niveau_xp = ($perso_px - $limite_niveau_actuel);
    $div_xp    = ($limite_niveau - $limite_niveau_actuel);
    $niveau_xp = (floor(($niveau_xp / $div_xp) * 10)) / 10;

    $barre_xp = round($niveau_xp, 1) * 100;
    //$barre_xp =floor($niveau_xp);
    if ($barre_xp >= 100)
    {
        $barre_xp = 100;
    }
    return $barre_xp;
}

// barre de sante
function barre_hp($perso_pv, $perso_pv_max)
{
    $barre_hp = floor(($perso_pv / $perso_pv_max) * 10) * 10;
    //$barre_hp = round(($perso_pv/$perso_pv_max),1)*100;
    if ($barre_hp >= 100)
    {
        $barre_hp = 100;
    }
    return $barre_hp;
}

// barre d'énergie
function barre_energie($perso_energie)
{
    $barre_energie = floor(($perso_energie / 100) * 10) * 10;
    if ($barre_energie >= 100)
    {
        $barre_energie = 100;
    }
    return $barre_energie;
}

// barre d'énergie divine (pour familiers divins uniquement)
function barre_divine($perso_divine)
{
    $barre_divine = floor(($perso_divine / 200) * 10) * 10;
    if ($barre_divine >= 100)
    {
        $barre_divine = 100;
    }
    return $barre_divine;
}

// affichage d'un bloc perso
function affiche_perso($perso_cod)
{
    include "img_pack.php";
    global $type_flux;
    global $is_log;
    $db  = new base_delain;
    $req = "select perso_cod,perso_nom,to_char(perso_dlt,'DD/MM/YYYY hh24:mi:ss') as dlt,perso_energie,
		perso_pv,perso_pv_max,dlt_passee(perso_cod) as dlt_passee,to_char(prochaine_dlt(perso_cod),'DD/MM hh24:mi') as prochaine_dlt,perso_pa,perso_race_cod,perso_sex,
		limite_niveau(perso_cod) as limite_niveau,limite_niveau_actuel(perso_cod) as limite_niveau_actuel,floor(perso_px) as perso_px,
		pos_x,pos_y,pos_etage,perso_niveau,perso_avatar,etage_libelle,perso_description,perso_tangible,perso_nb_tour_intangible,
		exists(select levt_cod from ligne_evt where levt_perso_cod1 = $perso_cod and levt_lu = 'N' limit 1) as events, perso_gmon_cod, perso_avatar_version
		from perso,perso_position,positions,etage
	where perso_cod = $perso_cod
	and ppos_perso_cod = perso_cod
	and ppos_pos_cod = pos_cod
	and perso_actif = 'O'
	and etage_numero = pos_etage ";
    $db->query($req);
    $db->next_record();
    // description

    $desc = nl2br(htmlspecialchars(str_replace('\'', '’', $db->f("perso_description"))));
    $pa   = $db->f("perso_pa");

    $db2 = new base_delain;
    if ($db->f("perso_avatar") == '')
    {
        $avatar = G_IMAGES . $db->f("perso_race_cod") . "_" . $db->f("perso_sex") . ".png";
    }
    else
    {
        $avatar = $type_flux . G_URL . "avatars/" . $db->f("perso_avatar");
    }
    //
    // Partie permier avril
    //
    //$avatar = G_URL . "avatars/" . $aff_avat;
    $annee_en_cours = date('Y');
    $debut_1avril   = mktime(0, 0, 1, 4, 1, $annee_en_cours);
    $fin_1avril     = mktime(0, 0, 1, 4, 2, $annee_en_cours);
    $is1avril       = time() > $debut_1avril && time() < $fin_1avril;
    //
    // fin 1er avril
    //
    $perso_px             = $db->f("perso_px");
    $limite_niveau_actuel = $db->f("limite_niveau_actuel");
    $limite_niveau        = $db->f("limite_niveau");
    $energie              = $db->f("perso_energie");
    $barre_xp             = barre_xp($perso_px, $limite_niveau_actuel, $limite_niveau);
    $barre_hp             = barre_hp($db->f("perso_pv"), $db->f("perso_pv_max"));
    $barre_energie        = barre_energie($db->f("perso_energie"));

    // récupération énergie divine pour les familiers divins
    $barre_divine   = -1;
    $energie_divine = -1;
    if ($db->f("perso_gmon_cod") == 441)
    {
        $db_divin = new base_delain;
        $req      = "select dper_points from dieu_perso where dper_perso_cod = $perso_cod";
        $db_divin->query($req);
        $db_divin->next_record();
        $energie_divine = $db_divin->f("dper_points");
        $barre_divine   = barre_divine($energie_divine);
    }
    echo '<table width="100%" border="0">
		<tr>
		<td colspan="2" class="titre" valign="top"><div class="titre">' . $db->f("perso_nom") . '</div></td></tr>
		<tr><td colspan="2" class="soustitre2"><div style="text-align:center;font-size:7pt;">' . $desc . '</div></td></tr>
		<tr><td class="soustitre2" colspan="2">';
    if ($db->f("dlt_passee") == 1)
    {
        echo '<b>';
    }
    echo 'DLT : ' . $db->f("dlt");
    if ($db->f("dlt_passee") == 1)
    {
        echo '</b>';
    }
    echo '<br /><i>Puis ± ', $db->f('prochaine_dlt') . '</i>';
    echo '<br></td></tr>
		<tr><td class="soustitre2" colspan="2">Position : X=' . $db->f("pos_x") . '; Y=' . $db->f("pos_y") . '; ' . $db->f("etage_libelle") . '</td></tr>';
    $num_perso = $perso_cod;
    $guilde    = $db2->get_nom_guilde($num_perso);
    echo '<tr><td class="soustitre2" colspan="2">';
    if ($guilde == '')
    {
        echo 'Pas de guilde';
    }
    else
    {
        echo 'Guilde : ' . $guilde;
    }
    $niveau           = $db->f("perso_niveau");
    $tours_impalpable = ($db->f('perso_nb_tour_intangible') > 1) ? ' tours' : ' tour';
    $impalpable       = ($db->f('perso_tangible') == 'N') ? '<br /><i>Impalpable (' . $db->f('perso_nb_tour_intangible') . $tours_impalpable . ')</i>' : '';

    if ($is1avril)
    {
        $niveau     = mt_rand(1, 10);
        $impalpable = ' - <i>Impalpable</i>';
        $dbavril    = new base_delain;
        $dbavril->query('select gmon_nom from monstre_generique where gmon_cod < 50 and gmon_niveau < 5 and gmon_cod <> 7
			order by random()
			limit 1');
        $dbavril->next_record();
        $impalpable .= '<br /><i>Tué par un(e) ' . $dbavril->f('gmon_nom') . '</i>';
        $barre_hp = barre_hp($db->f("perso_pv") / 3, $db->f("perso_pv_max"));
    }

    echo '</td></tr>
		<tr><td valign="top"><center><a href="#" onClick="javascript:document.login.perso.value=' . $num_perso . ';document.login.submit();"><img src="' . $avatar . '?' . $db->f("perso_avatar_version") . '" alt="Jouer ' . $db->f("perso_nom") . '"/></a>
		', ($db->f('events') == 'f' ? '' : '<table><tr><td class="bouton" height="1" width="1"><span class="bouton">
		<input type="button" class="bouton" onClick="javascript:window.open(\'' . $type_flux . G_URL . 'visu_derniers_evt.php?visu_perso=' . $num_perso . '&is_log=' . $is_log . '\',\'evenements\',\'scrollbars=yes,resizable=yes,width=500,height=300\');" title=\'Cliquez ici pour voir vos événements importants depuis votre dernière connexion\' value="Événements" /></span></td></tr></table>'), '
		</center>
		</td>
		<td>
		<table>
		<tr><td>
		<div class="image"><b>Niveau ' . $niveau . '</b>' . $impalpable . '</div>
		</td></tr>
		<tr><td>
		<div class="image"><img src="' . G_IMAGES . 'barrepa_' . $pa . '.gif" alt="' . $pa . 'PA">
		</div></td></tr>
		<tr><td>
		<div class="image"><img src="' . G_IMAGES . 'coeur.gif" alt=""> <img src="' . G_IMAGES . 'hp' . $barre_hp . '.gif" title="' . $db->f("perso_pv") . 'PV sur ' . $db->f("perso_pv_max") . '" alt="' . $db->f("perso_pv") . 'PV sur ' . $db->f("perso_pv_max") . '">
		</div></td></tr>';

    $is_enchanteur = $db->is_enchanteur($perso_cod);
    if ($is_enchanteur)
    {
        echo '	<tr><td>
			<div class="image"><img src="' . G_IMAGES . 'energi10.png" alt=""> <img src="' . G_IMAGES . 'nrj' . $barre_energie . '.png" title="' . $energie . ' sur 100" alt="' . $energie . ' sur 100">
			</div></td></tr>';
    }
    echo '	<tr><td>
		<div class="image"><img src="' . G_IMAGES . 'iconexp.gif" alt=""> <img src="' . G_IMAGES . 'xp' . $barre_xp . '.gif" title="' . $perso_px . ' PX, prochain niveau à ' . $limite_niveau . '" alt="' . $perso_px . ' PX sur ' . $limite_niveau . '">
		</div></td></tr>';

    if ($energie_divine > 0)
    {
        echo '	<tr><td>
			<div class="image"><img src="' . G_IMAGES . 'magie.gif" alt="" title="Énergie divine"> <img src="' . G_IMAGES . 'nrj' . $barre_divine . '.png" title="Énergie divine : ' . $energie_divine . '" alt="Énergie divine : ' . $energie_divine . '">
			</div></td></tr>';
    }

    echo '<tr><td>';
    //
    // Messages
    //
    $req_msg = "select count(*) as nombre from messages_dest where dmsg_perso_cod = $num_perso
		and dmsg_lu = 'N' and dmsg_archive = 'N' ";
    $db2->query($req_msg);
    $db2->next_record();
    $nb_msg = $db2->f("nombre");
    if ($nb_msg != 0)
    {
        $txt_msg = '<span class="bouton">
			<input type="button" class="bouton"
				onClick="javascript:window.open(\'' . $type_flux . G_URL . 'visu_messages.php?visu_perso=' . $num_perso . '\',\'messages\',\'scrollbars=yes,resizable=yes,width=800,height=600\');" title=\'Cliquez ici pour lire vos 10 derniers messages\' value="' . $nb_msg . ' messages non lus." />
			</span>';
        echo $txt_msg . '<br>';
    }
    //
    // Transactions
    //
    $req_tran = "select * from transaction where (tran_vendeur = $num_perso or tran_acheteur = $num_perso)";
    $db2->query($req_tran);
    $nb_tran = $db2->nf();
    if ($nb_tran != 0)
    {
        echo $nb_tran . ' transactions en attente.<br>';
    }
    echo '</td></tr></table>';
    echo '</td></tr></table>';
}

function affiche_case_perso_vide()
{
    global $type_flux, $compt_cod;
    echo '<table width="100%" height="100%" border="0">
		<tr><td height="100%" valign="center" class="soustitre2" style="text-align:center;">Pas de personnage<br></td></tr>
		<tr><td height="100%" valign="center">&nbsp;<br></td></tr>
		<tr><td height="100%" valign="center">&nbsp;<br></td></tr>
		<tr><td><center><a href="', $type_flux, G_URL, 'cree_perso_compte.php?compt_cod=', $compt_cod, '">
		<img src="', G_IMAGES, 'noperso.gif" alt="Créer un nouveau"></a></center></td></tr></table>';
}

function affiche_case_monstre_vide()
{
    echo '<table width="100%" height="100%" border="0">
		<tr><td height="100%" valign="center" class="soustitre2" style="text-align:center;">Pas encore de monstre !<br></td></tr>
		<tr><td height="100%" valign="center">&nbsp;<br></td></tr>
		<tr><td height="100%" valign="center">&nbsp;<br></td></tr>
		<tr><td><center><img src="', G_IMAGES, 'noperso.gif" alt="Pas de monstre"/></center></td></tr></table>';
}

/***************************************************************/
/* Fin des fonctions                                           */
/***************************************************************/
//
/***************************************************************/
/* Début de la page											*/
/***************************************************************/
$req = "select compt_ligne_perso, autorise_4e_perso(compt_quatre_perso, compt_dcreat) OR autorise_4e_monstre(compt_quatre_perso, compt_dcreat) as autorise_quatrieme, compt_type_quatrieme ";
$req .= " from compte where compt_cod = " . $compt_cod;
$db->query($req);
$db->next_record();
$nb_perso_max   = $db->f('compt_ligne_perso') * 3;
$nb_perso_ligne = 3;
$ok_4           = ($db->f('autorise_quatrieme') != 'f');
if ($ok_4)
{
    $nb_perso_max   = $db->f('compt_ligne_perso') * 4;
    $nb_perso_ligne = 4;
}
$taille = 100 / $nb_perso_ligne;
$type_4 = $db->f('compt_type_quatrieme');
/*********************/
/* Persos classiques */
/*********************/
$req_perso = "select pcompt_perso_cod, case when 2 IN (perso_type_perso, perso_pnj) then 1 else 0 end as quatrieme
	from perso_compte
	inner join perso on perso_cod = pcompt_perso_cod
	where pcompt_compt_cod = $compt_cod and perso_actif = 'O'
	order by perso_cod ";
$db->query($req_perso);
$perso_normaux = array();
$quatriemes    = array();

$cpt_normaux    = 0;
$cpt_quatriemes = 0;

while ($db->next_record())
{
    if ($db->f('quatrieme') == 1)
    {
        $quatriemes[] = $db->f('pcompt_perso_cod');
    }
    else
    {
        $perso_normaux[] = $db->f('pcompt_perso_cod');
    }
    $premier_perso = $db->f('pcompt_perso_cod');
}
if (sizeof($quatriemes) == 0 && $ok_4)
{
    $quatriemes[] = false;
}

while (sizeof($perso_normaux) % 3 !== 0)
{
    $perso_normaux[] = false;
}
$premier_perso = (isset($perso_normaux[0])) ? $perso_normaux[0] : -1;
if ($premier_perso == -1)
{
    $premier_perso = (isset($quatriemes[0])) ? $quatriemes[0] : -1;
}

$numero_quatrieme = -1;
$cpt              = 0;
while ($cpt_normaux < sizeof($perso_normaux) || $cpt_quatriemes < sizeof($quatriemes))
{
    // Est-on sur la case réservée au quatrième ?
    $case_quatrieme = $ok_4 && ($cpt % $nb_perso_ligne == $nb_perso_ligne - 1);

    // Début de ligne
    if (fmod($cpt, $nb_perso_ligne) == 0)
    {
        echo '<tr>';
    }

    // Début de case
    echo '<td valign="top" width="' . $taille . '%">';

    // Une case normale
    if (!$case_quatrieme)
    {
        // On a un perso à afficher
        if (!empty($perso_normaux[$cpt_normaux]))
        {
            affiche_perso($perso_normaux[$cpt_normaux]);
        }
        else
        {
            affiche_case_perso_vide();
        }

        $cpt_normaux++;
    }

    // Une case de 4ème perso
    if ($case_quatrieme)
    {
        // On a un perso à afficher
        if (!empty($quatriemes[$cpt_quatriemes]))
        {
            affiche_perso($quatriemes[$cpt_quatriemes]);
        }
        elseif ($type_4 != 2)
        {
            affiche_case_perso_vide();
        }
        else
        {
            affiche_case_monstre_vide();
        }

        $cpt_quatriemes++;
    }

    echo '</td>';
    if (fmod(($cpt + 1), $nb_perso_ligne) == 0)
    {
        echo '</tr>';
    }
    $cpt++;
}

echo "<tr><td colspan='$nb_perso_ligne' style='text-align:center'><span class='bouton'>";
echo '<input type="button" class="bouton" onClick="javascript:window.open(\'' . $type_flux . G_URL . 'visu_derniers_evt.php?visu_perso=' . $premier_perso . '&is_log=' . $is_log . '&voir_tous=1\',\'evenements\',\'scrollbars=yes,resizable=yes,width=500,height=300\');" title="Voir les derniers événements de tous les personnages" value="Voir tous les événements" style="width:200px;"/></span>&nbsp;&nbsp;';
echo "<span class='bouton'><input type='button' class='bouton' onClick='javascript:document.login.perso.value=$premier_perso; document.login.activeTout.value=1; document.login.submit();' title='Activer toutes les DLT' value='Activer toutes les DLT' style='width:200px;'/></span></td></tr>";

/*************/
/* Familiers */
/*************/
$req_perso = "select pfam_familier_cod,perso_cod
	from perso,perso_compte,perso_familier
	where pcompt_compt_cod = $compt_cod
	and pcompt_perso_cod = pfam_perso_cod
	and pfam_familier_cod = perso_cod
	and perso_actif = 'O'
	and perso_type_perso = 3
	order by pfam_perso_cod ";
$db->query($req_perso);
if ($db->nf() != 0)
{
    echo '<tr><td colspan="3"><hr><div class="titre">Familiers : </div></td></tr>';
    $nb_perso    = $db->nf();
    $alias_perso = 0;
    for ($cpt = 0; $cpt < $nb_perso_max; $cpt++)
    {
        if (fmod($cpt, $nb_perso_ligne) == 0)
        {
            echo '<tr>';
        }
        echo '<td valign="top" width="' . $taille . '%">';

        //tableau intérieur
        if ($cpt < $nb_perso)
        {
            $db->next_record();
            affiche_perso($db->f('perso_cod'));
        }
        //fin tableau intérieur

        echo '</td>';
        if (fmod(($cpt + 1), $nb_perso_ligne) == 0)
        {
            echo '</tr>';
        }
    }
}
/******************************************/
/* Comptes sittés ?					   */
/******************************************/
$req_perso = "select pcompt_perso_cod
	from perso,perso_compte,compte_sitting
	where csit_compte_sitteur = $compt_cod
	and csit_compte_sitte = pcompt_compt_cod
	and csit_ddeb <= now()
	and csit_dfin >= now()
	and pcompt_perso_cod = perso_cod
	and perso_actif = 'O'
	and perso_type_perso = 1
	order by perso_cod ";
$db->query($req_perso);
if ($db->nf() != 0)
{
    //
    // là on a des persos sittés, donc, on va quand même regarder ce qui se passe
    //
    echo '<tr><td colspan="3"><hr><div class="titre">Persos sittés : </div></td></tr>';
    $nb_perso_max = $db->nf();
    $nb_perso     = $nb_perso_max;
    for ($cpt = 0; $cpt < $nb_perso_max; $cpt++)
    {
        if ($cpt < $nb_perso)
        {
            $db->next_record();
        }
        if (fmod($cpt, $nb_perso_ligne) == 0)
        {
            echo '<tr>';
        }
        echo '<td valign="top" width="' . $taille . '%">';

        //tableau intérieur
        if ($cpt >= $nb_perso)
        {
            $nom      = 'Pas de perso';
            $image    = '';
            $barre_pa = '';
            $barre_hp = '';
            $barre_xp = '';
            $enc      = '';
            echo '<table width="100%" height="100%" border="0">
				<tr><td height="100%" valign="center" class="soustitre2" style="text-align:center;">Pas de personnage<br></td></tr>
				<tr><td height="100%" valign="center">&nbsp;<br></td></tr>
				<tr><td height="100%" valign="center">&nbsp;<br></td></tr>
				<tr><td><center><img src="' . G_IMAGES . 'noperso.gif"></center></td></tr></table>';
        }
        else
        {
            affiche_perso($db->f('pcompt_perso_cod'));
        }
        //fin tableau intérieur

        echo '</td>';
        if (fmod(($cpt + 1), $nb_perso_ligne) == 0)
        {
            echo '</tr>';
        }
    }
    //
    // bon, on sait qu'on a sitté des persos, maintenant, on va quand même voir s'il y a des familiers
    //
    $req_perso = "select pfam_familier_cod,perso_cod
		from perso,perso_compte,perso_familier,compte_sitting
		where csit_compte_sitteur = $compt_cod
		and csit_compte_sitte = pcompt_compt_cod
		and csit_ddeb <= now()
		and csit_dfin >= now()
		and pcompt_perso_cod = pfam_perso_cod
		and pfam_familier_cod = perso_cod
		and perso_actif = 'O'
		and perso_type_perso = 3
		order by pfam_perso_cod ";
    $db->query($req_perso);
    if ($db->nf() != 0)
    {

        $nb_perso_max = $db->nf();
        $nb_perso     = $nb_perso_max;
        $alias_perso  = 0;
        for ($cpt = 0; $cpt < $nb_perso_max; $cpt++)
        {
            if ($cpt < $nb_perso)
            {
                $db->next_record();
            }
            if (fmod($cpt, $nb_perso_ligne) == 0)
            {
                echo '<tr>';
            }
            echo '<td valign="top" width="' . $taille . '%">';
            echo '<!--' . $cpt . '-' . $nb_perso_max . '-' . $nb_perso . '-->';
            //tableau intérieur
            if ($cpt < $nb_perso)
            {
                affiche_perso($db->f('perso_cod'));
            }
            //fin tableau intérieur

            echo '</td>';
            if (fmod(($cpt + 1), $nb_perso_ligne) == 0)
            {
                echo '</tr>';
            }
        }
    }
}
