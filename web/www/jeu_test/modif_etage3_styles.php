<?php /* Affichage de tous les styles de murs et fonds */

include_once "verif_connexion.php";
include_once '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include_once('variables_menu.php');


function ecrireResultatEtLoguer($texte, $loguer, $sql = '')
{
    global $db, $compt_cod;

    if ($texte)
    {
        $log_sql = false;	// Mettre à true pour le debug des requêtes

        if (!$log_sql || $sql == '')
            $sql = "\n";
        else
            $sql = "\n\t\tRequête : $sql\n";

        $req = "select compt_nom from compte where compt_cod = $compt_cod";
        $db->query($req);
        $db->next_record();
        $compt_nom = $db->f("compt_nom");

        $en_tete = date("d/m/y - H:i") . "\tCompte $compt_nom ($compt_cod)\t";
        echo "<div style='padding:10px;'>$texte<pre>$sql</pre></div><hr />";
        if ($loguer)
            writelog($en_tete . $texte . $sql,'lieux_etages');
    }
}

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$contenu = '';
$erreur = 0;
$req = "select dcompt_modif_carte from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);

if ($db->nf() == 0)
{
    $droit['carte'] = 'N';
}
else
{
	$db->next_record();
	$droit['carte'] = $db->f("dcompt_modif_carte");
}
if ($droit['carte'] != 'O')
{
	die("<p>Erreur ! Vous n’avez pas accès à cette page !</p>");
}
if ($erreur == 0)
{

    $pdo    = new bddpdo;            // 2018-05-22 - Marlyza - pour traiter les requêtes secondaires

    //$style ne doit comporter que des caractères: [0-9a-zA-Z] ---> filter tout autre caractère!
    $style = substr( preg_replace('/[^0-9a-zA-Z]/', '', $_REQUEST['style']), 0, 10);

    $is_style_exists = false ;

	// Récupération des images existantes
	// On y va à la bourrin : on parcourt tous les fichiers du répertoire images.
	$patron_fond = '/^f_(?P<affichage>[0-9a-zA-Z]+)_(?P<type>\d+)\.png$/';
	$patron_mur = '/^t_(?P<affichage>[0-9a-zA-Z]+)_mur_(?P<type>\d+)\.png$/';
    $patron_fig = '/^t_(?P<affichage>[0-9a-zA-Z]+)_(?P<type>enn|per|lie|obj)\.png$/';
	$chemin = '../../images/';

	$tableau_styles = array();
	$tableau_fonds = array();
	$tableau_murs = array();
	$tableau_figs = array();
	$js_tab_fonds = "\nvar tab_fonds = new Array();";
	$js_tab_murs = "\nvar tab_murs = new Array();";
    $js_tab_figs = "\nvar tab_figs = new Array();";
	$js_usage = "\nvar tab_usage = new Array();";

	$rep = opendir($chemin);
	while (false !== ($fichier = readdir($rep)))
	{
		$correspondances = array();
		$flagNouveauStyle = "" ;
		if (1 === preg_match($patron_fond, $fichier, $correspondances))
		{
            if (!isset($tableau_styles[$correspondances['affichage']]))
            {
                $flagNouveauStyle = $correspondances['affichage'];
                $tableau_styles[$correspondances['affichage']] = $correspondances['affichage'];
                $js_tab_fonds .= "\ntab_fonds['" . $correspondances['affichage'] . "'] = new Array();";
                $js_tab_murs .= "\ntab_murs['" . $correspondances['affichage'] . "'] = new Array();";
                if (!isset($tableau_figs[$correspondances['affichage']]['fig']))
                {
                    $tableau_figs[$correspondances['affichage']]['fig'] = $correspondances['affichage'];
                    $js_tab_figs .= "\ntab_figs['" . $correspondances['affichage'] . "'] = new Array();";
                }
            }
            $js_tab_fonds .= "\ntab_fonds['" . $correspondances['affichage'] . "'][" . $correspondances['type'] . "] = " . $correspondances['type'] . ";";
            $tableau_fonds[$correspondances['affichage']][$correspondances['type']] =  $correspondances['type'];
		}

		$correspondances = array();
		if (1 === preg_match($patron_mur, $fichier, $correspondances))
        {
            if (!isset($tableau_styles[$correspondances['affichage']]))
            {
                $flagNouveauStyle = $correspondances['affichage'];
                $tableau_styles[$correspondances['affichage']] = $correspondances['affichage'];
                $js_tab_fonds .= "\ntab_fonds['" . $correspondances['affichage'] . "'] = new Array();";
                $js_tab_murs .= "\ntab_murs['" . $correspondances['affichage'] . "'] = new Array();";
                if (!isset($tableau_figs[$correspondances['affichage']]['fig']))
                {
                    $tableau_figs[$correspondances['affichage']]['fig'] = $correspondances['affichage'];
                    $js_tab_figs .= "\ntab_figs['" . $correspondances['affichage'] . "'] = new Array();";
                }
            }
            $js_tab_murs .= "\ntab_murs['" . $correspondances['affichage'] . "'][" . $correspondances['type'] . "] = " . $correspondances['type'] . ";";
            $tableau_murs[$correspondances['affichage']][$correspondances['type']] =  $correspondances['type'];
        }

        $correspondances = array();
        if (1 === preg_match($patron_fig, $fichier, $correspondances))
        {
            if (!isset($tableau_figs[$correspondances['affichage']]['fig']))
            {
                $tableau_figs[$correspondances['affichage']]['fig'] = $correspondances['affichage'];
                $js_tab_figs .= "\ntab_figs['" . $correspondances['affichage'] . "'] = new Array();";
            }
            $js_tab_figs .= "\ntab_figs['" . $correspondances['affichage'] . "']['" . $correspondances['type'] . "'] = '" . $correspondances['type'] . "';";
            $tableau_figs[$correspondances['affichage']][$correspondances['type']] =  $correspondances['type'];
        }

		if  ($flagNouveauStyle!="")
        {
            if ($style==$flagNouveauStyle) $is_style_exists = true ;

            // Pour chque nouveau style on calcul ne nombre d'étage l'utilisant.
            $req_style = "select count(distinct etage_numero) count from etage where etage_affichage = ?;";
            $stmt = $pdo->prepare($req_style);
            $stmt = $pdo->execute(array($flagNouveauStyle), $stmt);
            $row = $stmt->fetch();
            $style_usage = $row['count'];
            $js_usage .= "\ntab_usage['" . $flagNouveauStyle . "'] = " .$style_usage . ";";

        }
	}

    // préparer la structure js pour l'ajout eds fond/murs
    if (!$is_style_exists && $style!='')
    {
        $js_tab_fonds .= "\ntab_fonds['" . $style . "'] = new Array();";
        $js_tab_murs .= "\ntab_murs['" . $style . "'] = new Array();";
        $js_tab_figs .= "\ntab_figs['" . $style . "'] = new Array();";
    }

    // Traitement des erreurs actions ========================================================
    if (($style=="") && (isset($_REQUEST["supprimer_fond"]) || isset($_REQUEST["nouveau_fond"]) || isset($_REQUEST["supprimer_mur"]) || isset($_REQUEST["nouveau_fig"])))
    {
        echo '<div class="barrTitle">Modification du style</div><br>';
        echo "<b>Action impossible, vous devez d'abord choisir style ou en créer un nouveau.</b><br><br>";
    }
    
    // Traitement des actions ===============================================================
    // --- Action de suppression d'un fond -------
    if (isset($_REQUEST["supprimer_fond"]) && ($style!=""))
    {
        echo '<div class="barrTitle">Suppression d\'un fond pour le style '.$style.'</div><br>';
        $fond_id = $_REQUEST["fond_id"];
        $filename = "f_{$style}_{$fond_id}.png" ;

        if (!file_exists($chemin.$filename))
        {
            echo "<b>Impossible de supprimer le fond pour l'id $fond_id, le fichier  $filename n'a pas été trouvé.</b><br><br>";
        }
        else
        {
            //  vérification vérification que le fond n'est pas utilisé dans les étages avant de le supprimer
            $req_fonds = "select count(*) from positions inner join etage on etage_numero = pos_etage where pos_type_aff=? and etage_affichage=? ";
            $stmt = $pdo->prepare($req_fonds);
            $stmt = $pdo->execute(array($fond_id,$style), $stmt);
            $row = $stmt->fetch();
            $fond_usage = $row['count'];
            if ($fond_usage>0)
            {
                echo "<b>Impossible de supprimer le fond pour l'id $fond_id, il est encore utilisé dans les étages.</b><br><br>";
            }
            else
            {
                unlink ( $chemin.$filename );
                ecrireResultatEtLoguer(" l'image $filename a été supprimée pour le style $style avec l'id #$fond_id", true);
                //supprimer pour l'affichage
                $js_tab_fonds .= "\ndelete tab_fonds['" . $style . "'][" . $fond_id . "] ;";
            }
        }
    }

    // --- Action de ajout modification d'un fond -------
    if (isset($_REQUEST["nouveau_fond"]) && ($style!=""))
    {
        echo '<div class="barrTitle">Ajout d\'un fond pour le style '.$style.'</div><br>';
        if ($_REQUEST["fond_id"]!="")
        {
            $fond_id = $_REQUEST["fond_id"];
            if (isset($tableau_fonds[$style][$fond_id]))
            {
                $fond_id = "" ;     // => pour ne pas ajouter l'image
                echo "<b>Impossible d'ajouter ce fond, l'id  $fond_id existe déjà.</b><br><br>";
            }
        }
        else
        {
            $fond_id = 1;
            while (isset($tableau_fonds[$style][$fond_id])) $fond_id ++ ;
        }

        // Upload de l'image
        if ($fond_id != "")
        {
            $filename = "f_{$style}_{$fond_id}.png" ;
            $imagesize = @getimagesize($_FILES["fond_file"]["tmp_name"]) ;
            if (($imagesize[0] != 28) || ($imagesize[0] != 28))
            {
                echo "<b>Impossible d'ajouter ce fond, l'image n'est pas aux dimensions de 28x28 pixels.</b><br><br>";
            }
            else
            {
                move_uploaded_file ( $_FILES["fond_file"]["tmp_name"] , $chemin.$filename );
                ecrireResultatEtLoguer(" l'image $filename a été ajoutée pour le style $style avec l'id #$fond_id", true);
                //ajouter les pour l'affichage
                $js_tab_fonds .= "\ntab_fonds['" . $style . "'][" . $fond_id . "] = " . $fond_id . ";";
            }
        }
    }

    // --- Action de suppression d'un mur -------
    if (isset($_REQUEST["supprimer_mur"]) && ($style!=""))
    {
        echo '<div class="barrTitle">Suppression d\'un mur pour le style '.$style.'</div><br>';
        $mur_id = $_REQUEST["mur_id"];
        $filename = "t_{$style}_mur_{$mur_id}.png" ;

        if (!file_exists($chemin.$filename))
        {
            echo "<b>Impossible de supprimer le mur pour l'id $mur_id, le fichier  $filename n'a pas été trouvé.</b><br><br>";
        }
        else
        {
            //  vérification vérification que le mur n'est pas utilisé dans les étages avant de le supprimer
            $req_murs = "select count(*) from murs inner join positions on pos_cod = mur_pos_cod inner join etage on etage_numero = pos_etage where mur_type=? and etage_affichage=? ";
            $stmt = $pdo->prepare($req_murs);
            $stmt = $pdo->execute(array($mur_id,$style), $stmt);
            $row = $stmt->fetch();
            $mur_usage = $row['count'];
            if ($mur_usage>0)
            {
                echo "<b>Impossible de supprimer le mur pour l'id $mur_id, il est encore utilisé dans les étages.</b><br><br>";
            }
            else
            {
                unlink ( $chemin.$filename );
                ecrireResultatEtLoguer(" l'image $filename a été supprimée pour le style $style avec l'id #$mur_id", true);
                //supprimer pour l'affichage
                $js_tab_murs .= "\ndelete tab_murs['" . $style . "'][" . $mur_id . "] ;";
            }
        }
    }

    // --- Action de ajout modification d'un mur -------
    if (isset($_REQUEST["nouveau_mur"]) && ($style!=""))
    {
        echo '<div class="barrTitle">Ajout d\'un mur pour le style '.$style.'</div><br>';
        if ($_REQUEST["mur_id"]!="")
        {
            $mur_id = $_REQUEST["mur_id"];
            if (isset($tableau_murs[$style][$mur_id]))
            {
                $mur_id = "" ;     // => pour ne pas ajouter l'image
                echo "<b>Impossible d'ajouter ce mur, l'id  $mur_id existe déjà.</b><br><br>";
            }
        }
        else
        {
            $mur_id = 1;
            while (isset($tableau_murs[$style][$mur_id])) $mur_id ++ ;
        }

        // Upload de l'image
        if ($mur_id != "")
        {
            $filename = "t_{$style}_mur_{$mur_id}.png" ;
            $imagesize = @getimagesize($_FILES["mur_file"]["tmp_name"]) ;
            if (($imagesize[0] != 28) || ($imagesize[0] != 28))
            {
                echo "<b>Impossible d'ajouter ce mur, l'image n'est pas aux dimensions de 28x28 pixels.</b><br><br>";
            }
            else
            {
                move_uploaded_file ( $_FILES["mur_file"]["tmp_name"] , $chemin.$filename );
                ecrireResultatEtLoguer(" l'image $filename a été ajoutée pour le style $style avec l'id #$mur_id", true);
                //ajouter les pour l'affichage
                $js_tab_murs .= "\ntab_murs['" . $style . "'][" . $mur_id . "] = " . $mur_id . ";";
            }
        }
    }
    
    // --- Action de ajout modification d'une figurine -------
    if (isset($_REQUEST["nouveau_fig"]) && ($style!=""))
    {
        echo '<div class="barrTitle">Ajout/Modification d\'une figurine pour le style '.$style.'</div><br>';
        if ($_REQUEST["fig_id"]=="")
        {
            echo "<b>Impossible d'ajouter cette figurine, vous n'avez pas spécifié son type (enn, per, lie, obj).</b><br><br>";
        }
        else
        {
            $fig_id = $_REQUEST["fig_id"] ;
            // il ne doit y avoir ni fond, ni mur de ce style utilisé pour pouvoir remplacer ...
            if (isset($tableau_figs[$style][$fig_id]))
            {
                $req_style = "select count(distinct etage_numero) count from etage where etage_affichage = ?;";
                $stmt = $pdo->prepare($req_style);
                $stmt = $pdo->execute(array($style), $stmt);
                $row = $stmt->fetch();
                $style_usage = 1*$row['count'];

                if ($style_usage>0)
                {
                    echo "<b>Impossible d'ajouter cette figurine, du type '$fig_id', elle existe déjà et elle est utilisée dans $style_usage étage(s).</b><br><br>";
                    $fig_id = "" ;
                }
            }
        }

        // Upload de l'image
        if ($fig_id != "")
        {
            $filename = "t_{$style}_{$fig_id}.png" ;
            $imagesize = @getimagesize($_FILES["fig_file"]["tmp_name"]) ;
            if (($imagesize[0] != 28) || ($imagesize[0] != 28))
            {
                echo "<b>Impossible d'ajouter cette figurine, l'image n'est pas aux dimensions de 28x28 pixels.</b><br><br>";
            }
            else
            {
                move_uploaded_file ( $_FILES["fig_file"]["tmp_name"] , $chemin.$filename );
                ecrireResultatEtLoguer(" l'image $filename a été ajoutée pour le style $style pour le type $fig_id", true);
                //ajouter les pour l'affichage
                $js_tab_figs .= "\ntab_figs['" . $style . "']['" . $fig_id . "'] = '" . $fig_id . "';";
            }
        }
    }

    // Traitement de l'affichage ===============================================================

    echo '<div class="barrTitle">Edition des styles</div><br>
             <form method="POST">Créer un nouveau Style: <input type="text" name="style">&nbsp;&nbsp;
                <input type="submit" class="test" name="nouveau_style" value="Créer">
            </form><br>';
    if(($style!=$_REQUEST['style']) && ($_REQUEST['style']!="")) echo "<u>ATTENTION</u>: le nom ne style saisi (<i>".$_REQUEST['style']."</i>) n'était pas conforme, il a été adapté: <b>$style</b>.<br>";
	echo '<hr /><b>Edition du style</b> : <select name="style" onChange="changeStyle(this.value)"><option value=\'--\'>Choisir un style...</option>';
    if (!$is_style_exists && $style!='') echo "<option value='$style' selected>$style</option>";
	foreach ($tableau_styles as $unStyle) echo "<option value='$unStyle'" . (($unStyle == $style) ? 'selected="selected"' : '') . ">$unStyle</option>";
	echo '</select>&nbsp;&nbsp;&nbsp;<a href="modif_etage3_fonds.php">Voir tous les styles</a>
    <br><i>Nombre d\'étage utilisant ce style </i>: <b><span id="id-usage">0</span></b>
    ';


    echo "<script type='text/javascript'>
		$js_tab_fonds
		$js_tab_murs
		$js_tab_figs
		$js_usage
		$js_usage_fonds

        function popFondInfo(d)
        {
            $('#info-fond-usage').html(d.data.message);
        }
        
        function popMurInfo(d)
        {
            $('#info-mur-usage').html(d.data.message);
        }
        
		function changeStyle(style)
		{
		    $('#id-style-fonds').val(style);
		    $('#id-style-murs').val(style);
		    $('#id-style-figs').val(style);
		    $('#id-usage').text(tab_usage[style] ? tab_usage[style] : 0);
		    
			var div_mur = document.getElementById('visu_murs');
			var div_fond = document.getElementById('visu_fonds');
			var div_fig = document.getElementById('visu_figs');

			var chaine_contenu = '';

			for (var i in tab_fonds[style])
			{
				var nom = '" . G_IMAGES . "f_' + style + '_' + i + '.png';
				chaine_contenu += '<div style=\"float:left; width:28px; height:38px; margin:6px;\"><img id=\"id-fond-'+i+'\" src=\"' + nom + '\"/><br />' + i + '</div>';
			}
			div_fond.innerHTML = chaine_contenu;

			var chaine_contenu = '';
			for (var i in tab_murs[style])
			{
				var nom = '" . G_IMAGES . "t_' + style + '_mur_' + i + '.png';
				chaine_contenu += '<div style=\"float:left; width:28px; height:38px; margin:6px;\"><img id=\"id-mur-'+i+'\" src=\"' + nom + '\"/><br />' + i + '</div>';
			}
			div_mur.innerHTML = chaine_contenu;
			
			var chaine_contenu = '';
			for (var i in tab_figs[style])
			{
				var nom = '" . G_IMAGES . "t_' + style + '_' + i + '.png';
				chaine_contenu += '<div style=\"float:left; width:28px; height:38px; margin:6px;\"><img id=\"id-fig-'+i+'\" src=\"' + nom + '\"/><br />' + i + '</div>';
			}
			div_fig.innerHTML = chaine_contenu;
	
                    			
			// armer/ré-armer le trigger sur les fonds
		    $(\"img[id^='id-fond-']\").on('click', function(e) {
		            runAsync({request: \"admin_info_style_fonds\", data:{style:style, fond_id:this.id.substr(8)}}, popFondInfo);
                    $('input#fond-id').val(this.id.substr(8)); 
		    });	
		    // armer/ré-armer le trigger sur les murs 		
		    $(\"img[id^='id-mur-']\").on('click', function(e) {
		            runAsync({request: \"admin_info_style_murs\", data:{style:style, mur_id:this.id.substr(7)}}, popMurInfo);
                    $('input#mur-id').val(this.id.substr(7)); 
		    });		
		    // armer/ré-armer le trigger sur les figs 		
		    $(\"img[id^='id-fig-']\").on('click', function(e) {
                    $('input#figs-id-'+this.id.substr(7)).prop('checked', true);
		    });					
		}
		
		</script>";

    echo '</p><hr />

    <p><h1>Fonds définis pour ce style :</h1></p><form method="post" enctype="multipart/form-data"> 
        <input id="id-style-fonds" type="hidden" name="style" value="'.$style.'">
        Fond id=<input type="text" id="fond-id" name="fond_id" size="3" value="">&nbsp;&nbsp;
        <input type="submit" class="test" name="supprimer_fond" value="Supprimer le fond"> ou 
        <input type="file" name="fond_file">&nbsp;&nbsp;
        <input type="submit" class="test" name="nouveau_fond" value="Ajouter le fond"></form>
        <span id="info-fond-usage"></span><br>
        <i><font color="#8b0000">L\'image doit être un .png de 28x28 pixels, si vous ne spécifiez pas d\'<b>id</b> il en sera assigné un automatiquement</font>.</i>
	<div style=\'width:600px; overflow:auto\' class=\'bordiv\' id=\'visu_fonds\'></div>
	
	<br><hr /><p><h1>Murs définis pour ce style :</h1></p><form method="post" enctype="multipart/form-data"> 
        <input id="id-style-murs" type="hidden" name="style" value="'.$style.'">
        Mur id=<input type="text" id="mur-id" name="mur_id" size="3" value="">&nbsp;&nbsp;
        <input type="submit" class="test" name="supprimer_mur" value="Supprimer le mur"> ou 
        <input type="file" name="mur_file">&nbsp;&nbsp;
        <input type="submit" class="test" name="nouveau_mur" value="Ajouter le mur"></form>
        <span id="info-mur-usage"></span><br>
        <i><font color="#8b0000">L\'image doit être un .png de 28x28 pixels, si vous ne spécifiez pas d\'<b>id</b> il en sera assigné un automatiquement</font>.</i>
	<div style=\'width:600px; overflow:auto\' class=\'bordiv\' id=\'visu_murs\'></div>
	
	<br><hr /><p><h1>Figurines définies pour ce style :</h1></p><form method="post" enctype="multipart/form-data"> 
        <input id="id-style-figs" type="hidden" name="style" value="'.$style.'">
        <input type="radio" id="figs-id-enn" name="fig_id"  value="enn">enn&nbsp;&nbsp;
        <input type="radio" id="figs-id-per" name="fig_id"  value="per">per&nbsp;&nbsp;
        <input type="radio" id="figs-id-lie" name="fig_id"  value="lie">lie&nbsp;&nbsp;
        <input type="radio" id="figs-id-obj" name="fig_id"  value="obj">obj&nbsp;&nbsp;
        <input type="file" name="fig_file">&nbsp;&nbsp;
        <input type="submit" class="test" name="nouveau_fig" value="Ajouter/Remplacer la figurine"></form>
        <i><font color="#8b0000">L\'image doit être un .png de 28x28 pixels, vous devez selectionner le type de la figurine</font>.</i>	
	<div style=\'width:600px; overflow:auto\' class=\'bordiv\' id=\'visu_figs\'></div>
	
	<br><div><i><u>Nota</u>: Vous pouvez cliquer sur les images de fond ou de mur pour saisir automatiquement leurs id .</i ></div >
	<div><i><u>Nota</u>: Pour qu\'un style soit créé, il faut lui ajouter au moins un fond ou un mur.</i ></div >
    <script type="text/javascript">changeStyle("'.$style.'");</script>';
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
