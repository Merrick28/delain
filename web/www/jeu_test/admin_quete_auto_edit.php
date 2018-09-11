<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';

$erreur = 0;
$req = 'select dcompt_modif_perso from compt_droit where dcompt_compt_cod = ' . $compt_cod;
$db->query($req);
if ($db->nf() == 0)
{
    $droit['modif_perso'] = 'N';
}
else
{
    $db->next_record();
    $droit['modif_perso'] = $db->f("dcompt_modif_perso");
}
if ($droit['modif_perso'] != 'O')
{
    echo "<p>Erreur ! Vous n'avez pas accès à cette page $compt_cod !";
    $erreur = 1;
}
if ($erreur == 0)
{

     // On est admin ici, on a les droits sur les quetes
    // Traitement des paramètres
    $mod_quete_cod = $_REQUEST['mod_quete_cod'] ;
    //-- traitement des actions
    if(isset($_REQUEST['methode']))
    {
        echo '<span style="color:blue;font-weight:bold">';


        echo '</span><br />';
    }


    // Liste des questes existantes
    echo '  <TABLE width="80%" align="center">
            <TR>
            <TD>
            <form method="post">
            Modifier la quête:<select name="mod_quete_cod"><option value="0">Sélectionner une quête</option>';

    $db->query('select aquete_nom, aquete_cod from quetes.aquete order by aquete_nom');
    while ($db->next_record())
    {
        echo '<option value="' . $db->f('aquete_cod');
        if ($db->f('aquete_cod') == $mod_quete_cod) echo '" selected="selected';
        echo '">' . $db->f('aquete_nom') . '</option>';
    }
    echo '  </select>
            <input type="submit" value="Valider">
            </form></TD>
            </TR>
            </TABLE>
            <HR>';

    echo '<b>Caractéristique de la Quête</b>';

    // La quête elle-même
    $quete = new aquete;
    $quete->charge(1*$mod_quete_cod);

    echo '  <br>
            <form  method="post">
            <input type="hidden" name="methode" value="update_quete" />
            <input type="hidden" name="mod_quete_cod" value="{$mod_quete_cod}" />
            <table width="80%" align="center">';

    echo '<tr><td>Nom de la quête :</td><td><input type="text" name="aquete_nom" value="{$quete->aquete_nom}"></td></tr>';
    echo '<tr><td>Description :</td><td><input type="text" size=80 name="aquete_nom" value="{$quete->aquete_description}"></td></tr>';
    echo '<tr><td>Type de déclenchement :</td><td>'.create_selectbox("aquete_trigger_type", array("perso","position","lieux"), $quete->aquete_trigger_type, 'onChange="alert(\'test\')";').'</td></tr>';
    echo '<tr><td>Perso déclencheur :</td><td><input id="aquete_trigger_cod" onClick=\'getTableCod("aquete_trigger_cod","perso");\' type="text" size="3" name="aquete_trigger_cod" value="{$quete->aquete_trigger_cod}"></td></tr>';
    echo '<tr><td colspan="2"><input type="submit" value="Créer/Modifier la quête" /></td></tr></table></form>';

}

/* Fonctions */
function create_selectbox($name, $data, $default='', $param=''){
    $out='<select name="'.$name.'"'. (!empty($param)?' '.$param:'') .">\n";

    foreach($data as $key=>$val) {
        $out.='<option value="' .$key. '"'. ($default==$key?' selected="selected"':'') .'>';
        $out.=$val;
        $out.="</option>\n";
    }
    $out.="</select>\n";

    return $out;
}#-# create_selectbox()
?>
<p style="text-align:center;"><a href="<?php echo$PHP_SELF ?>">Retour au début</a>
<?php $contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
