<!DOCTYPE html>
<html>
<?php
//test

require "ident.php";
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
    $erreur = 0;
    if (((isset($_REQUEST['visu_perso']) && $_REQUEST['visu_perso'] != '' &&
          isset($compt_cod) && $compt_cod != '')) && !isset($_REQUEST['voir_tous'])) {
        $req  = "select pcompt_compt_cod
            from perso_compte
            where pcompt_perso_cod = :perso
            or pcompt_perso_cod =
                  ( select pfam_perso_cod from perso_familier where pfam_familier_cod = :perso  )";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
                                  ":perso" => $_REQUEST['visu_perso']
                              ), $stmt);

        if (!$result = $stmt->fetch()) {    // Monstre
            echo "<p>Erreur ! Vous ne pouvez pas consulter les événements de ce perso !</p>";
            $erreur = 1;
        } elseif ($result['pcompt_compt_cod'] != $compt_cod) {  // Sitté ?
            $sitte = $result['pcompt_compt_cod'];
            // Test sitting.
            $req  = 'select csit_compte_sitteur from compte_sitting where
                csit_compte_sitteur = :compte and
                csit_compte_sitte = :sitte and csit_ddeb < now() and
                csit_dfin > now()';
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":compte" => $compt_cod,
                                        ":sitte"  => $sitte), $stmt);

            if (!$stmt->fetch()) {  // Pas un cas de sitting.
                echo "<p>Erreur ! Vous ne pouvez pas consulter les événements de ce perso !</p>";
                $erreur = 1;
            }
        }
    } elseif (isset($compt_cod) && $compt_cod != '' && isset($_REQUEST['voir_tous']) && $_REQUEST['voir_tous'] == 1) {
    } else {    // Missing info
        $erreur = 1;
    }

    if ($erreur == 0) {
        $tableau_numeros = array();
        $tableau_noms    = array();
        if (isset($voir_tous) && $voir_tous == 1) {
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

            while ($result = $stmt->fetch()) {
                $tableau_numeros[] = $result['perso_cod'];
                $tableau_noms[]    = $result['perso_nom'];
            }
        } else {
            $tableau_numeros[] = $visu_perso;
        }


        $premier_perso = true;

        foreach ($tableau_numeros as $key => $numero_perso) {
            if (!$premier_perso) {
                echo '<hr />';
            }


            if (isset($tableau_noms[$key])) {
                echo "<p><strong>Pour " . $tableau_noms[$key] . " :</strong></p>";
            }
            $levt   = new ligne_evt();
            $allevt = $levt->getByPersoNonLu($numero_perso);


            if (count($allevt) != 0) {
                ?>
                <table>

                    <tr>
                        <td><p>Vos derniers événements importants :</p>
                            <p>
                                <?php
                                $allevtformat = $levt->mise_en_page_evt($allevt, true, true, false);

                foreach ($allevt as $detailevt) {
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
            } else {
                echo "<p>Pas d’événements depuis votre dernière DLT</p>";
            }
        }
    }
    ?>
    <p/><a href="javascript:window.close();">Fermer cette fenêtre</a>
</div>

</body>
</html>
