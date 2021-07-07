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
    if(isset($_POST['methode']) && $_POST['methode']=="creer_meca")
    {
        print_r($_POST);

        writelog($log . $message, 'lieux_etages');
        echo nl2br($message);
        echo "<hr>";

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

        $req_m_terrain= "select ter_cod, ter_nom from terrain where ter_cod > 0 order by ter_nom";
        $stmt_m_terrain = $pdo->query($req_m_terrain);
        $terrains = $stmt_m_terrain->fetchAll(PDO::FETCH_ASSOC);

        echo "
        <form method='post' action='admin_meca_etage.php'>
                    <input type='hidden' name='methode' value='creer_meca'>
                    <input type='hidden' name='pos_etage' value='$pos_etage'>
                    <input type='hidden' id='meca-Fond' name='type_aff' value=''>
                    <input type='hidden' id='meca-Decor' name='pos_decor' value=''>
                    <input type='hidden' id='meca-Mur' name='mur_type' value=''>
                    <input type='hidden' id='meca-DecorDessus' name='decor_dessus' value=''>
                    
            <br>Nom du mécanisme: <input type='text' name='nom'  size='40'><br/>
            <div style='float: left; display: inline-flex;'>Pattern:&nbsp;&nbsp;&nbsp; 
                        <div id='pattern-Fond' data-pattern='0' class='caseVue caseFond pinceau'>
                            <div id='pattern-Decor' data-pattern='0' class='caseVue'>
                                <div id='pattern-Mur' data-pattern='0' class='caseVue'>                                
                                    <div id='pattern-DecorDessus' data-pattern='0' class='caseVue'>                                
                                    </div>
                                </div>
                            </div>
                        </div>
            </div><br/><br>
            <table>
            <tr><td>Passage autorisé:</td><td><input checked type='radio' name='passage_autorise' value='-1'>Base</td><td>&nbsp;|&nbsp;<input type='radio' name='passage_autorise' value='0'>Autorisé</td><td>&nbsp;|&nbsp;<input type='radio' name='passage_autorise' value='1'>Interdit<td><tr></tr>
            <tr><td>Mur tangible:</td><td><input checked type='radio' name='mur_tangible' value='-1'>Base</td><td>&nbsp;|&nbsp;<input type='radio' name='mur_tangible' value='0'>Tangible</td><td>&nbsp;|&nbsp;<input type='radio' name='mur_tangible' value='1'>Intangible<td><tr></tr>
            <tr><td>Mur illusion:</td><td><input checked type='radio' name='mur_illusion' value='-1'>Base</td><td>&nbsp;|&nbsp;<input type='radio' name='mur_illusion' value='0'>Infranchissable</td><td>&nbsp;|&nbsp;<input type='radio' name='mur_illusion' value='1'>Illusion<td><tr>
            </table>
            Terrain: ";
            echo '<select name="pos_ter_cod" id="select-terrain">';
            echo '<option value="-1">Terrain Inchangé (base)</option>';
            echo '<option value="0">Sans terrain spécifique</option>';
            for ($t=0; $t<count($terrains); $t++ )
            {
                echo '<option value="'.$terrains[$t]["ter_cod"].'">'.$terrains[$t]["ter_nom"].'</option>';
            }
            echo '</select> Modificateur de PA: <input name="modif_pa_dep" id="modif_pa_dep" value="" type="text" size="3"/> (<em style="font-size: 10px;">laisser vide pour garder le modificateur de base</em>)';
        echo "<br><br><input type='submit' class='test' value='créer le mécanisme!'/><br>
        </form>";



        echo "<HR>Liste des mécanismes de l'étage:";


    }
}


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