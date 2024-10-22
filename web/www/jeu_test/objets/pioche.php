<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;


$contenu_page = '';

// ON VRERIFIE SI L'OBJET EST BIEN DANS L'INVENTAIRE.


$req_matos = "select perobj_obj_cod from perso_objets,objets "
             . "where perobj_obj_cod = obj_cod and perobj_perso_cod = $perso_cod and obj_gobj_cod in ( 332, 369, 858, 1091, 1101, 1105, 1106, 1107) and perobj_equipe='O' order by perobj_obj_cod";
$stmt      = $pdo->query($req_matos);
if (!($result = $stmt->fetch()))
{
  // PAS D'OBJET.
 	$contenu_page .= "<p>Vous n'avez pas de pioche équipée</p>";
}
else
{
	$erreur = 0;
	
	if(isset($_POST['methode'])){
        switch ($_POST['methode'])
        {
            case "creuser":
                $req          = "select creuser($perso_cod," . $_POST['pos_cod'] . ") as resultat";
                $stmt         = $pdo->query($req);
                $result       = $stmt->fetch();
                $contenu_page .= "<p>" . $result['resultat'] . "</p><HR>";
                break;
        }
    }

	// POSITION DU JOUEUR
	$req_matos = "select pos_x,pos_y,pos_etage,pos_cod "
	."from perso_position,positions "
	."where ppos_perso_cod = $perso_cod "
	."and ppos_pos_cod = pos_cod ";
    $stmt = $pdo->query($req_matos);
    $result = $stmt->fetch();
    $perso_pos_x     = $result['pos_x'];
    $perso_pos_y     = $result['pos_y'];
    $perso_pos_etage = $result['pos_etage'];
    $perso_pos_cod   = $result['pos_cod'];


	$req_murs = "select pos_cod, pos_x,pos_y,mur_creusable,mur_usure from positions "
	." LEFT JOIN murs ON positions.pos_cod = murs.mur_pos_cod"
	." where"
	." pos_x = $perso_pos_x-1"
	." and pos_y = $perso_pos_y+1"
	." and pos_etage = $perso_pos_etage"
	." order by pos_y desc,pos_x asc";
    $stmt = $pdo->query($req_murs);
    if($result = $stmt->fetch())
    {
        if ($result['mur_creusable'] == 'O')
        {
            $pos_no = $result['pos_cod'];
		}
	}
	$req_murs = "select pos_cod, pos_x,pos_y,mur_creusable,mur_usure from positions "
	." LEFT JOIN murs ON positions.pos_cod = murs.mur_pos_cod"
	." where"
	." pos_x = $perso_pos_x"
	." and pos_y = $perso_pos_y+1"
	." and pos_etage = $perso_pos_etage"
	." order by pos_y desc,pos_x asc";
    $stmt = $pdo->query($req_murs);
    if($result = $stmt->fetch())
    {
        if ($result['mur_creusable'] == 'O')
        {
            $pos_nord = $result['pos_cod'];
		}
	}
	$req_murs = "select pos_cod, pos_x,pos_y,mur_creusable,mur_usure from positions "
	." LEFT JOIN murs ON positions.pos_cod = murs.mur_pos_cod"
	." where"
	." pos_x = $perso_pos_x+1"
	." and pos_y = $perso_pos_y+1"
	." and pos_etage = $perso_pos_etage"
	." order by pos_y desc,pos_x asc";
    $stmt = $pdo->query($req_murs);
    if($result = $stmt->fetch())
    {
        if ($result['mur_creusable'] == 'O')
        {
            $pos_ne = $result['pos_cod'];
		}
	}
	$req_murs = "select pos_cod, pos_x,pos_y,mur_creusable,mur_usure from positions "
	." LEFT JOIN murs ON positions.pos_cod = murs.mur_pos_cod"
	." where"
	." pos_x = $perso_pos_x-1"
	." and pos_y = $perso_pos_y-1"
	." and pos_etage = $perso_pos_etage"
	." order by pos_y desc,pos_x asc";
    $stmt = $pdo->query($req_murs);
    if($result = $stmt->fetch())
    {
        if ($result['mur_creusable'] == 'O')
        {
            $pos_so = $result['pos_cod'];
		}
	}
	$req_murs = "select pos_cod, pos_x,pos_y,mur_creusable,mur_usure from positions "
	." LEFT JOIN murs ON positions.pos_cod = murs.mur_pos_cod"
	." where"
	." pos_x = $perso_pos_x"
	." and pos_y = $perso_pos_y-1"
	." and pos_etage = $perso_pos_etage"
	." order by pos_y desc,pos_x asc";
    $stmt = $pdo->query($req_murs);
    if($result = $stmt->fetch())
    {
        if ($result['mur_creusable'] == 'O')
        {
            $pos_sud = $result['pos_cod'];
		}
	}
	$req_murs = "select pos_cod, pos_x,pos_y,mur_creusable,mur_usure from positions "
	." LEFT JOIN murs ON positions.pos_cod = murs.mur_pos_cod"
	." where"
	." pos_x = $perso_pos_x+1"
	." and pos_y = $perso_pos_y-1"
	." and pos_etage = $perso_pos_etage"
	." order by pos_y desc,pos_x asc";
    $stmt = $pdo->query($req_murs);
    if($result = $stmt->fetch())
    {
        if ($result['mur_creusable'] == 'O')
        {
            $pos_se = $result['pos_cod'];
		}
	}
	$req_murs = "select pos_cod, pos_x,pos_y,mur_creusable,mur_usure from positions "
	." LEFT JOIN murs ON positions.pos_cod = murs.mur_pos_cod"
	." where"
	." pos_x = $perso_pos_x-1"
	." and pos_y = $perso_pos_y"
	." and pos_etage = $perso_pos_etage"
	." order by pos_y desc,pos_x asc";
    $stmt = $pdo->query($req_murs);
    if($result = $stmt->fetch())
    {
        if ($result['mur_creusable'] == 'O')
        {
            $pos_ouest = $result['pos_cod'];
		}
	}
	$req_murs = "select pos_cod, pos_x,pos_y,mur_creusable,mur_usure from positions "
	." LEFT JOIN murs ON positions.pos_cod = murs.mur_pos_cod"
	." where"
	." pos_x = $perso_pos_x+1"
	." and pos_y = $perso_pos_y"
	." and pos_etage = $perso_pos_etage"
	." order by pos_y desc,pos_x asc";
    $stmt = $pdo->query($req_murs);
    if($result = $stmt->fetch())
    {
        if ($result['mur_creusable'] == 'O')
        {
            $pos_est = $result['pos_cod'];
		}
	}
	$contenu_page .= '<p align="center">Une pioche ? Bah c’est fait pour creuser !</p><hr /><table>
<tr>
<td>';
	$texte_nok = '<p>Pas de filon creusable __DIRECTION__.</p>';
	$texte_ok = '<form name="__FORM__" method="post">
		<input type="hidden" name="pos_cod" value="__POSITION__">
		<input type="hidden" name="methode" value="creuser" >
		<p><a href="javascript:document.__FORM__.submit();">Creuser __DIRECTION__ (4 PAs)</a></p>
		</form>';
	$masque = array('__DIRECTION__', '__FORM__', '__POSITION__');
	
	$valeurs = array('au nord-ouest', 'creuser_no', 0);
	if(isset($pos_no)) {
		$valeurs[2] = $pos_no;
		$contenu_page .= str_replace($masque, $valeurs, $texte_ok);
	} else
		$contenu_page .= str_replace($masque, $valeurs, $texte_nok);

	$contenu_page .= '</td><td>';
	
	$valeurs = array('au nord', 'creuser_nord', 0);
	if(isset($pos_nord)) {
		$valeurs[2] = $pos_nord;
		$contenu_page .= str_replace($masque, $valeurs, $texte_ok);
	} else
		$contenu_page .= str_replace($masque, $valeurs, $texte_nok);

	$contenu_page .= '</td><td>';
	
	$valeurs = array('au nord-est', 'creuser_ne', 0);
	if(isset($pos_ne)) {
		$valeurs[2] = $pos_ne;
		$contenu_page .= str_replace($masque, $valeurs, $texte_ok);
	} else
		$contenu_page .= str_replace($masque, $valeurs, $texte_nok);

	$contenu_page .= '</td></tr><tr><td>';
	
	$valeurs = array('à l’ouest', 'creuser_ouest', 0);
	if(isset($pos_ouest)) {
		$valeurs[2] = $pos_ouest;
		$contenu_page .= str_replace($masque, $valeurs, $texte_ok);
	} else
		$contenu_page .= str_replace($masque, $valeurs, $texte_nok);

	$contenu_page .= '</td></td><td><td>';
	
	$valeurs = array('à l’est', 'creuser_est', 0);
	if(isset($pos_est)) {
		$valeurs[2] = $pos_est;
		$contenu_page .= str_replace($masque, $valeurs, $texte_ok);
	} else
		$contenu_page .= str_replace($masque, $valeurs, $texte_nok);

	$contenu_page .= '</td></tr><tr><td>';
	
	$valeurs = array('au sud-ouest', 'creuser_so', 0);
	if(isset($pos_so)) {
		$valeurs[2] = $pos_so;
		$contenu_page .= str_replace($masque, $valeurs, $texte_ok);
	} else
		$contenu_page .= str_replace($masque, $valeurs, $texte_nok);

	$contenu_page .= '</td><td>';
	
	$valeurs = array('au sud', 'creuser_sud', 0);
	if(isset($pos_sud)) {
		$valeurs[2] = $pos_sud;
		$contenu_page .= str_replace($masque, $valeurs, $texte_ok);
	} else
		$contenu_page .= str_replace($masque, $valeurs, $texte_nok);

	$contenu_page .= '</td><td>';
	
	$valeurs = array('au sud-est', 'creuser_se', 0);
	if(isset($pos_se)) {
		$valeurs[2] = $pos_se;
		$contenu_page .= str_replace($masque, $valeurs, $texte_ok);
	} else
		$contenu_page .= str_replace($masque, $valeurs, $texte_nok);

	$contenu_page .= '</td></tr></table>';
}

// on va maintenant charger toutes les variables liées au menu
include('../variables_menu.php');

$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'CONTENU_PAGE'             => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));
