<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;


$contenu_page = '';


$req_matos = "select perobj_obj_cod from perso_objets,objets
												where perobj_obj_cod = obj_cod and perobj_perso_cod = $perso_cod and obj_gobj_cod = 534 ";
$stmt      = $pdo->query($req_matos);
if ($result = $stmt->fetch())
{
    $baguette = $result['perobj_obj_cod'];
	if(isset($_POST['methode']))
	{
		$req_pa = "select perso_pa from perso where perso_cod = $perso_cod";
        $stmt = $pdo->query($req_pa);
        $result = $stmt->fetch();
        if ($result['perso_pa'] < 4)
		{
			$contenu_page .= "Vous n'avez pas assez de PA !";
		}
		else
		{
			$req_use = "select use_artefact($baguette)";
            $stmt = $pdo->query($req_use);
			$req_enl_pa = "update perso set perso_pa = perso_pa - 4 where perso_cod = $perso_cod";
            $stmt = $pdo->query($req_enl_pa);
			$contenu_page .= '<p>Le cadran du détecteur affiche :</p>
				<center><table background="../../images/fond5.gif" border="0" cellspacing="1" cellpadding="0">';
			
			// POSITION DU JOUEUR
			$req_position = "select pos_x,pos_y,pos_etage
												from perso_position,positions
												where ppos_perso_cod = $perso_cod
												and ppos_pos_cod = pos_cod ";
            $stmt = $pdo->query($req_position);
            $result = $stmt->fetch();
            $perso_pos_x     = $result['pos_x'];
            $perso_pos_y     = $result['pos_y'];
            $perso_pos_etage = $result['pos_etage'];
			//echo "POSJ = $perso_pos_x ; $perso_pos_y ; $perso_pos_etage <br>";
			$cachetteArray = array();
			// POSITION DES CACHETTES
			$req_cachette = "select pos_x,pos_y,pos_etage from cachettes,positions
															where cache_pos_cod = pos_cod
															and pos_etage = $perso_pos_etage";
            $stmt = $pdo->query($req_cachette);
            while ($result = $stmt->fetch())
			{
                //echo "POS=".$result['pos_x'].";".$result['pos_y'].";".$result['pos_etage']."<br>";
                $key = $result['pos_x'] . "X" . $result['pos_y'];
                if (isset($cachetteArray[$key]))
                {
                    $cachetteArray[$key]++;
                } else
                {
                    $cachetteArray[$key] = 1;
                }
            }
			
			for ($i=-6; $i<7; $i++)
			{  
				$contenu_page .= "<tr>";
				for ($j=-6; $j<7; $j++)
				{
					if(($i*$i + $j*$j) <37 )
					{
						$pos_case_x = $perso_pos_x + $j;
						$pos_case_y = $perso_pos_y - $i;
						$key = $pos_case_x."X".$pos_case_y;
						if(isset($cachetteArray[$key]))
						{
							$contenu_page .= "<td><img src=\"../../images/automap_1_3.gif\" title=\"Position : X = ".$pos_case_x." / Y = ".$pos_case_y."\"></td>";	
						} 
						else 
						{
							if($i == 0 && $j == 0)
							{
								$contenu_page .= "<td><img src=\"../../images/automap_1_6.gif\" title=\"rien $key\"></td>";
							} 
							else 
							{
								$contenu_page .= "<td><img src=\"../../images/automap_0_0.gif\" title=\"rien $key\"></td>";
							}
						}
						
					} 
					else 
					{
						$contenu_page .= "<td></td>";
					}
				}
				$contenu_page .= "</tr>";
			}
			$contenu_page .= '</table></center>';
		}
	}
	$contenu_page .= '<p align="center">Le détecteur fonctionne, Appuyer sur le bouton ?<br><br>
		<form method="post" action="detecteur_cachette.php">
		<input type="hidden" name="methode" value="detecter">
		<input type="submit" value="Appuyer (4PA)"  class="test">
		</form>
		</p>';
} 
else 
{
	$contenu_page .= "<p>Accès interdit</p>";	
}

// on va maintenant charger toutes les variables liées au menu
include('../variables_menu.php');

$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'CONTENU_PAGE'             => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));