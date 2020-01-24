<?php
// visu_evt_perso
include "blocks/_header_page_jeu.php";

if (!isset($_REQUEST['visu']))
{
    $visu = '';
} else
{
    $visu = $_REQUEST['visu'];
}

if (filter_var($visu, FILTER_VALIDATE_INT) === false)
{
    echo "Anomalie sur numéro perso !";
    exit();
}

$visu_perso = new perso();
$visu_perso->charge($visu);

/*****************************/
/* GESTION DE LA DESCRIPTION */
/*****************************/
if (!isset($_REQUEST['met']))
{
    $met = 'vide';
} else
{
    $met = $_REQUEST['met'];
}


$compte = new compte;
$compte->charge($compte_cod);
if ($met == 'aff')
{
    $compte->compt_vue_desc = 1;
    $compte->stocke();
}

if ($met == 'masq')
{
    $compte->compt_vue_desc = 0;
    $compte->stocke();
}

if ($compte->compt_vue_desc == 1)
{
    include "perso2_description.php";

    $contenu_page .= '<form name="message" method="post" action="messagerie2.php">
	<input type="hidden" name="m" value="2">
	<input type="hidden" name="n_dest" value="' . $visu_perso->perso_nom . '">
	<input type="hidden" name="dmsg_cod">
	</form>
	<div style=text-align:center>
	<a href="javascript:document.message.submit();">Envoyer un message !</a><br>
	<a href="' . $PHP_SELF . '?met=masq&visu=' . $visu . '">Masquer la description ?</a></div>';

} else
{
    $contenu_page .= '<center><a href="' . $PHP_SELF . '?met=aff&visu=' . $visu . '">Afficher la description ?</a></center>';
}
/****************************************/
/* CONTENU                              */
/****************************************/

if (!isset($_REQUEST['pevt_start']))
{
    $pevt_start = 0;
} else
{
    $pevt_start = $_REQUEST['pevt_start'];
}
if ($pevt_start < 0)
{
    $pevt_start = 0;
}

$race = new race;
$race->charge($visu_perso->perso_race_cod);

$contenu_page .= '<center><table cellspacing="2">
	<tr>
	<td colspan="3" class="titre"><div class="titre">Evènements de ' . $visu_perso->perso_nom . '(' .
                 $visu_perso->perso_sex . ' - ' .
                 $race->race_nom . ')</div></td>
	</tr>';
$contenu_page .= '<form name="visu_evt" method="post" action="visu_evt_perso.php">
	<input type="hidden" name="visu">';
$levt         = new ligne_evt();
$tab_evt      = $levt->getByPerso($pevt_start, 20);
$first        = true;
foreach ($tab_evt as $val)
{
    $contenu_page .= '<tr>
			<td class="soustitre3">' . format_date($ligne_evt->levt_date) . '</td>
			<td class="soustitre3"><strong>' . $ligne_evt->tevt->tevt_libelle . '</strong></td>';
    if ($compte->is_admin())
    {
        $texte = str_replace('[perso_cod1]', '<strong><a href="javascript:document.visu_evt.visu.value=' .
                                             $val->levt_perso_cod1 . ';document.visu_evt.submit();">' . $val->perso1->perso_nom . '</a></strong>', $val->levt_texte);
    } else
    {
        if ($first && 'Effet automatique' == $ligne_evt->tevt->tevt_libelle)
        {
            continue;
        }

        $first = false;
        $texte =
            str_replace('[perso_cod1]', '<strong><a href="javascript:document.form_visu.visu.value=' .
                                        $val->levt_perso_cod1 . ';document.form_visu.submit();">' . $val->perso1->perso_nom . '</a></strong>', $val->tevt->tevt_texte);
    }
    $texte = str_replace('[attaquant]', '<strong><a href="javascript:document.form_visu.visu.value=' .
                                        $val->levt_attaquant . ';document.form_visu.submit();">' .
                                        $val->perso_attaquant->perso_nom . '</a></strong>', $texte);
    $texte = str_replace('[cible]', '<strong><a href="javascript:document.form_visu.visu.value=' .
                                    $val->levt_cible . ';document.form_visu.submit();">' .
                                    $val->perso_cible->perso_nom . '</a></strong>', $texte);

    $contenu_page .= '<td>' . $texte . '</td></tr>';
}


$contenu_page .= '<tr></form><td><form name="evt" method="post" action="visu_evt_perso.php"><input type="hidden" name="pevt_start">
	<input type="hidden" name="visu" value="' . $visu . '">';
if ($pevt_start != 0)
{
    $contenu_page .= '<div align="left"><a href="javascript:document.evt.pevt_start.value=' . $pevt_start . '-20;document.evt.submit();"><== Précédent</a></div>';
}
$contenu_page .= '</td><td></td>
	<td><div align="right"><a href="javascript:document.evt.pevt_start.value=' . $pevt_start . '+20;document.evt.submit();">Suivant ==></a></div></td>
	</tr></form></table></center>';
include "blocks/_footer_page_jeu.php";