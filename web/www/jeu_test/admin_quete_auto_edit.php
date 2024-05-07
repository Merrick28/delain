<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
include_once '../includes/tools.php';

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

// Editeur WYSIWYG pour le texte d'étape! (SCEdtor) / Avait été retiré? pourquoi? par qui ?
echo '<link href="/styles/sceditor.min.css" rel="stylesheet">';
echo '<script src="/scripts/sceditor.min.js" type="text/javascript"></script>';
echo '<script src="/scripts/sceditor-xhtml.min.js" type="text/javascript"></script>';
echo '<script>//# sourceURL=admin_quete_auto_edit.js
 $( document ).ready(function() {
    var textarea = document.getElementById("id-textarea-etape");
    sceditor.create(textarea, {
        format: "xhtml",
        style: "/styles/sceditor.min.css",
        toolbar: "bold,italic,underline,strike,subscript,superscript|left,center,right,justify|size,color,removeformat|table,quote,image|maximize|source",
    });
});
</script>';

//
//Contenu de la div de droite
//

$contenu_page = '';

$droit_modif = 'dcompt_animations';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0)
{
    //=======================================================================================
    // == Main
    //=======================================================================================
    // On est admin ici, on a les droits sur les quetes
    // Traitement des paramètres
    $aquete_cod = 1*$_REQUEST['aquete_cod'] ;
    $pos_etage = 1*$_REQUEST['pos_etage'] ;     // SI $pos_etage >0, on est dans le cas des interactions sinon QA standard
    //-- traitement des actions
    if(isset($_REQUEST['methode']))
    {
        // Traitement des actions
        include ("admin_traitement_quete_auto_edit.php");
    }
    //echo "<pre>"; print_r($_REQUEST); echo "</pre>";

    //=======================================================================================
    // == Constantes quete_auto
    //=======================================================================================
    //$request_select_etage_ref = "SELECT null etage_cod, 'Aucune restriction' etage_libelle, null etage_numero UNION SELECT etage_cod, etage_libelle, etage_numero from etage where etage_reference = etage_numero order by etage_numero desc" ;
    $request_select_etage_ref = "SELECT etage_numero, etage_libelle from etage where etage_reference = etage_numero order by etage_numero desc" ;
    $request_select_etage = "SELECT etage_numero, case when etage_reference <> etage_numero then ' |- ' else '' end || etage_libelle as etage_libelle from etage order by etage_reference desc, etage_numero";

    //=======================================================================================
    //-- On commence par l'édition de la quete elle-meme (ajout/modif)
    //---------------------------------------------------------------------------------------
    if(!isset($_REQUEST['methode']) || ($_REQUEST['methode']=='edite_quete')) {

        $type_quete = isset($_REQUEST["type_quete"]) ? $_REQUEST["type_quete"] : 0 ;

        if ($pos_etage>0) {
            echo '   <span style="color: white; background-color: #800000; font-weight:bold">&nbsp;&nbsp;&nbsp;GESTION DES QUËTES D’INTERACTIONS SUR L’ETAGE&nbsp;&nbsp;&nbsp;</span>';
        }
        // Liste des quetes existantes
        echo '  <TABLE width="80%" align="center">
                <TR>
                <TD>
                <form method="post">
                Filter les quêtes sur :<select onchange="this.parentNode.submit();" name="type_quete">
                                <option '.($type_quete==0 ? " selected " : "").'value="0">Les quêtes ouvertes</option>
                                <option '.($type_quete==1 ? " selected " : "").'value="1">Les quêtes fermées</option>
                                <option '.($type_quete==2 ? " selected " : "").'value="2">Toutes les quêtes</option>
                                </select>
                Editer la quête:<select onchange="this.parentNode.submit();" name="aquete_cod"><option value="0">Sélectionner ou créer une quête</option>';

        if ($type_quete==0)
            $filtre_quete = " and aquete_actif = 'O'";
        else if ($type_quete==1)
            $filtre_quete = " and aquete_actif = 'N'";
        else
            $filtre_quete = "";


        if ($pos_etage>0) {
            $stmt = $pdo->query('select aquete_nom_alias, aquete_cod from quetes.aquete where aquete_pos_etage = '.$pos_etage.$filtre_quete.' order by aquete_nom_alias');
        } else {
            $stmt = $pdo->query('select aquete_nom_alias, aquete_cod from quetes.aquete where aquete_pos_etage is null '.$filtre_quete.' order by aquete_nom_alias');
        }
        while ($result = $stmt->fetch())
        {
            echo '<option value="' . $result['aquete_cod'];
            if ($result['aquete_cod'] == $aquete_cod) echo '" selected="selected';
            echo '">' . $result['aquete_nom_alias'] . '</option>';
        }
        echo '  </select>
                <!--input type="submit" value="Valider"-->
                </form></TD>
                </TR>
                </TABLE>
                <HR>';

        if ($pos_etage>0) {
            echo '<strong>Caractéristiques de l\'interaction</strong>'. ($aquete_cod>0 ? " #$aquete_cod" : "");
            echo' <br><em style="font-size:10px">Une interaction est une Quête-auto spécifique à un etage et déclenchée sur une position determinée.</em>';
            echo' <br><em style="font-size:10px">Toutes les étapes de cette interaction ne pourront avoir lieu QUE sur cette position.</em>';
            echo' <br><em style="font-size:10px">Si le joueur quitte la position, l\'interaction est terminée et reprendra au départ s\'il revient en place.</em>';

        } else {
            echo '<strong>Caractéristiques de la Quête</strong>'. ($aquete_cod>0 ? " #$aquete_cod" : "");
        }

        // La quête elle-même ----------------------------------------------------------------------
        $quete = new aquete;
        $quete->charge($aquete_cod);

        echo '  <br>
                <script>
                function confirm_delete_quete() {
                    var ok = confirm(\'Êtes-vous sûr de vouloir supprimer entièrement la quête?\') ;
                    if (!ok) return false;
                    $(\'#quete-methode\').val(\'delete_quete\');
                    return true;                    
                }                
                function confirm_dupliquer_quete() {
                    var ok = confirm(\'Êtes-vous sûr de vouloir dupliquer entièrement la quête?\') ;
                    if (!ok) return false;
                    $(\'#quete-methode\').val(\'dupliquer_quete\');
                    return true;                    
                }
                function confirm_terminer_quete(nbq) {
                    var ok = confirm(\'Êtes-vous sûr de vouloir terminer les \'+ nbq + \' quêtes en cours?\\nATTENTION: La quête ira directement à l’état "terminer", pour tous les utilisateurs l’ayant commencé!!\') ;
                    if (!ok) return false;
                    $(\'#quete-methode\').val(\'terminer_quete\');
                    return true;                    
                }
                </script>
  
                <form  method="post"><input id="quete-methode" type="hidden" name="methode" value="sauve_quete" />';

        echo    '<input type="hidden" name="aquete_cod" value="'.$aquete_cod.'" />';
        echo    '<input type="hidden" name="type_quete" value="'.$type_quete.'" />';
        if ($pos_etage>0) {
            echo '<input type="hidden" name="aquete_journal_archive" value="N" />';
            echo '<input type="hidden" name="aquete_nb_max_instance" value="" />';
            echo '<input type="hidden" name="aquete_nb_max_participant" value="" />';
            echo '<input type="hidden" name="aquete_limite_triplette" value="N" />';
            echo '<input type="hidden" name="aquete_nb_max_rejouable" value="" />';
            echo '<input type="hidden" name="aquete_nb_max_quete" value="" />';
            echo '<input type="hidden" name="aquete_max_delai" value="" />';
            echo '<input type="hidden" name="aquete_pos_etage" value="'.$pos_etage.'" />';
        } else {
            echo '<input type="hidden" name="aquete_pos_etage" value="" />';
        }
        echo    '<table width="80%" align="center">';

        echo '<tr><td><strong>Nom de référence admin </strong>:</td><td><input size=80 type="text" name="aquete_nom_alias" value="'.htmlspecialchars($quete->aquete_nom_alias).'"></td></tr>';
        echo '<tr><td><strong>Nom de la quête </strong>:</td><td><input size=80 type="text" name="aquete_nom" value="'.htmlspecialchars($quete->aquete_nom).'"></td></tr>';
        echo '<tr><td><strong>Description (visible par le joueur) </strong>:</td><td><input type="text" size=80 name="aquete_description" value="'.htmlspecialchars($quete->aquete_description).'"></td></tr>';
        echo '<tr><td><strong>Quête ouverte </strong>:</td><td>'.create_selectbox("aquete_actif", array("O"=>"Oui","N"=>"Non"), $quete->aquete_actif).' <em>Activation/désactivation générale</em></td></tr>';
        if ($pos_etage==0) {
            echo '<tr><td><strong>Archivage dans le journal </strong>:</td><td>'.create_selectbox("aquete_journal_archive", array("O"=>"Oui","N"=>"Non"), $quete->aquete_journal_archive).' <em>Faut-il mettre la quette dans le journal des quêtes terminée ?</em></td></tr>';
        }
        echo '<tr><td><strong>Début </strong><em style="font-size: 7pt;">(dd/mm/yyyy hh:mm:ss)</em>:</td><td><input type="text" size=18 name="aquete_date_debut" value="'.$quete->aquete_date_debut.'"> <em>Elle ne peut pas être commencée avant cette date (pas de limite si vide)</em></td></tr>';
        echo '<tr><td><strong>Fin </strong><em style="font-size: 7pt;">(dd/mm/yyyy hh:mm:ss)</em>:</td><td><input type="text" size=18 name="aquete_date_fin" value="'.$quete->aquete_date_fin.'"> <em>Elle ne peut plus être commencée après cette date (pas de limite si vide)</em></td></tr>';

        $nb_quete_en_cours = $quete->get_nb_en_cours();

        if ($pos_etage==0) {
            echo '<tr><td><strong>Nb. quête simultanée</strong>:</td><td><input type="text" size=10 name="aquete_nb_max_instance" value="' . $quete->aquete_nb_max_instance . '"> <em>Nombre maximum de joueurs qui peuvent prendre la quête (pas de limite si vide)</em></td></tr>';
            echo '<tr style="display:none;"><td><strong></strong><del>Nb. participants max</del></strong>:</td><td><input disabled type="text" size=10 name="aquete_nb_max_participant" value="1"> <del><em>Nombre max de perso pouvant la faire ensemble (pas de limite si vide)</del></em></td></tr>';
            echo '<tr><td><strong>Limitation à la triplette </strong>:</td><td>'.create_selectbox("aquete_limite_triplette", array("N"=>"Non", "O"=>"Limité"), $quete->aquete_limite_triplette).' <em>Limiter la quête à un seul perso de la tripplette</em></td></tr>';
            echo '<tr><td><strong>Nb. rejouabilité</strong>:</td><td><input type="text" size=10 name="aquete_nb_max_rejouable" value="' . $quete->aquete_nb_max_rejouable . '"> <em>Nombre de fois où elle peut être jouée par un même perso/triplette (pas de limite si vide)</em></td></tr>';
            echo '<tr><td><strong>Nb. de quête</strong>:</td><td><input type="text" size=10 name="aquete_nb_max_quete" value="' . $quete->aquete_nb_max_quete . '"> <em>Nombre de fois où elle peut être rejouée tous persos confondus (pas de limite si vide)</em></td></tr>';
            echo '<tr><td><strong>Délai max. </strong><em style="font-size: 7pt;">(en jours)</em>:</td><td><input type="text" size=10 name="aquete_max_delai" value="' . $quete->aquete_max_delai . '"> <em>Délai max alloué pour la quête (pas de limite si vide)</em></td></tr>';
            echo '<tr><td><strong>Info sur Nb. Réalisation</strong>:</td><td style="color:#800000">Il y a <strong>' . $nb_quete_en_cours . '</strong> quête en cours sur <strong>' . $quete->get_nb_total() . '</strong> au total <em>(tous persos confondus)</em>';
            if ($nb_quete_en_cours > 0) echo '&nbsp;&nbsp;&nbsp;<input class="test" onclick="return confirm_terminer_quete('.$nb_quete_en_cours.');";  type="submit" value="Terminer les quêtes en cours" />';
            echo '</td></tr>';
        }
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
            $liste_etape = array();
            $liste_etape[0]="Tout à la fin";
            if ($pos_etage==0) {
                $filter = (!$etapes || sizeof($etapes) == 0) ? "where aqetapmodel_tag='#START'" : "where aqetapmodel_tag<>'#START' AND aqetapmodel_tag<>'#START #INTERACTION'";
            } else {
                $filter = (!$etapes || sizeof($etapes) == 0) ? "where aqetapmodel_tag='#START #INTERACTION'" : "where aqetapmodel_tag<>'#START' AND aqetapmodel_tag<>'#START #INTERACTION'";
            }
            echo '<tr><td colspan="2"><input class="test" type="submit" value="sauvegarder la quête" />';
            echo '&nbsp;&nbsp;&nbsp;<input class="test" onclick="return confirm_dupliquer_quete();";  type="submit" value="dupliquer la quête" />';
            if ($nb_quete_en_cours==0)
                echo '&nbsp;&nbsp;&nbsp;<input class="test" onclick="return confirm_delete_quete();"; type="submit" value="Supprimer la quête" />';
            else
                echo '&nbsp;&nbsp;&nbsp;<em>Suppression de la quête impossible, car il y a des réalisations en cours.</em>';
            echo '</td></tr>';
            //if ($nb_quete_en_cours>0)  echo '<tr><td colspan="2"><u><strong>ATTENTION</strong></u>: il y a déjà <strong>'.$nb_quete_en_cours.'</strong> perso(s) en cours de réalisation de cette quête.</td></tr>';
            echo '</table>';
            echo '</form>';
            echo '<hr>';

            if ( $etapes)
            {
                $tag=date("mdHis");
                foreach ($etapes as $k => $etape)
                {
                    // Preparation de la liste des etape pour la boite de selection
                    $liste_etape[$etape->aqetape_cod]='Après #'.$etape->aqetape_cod.' : '.$etape->aqetape_nom;

                    $etape_modele = new aquete_etape_modele;
                    $etape_modele->charge($etape->aqetape_aqetapmodel_cod);    // On charge le modele de l'étape.


                    echo '<div id="etape-'.$etape->aqetape_cod.'" style="display: flex;"><div style="width: 30px;">';
                    if ($k>1)
                        echo '<a href="?methode=deplace_etape&tag='.$tag.'&move=up&aquete_cod='.$aquete_cod.'&aqetape_cod='.$etape->aqetape_cod.'#etape-'.$etape->aqetape_cod.'"><img src="/images/up-24.png"></a><br><br>';
                    if(($etape->aqetape_etape_cod!=0) && ($k>0))
                        echo '<a href="?methode=deplace_etape&tag='.$tag.'&move=down&aquete_cod='.$aquete_cod.'&aqetape_cod='.$etape->aqetape_cod.'#etape-'.$etape->aqetape_cod.'"><img src="/images/down-24.png"></a>';
                    echo '</div><div>';

                    echo '<form  method="post"><input type="hidden" id="etape-methode-'.$k.'" name="methode" value=""/>';
                    echo '<input type="hidden" name="aquete_cod" value="'.$aquete_cod.'" />';
                    echo '<input type="hidden" name="aqetape_cod" value="'.$etape->aqetape_cod.'" />';
                    echo '<input type="hidden" name="aqetapmodel_cod" value="'.$etape->aqetape_aqetapmodel_cod.'" />';
                    echo "<font color='blue'>Etape #{$etape->aqetape_cod}:</font> <strong>{$etape->aqetape_nom}</strong> basée sur le modèle <strong>{$etape_modele->aqetapmodel_nom}</strong>:<br>";
                    echo "&nbsp;&nbsp;&nbsp;{$etape_modele->aqetapmodel_description} <br>";
                    echo "&nbsp;&nbsp;&nbsp;Texte de l'étape: <em style='color: white'>{$etape->aqetape_texte}</em><br>";
                    echo '<input class="test" type="submit" name="edite_etape" value="Editer l\'étape" onclick="$(\'#etape-methode-'.$k.'\').val(\'edite_etape\');">&nbsp;&nbsp;&nbsp;&nbsp;';
                    // LE bouton "supprimer" nsur la première etape 'est possible que s'il n'y a qu'une etape.
                    $nb_encours_etape = $quete->get_nb_en_cours($etape->aqetape_cod);

                    // On ne peut pas dupliquer la 1ere étape
                    if ($k!=0)
                    {
                        echo '<input class="test" type="submit" name="duplique_etape" value="Dupliquer l\'étape" onclick="$(\'#etape-methode-'.$k.'\').val(\'duplique_etape\');">&nbsp;&nbsp;&nbsp;&nbsp;' ;
                    }
                    if (($k!=0 || sizeof($etapes)==1) && ($nb_encours_etape==0))
                    {
                        $nb_quete_en_cours = $quete->get_nb_en_cours($etape->aqetape_cod);
                        if ($nb_quete_en_cours>0)
                        {
                            echo 'Il y a <strong>' . $nb_quete_en_cours . '</strong> persos en cours à cette étape (<em style="font-size:9px;">les persos à cette étape ne subiront pas les modifications faites par l\'édition</em>)';
                        } else
                        {
                            echo '<input class="test" type="submit" name="supprime_etape" value="Supprimer l\'étape" onclick="$(\'#etape-methode-'.$k.'\').val(\'supprime_etape\');">';
                        }
                    }
                    else if ($nb_encours_etape>0)
                    {
                        // sauf sur la dernière étape on propose le menu pour forcer le passage
                        if ($k<(sizeof($etapes) -1))
                        {
                            echo '<input class="test" type="submit" name="skip_etape" value="Forcer le passage" onclick="$(\'#etape-methode-' . $k . '\').val(\'skip_etape\');">';
                        }
                        echo '&nbsp;&nbsp;&nbsp;<span style="color:#800000"><u><strong>ATTENTION</strong></u>: il y a <strong>'.$nb_encours_etape.'</strong> quête en cours de réalisation à cette etape. (<em style="font-size: 10px;">les modifications n\'impacteront que les futures réalisations</em>)</span>';
                    }
                    echo '</form>';
                    echo '</div></div>';

                    if (in_array($etape_modele->aqetapmodel_tag, array("#SAUT #CONDITION #DETRUIRE #OBJET", "#SAUT #MULTIPLE #CONDITION #PERSO", "#SAUT #CONDITION #EQUIPE", "#CHOIX", "#START", "#SAUT","#SAUT #CONDITION #ETAPE","#SAUT #CONDITION #PERSO","#SAUT #CONDITION #DIALOGUE","#SAUT #CONDITION #PA","#SAUT #CONDITION #MECA","#SAUT #CONDITION #CODE","#SAUT #CONDITION #INTERACTION","#SAUT #CONDITION #ALEATOIRE","#SAUT #CONDITION #ALEATOIRE","#SAUT #CONDITION #COMPETENCE","#SAUT #CONDITION #CARAC")))
                    {
                        $type_saut = $etape_modele->aqetapmodel_tag=="#SAUT" ? "inconditionnel" : "conditionnel" ;
                        $element = new aquete_element;
                        if (in_array($etape_modele->aqetapmodel_tag, array("#START", "#SAUT #MULTIPLE #CONDITION #PERSO", "#SAUT #CONDITION #ETAPE", "#SAUT #CONDITION #PERSO", "#SAUT #CONDITION #DIALOGUE", "#SAUT #CONDITION #PA", "#SAUT #CONDITION #INTERACTION", "#SAUT #CONDITION #ALEATOIRE")))
                        {
                            $elements = $element->getBy_etape_param_id($etape->aqetape_cod, 2) ;
                            if (($etape_modele->aqetapmodel_tag == "#SAUT #MULTIPLE #CONDITION #PERSO")||($etape_modele->aqetapmodel_tag == "#SAUT #CONDITION #ETAPE")||($etape_modele->aqetapmodel_tag == "#SAUT #CONDITION #PERSO")||($etape_modele->aqetapmodel_tag == "#SAUT #CONDITION #PA")||($etape_modele->aqetapmodel_tag == "#SAUT #CONDITION #DIALOGUE")||($etape_modele->aqetapmodel_tag == "#SAUT #CONDITION #INTERACTION"))
                            {
                                $elements = is_array($elements) ? array_merge($elements, $element->getBy_etape_param_id($etape->aqetape_cod, 3)) : $element->getBy_etape_param_id($etape->aqetape_cod, 3);
                            }
                        } else if (in_array($etape_modele->aqetapmodel_tag, array("#SAUT #CONDITION #COMPETENCE")))
                        {
                            $elements = $element->getBy_etape_param_id($etape->aqetape_cod, 4) ;
                            $elements = array_merge($elements, $element->getBy_etape_param_id($etape->aqetape_cod, 5));
                            $elements = array_merge($elements, $element->getBy_etape_param_id($etape->aqetape_cod, 6));
                            $elements = array_merge($elements, $element->getBy_etape_param_id($etape->aqetape_cod, 7));
                            $elements = array_merge($elements, $element->getBy_etape_param_id($etape->aqetape_cod, 8));
                        } else if (in_array($etape_modele->aqetapmodel_tag, array("#SAUT #CONDITION #CARAC")))
                        {
                            $elements = $element->getBy_etape_param_id($etape->aqetape_cod, 4) ;
                            $elements = array_merge($elements, $element->getBy_etape_param_id($etape->aqetape_cod, 5));
                            $elements = array_merge($elements, $element->getBy_etape_param_id($etape->aqetape_cod, 6));
                            $elements = array_merge($elements, $element->getBy_etape_param_id($etape->aqetape_cod, 7));
                        } else if (in_array($etape_modele->aqetapmodel_tag, array("#SAUT #CONDITION #CODE")))
                        {
                            $elements = $element->getBy_etape_param_id($etape->aqetape_cod, 4) ;
                            $elements = array_merge($elements, $element->getBy_etape_param_id($etape->aqetape_cod, 5));
                        } else if (in_array($etape_modele->aqetapmodel_tag, array("#SAUT #CONDITION #EQUIPE")))
                        {
                            $elements = $element->getBy_etape_param_id($etape->aqetape_cod, 6) ;
                            $elements = array_merge($elements, $element->getBy_etape_param_id($etape->aqetape_cod, 5));
                        } else if (in_array($etape_modele->aqetapmodel_tag, array("#SAUT #CONDITION #MECA", "#SAUT #CONDITION #DETRUIRE #OBJET")))
                        {
                            $elements = $element->getBy_etape_param_id($etape->aqetape_cod, 3) ;
                            $elements = array_merge($elements, $element->getBy_etape_param_id($etape->aqetape_cod, 4));
                        } else
                        {
                            $elements = $element->getBy_etape_param_id($etape->aqetape_cod, 1) ;
                        }
                        foreach ($elements as $k => $element)
                        {
                            if ($element->aqelem_misc_cod>0)
                            {
                                $e = new aquete_etape;
                                if (!$e->charge($element->aqelem_misc_cod))    // on charge l'étape pour récupérer le nom!
                                {
                                    echo "<strong style='color: red'>&rArr; Saut {$type_saut} vers Etape #{$element->aqelem_misc_cod}</strong> <em style='color: red'>(étape inexistante)</em><br>";
                                }
                                else
                                {
                                    echo "<strong style='color: blue'>&rArr; Saut {$type_saut} vers Etape #{$element->aqelem_misc_cod}</strong> <em>({$e->aqetape_nom})</em><br>";
                                }
                            }
                            else if ($element->aqelem_misc_cod==0)
                            {
                                echo "<strong style='color: blue'>&rArr; Etape suivante</strong> <br>";
                            }
                            else if ($element->aqelem_misc_cod<0) //if ($etape_modele->aqetapmodel_tag!="#CHOIX")
                            {
                                echo "<strong style='color: blue'>&rArr; Fin de la quête</strong> <br>";
                            }
                        }
                    }

                    // Saut d'étape interne à toutes les étapes
                    if ( (int)$etape->aqetape_saut_etape_cod != 0 )
                    {
                        $e = new aquete_etape;
                        if (!$e->charge($etape->aqetape_saut_etape_cod))    // on charge l'étape pour récupérer le nom!
                        {
                            if ($etape->aqetape_saut_etape_cod < 0)
                            {
                                echo "<strong style='color: yellow'>&rArr; Etape suivante #{$etape->aqetape_saut_etape_cod}</strong> <em style='color: yellow'>Fin de la quête</em><br>";
                            } else {
                                echo "<strong style='color: red'>&rArr; Etape suivante #{$etape->aqetape_saut_etape_cod}</strong> <em style='color: red'>(étape inexistante)</em><br>";

                            }
                        }
                        else
                        {
                            echo "<strong style='color: yellow'>&rArr; Etape suivante #{$etape->aqetape_saut_etape_cod}</strong> <em>({$e->aqetape_nom})</em><br>";
                        }
                    }

                    // Saut d'étape timeout (sortie par le delai)
                    $element = new aquete_element;
                    $elements = $element->getBy_etape_param_id($etape->aqetape_cod, 1);
                    if ($elements && sizeof($elements) >= 1 && $elements[0]->aqelem_type == 'delai' && $elements[0]->aqelem_param_num_1 > 0)
                    {
                        $e = new aquete_etape;
                        if ($elements[0]->aqelem_misc_cod == 0)
                        {
                            echo "<strong style='color: yellow'>&rArr; Etape Hors-delai #{$elements[0]->aqelem_misc_cod}</strong> <em style='color: yellow'>Etape suivante</em><br>";
                        }
                        else if ($elements[0]->aqelem_misc_cod < 0)
                        {
                            echo "<strong style='color: yellow'>&rArr; Etape Hors-delai #{$elements[0]->aqelem_misc_cod}</strong> <em style='color: yellow'>Fin de la quête</em><br>";
                        }
                        else if (!$e->charge($elements[0]->aqelem_misc_cod))    // on charge l'étape pour récupérer le nom!
                        {
                            echo "<strong style='color: red'>&rArr; Etape Hors-delai #{$elements[0]->aqelem_misc_cod}</strong> <em style='color: red'>(étape inexistante)</em><br>";
                        }
                        else
                        {
                            echo "<strong style='color: yellow'>&rArr; Etape Hors-delai #{$e->aqetape_cod}</strong> <em>({$e->aqetape_nom})</em><br>";
                        }
                    }

                    if ($etape_modele->aqetapmodel_tag=="#END #KO")
                        echo '<div class="hr">&nbsp;&nbsp;<strong  style=\'color: blue\'>Fin de la Quête sur un Echec</strong>&nbsp;&nbsp;</div>';
                    else if ( (($etape_modele->aqetapmodel_tag=="#END #OK") || ($k == count($etapes)-1)) && ($etape_modele->aqetapmodel_tag!="#START") )
                        echo '<div class="hr">&nbsp;&nbsp;<strong  style=\'color: blue\'>Fin de la Quête avec Succès</strong>&nbsp;&nbsp;</div>';
                    else
                        echo '<hr>';
                }
            }

            echo '<form  method="post"><input type="hidden" name="methode" value="ajoute_etape"/>';
            echo '<input type="hidden" name="aquete_cod" value="'.$aquete_cod.'" />';
            echo '<table width="80%" align="center">';
            echo '<tr style=""><td colspan="2">Choisir un type d\'étape '.create_selectbox_from_req("aqetapmodel_cod", "select aqetapmodel_cod, aqetapmodel_nom from quetes.aquete_etape_modele {$filter} order by aqetapmodel_nom").' et <input class="test" type="submit" value="ajouter une étape"" />';
            if ( $etapes)
            {
                echo '&nbspinserer&nbsp:&nbsp'.create_selectbox("etape_position", $liste_etape, "",array("style"=>"style='width:250px;'")).'</td></tr>';
            }
            else
            {
                echo '<input type="hidden" name="etape_position" value="0">';
            }
            echo '</td></tr>';
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

        echo '<strong>Quête</strong> #'.$aquete_cod.' - '.$quete->aquete_nom_alias.'<br><hr>';
        echo '<strong>Caractéristiques de l\'étape</strong>'. ($aqetape_cod>0 ? " #$aqetape_cod" : "");

        // Mise en forme de l'étape pour la saisie des infos.
        echo '  <br>
                <form  method="post"><input type="hidden" id="etape-methode" name="methode" value="sauve_etape" />
                <input type="hidden" name="aquete_cod" value="'.$aquete_cod.'" />
                <input type="hidden" name="aqetape_cod" value="'.$aqetape_cod.'" />
                <input type="hidden" name="etape_position" value="'.$_REQUEST["etape_position"].'" />
                <input type="hidden" name="aqetapmodel_cod" value="'.$aqetapmodel_cod.'" />
                <table width="80%" align="center">';

        echo '<tr><td colspan="2">'.$etape_modele->aqetapmodel_description.'</td></tr>';
        echo '<tr><td><strong>Exemple </strong>:</td><td>'.$etape_modele->aqetapmodel_modele.'<br><br></td></tr>';
        echo '<tr><td><strong>Nom de l\'étape </strong>:</td><td><input type="text" size="50" name="aqetape_nom" value="'.htmlspecialchars($etape->aqetape_nom).'"></td></tr>';
        echo '<tr><td><strong>Texte de l\'étape </strong>:</td><td><textarea id="id-textarea-etape" style="min-height: 150px; min-width: 650px;" name="aqetape_texte">'.( $etape->aqetape_texte != "" ? $etape->aqetape_texte : $etape_modele->aqetapmodel_modele).'</textarea></td></tr>';
        echo '<tr><td></td><td><em style="font-size: 10px;">Ce texte sera afficher au début de l\'étape, il doit orienter l\'aventurier sur ce qu\'il doit faire pour poursuivre sa quête.<br>
                   Vous pouvez utiliser des images en les déposants sur le serveur à l\'aide de cet outil: <a target="_blank" href="/jeu_test/modif_etage3_images.php">ressources images</a><br><u>Nota</u>: Vous pouvez aussi utiliser ce texte pour le féliciter sur la réussite de l\'étape précédente.</em>&nbsp;
                   <a href="#" onclick="$(\'#info-variables\').slideToggle();"><img src="/images/info_16.png"></a><div id="info-variables" style="display:none;"><br>Le texte d\'étape peut contenir des <u>variables</u>:<br>
                   <br>* [X] est une représentation en texte du paramètre X de l\'étape (exemple [1], [2] etc...<br> 
                   <br>* [#perso.XXXXX] est une représentation en texte de la propriété "XXXXX" du perso, comme par exemple:<br>
                   &nbsp;&nbsp;&nbsp;&nbsp;[#perso.nom] : nom du meneur de quete<br>
                   &nbsp;&nbsp;&nbsp;&nbsp;[#perso.sex] : son sexe (Monsieur ou Madame)<br>
                   &nbsp;&nbsp;&nbsp;&nbsp;[#perso.niveau] : son niveau, <br>
                   &nbsp;&nbsp;&nbsp;&nbsp;[#perso.for], [#perso.dex], [#perso.int],[#perso.con] : sa force, sa dex, son int et sa constitution, <br>
                   &nbsp;&nbsp;&nbsp;&nbsp;[#perso.po] : le nombre de brouzouf qu\'il possède.<br>
                   &nbsp;&nbsp;&nbsp;&nbsp;etc..<br>
                   <br>* [#perso.XXXXX()] est une représentation en texte de la méthode XXXXX du perso, comme par exemple:<br>
                   &nbsp;&nbsp;&nbsp;&nbsp;[#perso.race()] : affiche la race du meneur de quete<br>
                   &nbsp;&nbsp;&nbsp;&nbsp;[#perso.get_poids()] : le poids qu\'il transporte<br>
                   &nbsp;&nbsp;&nbsp;&nbsp;etc..<br>
                   <br>* [#perso.genre(XXXXX,YYYYY)] si le perso est féminin YYYYY sera affiché sinon c\'est XXXXX, par exemple:<br>
                   &nbsp;&nbsp;&nbsp;&nbsp;[#perso.genre(le meneur,la meneuse)] : affichera "la meneuse" pour les filles et "le meneur" pour les gars.<br>
                   &nbsp;&nbsp;&nbsp;&nbsp;<br>
                   <br></div></td></tr>';
        $aquete_etape = new aquete_etape() ;
        $aqetape_saut_etape_nom = $aquete_etape->getNom(1*$etape->aqetape_saut_etape_cod) ;
        //sauf pour le cas des etapes de fin ECHEC/SUCCESS, on donne une possibilité de saut sur 'étape suivante
        $etape_tags = explode(" ", $etape_modele->aqetapmodel_tag);
        if ( in_array("#END", $etape_tags) || in_array("#START", $etape_tags) || in_array("#SAUT", $etape_tags) || in_array("#CHOIX", $etape_tags) )
        {
            echo '<input type="hidden" name="aqetape_saut_etape_cod" value="0" />';
        } else {
            echo   '<tr><td><strong>Etape suivante</strong>:</td><td>
                        <input data-entry="val" name="aqetape_saut_etape_cod" id="aqetape_saut_etape_cod" type="text" size="5" value="'.$etape->aqetape_saut_etape_cod.'" onChange="if($(\'#aqetape_saut_etape_cod\').val() < 0) $(\'#aqetape_saut_etape_cod\').val(0); setNomByTableCod(\'aqetape_saut_etape_nom\', \'etape\', $(\'#aqetape_saut_etape_cod\').val());">
                        &nbsp;<em></em><span data-entry="text" id="aqetape_saut_etape_nom">'.$aqetape_saut_etape_nom.'</span></em>
                        &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("aqetape_saut_etape","etape","Rechercher une etape", ['.$aquete_cod.','.$aqetape_cod.']);\'> 
                        <em style="font-size: 10px;">(0 ou vide pour poursuivre à l’étape suivante, les étapes spéciales ne sont pas possible)</em></td></tr>';

        }
        echo '</table>';


        $param_liste = $etape_modele->get_liste_parametres();

        foreach ($param_liste as $param_id => $param)
        {

            // Certains paramètres peuvent être remplacé par un paramètre d'étape déjà saisi précédement
            if (( in_array($param['type'], array("perso", "lieu", "type_lieu", "objet_generique", "monstre_generique", "position" )) ) && ($etape_modele->aqetapmodel_tag != '#START')) $alternate_type = true ; else $alternate_type = false ;

            echo '<br><br><strong>Edition du paramètre ['.$param_id.']</strong>: <em>('.$param['texte'].')</em><br>';
            echo $param['desc'].'</em><br><br>';

            if (1*$param['n']<0)
            {
                // Pour les paramètre non-éditable on prépare juste une coquille vide
                $row_id = "row-$param_id-0-";
                echo   '<input id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.$elements[0]->aqelem_cod.'"> 
                        <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> ';
                continue;       // paramètre suivant!
            }

            $element = new aquete_element;
            $elements = $element->getBy_etape_param_id($aqetape_cod, $param_id) ;
            if (!$elements) $elements[] =  new aquete_element;
            while ( sizeof($elements) < (1*$param['n']) )   $elements[] =  new aquete_element;
            $add_buttons = (1*$param['M']==1*$param['n'] && 1*$param['M']>0) ? false : true;

            // Gestion du type "element" alternatif
            if ($alternate_type)
            {
                echo 'Vous pouvez définir un nouveau paramètre ou choisir d\'utiliser un élément défini lors d\'une étape précédente:';
                echo '<input type="hidden" id="alt-type-'.$param_id.'" name="element_type['.$param_id.']" value="'.($elements[0]->aqelem_type == "element" ? "element" : $param['type']).'">';
                echo '<input type="button" value="Changer" onClick="switchQueteAutoParamRow(\''.$param_id.'\', \''.$param['type'].'\');"><br><br>';
            }
            echo '<table width="80%" align="center">';
            //echo '<input type="hidden" id="max-param-'.$param_id.'" value="'.$param['M'].'">';

            // En cas de type alternatif, il y a un ligne de saisie supplementaire
            $style_tr = "display: block;" ;
            if ($alternate_type)
            {
                // affcihage de l'élement alternatif !
                if ($elements[0]->aqelem_type == "element") $style_tr = "display: none;" ;
                if ((1*$elements[0]->aqelem_misc_cod != 0) && ($elements[0]->aqelem_type == "element"))
                {
                    $aquete_etape = new aquete_etape ;
                    $aquete_etape->charge( $elements[0]->aqelem_misc_cod );
                    $aqelem_misc_nom = $aquete_etape->aqetape_nom ;
                }

                $row_id = "row-$param_id-x-"; // paramètre en x pour ne pas parasiter avec les autres et pouvoir la distinguer !!
                // Il faut aussi utilisé tous les champs num_2, num_3, etc.. même s'il ne son tpas utilisé pour ne pas avoir de déclage d'indice sur l'élément réel.
                echo   '<tr style="'.($elements[0]->aqelem_type=='element' ? "display: block;" : "display: none;").'" id="alt-'.$param_id.'"><td colspan="2">Element d\'une autre étape :
                                        <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($elements[0]->aqelem_type=='element' ? $elements[0]->aqelem_cod : '').'"> 
                                        <input name="aqelem_type['.$param_id.'][]" type="hidden" value="element"> 
                                        <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.($elements[0]->aqelem_type=='element' ? $elements[0]->aqelem_misc_cod : '').'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'etape\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                         #<input data-entry="val" name="aqelem_param_num_1['.$param_id.'][]" id="'.$row_id.'aqelem_misc_num_1" type="text" size="2" value="'.($elements[0]->aqelem_type=='element' ? $elements[0]->aqelem_param_num_1 : '').'">
                                         &nbsp'.create_selectbox("aqelem_param_num_2[$param_id][]", array("0"=>"Perso","1"=>"Quête"), 1*$elements[0]->aqelem_param_num_2, array('id' =>"{$row_id}aqelem_param_num_2", 'style'=>'style="width: 100px;" data-entry="val"')).'
                                         <input name="aqelem_param_num_3['.$param_id.'][]" id="'.$row_id.'aqelem_param_num_3" type="hidden">
                                         <input name="aqelem_param_txt_1['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_1" type="hidden">
                                         <input name="aqelem_param_txt_2['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_2" type="hidden">
                                         <input name="aqelem_param_txt_3['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_3" type="hidden">
                                        &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                        &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","element","Rechercher un élément", ['.$aquete_cod.', '.$aqetape_cod.', "'.$param['type'].'" ]);\'> 
                                        </td>';
                echo "</tr>";
            }
            //echo "$param_id => <pre>"; print_r($elements);echo "</pre>";

            foreach($elements as $row => $element)
            {
                $row_id = "row-$param_id-$row-";
                $aqelem_misc_nom = "" ;
                echo   '<tr id="'.$row_id.'" style="'.$style_tr.'">';
                if ($add_buttons) echo   '<td><input type="button" class="test" value="Supprimer" onClick="delQueteAutoParamRow($(this).parent(\'td\').parent(\'tr\'), '.$param['n'].');"></td>';
                switch ($param['type'])
                {
                    case 'perso':
                            if ((1*$element->aqelem_misc_cod != 0) && ($element->aqelem_type==$param['type']))
                            {
                                $perso = new perso() ;
                                $perso->charge( $element->aqelem_misc_cod );
                                $aqelem_misc_nom = $perso->perso_nom ;
                            }
                            echo   '<td>Perso :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_cod : '').'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                    <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_misc_cod : '').'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'perso\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">                                    
                                    <input name="aqelem_param_num_1['.$param_id.'][]" id="'.$row_id.'aqelem_param_num_1" value="'.($element->aqelem_param_num_1).'" type="hidden">
                                    <input name="aqelem_param_num_2['.$param_id.'][]" id="'.$row_id.'aqelem_param_num_2" value="'.($element->aqelem_param_num_2).'" type="hidden">
                                    <input name="aqelem_param_num_3['.$param_id.'][]" id="'.$row_id.'aqelem_param_num_3" value="'.($element->aqelem_param_num_3).'" type="hidden">
                                    <input name="aqelem_param_txt_1['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_1" value="'.($element->aqelem_param_txt_1).'" type="hidden">
                                    <input name="aqelem_param_txt_2['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_2" value="'.($element->aqelem_param_txt_2).'" type="hidden">
                                    <input name="aqelem_param_txt_3['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_3" value="'.($element->aqelem_param_txt_3).'" type="hidden">
                                    &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","perso","Rechercher un perso");\'> 
                                    </td>';
                    break;

                    case 'lieu':
                            if ((1*$element->aqelem_misc_cod != 0) && ($element->aqelem_type==$param['type']))
                            {
                                $lieu = new lieu() ;
                                $lieu->charge( $element->aqelem_misc_cod );
                                $aqelem_misc_nom = $lieu->lieu_nom ;
                            }
                            echo   '<td>Lieu :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_cod : '').'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                    <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_misc_cod : '').'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'lieu\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                    &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","lieu","Rechercher un lieu");\'> 
                                    </td>';
                    break;

                    case 'position':
                            $etage_reference = "";
                            $pos_x = "";
                            $pos_y = "";
                            if ((1*$element->aqelem_misc_cod != 0) && ($element->aqelem_type==$param['type']))
                            {
                                $position = new positions() ;
                                $position->charge( $element->aqelem_misc_cod );
                                $etage = new etage();
                                $etage->getByNumero($position->pos_etage);
                                $etage_reference = $etage->etage_reference ;
                                $pos_x = $position->pos_x ;
                                $pos_y = $position->pos_y ;
                                $aqelem_misc_nom = 'Etage:'.$etage->etage_reference.': X='.$position->pos_x.',Y='.$position->pos_y.' - '.$etage->etage_libelle ;
                                $lpos = new lieu_position();
                                if ($lpos->getByPos( $element->aqelem_misc_cod ))
                                {
                                    $lieu = new lieu();
                                    $lieu->charge($lpos->lpos_lieu_cod);
                                    $aqelem_misc_nom.= " (".$lieu->lieu_nom.")";
                                }
                            }
                            echo   '<td>Position :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_cod : '').'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                    <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_misc_cod : '').'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'position\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                    &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","position","Rechercher une position",["'.$etage_reference.'","'.$pos_x,'","'.$pos_y.'"]);\'> 
                                    </td>';
                    break;

                    case 'objet_generique':
                            if ((1*$element->aqelem_misc_cod != 0) && ($element->aqelem_type==$param['type']))
                            {
                                $gobj = new objet_generique() ;
                                $gobj->charge( $element->aqelem_misc_cod );
                                $aqelem_misc_nom = $gobj->gobj_nom ;
                            }
                            echo   '<td>Objet générique :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_cod : '').'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                    <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_misc_cod : '').'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'objet_generique\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                    &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","objet_generique","Rechercher un objet générique");\'> 
                                    </td>';
                    break;

                    case 'type_objet':
                            if ((1*$element->aqelem_misc_cod != 0) && ($element->aqelem_type==$param['type']))
                            {
                                $tobj = new type_objet() ;
                                $tobj->charge( $element->aqelem_misc_cod );
                                $aqelem_misc_nom = $tobj->tobj_libelle ;
                            }
                            echo   '<td>Type d\'objet :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_cod : '').'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                    <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_misc_cod : '').'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'type_objet\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                    &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","type_objet","Rechercher un type d’objet");\'> 
                                    </td>';
                    break;

                    case 'lieu_type':
                            if ((1*$element->aqelem_misc_cod != 0) && ($element->aqelem_type==$param['type']))
                            {
                                $lieu_type = new lieu_type() ;
                                $lieu_type->charge( $element->aqelem_misc_cod );
                                $aqelem_misc_nom = $lieu_type->tlieu_libelle ;
                            }
                            echo   '<td>Type de lieu :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_cod : '').'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                    <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_misc_cod : '').'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'lieu_type\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                    &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","lieu_type","Rechercher un type de lieu");\'>
                                    <br>Situé en dessous de&nbsp;:'.create_selectbox_from_req("aqelem_param_num_1[$param_id][]", $request_select_etage_ref, $element->aqelem_param_num_1,     array('id' =>"{$row_id}aqelem_param_num_1", 'style'=>'style="width: 150px;" data-entry="val"')).'
                                    et situé au dessus de:'.create_selectbox_from_req("aqelem_param_num_2[$param_id][]", $request_select_etage_ref, $element->aqelem_param_num_2,     array('id' =>"{$row_id}aqelem_param_num_2", 'style'=>'style="width: 150px;" data-entry="val"')).'
                                    
                                    </td>';
                    break;

                    case 'race':
                        if ((1*$element->aqelem_misc_cod != 0) && ($element->aqelem_type==$param['type']))
                        {
                            $gobj = new race() ;
                            $gobj->charge( $element->aqelem_misc_cod );
                            $aqelem_misc_nom = $gobj->race_nom ;
                        }
                        echo   '<td>Race :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_cod : '').'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                    <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_misc_cod : '').'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'race\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                    &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","race","Rechercher une race de monstre");\'> 
                                    </td>';
                        break;

                    case 'competence':
                        if ((1*$element->aqelem_misc_cod != 0) && ($element->aqelem_type==$param['type']))
                        {
                            $gobj = new competences() ;
                            $gobj->charge( $element->aqelem_misc_cod );
                            $aqelem_misc_nom = $gobj->comp_libelle ;
                        }
                        echo   '<td>Compétence :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_cod : '').'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                    <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_misc_cod : '').'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'competence\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                    &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","competence","Rechercher une compétence");\'> 
                                    </td>';
                        break;

                    case 'type_monstre_generique':      // pour type seulement (pas d'option d'invocation)
                        if ((1*$element->aqelem_misc_cod != 0) && ($element->aqelem_type==$param['type']))
                        {
                            $gmon = new monstre_generique() ;
                            $gmon->charge( $element->aqelem_misc_cod );
                            $aqelem_misc_nom = $gmon->gmon_nom ;
                        }
                        echo   '<td>Monstre générique :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_cod : '').'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                    <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_misc_cod : '').'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'monstre_generique\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                    &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","monstre_generique","monstre_generique");\'> 
                                    </td>';
                        break;

                    case 'monstre_generique':       // pour invocation
                        if ((1*$element->aqelem_misc_cod != 0) && ($element->aqelem_type==$param['type']))
                        {
                            $gmon = new monstre_generique() ;
                            $gmon->charge( $element->aqelem_misc_cod );
                            $aqelem_misc_nom = $gmon->gmon_nom ;
                        }
                        echo   '<td>Monstre générique :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_cod : '').'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                    <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_misc_cod : '').'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'monstre_generique\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                    &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","monstre_generique","Rechercher un monstre générique");\'> 
                                     <br>Mode d\'invocation &rArr;&nbsp;'.create_selectbox("aqelem_param_num_1[$param_id][]", array("0"=>"Monstre","1"=>"Perso"), 1*$element->aqelem_param_num_1, array('id' =>"{$row_id}aqelem_param_num_1", 'style'=>'style="width: 100px;" data-entry="val"')).'
                                     &nbsp;'.create_selectbox("aqelem_param_num_2[$param_id][]", array("0"=>"Tangible","1"=>"Intangible"), 1*$element->aqelem_param_num_2, array('id' =>"{$row_id}aqelem_param_num_2", 'style'=>'style="width: 100px;" data-entry="val"')).'
                                     &nbsp;'.create_selectbox("aqelem_param_num_3[$param_id][]", array("0"=>"Standard","1"=>"Type PNJ"), 1*$element->aqelem_param_num_3, array('id' =>"{$row_id}aqelem_param_num_3", 'style'=>'style="width: 100px;" data-entry="val"')).'
                                   </td>';
                        break;


                    case 'echange':
                        $aqelem_misc_nom2 =  "";
                        if ((1*$element->aqelem_misc_cod != 0) && ($element->aqelem_type==$param['type']))
                        {
                            $gobj = new objet_generique();
                            $gobj->charge($element->aqelem_misc_cod);
                            $aqelem_misc_nom = $gobj->gobj_nom ;
                        }

                        if ((1*$element->aqelem_param_num_2 != 0) && ($element->aqelem_type==$param['type']))
                        {
                            $gobj = new objet_generique() ;
                            $gobj->charge( $element->aqelem_param_num_2 );
                            $aqelem_misc_nom2 = $gobj->gobj_nom ;
                        }

                        echo   '<td>Echange :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_cod : '').'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                    
                                    <input data-entry="val" name="aqelem_param_num_1['.$param_id.'][]" id="'.$row_id.'aqelem_param_num_1" type="text" size="2" value="'.$element->aqelem_param_num_1.'"> x
                                    
                                    <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_misc_cod : '').'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'objet_generique\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                    &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","objet_generique","Rechercher un objet générique");\'> 
                                    
                                    contre 
                                    <input data-entry="val" name="aqelem_param_txt_1['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_1" type="text" size="5" value="'.$element->aqelem_param_txt_1.'"> Bzf et
                                    <input data-entry="val" name="aqelem_param_num_3['.$param_id.'][]" id="'.$row_id.'aqelem_param_num_3" type="text" size="2" value="'.$element->aqelem_param_num_3.'"> x
                                    <input data-entry="val" name="aqelem_param_num_2['.$param_id.'][]" id="'.$row_id.'aqelem_param_num_2" type="text" size="5" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_param_num_2 : '').'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom2\', \'objet_generique\', $(\'#'.$row_id.'aqelem_param_num_2\').val());">
                                    &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom2">'.$aqelem_misc_nom2.'</span></em>
                                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod(["'.$row_id.'aqelem_param_num_2","'.$row_id.'aqelem_misc_nom2"],"objet_generique","Rechercher un objet générique");\'> 
                                    </td>';
                        break;

                    case 'bonus':       // pour ls BM
                        if ((1*$element->aqelem_misc_cod != 0) && ($element->aqelem_type==$param['type']))
                        {
                            $bon = new bonus_type() ;
                            $bon->charge( $element->aqelem_misc_cod );
                            $aqelem_misc_nom = $bon->tonbus_libelle ;
                        }
                        echo   '<td>Bonus/Malus :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_cod : '').'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                     '.create_selectbox_from_req("aqelem_misc_cod[$param_id][]", "select tbonus_cod, tonbus_libelle||case when tbonus_gentil_positif then ' (+)' else ' (-)' end from bonus_type order by tonbus_libelle", 1*$element->aqelem_misc_cod, array('id' =>"{$row_id}aqelem_misc_cod", 'style'=>'style="width: 250px;" data-entry="val"')).'
                                     Puissance :<input data-entry="val" name="aqelem_param_num_1['.$param_id.'][]" id="'.$row_id.'aqelem_param_num_1" type="text" size="5" value="'.$element->aqelem_param_num_1.'" style="margin-top: 5px;">
                                     Nombre de DLT :<input data-entry="val" name="aqelem_param_num_2['.$param_id.'][]" id="'.$row_id.'aqelem_param_num_2" type="text" size="5" value="'.$element->aqelem_param_num_2.'">
                                   </td>';
                        break;

                    case 'meca':       // pour les mecanisme d'étage
                        if ((1*$element->aqelem_misc_cod != 0) && ($element->aqelem_type==$param['type']))
                        {
                            $meca = new meca() ;
                            $meca->charge( $element->aqelem_misc_cod );
                            $etage = new etage();
                            $etage->charge( $meca->meca_pos_etage );
                            $aqelem_misc_nom = $etage->etage_libelle." / ".$meca->meca_nom ;
                        }
                        echo   '<td>Mecanisme :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.$element->aqelem_cod.'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                    <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.$element->aqelem_misc_cod.'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'meca\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                    &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","meca","Rechercher un mécanisme", ['.$pos_etage.']);\'> 
                                    <span title="Position ciblée pour les mecanismes individuels (facultatif, mettre -1 pour cibler toutes les positions)">Position: <input data-entry="val" name="aqelem_param_num_3['.$param_id.'][]" id="'.$row_id.'aqelem_param_num_3" type="text" size="5" value="'.$element->aqelem_param_num_3.'"></span>
                                    Déclenchement : '.create_selectbox("aqelem_param_num_1[$param_id][]", array("0"=>"Active","-1"=>"Désactive","2"=>"Inverse"), 1*$element->aqelem_param_num_1, array('id' =>"{$row_id}aqelem_param_num_1", 'style'=>'style="width: 100px;" data-entry="val"')).'
                                     Chance (en %): <input data-entry="val" name="aqelem_param_num_2['.$param_id.'][]" id="'.$row_id.'aqelem_param_num_2" type="text" size="5" value="'.$element->aqelem_param_num_2.'">
                               </td>';
                        break;

                    case 'meca_etat':       // pour les mecanisme d'étage
                        if ((1*$element->aqelem_misc_cod != 0) && ($element->aqelem_type==$param['type']))
                        {
                            $meca = new meca() ;
                            $meca->charge( $element->aqelem_misc_cod );
                            $etage = new etage();
                            $etage->charge( $meca->meca_pos_etage );
                            $aqelem_misc_nom = $etage->etage_libelle." / ".$meca->meca_nom ;
                        }
                        echo   '<td>Mecanisme :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.$element->aqelem_cod.'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                    <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.$element->aqelem_misc_cod.'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'meca\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                    &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","meca","Rechercher un mécanisme", ['.$pos_etage.']);\'> 
                                    <span title="Position ciblée pour les mecanismes individuels (facultatif, mettre -1 pour cibler toutes les positions)">Position: <input data-entry="val" name="aqelem_param_num_3['.$param_id.'][]" id="'.$row_id.'aqelem_param_num_3" type="text" size="5" value="'.$element->aqelem_param_num_3.'"></span>
                                    Etat : '.create_selectbox("aqelem_param_num_1[$param_id][]", array("0"=>"Actif","-1"=>"Désactivé"), 1*$element->aqelem_param_num_1, array('id' =>"{$row_id}aqelem_param_num_1", 'style'=>'style="width: 100px;" data-entry="val"')).'
                               </td>';
                        break;

                    case 'perso_condition':       // pour invocation
                        if ((1*$element->aqelem_misc_cod != 0) && ($element->aqelem_type==$param['type']))
                        {
                            $bon = new bonus_type() ;
                            $bon->charge( $element->aqelem_misc_cod );
                            $aqelem_misc_nom = $bon->tonbus_libelle ;
                        }
                        echo   '<td>Conditions :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_cod : '').'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                     '.create_selectbox("aqelem_param_num_1[$param_id][]", array("0"=>"ET","1"=>"OU"), 1*$element->aqelem_param_num_1, array('id' =>"{$row_id}aqelem_param_num_1", 'style'=>'style="width: 100px;" data-entry="val"')).'
                                     '.create_selectbox_from_req("aqelem_misc_cod[$param_id][]", "select aqtypecarac_cod, aqtypecarac_nom, aqtypecarac_type from quetes.aquete_type_carac order by aqtypecarac_type, aqtypecarac_nom, aqtypecarac_cod", 1*$element->aqelem_misc_cod, array('id' =>"{$row_id}aqelem_misc_cod", 'style'=>'style="width: 250px;" data-entry="val"')).'
                                     '.create_selectbox("aqelem_param_txt_1[$param_id][]", array("="=>"=","!="=>"!=","<"=>"<","<="=>"<=","entre"=>"entre",">"=>">",">="=>">="), $element->aqelem_param_txt_1, array('id' =>"{$row_id}aqelem_param_txt_1", 'style'=>'style="width: 50px;" data-entry="val"')).'
                                     <input data-entry="val" name="aqelem_param_txt_2['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_2" type="text" size="15" value="'.$element->aqelem_param_txt_2.'" style="margin-top: 5px;">
                                     &nbsp;&nbsp;( et <input data-entry="val" name="aqelem_param_txt_3['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_3" type="text" size="15" value="'.$element->aqelem_param_txt_3.'"> &rArr; pour la condition « entre » seulement )
                                   </td>';
                        break;

                    case 'perso_condition_liste':       // pour invocation
                        if ((1*$element->aqelem_misc_cod != 0) && ($element->aqelem_type==$param['type']))
                        {
                            $bon = new bonus_type() ;
                            $bon->charge( $element->aqelem_misc_cod );
                            $aqelem_misc_nom = $bon->tonbus_libelle ;
                        }
                        echo   '<td>Groupes de conditions :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_cod : '').'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                     '.create_selectbox("aqelem_param_num_2[$param_id][]", array(""=>"","1"=>"Nouveau Groupe"), 1*$element->aqelem_param_num_2, array('id' =>"{$row_id}aqelem_param_num_2", 'style'=>'style="width: 150px;" data-entry="val"')).'
                                     '.create_selectbox("aqelem_param_num_1[$param_id][]", array("0"=>"ET","1"=>"OU"), 1*$element->aqelem_param_num_1, array('id' =>"{$row_id}aqelem_param_num_1", 'style'=>'style="width: 50px;" data-entry="val"')).'
                                     '.create_selectbox_from_req("aqelem_misc_cod[$param_id][]", "select aqtypecarac_cod, aqtypecarac_nom, aqtypecarac_type from quetes.aquete_type_carac order by aqtypecarac_type, aqtypecarac_nom, aqtypecarac_cod", 1*$element->aqelem_misc_cod, array('id' =>"{$row_id}aqelem_misc_cod", 'style'=>'style="width: 250px;" data-entry="val"')).'
                                     '.create_selectbox("aqelem_param_txt_1[$param_id][]", array("="=>"=","!="=>"!=","<"=>"<","<="=>"<=","entre"=>"entre",">"=>">",">="=>">="), $element->aqelem_param_txt_1, array('id' =>"{$row_id}aqelem_param_txt_1", 'style'=>'style="width: 50px;" data-entry="val"')).'
                                     <input data-entry="val" name="aqelem_param_txt_2['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_2" type="text" size="15" value="'.$element->aqelem_param_txt_2.'" style="margin-top: 5px;">
                                     &nbsp;&nbsp;( et <input data-entry="val" name="aqelem_param_txt_3['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_3" type="text" size="15" value="'.$element->aqelem_param_txt_3.'"> &rArr; pour la condition « entre » seulement )
                                   </td>';
                        break;

                    case 'choix':

                        $aquete_etape = new aquete_etape() ;
                        $aqelem_misc_nom = $aquete_etape->getNom(1*$element->aqelem_misc_cod) ;

                        echo   '<td>Choix  :
                                <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.$element->aqelem_cod.'"> 
                                <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                <input data-entry="val" name="aqelem_param_txt_1['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_1" type="text" size="80" value="'.htmlspecialchars($element->aqelem_param_txt_1).'"> <br>Etape si choisi:
                                <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.$element->aqelem_misc_cod.'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'etape\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","etape","Rechercher une etape", ['.$aquete_cod.','.$aqetape_cod.']);\'> 
                                </td>';
                        break;

                    case 'choix_etape':

                        $aquete_etape = new aquete_etape() ;
                        $aqelem_misc_nom = $aquete_etape->getNom(1*$element->aqelem_misc_cod) ;

                        echo   '<td>Liste de mot <em style="font-size: x-small">(séparés par |)</em>  :
                                <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.$element->aqelem_cod.'"> 
                                <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                <input data-entry="val" name="aqelem_param_txt_1['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_1" type="text" size="80" value="'.htmlspecialchars($element->aqelem_param_txt_1).'"> <br>
                                Condition sur les mots: |='.create_selectbox("aqelem_param_num_2[$param_id][]", array("0"=>"ET","1"=>"OU"), 1*$element->aqelem_param_num_2, array('id' =>"{$row_id}aqelem_param_num_2", 'style'=>'style="width: 60px;" data-entry="val"')).'
                                Etape sur condition: <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.$element->aqelem_misc_cod.'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'etape\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","etape","Rechercher une etape", ['.$aquete_cod.','.$aqetape_cod.']);\'> 
                                </td>';
                        break;

                    case 'delai':

                        $aquete_etape = new aquete_etape() ;
                        $aqelem_misc_nom = $aquete_etape->getNom(1*$element->aqelem_misc_cod) ;

                        echo   '<td>Délai (<em>en jours</em>) :
                                <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.$element->aqelem_cod.'"> 
                                <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                <input data-entry="val" name="aqelem_param_num_1['.$param_id.'][]" id="'.$row_id.'aqelem_param_num_1" type="text" size="2" value="'.$element->aqelem_param_num_1.'"> (laisser à 0 si illimité), Etape si délai écoulé:
                                <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.$element->aqelem_misc_cod.'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'etape\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","etape","Rechercher une etape", ['.$aquete_cod.','.$aqetape_cod.']);\'> 
                                </td>';
                        break;

                    case 'valeur':

                        echo   '<td>Valeur :
                                <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.$element->aqelem_cod.'"> 
                                <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                <input data-entry="val" name="aqelem_param_num_1['.$param_id.'][]" id="'.$row_id.'aqelem_param_num_1" type="text" size="8" value="'.$element->aqelem_param_num_1.'">
                                </td>';
                        break;

                    case 'texte':

                        echo   '<td>Texte :
                                <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.$element->aqelem_cod.'"> 
                                <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                <input data-entry="val" name="aqelem_param_txt_1['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_1" type="text" size="95" value="'.htmlspecialchars($element->aqelem_param_txt_1).'">
                                </td>';
                        break;

                    case 'craft':

                        echo   '<td>Num#1 :
                                <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.$element->aqelem_cod.'"> 
                                <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                <input data-entry="val" name="aqelem_param_num_1['.$param_id.'][]" id="'.$row_id.'aqelem_param_num_1" type="text" size="8" value="'.$element->aqelem_param_num_1.'">&nbsp;
                                Num#2&nbsp;:&nbsp;<input data-entry="val" name="aqelem_param_num_2['.$param_id.'][]" id="'.$row_id.'aqelem_param_num_2" type="text" size="8" value="'.$element->aqelem_param_num_2.'">&nbsp;
                                Num#3&nbsp;:&nbsp;<input data-entry="val" name="aqelem_param_num_3['.$param_id.'][]" id="'.$row_id.'aqelem_param_num_3" type="text" size="8" value="'.$element->aqelem_param_num_3.'">&nbsp;
                                Int#1&nbsp;:&nbsp;<input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="8" value="'.$element->aqelem_misc_cod.'"><br>
                                Txt#1&nbsp;:&nbsp;<input data-entry="val" name="aqelem_param_txt_1['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_1" type="text" size="95" value="'.htmlspecialchars($element->aqelem_param_txt_1).'"><br>
                                Txt#2&nbsp;:&nbsp;<input data-entry="val" name="aqelem_param_txt_2['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_2" type="text" size="95" value="'.htmlspecialchars($element->aqelem_param_txt_2).'"><br>
                                Txt#3&nbsp;:&nbsp;<input data-entry="val" name="aqelem_param_txt_3['.$param_id.'][]" id="'.$row_id.'aqelem_param_txt_3" type="text" size="95" value="'.htmlspecialchars($element->aqelem_param_txt_3).'">
                                </td>';
                        break;

                    case 'etape':

                        $aquete_etape = new aquete_etape() ;
                        $aqelem_misc_nom = $aquete_etape->getNom(1*$element->aqelem_misc_cod) ;

                        echo   '<td>Etape : 
                                <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.$element->aqelem_cod.'"> 
                                <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.$element->aqelem_misc_cod.'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'etape\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","etape","Rechercher une etape", ['.$aquete_cod.','.$aqetape_cod.']);\'> 
                                </td>';
                        break;

                    case 'quete_etape':

                        $aquete_etape = new aquete_etape() ;
                        $aqelem_misc_nom = $aquete_etape->getNom(1*$element->aqelem_misc_cod) ;

                        echo   '<td>Etape : 
                                <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.$element->aqelem_cod.'"> 
                                <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.$element->aqelem_misc_cod.'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'etape\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                &nbsp;<i></i><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></i>
                                &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","etape","Rechercher une etape");\'> 
                                </td>';
                        break;

                    case 'quete':
                        if ((1*$element->aqelem_misc_cod != 0) && ($element->aqelem_type==$param['type']))
                        {
                            $aquete = new aquete() ;
                            $aquete->charge( $element->aqelem_misc_cod );
                            $aqelem_misc_nom = $aquete->aquete_nom_alias ;
                        }

                        echo   '<td>Quête : 
                                <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.$element->aqelem_cod.'"> 
                                <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> 
                                <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.$element->aqelem_misc_cod.'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'quete\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                &nbsp;<i></i><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></i>
                                &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","quete","Rechercher une etape");\'> 
                                </td>';
                        break;

                    case 'element':

                        $aquete_etape = new aquete_etape();
                        $aquete_etape->charge($element->aqelem_misc_cod);
                        $aqelem_misc_nom = $aquete_etape->aqetape_nom;

                        echo   '<td>Element : 
                                <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($element->aqelem_type=='element' ? $element->aqelem_cod : '').'"> 
                                <input name="aqelem_type['.$param_id.'][]" type="hidden" value="element"> 
                                <input data-entry="val" name="aqelem_misc_cod['.$param_id.'][]" id="'.$row_id.'aqelem_misc_cod" type="text" size="5" value="'.($element->aqelem_type=='element' ? $element->aqelem_misc_cod : '').'" onChange="setNomByTableCod(\''.$row_id.'aqelem_misc_nom\', \'etape\', $(\'#'.$row_id.'aqelem_misc_cod\').val());">
                                #<input data-entry="val" name="aqelem_param_num_1['.$param_id.'][]" id="'.$row_id.'aqelem_misc_num_1" type="text" size="2" value="'.($element->aqelem_type=='element' ? $element->aqelem_param_num_1 : '').'">
                                &nbsp;<em></em><span data-entry="text" id="'.$row_id.'aqelem_misc_nom">'.$aqelem_misc_nom.'</span></em>
                                &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("'.$row_id.'aqelem_misc","element","Rechercher un élément", ['.$aquete_cod.', '.$aqetape_cod.', "" ]);\'> 
                                </td>';
                        break;

                    case 'selecteur':
                        echo   '<td>Selectionner :
                                    <input data-entry="val" id="'.$row_id.'aqelem_cod" name="aqelem_cod['.$param_id.'][]" type="hidden" value="'.($element->aqelem_type==$param['type'] ? $element->aqelem_cod : '').'"> 
                                    <input name="aqelem_type['.$param_id.'][]" type="hidden" value="'.$param['type'].'"> '.
                                    create_selectbox("aqelem_misc_cod[{$param_id}][]", $param['ext'],($element->aqelem_type==$param['type'] ? $element->aqelem_misc_cod : ''),array("id"=>$row_id.'aqelem_misc_cod'))
                                    .'</td>';
                        break;
                    default:
                        echo '<td>Type de paramètre inconnu</td>';
                    break;
                }
                echo "</tr>";
            }
            if ($add_buttons) echo '<tr id="add-'.$row_id.'" style="'.$style_tr.'"><td> <input type="button" class="test" value="Nouveau" onClick="addQueteAutoParamRow($(this).parent(\'td\').parent(\'tr\').prev(), '.$param['M'].');"> </td></tr>';
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
