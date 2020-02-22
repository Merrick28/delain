<?php
define("SYNODIC", 29.53058867); //constante pour la période synodique

define("MSPARJOUR", 24 * 60 * 60 * 1000); //constante pour le nombre de millisecondes par jour

$perso = new perso;
$perso = $verif_connexion->perso;
define('APPEL', 1);
require "blocks/_recup_composant.php";

$soleil = "inconnu";
$Tdate  = getdate($tstamp);
$heure  = $Tdate["hours"];
if ($heure < 6 || $heure > 18)
{
    $soleil = "NUIT";
} else
{
    $soleil = "JOUR";
}


/*if(!isset($db))
	$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;

include "../includes/template.inc";
// definition des variables de template
$temp = new template;
$temp->set_file("FileRef","../template/classic/" . $nom_template . ".tpl");
// chemins
$temp->set_var("g_url",$type_flux.G_URL);
$temp->set_var("g_che",G_CHE);
$temp->set_var("action_onload",$action_onload);
// chemin des images
$temp->set_var("img_path",G_IMAGES);
// contenu de la page*/

$phase        = CalcPhase();
$contenu_page .= '<table width="70%">
											<td><strong>- Phase lunaire : </td>
											<td><strong>- Image lune : </strong></td>
											<td><strong>- Aide Lunaire : </strong></td>
										<tr>
											<td><em>' . NommerPhase($phase) . '</em></strong></td>
											<td><img src="' . ImgPhase($phase) . '"></td>
											<td><em>Il reste ' . JoursAvantNL($phase) . ' jours avant la nouvelle lune
											<br> et ' . JoursAvantPL($phase) . ' jours avant la pleine lune <em></td>
										</tr>
										</table>';

//
// Tests sur les possibilités de cueillettes
//
$methode = get_request_var('methode', 'debut');
switch ($methode)
{
    case "debut":
        $contenu_page .= '<br><br><br><em>Vous ne pouvez pas récupérer de composants à n\'importe quel moment. En effet, il ne faut pas que vous soyez en combat pour cela ...
			<br>En dehors de cette contrainte, vous pouvez lancer une cueillette quand vous le souhaitez. Vous récupèrerez plusieurs composants. Mais si aucun composant ne se trouve dans la zone vos PA seront perdus.
			<br>Pour améliorer vos chances, vous pouvez lancer des détections, qui vous indiqueront les coins propices à la présence de composants. Attention, ceci ne signifie pas forcément que des composants seront présents, mais qu\'il y a au moins une chance qu\'il y en ait.
			<br><br></em>';
        $lock         = $perso->is_locked();
        if ($lock == true)
        {
            $contenu_page .= '<br><br><strong>Vous ne pouvez pas réaliser de cueillette, étant donné que vous êtes locké en combat</strong>';

        } else
        {

            $contenu_page .= '<form name="recup_composant" method="post" action="' . $_SERVER['PHP_SELF'] . '">
														<input type="hidden" name="methode" value="recup">
														<input type="hidden" name="t" value="' . $t . '">
														<table width="70%">
														<td><input type="submit" name="recuperation" value="Cueillette de composants (4 PA)"  class="test"></td>
													</form></table>';
        }
        break;

    case "recup":
        /*vérif si pas de lock de combat*/
        $lock  = $perso->is_locked();
        $phase = CalcPhase();
        if ($lock == true)
        {
            $contenu_page .= '<br>Vous ne pouvez pas faire de cueillette, étant donné que vous êtes locké en combat';
            $erreur       = 1;
        }
        /*Controle pour la compétence alchimie*/
        $alchimiste = $perso->existe_competence(97);
        if ($alchimiste != true)
        {
            $contenu_page .= '<br>Vous ne pouvez pas réaliser de cueillette, car vous n\'êtes pas alchimiste ! Ici, il n\'y a que de vulgaires plantes pour vous.';
            $erreur       = 1;
        }
        /*Controle sur les Pas*/
        $req  = 'select perso_pa from perso where perso_cod = ' . $perso_cod;
        $stmt = $pdo->query($req);
        if ($stmt->rowCount() == 0)
        {
            $contenu_page .= '<td><br />Erreur sur le perso concerné<br /><br /></td>';
            $erreur       = 1;
        } else
        {
            $result = $stmt->fetch();
            if ($result['perso_pa'] < 4)
            {
                $contenu_page .= '<br>Vous n\'avez pas suffisamment de PA pour réaliser cette action.';
                $erreur       = 1;
            }
        }
        if ($erreur != 1)
        {
            $position     = $perso->get_position();
            $pos_cod      = $position['pos']->pos_cod;
            $req          =
                'select potions.recup_composant(' . $perso_cod . ',' . $pos_cod . ',' . $phase . ') as resultat';
            $stmt         = $pdo->query($req);
            $result       = $stmt->fetch();
            $contenu_page .= $result['resultat'];
        }
        break;

}
