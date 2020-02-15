<?php
include "blocks/_header_page_jeu.php";
ob_start();
$erreur = 0;
$perso  = new perso;
$perso->charge($perso_cod);
if ($perso->is_milice() == 0)
{
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
$req                =
    "select pguilde_rang_cod from guilde_perso where pguilde_perso_cod = $perso_cod and pguilde_rang_cod = 3 ";
$stmt               = $pdo->query($req);
if ($stmt->rowCount() == 0)
{
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
if ($erreur == 0)
{
    $methode          = get_request_var('methode', 'debut');
    switch ($methode)
    {
        case "debut":
            echo "<p><a href=\"", $_SERVER['PHP_SELF'], "?methode=voir\">Voir les peines en cours ?</a><br>";
            echo "<p><a href=\"", $_SERVER['PHP_SELF'], "?methode=voirf\">Voir les peines faites ?</a><br>";
            echo "<p><a href=\"", $_SERVER['PHP_SELF'], "?methode=ajout\">Ajouter une peine ?</a><br>";
            break;
        case "voir":
            echo "<p class=\"titre\">Peines existantes </p>";
            $req  =
                "select peine_cod,acc.perso_cod as c_acc,acc.perso_nom as n_acc,mag.perso_cod as c_mag,mag.perso_nom as n_mag,peine_type,peine_duree,peine_faite,to_char(peine_date,'DD/MM/YYYY hh24:mi:ss') as date_peine ";
            $req  = $req . "from peine,perso acc,perso mag ";
            $req  = $req . "where peine_magistrat = mag.perso_cod ";
            $req  = $req . "and peine_perso_cod = acc.perso_cod ";
            $req  = $req . "and peine_faite < 2 ";
            $stmt = $pdo->query($req);
            if ($stmt->rowCount() == 0)
            {
                echo "<p>Aucune peine non effectuée en cours.";
            } else
            {
                $etat[0]  = "Non effectuée";
                $etat[1]  = "En cours";
                $peine[0] = "Peine de mort";
                $peine[1] = "Emprisonnement limité";
                $peine[2] = "Emprisonnement à pertpétuité";
                ?>
                <table>
                    <tr>
                        <td class="soustitre2"><strong>Dossier</strong></td>
                        <td class="soustitre2"><strong>Accusé</strong></td>
                        <td class="soustitre2"><strong>Peine</strong></td>
                        <td class="soustitre2"><strong>Validée par</strong></td>
                        <td class="soustitre2"><strong>Date de peine</strong></td>
                        <td class="soustitre2"><strong>Etat de la peine</strong></td>
                        <td></td>
                    </tr>
                    <?php
                    while ($result = $stmt->fetch())
                    {
                        $v_peine = $result['peine_type'];
                        $v_faite = $result['peine_faite'];
                        echo "<tr>";
                        echo "<td class=\"soustitre2\">", $result['peine_cod'], "</td>";
                        echo "<td class=\"soustitre2\"><a href=\"visu_desc_perso.php?visu=", $result['c_acc'], "\"><strong>", $result['n_acc'], "</strong></td>";
                        echo "<td>$peine[$v_peine]</td>";
                        echo "<td class=\"soustitre2\"><a href=\"visu_desc_perso.php?visu=", $result['c_mag'], "\"><strong>", $result['n_mag'], "</strong></td>";
                        echo "<td>", $result['date_peine'], "</td>";
                        echo "<td>$etat[$v_faite]</td>";
                        echo "<td><a href=\"", $_SERVER['PHP_SELF'], "?methode=suppr&peine=", $result['peine_cod'], "&perso=", $result['c_acc'], "\">Retirer la peine ?</a></td>";
                        echo "</tr>";
                    }

                    ?>
                </table>
                <?php
            }
            break;
        case "voirf":
            echo "<p class=\"titre\">Peines existantes </p>";
            $req  =
                "select peine_cod,to_char(peine_dexec,'DD/MM/YYYY') as dexec,acc.perso_cod as c_acc,acc.perso_nom as n_acc,mag.perso_cod as c_mag,mag.perso_nom as n_mag,peine_type,peine_duree,peine_faite,to_char(peine_date,'DD/MM/YYYY hh24:mi:ss') as date_peine ";
            $req  = $req . "from peine,perso acc,perso mag ";
            $req  = $req . "where peine_magistrat = mag.perso_cod ";
            $req  = $req . "and peine_perso_cod = acc.perso_cod ";
            $req  = $req . "and peine_faite = 2 ";
            $stmt = $pdo->query($req);
            if ($stmt->rowCount() == 0)
            {
                echo "<p>Aucune peine effectuée ";
            } else
            {
                $etat[0]  = "Non effectuée";
                $etat[1]  = "En cours";
                $peine[0] = "Peine de mort";
                $peine[1] = "Emprisonnement limité";
                $peine[2] = "Emprisonnement à pertpétuité";
                ?>
                <table>
                    <tr>
                        <td class="soustitre2"><strong>Dossier</strong></td>
                        <td class="soustitre2"><strong>Accusé</strong></td>
                        <td class="soustitre2"><strong>Peine</strong></td>
                        <td class="soustitre2"><strong>Validée par</strong></td>
                        <td class="soustitre2"><strong>Date de peine</strong></td>
                        <td class="soustitre2"><strong>Etat de la peine</strong></td>
                        <td class="soustitre2"><strong>Date d'éxécution</strong></td>
                        <td></td>
                    </tr>
                    <?php
                    while ($result = $stmt->fetch())
                    {
                        $v_peine = $result['peine_type'];
                        $v_faite = $result['peine_faite'];
                        echo "<tr>";
                        echo "<td class=\"soustitre2\">", $result['peine_cod'], "</td>";
                        echo "<td class=\"soustitre2\"><a href=\"visu_desc_perso.php?visu=", $result['c_acc'], "\"><strong>", $result['n_acc'], "</strong></td>";
                        echo "<td>$peine[$v_peine]</td>";
                        echo "<td class=\"soustitre2\"><a href=\"visu_desc_perso.php?visu=", $result['c_mag'], "\"><strong>", $result['n_mag'], "</strong></td>";
                        echo "<td>", $result['date_peine'], "</td>";
                        echo "<td>$etat[$v_faite]</td>";
                        echo "<td>", $result['dexec'], "</td>";
                        echo "<td><a href=\"", $_SERVER['PHP_SELF'], "?methode=suppr&peine=", $result['peine_cod'], "&perso=", $result['c_acc'], "\">Retirer la peine ?</a></td>";
                        echo "</tr>";
                    }

                    ?>
                </table>
                <?php
            }
            break;
        case "ajout":
            ?>
            <p class="titre">Ajout d'une peine</p>
            <form name="ajout" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="methode" value="ajout2">
                <p>Etape 1 : choisissez le nom de l'heureux élu :
                    <input type="text" name="nom">
                    <input type="submit" class="test" value="Rechercher !">
            </form>
            <?php
            break;
        case "ajout2":
            $erreur = 0;
            $req    = "select f_cherche_perso('$nom') as resultat ";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            if ($result['resultat'] == -1)
            {
                echo "<p>Erreur ! Perso non trouvé !";
                $erreur = 1;
            }
            $perso = $result['resultat'];
            $req   = "select peine_cod from peine where peine_perso_cod = $perso and peine_faite < 2";
            $stmt  = $pdo->query($req);
            if ($stmt->rowCount() != 0)
            {
                echo "<p>Erreur ! Le perso ciblé est déjà sous le coup d'une peine !<br>
					Si vous voulez rajouter une peine à ce perso, vous devez supprimer la peine existante.";
                $erreur = 1;
            }
            if ($erreur == 0)
            {
                ?>
                <p class="titre">Ajout d'une peine </p>
                <form name="ajout" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="methode" value="ajout3">
                    <input type="hidden" name="perso" value="<?php echo $perso; ?>">
                    <p>Etape 2 : choisissez un type de peine :
                        <select name="type">
                            <option value="0">Peine de mort</option>
                            <option value="1">Emprisonnement limité</option>
                            <option value="2">Emprisonnement à perpétuité</option>
                        </select>
                        ainsi qu'une durée éventuelle (en heures) :
                        <input type="text" name="duree" value="0">
                        <input type="submit" class="test" value="Valider !">
                </form>
                <?php
            }
            break;
        case "ajout3":
            $erreur = 0;
            $type   = $_REQUEST['type'];
            $duree  = $_REQUEST['duree'];
            if (($type == 1) && ($duree == ""))
            {
                echo "<p>Erreur ! Si vous choisissez un emprisonnement limité, vous devez mettre une durée !";
                echo "<a href=\"", $_SERVER['PHP_SELF'], "?methode=ajout2&perso=$perso\">Retour</a><br>";
                $erreur = 1;
            }
            if ($erreur == 0)
            {
                $req  = "insert into peine (peine_magistrat,peine_perso_cod,peine_type,peine_duree) 
                values ($perso_cod,$perso,$type,$duree) ";
                $stmt = $pdo->query($req);
                echo "<p>La peine a bien été enregistrée.";
                $req         = "select peine_cod,peine_type,perso_nom from peine,perso where peine_perso_cod = $perso 
                and perso_cod = $perso_cod ";
                $stmt        = $pdo->query($req);
                $result      = $stmt->fetch();
                $peine[0]    = "Peine de mort";
                $peine[1]    = "Emprisonnement limité";
                $peine[2]    = "Emprisonnement à pertpétuité";
                $titre       = "Condamnation.";
                $v_peine     = $result['peine_type'];
                $v_faite     = $result['peine_faite'];
                $texte       =
                    "Le joueur " . $result['perso_nom'] . ", en tant que Magistrat de la Milice d'Hormandre III, a émis une condamnation contre vous.<br />";
                $texte       =
                    $texte . "La condamnation est : <strong>" . $peine[$v_peine] . "</strong> et est enrgistrée sous le dossier <strong>" . $result['peine_cod'] . "</strong>.";
                $texte       = str_replace("'", "\'", $texte);
                $req_num_mes = "select nextval('seq_msg_cod') as num_mes";
                $stmt        = $pdo->query($req_num_mes);
                $result      = $stmt->fetch();
                $num_mes     = $result['num_mes'];
                $req_mes     = "insert into messages (msg_cod,msg_date,msg_titre,msg_corps,msg_date2) 
                values ($num_mes, now(), '$titre', '$texte', now()) ";
                $stmt        = $pdo->query($req_mes);
                // on renseigne l'expéditeur
                $req      = "insert into messages_exp (emsg_msg_cod,emsg_perso_cod,emsg_archive)
                values ($num_mes,$perso_cod,'N') ";
                $stmt     = $pdo->query($req);
                $req_dest =
                    "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) values ($num_mes,$perso,'N','N') ";
                $stmt     = $pdo->query($req_dest);
            }
            break;
        case "suppr":
            $req  = "delete from peine where peine_cod = $peine ";
            $stmt = $pdo->query($req);
            echo "<p>La peine a été retirée.";
            $titre       = "Suppression de peine.";
            $req         = "select perso_nom from perso where perso_cod = $perso_cod ";
            $stmt        = $pdo->query($req);
            $result      = $stmt->fetch();
            $texte       =
                "Le joueur " . $result['perso_nom'] . ", en tant que Magistrat de la Milice d'Hormandre III, a levé la peine qui était émise contre vous.<br />";
            $texte       = str_replace("'", "\'", $texte);
            $req_num_mes = "select nextval('seq_msg_cod') as num_mes";
            $stmt        = $pdo->query($req_num_mes);
            $result      = $stmt->fetch();
            $num_mes     = $result['num_mes'];
            $req_mes     = "insert into messages (msg_cod,msg_date,msg_titre,msg_corps,msg_date2)
            values ($num_mes, now(), '$titre', '$texte', now()) ";
            $stmt        = $pdo->query($req_mes);
            // on renseigne l'expéditeur
            $req      = "insert into messages_exp (emsg_msg_cod,emsg_perso_cod,emsg_archive) 
            values ($num_mes,$perso_cod,'N') ";
            $stmt     = $pdo->query($req);
            $req_dest =
                "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) values ($num_mes,$perso,'N','N') ";
            $stmt     = $pdo->query($req_dest);
            break;


    }
    echo "<hr><a href=\"", $_SERVER['PHP_SELF'], "\">Retour à la gestion des peines</a><br>";
    echo "<a href=\"milice.php\">Retour à la page milice</a><br>";


}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

