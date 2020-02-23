<?php
if (!defined("APPEL"))
    die("Erreur d'appel de page !");

$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
$param     = new parametres();
// on regarde si le joueur est bien sur une passage
$perso     = new perso;
$perso     = $verif_connexion->perso;
$type_lieu = 10;
$nom_lieu  = 'un passage';

define('APPEL', 1);
include "blocks/_test_lieu.php";

if ($erreur == 0)
{
    $tab_lieu  = $perso->get_lieu();
    $nom_lieu  = $tab_lieu['lieu']->lieu_nom;
    $desc_lieu = $tab_lieu['lieu']->lieu_description;
    $lieu_cod  = $tab_lieu['lieu']->lieu_cod;


    //FORMULE DU LIEU
    $req_formule = "select parm_valeur_texte from parametres where parm_desc = 'FORMULE_DU_LIEU_$lieu_cod'";
    $stmt        = $pdo->query($req_formule);
    if ($result = $stmt->fetch())
    {
        $formule = $result['parm_valeur_texte'];
    } else
    {
        $req_formule =
            "insert into parametres (parm_type,parm_desc,parm_valeur_texte) values('Text','FORMULE_DU_LIEU_$lieu_cod','DEFAULT')";
        $stmt        = $pdo->query($req_formule);
        $formule     = 'DEFAULT';
    }
    // DETERMINATION DE L'ETAT EN FONCTION DE LA FORMULE
    if ($formule != 'DEFAULT')
    {
        $etat  = "FERME";
        $etats = explode(";", $formule);
        for ($i = 0; $i < count($etats); $i++)
        {
            $etat_ref = $etats[$i];
            if ($etat_ref != "")
            {
                //ETAT DU LIEU
                $req_etat = "select parm_valeur_texte from parametres where parm_desc = '$etat_ref'";
                $stmt     = $pdo->query($req_etat);
                if ($result = $stmt->fetch())
                {
                    $etat = $result['parm_valeur_texte'];
                }
            }
        }

    } else
    {
        $etat = "FERME";
    }

    /* Ajout de prix différents, d'après l'étage où se trouve la passage payant.
        (Maverick, le 31/12/2010) */
    $ppos = $perso->get_position();
    switch ($ppos['pos']->pos_etage)
    {
        case '56' : // Le bayou de l'agonie
            $cout = 2000;
            break;

        default :
            $cout = $param->getparm(88);
            break;
    }
    /* Fin modif */
    $cout_pa = $param->getparm(13);

    // TRAITEMENT DES ACTIONS
    if (isset($_GET['methode']))
    {
        switch ($methode)
        {
            case 'passage_payant':
                $erreur = 0;

                /*On vérifie si le passage est ouvert*/
                if ($etat != "NEUTRE")
                {
                    echo "<p>Erreur ! la porte est fermée</p>";
                    $erreur = 1;
                }
                // CONTROLE: FAMILIER
                $req    = "select perso_type_perso from perso where perso_cod = $perso_cod ";
                $stmt   = $pdo->query($req);
                $result = $stmt->fetch();
                if ($result['perso_type_perso'] == 3)
                {
                    echo "<p>Erreur ! Un familier ne peut pas se déplacer seul !</p>";
                    $erreur = 1;
                }
                // CONTROLE: ARGENT DISPONIBLE
                $req_or = "select perso_po,perso_pa from perso where perso_cod = $perso_cod ";
                $stmt   = $pdo->query($req_or);
                $result = $stmt->fetch();
                $nb_or  = $result['perso_po'];
                if ($nb_or < $cout)
                {
                    $erreur = 1;
                    echo "<p>Vous n'avez pas assez d'argent dans votre bourse</p>";
                }
                // CONTROLE: PA DISPONIBLE
                $nb_pa = $result['perso_pa'];
                if ($nb_pa < $cout_pa)
                {
                    $erreur = 1;
                    echo "<p>Vous n'avez pas assez de pa pour réaliser ce déplacement.</p>";
                }
                // QUASI-COPIE DU CAS GRATUIT...
                if ($erreur == 0)
                {
                    // RETRAIT DE LA SOMME
                    $req_or = "update perso set perso_po = perso_po - $cout where perso_cod = $perso_cod ";
                    $stmt   = $pdo->query($req_or);
                    echo "<p>Vous payez $cout Br pour passer.</p>";
                    require "blocks/_passage_meca.php";
                }
                break;
            case 'passage':
                $erreur = 0;
                /*On vérifie si le passage est ouvert*/
                if ($etat != "OUVERT")
                {
                    echo "<p>Erreur ! la porte est fermée</p>";
                    $erreur = 1;
                }
                /* On se déplace */
                $req    = "select perso_type_perso from perso where perso_cod = $perso_cod ";
                $stmt   = $pdo->query($req);
                $result = $stmt->fetch();
                if ($result['perso_type_perso'] == 3)
                {
                    echo "<p>Erreur ! Un familier ne peut pas se déplacer seul !</p>";
                    $erreur = 1;
                }
                if ($erreur == 0)
                {
                    require "blocks/_passage_meca.php";
                }
                break;
        }
    } else
    {
        switch ($etat)
        {
            case "OUVERT":
                ?>
                <p><strong><?php echo $nom_lieu ?></strong> - <?php echo $desc_lieu ?></p>
                <p>Vous voyez une porte: elle est ouverte.</p>
                <p><a href="lieu.php?methode=passage">Prendre ce passage ! (<?php echo $cout_pa ?> PA)</a></p>
                <?php
                break;
            case "NEUTRE":
                ?>
                <p><strong><?php echo $nom_lieu ?></strong> - <?php echo $desc_lieu ?></p>
                <p>Vous voyez une porte: vous pouvez la franchir en payant un droit de passage:
                    <strong><?php echo $cout; ?> Br</strong>.</p>
                <p><a href="lieu.php?methode=passage_payant">Prendre ce passage ! (<?php echo $cout_pa ?> PA)</a></p>
                <?php
                break;
            case "FERME":
                ?>
                <p><strong><?php echo $nom_lieu ?></strong> - <?php echo $desc_lieu ?></p>
                <p>Vous voyez une porte: elle est fermée.</p>

                <?php
                break;
        }
    }
}
?>
