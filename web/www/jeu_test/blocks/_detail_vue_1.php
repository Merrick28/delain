<?php
$comment = "";
$titre = "";

if (!$terrain_chevauchable) echo '<div class="horseBlink">';

if ($result['t_decor'] != 0)
{
    echo '<div class="caseVue decor' . $result['t_decor'] . '">';
}


if ($result['t_type_aff'] == 1)
{
    $comment .= '1 mur';
    echo '<div class="caseVue mur_' . $result['t_type_mur'] . '">';
}

if ($result['t_dist'] == 0)
{
    echo '<div class="oncase caseVue">';
}

if ($result['t_nb_perso'] != 0)
{
    $titre .= $result['t_nb_perso'] . ' aventuriers, ';
}
if ($result['t_nb_monstre'] != 0)
{
    $titre .= $result['t_nb_monstre'] . ' monstres.';
}

echo '<div id="dep' . $result['t_pos_cod'] . '" class="main caseVue" onClick="javascript:vue_clic(' . $result['t_pos_cod'] . ', ' . $result['t_dist'] . ');" title="' . $titre . '">';

if (($result['t_traj'] == 0) && ($result['t_type_aff'] != 1))
{
    echo '<div class="br caseVue">';
}
if ($result['t_traj'] == 1)
{
    echo '<div id="cell2' . $result['t_pos_cod'] . ' caseVue">';
}
if ($result['t_decor_dessus'] != 0)
{
    echo '<div class="caseVue decor' . $result['t_decor_dessus'] . '">';
}


if ($result['t_nb_perso'] != 0)
{
    $comment .= $result['t_nb_perso'] . ' aventurier(s), ';
    $detail  = 1;
    echo '<div class="joueur">';
    $titre .= $result['t_nb_perso'] . ' aventuriers, ';
}
if ($result['t_nb_monstre'] != 0)
{
    $comment .= $result['t_nb_monstre'] . ' monstre(s), ';
    $detail  = 1;
    echo '<div class="monstre">';
    $titre .= $result['t_nb_monstre'] . ' monstres.';
}
if ($aff_lock)
{
    if ($result['t_nb_lock'] != 0)
    {
        $detail = 1;
        echo '<div class="lock">';
    }
}
if ($result['t_nb_obj'] != 0)
{
    $comment .= $result['t_nb_obj'] . ' objet(s), ';
    $detail  = 1;
    echo '<div class="objet">';
    $isobjet = 1;
}
if ($result['t_or'] != 0)
{
    $comment .= $result['t_or'] . ' tas d’or, ';
    $detail  = 1;
    if ($isobjet == 0)
    {
        $isobjet = 1;
        echo '<div class="objet">';
    }
}
if ($result['t_type_bat'] != 0)
{
    $comment .= '1 lieu, ';
    echo '<div class="caseVue lieu' . $result['t_type_bat'] . '">';
}

echo '<div id="cell' . $result['t_pos_cod'] . '" class="pasvu caseVue" title="' . $titre . '">';
echo '<img src="' . G_IMAGES . 'del.gif" width="28" height="28" alt="' . $comment . '" />';
echo '</div>';

if ($result['t_type_bat'] != 0)
{
    echo '</div>';
}


if ($isobjet == 1)
{
    echo '</div>';
}
if ($aff_lock)
{
    if ($result['t_nb_lock'] != 0)
    {
        echo '</div>';
    }
}
if ($result['t_nb_monstre'] != 0)
{
    echo '</div>';
}

if ($result['t_nb_perso'] != 0)
{
    echo '</div>';
}


if ($result['t_decor_dessus'] != 0)
{
    echo '</div>';
}
if ($result['t_traj'] == 1)
{
    echo '</div>';
}
if (($result['t_traj'] == 0) && ($result['t_type_aff'] != 1))
{
    echo '</div>';
}
echo '</div>';
if ($result['t_dist'] == 0)
{
    echo '</div>';
}

if ($result['t_type_aff'] == 1)
{
    echo '</div>';
}


if ($result['t_decor'] != 0)
{
    echo '</div>';
}

if (!$terrain_chevauchable) echo '</div>';
