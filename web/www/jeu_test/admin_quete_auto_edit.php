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
    //=======================================================================================
    // == Main
    //=======================================================================================
    // On est admin ici, on a les droits sur les quetes
    // Traitement des paramètres
    $aquete_cod = 1*$_REQUEST['aquete_cod'] ;
    //-- traitement des actions
    if(isset($_REQUEST['methode']))
    {
        // Traitement des actions
        define("APPEL",1);
        include ("admin_traitement_quete_auto_edit.php");
    }


    //=======================================================================================
    //-- On commence par l'édition de la quete elle-meme (ajout/modif)
    //---------------------------------------------------------------------------------------
    if(!isset($_REQUEST['ajout_etape'])) {

        // Liste des quetes existantes
        echo '  <TABLE width="80%" align="center">
                <TR>
                <TD>
                <form method="post">
                Modifier la quête:<select onchange="this.parentNode.submit();" name="aquete_cod"><option value="0">Sélectionner ou créer une quête</option>';

        $db->query('select aquete_nom, aquete_cod from quetes.aquete order by aquete_nom');
        while ($db->next_record())
        {
            echo '<option value="' . $db->f('aquete_cod');
            if ($db->f('aquete_cod') == $aquete_cod) echo '" selected="selected';
            echo '">' . $db->f('aquete_nom') . '</option>';
        }
        echo '  </select>
                <!--input type="submit" value="Valider"-->
                </form></TD>
                </TR>
                </TABLE>
                <HR>';

        echo '<b>Caractéristiques de la Quête</b>'. ($aquete_cod>0 ? " #$aquete_cod" : "");

        // La quête elle-même ----------------------------------------------------------------------
        $quete = new aquete;
        $quete->charge($aquete_cod);

        echo '  <br>
                <form  method="post"><input type="hidden" name="methode" value="update_quete" />
                <input type="hidden" name="aquete_cod" value="'.$aquete_cod.'" />
                <table width="80%" align="center">';

        echo '<tr><td><b>Nom de la quête </b>:</td><td><input type="text" name="aquete_nom" value="'.$quete->aquete_nom.'"></td></tr>';
        echo '<tr><td><b>Description </b>:</td><td><input type="text" size=80 name="aquete_description" value="'.$quete->aquete_description.'"></td></tr>';
        echo '<tr><td><b>Quête ouverte </b>:</td><td>'.create_selectbox("aquete_actif", array("O"=>"Oui","N"=>"Non"), $quete->aquete_actif).' <i>activation/désactivation général</i></td></tr>';
        echo '<tr><td><b></b>Début </b><i style="font-size: 7pt;">(dd/mm/yyyy hh:mm:ss)</i>:</td><td><input type="text" size=18 name="aquete_date_debut" value="'.$quete->aquete_date_debut.'"> <i>elle ne peut pas être commencée avant cette date (pas de limite si vide)</i></td></tr>';
        echo '<tr><td><b></b>Fin </b><i style="font-size: 7pt;">(dd/mm/yyyy hh:mm:ss)</i>:</td><td><input type="text" size=18 name="aquete_date_fin" value="'.$quete->aquete_date_fin.'"> <i>elle ne peut plus être commencée après cette date (pas de limite si vide)</i></td></tr>';
        echo '<tr><td><b>Nb. quête simultanée</b>:</td><td><input type="text" size=10 name="aquete_nb_max_instance" value="'.$quete->aquete_nb_max_instance.'"> <i>nb de fois où elle peut être faite en parallèle (pas de limite si vide)</i></td></tr>';
        echo '<tr><td><b></b><del>Nb. participants max</del></b>:</td><td><input disabled type="text" size=10 name="aquete_nb_max_participant" value="1"> <del><i>nb max de perso pouvant la faire ensemble (pas de limite si vide)</del></i></td></tr>';
        echo '<tr><td><b>Nb. rejouabilité</b>:</td><td><input type="text" size=10 name="aquete_nb_max_rejouable" value="'.$quete->aquete_nb_max_rejouable.'"> <i>nb de fois où elle peut être jouer par un même perso (pas de limite si vide)</i></td></tr>';
        echo '<tr><td><b>Nb. de quête</b>:</td><td><input type="text" size=10 name="aquete_nb_max_quete" value="'.$quete->aquete_nb_max_quete.'"> <i>nb de fois où elle peut être rejouer tous persos confondus (pas de limite si vide)</i></td></tr>';
        if ($aquete_cod==0)
        {
            // cas d'une nouvelle quete
            echo '<tr><td colspan="2"><input type="submit" value="Créer la quête" /></td></tr>';
            echo '</table>';
        }
        else
        {
            // La quete existe proposer l'ajout d'étape ==>  Si c'est la première etape, elle doit-être du type START
            $filter = ($quete->get_nb_etape()==0) ? "where aqetaptemp_tag='START'" : "" ;
            echo '<tr><td colspan="2"><input type="submit" value="Modifier la quête" /></td></tr>';
            echo '</table>';
            echo '<hr>';
            echo '<table width="80%" align="center">';
            echo '<tr style=""><td colspan="2">Choisir un type d\'étape '.create_selectbox_from_req("aqetaptemp_cod", "select aqetaptemp_cod, aqetaptemp_nom from quetes.aquete_etape_template {$filter} order by aqetaptemp_nom").' et <input type="submit" name="ajout_etape" value="ajouter une étape"" /></td></tr>';
            echo '</table>';
        }
        echo '</form>';

        // Etape 1 ----------------------------------------------------------------------
        // Seulement si la quete a été créée
        if (1*$aquete_cod > 0)
        {
            echo '<hr>';
            echo '  <form  method="post">
                    <input type="hidden" name="methode" value="update_etape_1" />
                    <input type="hidden" name="mod_quete_cod" value="{$mod_quete_cod}" />
                    <table width="80%" align="center">';
            echo '<tr><td>Perso déclencheur :</td><td>
                                            <input id="aquete_trigger_cod" type="text" size="5" name="aquete_trigger_cod" value="{$quete->aquete_trigger_cod}" onChange="$(\'#aquete_trigger_nom\').text(\'\');">
                                            &nbsp;<i></i><span id="aquete_trigger_nom"></span></i>
                                            &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("aquete_trigger","perso","Rechercher un perso");\'> 
                                            </td></tr>';
            echo '<tr><td colspan="2"><input type="submit" value="Modifier" /></td></tr></table></form>';
        }
        // Fin saisie  ----------------------------------------------------------------------
        echo '<hr>';
    }
    //=======================================================================================
    //-- Section dédiée aux étapes (ajout/modif)
    //---------------------------------------------------------------------------------------
    else
    {
        $quete = new aquete;
        $quete->charge($aquete_cod);    // On charge la quete

        $aqetaptemp_cod = 1*$_REQUEST["aqetaptemp_cod"] ;
        $etape_template = new aquete_etape_template;
        $etape_template->charge($aqetaptemp_cod);    // On charge le modele de l'étape.

        $aqetape_cod = 1*$_REQUEST["aqetape_cod"] ;
        $etape = new aquete_etape;
        $etape->charge($aqetape_cod);    // On charge l'étape elle.

        echo '<b>Quête</b> #'.$aquete_cod.' - '.$quete->aquete_nom.'<br><hr>';
        echo '<b>Caractéristiques de l\'étape</b>'. ($aqetape_cod>0 ? " #$aqetape_cod" : "");

        // Mise en forme de l'étape pour la saisie des infos.
        echo '  <br>
                <form  method="post"><input type="hidden" name="methode" value="update_etape" />
                <input type="hidden" name="aquete_cod" value="'.$aquete_cod.'" />
                <input type="hidden" name="aqetape_cod" value="'.$aqetape_cod.'" />
                <input type="hidden" name="aqetaptemp_cod" value="'.$aqetaptemp_cod.'" />
                <table width="80%" align="center">';

        echo '<tr><td colspan="2">'.$etape_template->aqetaptemp_description.'<br><br></td></tr>';
        echo '<tr><td><b>Paramètres </b>:</td><td>'.$etape_template->aqetaptemp_parametres.'</td></tr>';
        echo '<tr><td><b>Exemple </b>:</td><td>'.$etape_template->aqetaptemp_template.'</td></tr>';
        echo '<tr><td><b>Nom de l\'étape </b>:</td><td><input type="text" size="50" name="aqetape_nom" value="'.$etape->aqetape_nom.'"></td></tr>';
        echo '<tr><td><b>Texte de l\'étape </b>:</td><td><textarea style="min-height: 80px; min-width: 500px;" name="aqetape_texte">'.( $etape->aqetape_texte != "" ? $etape->aqetape_texte : $etape_template->aqetaptemp_template).'</textarea></td></tr>';
        echo '</table>';



        $param_liste = $etape_template->get_liste_parametres();
        foreach ($param_liste as $id => $param)
        {
            echo '<br><br><b>Edition du paramètre #'.$id.'</b>: <i>('.$param['raw'].' => '.$param['texte'].')</i><br>';
            echo '<table width="80%" align="center">';
            echo '<input type="hidden" id="max-param-'.$id.'" value="'.$param['M'].'">';

            $row = 0;
            $row_id = "row-$id-$row-";
            switch ($param['type'])
            {
                case 'perso':
                    echo   '<tr id="'.$row_id.'"><td>Perso :
                            <input id="'.$row_id.'aqetape_aqelem_cod" type="text" size="5" value="" onChange="$(\'#'.$row_id.'aqetape_aqelem_nom\').text(\'\');">
                            &nbsp;<i></i><span id="'.$row_id.'aqetape_aqelem_nom"></span></i>
                            &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqetape_aqelem","perso","Rechercher un perso");\'> 
                            </td></tr>';
                break;
                default:
                    echo '<tr id="'.$row_id.'"><td>Type de paramètre inconnu</td></tr>';
                break;
            }
            echo '<tr><td> <input type="button" class="test" value="ajouter" onClick="addQueteAutoParamRow($(this).parent(\'td\').parent(\'tr\').prev());"> </td></tr>';
            echo '</table>';
        }

        //Boucle sur les paramètres

        echo '</form>';
        echo '<hr>';

    }
}

//=======================================================================================
// == Fonction
//=======================================================================================
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

//=======================================================================================
function create_selectbox_from_req($name, $req, $default='', $param=''){
    $pdo = new bddpdo;
    $stmt = $pdo->query($req);
    $data = array();
    while($result = $stmt->fetch(PDO::FETCH_NUM )) $data[$result[0]] = $result[1] ;
    return create_selectbox($name, $data, $default, $param);
}#-# create_selectbox_from_table()

//=======================================================================================
// == Footer
//=======================================================================================
?>
<p style="text-align:center;"><a href="<?php echo$PHP_SELF ?>">Retour au début</a>
<?php $contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
