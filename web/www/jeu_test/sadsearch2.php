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
     		$req = "select compt_nom,compt_cod from compte where compt_cod like '" . (1*$foo) . "%'";
     		$db->query($req);
     		$xml = "<resultats nb=\"" .  $db->nf() . "\">";
     		if($db->nf()!= 0)
     		{
     			while($db->next_record())
     			{
     				//$xml .= $db->f('perso_nom');
     				$xml .= "\n" . '<resultat titre="' . str_replace('"', "",$db->f('compt_nom')) .  ' - (' . $db->f('compt_cod') . ')" url="javascript:mettrevaleur2(\'' . $db->f('compt_cod') . '\')" />';
     				//$xml .= "\n<resultat titre=\"" . str_replace('"', "", $db->f('perso_nom') . "\" url=\"/blog/" . $Db->f('perso_cod') . ".html\" />";
       		}	
     		}
          /*$rqListBillet = "
               SELECT *
               FROM `blog_blabla`
               WHERE `titre` like '" . $_REQUEST["foo"] . "%'
               ORDER BY `date_parution` DESC";
          $rsListBillet = mysql_query($rqListBillet);
          $xml = "<resultats nb=\"" .  mysql_num_rows($rsListBillet) . "\">";
          if (mysql_num_rows($rsListBillet) > 0) {
               while ($billet = mysql_fetch_object($rsListBillet)) {
                    $xml .= "\n<resultat titre=\"" . str_replace('"', "", $billet->titre) . "\" url=\"/blog/" . $billet->url_page . ".html\" />";
               }

          }*/
     }
     else {
          $xml = "<resultats nb=\"0\">";
     }
     $xml .= "\n</resultats>";
     echo utf8_encode($xml);
?>