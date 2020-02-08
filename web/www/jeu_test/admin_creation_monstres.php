<?php
include "blocks/_header_page_jeu.php";

$etage = $_REQUEST['etage'];

ob_start();

$erreur = 0;

$droit_modif = 'dcompt_creer_monstre';
include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0)
{
    include "admin_edition_header.php";

    echo '<h1>Création de monstre</h1>';

    // TRAITEMENT DE FORMULAIRE
    if (isset($_POST['methode']))
    {
        switch ($methode)
        {
            case "deposer_monstre":
                $err_depl = 0;
                require "_admin_creation_monstre_test_pos.php";
                if ($err_depl == 0)
                {
                    $perso_sta_combat      = (isset($_POST['perso_sta_combat'])) ? 'O' : 'N';
                    $perso_sta_hors_combat = (isset($_POST['perso_sta_hors_combat'])) ? 'O' : 'N';
                    $perso_dirige_admin    = (isset($_POST['perso_dirige_admin'])) ? 'N' : 'O';
                    $req                   = "select cree_monstre_test(:gmon_cod,:etage) as num_mons ";
                    $stmt                  = $pdo->prepare($req);
                    $req                   =
                        "update perso_position set ppos_pos_cod = :pos_cod where ppos_perso_cod = :mon_cod";
                    $stmt2                 = $pdo->prepare($req);
                    $req                   = "select cree_monstre_pos(:gmon_cod,:pos_cod) as num_mons ";
                    $stmt3                 = $pdo->prepare($req);
                    for ($i = 0; $i < $_REQUEST['nombre_mons']; $i++)
                    {
                        if ($_REQUEST['utiliser_test'] == "OUI")
                        {

                            $stmt    = $pdo->execute(array(":gmon_cod" => $_REQUEST['gmon_cod'],
                                                           ":etage"    => $_REQUEST['etage']), $stmt);
                            $result  = $stmt->fetch();
                            $mon_cod = $result['num_mons'];

                            $stmt2 = $pdo->execute(array(":pos_cod" => $pos_cod,
                                                         ":mon_cod" => $mon_cod), $stmt2);

                        } else
                        {

                            $stmt3   = $pdo->execute(array(":gmon_cod" => $_REQUEST['gmon_cod'],
                                                           ":pos_cod"  => $pos_cod), $stmt3);
                            $result  = $stmt3->fetch();
                            $mon_cod = $result['num_mons'];
                        }
                        require "_admin_creation_monstre_modif_perso.php";
                        echo "<p>Le monstre $mon_cod a bien été déposé !</p>";
                    }
                    $req_mons =
                        "select gmon_nom, perso_nom, compt_nom 
                        from monstre_generique, perso, compte 
                        where gmon_cod = :gmon_cod
                        and perso_cod = :perso_cod and compt_cod = :compt_cod";
                    $stmt     = $pdo->prepare($req_mons);
                    $stmt     = $pdo->execute(array(":gmon_cod"  => $_REQUEST['gmon_cod'],
                                                    ":perso_cod" => $perso_cod,
                                                    ":compt_cod" => $compt_cod), $stmt);
                    if ($result = $stmt->fetch())
                    {
                        $pmons_mod_nom = $result['gmon_nom'];
                        $perso_nom     = $result['perso_nom'];
                        $compt_nom     = $result['compt_nom'];
                    } else
                    {
                        $pmons_mod_nom = $gmon_nom;
                        $perso_nom     = 'Perso: ' . $perso_cod;
                    }
                    writelog(date("d/m/y - H:i") . "$perso_nom - $compt_nom (compte $compt_cod) depose $nombre_mons $pmons_mod_nom ($gmon_cod) en $pos_x, $pos_y, $etage\n", 'monstre_edit');
                }
                break;

            case "deposer_armée":
                $err_depl = 0;
                require "_admin_creation_monstre_test_pos.php";
                if ($err_depl == 0)
                {
                    $commandant = -1;
                    $nb_troupes = 0;
                    for ($j = 0; $j < 5; $j++)
                    {
                        if ($_POST['gmon_cod_' . $j] > 0)
                        {
                            $gmon_cod              = $_POST['gmon_cod_' . $j];
                            $perso_sta_combat      = (isset($_POST['perso_sta_combat_' . $j])) ? 'O' : 'N';
                            $perso_sta_hors_combat = (isset($_POST['perso_sta_hors_combat_' . $j])) ? 'O' : 'N';
                            $mcom_cod              = $_POST['mcom_cod_' . $j];
                            $compt_admin           = $_POST['compt_admin_' . $j];
                            $pia_ia_type           = $_POST['pia_ia_type_' . $j];
                            $nombre_mons           = $_POST['nombre_mons_' . $j];
                            $perso_dirige_admin    = (isset($_POST['perso_dirige_admin_' . $j])) ? 'N' : 'O';

                            $nb_troupes += $nombre_mons;
                            $req        = "select cree_monstre_pos(:gmon_cod, :pos_cod) as num_mons ";
                            $stmt       = $pdo->prepare($req);


                            $tmpperso = new perso;
                            $tmpperso->charge($mon_cod);
                            $tmpperso->perso_sta_combat      = $perso_sta_combat;
                            $tmpperso->perso_sta_hors_combat = $perso_sta_hors_combat;
                            $tmpperso->perso_mcom_cod        = $mcom_cod;
                            $tmpperso->stocke();


                            for ($i = 0; $i < $nombre_mons; $i++)
                            {

                                $stmt    = $pdo->execute(array(":gmon_cod" => $gmon_cod,
                                                               ":pos_cod"  => $pos_cod), $stmt);
                                $result  = $stmt->fetch();
                                $mon_cod = $result['num_mons'];

                                if ($commandant == -1 && $j == 0 && isset($_POST['commandant']))
                                {
                                    $commandant = $mon_cod;
                                }


                                require "_admin_creation_monstre_modif_perso.php";
                                if ($commandant > 0 && $commandant != $mon_cod)
                                {
                                    $req_troupe = "select ajoute_commandement(:commandant, :mon_cod, true) as resultat";
                                    $stmt       = $pdo->prepare($req_troupe);
                                    $stmt       = $pdo->execute(array(":commandant" => $commandant,
                                                                      ":mon_cod"    => $mon_cod), $stmt);
                                    $result     = $stmt->fetch();
                                    echo '<p>' . $result['resultat'] . '</p>';
                                }
                            }
                            $req_mons =
                                "select gmon_nom, perso_nom, compt_nom 
                                    from monstre_generique, perso, compte 
                                    where gmon_cod = :gmon_cod 
                                    and perso_cod = :perso_cod 
                                    and compt_cod = :compt_cod";
                            $stmt     = $pdo->prepare($req_mons);
                            $stmt     = $pdo->execute(array(":gmon_cod"  => $_REQUEST['gmon_cod'],
                                                            ":perso_cod" => $perso_cod,
                                                            ":compt_cod" => $compt_cod), $stmt);
                            if ($result = $stmt->fetch())
                            {
                                $pmons_mod_nom = $result['gmon_nom'];
                                $perso_nom     = $result['perso_nom'];
                                $compt_nom     = $result['compt_nom'];
                            } else
                            {
                                $pmons_mod_nom = $gmon_nom;
                                $perso_nom     = 'Perso : ' . $perso_cod;
                            }
                            echo "Création de $nombre_mons $pmons_mod_nom ($gmon_cod) en $pos_x, $pos_y, $etage<br />";
                            writelog(date("d/m/y - H:i") . "$perso_nom - $compt_nom (compte $compt_cod) depose $nombre_mons $pmons_mod_nom ($gmon_cod) en $pos_x, $pos_y, $etage\n", 'monstre_edit');
                        }
                    }

                    if ($commandant > 0)
                    {
                        // on ajoute la compétence au commandant
                        $req_commandant = "select pcomp_modificateur from perso_competences 
							where pcomp_perso_cod = $commandant 
								and pcomp_pcomp_cod = 80";
                        $pc             = new perso_competences();
                        if ($pc->getByPersoComp($commandant, 80))
                        {
                            $pc->pcomp_modificateur = max($nb_troupes, $pc->pcomp_modificateur);
                            $pc->stocke();
                        } else
                        {
                            $pc->pcomp_pcomp_cod    = 80;
                            $pc->pcomp_perso_cod    = $commandant;
                            $pc->pcomp_modificateur = max($nb_troupes, 20);
                            $pc->stocke(true);

                        }
                    }
                }
                break;
        }
    }

    $option_monstre = '';
    $req            = "select gmon_cod,gmon_nom from monstre_generique order by gmon_nom ";
    $stmt           = $pdo->query($req);
    while ($result = $stmt->fetch())
    {
        $option_monstre .= "<option value=\"" . $result['gmon_cod'] . "\" >" . $result['gmon_nom'] . "</option>";
    }

    $option_compte = '';
    $req           = "select compt_nom,compt_cod from compte where compt_monstre = 'O' order by compt_nom desc ";
    $stmt          = $pdo->query($req);
    while ($result = $stmt->fetch())
    {
        $texte_select  = ($result['compt_cod'] == $compt_cod) ? 'selected="selected"' : '';
        $option_compte .= "<option value=\"" . $result['compt_cod'] . "\" $texte_select>" . $result['compt_nom'] . "</option>";
    }

    $option_IA = '';
    $req       = "select ia_type,ia_nom from type_ia order by ia_type desc ";
    $stmt      = $pdo->query($req);
    while ($result = $stmt->fetch())
    {
        $option_IA .= "<option value=\"" . $result['ia_type'] . "\">" . $result['ia_nom'] . "</option>";
    }

    $option_combat = '';
    $req           = "select mcom_cod, mcom_nom from mode_combat order by mcom_cod desc ";
    $stmt          = $pdo->query($req);
    while ($result = $stmt->fetch())
    {
        $option_combat .= "<option value=\"" . $result['mcom_cod'] . "\">" . $result['mcom_nom'] . "</option>";
    }


    ?>
    <p>Déposer un monstre à une position : </p>
    <form method="post">
        <input type="hidden" name="methode" value="deposer_monstre">
        <!--<font color="red">Utiliser la fonction de test ? OUI<input type="radio" name="utiliser_test" value="OUI">
        NON<input type="radio" name="utiliser_test" value="NON" checked></font>--><br/>
        X : <input type="text" name="pos_x" maxlength="5" size="5" value="<?php echo $pos_x ?>"> -
        Y : <input type="text" name="pos_y" maxlength="5" size="5" value="<?php echo $pos_y ?>"> -
        Étage : <select name="etage">
            <?php
            echo $html->etage_select($etage);
            ?>
        </select><br>
        Monstre : <select name="gmon_cod">
            <?php
            echo $option_monstre;
            ?>
        </select> -
        Nombre : <input type="text" name="nombre_mons" maxlength="5" size="5" value=""><br>
        <label for="perso_dirige_admin">Placer le monstre sous le contrôle de l’IA ? (automatique si aucun admin
            monstre)</label><input type="checkbox" name="perso_dirige_admin" id="perso_dirige_admin"/><br/>
        Admin monstre : <select name="compt_admin">
            <option value="-1">Aucun (géré par l’IA)</option>
            <?php
            echo $option_compte;
            ?>
        </select> -
        IA monstre : <select name="pia_ia_type">
            <option value="-1">Aucune (IA par défaut)</option>
            <?php
            echo $option_IA;
            ?>
        </select><br>
        Mode de combat : <select name="mcom_cod">
            <?php
            echo $option_combat;
            ?>
        </select> -
        <label for="perso_sta_combat">Statique en combat ? </label><input type="checkbox" name="perso_sta_combat"
                                                                          id="perso_sta_combat"/> -
        <label for="perso_sta_hors_combat">Statique hors combat ? </label><input type="checkbox"
                                                                                 name="perso_sta_hors_combat"
                                                                                 id="perso_sta_hors_combat"/><br/>
        <input type="submit" value="Déposer" class='test'/>
    </form>
    <hr/>
    <p>Déposer une armée à une position : </p>
    <form method="post">
        <input type="hidden" name="methode" value="deposer_armée">
        X : <input type="text" name="pos_x" maxlength="5" size="5" value="<?php echo $pos_x ?>"> -
        Y : <input type="text" name="pos_y" maxlength="5" size="5" value="<?php echo $pos_y ?>"> -
        Étage : <select name="etage">
            <?php
            echo $html->etage_select($etage);
            ?>
        </select><br>
        <label for='commandant'>Définir un commandant ? <input type='checkbox' id='commandant' name='commandant'
                                                               checked='checked'/> (celui-ci sera choisi dans le premier
            groupe de monstres créés ci-dessous) </label>

        <?php for ($i = 0; $i < 5; $i++)
        {
            ?>
            <div style='margin-top:15px; margin-left:15px'>
                Monstre : <select name="gmon_cod_<?php echo $i; ?>">
                    <option value='-1'>Aucun monstre</option>
                    <?php
                    echo $option_monstre;
                    ?>
                </select> -
                Nombre : <input type="text" name="nombre_mons_<?php echo $i; ?>" maxlength="5" size="5" value=""><br>
                <label for="perso_dirige_admin_<?php echo $i; ?>">Placer le monstre sous le contrôle de l’IA ?
                    (automatique si aucun admin monstre)</label><input type="checkbox"
                                                                       name="perso_dirige_admin_<?php echo $i; ?>"
                                                                       id="perso_dirige_admin_<?php echo $i; ?>"/><br/>
                Admin monstre : <select name="compt_admin_<?php echo $i; ?>">
                    <option value="-1">Aucun (géré par l’IA)</option>
                    <?php
                    echo $option_compte;
                    ?>
                </select> -
                IA monstre : <select name="pia_ia_type_<?php echo $i; ?>">
                    <option value="-1">Aucune (IA par défaut)</option>
                    <?php
                    echo $option_IA;
                    ?>
                </select><br>
                Mode de combat : <select name="mcom_cod_<?php echo $i; ?>">
                    <?php
                    echo $option_combat;
                    ?>
                </select> -
                <label for="perso_sta_combat_<?php echo $i; ?>">Statique en combat ? </label><input type="checkbox"
                                                                                                    name="perso_sta_combat_<?php echo $i; ?>"
                                                                                                    id="perso_sta_combat_<?php echo $i; ?>"/>
                -
                <label for="perso_sta_hors_combat_<?php echo $i; ?>">Statique hors combat ? </label><input
                        type="checkbox" name="perso_sta_hors_combat_<?php echo $i; ?>"
                        id="perso_sta_hors_combat_<?php echo $i; ?>"/><br/>
            </div>
        <?php }
        ?>
        <input type="submit" value="Déposer" class='test'/>
    </form>
    <hr/>
    <p style="text-align:center;"><a href="<?php echo $PHP_SELF ?>">Retour au début</a>
<?php }

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";