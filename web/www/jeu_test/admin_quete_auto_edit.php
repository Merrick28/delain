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
    $reqQuete = 'select aquete_nom from quetes.aquete where aquete_cod = ' . (1*$mod_quete_cod);
    $db->query($reqQuete);
    $quete_nom = '';
    if ($db->next_record())  $quete_nom = $db->f('aquete_nom');


    echo '  <br>
            <form  method="post">
            <input type="hidden" name="methode" value="update_quete" />
            <input type="hidden" name="mod_quete_cod" value="{$mod_quete_cod}" />
            <table width="80%" align="center">';

    echo '<tr><td>Nom de la quête :<input type="text" name="aquete_nom" value="{$quete_nom}"></td></tr>';
    echo '<tr><td><input type="submit" value="Modifier la quête" /></td></tr></table></form>';

}

?>
<p style="text-align:center;"><a href="<?php echo$PHP_SELF ?>">Retour au début</a>
<?php $contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
