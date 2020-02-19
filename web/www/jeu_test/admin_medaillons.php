<?php
if (!defined("APPEL"))
    die("Erreur d'appel de page !");

echo '<div class="bordiv" style="padding:0; margin-left: 205px; max-height:20px; overflow:hidden;" id="cadre_medaillons">';
echo '<div class="barrTitle" onclick="permutte_cadre(this.parentNode);">Quête des Médaillons</div><br />';
$methode = $_REQUEST['methode'];
switch ($methode)
{
    case 'medaillon_redistribution':    // Redonne le médaillon à un monstre aléatoire de son antre
        $req_verif = "select obj_gobj_cod, obj_nom from objets where obj_cod = $obj_cod";
        $stmt      = $pdo->query($req_verif);
        $result    = $stmt->fetch();
        $antre     = -4;
        if ($result['obj_gobj_cod'] == 86) // loup
            $antre = 1;
        if ($result['obj_gobj_cod'] == 87) // scorpion
            $antre = 3;
        if ($result['obj_gobj_cod'] == 88) // serpent
            $antre = 2;
        $nom_medaillon     = $result['obj_nom'];
        $req_choix_monstre = "select perso_cod from perso " .
                             "inner join perso_position on ppos_perso_cod = perso_cod " .
                             "inner join positions on pos_cod = ppos_pos_cod " .
                             "where perso_gmon_cod IN (16, 37, 40, 41, 42, 43) AND pos_etage=$antre AND perso_actif='O' order by random() limit 1;";
        $stmt              = $pdo->query($req_choix_monstre);
        $result            = $stmt->fetch();
        $monstre_cod       = $result['perso_cod'];

        if ($monstre_cod > 0 && $antre > 0)
        {
            $req_enleve = "delete from perso_objets where perobj_obj_cod = $obj_cod";
            $stmt       = $pdo->query($req_enleve);
            $req_enleve = "delete from objet_position where pobj_obj_cod = $obj_cod";
            $stmt       = $pdo->query($req_enleve);
            $req_donne  =
                'insert into perso_objets(perobj_perso_cod, perobj_obj_cod, perobj_identifie, perobj_equipe) VALUES ' .
                "($monstre_cod, $obj_cod, 'O', 'N')";
            $stmt       = $pdo->query($req_donne);
        }
        echo '<p>Redistribution effectuée pour le médaillon ' . $nom_medaillon . '</p>';
        break;
}
require "blocks/_admin_medaillons.php";
