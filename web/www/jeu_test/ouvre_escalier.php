<?php
include "blocks/_header_page_jeu.php";
ob_start();

$type_lieu = 3;
$nom_lieu  = 'un escalier';

define('APPEL', 1);
include "blocks/_test_lieu.php";
$perso = $verif_connexion->perso;

if ($erreur == 0)
{
    $nb_depose = 0;
    $req       = "select qparm_cod from quete_params where qparm_quete_cod = 5 and qparm_gobj_cod = 86 ";
    $stmt      = $pdo->query($req);
    if ($stmt->rowCount() != 0)
    {
        $nb_depose = $nb_depose + 1;
    }
    $req  = "select qparm_cod from quete_params where qparm_quete_cod = 5 and qparm_gobj_cod = 87 ";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() != 0)
    {
        $nb_depose = $nb_depose + 1;
    }
    $req  = "select qparm_cod from quete_params where qparm_quete_cod = 5 and qparm_gobj_cod = 88 ";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() != 0)
    {
        $nb_depose = $nb_depose + 1;
    }

    if ($nb_depose == 3)
    {
        //
        //$pdo->Begin();
        $req  =
            "delete from perso_objets where perobj_obj_cod in (select obj_cod from objets where obj_gobj_cod in (86,87,88)) ";
        $stmt = $pdo->query($req);
        $req  =
            "delete from objet_position where pobj_obj_cod in (select obj_cod from objets where obj_gobj_cod in (86,87,88)) ";
        $stmt = $pdo->query($req);
        $req  =
            "delete from perso_identifie_objet where pio_obj_cod in (select obj_cod from objets where obj_gobj_cod in (86,87,88)) ";
        $stmt = $pdo->query($req);
        $req  = "delete from objets where obj_gobj_cod in (86,87,88) ";
        $stmt = $pdo->query($req);
        // on commence par ouvrir l'escalier
        $req  =
            "update lieu set lieu_url = 'passage_escalier.php',lieu_dfin = (now() + '2 days'::interval) where lieu_cod in (184,186,187) ";
        $stmt = $pdo->query($req);
        // on recrée les monstres qui vont bien
        $req  = "select cree_monstre_hasard(58,1)";
        $stmt = $pdo->query($req);
        $req  = "select cree_monstre_hasard(59,3)";
        $stmt = $pdo->query($req);
        $req  = "select cree_monstre_hasard(60,2)";
        $stmt = $pdo->query($req);
        // on enlève les médaillons
        $req  = "delete from quete_params where qparm_quete_cod = 5 ";
        $stmt = $pdo->query($req);
        // on donne quelques PX
        $perso->perso_px = $perso->perso_px + 20;
        $perso->stocke();

        echo "<p>Vous avez ouvert les escaliers vers le -5. Ceux ci resteront ouverts pendant 48 heures avant de se refermer.<br>";
        echo "Vous gagnez 20 PX pour cette action !<br><br>";
        //$pdo->Commit();


    } else
    {
        echo "<p>Erreur ! Les 3 médaillons ne sont pas sur l'escalier !";
    }
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

