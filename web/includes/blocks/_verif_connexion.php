<?php
$type_perso       = 'joueur';
$is_admin_monstre = false;
$is_admin         = false;
if ($compte->compt_monstre == 'O')
{
    $type_perso       = 'monstre';
    $is_admin_monstre = true;
}
if ($compte->compt_admin == 'O')
{
    $type_perso = 'admin';
    $is_admin   = true;
}