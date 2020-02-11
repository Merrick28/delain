<?php
include "blocks/_header_page_jeu.php";
ob_start();
$perso = new perso;
$perso->charge($perso_cod);

$tab_position = $perso->get_position();


$num_etage  = $tab_position['etage']->etage_reference;
$pos_temple = $tab_position['pos']->pos_cod;
if ($num_etage < 0)
{
    $etage = abs($num_etage) + 1;
} else
{
    $etage = 5;
}
$sexe    = $perso->perso_sex;
$or      = $perso->perso_po;
$nb_mort = $perso->perso_nb_mort;


$prix = ($etage * $param->getparm(30)) + ($nb_mort * $param->getparm(31));

if ($or < $prix)
{
    echo "<p>Désolé $nom_sexe[$sexe], mais il semble que vous n'ayez pas assez de brouzoufs pour vous payer ce service.";
} else
{
    $req_or      = "update perso set perso_po = perso_po - $prix where perso_cod = $perso_cod";
    $stmt        = $pdo->query($req_or);
    $req_temple1 = "delete from perso_temple where ptemple_perso_cod = $perso_cod ";
    $stmt        = $pdo->query($req_temple1);
    $req_temple2 =
        "insert into perso_temple(ptemple_perso_cod,ptemple_pos_cod,ptemple_nombre) values ($perso_cod,$pos_temple,0)";
    $stmt        = $pdo->query($req_temple2);
    echo "<p>La guérisseuse note d'une écriture vive et précise votre nom et votre race sur un grand livre prévu à cet effet.";

}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";