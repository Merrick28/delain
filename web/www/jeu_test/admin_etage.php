<?php
include "blocks/_header_page_jeu.php";
ob_start();
define('APPEL', 1);
include "blocks/_test_droit_modif_etage.php";

$methode     = get_request_var('methode', 'debut');
$admin_etage = get_request_var('etage', 0);

if (isset($_REQUEST['admin_etage']))
{
    $admin_etage = get_request_var('admin_etage', 0);
}

if (!isset($admin_etage) && $methode == 'debut')
{
    $admin_etage = 0;
}
$html = new html;
echo "<table><tr><td><p><strong>Choisissez l’étage à modifier :</strong></p>
	<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>
	<input type='hidden' value='dessine' name='methode' />
	<select name='etage'>" .
     $html->etage_select($admin_etage) .
    "</select>&nbsp;<input type='submit' value='Valider' class='test'/></form></td><td>
	<p><strong>Autres outils</strong><br />
	<a href='modif_etage3.php?admin_etage={$admin_etage}'>Créer / modifier un étage (caractéristiques générales)</a><br />
	<a href='modif_etage3bis.php?admin_etage={$admin_etage}'>Créer / modifier les lieux</a><br />
	<a href='modif_etage3ter.php?admin_etage={$admin_etage}'>Creation multiple de lieux</a><br />
	<a target='_blank' href='admin_meca_etage.php?admin_etage={$admin_etage}'>Gestion des Mécanismes d'étage</a><br />
	<a target='_blank' href='admin_ea_etage.php?admin_etage={$admin_etage}'>Gestion des EA d'étage</a><br />
	<a target='_blank' href='admin_quete_auto_edit.php?pos_etage={$admin_etage}'>Gestion des QA d'étage</a><br />
	<a href='modif_etage3quater.php'>Dupliquer/Supprimer un étage</a><br />
	<a href='modif_etage.php'>Autres outils</a></td>
	</tr></table>";

//cahrger les type de terrains
$pdo = new bddpdo();
$req_m_terrain= "select ter_cod, ter_nom from terrain where ter_cod > 0 order by ter_nom";
$stmt_m_terrain = $pdo->query($req_m_terrain);
$terrains = $stmt_m_terrain->fetchAll(PDO::FETCH_ASSOC);

$req_m_ea= "select fonc_cod, fonc_trigger_param->>'fonc_trig_nom_ea' as nom_ea , fonc_trigger_param->>'fonc_trig_pos_cods' as pos_cods from fonction_specifique where fonc_trigger_param->>'fonc_trig_pos_etage'={$admin_etage} order by fonc_trigger_param->>'fonc_trig_nom_ea' ";
$stmt_m_ea = $pdo->query($req_m_ea);
$effet_auto = $stmt_m_ea->fetchAll(PDO::FETCH_ASSOC);

$req_m_qa= "select aquete_nom_alias as nom_qa, aquete_cod, STRING_AGG (aqelem_misc_cod, ', ') pos_cods from quetes.aquete join quetes.aquete_etape on aqetape_cod = aquete_etape_cod join  quetes.aquete_element on aqelem_aqetape_cod=aqetape_cod and aqelem_param_id=1 and aqelem_aqperso_cod is null where aquete_pos_etage = {$admin_etage} group by aquete_nom_alias, aquete_cod order by aquete_nom_alias ";
$stmt_m_qa = $pdo->query($req_m_qa);
$quete_auto = $stmt_m_qa->fetchAll(PDO::FETCH_ASSOC);

$req_m_meca= "select meca_cod, meca_nom,STRING_AGG (pmeca_pos_cod, ', ') pos_cods  from meca left join meca_position on pmeca_meca_cod=meca_cod where meca_pos_etage={$admin_etage}  group by meca_cod, meca_nom order by meca_nom ";
$stmt_m_meca = $pdo->query($req_m_meca);
$mecanisme = $stmt_m_meca->fetchAll(PDO::FETCH_ASSOC);


