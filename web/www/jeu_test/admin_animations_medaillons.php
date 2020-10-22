<?php
$verif_connexion = new verif_connexion();
$verif_connexion::verif_appel();

$pdo = new bddpdo();

echo '<div class="bordiv" style="padding:0; margin-left: 205px;">';
echo '<div class="barrTitle">Quête des Médaillons</div><br />';
$methode = $_REQUEST['methode'];
switch ($methode)
{
    case 'medaillon_redistribution':    // Redonne le médaillon à un monstre aléatoire de son antre
        $objet = new objets();
        $objet->charge($_REQUEST['obj_cod']);
        $antre = -4;
        if ($objet->obj_gobj_cod == 86) // loup
        {
            $antre = 1;
        }
        if ($objet->obj_gobj_cod == 87) // scorpion
        {
            $antre = 3;
        }
        if ($objet->obj_gobj_cod == 88) // serpent
        {
            $antre = 2;
        }

        $nom_medaillon     = $objet->obj_nom;
        $req_choix_monstre = "select perso_cod from perso 
            inner join perso_position on ppos_perso_cod = perso_cod 
            inner join positions on pos_cod = ppos_pos_cod 
            where perso_gmon_cod IN (16, 37, 40, 41, 42, 43) AND pos_etage=:antre AND perso_actif='O' order by random() limit 1;";
        $stmt              = $pdo->prepare($req_choix_monstre);
        $stmt              = $pdo->execute(array(":antre" => $antre), $stmt);
        $result            = $stmt->fetch();
        $monstre_cod       = $result['perso_cod'];
        $obj_cod           = $_REQUEST['obj_cod'];
        if ($monstre_cod > 0 && $antre > 0)
        {
            $req_enleve = "delete from perso_objets where perobj_obj_cod = :objet";
            $stmt       = $pdo->prepare($req_enleve);
            $stmt       = $pdo->execute(array(":objet" => $obj_cod), $stmt);
            $req_enleve = "delete from objet_position where pobj_obj_cod = :objet";
            $stmt       = $pdo->prepare($req_enleve);
            $stmt       = $pdo->execute(array(":objet" => $obj_cod), $stmt);
            $req_donne  =
                "insert into perso_objets(perobj_perso_cod, perobj_obj_cod, perobj_identifie, perobj_equipe) VALUES 
                    (:monstre, :objet, 'O', 'N')";
            $stmt       = $pdo->prepare($req_donne);
            $stmt       = $pdo->execute(array(":monstre" => $monstre_cod,
                                              ":objet"   => $obj_cod), $stmt);
        }
        echo '<p>Redistribution effectuée pour le médaillon ' . $nom_medaillon . '</p>';

        $log = date("d/m/y - H:i") . "\tCompte $compt_cod a redistribué le médaillon $nom_medaillon.\n";
        writelog($log, 'admin_animations_medaillons');
        break;
}
require "blocks/_admin_medaillons.php";
