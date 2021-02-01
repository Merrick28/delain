<?php
$verif_connexion::verif_appel();

//
// à partir d'ici, tout devrait être bon.
//
$nb_tours = $niveau_actu * $niveau_actu;
// on commence par enlever les points au dieu
$req  = "update dieu set dieu_pouvoir = dieu_pouvoir - " . $points_ben . " where dieu_cod = " . $dieu_cod;
$stmt = $pdo->query($req);
// on enlève les PA

$param           = new parametres();
$perso->perso_pa = $perso->perso_pa - $param->getparm(110);
$perso->stocke();

// et maintenant, on va switcher par rapport à la divinité.
switch ($dieu_cod)
{
    case 1: // IO l'aveugle => chant du barde
        $req   = "select ajoute_bonus($perso_cod,'TOU', $nb_tours, 25)";
        $stmt  = $pdo->query($req);
        $req   = "select ajoute_bonus($perso_cod,'PAA', $nb_tours, -1)";
        $stmt  = $pdo->query($req);
        $req   = "select ajoute_bonus($perso_cod,'DEG', $nb_tours, 2)";
        $stmt  = $pdo->query($req);
        $texte = "Io vous a entendu, vous vous sentez plus habile au combat.";
        break;
    case 2: // Balgur => soins importants
        $req      = "select perso_pv,perso_pv_max from perso
							where perso_cod = " . $perso_cod;
        $stmt     = $pdo->query($req);
        $result   = $stmt->fetch();
        $v_pv     = $result['perso_pv'];
        $v_pv_max = $result['perso_pv_max'];
        $pv_gagne = 0;
        for ($i = 0; $i < 8; $i++)
        {
            $req      = "select lancer_des(1,4) as resultat";
            $stmt     = $pdo->query($req);
            $result   = $stmt->fetch();
            $pv_gagne += $result['resultat'];
        }
        $tot_pv = $v_pv + $pv_gagne;
        if ($tot_pv > $v_pv_max)
            $tot_pv = $v_pv_max;

        $perso->perso_pv = $tot_pv;
        $perso->stocke();

        $texte = "Balgur soigne vos blessures... ";
        break;
    case 3: // Galthée => reconstruction intense
        $req   = "select ajoute_bonus($perso_cod,'REG', $nb_tours, 10)";
        $stmt  = $pdo->query($req);
        $texte = "Dans sa grande bonté, Galthée aide votre corps à se reconstruire.";
        break;
    case 4: // Elian => sort de défense
        $req   = "select ajoute_bonus($perso_cod,'ARM', $nb_tours, 2)";
        $stmt  = $pdo->query($req);
        $texte = "Elian rend votre corps plus résistant aux blessures.";
        break;
    case 5: // Apiera => armure dure
        $req   = "select ajoute_bonus($perso_cod,'ARM', $nb_tours, 5)";
        $stmt  = $pdo->query($req);
        $texte = "Apiera rend votre corps plus résistant aux blessures.";
        break;
    case 7:    // FAlis => Fou furieux
        $req   = "select ajoute_bonus($perso_cod,'PAA', $nb_tours, -3)";
        $stmt  = $pdo->query($req);
        $texte = "Falis vous entend, et répond à votre demande de bénédiction. Vous vous sentez plus rapide au combat.";
        break;
    case 8: // Ecatis => Mange ta soupe
        $req   = "select ajoute_bonus($perso_cod,'TOU', $nb_tours, 25)";
        $stmt  = $pdo->query($req);
        $texte = "Ecatis vous a entendu, vous vous sentez plus habile au combat.";
        break;
    case 9: // Tonto => Danse de Saint Guy
        $req   = "select ajoute_bonus($perso_cod,'ESQ', $nb_tours, 25)";
        $stmt  = $pdo->query($req);
        $req   = "select ajoute_bonus($perso_cod,'DSG', $nb_tours, 20)";
        $stmt  = $pdo->query($req);
        $texte = "Tonto vous enseigne la technique de l'homme ivre.";
        break;
}
echo $texte;
