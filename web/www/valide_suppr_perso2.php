<?php
include "jeu_test/verif_connexion.php";

require_once G_CHE . "/includes/base_delain.php";

$perso_cible = $_REQUEST['perso'];
$pdo = new bddpdo();
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="https://www.jdr-delain.net//css/delain.css" rel="stylesheet">
<head>
    <title>Suppression de perso</title>
</head>
<body background="../images/fond5.gif">
<div class="bordiv">
    <?php
    $erreur = 0;
    $req = 'select (perso_dcreat + \'24 hours\'::interval) > now() as test, perso_type_perso from perso where perso_cod = :perso';
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array(":perso" => $perso_cible),$stmt);
    if(!$result = $stmt->fetch())
    {
        $erreur = 1;
    }



    if ($result['test'] == 't') {
        $erreur = 1;
    }
    $perso_a_effacer = new perso;
    $perso_a_effacer->charge($perso_cible);
    $type_perso = $perso_a_effacer->perso_type_perso;

    if ($type_perso == 3) {
        $req = "select pcompt_compt_cod from perso_familier,perso_compte where pfam_familier_cod = $perso_cible and pfam_perso_cod = pcompt_perso_cod";
        $db->query($req);
        $db->next_record();
        $compt_fam = $db->f("pcompt_compt_cod");
    } else
        $compt_fam = -1;

    $req = "select pcompt_perso_cod, perso_type_perso from perso_compte, perso where pcompt_perso_cod = $perso_cible and pcompt_compt_cod = $compt_cod and pcompt_perso_cod = perso_cod";
    $db->query($req);
    if ($db->nf() == 0 and $type_perso != 3) // Non rattaché au compte, pas un familier.
    {
        echo "Vous êtes en train de tenter de supprimer un personnage qui n’est pas rattaché à votre compte !";
    } else if ($type_perso == 3 and $compt_cod != $compt_fam) // Un familier, mais rattaché à un perso d’un autre compte
    {
        echo "Vous êtes en train de tenter de supprimer un familier qui n’est pas rattaché à l’un de vos persos !";
    } else if ($erreur == 1)  // Mois de 24 heures depuis la création du perso
    {
        echo 'Allons allons...  Cet aventurier a moins de 24 heures d’existence, ',
        'laissez-le donc vivre un peu plus...';
    } else if ($type_perso == 2) // Cas d’un monstre contrôlé par le compte. Pas de suppression, juste un détachement.
    {
        $req1 = "select case when (now() - pcompt_date_attachement) < '1 day'::interval then 1 else 0 end as trop_recent, perso_actif
		from perso
		inner join perso_compte on pcompt_perso_cod = perso_cod
		where perso_cod = $perso_cible";
        $db->query($req1);
        $db->next_record();
        $trop_recent = $db->f('trop_recent');
        $actif = $db->f('perso_actif');
        if ($trop_recent == 1 && $actif == 'O') {
            echo '<p>Ce monstre vous a été affecté trop récemment, gardez-le au moins 24 heures !</p>';
        } else {
            $req1 = "select relache_monstre_4e_perso($perso_cible, 2::smallint) as resultat";
            $db->query($req1);
            $db->next_record();
            echo '<p>' . $db->f("resultat") . '</p>';
        }
    } else {
        $req1 = "update perso set perso_nom = perso_nom||'_inactif' where perso_cod = $perso_cible ";
        $db->query($req1);
        $req1 = "delete from lock_combat where lock_cible = $perso_cible ";
        $db->query($req1);
        $req1 = "delete from lock_combat where lock_attaquant = $perso_cible ";
        $db->query($req1);
        $req1 = "delete from perso_familier where pfam_familier_cod = $perso_cible ";
        $db->query($req1);
        $req1 = "select pfam_familier_cod from perso_familier where pfam_perso_cod = $perso_cible ";
        $db->query($req1);
        if ($db->nf() != 0) {
            $req = "update perso set perso_type_perso = 2 where perso_cod in ";
            $req = $req . "(select pfam_familier_cod from perso_familier where pfam_perso_cod = $perso_cible) ";
            $db->query($req);
        }

        $req = "insert into ligne_evt (levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible) ";
        $req = $req . "values (23,now(),1,$perso_cible,'Le personnage a été supprimé !','O','O') ";
        $db->query($req);

        $req2 = "update perso set perso_actif = 'N' where perso_cod = $perso_cible ";
        $db->query($req2);
        echo("<p>Votre perso a été supprimé !");
    }
    echo "<a class='centrer' href=\"jeu_test/switch.php\">Retour !</a>";

    ?>
</div>
</body>
</html>
