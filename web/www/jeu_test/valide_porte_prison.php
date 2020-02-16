<?php
include "blocks/_header_page_jeu.php";
ob_start();


$perso = new perso;
$perso = $verif_connexion->perso;

// on regarde si le joueur est bien sur une banque
$erreur = 0;
if (!$perso->is_lieu())
{
    echo("<p>Erreur ! Vous n'êtes pas sur un escalier !!!");
    $erreur = 1;
}
if ($erreur == 0) {
    $tab_lieu = $perso->get_lieu();
    if ($tab_lieu['lieu']->lieu_tlieu_cod != 2139)
    {
        $erreur = 1;
        echo("<p>Erreur ! Vous n'êtes pas sur un escalier !!!");
    }
}
if ($erreur == 0) {
    $req    = "select lpos_lieu_cod from lieu_position where lpos_pos_cod =  
    (select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod) ";
    $stmt   = $pdo->query($req);
    $result = $stmt->fetch();
    $lieu   = $result['lpos_lieu_cod'];
    $req    = "select pge_perso_cod from perso_grand_escalier where pge_perso_cod = $perso_cod ";
    $req    = $req . "and pge_lieu_cod = $lieu ";
    $stmt   = $pdo->query($req);
    if ($stmt->rowCount() == 0)
    {
        echo "<p>Erreur ! Vous ne pouvez pas utiliser ce passage !";
    } else
    {


        $pa_perso = $perso->perso_pa;
        if ($pa_perso < 4) {
            echo("<p>Vous n'avez pas assez de PA !!!!");
        } else {
            $pa = "update perso set perso_pa = perso_pa - 4 where perso_cod = $perso_cod ";
            $stmt = $pdo->query($pa);

            $req_pos = "select ppos_pos_cod,pos_x,pos_y,pos_etage from perso_position,positions where ppos_perso_cod = $perso_cod and ppos_pos_cod = pos_cod ";
            $stmt = $pdo->query($req_pos);
            $result = $stmt->fetch();

            $pos_actuelle = $result['ppos_pos_cod'];

            $req = "select lieu_dest from lieu,lieu_position ";
            $req = $req . "where lpos_pos_cod = $pos_actuelle ";
            $req = $req . "and lpos_lieu_cod = lieu_cod ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            $n_pos = $result['lieu_dest'];

            $req = "select pos_etage from positions where pos_cod = $n_pos ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            $n_etage = $result['pos_etage'];


            $req = "select update_etage_visite($perso_cod,$n_etage)";
            $stmt = $pdo->query($req);


            $deplace = "update perso_position set ppos_pos_cod = $n_pos where ppos_perso_cod = $perso_cod ";
            $stmt = $pdo->query($deplace);


            // on efface pour le retour
            $req = "delete from perso_grand_escalier where pge_perso_cod = $perso_cod ";
            $req = $req . "and pge_lieu_cod = 2139 ";
            $stmt = $pdo->query($req);

            $req = "select etage_libelle,etage_description from etage,positions where pos_cod = $n_pos and pos_etage = etage_numero ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            echo "<p>Vous arrivez dans le lieu : <strong>" . $result['etage_libelle'] . "</strong><br>";
            echo "<p><em>" . $result['etage_description'] . "</em>";

            // on remet l'ancien temple si besoin
            $req = "select ptemple_anc_pos_cod from perso_temple where ptemple_perso_cod = $perso_cod ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            if ($result['ptemple_anc_pos_cod'] == 0) {
                $req = "delete from perso_temple where ptemple_perso_cod = $perso_cod ";
            } else {
                $req = "update perso_temple set ptemple_pos_cod = ptemple_anc_pos_cod,ptemple_nombre = ptemple_anc_nombre ";
                $req = $req . "where ptemple_perso_cod = $perso_cod ";
            }
            $stmt = $pdo->query($req);

        }
    }

}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

