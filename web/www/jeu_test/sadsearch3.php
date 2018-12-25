<?php
define('NO_DEBUG');
     header("Pragma: no-cache");
     header("Expires: 0");
     header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
     header("Cache-Control: no-cache, must-revalidate");
     header("Content-type: application/xml"); 
		require "classes.php";
		$db = new base_delain;
     if(!empty($_REQUEST["foo"]))
     {
     		$req = "select creappro_gobj_cod,gobj_nom from cachette_reappro,objet_generique where creappro_gobj_cod = gobj_cod and creappro_cache_liste_respawn = '" . (1*$foo) . "' order by gobj_cod";
     		$db->query($req);
				$nb_tobj = 0;
     		$xml = "";
     		$xml = "<resultats nb=\"" .  $db->nf() . "\">";
     		if($db->nf()!= 0)
     		{
     			while($db->next_record())
     			{
						/*$xml .= "listeCurrent[$nb_tobj] = new Array(0); \n";
						$xml .= "listeCurrent[$nb_tobj][0] = \"".$db->f("gobj_nom")."\"; \n";
						$xml .= "listeCurrent[$nb_tobj][1] = \"".$db->f("creappro_gobj_cod")."\"; \n";
						$nb_tobj++;		*/
						$xml .= "\n" . '<resultat titre="' . str_replace('"', "",$db->f('creappro_gobj_cod')) .  ' - (' . $db->f('gobj_nom') . ')" url="javascript:supprimervaleur(\'' . $db->f('creappro_gobj_cod') . '\',\'' . $db->f('gobj_nom') . '\')" />';
						/*$xml .= "\n" . 'titre="' . str_replace('"', "",$db->f('creappro_gobj_cod')) .  ' - (' . $db->f('gobj_nom') . ')" />';*/
       		}	
     		}
     }
     else {
          $xml = "\n";
     }
     $xml .= "\n</resultats>";
     /*$xml .= "\n";*/
     echo utf8_encode($xml);
?>
