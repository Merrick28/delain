<?php /* Affichage de tous les styles de murs et fonds */

include "blocks/_header_page_jeu.php";


function ecrireResultatEtLoguer($texte, $loguer, $sql = '')
{
    global $pdo, $compt_cod;

    if ($texte)
    {
        $log_sql = false;	// Mettre à true pour le debug des requêtes

        if (!$log_sql || $sql == '')
            $sql = "\n";
        else
            $sql = "\n\t\tRequête : $sql\n";

        $req = "select compt_nom from compte where compt_cod = $compt_cod";
        $stmt = $pdo->query($req);
        $result = $stmt->fetch();
        $compt_nom = $result['compt_nom'];

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
define('APPEL', 1);
include "blocks/_test_droit_modif_etage.php";
if ($erreur == 0)
{

    $pdo    = new bddpdo;            // 2018-05-22 - Marlyza - pour traiter les requêtes secondaires


	// Récupération des images existantes
	// On y va à la bourrin : on parcourt tous les fichiers du répertoire images.
	$patron_decors = '/^dec_(?P<type>\d+)\.gif$/';
	$chemin = '../../images/';

	$tableau_decors = array();
	$js_tab_decors = "\nvar tab_decors = new Array();";

	$rep = opendir($chemin);
	while (false !== ($fichier = readdir($rep)))
	{
		$correspondances = array();
		if (1 === preg_match($patron_decors, $fichier, $correspondances))
		{
            $js_tab_decors .= "\ntab_decors[" . $correspondances['type'] . "] = " . $correspondances['type'] . ";";
            $tableau_decors[$correspondances['type']] =  $correspondances['type'];
		}
	}


    // Traitement des actions ===============================================================
    // --- Action de suppression d'un fond -------
    if (isset($_REQUEST["supprimer_decor"]))
    {
        echo '<div class="barrTitle">Suppression d\'un décors</div><br>';
        $decor_id = $_REQUEST["decor_id"];
        $filename = "dec_{$decor_id}.gif" ;

        if (!file_exists($chemin.$filename))
        {
            echo "<strong>Impossible de supprimer le decor pour l'id $decor_id, le fichier  $filename n'a pas été trouvé.</strong><br><br>";
        }
        else
        {
            //  vérification  que le decor n'est pas utilisé dans les étages avant de le supprimer
            $req_decors = "select count(*) as count from positions where pos_decor=? or pos_decor_dessus=?";
            $stmt = $pdo->prepare($req_decors);
            $stmt = $pdo->execute(array($decor_id,$decor_id), $stmt);
            $row = $stmt->fetch();
            $decor_usage = $row['count'];
            if ($decor_usage>0)
            {
                echo "<strong>Impossible de supprimer le decor pour l'id $decor_id, il est encore utilisé dans les étages.</strong><br><br>";
            }
            else
            {
                unlink ( $chemin.$filename );
                ecrireResultatEtLoguer(" l'image $filename a été supprimée pour le décor id #$decor_id", true);
                //supprimer pour l'affichage
                $js_tab_decors .= "\ndelete tab_decors[" . $decor_id . "] ;";
            }
        }
    }

    // --- Action de ajout modification d'un decor -------
    if (isset($_REQUEST["nouveau_decor"]))
    {
        echo '<div class="barrTitle">Ajout d\'un decor</div><br>';
        if ($_REQUEST["decor_id"]!="")
        {
            $decor_id = $_REQUEST["decor_id"];
            if (isset($tableau_decors[$decor_id]))
            {
                $decor_id = "" ;     // => pour ne pas ajouter l'image
                echo "<strong>Impossible d'ajouter ce decor, l'id  $decor_id existe déjà.</strong><br><br>";
            }
        }
        else
        {
            $decor_id = 1;
            while (isset($tableau_decors[$decor_id])) $decor_id ++ ;
        }

        // Upload de l'image
        if ($decor_id != "")
        {
            $filename = "dec_{$decor_id}.gif" ;
            $imagesize = @getimagesize($_FILES["decor_file"]["tmp_name"]) ;
            if (($imagesize[0] != 28) || ($imagesize[1] != 28))
            {
                echo "<strong>Impossible d'ajouter ce decor, l'image n'est pas aux dimensions de 28x28 pixels.</strong><br><br>";
            }
            else
            {
                move_uploaded_file ( $_FILES["decor_file"]["tmp_name"] , $chemin.$filename );
                ecrireResultatEtLoguer(" l'image $filename a été ajoutée pour le décor id #$decor_id", true);
                //ajouter les pour l'affichage
                $js_tab_decors .= "\ntab_decors[" . $decor_id . "] = " . $decor_id . ";";
            }
        }
    }

    // Traitement de l'affichage ===============================================================

    echo '<div class="barrTitle">Edition des décors</div><br>';


    echo "<script type='text/javascript'>
		$js_tab_decors

        function popDecorInfo(d)
        {
            $('#info-decor-usage').html(d.data.message);
        }
             
		function afficheDecor()
		{
	    
			var div_decor = document.getElementById('visu_decors');

			var chaine_contenu = '';

			for (var i in tab_decors)
			{
				var nom = '" . G_IMAGES . "dec_' + i + '.gif';
				chaine_contenu += '<div style=\"float:left; width:28px; height:38px; margin:6px;\"><img id=\"id-decor-'+i+'\" src=\"' + nom + '\"/><br />' + i + '</div>';
			}
			div_decor.innerHTML = chaine_contenu;

			// armer/ré-armer le trigger sur les fonds
		    $(\"img[id^='id-decor-']\").on('click', function(e) {
		            runAsync({request: \"admin_info_decors\", data:{ decor_id:this.id.substr(9)} }, popDecorInfo);
                    $('input#decor-id').val(this.id.substr(9)); 
		    });	
		}
		
		</script>";

    echo '</p><hr />

    <p><h1>decors définis :</h1></p><form method="post" enctype="multipart/form-data"> 
        <input id="id-style-decors" type="hidden" name="style" value="'.$style.'">
        decor id=<input type="text" id="decor-id" name="decor_id" size="3" value="">&nbsp;&nbsp;
        <input type="submit" class="test" name="supprimer_decor" value="Supprimer le decor"> ou 
        <input type="file" name="decor_file">&nbsp;&nbsp;
        <input type="submit" class="test" name="nouveau_decor" value="Ajouter le decor"></form>
        <span id="info-decor-usage"></span><br>
        <em><span class="color:#8b0000;">L\'image doit être un .gif de 28x28 pixels, si vous ne spécifiez pas d\'<strong>id</strong> il en sera assigné un automatiquement</span>.</em>
	<div style=\'width:600px; overflow:auto\' class=\'bordiv\' id=\'visu_decors\'></div>
	
	
	<br><div><em><u>Nota</u>: Vous pouvez cliquer sur les images de décors pour saisir automatiquement son id .</i ></div >
    <script type="text/javascript">afficheDecor();</script>';
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";