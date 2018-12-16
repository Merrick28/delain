<?php
//
//Contenu de la div de droite
//

// on regarde si le joueur est bien sur une banque
$erreur = 0;
$db = new base_delain;
if (!$db->is_lieu($perso_cod))
{
    echo("<p>Erreur ! Vous n'êtes pas sur une banque !!!");
    $erreur = 1;
}
if ($erreur == 0)
{
    $tab_lieu = $db->get_lieu($perso_cod);
    if ($tab_lieu['type_lieu'] != 1)
    {
        $erreur = 1;
        echo("<p>Erreur ! Vous n'êtes pas sur une banque !!!");
    }
}

if ($erreur == 0)
{
    // INFOS GUILDE
    $req_guilde = "select guilde_nom,guilde_cod,rguilde_admin from guilde,guilde_perso,guilde_rang ";
    $req_guilde = $req_guilde . "where pguilde_perso_cod = $perso_cod and pguilde_valide = 'O' and pguilde_guilde_cod = guilde_cod ";
    $req_guilde = $req_guilde . "and rguilde_guilde_cod = guilde_cod and rguilde_rang_cod = pguilde_rang_cod ";
    $db->query($req_guilde);
    $nb_guilde = $db->nf();
    if ($nb_guilde > 0)
    {
        $db->next_record();
        $adm = $db->f("rguilde_admin");
        $guilde_cod = $db->f("guilde_cod");
        $guilde_nom = $db->f("guilde_nom");
    }
    // INFOS ETAGE
    $req_pos = " select pos_etage from positions,perso_position"
        . " where ppos_pos_cod = pos_cod and ppos_perso_cod = $perso_cod";
    $db->query($req_pos);
    $db->next_record();
    $numero = -1 * $db->f("pos_etage");
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
                            $req_compte_guilde = "select gbank_cod,gbank_nom,guilde_nom from guilde_banque,guilde where gbank_guilde_cod = guilde_cod order by guilde_nom";
                            $db->query($req_compte_guilde);
                            while ($db->next_record())
                            {
                                echo "<option value=\"", $db->f("gbank_cod"), "\">", $db->f("gbank_nom"), " (", $db->f("guilde_nom"), ")</option>";
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
                if ($quantite < 0)
                {
                    echo("<p>Bien tenté ...");
                    break;
                }
                // TRAITEMENT: UN PERSONNAGE FAIT UN DEPOT SUR SON COMPTE PERSONNEL
                $req_depot = "select depot_banque($perso_cod,$quantite) as depot";
                $db->query($req_depot);
                $db->next_record();
                //$tab_depot = pg_fetch_array($res_depot,0);
                if ($db->f("depot") == 0)
                {
                    echo("<p>Vous venez de déposer <strong>$quantite</strong> brouzoufs sur votre compte en banque.");
                } else
                {
                    printf("<p>Une anomalie est survenue : <strong>%s</strong>", $db->f("depot"));
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
                    $req_depot = "select retrait_banque($perso_cod,$quantite) as retrait";
                    $db->query($req_depot);
                    $db->next_record();
                    $tab_depot = $db->f("retrait");
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
                    // CONTROLE: COMPTE NON EXISTANT
                    $req_compte_guilde = "select gbank_cod from guilde_banque where gbank_guilde_cod = $guilde_cod";
                    $db->query($req_compte_guilde);
                    if ($db->nf() > 0)
                    {
                        $erreur = 1;
                        $info = "Votre guilde dispose déjà d'un compte";
                    }
                    // CONTROLE: ARGENT DISPONIBLE
                    $req_or = "select perso_po from perso where perso_cod = $perso_cod ";
                    $db->query($req_or);
                    $db->next_record();
                    $nb_or = $db->f("perso_po");
                    if ($nb_or < 5000)
                    {
                        $erreur = 1;
                        $info = "Vous n'avez pas assez d'argent dans votre bourse";
                    }
                    if ($erreur == 0)
                    {
                        // RETRAIT DE LA SOMME
                        $req_or = "update perso set perso_po = perso_po - 5000 where perso_cod = $perso_cod ";
                        $db->query($req_or);
                        // CREATION DU COMPTE
                        $compte_nom = str_replace("''", "\'", $compte_nom);
                        $compte_nom = pg_escape_string($compte_nom);

                        $req_cre_compte = "insert into guilde_banque (gbank_guilde_cod,gbank_nom,gbank_or,gbank_date_creation) values ($guilde_cod,e'$compte_nom',0,now())";
                        $db->query($req_cre_compte);
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
                $quantite = intval($quantite);
                $depot_guilde = floor((1.0 - $numero / 100.0) * $quantite);

                // CONTROLE: ARGENT DISPONIBLE
                $req_or = "select perso_po from perso where perso_cod = $perso_cod ";
                $db->query($req_or);
                $db->next_record();
                $nb_or = $db->f("perso_po");
                if ($nb_or < $quantite)
                {
                    $erreur = 1;
                    $info = "Vous n'avez pas assez d'argent dans votre bourse";
                }
                if ($quantite < 1)
                {
                    $erreur = 1;
                    $info = "Vous devez déposer au moins 1 Br";
                }
                if ($depot_compt_cod < 0)
                {
                    $erreur = 1;
                    $info = "Vous devez sélectionner un compte";
                }
                if ($erreur == 0)
                {
                    // RETRAIT DE LA SOMME
                    $req_or = "update perso set perso_po = perso_po - $quantite where perso_cod = $perso_cod ";
                    $db->query($req_or);
                    // AJOUT AU COMPTE
                    $req_compte = "update guilde_banque set gbank_or  = gbank_or + $depot_guilde where gbank_cod = $depot_compt_cod";
                    $db->query($req_compte);
                    // LIGNE DE TRANSACTION
                    $req_compte = "insert into guilde_banque_transactions (gbank_tran_gbank_cod,gbank_tran_perso_cod,gbank_tran_montant,gbank_tran_debit_credit,gbank_tran_date) values ($depot_compt_cod,$perso_cod,$depot_guilde,'C',now())";
                    $db->query($req_compte);

                    $req_or = "select gbank_nom,guilde_nom from guilde,guilde_banque where gbank_cod = $depot_compt_cod and gbank_guilde_cod = guilde_cod";
                    $db->query($req_or);
                    $db->next_record();
                    ?>
                    <p>Vous avez versé <?php echo $depot_guilde; ?> Br sur le compte <?php echo $db->f("gbank_nom") ?>
                        (<?php echo $db->f("guilde_nom") ?>), <BR\>
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
                    if ($quantite <= 0)
                    {
                        $erreur = 1;
                        $info = "Vous ne pouvez pas retirer une somme inférieure ou égale à 0 !";
                    }
                    // CONTROLE: ARGENT DISPONIBLE
                    $req_or = "select gbank_cod,gbank_or from guilde_banque where gbank_guilde_cod = $guilde_cod ";
                    $db->query($req_or);
                    if ($db->nf() > 0)
                    {
                        $db->next_record();
                        $nb_or = $db->f("gbank_or");
                        $gbank_cod_retrait = $db->f("gbank_cod");
                        if ($nb_or < $quantite)
                        {
                            $erreur = 1;
                            $info = "Vous n'avez pas assez d'argent sur le compte de votre guilde";
                        }
                    } else
                    {
                        $erreur = 1;
                        $info = "Votre guilde ne dispose pas de compte !";
                    }
                    if ($erreur == 0)
                    {
                        // RETRAIT SUR LE COMPTE
                        $req_compte = "update guilde_banque set gbank_or  = gbank_or - $quantite where gbank_cod = $gbank_cod_retrait";
                        $db->query($req_compte);


                        // AJOUT DANS LA BOURSE
                        $req_or = "update perso set perso_po = perso_po + $quantite where perso_cod = $perso_cod ";
                        $db->query($req_or);

                        // LIGNE DE TRANSACTION
                        $req_compte = "insert into guilde_banque_transactions (gbank_tran_gbank_cod,gbank_tran_perso_cod,gbank_tran_montant,gbank_tran_debit_credit,gbank_tran_date) values ($gbank_cod_retrait,$perso_cod,$quantite,'D',now())";
                        $db->query($req_compte);

                        $req_or = "select gbank_nom,guilde_nom from guilde,guilde_banque where gbank_cod = $gbank_cod_retrait and gbank_guilde_cod = guilde_cod";
                        $db->query($req_or);
                        $db->next_record();
                        ?>
                        <p>Vous venez de retirer <?php echo $quantite; ?> Br à partir du
                            compte <?php echo $db->f("gbank_nom") ?> (<?php echo $db->f("guilde_nom") ?>).
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
    // on recherche l'or sur soi
    $req_or_perso = "select perso_po from perso where perso_cod = $perso_cod ";
    $db->query($req_or_perso);
    $db->next_record();
    $nb_or_perso = $db->f("perso_po");
    echo '<p>Vous avez ' . $nb_or_perso . ' brouzoufs sur vous.</p>'
    ?>
    <?php
    // on recherche l'or en banque
    $req_or = "select pbank_or from perso_banque where pbank_perso_cod = $perso_cod ";
    $db->query($req_or);
    $nb_or = $db->nf();
    if ($nb_or == 0)
    {
        $qte_or = 0;
    } else
    {
        $db->next_record();
        $qte_or = $db->f("pbank_or");
    }
    ?>
    <p>Vous avez <?php echo $qte_or; ?> brouzoufs sur votre compte.</p>

    <hr/>

    <p>
    <form name="depot" method="post" action="lieu.php">
        <input type="hidden" name="methode" value="depot">
        <input type="submit" value="Déposer des brouzoufs !" class="test">
    </form>
    </p>
    <?php if ($qte_or != 0)
{
    ?>
    <p>
    <form name="retrait" method="post" action="lieu.php">
        <input type="hidden" name="methode" value="retrait">
        <input type="submit" value="Faire un retrait !" class="test">
    </form>
    </p>
<?php }
    ?>
    <HR/>
    <?php
    // PARTIE COMPTE BANCAIRE DE GUILDE
    // ON VERIFIE SI LE PERSO FAIT PARTIE D'UNE GUILDE

    if ($nb_guilde > 0)
    {
        if ($adm == "O")
        {
            echo "Vous êtes administrateur de la guilde: ", $guilde_nom;
            $req_compte_guilde = "select gbank_cod,gbank_nom,gbank_or from guilde_banque where gbank_guilde_cod = $guilde_cod";
            $db->query($req_compte_guilde);
            if ($db->nf() > 0)
            {
                $db->next_record();
                $gbank_cod = $db->f("gbank_cod");
                $solde = $db->f("gbank_or");
                ?>
                <p>Votre guilde dispose d'un compte: <strong><?php echo $db->f("gbank_nom"); ?></strong> Solde actuel:
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
                        $req_compte_guilde = "select perso_nom,gbank_tran_montant,gbank_tran_debit_credit,to_char(gbank_tran_date,'DD/MM/YYYY hh24:mi:ss') as date from guilde_banque_transactions,perso where gbank_tran_gbank_cod = $gbank_cod and gbank_tran_perso_cod = perso_cod order by gbank_tran_date";
                        $db->query($req_compte_guilde);
                        $i = 0;
                        while ($db->next_record())
                        {
                            if (($i % 2) == 0)
                            {
                                $style = "class=\"soustitre2\"";
                            } else
                            {
                                $style = "";
                            }
                            $i++;
                            echo "<TR><TD $style>", $db->f("perso_nom"), "</TD>";
                            echo "<TD $style>", $db->f("date"), "</TD>";
                            if ($db->f("gbank_tran_debit_credit") == 'D')
                            {
                                echo "<TD $style>", $db->f("gbank_tran_montant"), "</TD><TD $style></TD>";
                            } else
                            {
                                echo "<TD $style></TD><TD $style>", $db->f("gbank_tran_montant"), "</TD>";
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
            echo "Vous n'êtes pas administrateur de la guilde: ", $guilde_nom;
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
