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
?>
<SCRIPT language="javascript" src="../scripts/controlUtils.js"></SCRIPT>
<script type="text/javascript">
<!--
function display(id)
{
    document.getElementById(id).style.display='';
}
-->
</script>
<?php $erreur = 0;
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
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
if ($erreur == 0)
{
?>
<?php // TRAITEMENT DU FORMULAIRE

if(isset($_GET['methode']))
{
    $_POST['methode'] = $_GET['methode'];
    $_POST['mod_quete_cod'] = $mod_quete_cod = $_GET['quete'];
}
if(isset($_POST['methode'])){
echo '<span style="color:blue;font-weight:bold">';
include "admin_traitement_quete_auto_edit.php";
echo '</span><br />';
}
?>
<b>RECHERCHE</b>

<TABLE width="80%" align="center">
<TR>
<TD>
<form method="post">
Modifier la quête:<select name="mod_quete_cod"><option value="0">Créer une nouvelle quête</option>
<?php $db->query('select aquete_nom, aquete_cod from quetes.quete_automatique order by aquete_nom');
while ($db->next_record())
{
    echo '<option value="' . $db->f('aquete_cod');
    if ($db->f('aquete_cod') == $mod_quete_cod)
        echo '" selected="selected';
    echo '">' . $db->f('aquete_nom') . '</option>';
}
?>
</select>
<input type="submit" value="Valider">
</form></TD>
</TR>
</TABLE>

<HR>

<?php 
echo '<b>QUETE</b>';


if(isset($_POST['mod_quete_cod'])){

// La quête elle-même
$reqQuete = 'select aquete_nom from quetes.quete_automatique where aquete_cod = ' . $mod_quete_cod;

$db->query($reqQuete);
$quete_nom = '';
if ($db->next_record())
{
    $quete_nom = $db->f('aquete_nom');
}
?>
<br>
<form  method="post">
<input type="hidden" name="methode" value="update_quete" />
<input type="hidden" name="mod_quete_cod" value="<?php echo $mod_quete_cod ?>" />
<table width="80%" align="center">
<?php if ($mod_quete_cod)
    echo '<tr><td><input type="checkbox" class="vide" name="duplicata" value="duplicata" />
[Pas encore disponible] Créer un duplicata de cette quête, et ne modifier que la nouvelle copie.<td></tr>';
?>
<tr><td>Nom de la quête :<input type="text" name="aquete_nom" value="<?php echo $quete_nom ?>"></td></tr>
<tr><td>Pour régler la récompense finale de la quête, vous pourrez modifier l'étape finale après avoir créé la quête.</td></tr>
<tr><td><input type="submit" value="Modifier la quête" /></td></tr></table></form>

<hr>
<?php if ($mod_quete_cod != 0)
{
?>
<b>ETAPES</b>
<table border="3px">
<?php $reqEtapes = 'select quetes.liste_etapes_quete(' . $mod_quete_cod . ') as etapes';
$db->query($reqEtapes);
$db->next_record();
$etapes = explode(' ' , $db->f('etapes'));
foreach ($etapes as $etape)
{
    if (!isset($etape) || $etape == '')
        $etape = 0;
    $reqEtape = 'select etape_cod, etape_nom, etape_description, etape_parametres,
        type_etape_libelle, type_etape_description, type_etape_cod, recompense_cod,
        recompense_pp, recompense_brouzoufs, recompense_px, recompense_objets
        from quetes.etape, quetes.type_etape, quetes.recompense
        where etape_type_etape_cod = type_etape_cod
        and etape_recompense_cod = recompense_cod
        and etape_cod = ' . $etape;
    $db->query($reqEtape);
    if ($db->next_record())
    {
        echo '<tr><td><table><tr><td>Nom: ' , $db->f('etape_nom') , '</td></tr>';
        echo '<tr><td>Description: ' , $db->f('etape_description') , '</td></tr>';
        echo '<tr><td>Type d\'étape: <b>' , $db->f('type_etape_libelle') , '</b>
            (' , $db->f('etape_parametres') , ')' ,
            $db->f('type_etape_description') , '</td></tr>';
        echo '<tr><td>Récompense : ' ,
            $db->f('recompense_pp') , ' points de prestige, ' ,
            $db->f('recompense_brouzoufs') , ' brouzoufs, ' ,
            $db->f('recompense_px') , ' points d\'expérience, ' ,
            'et les objets suivants: ' , $db->f('recompense_objets') ,
            '</td></tr>';
        echo '<tr><td><div id="modif' . $db->f('etape_cod') . '" style="display:none">
        <form method="post">
        <input type="hidden" name="methode" value="modif_etape" />
        <input type="hidden" name="mod_quete_cod" value="' . $mod_quete_cod . '" />
        <input type="hidden" name="etape_cod" value="' . $db->f('etape_cod') . '" />
        <input type="hidden" name="recompense_cod" value="' . $db->f('recompense_cod') . '" />
        Nom: <input type="text" name="etape_nom" value="' . $db->f('etape_nom') . '" /><br />
        Description: <textarea name="etape_description" rows="10" cols="50">' .
            $db->f('etape_description') . '</textarea> <br />
        Type d\'étape :<select name="etape_type_etape">';
        $dbTypes = new base_delain;
        $dbTypes->query('select type_etape_cod, type_etape_libelle, type_etape_description
            from quetes.type_etape'); // Une seule fin d'étape.
        while ($dbTypes->next_record())
        {
            echo '<option value="' , $dbTypes->f('type_etape_cod') ,
                ($dbTypes->f('type_etape_cod') == $db->f('type_etape_cod') ?
                    '" selected="selected' : '') , '">' ,
                $dbTypes->f('type_etape_libelle') , '</option>';
        }
        echo '</select>
        Paramètres: <input type="text" name="etape_parametres" value="' . $db->f('etape_parametres') . '" /><br />
        Récompenses:
        Points de prestige: <input type="text" size="4" name="recompense_pp" value="' .
            $db->f('recompense_pp') . '" />
        Brouzoufs: <input type="text" size="6" name="recompense_br" value="' .
            $db->f('recompense_brouzoufs') . '" />
        PX: <input type="text" size="4" name="recompense_px" value="' .
            $db->f('recompense_px') . '" />
        Objets: <input type="text" name="recompense_objets" value="' .
            $db->f('recompense_objets') . '" /><br />
        <input type="submit" value="Modifier l\'étape" />
        </form></div></td></tr>';
        echo '<tr><td>
        <a href="javascript:display(\'modif' . $db->f('etape_cod') . '\')">Modifier cette étape</a>
        <a href="?methode=monte_etape&etape=' . $db->f('etape_cod') .
            '&quete=' . $mod_quete_cod . '">Monter d\'un cran</a>
        <a href="?methode=supprime_etape&etape=' . $db->f('etape_cod') .
            '&quete=' . $mod_quete_cod . '">Retirer cette étape</a></td></tr>';
        echo '</table></td></tr>';
    }
}
?>

<tr><td>
<h4>Rajouter une étape</h4>
<form method="post">
<input type="hidden" name="methode" value="cree_etape" />
<input type="hidden" name="mod_quete_cod" value="<?php echo $mod_quete_cod ?>" />
<table><tr><td>Nom: <input type="text" name="etape_nom" /></td></tr>
<tr><td>Description complète : <textarea name="etape_description" rows="10" cols="50"></textarea></td></tr>
<tr><td>Type d'étape :<select name="etape_type_etape">
<?php $db->query('select type_etape_cod, type_etape_libelle, type_etape_description
    from quetes.type_etape where type_etape_cod != 0'); // Une seule fin d'étape.
while ($db->next_record())
{
    echo '<option value="' , $db->f('type_etape_cod') , '">' ,
        $db->f('type_etape_libelle') , '</option>';
}
?>
</select>
Paramètres: <input type="text" name="etape_parametres" />
</td></tr>
<tr><td><input type="submit" value="Créer et rajouter l'étape" /></td></tr></table></form>
</td></tr>
<tr><td><h4>Insérer une étape déjà créée</h4>
[Pas encore disponible]<form method="post">
<input type="hidden" name="methode" value="insere_etape" />
<input type="hidden" name="mod_quete_cod" value="<?php echo $mod_quete_cod ?>" />
<select name="ins_etape_cod"><option value="0" selected="selected">Choisissez une étape existante</option>
<?php $reqEtapes = 'select etape_nom, etape_cod from quetes.etape order by etape_nom asc';
$db->query($reqEtapes);
while ($db->next_record())
{
    echo '<option value="' , $db->f('etape_cod') , '">' , $db->f('etape_nom') , '</option>';
}
?>
</select><input type="submit" value="Insérer" /></form></td></tr>
</table>
<?php }   // FIn de si quête sélectionnée.
?>


<?php } else {
echo "<br />Sélectionnez une quête";
}
}
?>
<p style="text-align:center;"><a href="<?php echo$PHP_SELF ?>">Retour au début</a>
<?php $contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
