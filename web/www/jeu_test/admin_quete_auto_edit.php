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

//
//Contenu de la div de droite
//
$contenu_page = '';

$erreur = 0;
$req = 'select dcompt_animations from compt_droit where dcompt_compt_cod = ' . $compt_cod;
$db->query($req);
if ($db->nf() == 0)
{
    $droit['modif_animations'] = 'N';
}
else
{
    $db->next_record();
    $droit['modif_animations'] = $db->f("dcompt_animations");
}
if ($droit['modif_animations'] != 'O')
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
    print_r($_REQUEST);

    //=======================================================================================
    // == Constantes quete_auto
    //=======================================================================================
    //$request_select_etage = "SELECT null etage_cod, 'Aucune restriction' etage_libelle, null etage_numero UNION SELECT etage_cod, etage_libelle, etage_numero from etage where etage_reference = etage_numero order by etage_numero desc" ;
    $request_select_etage = "SELECT etage_numero, etage_libelle from etage where etage_reference = etage_numero order by etage_numero desc" ;

    //=======================================================================================
    //-- On commence par l'édition de la quete elle-meme (ajout/modif)
    //---------------------------------------------------------------------------------------
    if(!isset($_REQUEST['methode']) || ($_REQUEST['methode']=='edite_quete')) {

        // Liste des quetes existantes
        echo '  <TABLE width="80%" align="center">
                <TR>
                <TD>
                <form method="post">
                Editer la quête:<select onchange="this.parentNode.submit();" name="aquete_cod"><option value="0">Sélectionner ou créer une quête</option>';

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
                <form  method="post"><input type="hidden" name="methode" value="sauve_quete" />
                <input type="hidden" name="aquete_cod" value="'.$aquete_cod.'" />
                <table width="80%" align="center">';

        echo '<tr><td><b>Nom de la quête </b>:</td><td><input type="text" name="aquete_nom" value="'.$quete->aquete_nom.'"></td></tr>';
        echo '<tr><td><b>Description </b>:</td><td><input type="text" size=80 name="aquete_description" value="'.$quete->aquete_description.'"></td></tr>';
        echo '<tr><td><b>Quête ouverte </b>:</td><td>'.create_selectbox("aquete_actif", array("O"=>"Oui","N"=>"Non"), $quete->aquete_actif).' <i>activation/désactivation général</i></td></tr>';
        echo '<tr><td><b>Début </b><i style="font-size: 7pt;">(dd/mm/yyyy hh:mm:ss)</i>:</td><td><input type="text" size=18 name="aquete_date_debut" value="'.$quete->aquete_date_debut.'"> <i>elle ne peut pas être commencée avant cette date (pas de limite si vide)</i></td></tr>';
        echo '<tr><td><b>Fin </b><i style="font-size: 7pt;">(dd/mm/yyyy hh:mm:ss)</i>:</td><td><input type="text" size=18 name="aquete_date_fin" value="'.$quete->aquete_date_fin.'"> <i>elle ne peut plus être commencée après cette date (pas de limite si vide)</i></td></tr>';
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
            // Lister les étapes déjà créées
            $etapes = $quete->get_etapes() ;

            // La quete existe proposer l'ajout d'étape ==>  Si c'est la première etape, elle doit-être du type START
            $filter = (!$etapes || sizeof($etapes)==0) ? "where aqetapmodel_tag='#START'" : "where aqetapmodel_tag<>'#START'" ;
            echo '<tr><td colspan="2"><input class="test" type="submit" value="sauvegarder la quête" /></td></tr>';
            echo '</table>';
            echo '</form>';
            echo '<hr>';

            if ( $etapes)
            {
                foreach ($etapes as $k => $etape)
                {
                    $etape_modele = new aquete_etape_modele;
                    $etape_modele->charge($etape->aqetape_aqetapmodel_cod);    // On charge le modele de l'étape.

                    echo '<form  method="post"><input type="hidden" id="etape-methode-'.$k.'" name="methode" value=""/>';
                    echo '<input type="hidden" name="aquete_cod" value="'.$aquete_cod.'" />';
                    echo '<input type="hidden" name="aqetape_cod" value="'.$etape->aqetape_cod.'" />';
                    echo '<input type="hidden" name="aqetapmodel_cod" value="'.$etape->aqetape_aqetapmodel_cod.'" />';
                    echo "Etape #{$etape->aqetape_cod}: <b>{$etape->aqetape_nom}</b> basée sur le modèle <b>{$etape_modele->aqetapmodel_nom}</b>:<br>";
                    echo "&nbsp;&nbsp;&nbsp;{$etape_modele->aqetapmodel_description} <br>";
                    echo "&nbsp;&nbsp;&nbsp;Texte de l'étape: {$etape->aqetape_texte}<br>";
                    echo '<input class="test" type="submit" name="edite_etape" value="Editer l\'étape" onclick="$(\'#etape-methode-'.$k.'\').val(\'edite_etape\');">&nbsp;&nbsp;&nbsp;&nbsp;';
                    // LE bouton "supprimer" nsur la première etape 'est possible que s'il n'y a qu'une etape.
                    if ($k!=0 || sizeof($etapes)==1)
                    {
                        echo '<input class="test" type="submit" name="supprime_etape" value="Supprimer l\'étape" onclick="$(\'#etape-methode-'.$k.'\').val(\'supprime_etape\');">';
                    }
                    echo '</form>';
                    echo '<hr>';

                }
            }

            echo '<form  method="post"><input type="hidden" name="methode" value="ajoute_etape"/>';
            echo '<input type="hidden" name="aquete_cod" value="'.$aquete_cod.'" />';
            echo '<table width="80%" align="center">';
            echo '<tr style=""><td colspan="2">Choisir un type d\'étape '.create_selectbox_from_req("aqetapmodel_cod", "select aqetapmodel_cod, aqetapmodel_nom from quetes.aquete_etape_modele {$filter} order by aqetapmodel_nom").' et <input class="test" type="submit" value="ajouter une étape"" /></td></tr>';
            echo '</table>';
            echo '</form>';
        }

        // Fin saisie  ----------------------------------------------------------------------
        echo '<hr>';
    }
    //=======================================================================================
    //-- Section dédiée aux étapes (ajout/modif)
    //---------------------------------------------------------------------------------------
    else if ( ( $_REQUEST['methode'] == "edite_etape" ) || ( $_REQUEST['methode'] == "ajoute_etape" ) )
    {
        $quete = new aquete;
        $quete->charge($aquete_cod);    // On charge la quete

        $aqetapmodel_cod = 1*$_REQUEST["aqetapmodel_cod"] ;
        $etape_modele = new aquete_etape_modele;
        $etape_modele->charge($aqetapmodel_cod);    // On charge le modele de l'étape.

        $aqetape_cod = 1*$_REQUEST["aqetape_cod"] ;
        $etape = new aquete_etape;
        $etape->charge($aqetape_cod);    // On charge l'étape elle-même.

        echo '<b>Quête</b> #'.$aquete_cod.' - '.$quete->aquete_nom.'<br><hr>';
        echo '<b>Caractéristiques de l\'étape</b>'. ($aqetape_cod>0 ? " #$aqetape_cod" : "");

        // Mise en forme de l'étape pour la saisie des infos.
        echo '  <br>
                <form  method="post"><input type="hidden" id="etape-methode" name="methode" value="sauve_etape" />
                <input type="hidden" name="aquete_cod" value="'.$aquete_cod.'" />
                <input type="hidden" name="aqetape_cod" value="'.$aqetape_cod.'" />
                <input type="hidden" name="aqetapmodel_cod" value="'.$aqetapmodel_cod.'" />
                <table width="80%" align="center">';

        echo '<tr><td colspan="2">'.$etape_modele->aqetapmodel_description.'</td></tr>';
        echo '<tr><td><b>Exemple </b>:</td><td>'.$etape_modele->aqetapmodel_modele.'<br><br></td></tr>';
        echo '<tr><td><b>Nom de l\'étape </b>:</td><td><input type="text" size="50" name="aqetape_nom" value="'.$etape->aqetape_nom.'"></td></tr>';
        echo '<tr><td><b>Texte de l\'étape </b>:</td><td><textarea style="min-height: 80px; min-width: 500px;" name="aqetape_texte">'.( $etape->aqetape_texte != "" ? $etape->aqetape_texte : $etape_modele->aqetapmodel_modele).'</textarea></td></tr>';
        echo '</table>';


        $param_liste = $etape_modele->get_liste_parametres();

        foreach ($param_liste as $param_id => $param)
        {

            // Certain paramètre peuvent être remplacé par un paramètre d'étape déjà saisi précédement
            if ( in_array($param['type'], array("perso")) ) $alternate_type = true ; else $alternate_type = false ;


            echo '<br><br><b>Edition du paramètre ['.$param_id.']</b>: <i>('.$param['texte'].')</i><br>';
            echo $param['desc'].'</i><br><br>';

            $element = new aquete_element;
            $elements = $element->getBy_etape_param_id($aqetape_cod, $param_id) ;
            if (!$elements) $elements[] =  new aquete_element;
            while ( sizeof($elements) < (1*$param['n']) )   $elements[] =  new aquete_element;

            if ($alternate_type)
            {
                echo 'Vous pouvez définir un nouveau paramètre ou choisir d\'en utiliser un défini lors d\'une étape précédente:';
                echo '<input type="hidden" id="alt-type-'.$param_id.'" name="element_type['.$param_id.']" value="'.($elements[0]->aqelem_type == "element" ? "element" : $param['type']).'">';
                echo '<input type="button" value="Changer" onClick="switchQueteAutoParamRow(\''.$param_id.'\', \''.$param['type'].'\');"><br><br>';
            }
            echo '<table width="80%" align="center">';
            //echo '<input type="hidden" id="max-param-'.$param_id.'" value="'.$param['M'].'">';

            $style_tr = "display: block;" ;
            if ($alternate_type)
            {
                // affcihage de l'élement alternatif !
                if ($elements[0]->aqelem_type == "element") $style_tr = "display: none;" ;

                $row_id = "row-$param_id-0-"; // paramètre en 0 pour ne pas parasiter avec les autres et pouvoir la distinguer !!
                echo   '<tr style="'.($elements[0]->aqelem_type=='element' ? "display: block;" : "display: none;").'" id="alt-'.$param_id.'"><td colspan="2">Paramètre d\'étape :
                                        <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($elements[0]->aqelem_type=='element' ? $elements[0]->aqelem_cod : '').'"> 
                                        <input name="aqelem_type['.$param_id.'][]" type="hidden" value="element"> 
                                        <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.($elements[0]->aqelem_type=='element' ? $elements[0]->aqelem_misc_cod : '').'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'element\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                        &nbsp;<i></i><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></i>
                                        &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","element","Rechercher un élément");\'> 
                                        </td>';
                echo "</tr>";
            }
            //echo "$param_id => <pre>"; print_r($elements);echo "</pre>";

            foreach($elements as $row => $element)
            {
                $row_id = "row-$param_id-$row-";
                $aqelem_misc_nom = "" ;
                echo   '<tr id="'.$row_id.'" style="'.$style_tr.'"><td><input type="button" class="test" value="Supprimer" onClick="delQueteAutoParamRow($(this).parent(\'td\').parent(\'tr\'), '.$param['n'].');"></td>';
                switch ($param['type'])
                {
                    case 'perso':
                            if (1*$element->aqelem_misc_cod != 0)
                            {
                                $perso = new perso ;
                                $perso->charge( $element->aqelem_misc_cod );
                                $aqelem_misc_nom = $perso->perso_nom ;
                            }
                            echo   '<td>Perso :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_cod : '').'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                    <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_misc_cod : '').'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'perso\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                    &nbsp;<i></i><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></i>
                                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","perso","Rechercher un perso");\'> 
                                    </td>';
                    break;

                    case 'lieu':
                            if (1*$element->aqelem_misc_cod != 0)
                            {
                                $lieu = new lieu ;
                                $lieu->charge( $element->aqelem_misc_cod );
                                $aqelem_misc_nom = $lieu->lieu_nom ;
                            }
                            echo   '<td>Lieu :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.$element->aqelem_cod.'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                    <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.$element->aqelem_misc_cod.'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'lieu\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                    &nbsp;<i></i><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></i>
                                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","lieu","Rechercher un lieu");\'> 
                                    </td>';
                    break;

                    case 'lieu_type':
                            if (1*$element->aqelem_misc_cod != 0)
                            {
                                $lieu_type = new lieu_type ;
                                $lieu_type->charge( $element->aqelem_misc_cod );
                                $aqelem_misc_nom = $lieu_type->tlieu_libelle ;
                            }
                            echo   '<td>Type de lieu :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.$element->aqelem_cod.'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                    <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.$element->aqelem_misc_cod.'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'lieu_type\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                    &nbsp;<i></i><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></i>
                                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","lieu_type","Rechercher un type de lieu");\'>
                                    <br>Situé en dessous de&nbsp;:'.create_selectbox_from_req("aqelem_param_num_1[$param_id][]", $request_select_etage, $element->aqelem_param_num_1,     array('id' =>"{$row_id}aqelem_param_num_1", 'style'=>'style="width: 150px;"')).'
                                    et situé au dessus de:'.create_selectbox_from_req("aqelem_param_num_2[$param_id][]", $request_select_etage, $element->aqelem_param_num_2,     array('id' =>"{$row_id}aqelem_param_num_2", 'style'=>'style="width: 150px;"')).'
                                    
                                    </td>';
                    break;

                    case 'choix':
                        if (1*$element->aqelem_misc_cod < 0)
                        {
                            $aqelem_misc_nom = "Quitter/Abandonner" ;
                        }
                        else if (1*$element->aqelem_misc_cod == 0)
                        {
                            $aqelem_misc_nom = "Etape suivante" ;
                        }
                        else
                        {
                            $aquete_etape = new aquete_etape ;
                            $aquete_etape->charge( $element->aqelem_misc_cod );
                            $aqelem_misc_nom = $aquete_etape->aqetape_nom ;
                        }
                        echo   '<td>Choix  :
                                <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.$element->aqelem_cod.'"> 
                                <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                <input data-entry="val" name="aqelem_param_txt_1['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_1" type="text" size="80" value="'.$element->aqelem_param_txt_1.'"> <br>Etape si choisi:
                                <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.$element->aqelem_misc_cod.'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'etape\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                &nbsp;<i></i><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></i>
                                &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","etape","Rechercher une etape", ['.$aquete_cod.']);\'> 
                                </td>';
                        break;

                    default:
                        echo '<td>Type de paramètre inconnu</td>';
                    break;
                }
                echo "</tr>";
            }
            echo '<tr id="add-'.$row_id.'"><td> <input type="button" class="test" value="ajouter" onClick="addQueteAutoParamRow($(this).parent(\'td\').parent(\'tr\').prev(), '.$param['M'].');"> </td></tr>';
            echo '</table>';
        }

        echo '<table width="80%" align="center">';
        echo '<tr><td colspan="2"><br><input class="test" type="submit" value="Annuler" onclick="$(\'#etape-methode\').val(\'edite_quete\');"/>&nbsp;&nbsp;&nbsp;<input class="test" type="submit" value="Sauvegarder l\'étape" /></td></tr>';
        echo '</table>';

        echo '</form>';
        echo '<hr>';

    }
}


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
