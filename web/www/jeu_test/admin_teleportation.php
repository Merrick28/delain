<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
include_once '../includes/tools.php';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

echo '<script>//# sourceURL=admin_teleportation.js
    function setNomAndPosPerso(divname, cod) { 
        //executer le service asynchrone
        $("#"+divname).text("");
        runAsync({request: "get_table_info", data:{info:"perso_pos", perso_cod:cod}}, function(d){
            if ((d.resultat == 0)&&(d.data)&&(d.data.perso_nom))
            {
                $("#"+divname).html(d.data.perso_nom+\' <i style="font-size:10px;"> (X=\'+d.data.pos_x+\' X=\'+d.data.pos_y+\' \'+d.data.etage_libelle+\')</i>\');
            }
        });
}
</script>';

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
?>
<title>TÉLÉPORTATION PERSOS / MONSTRES</title>
<?php
$erreur = 0;
$req = "select dcompt_modif_perso, dcompt_modif_gmon, dcompt_controle, dcompt_creer_monstre from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);
if ($db->nf() == 0)
{
	$droit['modif_perso'] = 'N';
	$droit['modif_gmon'] = 'N';
	$droit['controle'] = 'N';
	$droit['creer_monstre'] = 'N';
}
else
{
	$db->next_record();
	$droit['modif_perso'] = $db->f("dcompt_modif_perso");
	$droit['modif_gmon'] = $db->f("dcompt_modif_gmon");
	$droit['controle'] = $db->f("dcompt_controle");
	$droit['creer_monstre'] = $db->f("dcompt_creer_monstre");
}
if ($droit['modif_perso'] != 'O')
{
	echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
	$erreur = 1;
}
if ($erreur == 0)
{
    //=======================================================================================
    // == Main
    //=======================================================================================
    //-- traitement des actions
    if(isset($_REQUEST['methode']))
    {
        // Traitement des actions

    }
    //print_r($_REQUEST);

    //=======================================================================================
    // == Constantes quete_auto
    //=======================================================================================
    //$request_select_etage_ref = "SELECT null etage_cod, 'Aucune restriction' etage_libelle, null etage_numero UNION SELECT etage_cod, etage_libelle, etage_numero from etage where etage_reference = etage_numero order by etage_numero desc" ;
    $request_select_etage = "SELECT etage_numero, case when etage_reference <> etage_numero then ' |- ' else '' end || etage_libelle as etage_libelle from etage order by etage_reference desc, etage_numero";

    echo   'Téléportation en position :<br><br>
                                    X = <input name="pos_x" id="pos_x" type="text" size="5" value="">&nbsp;
                                    Y = <input name="pos_y" id="pos_Y" type="text" size="5" value="">&nbsp;
                                    Etage&nbsp;:'.create_selectbox_from_req("pos_etage", $request_select_etage, 0, array('id' =>"pos_etage", 'style'=>'style="width: 350px;"')).'                                   
                                    <br><br>';
    echo   'Liste des persos à téléporter:<br><br>';

    echo '<table width="80%" align="center">';

    // Pour copier le modele quete-auto (pour un dev flash, on reprend de l'existant)
    $style_tr = "display: block;" ;
    $param_id=0;
    $row=0;
    $row_id = "row-$param_id-$row-";
    $aqelem_misc_nom = "" ;
    echo   '<tr id="'.$row_id.'" style="'.$style_tr.'">';
    echo   '<td><input type="button" class="test" value="Supprimer" onClick="delQueteAutoParamRow($(this).parent(\'td\').parent(\'tr\'), 1);"></td>';
    echo   '<td>Perso :
                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="">
                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="">
                    <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="" onChange="setNomAndPosPerso(\''.$row_id.'aqelem_misc_nom\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                    &nbsp;<i></i><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></i>
                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","perso","Rechercher un perso");\'>
                    </td>';
    echo '<tr id="add-'.$row_id.'" style="'.$style_tr.'"><td> <input type="button" class="test" value="ajouter" onClick="addQueteAutoParamRow($(this).parent(\'td\').parent(\'tr\').prev(),0);"> </td></tr>';
    echo '</table>';
}
?>
<p style="text-align:center;"><a href="<?php echo$PHP_SELF ?>">Retour au début</a>
<?php $contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>