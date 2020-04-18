<!DOCTYPE html>
<html>
<?php
//test

$verif_connexion = new verif_connexion();
$verif_connexion->ident();
$verif_auth = $verif_connexion->verif_auth;
$compte     = $verif_connexion->compte;
$compt_cod  = $verif_connexion->compt_cod;
require_once "fonctions.php";
$pdo = new bddpdo();
//page_open(array("sess" => "My_Session", "auth" => "My_Auth"));


?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="https://www.jdr-delain.net//css/delain.css" rel="stylesheet">
<head>
    <title>Visu des derniers évènements</title>
</head>
<body background="images/fond5.gif">
<!-- nv -->
<div class="bordiv">
    <?php
    $erreur     = 0;
    $voir_tous  = get_request_var('voir_tous', 0);
    $visu_perso = get_request_var('visu_perso');


    if (!$compte->autoriseJouePerso($visu_perso))
    {    // Monstre
        echo "<p>Erreur ! Vous ne pouvez pas consulter les événements de ce perso !</p>";
        $erreur = 1;

    } elseif (isset($compt_cod) && $compt_cod != '' && $voir_tous == 1)
    {
    } else
    {    // Missing info
        $erreur = 1;
    }

    if ($erreur == 0)
    {
        $tableau_numeros = array();
        $tableau_noms    = array();
        if ($voir_tous == 1)
        {
            $req_persos = "select perso_cod, perso_nom
                from perso
                inner join perso_compte on pcompt_perso_cod = perso_cod
                where pcompt_compt_cod = :compte
                    and perso_actif = 'O'
                    and perso_type_perso = 1
                union all
                select perso_cod, perso_nom
                from perso, perso_compte, perso_familier
                where pcompt_compt_cod = :compte
                    and pcompt_perso_cod = pfam_perso_cod
                    and pfam_familier_cod = perso_cod
                    and perso_actif = 'O'
                    and perso_type_perso = 3
                order by perso_cod";
            $stmt       = $pdo->prepare($req_persos);
            $stmt       = $pdo->execute(array(":compte" => $compt_cod), $stmt);

            while ($result = $stmt->fetch())
            {
                $tableau_numeros[] = $result['perso_cod'];
                $tableau_noms[]    = $result['perso_nom'];
            }
        } else
        {
            $tableau_numeros[] = $visu_perso;
        }


        $premier_perso = true;

        foreach ($tableau_numeros as $key => $numero_perso)
        {
            if (!$premier_perso)
            {
                echo '<hr />';
            }


            if (isset($tableau_noms[$key]))
            {
                echo "<p><strong>Pour " . $tableau_noms[$key] . " :</strong></p>";
            }
            $levt = new ligne_evt();

            $allevt = $levt->getByPersoNonLu($numero_perso);


            if (count($allevt) != 0)
            {
                ?>
                <table>

                    <tr>
                        <td><p>Vos derniers événements importants :</p>
                            <p>
                                <?php
                                $allevtformat = $levt->mise_en_page_evt($allevt, true, true, false);

                                foreach ($allevt as $detailevt)
                                {
                                    printf(
                                        "%s : $texte_evt (%s).</br>",
                                        format_date($detailevt->levt_date),
                                        $detailevt->levt_texte
                                    );
                                } ?>
                        </td>
                    </tr>
                </table>
                <?php
            } else
            {
                echo "<p>Pas d’événements depuis votre dernière DLT</p>";
            }
        }
    }
    ?>
    <p/><a href="javascript:window.close();">Fermer cette fenêtre</a>
</div>

</body>
</html>
