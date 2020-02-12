<?php
include "blocks/_header_page_jeu.php";
ob_start();

$type_lieu = 3;
$nom_lieu  = 'un escalier';

include "blocks/_test_lieu.php";

$perso = new perso;
$perso->charge($perso_cod);

if ($erreur == 0)
{
    if ($perso->compte_objet($objet) != 0)
    {
        // on enlève de l'inventaire
        $req  =
            "delete from perso_objets where perobj_perso_cod = $perso_cod and perobj_obj_cod in (select obj_cod from objets where obj_gobj_cod =  $objet) ";
        $stmt = $pdo->query($req);
        // on détruit l'objet
        $req  =
            "delete from perso_identifie_objet where pio_obj_cod in (select obj_cod from objets where obj_gobj_cod =  $objet) ";
        $stmt = $pdo->query($req);
        $req  = "delete from objets where obj_cod in (select obj_cod from objets where obj_gobj_cod =  $objet) ";
        $stmt = $pdo->query($req);
        // on la rajoute à la quête
        $req  = "insert into quete_params (qparm_quete_cod,qparm_gobj_cod) values (5,$objet) ";
        $stmt = $pdo->query($req);
        echo "<p>Vous avez déposé le médaillon à sa place. Il est impossible de l'en retirer maintenant.";


    } else
    {
        echo "<p>Erreur ! L'objet n'est pas dans voter inventaire !";
    }

}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
