<?php
$type_lieu = 1;
$nom_lieu  = 'une banque';

define('APPEL', 1);
include "blocks/_test_lieu.php";
$perso     = $verif_connexion->perso;
$perso_cod = $verif_connexion->perso_cod;

if ($erreur == 0)
{
    // INFOS GUILDE

    $gp = new guilde_perso();
    if ($gp->get_by_perso($perso_cod))
    {
        $guilde = new guilde();
        $guilde->charge($gp->pguilde_guilde_cod);
        $gr = new guilde_rang();
        $gr->get_by_guilde_rang($guilde->guilde_cod, $gp->pguilde_rang_cod);
        $adm        = $gr->rguilde_admin;
        $guilde_cod = $guilde->guilde_cod;
        $guilde_nom = $guilde->guilde_nom;
    }

    // INFOS ETAGE
    $pos                         = $perso->get_position();
    $numero                      = -1 * $pos['pos']->pos_etage;
    if ($numero < 0)
    {
        $numero = 5;
    }
    if ($numero > 5)
    {
        $numero = 5;
    }

    echo("<img src=\"../images/banque3.png\"><br />");


    if (isset($_POST['methode']))
    {
        switch ($methode)
        {
            case "depot":
                // AFFICHAGE: FORMULAIRE POUR UN DEPOT SUR COMPTE PERSO
                ?>
                <form name="val_depot" method="post" action="lieu.php">
                    <input type="hidden" name="methode" value="valider_depot">
                    <p>Déposer <input type="text" name="quantite"> brouzoufs sur mon compte.</p>
                    <p><input type="submit" value="Valider !" class="test centrer"></p>
                </form>
                <?php
                break;
            case "retrait":
                // AFFICHAGE: FORMULAIRE POUR UN RETRAIT SUR COMPTE PERSO
                ?>
                <form name="val_retrait" method="post" action="lieu.php">
                    <input type="hidden" name="methode" value="valider_retrait">
                    <p>Retirer <input type="text" name="quantite"> brouzoufs de mon compte.</p>
                    <p><input type="submit" value="Valider !" class="test centrer"></p>
                </form>
                <?php
                break;
            case "view_depot_guilde":
                // AFFICHAGE: FORMULAIRE POUR UN DEPOT SUR COMPTE GUILDE
                ?>
                <form name="dep_compt_guilde" method="post" action="lieu.php">
                    <input type="hidden" name="methode" value="depot_compte_guilde">
                    <p>Verser <input type="text" name="quantite" value="0"
                                     onChange="quantite_2.value=Math.ceil(0.0<?php echo $numero; ?>*quantite.value);quantite_3.value=Math.floor(0.9<?php echo(10 - $numero); ?>*quantite.value);">
                        Br sur le compte:
                        <select name="depot_compt_cod">
                            <option value="-1">-- SELECTIONNER UN COMPTE DE GUILDE --</option>
                            <?php
                            $req_compte_guilde =
                                "select gbank_cod,gbank_nom,guilde_nom from guilde_banque,guilde where gbank_guilde_cod = guilde_cod order by guilde_nom";
                            $stmt              = $pdo->query($req_compte_guilde);
                            while ($result = $stmt->fetch())
                            {
                                echo "<option value=\"", $result['gbank_cod'], "\">", $result['gbank_nom'], " (", $result['guilde_nom'], ")</option>";
                            }
                            ?>
                        </select>
                    </p>
                    <p><em>(une taxe de <?php echo $numero; ?> % est prélevée pour chaque dépot à cet étage
                            correspondant à : <input type="text" name="quantite_2" value="0" disabled> Br.
                            <br><input type="text" name="quantite_3" value="0" disabled> Br seront effectivement virés
                            sur le compte de guilde). Attention, la banque applique son pourcentage toujours en premier
                            !</em></p>
                    <p><input type="submit" value="Valider !" class="test centrer"></p>
                </form>
                <?php
                break;
            case "view_retrait_guilde":
                // AFFICHAGE: FORMULAIRE POUR UN RETRAIT SUR COMPTE GUILDE
                ?>
                <form name="val_retrait" method="post" action="lieu.php">
                    <input type="hidden" name="methode" value="retrait_compte_guilde">
                    <p>Retirer <input type="text" name="quantite"> brouzoufs du compte de ma guilde.</p>
                    <p><input type="submit" value="Valider !" class="test centrer"></p>
                </form>
                <?php
                break;
            case "valider_depot":
                $quantite = get_request_var('quantite');
                if ($quantite < 0)
                {
                    echo("<p>Bien tenté ...");
                    break;
                }
                // TRAITEMENT: UN PERSONNAGE FAIT UN DEPOT SUR SON COMPTE PERSONNEL
                $req_depot = "select depot_banque(:perso_cod,:quantite) as depot";
                $stmt      = $pdo->prepare($req_depot);
                $stmt      = $pdo->execute(array(":perso_cod" => $perso_cod,
                                                 ":quantite" => $quantite), $stmt);
                $result    = $stmt->fetch();
                //$tab_depot = pg_fetch_array($res_depot,0);
                if ($result['depot'] == 0)
                {
                    echo("<p>Vous venez de déposer <strong>$quantite</strong> brouzoufs sur votre compte en banque.");
                } else
                {
                    printf("<p>Une anomalie est survenue : <strong>%s</strong>", $result['depot']);
                }
                break;
            case "valider_retrait" :
                // TRAITEMENT: UN PERSONNAGE FAIT UN RETRAIT SUR SON COMPTE PERSONNEL
                if ($quantite <= 0)
                {
                    $erreur = 1;
                    ?>
                    <p>Vous ne pouvez pas retirer une somme inférieure ou égale à 0 !
                    <?php
                }
                if ($erreur == 0)
                {
                    $req_depot = "select retrait_banque(:perso_cod,:quantite) as retrait";
                    $stmt      = $pdo->prepare($req_depot);
                    $stmt      = $pdo->execute(array(":perso_cod" => $perso_cod,
                                                     ":quantite"  => $quantite), $stmt);
                    $result    = $stmt->fetch();
                    $tab_depot = $result['retrait'];
                    if ($tab_depot == 0)
                    {
                        ?>
                        <p>Vous venez de retirer <strong><?php echo $quantite; ?></strong> brouzoufs de votre compte en
                            banque.</p>
                        <?php
                    } else
                    {
                        ?>
                        <p>Une anomalie est survenue : <strong><?php echo $tab_depot ?></strong></p>
                        <?php
                    }
                }
                break;
            case "creer_compte_guilde":
                // TRAITEMENT: UN ADMIN CREE UN COMPTE POUR SA GUILDE
                if (($nb_guilde > 0) and ($adm == "O"))
                {
                    $gbank      = new guilde_banque();
                    $guilde_cod = get_request_var('guilde_cod');
                    // CONTROLE: COMPTE NON EXISTANT
                    if ($gbank->getByGuilde($guilde_cod))
                    {
                        $erreur = 1;
                        $info   = "Votre guilde dispose déjà d'un compte";
                    }
                    // CONTROLE: ARGENT DISPONIBLE
                    $nb_or = $perso->perso_po;
                    if ($nb_or < 5000)
                    {
                        $erreur = 1;
                        $info   = "Vous n'avez pas assez d'argent dans votre bourse";
                    }
                    if ($erreur == 0)
                    {
                        // RETRAIT DE LA SOMME
                        $perso->perso_po = $perso->perso_po - 5000;
                        $perso->stocke();
                        // CREATION DU COMPTE

                        $gbank->gbank_nom           = $_REQUEST['compte_nom'];
                        $gbank->gbank_or            = 0;
                        $gbank->gbank_date_creation = date('Y-m-d H:i:s');
                        $gbank->gbank_guilde_cod    = $guilde_cod;
                        $gbank->stocke(true);

                        ?>
                        <p>Le compte a bien été créé</p>
                        <?php
                    } else
                    {
                        ?>
                        <p>Une anomalie est survenue : <strong><?php echo $info ?> </strong></p>
                        <?php
                    }

                } else
                {
                    ?>
                    <p>Vous n'êtes pas administrateur d'une guilde</p>
                    <?php
                }
                break;
            case "depot_compte_guilde":
                // TRAITEMENT: UN PERSONNAGE FAIT UN DEPOT SUR LE COMPTE D'UNE GUILDE
                $quantite = intval(get_request_var('quantite', 0));
                $depot_guilde    = floor((1.0 - $numero / 100.0) * $quantite);
                $depot_compt_cod = get_request_var('depot_compt_cod');
                // CONTROLE: ARGENT DISPONIBLE
                $nb_or = $perso->perso_po;
                if ($nb_or < $quantite)
                {
                    $erreur = 1;
                    $info   = "Vous n'avez pas assez d'argent dans votre bourse";
                }
                if ($quantite < 1)
                {
                    $erreur = 1;
                    $info   = "Vous devez déposer au moins 1 Br";
                }
                if ($depot_compt_cod < 0)
                {
                    $erreur = 1;
                    $info   = "Vous devez sélectionner un compte";
                }
                if ($erreur == 0)
                {
                    // RETRAIT DE LA SOMME
                    $perso->perso_po = $perso->perso_po - $quantite;
                    $perso->stocke();
                    // AJOUT AU COMPTE
                    $gbank = new guilde_banque();
                    $gbank->charge($depot_compt_cod);
                    $gbank->gbank_or += $depot_guilde;
                    $gbank->stocke();
                    // LIGNE DE TRANSACTION
                    $gbanktran                          = new guilde_banque_transactions();
                    $gbanktran->gbank_tran_gbank_cod    = $depot_compt_cod;
                    $gbanktran->gbank_tran_perso_cod    = $perso_cod;
                    $gbanktran->gbank_tran_montant      = $depot_guilde;
                    $gbanktran->gbank_tran_debit_credit = 'C';
                    $gbanktran->gbank_tran_date         = date('Y-m-d H:i:s');
                    $gbanktran->stocke(true);

                    $myguilde = new guilde;
                    $myguilde->charge($gbank->gbank_guilde_cod);

                    $result = $stmt->fetch();
                    ?>
                    <p>Vous avez versé <?php echo $depot_guilde; ?> Br sur le compte <?php echo $gbank->gbank_nom ?>
                        (<?php echo $myguilde->guilde_nom ?>), <BR\>
                        <?php echo $quantite ?> Br ont été retirés de votre bourse.
                    </p>
                    <?php
                } else
                {
                    ?>
                    <p>Une anomalie est survenue : <strong><?php echo $info ?> </strong></p>
                    <?php
                }
                break;
            case "retrait_compte_guilde":
                // TRAITEMENT: UN ADMIN FAIT UN RETRAIT SUR LE COMPTE DE SA GUILDE
                if (($nb_guilde > 0) and ($adm == "O"))
                {
                    $quantite = get_request_var('quantite');
                    if ($quantite <= 0)
                    {
                        $erreur = 1;
                        $info   = "Vous ne pouvez pas retirer une somme inférieure ou égale à 0 !";
                    }
                    // CONTROLE: ARGENT DISPONIBLE
                    $gbank = new guilde_banque();
                    $gbank->getByGuilde($_REQUEST['guilde_cod']);

                    if ($gbank->getByGuilde($_REQUEST['guilde_cod']))
                    {
                        if ($gbank->gbank_or < $quantite)
                        {
                            $erreur = 1;
                            $info   = "Vous n'avez pas assez d'argent sur le compte de votre guilde";
                        }
                    } else
                    {
                        $erreur = 1;
                        $info   = "Votre guilde ne dispose pas de compte !";
                    }
                    if ($erreur == 0)
                    {
                        // RETRAIT SUR LE COMPTE
                        $gbank->gbank_or -= $quantite;
                        $gbank->stocke();


                        // AJOUT DANS LA BOURSE
                        $perso->perso_po = $perso->perso_po + $quantite;
                        $perso->stocke();

                        // LIGNE DE TRANSACTION
                        $gbanktran                          = new guilde_banque_transactions();
                        $gbanktran->gbank_tran_gbank_cod    = $gbank->gbank_cod;
                        $gbanktran->gbank_tran_perso_cod    = $perso_cod;
                        $gbanktran->gbank_tran_montant      = $quantite;
                        $gbanktran->gbank_tran_debit_credit = 'D';
                        $gbanktran->gbank_tran_date         = date('Y-m-d H:i:s');
                        $gbanktran->stocke(true);

                        $myguilde = new guilde;
                        $myguilde->charge($gbank->gbank_guilde_cod);

                        ?>
                        <p>Vous venez de retirer <?php echo $quantite; ?> Br à partir du
                            compte <?php echo $gbank->gbank_nom ?> (<?php echo $myguilde->guilde_nom ?>).
                        </p>
                        <?php

                    } else
                    {
                        ?>
                        <p>Une anomalie est survenue : <strong><?php echo $info ?></strong></p>
                        <?php
                    }
                }
                break;
        }
    }

    echo '<p>Vous avez ' . $perso->perso_po . ' brouzoufs sur vous.</p>'
    ?>
    <?php
    // on recherche l'or en banque
    $pbank  = new perso_banque();
    $qte_or = 0;
    if ($pbank->getByPerso($perso_cod))
    {
        $qte_or = $pbank->pbank_or;
    }


    ?>
    <p>Vous avez <?php echo $qte_or; ?> brouzoufs sur votre compte.</p>

    <hr/>

    <br/>
    <form name="depot" method="post" action="lieu.php">
        <input type="hidden" name="methode" value="depot">
        <input type="submit" value="Déposer des brouzoufs !" class="test">
    </form>
    <?php if ($qte_or != 0)
{
    ?>
    <br/>
    <form name="retrait" method="post" action="lieu.php">
        <input type="hidden" name="methode" value="retrait">
        <input type="submit" value="Faire un retrait !" class="test">
    </form>

<?php }
    ?>
    <hr/>
    <?php
    // PARTIE COMPTE BANCAIRE DE GUILDE
    // ON VERIFIE SI LE PERSO FAIT PARTIE D'UNE GUILDE

    if (isset($guilde))
    {
        if ($adm == "O")
        {
            echo "Vous êtes administrateur de la guilde: ", $guilde->guilde_nom;
            $gbank = new guilde_banque();
            if ($gbank->getByGuilde($guilde->guilde_cod))
            {

                $gbank_cod = $gbank->gbank_cod;
                $solde     = $gbank->gbank_or;
                ?>
                <p>Votre guilde dispose d'un compte: <strong><?php echo $gbank->gbank_nom; ?></strong> Solde actuel:
                    <strong><?php echo $solde; ?> Br</strong> <BR/>
                <p>
                <form name="retrait_guilde" method="post" action="lieu.php">
                    <input type="hidden" name="methode" value="view_retrait_guilde">
                    <input type="submit" value="Faire un retrait sur votre compte de guilde !" class="test">
                </form>
                </p>
                </p>
                <p><strong> RELEVE DE COMPTES </strong>
                <div class="centrer">
                    <TABLE width="85%">
                        <TR>
                            <TD>PERSONNAGE</TD>
                            <TD>DATE</TD>
                            <TD>DEBIT</TD>
                            <TD>CREDIT</TD>
                        </TR>
                        <?php
                        // RELEVE DE COMPTES
                        $gbanktran = new guilde_banque_transactions();
                        $alltran   = $gbanktran->getByCompte($gbank->gbank_cod);

                        $i = 0;
                        foreach ($alltran as $result)
                        {
                            if (($i % 2) == 0)
                            {
                                $style = "class=\"soustitre2\"";
                            } else
                            {
                                $style = "";
                            }
                            $i++;
                            echo "<TR><TD $style>", $result->perso['perso_nom'], "</TD>";
                            echo "<TD $style>", format_date($result['gbank_tran_date']), "</TD>";
                            if ($result['gbank_tran_debit_credit'] == 'D')
                            {
                                echo "<TD $style>", $result['gbank_tran_montant'], "</TD><TD $style></TD>";
                            } else
                            {
                                echo "<TD $style></TD><TD $style>", $result['gbank_tran_montant'], "</TD>";
                            }
                            echo "</TR>";
                        } ?>
                        <TR>
                            <TD>&nbsp;</TD>
                            <TD>&nbsp;</TD>
                            <TD>&nbsp;</TD>
                            <TD>&nbsp;</TD>
                        </TR>
                        <TR>
                            <TD>SOLDE:</TD>
                            <TD>--</TD>
                            <TD>--</TD>
                            <TD><strong><?php echo $solde; ?> Br</strong></TD>
                        </TR>
                    </TABLE>
                </div>
            <?php } else
            {
                // PAS DE COMPTE POUR LA GUILDE, ON PROPOSE D'EN CREER UN.
                ?>

                <p> Votre guilde ne dispose pas de compte, en créer un pour 5000 Br ?
                    <form name="cre_compt_guilde" method="post" action="lieu.php">
                        <input type="hidden" name="methode" value="creer_compte_guilde">
                <p>Nom du compte : <input type="text" name="compte_nom">
                    <input type="submit" value="Créer !" class="test centrer"></p>
                </form>
                <?php
            }

        } else
        {
            echo "Vous n'êtes pas administrateur de la guilde: ", $guilde->guilde_nom;
        }
    }
    // FAIRE UN DEPOT SUR UN COMPTE DE GUILDE
    ?>
    <HR/>
    <p>
    <form name="depot_guilde" method="post" action="lieu.php">
        <input type="hidden" name="methode" value="view_depot_guilde">
        <input type="submit" value="Faire un dépot sur un compte de guilde !" class="test">
    </form>
    </p>
<?php }
include_once "quete.php";
?>
