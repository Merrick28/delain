<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
include_once '../includes/tools.php';

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');


$contenu_page = '';

define('APPEL', 1);
include "blocks/_test_droit_modif_etage.php";

//echo "<pre>"; print_r($_REQUEST); die();
if (!isset($_REQUEST["pos_etage"]) && isset($_REQUEST["admin_etage"]) && $_REQUEST["admin_etage"]!=0) $pos_etage = 1*$_REQUEST["admin_etage"] ; else $pos_etage = 1*$_REQUEST['pos_etage'] ;


if ($erreur == 0)
{
    $pdo = new bddpdo();

    //=======================================================================================
    // == Main
    //=======================================================================================
    // On est admin ici, on a les droits sur les quetes
    // Traitement des paramètres

    //-- traitement des actions=======================================================================================
    //print_r($_REQUEST);
    if(isset($_POST['methode']) && ($pos_etage>1) && isset($_POST['methode']))
    {
        //print_r($_POST);

        if ($_POST['methode']=="editer_meca" ) {

            $meca_nom = $_POST["nom"] == "" ? "Inconnu" : $_POST["nom"] ;
            $bindings = array(
                ":meca_nom"                     => $meca_nom,
                ":meca_type"                    => $_POST["meca_type"] == "" ? "G" : $_POST["meca_type"],
                ":meca_pos_etage"               => $pos_etage,
                ":meca_pos_type_aff"            => ($_POST["type_aff"] == "" || $_POST["type_aff"] == "-1") ? NULL : $_POST["type_aff"],
                ":meca_pos_decor"               => (int)$_POST["pos_decor"] == 0 ? NULL : $_POST["pos_decor"],
                ":meca_pos_decor_dessus"        => (int)$_POST["decor_dessus"] == 0 ? NULL : $_POST["decor_dessus"],
                ":meca_pos_passage_autorise"    => $_POST["passage_autorise"] == "-1" ? NULL : ($_POST["passage_autorise"] == "0" ? 1 : 0),
                ":meca_pos_modif_pa_dep"        => $_POST["modif_pa_dep"] == "" ? NULL : $_POST["modif_pa_dep"],
                ":meca_pos_ter_cod"             => $_POST["pos_ter_cod"] == "-1" ? NULL : $_POST["pos_ter_cod"],
                ":meca_mur_type"                => (int)$_POST["mur_type"] == 0 ? NULL : $_POST["mur_type"],
                ":meca_mur_tangible"            => $_POST["mur_tangible"] == "-1" ? NULL : ($_POST["mur_tangible"] == "0" ? 'N': 'O'),
                ":meca_mur_illusion"            => $_POST["mur_illusion"] == "-1" ? NULL: ($_POST["mur_illusion"] == "0" ? 'N': 'O'),
            );

            if ($_POST['meca_cod']>0) {

                // désactiver un mécanisme avant de le modifier!
                $req = "SELECT meca_declenchement(pmeca_meca_cod,-1,pmeca_pos_cod,null) from meca_position where pmeca_meca_cod=:meca_cod " ;
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":meca_cod"  => $_POST['meca_cod'] ), $stmt);

                $req = "UPDATE meca SET meca_nom=:meca_nom, meca_type=:meca_type, meca_pos_etage=:meca_pos_etage, meca_pos_type_aff=:meca_pos_type_aff, 
                                        meca_pos_decor=:meca_pos_decor, meca_pos_decor_dessus=:meca_pos_decor_dessus, meca_pos_passage_autorise=:meca_pos_passage_autorise, 
                                        meca_pos_modif_pa_dep=:meca_pos_modif_pa_dep, meca_pos_ter_cod=:meca_pos_ter_cod, meca_mur_type=:meca_mur_type, 
                                        meca_mur_tangible=:meca_mur_tangible,meca_mur_illusion=:meca_mur_illusion WHERE meca_cod=:meca_cod ";

                $bindings = array_merge($bindings, array( ":meca_cod" => $_POST['meca_cod']));

                $log =date("d/m/y - H:i") . $perso->perso_nom . " (compte $compt_cod) modifie des mécanismes d'étage : $pos_etage\n";
                $message = "Modification du mécanisme: #".  $_POST['meca_cod']." (".$meca_nom.")";

            } else {

                $req = "INSERT INTO meca (meca_nom, meca_type, meca_pos_etage, meca_pos_type_aff, meca_pos_decor, meca_pos_decor_dessus, meca_pos_passage_autorise, meca_pos_modif_pa_dep, meca_pos_ter_cod, meca_mur_type, meca_mur_tangible, meca_mur_illusion)
                            VALUES (:meca_nom, :meca_type, :meca_pos_etage, :meca_pos_type_aff, :meca_pos_decor, :meca_pos_decor_dessus, :meca_pos_passage_autorise, :meca_pos_modif_pa_dep, :meca_pos_ter_cod, :meca_mur_type, :meca_mur_tangible,:meca_mur_illusion)";

                $log =date("d/m/y - H:i") . $perso->perso_nom . " (compte $compt_cod) modifie des mécanismes d'étage : $pos_etage\n";
                $message = "Creation du mécanisme: ". $meca_nom;
            }

            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute($bindings, $stmt);

            writelog($log . $message, 'lieux_etages');
            echo nl2br($message);
            echo "<hr>";

            unset($_POST['meca_cod'] );
            unset($_REQUEST['meca_cod'] );

        } else if ($_POST['methode']=="supprimer_meca") {

                $req = "DELETE FROM meca where meca_cod=:meca_cod " ;
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":meca_cod"  => $_POST['meca_cod'] ), $stmt);

                $log =date("d/m/y - H:i") . $perso->perso_nom . " (compte $compt_cod) supprime mécanismes d'étage : $pos_etage\n";
                $message = "Suppression du mécanisme: #". $_POST['meca_cod'];

                writelog($log . $message, 'lieux_etages');
                echo nl2br($message);
                echo "<hr>";
        } else if ($_POST['methode']=="activer_meca") {

                $req = "SELECT meca_declenchement(pmeca_meca_cod,1,pmeca_pos_cod,null) from meca_position where pmeca_meca_cod=:meca_cod " ;
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":meca_cod"  => $_POST['meca_cod'] ), $stmt);

                $log =date("d/m/y - H:i") . $perso->perso_nom . " (compte $compt_cod) active mécanismes d'étage : $pos_etage\n";
                $message = "Activation du mécanisme: #". $_POST['meca_cod'];

                writelog($log . $message, 'lieux_etages');
                echo nl2br($message);
                echo "<hr>";
        } else if ($_POST['methode']=="desactiver_meca") {

                $req = "SELECT meca_declenchement(pmeca_meca_cod,-1,pmeca_pos_cod,null) from meca_position where pmeca_meca_cod=:meca_cod " ;
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":meca_cod"  => $_POST['meca_cod'] ), $stmt);

                $log =date("d/m/y - H:i") . $perso->perso_nom . " (compte $compt_cod) désactive mécanismes d'étage : $pos_etage\n";
                $message = "Désactivation du mécanisme: #". $_POST['meca_cod'];

                writelog($log . $message, 'lieux_etages');
                echo nl2br($message);
                echo "<hr>";
        }
    }

    //=======================================================================================
    echo '  <link rel="stylesheet" type="text/css"  href="style_vue.php?num_etage='.$pos_etage.'&source=fichiers" title="essai"> 
            <script type="text/javascript" src="../scripts/admin_etage_code.js?v='.$__VERSION.'"></script>
            <script type="text/javascript" src="../scripts/admin_etage_meca.js?v='.$__VERSION.'"></script>
            <script type="text/javascript" src="../scripts/manip_css.js?v='.$__VERSION.'"></script>            
            <script type="text/javascript" src="admin_etage_data.js.php?num_etage='.$pos_etage.'&v='.$__VERSION.'"></script>
            <script type="text/javascript">
            $(document).ready(function () {
                console.log("Lancement des JS");
                Pinceau.dessineListe(Fonds, document.getElementById(\'pinceauFonds\'));
                console.log("dessine liste 1");
                Pinceau.dessineListe(Decors, document.getElementById(\'pinceauDecors\'));
                console.log("dessine liste 2");
                Pinceau.dessineListe(DecorsDessus, document.getElementById(\'pinceauDecorsDessus\'));
                console.log("dessine liste 3");
                Pinceau.dessineListe(Murs, document.getElementById(\'pinceauMurs\'));
                console.log("dessine liste 4");
            });

        </script>';

    echo "On trouve ici des mécaniques qui sont définies sur un étage.<br>
          Un mécanisme définit les caractéristiques d'une case (fond, decors, murs, etc..), il pourra être utilisé pour venir remplacer (temporairement) les \"vrais\" caractéristiques (celles de base) d'une case de l'étage.<br>
          Ce remplacement est réalisé à l'aide des EA d'étage, des QA ou encore grace à d'autres mécanismes.<br>
        <br><br>";


    echo '  <TABLE width="80%" align="center">
            <TR>
            <TD>
            <form method="post">
            Editer les mécaniques d\'un étage:<select onchange="this.parentNode.submit();" name="pos_etage"><option value="0">Sélectionner l\'étage</option>';

    if (!isset($pos_etage)) $pos_etage = '';
    echo $html->etage_select($pos_etage);

    echo '  </select>
            </form></TD>
            </TR>
            </TABLE>';
    echo "<HR>";

    echo '<strong>DEFINITION D’UN MECANISME:</strong><br><br>';

    if ($pos_etage==0) {
        echo 'Sélectionnez l’étage!';
    } else {

        $meca = new meca();
        if (isset($_REQUEST["meca_cod"]) && $_REQUEST["meca_cod"]>0) { $meca->charge($_REQUEST["meca_cod"]); }

        echo "<div class=\"bordiv\">
            <table>
                <tr>
                    <td><strong>Fonds</strong></td>
                    <td><strong>Décors</strong></td>
                    <td><strong>Murs</strong></td>
                    <td><strong>Décors superposés</strong></td>

                </tr>
                <tr  style='vertical-align: top;'>
                    <td id='pinceauFonds' class='bordiv'></td>
                    <td id='pinceauDecors' class='bordiv'></td>
                    <td id='pinceauMurs' class='bordiv'></td>
                    <td id='pinceauDecorsDessus' class='bordiv'></td>
                </tr>
            </table></div>
        ";

        $req_m_terrain = "select ter_cod, ter_nom from terrain where ter_cod > 0 order by ter_nom";
        $stmt_m_terrain = $pdo->query($req_m_terrain);
        $terrains = $stmt_m_terrain->fetchAll(PDO::FETCH_ASSOC);

        echo "
        <form method='post' action='admin_meca_etage.php'>
                    <input type='hidden' name='methode' value='editer_meca'>
                    <input type='hidden' name='pos_etage' value='$pos_etage'>
                    <input type='hidden' name='meca_cod' value='{$meca->meca_cod}'>
                    <input type='hidden' id='meca-Fond' name='type_aff' value='".$meca->meca_pos_type_aff."'>
                    <input type='hidden' id='meca-Decor' name='pos_decor' value='".$meca->meca_pos_decor."'>
                    <input type='hidden' id='meca-Mur' name='mur_type' value='".$meca->meca_mur_type."'>
                    <input type='hidden' id='meca-DecorDessus' name='decor_dessus' value='".$meca->meca_pos_decor_dessus."'>
                    
            <br>Nom du mécanisme: <input type='text' name='nom'  size='40' value='".$meca->meca_nom."'><br>
            <br>Type de mécanisme:";

        echo '<select name="meca_type" id="select-type">';
        echo '<option '.($meca->meca_type=="G" ? "selected" : "").' value="G">Grappe</option>';
        echo '<option '.($meca->meca_type=="I" ? "selected" : "").' value="I">Individuel</option>';
        echo '</select> (<em style="font-size: 10px;">Si grappe est choisi, à l’activation de ce mécanisme, les cases l’utilisant seront toutes activées. Sinon c’est seulement la case du perso</em>)<br>';

        $chkp0 = "";  $chkp1 = "" ;   $chkp2 = "" ;  if ($meca->meca_pos_passage_autorise=="1") {$chkp1 = " checked ";} else if ($meca->meca_pos_passage_autorise=="0") {  $chkp2 = " checked " ; } else {  $chkp0 = " checked " ; };
        $chkmt0 = "";  $chkmt1 = "" ;   $chkmt2 = "" ;  if ($meca->meca_mur_tangible=="N") {$chkmt1 = " checked ";} else if ($meca->meca_mur_tangible=="O") {  $chkmt2 = " checked " ; } else {  $chkmt0 = " checked " ; };
        $chkmi0 = "";  $chkmi1 = "" ;   $chkmi2 = "" ;  if ($meca->meca_mur_illusion=="N") {$chkmi1 = " checked ";} else if ($meca->meca_mur_illusion=="O") {  $chkmi2 = " checked " ; } else {  $chkmi0 = " checked " ; };

        echo "<div style='float: left; display: inline-flex;'>Pattern:&nbsp;&nbsp;&nbsp; 
                        <div id='pattern-Fond' data-pattern='".($meca->meca_pos_type_aff=="" ? -1 : $meca->meca_pos_type_aff)."' class='caseVue caseFond pinceau ".($meca->meca_pos_type_aff=="" ? "" : "v".$meca->meca_pos_type_aff)."'>
                            <div id='pattern-Decor' data-pattern='".(int)$meca->meca_pos_decor."' class='caseVue ".($meca->meca_pos_decor=="" ? "" : "decor".$meca->meca_pos_decor)."'>
                                <div id='pattern-Mur' data-pattern='".(int)$meca->meca_mur_type."' class='caseVue ".($meca->meca_mur_type=="" ? "" : "mur_".$meca->meca_mur_type)."'>                                
                                    <div id='pattern-DecorDessus' data-pattern='".(int)$meca->meca_pos_decor_dessus."' class='caseVue ".($meca->meca_pos_decor_dessus=="" ? "" : "decor".$meca->meca_pos_decor_dessus)."'>                                
                                    </div>
                                </div>
                            </div>
                        </div>
            </div><br/><br>
            <table>
            <tr><td>Passage autorisé:</td><td><input $chkp0 type='radio' name='passage_autorise' value='-1'>Base</td><td>&nbsp;|&nbsp;<input $chkp1 type='radio' name='passage_autorise' value='0'>Autorisé</td><td>&nbsp;|&nbsp;<input $chkp2 type='radio' name='passage_autorise' value='1'>Interdit<td><tr></tr>
            <tr><td>Mur tangible:</td><td><input $chkmt0 type='radio' name='mur_tangible' value='-1'>Base</td><td>&nbsp;|&nbsp;<input $chkmt1 type='radio' name='mur_tangible' value='0'>Tangible</td><td>&nbsp;|&nbsp;<input $chkmt2 type='radio' name='mur_tangible' value='1'>Intangible<td><tr></tr>
            <tr><td>Mur illusion:</td><td><input $chkmi0 type='radio' name='mur_illusion' value='-1'>Base</td><td>&nbsp;|&nbsp;<input $chkmi1 type='radio' name='mur_illusion' value='0'>Infranchissable</td><td>&nbsp;|&nbsp;<input $chkmi2 type='radio' name='mur_illusion' value='1'>Illusion<td><tr>
            </table>
            Terrain: ";
        echo '<select name="pos_ter_cod" id="select-terrain">';
        echo '<option '.($meca->meca_pos_ter_cod=="" ? "selected ": "").' value="-1">Terrain Inchangé (base)</option>';
        echo '<option '.($meca->meca_pos_ter_cod=="0" ? "selected ": "").'value="0">Sans terrain spécifique</option>';
        for ($t = 0; $t < count($terrains); $t++)
        {
            echo '<option '.($meca->meca_pos_ter_cod==$terrains[$t]["ter_cod"] ? "selected ": "").' value="' . $terrains[$t]["ter_cod"] . '">' . $terrains[$t]["ter_nom"] . '</option>';
        }
        echo '</select> Modificateur de PA: <input name="modif_pa_dep" id="modif_pa_dep" value="'.$meca->meca_pos_modif_pa_dep.'" type="text" size="3"/> (<em style="font-size: 10px;">laisser vide pour garder le modificateur de base</em>)';

        $bouton = ($meca->meca_cod > 0) ? "Modifier le mécanisme!" : "créer le mécanisme!";
        echo "<br><br><input type='submit' class='test' value='{$bouton}'/>";
        if ($meca->meca_cod > 0)
        {
            echo '&nbsp;&nbsp;&nbsp;&nbsp;ou&nbsp;<a href="admin_meca_etage.php?pos_etage='.$pos_etage.'">Créer un nouveau</a><br>';
        }
        echo "<br><br><b><u>ATTENTION</u></b>: Modifier un mécanisme existant entraine sa désactivation.";
        echo "<br></form>";

        echo "<HR>Liste des mécanismes de l'étage:";

        echo " <form name='supprimermeca' method='post' action='admin_meca_etage.php'>
                   <input type='hidden' name='methode' value='supprimer_meca'>
                   <input type='hidden' name='pos_etage' value='{$pos_etage}'>
                   <input type='hidden' name='meca_cod' value=''>
               </form>
               <form name='activermeca' method='post' action='admin_meca_etage.php'>
                   <input type='hidden' name='methode' value='activer_meca'>
                   <input type='hidden' name='pos_etage' value='{$pos_etage}'>
                   <input type='hidden' name='meca_cod' value=''>
               </form>
               <form name='desactivermeca' method='post' action='admin_meca_etage.php'>
                   <input type='hidden' name='methode' value='desactiver_meca'>
                   <input type='hidden' name='pos_etage' value='{$pos_etage}'>
                   <input type='hidden' name='meca_cod' value=''>
               </form>
               <script language='javascript'>
                   function supprimermeca(code)
                   {
                       document.supprimermeca.meca_cod.value = code;
                       document.supprimermeca.submit();
                   }
                   function activermeca(code)
                   {
                       document.activermeca.meca_cod.value = code;
                       document.activermeca.submit();
                   }
                   function desactivermeca(code)
                   {
                       document.desactivermeca.meca_cod.value = code;
                       document.desactivermeca.submit();
                   }
               </script>
               <table>";

        $req_meca  = "select * from meca left join terrain on ter_cod=meca_pos_ter_cod where meca_pos_etage = ? order by meca_nom, meca_cod";
        $stmt = $pdo->prepare($req_meca);
        $stmt = $pdo->execute(array($pos_etage), $stmt);
        echo "<tr><td></td><td width='200px;'><b>Nom</b></td><td><b>Type</b></td><td><b>Pattern</b></td><td><b>Passage</b></td><td><b>Mur Tangible</b></td><td><b>Mur Illusion</b></td><td><b>Terrain</b></td><td><b>Modif. PA</b></td><td><b>Etat actuel</b></td><td><b>Activation/Désactivation</b></td><td></td></tr>";
        while ($result = $stmt->fetch())
        {
            // calculer l'état du mecanisme
            $req_pmeca  = "select count(*) nb_count, sum(pmeca_actif) as nb_actif from meca_position where pmeca_meca_cod= ? ";
            $stmt2 = $pdo->prepare($req_pmeca);
            $stmt2 = $pdo->execute(array($result['meca_cod']), $stmt2);
            $result2 = $stmt2->fetch();

            echo "
            <tr>
                <td><a href=\"?pos_etage={$pos_etage}&meca_cod={$result['meca_cod']}\">Modifier</a></td>
                <td>{$result['meca_nom']}</td>
                <td><div class='caseVue caseFond pinceau ".($result['meca_pos_type_aff']=="" ? "" : "v".$result['meca_pos_type_aff'])."'>
                            <div class='caseVue ".($result['meca_pos_decor']=="" ? "" : "decor".$result['meca_pos_decor'])."'>
                                <div  class='caseVue ".($result['meca_mur_type']=="" ? "" : "mur_".$result['meca_mur_type'])."'>                                
                                    <div class='caseVue ".($result['meca_pos_decor_dessus']=="" ? "" : "decor".$result['meca_pos_decor_dessus'])."'>                                
                                    </div>
                                </div>
                            </div>
                        </div>
                </td>
                <td>".($result['meca_type']=="G" ? "Grappe" : "Individuel")."</td>
                <td>".($result['meca_pos_passage_autorise']=="" ? "base" : ($result['meca_pos_passage_autorise']=="1" ? "autorisé": "interdit"))."</td>
                <td>".($result['meca_mur_tangible']=="" ? "base" : ($result['meca_mur_tangible']=="O" ? "intangible": "tangible"))."</td>
                <td>".($result['meca_mur_illusion']=="" ? "base" : ($result['meca_mur_illusion']=="O" ? "illusion": "infranchissable"))."</td>
                <td>".($result['meca_pos_ter_cod']=="" ? "base" : ($result['meca_pos_ter_cod']=="0" ? "sans terrain": $result['ter_nom']))."</td>
                <td>".($result['meca_pos_modif_pa_dep']=="" ? "base" : (int)$result['meca_pos_modif_pa_dep'] )."</td>
                <td><b>".($result2["nb_count"]==0 ? "Inutilisé" : ($result2["nb_actif"]==0 ? "Désactivé" : "Activé".($result['meca_type']=="G" ? "" : " ".$result2["nb_actif"]."/".$result2["nb_count"])))."</b></td>
                <td><a href=\"javascript:desactivermeca({$result['meca_cod']});\">Désactiver</a> / <a href=\"javascript:activermeca({$result['meca_cod']});\">Activer</a></td>
                <td><a href=\"javascript:supprimermeca({$result['meca_cod']});\">Supprimer</a></td>
            </tr>";
        }
        echo '</table>';
        echo "<br><br><b><u>ATTENTION</u></b>: Une activation sur un mecanisme « individuel » activera toutes les cases où ce mécanisme est utilisé!";
    }
}
#meca_nom, meca_type, meca_pos_etage, meca_pos_type_aff, meca_pos_decor, meca_pos_decor_dessus, meca_pos_passage_autorise, meca_pos_modif_pa_dep, meca_pos_ter_cod, meca_mur_type, meca_mur_tangible, meca_mur_illusion)

//=======================================================================================
// == Footer
//=======================================================================================
?>
    <p style="text-align:center;"><a href="<?php echo $_SERVER['PHP_SELF'] ?>">Retour au début</a>
<?php $contenu_page = ob_get_contents();
ob_end_clean();

$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'PERSO'        => $perso,
    'PHP_SELF'     => $_SERVER['PHP_SELF'],
    'CONTENU_PAGE' => $contenu_page

);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));