switch ($methode) {
    case "debut":
        break;

    case "dessine":
        ?>
        <link rel="stylesheet" type="text/css"
              href="style_vue.php?num_etage=<?php echo $admin_etage; ?>&source=fichiers" title="essai">

        <div class="bordiv">
            <table>
                <tr>
                    <td><strong>Pinceau</strong></td>
                    <td><strong>Fonds</strong></td>
                    <td><strong>Décors</strong></td>
                    <td><strong>Murs</strong></td>
                    <td><strong>Décors superposés</strong></td>
                    <td><strong>Spécial</strong></td>
                </tr>
                <tr valign="top">
                    <td style="min-width: 330px;" class="bordiv">
                        Outil sélectionné : <img style="display: inline;" src="" alt="Aucun" title="Aucun"
                                                 id="imgPinceau">
                        (Type : <span id="typePinceau">aucun</span>)<br>
                        <label><input name="remplissage" checked="checked" value="standard"
                                      onclick="Pinceau.action = this.value;" type="radio"/>Remplissage standard,
                        </label>
                        <label><input name="remplissage" value="pavage" onclick="Pinceau.action = this.value;"
                                      type="radio"/>Pavage, </label><br/>
                        <label><input name="remplissage" value="murs" onclick="Pinceau.action = this.value;"
                                      type="radio"/>Murs seuls, </label>
                        <label><input name="remplissage" value="sols" onclick="Pinceau.action = this.value;"
                                      type="radio"/>Pas les murs, </label><br/>
                        <label><input name="remplissage" value="annule" onclick="Pinceau.action = this.value;"
                                      type="radio"/>Annulation. </label><br/>
                        Forme du pinceau :
                        <div id="visuPinceau" onmouseout="Pinceau.survole(-1, -1)"></div>
                    </td>
                    <td id="pinceauFonds" class="bordiv"></td>
                    <td id="pinceauDecors" class="bordiv"></td>
                    <td id="pinceauMurs" class="bordiv"></td>
                    <td id="pinceauDecorsDessus" class="bordiv"></td>
                    <td id="pinceauSpecial" class="bordiv">
                        <label><input name="affichage" checked="checked" value="joli"
                                      onclick="Etage.ModeVisu.Change (Etage.ModeVisu.Joli);" type="radio"/>Affichage
                            standard, </label><br/>
                        <label><input name="affichage" value="murs"
                                      onclick="Etage.ModeVisu.Change (Etage.ModeVisu.Murs);" type="radio"/>ou murs
                            seuls.</label>
                        <hr/>
                        Passages:
                        <label><input name="special" value="passageTOGGLE"
                                      onclick="Pinceau.miseAJour ('Speciaux', this.value)"
                                      type="radio"/>bascule</label>
                        <label><input name="special" value="passageOK"
                                               onclick="Pinceau.miseAJour ('Speciaux', this.value)" type="radio"/>autorisés</label>
                        <label><input name="special" value="passageNOK"
                                      onclick="Pinceau.miseAJour ('Speciaux', this.value)"
                                      type="radio"/>interdits.</label><br/>
                        PVP: <label><input name="special" value="pvpTOGGLE"
                                          onclick="Pinceau.miseAJour ('Speciaux', this.value)"
                                          type="radio"/>bascule</label>
                        <label><input name="special" value="pvpOK"
                                          onclick="Pinceau.miseAJour ('Speciaux', this.value)"
                                          type="radio"/>autorisé</label>
                        <label><input name="special" value="pvpNOK" onclick="Pinceau.miseAJour ('Speciaux', this.value)"
                                      type="radio"/>interdit.</label><br/>
                        Mur creusable: <label><input name="special" value="creusableTOGGLE"
                                                    onclick="Pinceau.miseAJour ('Speciaux', this.value)" type="radio"/>bascule</label>
                        <label><input name="special" value="creusableOK"
                                                    onclick="Pinceau.miseAJour ('Speciaux', this.value)" type="radio"/>oui</label>
                        <label><input name="special" value="creusableNOK"
                                      onclick="Pinceau.miseAJour ('Speciaux', this.value)"
                                      type="radio"/>non.</label><br/>
                        <span title="Un mur non tangible permet de voir / tirer au travers, mais empêche de passer">Mur tangible (*)</span>
                        <label><input name="special" value="tangibleTOGGLE"
                                      onclick="Pinceau.miseAJour ('Speciaux', this.value)" type="radio"/>bascule</label>
                        <label><input name="special" value="tangibleOK"
                                      onclick="Pinceau.miseAJour ('Speciaux', this.value)" type="radio"/>oui</label>
                        <label><input name="special" value="tangibleNOK"
                                      onclick="Pinceau.miseAJour ('Speciaux', this.value)"
                                      type="radio"/>non.</label><br/>
                        <span title="Un mur illusion se comporte comme un mur pour l'automap, mais n'en est pas un!">Mur illusion (*)</span>
                        <label><input name="special" value="illusionTOGGLE"
                                      onclick="Pinceau.miseAJour ('Speciaux', this.value)" type="radio"/>bascule</label>
                        <label><input name="special" value="illusionOK"
                                      onclick="Pinceau.miseAJour ('Speciaux', this.value)" type="radio"/>oui</label>
                        <label><input name="special" value="illusionNOK"
                                      onclick="Pinceau.miseAJour ('Speciaux', this.value)"
                                      type="radio"/>non.</label><br/>
                        <span title="Position d'entrée pour les etage du type arène.">Entrée d'arène</span>
                        <label><input name="special" value="areneTOGGLE"
                                      onclick="Pinceau.miseAJour ('Speciaux', this.value)" type="radio"/>bascule</label>
                        <label><input name="special" value="areneOK"
                                      onclick="Pinceau.miseAJour ('Speciaux', this.value)" type="radio"/>oui</label>
                        <label><input name="special" value="areneNOK"
                                      onclick="Pinceau.miseAJour ('Speciaux', this.value)"
                                      type="radio"/>non.</label><br/>

                        <input name="special" value="terrain" onclick="Pinceau.miseAJour ('Speciaux', this.value)" type="radio"/>
                        <span title="Sélection du terrain.">Terrains seuls: </span>
                        <?php
                        echo '<select name="select-terrain" id="select-terrain" onchange="Pinceau.miseAJour (\'Speciaux\', \'terrain\')">';
                        echo '<option value="0">Sans terrain spécifique</option>';
                        for ($t=0; $t<count($terrains); $t++ )
                        {
                            echo '<option value="'.$terrains[$t]["ter_cod"].'">'.$terrains[$t]["ter_nom"].'</option>';
                        }
                        echo '</select>';
                        ?><br>

                        <input name="special" value="deplacement" onclick="Pinceau.miseAJour ('Speciaux', this.value)" type="radio"/>
                        <span title="Gestion des BM de déplacement.">Déplacement: </span>
                        Modificateur PA: <input name="dep_pa" id="dep_pa" value="0" type="text" size="3"/>
                        <br/>

                        <input name="special" value="terrain-dep" onclick="Pinceau.miseAJour ('Speciaux', this.value)" type="radio"/>
                        <span title="Sélection du terrain et des BM de déplacement.">Gestion des Terrains/PA: </span><br>
                        Modificateur PA: <input name="terrain_dep_pa" id="terrain-dep_pa" value="0" type="text" size="3"/>
                        <?php
                        echo '<select name="select-terrain-dep" id="select-terrain-dep" onchange="Pinceau.miseAJour (\'Speciaux\', \'terrain-dep\')">';
                        echo '<option value="0">Sans terrain spécifique</option>';
                        for ($t=0; $t<count($terrains); $t++ )
                        {
                            echo '<option value="'.$terrains[$t]["ter_cod"].'">'.$terrains[$t]["ter_nom"].'</option>';
                        }
                        echo '</select>';
                        ?>
                       <br/>

                        <input name="special" value="ea-dep" onclick="Pinceau.miseAJour ('Speciaux', this.value)" type="radio"/>
                        <span title="Gestion de la position des Effets-Auto.">Effets-Auto: </span>
                        <?php
                        echo '<select name="select-ea-dep" id="select-ea-dep" onchange="Pinceau.miseAJour (\'Speciaux\', \'ea-dep\')">';
                        echo '<option value="0">Selecteur de positions</option>';
                        for ($ea=0; $ea<count($effet_auto); $ea++ )
                        {
                            echo '<option value="'.$effet_auto[$ea]["fonc_cod"].'">'.$effet_auto[$ea]["nom_ea"].'</option>';
                        }
                        echo '</select>';
                        ?>                       <br/>

                        <input name="special" value="qa-dep" onclick="Pinceau.miseAJour ('Speciaux', this.value)" type="radio"/>
                        <span title="Gestion de la position des Quetes-Auto.">Quete-Auto: </span>
                        <?php
                        echo '<select name="select-qa-dep" id="select-qa-dep" onchange="Pinceau.miseAJour (\'Speciaux\', \'qa-dep\')">';
                        for ($qa=0; $qa<count($quete_auto); $qa++ )
                        {
                            echo '<option value="'.$quete_auto[$qa]["aquete_cod"].'">'.$quete_auto[$qa]["nom_qa"].'</option>';
                        }
                        echo '</select>';
                        ?>                       <br/>

                        <input name="special" value="meca-dep" onclick="Pinceau.miseAJour ('Speciaux', this.value)" type="radio"/>
                        <span title="Gestion de la position des Mécanismes.">Mécanismes: </span>
                        <?php
                        echo '<select name="select-meca-dep" id="select-meca-dep" onchange="Pinceau.miseAJour (\'Speciaux\', \'meca-dep\')">';
                        for ($m=0; $m<count($mecanisme); $m++ )
                        {
                            echo '<option value="'.$mecanisme[$m]["meca_cod"].'">'.$mecanisme[$m]["meca_nom"].'</option>';
                        }
                        echo '</select>';
                        ?>

                       <br/>
                    </td>
                </tr>
            </table>
            <em>Notes :<br/> - L’ordre de superposition des couches graphiques est le suivant : fond < décor < mur <
                décor superposé.</em><br/>
            <em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Donc seul un « décor superposé » peut apparaître par dessus un
                mur.</em><br/>
            <em> - L’outil « Annulation » replace les éléments sous le pinceau à leur état initial (depuis la dernière
                sauvegarde)</em><br/>
            <em> - Pour enlever un décor (resp. mur), il faut sélectionner le premier décor (resp. mur) de la liste et
                l’appliquer sur le(s) décor(s) ) enlever.</em><br/>
            <em> - Les outils spéciaux Creusable, Tangibles et Illusion ne s’appliquent qu’aux murs. Vous pouvez utiliser la
                brosse spéciale dédiée.</em><br/>
            <em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Il n’est pas possible de supprimer un fond.</em><br/>
        </div>

        <div id="ea-liste-container" class="bordiv" style="display:none;">
            <button onclick="Etage.nettoyer_ea_list()">Vider</button>
            <b><u>Listes des positions</u></b>
            <button onclick="copyToClipboard('#ea-liste-cases')">Copier</button> :
            <div id="ea-liste-cases" ></div>
        </div>



        <div id="vueEtage"></div>
        <script type="text/javascript" src="../scripts/admin_etage_code.js?v=<?php echo $__VERSION; ?>"></script>
        <script type="text/javascript" src="../scripts/admin_etage_pinceau.js?v=<?php echo $__VERSION; ?>"></script>
        <script type="text/javascript" src="../scripts/manip_css.js?v=<?php echo $__VERSION; ?>"></script>
        <script type="text/javascript" src="admin_etage_data.js.php?num_etage=<?php echo $admin_etage; ?>&v=<?php echo $__VERSION; ?>"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                console.log("Lancement des JS");
                Pinceau.dessineRadar();
                console.log("dessine radar ok");
                Pinceau.dessineListe(Fonds, document.getElementById('pinceauFonds'));
                console.log("dessine liste 1");
                Pinceau.dessineListe(Decors, document.getElementById('pinceauDecors'));
                console.log("dessine liste 2");
                Pinceau.dessineListe(DecorsDessus, document.getElementById('pinceauDecorsDessus'));
                console.log("dessine liste 3");
                Pinceau.dessineListe(Murs, document.getElementById('pinceauMurs'));
                console.log("dessine liste 4");
                Etage.Dessine();
                console.log("etage dessine");

            });


        </script>
        <form name="plateau" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"
              onsubmit="Etage.ecrireModifs();">
            <input type="hidden" name="admin_etage" value="<?php echo $admin_etage; ?>"/>
            <input type="hidden" name="methode" value="valide"/>
            <input type="hidden" name="modifs" value=""/>
            <?php
            for ($ea=0; $ea<count($effet_auto); $ea++ )
            {
                echo "<input type='hidden' name=\"ea-modif-cases-".$effet_auto[$ea]["fonc_cod"]."\" id=\"ea-modif-cases-".$effet_auto[$ea]["fonc_cod"]."\" value=\"0\">";
                echo "<input type='hidden' name=\"ea-liste-cases-".$effet_auto[$ea]["fonc_cod"]."\" id=\"ea-liste-cases-".$effet_auto[$ea]["fonc_cod"]."\" value=\"".$effet_auto[$ea]["pos_cods"]."\">";
            }

            for ($qa=0; $qa<count($quete_auto); $qa++ )
            {
                echo "<input type='hidden' name=\"qa-modif-cases-".$quete_auto[$qa]["aquete_cod"]."\" id=\"qa-modif-cases-".$quete_auto[$qa]["aquete_cod"]."\" value=\"0\">";
                echo "<input type='hidden' name=\"qa-liste-cases-".$quete_auto[$qa]["aquete_cod"]."\" id=\"qa-liste-cases-".$quete_auto[$qa]["aquete_cod"]."\" value=\" ".$quete_auto[$qa]["pos_cods"].",\">";
            }

            for ($m=0; $m<count($mecanisme); $m++ )
            {
                echo "<input type='hidden' name=\"meca-modif-cases-".$mecanisme[$m]["meca_cod"]."\" id=\"meca-modif-cases-".$mecanisme[$m]["meca_cod"]."\" value=\"0\">";
                echo "<input type='hidden' name=\"meca-liste-cases-".$mecanisme[$m]["meca_cod"]."\" id=\"meca-liste-cases-".$mecanisme[$m]["meca_cod"]."\" value=\" ".$mecanisme[$m]["pos_cods"].",\">";
            }
            ?>
            <center><input type="submit" class="test" value="Modifier !"></center>
        </form>


        <?php break;

    case "valide":

        // on traite d'abord les EA ======================================
        $nb_modif_ea = 0 ;
        foreach ($_REQUEST as $k => $v) {
            if ((substr($k, 0,15) == "ea-modif-cases-") && ($v=="1") && isset($_REQUEST["ea-liste-cases-".substr($k, 15)])) {
                $nb_modif_ea ++ ;

                $req_ea= "update fonction_specifique set fonc_trigger_param=jsonb_set(fonc_trigger_param::jsonb, '{\"fonc_trig_pos_cods\"}', '\"".$_REQUEST["ea-liste-cases-".substr($k, 15)]."\"') where fonc_cod=:fonc_cod";
                $stmt = $pdo->prepare($req_ea);
                $stmt = $pdo->execute(array(":fonc_cod" => substr($k, 15)), $stmt);
            }
        }

        // on traite ensuite les QA ======================================
        $nb_modif_qa = 0 ;
        foreach ($_REQUEST as $k => $v) {
            if ((substr($k, 0,15) == "qa-modif-cases-") && ($v=="1") && isset($_REQUEST["qa-liste-cases-".substr($k, 15)])) {
                $nb_modif_qa ++ ;
                $aquete_cod = substr($k, 15);

                $aquete = new aquete();
                $aqetape = new aquete_etape();
                $aquete->charge($aquete_cod);
                $aqetape->charge($aquete->aquete_etape_cod);
                $pos_cod_liste = explode( ",", $_REQUEST["qa-liste-cases-".substr($k, 15)] );
                $aqetape->set_interaction_positions($pos_cod_liste);
            }
        }

        // on traite ensuite les MECA ======================================
        $nb_modif_meca = 0 ;
        foreach ($_REQUEST as $k => $v) {
            if ((substr($k, 0,17) == "meca-modif-cases-") && ($v=="1") && isset($_REQUEST["meca-liste-cases-".substr($k, 17)])) {
                $nb_modif_meca ++ ;
                $meca_cod = substr($k, 17);
                $meca = new meca();
                $meca->charge($meca_cod);
                $pos_cod_liste = explode( ",", $_REQUEST["meca-liste-cases-".substr($k, 17)] );
                $meca->set_positions($pos_cod_liste);

            }
        }

        //echo "<pre>"; print_r($_REQUEST); die();

        $modifs      = $_REQUEST['modifs'];
        $admin_etage = get_request_var('admin_etage', '');
        $erreur      = false;
        if (!isset($admin_etage) || $admin_etage == '')
        {
            echo "<p>Erreur ! Étage non défini.</p>";
            $erreur = true;
        }
        if (empty($modifs) && ($nb_modif_ea==0) && ($nb_modif_qa==0) && ($nb_modif_meca==0))
        {
            echo "<p>Aucune modification enregistrée</p>";
            $erreur = true;
        }



        // validation des modifs Version 2: preg_match ne supporte qu'un taille limité pour la chaine d'entrée
        $split=explode(";",$modifs);
        $schema = "/^\d+\|([dmsfpvctigba]=[-0123456789]+,)/i";
        foreach ($split as $s) {
            if ($s!=""){
                if (!preg_match($schema, $s)) {
                    echo "<p>Erreur ! Modifications non valides <br />-- debug --$modifs</p>";
                    $erreur = true;
                    break;
                }
            }
        }

        // validation des modifs, de la forme 1234|d=1,f=2,m=0;1235|d=0,m=999;
        //$schema = "/(\d+\|([dmsfpvct]=\d+,)+;)*/i";
        //if (!preg_match($schema, $modifs)) {
        //    echo "<p>Erreur ! Modifications non valides <br />-- debug --$modifs</p>";
        //    $erreur = true;
        //}
        if (!$erreur && !empty($modifs)) {
            $tab_modifs = explode(';', $modifs);
            $cpt_fond = 0;
            $cpt_mur = 0;
            $cpt_dec = 0;
            $cpt_des = 0;
            $cpt_pvp = 0;
            $cpt_pas = 0;
            $cpt_tan = 0;
            $cpt_alu = 0;
            $cpt_arn = 0;
            $cpt_cre = 0;
            $cpt_ter = 0;
            $cpt_dep = 0;
            $cpt_erreur = 0;
            // Parcours de toutes les cases modifiées
            foreach ($tab_modifs as $infos_case) {
                $tab_infos_case = explode('|', $infos_case);
                $case = $tab_infos_case[0];
                if ($case == "") continue;
                $req_case = "select pos_type_aff, coalesce(mur_type, -1) as mur_type, pos_decor, pos_decor_dessus, coalesce(pos_modif_pa_dep, 0) as pa_dep, coalesce(pos_ter_cod, 0) as ter_cod
					from positions left outer join murs on mur_pos_cod = pos_cod
					where pos_cod = :case AND pos_etage = :admin_etage";
                $stmt = $pdo->prepare($req_case);
                $stmt = $pdo->execute(array(":case" => $case,
                    ":admin_etage" => $admin_etage), $stmt);

                if ($result = $stmt->fetch()) {
                    $mur_ancien = $result['mur_type'];
                    $modifs_case = explode(',', $tab_infos_case[1]);
                    $set_case = array();    // on regroupe les changements qui s’effectuent sur une même case
                    $set_mur = array();        // on regroupe les changements qui s’effectuent sur un même mur

                    foreach ($modifs_case as $une_modif) {
                        $donnees = explode('=', $une_modif);
                        $type = $donnees[0];
                        if ($type == "") continue;
                        $valeur = $donnees[1];
                        switch ($type) {
                            case 'f': // fonds
                                $set_case[] = "pos_type_aff = $valeur";
                                $cpt_fond++;
                                break;
                            case 'd': // décors
                                $set_case[] = "pos_decor = $valeur";
                                $cpt_dec++;
                                break;
                            case 's': // décors superposés
                                $set_case[] = "pos_decor_dessus = $valeur";
                                $cpt_des++;
                                break;
                            case 'p': // passages autorisés
                                $set_case[] = "pos_passage_autorise = $valeur";
                                $cpt_pas++;
                                break;
                            case 'v': // pvp autorisé
                                $set_case[] = "pos_pvp = '" . (($valeur) ? 'O' : 'N') . "'";
                                $cpt_pvp++;
                                break;
                            case 'a': // entree arène
                                $set_case[] = "pos_entree_arene = '" . (($valeur) ? 'O' : 'N') . "'";
                                $cpt_arn++;
                                break;
                            case 'm': // murs
                                $req = '';
                                if ($valeur == 0)    // suppression de mur
                                    $req = "delete from murs where mur_pos_cod = $case";
                                elseif ($mur_ancien == -1)    // ajout de mur
                                    $req = "insert into murs (mur_pos_cod, mur_type, mur_tangible) values ($case, $valeur, 'O') ";
                                else                // et modif de mur
                                    $set_mur[] = "mur_type = $valeur";
                                $cpt_mur++;
                                if ($req !== '')
                                    $pdo->query($req);
                                break;
                            case 'c': // mur creusable
                                $set_mur[] = "mur_creusable = '" . (($valeur) ? 'O' : 'N') . "'";
                                $cpt_cre++;
                                break;
                            case 't': // mur tangible
                                $set_mur[] = "mur_tangible = '" . (($valeur) ? 'O' : 'N') . "'";
                                $cpt_tan++;
                                break;
                            case 'i': // mur illusion
                                $set_mur[] = "mur_illusion = '" . (($valeur) ? 'O' : 'N') . "'";
                                $cpt_alu++;
                                break;
                            case 'g': // ground = terrain
                                $set_case[] = "pos_ter_cod = " . (int)$valeur ;
                                $cpt_ter++;
                                break;
                            case 'b': // Bonus/malus de déplacment (modif pa)
                                $set_case[] = "pos_modif_pa_dep = " . (int)$valeur ;
                                $cpt_dep++;
                                break;
                        }
                    }
                    // vérifier si la case est à déjà un mécanisme activeé, si c'est le cas il faut traiter les cases "base" du mécanisme à la place
                    $req ="select count(*)as count from meca_position where pmeca_pos_cod=$case and pmeca_actif=1 ";
                    $stmt = $pdo->query($req, PDO::FETCH_ASSOC);
                    $result = $stmt->fetch();

                    if ($result["count"]<=0) {
                        // Traitement sur les cases normales seulement si elles n'ont pas été activées par un mécanisme!
                        $set_req = implode(',', $set_case);
                        if ($set_req !== "") {
                            $req = "update positions set $set_req where pos_cod = $case";
                            $pdo->query($req);
                        }
                        $set_req = implode(',', $set_mur);
                        if ($set_req !== "") {
                            $req = "update murs set $set_req where mur_pos_cod = $case";
                            $pdo->query($req);
                        }
                    }

                    // ensuite on traite les modifications sur les mécanismes (sauvegarde de leur nouvelle base)
                    $set_case = array_filter($set_case, function($item) { return in_array(trim(substr($item, 0, strpos($item,"="))), ["pos_type_aff","pos_decor","pos_decor_dessus","pos_passage_autorise","pos_ter_cod","pos_modif_pa_dep"]);} );
                    array_walk($set_case, function (&$item) { $item = "pmeca_base_".$item; });
                    $set_req = implode(',', $set_case);
                    if ($set_req !== "") {
                        $req = "update meca_position set $set_req where pmeca_pos_cod = $case";
                        $pdo->query($req);
                    }

                    $set_mur = array_filter($set_mur, function($item) { return in_array(trim(substr($item, 0, strpos($item,"="))), ["mur_type","mur_tangible","mur_illusion"]);} );
                    array_walk($set_mur, function (&$item) { $item = "pmeca_base_".$item; });
                    $set_req = implode(',', $set_mur);
                    if ($set_req !== "") {
                        $req = "update meca_position set $set_req where pmeca_pos_cod = $case";
                        $pdo->query($req);
                    }


                } else {
                    $cpt_erreur++;
                }
            }
            echo "<p>Modifications effectuées ! Résumé :<br />
				$cpt_mur murs modifiés<br />
				$cpt_dec décors modifiés<br />
				$cpt_fond fonds modifiés<br />
				$cpt_des décors superposés modifiés<br />
				$cpt_pas passages autorisés modifiés<br />
				$cpt_pvp pvp autorisés modifiés<br />
				$cpt_arn entrées d'arènes modifiées<br />
				$cpt_cre murs creusables modifiés<br />
				$cpt_tan murs tangibles modifiés<br />
				$cpt_alu murs illusions modifiés<br />
				$cpt_ter terrains modifiés<br />
				$cpt_dep bonus/malus de déplacement modifiés<br />
				$cpt_erreur erreurs détectées<br /></p>";

            $req = "select init_automap($admin_etage) ";
            $stmt = $pdo->query($req);
            echo "<p>Changements validés dans les automaps.</p>";
        }
        if ($nb_modif_ea>0) echo "<p>Modifications sur les positions d'EA : $nb_modif_ea<br /></p>";
        if ($nb_modif_qa>0) echo "<p>Modifications sur les positions de QA : $nb_modif_qa<br /></p>";
        if ($nb_modif_meca>0) echo "<p>Modifications sur les positions de mécanismes : $nb_modif_meca <br /></p>";
        break;
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
