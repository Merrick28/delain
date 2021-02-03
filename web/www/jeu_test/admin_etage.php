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
	<a href='modif_etage3.php'>Créer / modifier un étage (caractéristiques générales)</a><br />
	<a href='modif_etage3bis.php'>Créer / modifier les lieux</a><br />
	<a href='modif_etage3ter.php'>Creation multiple de lieux</a><br />
	<a href='modif_etage3quater.php'>Dupliquer/Supprimer un étage</a><br />
	<a href='modif_etage.php'>Autres outils</a></td></tr></table>";

//cahrger les type de terrains
$pdo = new bddpdo();
$req_m_terrain= "select ter_cod, ter_nom from terrain order by ter_nom";
$stmt_m_terrain = $pdo->query($req_m_terrain);
$terrains = $stmt_m_terrain->fetchAll(PDO::FETCH_ASSOC);

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
                    <td class="bordiv">
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
                        echo '<option value="0"'.($ter_cod == 0 ? ' selected ' : '').'>Sans terrain spécifique</option>';
                        for ($t=0; $t<count($terrains); $t++ )
                        {
                            echo '<option value="'.$terrains[$t]["ter_cod"].'"'.($ter_cod == $terrains[$t]["ter_cod"] ? ' selected ' : '').'>'.$terrains[$t]["ter_nom"].'</option>';
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
                        echo '<option value="0"'.($ter_cod == 0 ? ' selected ' : '').'>Sans terrain spécifique</option>';
                        for ($t=0; $t<count($terrains); $t++ )
                        {
                            echo '<option value="'.$terrains[$t]["ter_cod"].'"'.($ter_cod == $terrains[$t]["ter_cod"] ? ' selected ' : '').'>'.$terrains[$t]["ter_nom"].'</option>';
                        }
                        echo '</select>';
                        ?>
                       <br/>

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
            <em> - Les outils spéciaux Creusable et Tangibles ne s’appliquent qu’aux murs. Vous pouvez utiliser la
                brosse spéciale dédiée.</em><br/>
            <em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Il n’est pas possible de supprimer un fond.</em><br/>
        </div>

        <div id="vueEtage"></div>
        <script type="text/javascript" src="../scripts/admin_etage_code.js"></script>
        <script type="text/javascript" src="../scripts/admin_etage_pinceau.js"></script>
        <script type="text/javascript" src="../scripts/manip_css.js"></script>
        <script type="text/javascript" src="admin_etage_data.js.php?num_etage=<?php echo $admin_etage; ?>"></script>
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
            <center><input type="submit" class="test" value="Modifier !"></center>
        </form>
    <hr>
    <b><u>IMPORTANT</u></b>: En cas de modification, s'il y a des spécifications liées aux types de terrain, elles doivent-être ré-appliquées manuellement (<a target="_blank" href="/jeu_test/modif_etage7.php?pos_etage=<?php echo $admin_etage; ?>">Type de terrain</a>)!
        <?php break;

    case "valide":
        $modifs      = $_REQUEST['modifs'];
        $admin_etage = get_request_var('admin_etage', '');
        $erreur      = false;
        if (!isset($admin_etage) || $admin_etage == '')
        {
            echo "<p>Erreur ! Étage non défini.</p>";
            $erreur = true;
        }
        if (empty($modifs))
        {
            echo "<p>Aucune modification enregistrée</p>";
            $erreur = true;
        }

        // validation des modifs Version 2: preg_match ne supporte qu'un taille limité pour la chaine d'entrée
        $split=explode(";",$modifs);
        $schema = "/^\d+\|([dmsfpvctgb]=\d+,)/i";
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
        if (!$erreur) {
            $tab_modifs = explode(';', $modifs);
            $cpt_fond = 0;
            $cpt_mur = 0;
            $cpt_dec = 0;
            $cpt_des = 0;
            $cpt_pvp = 0;
            $cpt_pas = 0;
            $cpt_tan = 0;
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
                } else
                    $cpt_erreur++;
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
				$cpt_ter terrains modifiés<br />
				$cpt_dep bonus/malus de déplacement modifiés<br />
				$cpt_erreur erreurs détectées<br /></p>";

            $req = "select init_automap($admin_etage) ";
            $stmt = $pdo->query($req);
            echo "<p>Changements validés dans les automaps.</p>";
        }
        break;
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
