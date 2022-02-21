<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
include_once '../includes/tools.php';

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');


$contenu_page = '';

$droit_modif = 'dcompt_enchantements';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0)
{
    //=======================================================================================
    // == Main
    //=======================================================================================
    // On est admin ici, on a les droits sur les quetes
    // Traitement des paramètres
    $tbonus_cod = 1*$_REQUEST['tbonus_cod'] ;


    //-- traitement des actions=======================================================================================
    //print_r($_REQUEST);
    if(isset($_REQUEST['methode']) && $_REQUEST['methode']=="add_mon_fonction")
    {
        // Traitement des actions

        $log =date("d/m/y - H:i") . $perso->perso_nom . " (compte $compt_cod) modifie les EA du compteur numero: $tbonus_cod\n";

        // Sauvegarder les modifications des effets-auto => save_effet_auto($post, $fonc_gmon_cod, $fonc_perso_cod)
        $message = save_effet_auto($_POST, null, null) ;

        writelog($log . $message, 'monstre_edit');
        echo nl2br($message);
        echo "<hr>";

    }

    //=======================================================================================
    echo '  <link href="../css/multiple-select.min.css?v'.$__VERSION.'" rel="stylesheet">
            <SCRIPT language="javascript" src="../scripts/controlUtils.js"></script>
            <script language="javascript" src="../scripts/validation.js"></script>
            <script language="javascript" src="../scripts/manip_css.js"></script>
            <script language="javascript" src="../scripts/admin_effets_auto.js?v'.$__VERSION.'"></script>
            <script language="javascript" src="../js/multiple-select.min.js?v'.$__VERSION.'"></script>
            <script language="javascript"> 
                // Paramètres de déclechement réduite aux BMC pour ces EA
                $.each(EffetAuto.Triggers, function( d ) {  if (d != "BMC") delete EffetAuto.Triggers[d]; });
            </script>
            ';

    echo "On trouve ici des EA (effets-auto) qui sont attachés à des Bonus/Malus du type compteur.<br>
          Cela signifie que tout « perso » (qu'il soit monstre ou joueurs) déclenchera ces effets s'il remplit les conditions du déclenchement.<br>
          <u><strong>ATTENTION</strong></u>: il faut noter que: <br>
          • Le changement de nom ne sera effectif QUE pour les monstres.<br>
        <br><br>";


    echo '  <TABLE width="80%" align="center">
            <TR>
            <TD>
            <form method="post">
            Editer les EA d\'un compteur:<select onchange="this.parentNode.submit();" name="tbonus_cod"><option value="0">Sélectionner le compteur</option>';

    // sortir les "E3(+)" : Exaltation, Excitation, Embrasement des compteurs configurables dans l'outil.
    $stmt = $pdo->query("select tonbus_libelle || case when tbonus_gentil_positif then ' (+)' else ' (-)' end as tonbus_libelle, tbonus_cod from bonus_type where tbonus_libc not in ('C01', 'C07', 'C08', 'C10', 'C11', 'C12') and tbonus_compteur='O' order by tbonus_libc");
    while ($result = $stmt->fetch())
    {
        echo '<option value="' . $result['tbonus_cod'];
        if ($result['tbonus_cod'] == $tbonus_cod) echo '" selected="selected';
        echo '">' . $result['tonbus_libelle'] . '</option>';
    }
    echo '  </select>
            </form></TD>
            </TR>
            </TABLE>';
    if ($tbonus_cod>0) {
        $req = "SELECT * FROM bonus_type WHERE tbonus_cod=:tbonus_cod";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":tbonus_cod" => $tbonus_cod), $stmt);

        if ($result = $stmt->fetch()) {
            echo "<strong>Compteur #" . $result["tbonus_cod"] ."</strong>: ". $result["tbonus_libc"] . ($result["tbonus_cumulable"]=='O' ? " - <strong>Progressivité:</strong> ".$result["tbonus_degressivite"]."%" : '') . "<br>";
            echo "<strong>Description de ce Compteur: </strong>" . $result["tbonus_description"] . "<br><br>";
        }
    }

    echo "<HR>";

    echo '<strong>EFFETS AUTOMATIQUES:</strong><br><br>';

    if ($tbonus_cod==0) {
        echo 'Sélectionnez le compteur!';
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
        $req = "select tbonus_libc, CASE WHEN tbonus_compteur='O' THEN '[compteur] - ' ELSE '' END || tonbus_libelle || case when tbonus_gentil_positif then ' (+)' else ' (-)' end as tonbus_libelle
                            from bonus_type where tbonus_cod={$tbonus_cod}
                            order by tonbus_libelle ";
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

        // Interface de saisie
        echo '<form method="post" onsubmit="return Validation.Valide ();">
            <input type="hidden" name="methode2" value="edit">
            <input type="hidden" name="methode" value="add_mon_fonction">
            <input type="hidden" name="sel_method" value="edit">
            <input type="hidden" name="tbonus_cod" value="'.$tbonus_cod.'">
            <input type="hidden" name="fonctions_supprimees" id="fonctions_supprimees" value=""/>
            <input type="hidden" name="fonctions_ajoutees" id="fonctions_ajoutees" value=""/>
            <input type="hidden" name="fonctions_annulees" id="fonctions_annulees" value=""/>
            <input type="hidden" name="fonctions_existantes" id="fonctions_existantes" value=""/>
            <div id="liste_fonctions"></div><script>
            EffetAuto.EditionCompteur = true ; ';       // En mode compteur les implantations d'EA sont interdites

        // Liste des EA Existantes
        $req = "select fonc_cod, fonc_nom, fonc_type, case when fonc_nom='deb_tour_generique' then substr(fonc_effet,1,3) else fonc_effet end as fonc_effet, case when fonc_nom='deb_tour_generique' and substr(fonc_effet,4,1)='+' then 'O' else 'N' end as fonc_cumulatif, fonc_force, fonc_duree, fonc_type_cible, fonc_nombre_cible, fonc_portee, fonc_proba, fonc_message, fonc_trigger_param
                        from fonction_specifique, bonus_type 
                        where   fonc_gmon_cod is null 
                            and fonc_perso_cod is null 
                            and fonc_type='BMC'
                            and tbonus_cod=".((int)$tbonus_cod)."
                            and fonc_trigger_param->>'fonc_trig_compteur'::text = tbonus_libc
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