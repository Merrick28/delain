<?php
define('NO_DEBUG');
header("Pragma: no-cache");
     header("Expires: 0");
     header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
     header("Cache-Control: no-cache, must-revalidate");
     header("Content-type: application/xml"); 
		include "classes.php";
		$db2 = new base_delain;
	   if(!empty($_REQUEST["foo"]))
     {
     		$req = "select mgroupe_perso_cod,perso_nom,mgroupe_statut from quetes.mission_groupe,perso 
     		where mgroupe_groupe_cod =".(1*$foo)." 
     		and mgroupe_perso_cod = perso_cod
     		and mgroupe_statut != 'E'";
     		$db->query($req);
     		$xml = "<resultats nb=\"" .  $db->nf() . "\">";
     		if($db->nf()!= 0)
     		{
     			$xml .= "<ul>";
     		/*$xml .= '<resultat titre="valeur=\'0\' title=\'Sélectionner le résultat désiré\' "/>';*/
     			while($db->next_record())
     			{
     				$chef = '';
     				if ($db->f('mgroupe_statut')=='O')
     				{
     					$chef = '- (chef)';
     				}
     				$xml .='<resultat titre="' . str_replace('"', "",$db->f('perso_nom')) .'' . $chef . '" url="javascript:mettrevaleur2(\'' . $db->f('perso_cod') . '\')" />';
       		}
       		$xml .= "</ul>";
     		}
    	 	else {
          $xml = "<resultats nb=\"0\">";
     		}
     	$xml .= "</resultats>";

	     echo utf8_encode($xml);
	    }
?>