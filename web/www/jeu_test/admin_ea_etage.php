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
    //=======================================================================================
    // == Main
    //=======================================================================================
    // On est admin ici, on a les droits sur les quetes
    // Traitement des paramètres

    //-- traitement des actions=======================================================================================
    //print_r($_REQUEST);
    if(isset($_POST['methode']) && $_POST['methode']=="add_mon_fonction")
    {
        // Traitement des actions: assurer le formatage du champ "fonc_trig_pos_cods" => " XXXXX, XXXXX, XXXXX, etc..."
        foreach ($_POST as $k => $v)
        {
            if (substr($k, 0, 18)=="fonc_trig_pos_cods")
            {
                $pos_cods = explode(",", $v);
                array_walk($pos_cods, function(&$value, &$key){return $value = " ".trim($value) ;} );
                $_POST[$k] = implode(",", array_filter($pos_cods, function ($val) { return ( $val == " " ? false : true ); } )).",";
            }
        }
        //echo "<pre>"; print_r($_REQUEST); die();

        $log =date("d/m/y - H:i") . $perso->perso_nom . " (compte $compt_cod) modifie les EA d'étage : $pos_etage\n";

        $message = save_effet_auto($_POST, null, null) ;

        writelog($log . $message, 'lieux_etages');
        echo nl2br($message);
        echo "<hr>";

    }

    //=======================================================================================
    echo '  <link href="../css/multiple-select.min.css?v'.$__VERSION.'" rel="stylesheet">
            <SCRIPT language="javascript" src="../scripts/tools.js"></script>
            <SCRIPT language="javascript" src="../scripts/controlUtils.js"></script>
            <script language="javascript" src="../scripts/validation.js"></script>
            <script language="javascript" src="../scripts/manip_css.js"></script>
            <script language="javascript" src="../scripts/admin_effets_auto.js?v'.$__VERSION.'"></script>
            <script language="javascript" src="../js/multiple-select.min.js?v'.$__VERSION.'"></script>
            <script language="javascript"> 
                // Paramètres de déclechement réduite aux BMC pour ces EA
                $.each(EffetAuto.Triggers, function( d ) {  if (d != "POS") delete EffetAuto.Triggers[d]; });
            </script>
            ';

    echo "On trouve ici des EA (effets-auto) qui sont attachés à des cases dans un étage.<br>
          Cela signifie que tout « perso » (qu'il soit monstre ou joueurs) déclenchera ces effets s'il remplit les conditions du déclenchement.<br>
          <u><strong>ATTENTION</strong></u>: il faut noter que: <br>
          • Le changement de nom ne sera effectif QUE pour les monstres.<br>
        <br><br>";


    echo '  <TABLE width="80%" align="center">
            <TR>
            <TD>
            <form method="post">
            Editer les EA d\'un étage:<select onchange="this.parentNode.submit();" name="pos_etage"><option value="0">Sélectionner l\'étage</option>';

    if (!isset($pos_etage)) $pos_etage = '';
    echo $html->etage_select($pos_etage);

    echo '  </select>
            </form></TD>
            </TR>
            </TABLE>';
    echo "<HR>";

    echo '<strong>EFFETS AUTOMATIQUES:</strong><br><br>';

    if ($pos_etage==0) {
        echo 'Sélectionnez l\'étage!';
    } else {

        // Liste des monstres générique
        $req = 'select gmon_nom, gmon_cod from monstre_generique order by gmon_nom';
        echo '<select id="liste_monstre_modele" style="display:none;">' . $html->select_from_query($req, 'gmon_cod', 'gmon_nom') . '</select>';

        // Liste des Bonus-malus
        $req = "select tbonus_libc, CASE WHEN tbonus_compteur='O' THEN '[compteur] - ' ELSE '' END || tonbus_libelle || case when tbonus_gentil_positif then ' (+)' else ' (-)' end as tonbus_libelle
                            from bonus_type
                            order by tonbus_libelle ";
        echo '<select id="liste_bm_modele" style="display:none;">' . $html->select_from_query($req, 'tbonus_libc', 'tonbus_libelle') . '</select>';

        // Liste des Bonus-malus pour les compteurs
        $req = "select tbonus_libc, CASE WHEN tbonus_compteur='O' THEN '[compteur] - ' ELSE '' END || tonbus_libelle || case when tbonus_gentil_positif then ' (+)' else ' (-)' end as tonbus_libelle from bonus_type  order by tonbus_libelle ";
        echo '<select id="liste_bmc_modele" style="display:none;">' . $html->select_from_query($req, 'tbonus_libc', 'tonbus_libelle') . '</select>';

        // Liste des conditions de perso
        $req = "select aqtypecarac_cod, aqtypecarac_nom from quetes.aquete_type_carac order by aqtypecarac_type, aqtypecarac_nom, aqtypecarac_cod ";
        echo '<select id="liste_perso_condition_modele" style="display:none;">' . $html->select_from_query($req, 'aqtypecarac_cod', 'aqtypecarac_nom') . '</select>';

        // Liste des sorts
        $req = "select  distinct dsort_dieu_cod, sort_cod, case when dsort_dieu_cod is null then '' else 'Divin - ' end || sort_nom || ' (' || case when sort_case='O' then 'case/' else '' end || case when sort_aggressif='O' then 'agressif)' when sort_soutien='O' then 'soutien)' else 'neutre)' end sort_nom
                        from sorts
                        left outer join dieu_sorts ON dsort_sort_cod = sort_cod
                        where dsort_dieu_cod is null                        
                        order by dsort_dieu_cod desc ,sort_nom
                                 ";
        echo '<select id="liste_sort_modele" style="display:none;">' . $html->select_from_query($req, 'sort_cod', 'sort_nom') . '</select>';

        // Liste des races
        $req = "select race_cod, race_nom from race order by race ";
        echo '<select id="liste_race_modele" style="display:none;">' . $html->select_from_query($req, 'race_cod', 'race_nom') . '</select>';

        // Liste des objets generique
        $req = "select gobj_nom, gobj_cod from objet_generique order by gobj_nom ";
        echo '<select id="liste_objet_modele" style="display:none;">' . $html->select_from_query($req, 'gobj_cod', 'gobj_nom') . '</select>';

        // Liste des méca de l'étage
        $req = "select meca_nom, meca_cod from meca where meca_pos_etage=".$pos_etage." order by meca_nom ";
        echo '<select id="liste_meca_modele" style="display:none;">' . $html->select_from_query($req, 'meca_cod', 'meca_nom') . '</select>';

        // Interface de saisie
        echo '<form method="post" onsubmit="return Validation.Valide ();">
            <input type="hidden" name="methode2" value="edit">
            <input type="hidden" name="methode" value="add_mon_fonction">
            <input type="hidden" name="sel_method" value="edit">
            <input type="hidden" name="pos_etage" value="'.$pos_etage.'">
            <input type="hidden" name="fonctions_supprimees" id="fonctions_supprimees" value=""/>
            <input type="hidden" name="fonctions_ajoutees" id="fonctions_ajoutees" value=""/>
            <input type="hidden" name="fonctions_annulees" id="fonctions_annulees" value=""/>
            <input type="hidden" name="fonctions_existantes" id="fonctions_existantes" value=""/>
            <div id="liste_fonctions"></div><script>
            EffetAuto.EditionEAPosition = true ; // En mode EA les implantations d\'EA sont interdites
            EffetAuto.EditionEA.etage_cod = '.$pos_etage.' ;
            ';

        // Liste des EA Existantes
        $req = "select fonc_cod, fonc_nom, fonc_type, case when fonc_nom='deb_tour_generique' then substr(fonc_effet,1,3) else fonc_effet end as fonc_effet, case when fonc_nom='deb_tour_generique' and substr(fonc_effet,4,1)='+' then 'O' else 'N' end as fonc_cumulatif, fonc_force, fonc_duree, fonc_type_cible, fonc_nombre_cible, fonc_portee, fonc_proba, fonc_message, fonc_trigger_param
                        from fonction_specifique
                        where   fonc_gmon_cod is null 
                            and fonc_perso_cod is null 
                            and fonc_type='POS'
                            and fonc_trigger_param->>'fonc_trig_pos_etage'::text=".((int)$pos_etage)."
                        order by fonc_cod ";

        echo getJS_ea_existant($req, false, false);

        echo '</script>';
        echo '<div style="clear: both;">
                <a onclick="EffetAuto.NouvelEffetAuto (); return false;">Nouvel effet</a><br/><br/>
                <input type="submit" value="Valider les suppressions / modifications / ajouts d’effets !"  class="test"/>
            </div>
            </form>';
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